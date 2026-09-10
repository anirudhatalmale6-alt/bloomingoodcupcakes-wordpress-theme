<?php
/**
 * The order form.
 *
 * Included by front-page.php inside .form-card. The mockup's version was a
 * disabled fieldset; this is the working one, with the same fields in the same
 * order so it looks exactly like what was approved.
 *
 * Three required fields, and only three. The old site asked for more than
 * thirty, four of them required, including a phone number and a date -- even
 * from someone who had just told it they only had a question.
 *
 * @package bloomingood
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bgc_state  = bgc_form_state();
$bgc_errors = $bgc_state['errors'];
$bgc_val    = $bgc_state['values'];

/**
 * Re-fill a field after a failed submission.
 *
 * Losing what somebody typed is the fastest way to lose the enquiry itself.
 *
 * @param string $key Field key.
 * @return string
 */
$bgc_fill = function ( $key ) use ( $bgc_val ) {
	return isset( $bgc_val[ $key ] ) ? $bgc_val[ $key ] : '';
};
?>

<?php if ( $bgc_state['sent'] ) : ?>
	<p class="formmsg ok" role="status">
		<?php esc_html_e( 'Thank you, that has come through. I read every enquiry myself and I will come back to you shortly.', 'bloomingood' ); ?>
	</p>
<?php elseif ( isset( $bgc_errors['form'] ) ) : ?>
	<p class="formmsg err" role="alert"><?php echo esc_html( $bgc_errors['form'] ); ?></p>
<?php elseif ( $bgc_errors ) : ?>
	<p class="formmsg err" role="alert">
		<?php esc_html_e( 'Almost there — there is just something to check below.', 'bloomingood' ); ?>
	</p>
<?php endif; ?>

