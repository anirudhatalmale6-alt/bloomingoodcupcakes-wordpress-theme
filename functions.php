<?php
/**
 * Bloomin' Good Cupcakes -- theme bootstrap.
 *
 * One page, one stylesheet, one small script, no page builder. The audit that
 * this rebuild answers measured the old site at 10.2 seconds to paint its
 * largest photograph on a phone, against Google's 2.5 second threshold, with
 * 2,545 KiB of image savings available. A builder plus its addons would put
 * most of that back before a single cupcake was on screen, so there is not one
 * here.
 *
 * Written to PHP 7.4 so it runs on the build subdomain and on whatever the
 * live host turns out to be.
 *
 * @package bloomingood
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BGC_VERSION', '1.2.1' );
define( 'BGC_DIR', get_template_directory() );
define( 'BGC_URI', get_template_directory_uri() );

require_once BGC_DIR . '/inc/customizer.php';   // defines bgc_defaults(), used by bgc_opt()
require_once BGC_DIR . '/inc/helpers.php';
require_once BGC_DIR . '/inc/reviews.php';
require_once BGC_DIR . '/inc/seo.php';

/*
 * Lead handling deliberately does NOT live here. The enquiry post type, the
 * spam scoring and the delivery path are in mu-plugins/bgc-enquiries.php, so
 * that changing or updating this theme cannot take the leads with it, and so
 * that switching theme does not hide every stored enquiry from wp-admin.
 * The templates below call those functions defensively.
 */

/**
 * Theme supports.
 */
function bgc_setup() {
	load_theme_textdomain( 'bloomingood', BGC_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	// The showcase is a fixed portrait crop; the hero is the one full-bleed shot.
	add_image_size( 'bgc-shot', 1000, 1400, true );
	add_image_size( 'bgc-hero', 1400, 1400, true );

	register_nav_menus(
		array(
			'primary' => __( 'Header links', 'bloomingood' ),
		)
	);
}
add_action( 'after_setup_theme', 'bgc_setup' );

/**
 * Front-end assets.
 *
 * Deliberately two files. Worth keeping this function boring.
 */
function bgc_assets() {
	wp_enqueue_style( 'bgc-main', get_stylesheet_uri(), array(), BGC_VERSION );
	wp_enqueue_script( 'bgc-main', BGC_URI . '/assets/js/site.js', array(), BGC_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'bgc_assets' );

/**
 * Preload the two faces that render above the fold.
 *
 * Without this the headline reflows once the woff2 arrives, which on a slow
 * connection is the most visible part of a slow first paint.
 */
function bgc_preload() {
	foreach ( array( 'petrona-latin.woff2', 'hanken-latin.woff2' ) as $face ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( BGC_URI . '/assets/fonts/' . $face )
		);
	}
	// The hero photograph is the largest contentful paint on every visit, and
	// the audit measured that exact element at 10.2s on the site this replaces.
	printf(
		'<link rel="preload" href="%s" as="image" type="image/webp" fetchpriority="high">' . "\n",
		esc_url( BGC_URI . '/assets/img/hero-bouquet-19.webp' )
	);
}
add_action( 'wp_head', 'bgc_preload', 1 );

/**
 * Strip what WordPress prints that this site does not use.
 */
function bgc_trim_head() {
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
}
add_action( 'init', 'bgc_trim_head' );

/**
 * Drop the block-library CSS on the front end.
 *
 * Nothing here is a block-editor layout, so it is ~90 KB of stylesheet for
 * rules nothing matches. Guarded so it never fires in the editor.
 */
function bgc_drop_block_css() {
	if ( is_admin() ) {
		return;
	}
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style( 'global-styles' );
}
add_action( 'wp_enqueue_scripts', 'bgc_drop_block_css', 100 );

/**
 * Stop the global styles being generated at all.
 *
 * Dequeuing 'global-styles' is not enough on its own: WordPress builds that
 * block from theme.json defaults and prints it inline, so the handle is gone
 * and roughly 8 KB of colour and gradient presets nothing in this design uses
 * still arrives in the head. Measured on the build site before this was added.
 */
function bgc_no_global_styles() {
	if ( is_admin() ) {
		return;
	}
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
	remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
	remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
	remove_action( 'in_admin_header', 'wp_global_styles_render_svg_filters' );
	// The auto-sizes shim exists for lazily loaded block images; there are none.
	remove_action( 'wp_head', 'wp_print_auto_sizes_contain_css_fix', 1 );
}
add_action( 'init', 'bgc_no_global_styles' );
