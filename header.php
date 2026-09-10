<?php
/**
 * Page head and site header.
 *
 * The audit's fourth finding was that the order page appeared in the navigation
 * on none of the six pages of the old site -- the menu read Home, Prices,
 * Showcase, and the only order form was reachable only from buttons part way
 * down a page. The header here is sticky and carries "Start your order" at
 * every scroll depth, on phones as well as desktop.
 *
 * @package bloomingood
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip" href="#top"><?php esc_html_e( 'Skip to content', 'bloomingood' ); ?></a>

<?php
$bgc_notice = bgc_opt( 'notice' );
if ( '' !== $bgc_notice ) :
	?>
	<p class="notice-bar"><?php echo esc_html( $bgc_notice ); ?></p>
	<?php
endif;
?>

<header class="hdr">
	<div class="hdr-in">
		<a class="hdr-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img src="<?php echo esc_url( BGC_URI . '/assets/img/logo.jpg' ); ?>"
			     width="54" height="54"
			     alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<span class="hdr-name">Bloomin&rsquo; Good<br>Cupcakes</span>
		</a>
		<nav class="hdr-nav" aria-label="<?php esc_attr_e( 'Main', 'bloomingood' ); ?>">
			<a href="#bouquets"><?php esc_html_e( 'Prices', 'bloomingood' ); ?></a>
			<a href="#gallery"><?php esc_html_e( 'Showcase', 'bloomingood' ); ?></a>
			<a href="#order"><?php esc_html_e( 'Order', 'bloomingood' ); ?></a>
		</nav>
		<a class="hdr-tel" href="tel:<?php echo esc_attr( bgc_tel_href() ); ?>"><?php echo esc_html( bgc_opt( 'phone' ) ); ?></a>
		<a class="btn btn-sm" href="#order"><?php esc_html_e( 'Start your order', 'bloomingood' ); ?></a>
	</div>
</header>

<main id="top">