<form method="post" action="<?php echo esc_url( home_url( '/#order' ) ); ?>" novalidate>
	<?php wp_nonce_field( 'bgc_enquiry', 'bgc_nonce' ); ?>
	<input type="hidden" name="bgc_enquiry" value="1">
	<input type="hidden" name="bgc_t" value="<?php echo esc_attr( time() ); ?>">

	<?php /* Not display:none -- some bots skip hidden fields deliberately. */ ?>
	<div class="hp" aria-hidden="true">
		<label for="bgc-website"><?php esc_html_e( 'Website', 'bloomingood' ); ?></label>
		<input id="bgc-website" name="bgc_website" type="text" tabindex="-1" autocomplete="off">
	</div>

	<div class="field<?php echo isset( $bgc_errors['name'] ) ? ' bad' : ''; ?>">
		<label for="f-name"><?php esc_html_e( 'Name', 'bloomingood' ); ?> <span class="req">*</span></label>
		<input id="f-name" name="bgc_name" type="text" autocomplete="name" required
		       value="<?php echo esc_attr( $bgc_fill( 'name' ) ); ?>"
		       <?php echo isset( $bgc_errors['name'] ) ? 'aria-describedby="e-name" aria-invalid="true"' : ''; ?>>
		<?php if ( isset( $bgc_errors['name'] ) ) : ?>
			<span class="fielderr" id="e-name"><?php echo esc_html( $bgc_errors['name'] ); ?></span>
		<?php endif; ?>
	</div>

	<div class="field<?php echo isset( $bgc_errors['email'] ) ? ' bad' : ''; ?>">
		<label for="f-email"><?php esc_html_e( 'Email Address', 'bloomingood' ); ?> <span class="req">*</span></label>
		<input id="f-email" name="bgc_email" type="email" autocomplete="email" required
		       value="<?php echo esc_attr( $bgc_fill( 'email' ) ); ?>"
		       <?php echo isset( $bgc_errors['email'] ) ? 'aria-describedby="e-email" aria-invalid="true"' : ''; ?>>
		<?php if ( isset( $bgc_errors['email'] ) ) : ?>
			<span class="fielderr" id="e-email"><?php echo esc_html( $bgc_errors['email'] ); ?></span>
		<?php endif; ?>
	</div>

	<fieldset class="typeset">
		<legend><?php esc_html_e( 'Requesting which type of cupcake?', 'bloomingood' ); ?></legend>
		<div class="radios">
			<?php
			$bgc_i = 0;
			foreach ( bgc_enquiry_types() as $bgc_key => $bgc_label ) :
				$bgc_i++;
				?>
				<label for="t<?php echo esc_attr( $bgc_i ); ?>">
					<input id="t<?php echo esc_attr( $bgc_i ); ?>" type="radio" name="bgc_type"
					       value="<?php echo esc_attr( $bgc_key ); ?>"
						<?php checked( $bgc_fill( 'type' ), $bgc_key ); ?>>
					<?php echo esc_html( $bgc_label ); ?>
				</label>
			<?php endforeach; ?>
		</div>
	</fieldset>

	<div class="field">
		<label for="f-date"><?php esc_html_e( 'Date you need it, if you know it yet', 'bloomingood' ); ?></label>
		<input id="f-date" name="bgc_date" type="text"
		       placeholder="<?php esc_attr_e( 'Optional', 'bloomingood' ); ?>"
		       value="<?php echo esc_attr( $bgc_fill( 'date' ) ); ?>">
	</div>

	<div class="field<?php echo isset( $bgc_errors['message'] ) ? ' bad' : ''; ?>">
		<label for="f-message"><?php esc_html_e( 'Message', 'bloomingood' ); ?> <span class="req">*</span></label>
		<textarea id="f-message" name="bgc_message" required
		          <?php echo isset( $bgc_errors['message'] ) ? 'aria-describedby="e-message" aria-invalid="true"' : ''; ?>><?php echo esc_textarea( $bgc_fill( 'message' ) ); ?></textarea>
		<?php if ( isset( $bgc_errors['message'] ) ) : ?>
			<span class="fielderr" id="e-message"><?php echo esc_html( $bgc_errors['message'] ); ?></span>
		<?php endif; ?>
	</div>

	<div class="field">
		<label for="f-phone"><?php esc_html_e( 'Phone, if you would rather I called', 'bloomingood' ); ?></label>
		<input id="f-phone" name="bgc_phone" type="tel" autocomplete="tel"
		       placeholder="<?php esc_attr_e( 'Optional', 'bloomingood' ); ?>"
		       value="<?php echo esc_attr( $bgc_fill( 'phone' ) ); ?>">
	</div>

	<?php
	/*
	 * The full product picker, behind a toggle. Somebody who already knows
	 * exactly what they want can name it; everybody else never sees the list.
	 * The old form put all of this in front of everyone, required.
	 */
	?>
	<details class="detail">
		<summary><?php esc_html_e( 'I know exactly what I want', 'bloomingood' ); ?></summary>
		<div class="detail-cols">
			<?php
			foreach ( array(
				__( 'Bouquets', 'bloomingood' )   => 'prices_bouquet',
				__( 'Boxes', 'bloomingood' )      => 'prices_box',
				__( 'Party cakes', 'bloomingood' ) => 'prices_drip',
			) as $bgc_head => $bgc_key ) :
				$bgc_rows = bgc_prices( $bgc_key );
				if ( 'prices_drip' === $bgc_key ) {
					$bgc_rows = array_merge( $bgc_rows, bgc_prices( 'prices_naked' ) );
				}
				if ( ! $bgc_rows ) {
					continue;
				}
				?>
				<div>
					<h3><?php echo esc_html( $bgc_head ); ?></h3>
					<ul role="list">
						<?php foreach ( $bgc_rows as $bgc_row ) : ?>
							<li><?php echo esc_html( $bgc_row[0] ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
		</div>
	</details>

	<p class="form-note"><?php esc_html_e( 'Any allergies must be declared at the time of placing your order.', 'bloomingood' ); ?></p>
	<p class="form-submit">
		<button class="btn" type="submit"><?php esc_html_e( 'SEND ENQUIRY', 'bloomingood' ); ?></button>
	</p>
</form>
