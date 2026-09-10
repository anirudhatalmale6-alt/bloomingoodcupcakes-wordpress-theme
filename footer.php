<?php
/**
 * Site footer.
 *
 * Markup follows the approved mockup exactly -- .ftr-in / .ftr-c / .ftr-meta --
 * because the stylesheet came from the mockup and invented class names would
 * simply not be styled.
 *
 * Both the phone number and the email are real links. On the old site the number
 * was plain text on all six pages and the email sat behind an obfuscation
 * script, so on a phone neither could be used with one tap.
 *
 * @package bloomingood
 */

?>
	</main>

	<footer class="ftr">
		<div class="shell">
			<div class="ftr-in">
				<div>
					<img class="ftr-logo" src="<?php echo esc_url( BGC_URI . '/assets/img/logo.jpg' ); ?>"
					     width="72" height="72" loading="lazy"
					     alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
					<p class="ftr-strap"><?php esc_html_e( 'Bespoke Cupcakes Made To Order in Hethersett, Norwich', 'bloomingood' ); ?></p>
				</div>
				<div class="ftr-c">
					<a href="tel:<?php echo esc_attr( bgc_tel_href() ); ?>"><?php echo esc_html( bgc_opt( 'phone' ) ); ?></a>
					<a href="mailto:<?php echo esc_attr( bgc_opt( 'email' ) ); ?>"><?php echo esc_html( bgc_opt( 'email' ) ); ?></a>
					<a href="#order"><?php esc_html_e( 'Send an enquiry', 'bloomingood' ); ?></a>
					<span><?php echo esc_html( bgc_opt( 'address' ) ); ?></span>
				</div>
				<div class="ftr-meta">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'container'      => false,
							'items_wrap'     => '%3$s',
							'depth'          => 1,
							'fallback_cb'    => '__return_empty_string',
							'link_before'    => '',
						)
					);
					?>
					<?php if ( bgc_opt( 'fhrs_url' ) ) : ?>
						<a href="<?php echo esc_url( bgc_opt( 'fhrs_url' ) ); ?>" rel="noopener">
							<?php
							printf(
								/* translators: %s: the rating, e.g. 5 */
								esc_html__( 'Food hygiene rating %s', 'bloomingood' ),
								esc_html( bgc_opt( 'fhrs_rating' ) )
							);
							?>
						</a>
					<?php endif; ?>
				</div>
			</div>
			<p class="ftr-btm">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> Bloomin&rsquo; Good Cupcakes</p>
		</div>
	</footer>

<?php wp_footer(); ?>
</body>
</html>
