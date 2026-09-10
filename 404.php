<?php
/**
 * Not found.
 *
 * The old site returned 404 for /shop/ and /cart/ while seven buttons across
 * the site said "book online". Anyone who still has one of those links
 * bookmarked lands here, so this page has to do something useful.
 *
 * @package bloomingood
 */

get_header();
?>
	<section class="band band-tall">
		<div class="shell">
			<p class="eyebrow"><?php esc_html_e( 'That page has moved', 'bloomingood' ); ?></p>
			<h1 class="h2"><?php esc_html_e( 'Everything now lives on one page', 'bloomingood' ); ?></h1>
			<p class="lede">
				<?php esc_html_e( 'The prices, the showcase and the order form are all on the home page now. Here are the three people usually want.', 'bloomingood' ); ?>
			</p>
			<p class="hero-acts">
				<a class="btn btn-lg" href="<?php echo esc_url( home_url( '/#order' ) ); ?>"><?php esc_html_e( 'Start your order', 'bloomingood' ); ?></a>
				<a class="tlink" href="<?php echo esc_url( home_url( '/#bouquets' ) ); ?>"><span><?php esc_html_e( 'See the price list', 'bloomingood' ); ?></span></a>
			</p>
		</div>
	</section>
<?php
get_footer();
