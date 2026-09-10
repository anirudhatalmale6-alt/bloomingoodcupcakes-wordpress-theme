<?php
/**
 * Small helpers used by the templates.
 *
 * @package bloomingood
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read a Customizer setting, falling back to the value shipped in the theme.
 *
 * The fallbacks are the approved redesign copy, so the site is complete and
 * correct the moment it is installed. Claire changes what she wants to change;
 * she is never presented with an empty page waiting to be filled in.
 *
 * @param string $key     Setting id, without the bgc_ prefix.
 * @param mixed  $default Value to use when nothing has been saved.
 * @return mixed
 */
function bgc_opt( $key, $default = null ) {
	/*
	 * Fall back to bgc_defaults() rather than to an empty string. Without this,
	 * every call that does not pass its own default -- which is most of them --
	 * returns nothing until somebody has been into the Customizer and pressed
	 * save, so a freshly installed site renders an empty phone link and four
	 * empty price lists. Caught on the build site: the header printed
	 * <a class="hdr-tel" href="tel:+447752661713"></a>, a link with no text in
	 * it, which looks like a spacing bug rather than a missing number.
	 */
	if ( null === $default ) {
		$defaults = bgc_defaults();
		$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	}
	$value = get_theme_mod( 'bgc_' . $key, $default );
	return ( '' === $value || null === $value ) ? $default : $value;
}

/**
 * A theme image, as a <picture> that prefers WebP.
 *
 * Every photograph on this page exists at two widths in two formats. Handing
 * the browser the whole set and letting it choose is what turns the audit's
 * 2,545 KiB of "estimated savings" into bytes that are actually never sent.
 *
 * @param string $stem   File stem, e.g. 'hero-bouquet'.
 * @param string $alt    Alt text. Empty string marks the image decorative.
 * @param array  $args   sizes, class, loading, fetchpriority, width, height.
 */
function bgc_picture( $stem, $alt, $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'sizes'         => '(min-width:900px) 50vw, 100vw',
			'class'         => '',
			'loading'       => 'lazy',
			'fetchpriority' => '',
			'decoding'      => 'async',
		)
	);

	$base = BGC_URI . '/assets/img/' . $stem;
	$dir  = BGC_DIR . '/assets/img/';

	// Only advertise a width that exists on disk. A srcset entry pointing at a
	// missing file is not a broken image -- the browser silently falls back --
	// which is exactly why it would never get noticed.
	$web = array();
	$jpg = array();
	foreach ( array( '-sm' => 640, '' => 1400 ) as $suffix => $w ) {
		if ( file_exists( $dir . $stem . $suffix . '.webp' ) ) {
			$web[] = esc_url( $base . $suffix . '.webp' ) . ' ' . $w . 'w';
		}
		if ( file_exists( $dir . $stem . $suffix . '.jpg' ) ) {
			$jpg[] = esc_url( $base . $suffix . '.jpg' ) . ' ' . $w . 'w';
		}
	}
	if ( ! $jpg ) {
		return;
	}

	$attr = '';
	foreach ( array( 'loading', 'decoding', 'fetchpriority' ) as $k ) {
		if ( ! empty( $args[ $k ] ) ) {
			$attr .= sprintf( ' %s="%s"', $k, esc_attr( $args[ $k ] ) );
		}
	}

	echo '<picture>';
	if ( $web ) {
		printf(
			'<source type="image/webp" srcset="%s" sizes="%s">',
			esc_attr( implode( ', ', $web ) ),
			esc_attr( $args['sizes'] )
		);
	}
	printf(
		'<img src="%s" srcset="%s" sizes="%s" alt="%s"%s%s>',
		esc_url( $base . '.jpg' ),
		esc_attr( implode( ', ', $jpg ) ),
		esc_attr( $args['sizes'] ),
		esc_attr( $alt ),
		$args['class'] ? ' class="' . esc_attr( $args['class'] ) . '"' : '',
		$attr // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped parts above.
	);
	echo '</picture>';
}

/**
 * The price list, as an array of rows.
 *
 * Stored as one text setting per group, one "Name | £Price" per line. A CPT
 * would be tidier in the abstract and worse in practice: the whole point of
 * the audit's second finding is that the prices have to agree with each other,
 * and they are easiest to keep in agreement when they can all be read at once
 * in a single box.
 *
 * @param string $key Setting id.
 * @return array<int,array{0:string,1:string}>
 */
function bgc_prices( $key ) {
	$rows = array();
	foreach ( preg_split( '/\R/', (string) bgc_opt( $key ) ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		if ( '' === $parts[0] ) {
			continue;
		}
		$rows[] = array( $parts[0], isset( $parts[1] ) ? $parts[1] : '' );
	}
	return $rows;
}

/**
 * Print a price list.
 *
 * @param string $key Setting id.
 */
function bgc_price_list( $key ) {
	$rows = bgc_prices( $key );
	if ( ! $rows ) {
		return;
	}
	echo '<ul class="plist" role="list">';
	foreach ( $rows as $row ) {
		printf(
			'<li><span class="pname">%s</span><span class="pdot"></span><span class="pprice">%s</span></li>',
			esc_html( $row[0] ),
			esc_html( $row[1] )
		);
	}
	echo '</ul>';
}

/**
 * The phone number, digits only, for a tel: href.
 *
 * The audit's fifth finding was that the number appeared on all six pages as
 * plain text with no tel: link anywhere on the site.
 *
 * @return string
 */
function bgc_tel_href() {
	$raw = preg_replace( '/[^0-9+]/', '', (string) bgc_opt( 'phone', '07752 661713' ) );
	if ( 0 === strpos( $raw, '0' ) ) {
		$raw = '+44' . substr( $raw, 1 );
	}
	return $raw;
}
