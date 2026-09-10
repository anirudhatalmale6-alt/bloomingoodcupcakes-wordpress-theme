<?php
/**
 * A plain content page.
 *
 * Only the legal pages use this. The audit found the old site's contact, terms
 * and privacy pages had no heading at all, so this template prints the page
 * title as the h1 whether or not the editor remembered to type one.
 *
 * @package bloomingood
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<section class="band band-tall">
		<div class="shell shell-narrow">
			<h1 class="h2"><?php the_title(); ?></h1>
			<div class="prose">
				<?php the_content(); ?>
			</div>
			<p class="cred-link">
				<a class="tlink" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<span><?php esc_html_e( 'Back to the cupcakes', 'bloomingood' ); ?></span>
				</a>
			</p>
		</div>
	</section>
	<?php
endwhile;

get_footer();
