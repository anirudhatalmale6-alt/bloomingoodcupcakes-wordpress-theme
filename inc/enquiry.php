<?php
/**
 * The order enquiry form.
 *
 * The mockup's form was a disabled fieldset -- a picture of a form. This is the
 * working one, and it is deliberately built store-first: the enquiry is written
 * to the database BEFORE any attempt to email it. wp_mail() returning true only
 * means the message was handed to the mailer; it says nothing about whether it
 * arrived. If mail is misconfigured on the day, the order is still on the site
 * waiting under Enquiries rather than gone.
 *
 * The old site's form asked for more than thirty fields, four of them required,
 * including a phone number and a date, even from someone who picked "Nothing
 * yet, I just have a question". This one requires a name, an email and a
 * message. The date and phone are optional and present, and the full product
 * picker sits behind a toggle for anyone who already knows what they want.
 *
 * @package bloomingood
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the storage post type.
 *
 * Not public: an enquiry has a customer's name, phone number and event date in
 * it, and none of that belongs on a URL.
 */
function bgc_enquiry_cpt() {
	register_post_type(
		'bgc_enquiry',
		array(
			'labels'          => array(
				'name'          => __( 'Enquiries', 'bloomingood' ),
				'singular_name' => __( 'Enquiry', 'bloomingood' ),
				'menu_name'     => __( 'Enquiries', 'bloomingood' ),
				'search_items'  => __( 'Search enquiries', 'bloomingood' ),
				'not_found'     => __( 'No enquiries yet.', 'bloomingood' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-email-alt',
			'menu_position'   => 26,
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
			'has_archive'     => false,
			'rewrite'         => false,
			'exclude_from_search' => true,
		)
	);
}
add_action( 'init', 'bgc_enquiry_cpt' );

/**
 * The order types offered on the form.
 *
 * The last one matters most: someone who only has a question must be able to
 * say so without being asked to pick a product first.
 *
 * @return array<string,string>
 */
function bgc_enquiry_types() {
	return array(
		'boxed'    => __( 'Boxed Cupcakes', 'bloomingood' ),
		'bouquet'  => __( 'Bouquet Cupcakes', 'bloomingood' ),
		'party'    => __( 'Party Cakes', 'bloomingood' ),
		'wedding'  => __( 'Wedding Bouquet', 'bloomingood' ),
		'question' => __( 'Nothing yet, I just have a question', 'bloomingood' ),
	);
}

/**
 * Handle the submission.
 *
 * Runs on template_redirect rather than init: it needs the query resolved so a
 * failed submission can be re-rendered in place with the values still in the
 * boxes, and it must run before any output.
 */
function bgc_enquiry_handle() {
	if ( 'POST' !== ( isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : '' ) ) {
		return;
	}
	if ( ! isset( $_POST['bgc_enquiry'] ) ) {
		return;
	}

	$state = array( 'errors' => array(), 'values' => array(), 'sent' => false );

	if ( ! isset( $_POST['bgc_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['bgc_nonce'] ) ), 'bgc_enquiry' ) ) {
		$state['errors']['form'] = __( 'That form had been open a while and the security check expired. Nothing is lost, please press send again.', 'bloomingood' );
		$GLOBALS['bgc_form_state'] = $state;
		return;
	}

	// wp_unslash before sanitising: WordPress adds slashes to $_POST, so
	// O'Hagan arrives as O\'Hagan and is stored that way if this is skipped.
	$field = function ( $key ) {
		return isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified above.
	};

	$data = array(
		'name'    => $field( 'bgc_name' ),
		'email'   => $field( 'bgc_email' ),
		'phone'   => $field( 'bgc_phone' ),
		'date'    => $field( 'bgc_date' ),
		'type'    => $field( 'bgc_type' ),
		'message' => isset( $_POST['bgc_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['bgc_message'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Missing
	);
	$state['values'] = $data;

	/*
	 * Two quiet traps. Neither is shown to a person: the honeypot is a field a
	 * human never sees, and the timer is the number of seconds the form was on
	 * screen. Both are cheap and neither inconveniences a customer, which a
	 * CAPTCHA on a cake shop very much would.
	 *
	 * A tripped trap FLAGS the enquiry, it does not discard it. The first
	 * version discarded, and testing this form on the build site is what showed
	 * why that is wrong: my own submission tripped the timer, the page said
	 * "thank you, that has come through", and nothing had been stored. A real
	 * customer who pastes a prepared message and presses send quickly would get
	 * the same silent hole. For a business whose enquiries ARE its orders,
	 * losing one is far worse than filing one that turns out to be junk, so a
	 * flagged enquiry is stored and simply not emailed.
	 */
	$suspect = '';
	if ( '' !== $field( 'bgc_website' ) ) {
		$suspect = 'honeypot';
	} else {
		$opened = absint( $field( 'bgc_t' ) );
		if ( $opened && ( time() - $opened ) < 3 ) {
			$suspect = 'too fast';
		}
	}

	if ( '' === $data['name'] ) {
		$state['errors']['name'] = __( 'Please tell me your name.', 'bloomingood' );
	}
	if ( '' === $data['email'] ) {
		$state['errors']['email'] = __( 'Please add an email address so I can reply.', 'bloomingood' );
	} elseif ( ! is_email( $data['email'] ) ) {
		$state['errors']['email'] = __( 'That email address does not look quite right. Could you check it?', 'bloomingood' );
	}
	if ( '' === $data['message'] ) {
		$state['errors']['message'] = __( 'Please tell me a little about what you are planning.', 'bloomingood' );
	}

	if ( $state['errors'] ) {
		$GLOBALS['bgc_form_state'] = $state;
		return;
	}

	$types = bgc_enquiry_types();
	$type  = isset( $types[ $data['type'] ] ) ? $types[ $data['type'] ] : '';

	$lines = array();
	if ( $type ) {
		$lines[] = sprintf( __( 'Looking for: %s', 'bloomingood' ), $type );
	}
	if ( '' !== $data['date'] ) {
		$lines[] = sprintf( __( 'Date needed: %s', 'bloomingood' ), $data['date'] );
	}
	if ( '' !== $data['phone'] ) {
		$lines[] = sprintf( __( 'Phone: %s', 'bloomingood' ), $data['phone'] );
	}
	$body = $data['message'];
	if ( $lines ) {
		$body = implode( "\n", $lines ) . "\n\n" . $body;
	}

	// STORE FIRST. Everything after this point can fail without losing the order.
	$post_id = wp_insert_post(
		array(
			'post_type'    => 'bgc_enquiry',
			'post_status'  => 'publish',
			'post_title'   => ( $suspect ? '[SPAM?] ' : '' ) . $data['name'] . ( $type ? ' — ' . $type : '' ),
			'post_content' => $body,
		),
		true
	);

	if ( ! is_wp_error( $post_id ) ) {
		foreach ( $data as $k => $v ) {
			update_post_meta( $post_id, '_bgc_' . $k, $v );
		}
		update_post_meta( $post_id, '_bgc_type_label', $type );
		update_post_meta( $post_id, '_bgc_ip', '' );  // deliberately not stored
		update_post_meta( $post_id, '_bgc_emailed', 0 );
		update_post_meta( $post_id, '_bgc_suspect', $suspect );
	}

	if ( $suspect ) {
		// Stored above, and that is the whole job. Show the same confirmation a
		// real customer sees: a bot told it failed simply submits again.
		wp_safe_redirect( home_url( '/?sent=1#order' ) );
		exit;
	}

	$to = bgc_opt( 'enquiry_to' );
	if ( '' === $to ) {
		$to = get_option( 'admin_email' );
	}

	$site    = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$subject = sprintf(
		/* translators: 1: customer name, 2: what they want */
		__( 'New enquiry from %1$s%2$s', 'bloomingood' ),
		$data['name'],
		$type ? ' — ' . $type : ''
	);

	$mail_body = sprintf(
		"%s\n\n%s\n%s\n\n%s\n\n%s\n%s\n",
		__( 'A new enquiry has come in through the website.', 'bloomingood' ),
		sprintf( __( 'Name:  %s', 'bloomingood' ), $data['name'] ),
		sprintf( __( 'Email: %s', 'bloomingood' ), $data['email'] ),
		$body,
		__( 'Reply straight to this email to answer them.', 'bloomingood' ),
		$post_id && ! is_wp_error( $post_id )
			? sprintf( __( 'Saved on the site: %s', 'bloomingood' ), get_edit_post_link( $post_id, 'raw' ) )
			: __( 'Note: this one could not be saved on the site, so this email is the only copy.', 'bloomingood' )
	);

	/*
	 * From must be a mailbox on this domain. Sending as the customer's own
	 * address is the intuitive thing to do and it is what gets the message
	 * binned -- the domain's SPF record does not authorise this server to send
	 * as them. Reply-To is what actually makes "hit reply" work.
	 */
	$domain  = wp_parse_url( home_url(), PHP_URL_HOST );
	$domain  = preg_replace( '/^www\./', '', (string) $domain );
	$headers = array(
		sprintf( 'From: %s <no-reply@%s>', $site, $domain ),
		sprintf( 'Reply-To: %s <%s>', $data['name'], $data['email'] ),
	);

	$ok = wp_mail( array_map( 'trim', explode( ',', $to ) ), $subject, $mail_body, $headers );

	if ( $post_id && ! is_wp_error( $post_id ) ) {
		update_post_meta( $post_id, '_bgc_emailed', $ok ? 1 : 0 );
	}

	/*
	 * Redirect after a successful post, so a refresh cannot send the enquiry a
	 * second time. The customer sees the confirmation at the form, not at the
	 * top of the page.
	 */
	wp_safe_redirect( home_url( '/?sent=1#order' ) );
	exit;
}
add_action( 'template_redirect', 'bgc_enquiry_handle' );

/**
 * The state of the form for this request.
 *
 * @return array{errors:array,values:array,sent:bool}
 */
function bgc_form_state() {
	$state = isset( $GLOBALS['bgc_form_state'] ) ? $GLOBALS['bgc_form_state'] : array();
	return wp_parse_args(
		$state,
		array(
			'errors' => array(),
			'values' => array(),
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only display flag.
			'sent'   => isset( $_GET['sent'] ),
		)
	);
}
