<?php
/**
 * Fallback template.
 *
 * This is a one-page site, so anything that is not the front page is sent to
 * it rather than rendered as a bare post list nobody designed.
 *
 * @package bloomingood
 */

get_header();
?>
	<section class="band band-tall">
		<div class="shell">
			<h1 class="h2"><?php the_archive_title(); ?></h1>
			<?php if ( have_posts() ) : ?>
				<div class="quotes">
					<?php
					while ( have_posts() ) :
						the_post();
						?>
						<div class="q">
							<h2 class="h3"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<?php the_excerpt(); ?>
						</div>
						<?php
					endwhile;
					?>
				</div>
			<?php else : ?>
				<p class="lede"><?php esc_html_e( 'Nothing here yet.', 'bloomingood' ); ?></p>
			<?php endif; ?>
			<p class="cred-link">
				<a class="tlink" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<span><?php esc_html_e( 'Back to the cupcakes', 'bloomingood' ); ?></span>
				</a>
			</p>
		</div>
	</section>
<?php
get_footer();
