<?php
/**
 * Plugin Name: Bloomin' Good Cupcakes — enquiries
 * Description: Stores, filters and delivers order enquiries. Lives in mu-plugins
 *              so that changing or updating the theme can never take the lead
 *              handling with it.
 * Version:     1.1.0
 * Author:      Smile Creative
 * Author URI:  https://smilecreative.agency/
 *
 * WHY THIS IS NOT IN THE THEME
 * The QC checklist is explicit: custom form handlers must live in a mu-plugin or
 * child theme, never in the parent theme, because a theme update silently wipes
 * them and the leads stop with no error anywhere. That rule was written after
 * Logan's Removals ran for about two weeks saving quotes and showing a success
 * message while its email path was commented out. The same reasoning applies to
 * the post type: if the enquiries are registered by the theme and the theme is
 * switched, every stored enquiry becomes invisible in wp-admin even though the
 * rows are still in the database.
 *
 * @package bloomingood
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BGC_ENQ_VERSION', '1.1.0' );

/* -------------------------------------------------------------------------
 * Storage
 * ---------------------------------------------------------------------- */

/**
 * Register the enquiry post type.
 *
 * Not public: an enquiry holds a customer's name, phone number and event date,
 * and none of that belongs on a URL.
 */
function bgc_enquiry_cpt() {
	register_post_type(
		'bgc_enquiry',
		array(
			'labels'              => array(
				'name'          => __( 'Enquiries', 'bloomingood' ),
				'singular_name' => __( 'Enquiry', 'bloomingood' ),
				'menu_name'     => __( 'Enquiries', 'bloomingood' ),
				'search_items'  => __( 'Search enquiries', 'bloomingood' ),
				'not_found'     => __( 'No enquiries yet.', 'bloomingood' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-email-alt',
			'menu_position'       => 26,
			'supports'            => array( 'title' ),
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'has_archive'         => false,
			'rewrite'             => false,
			'exclude_from_search' => true,
		)
	);
}
add_action( 'init', 'bgc_enquiry_cpt' );

/**
 * The order types offered on the form.
 *
 * The last one matters most: somebody who only has a question must be able to
 * say so without being made to pick a product first.
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

/* -------------------------------------------------------------------------
 * Spam scoring
 * ---------------------------------------------------------------------- */

/**
 * Score a submission for spam, and say WHY.
 *
 * Returns '' for a clean enquiry, otherwise a short reason. Nothing here ever
 * rejects: a scored enquiry is stored and flagged, and only the email is
 * skipped. For a business whose enquiries are its orders, filing a junk one
 * costs a glance and losing a real one costs the order.
 *
 * The behaviour checks (honeypot, timer) are the easy half and a bot driving a
 * real browser passes both, so the content checks below carry most of the load.
 *
 * @param array $data   Sanitised submission.
 * @param array $raw    Raw request values needed for the behaviour checks.
 * @return string Reason, or '' if clean.
 */
function bgc_enquiry_spam_reason( $data, $raw ) {

	// 1. Honeypot: a field no person can see, so anything in it is a machine.
	if ( '' !== trim( (string) $raw['website'] ) ) {
		return 'honeypot';
	}

	// 2. Timer, from page render. Under three seconds is not typing.
	$opened = absint( $raw['t'] );
	if ( $opened && ( time() - $opened ) < 3 ) {
		return 'submitted in under 3 seconds';
	}
	// And a form left open for over an hour is a replayed token, not a customer.
	if ( $opened && ( time() - $opened ) > HOUR_IN_SECONDS ) {
		return 'form was open over an hour';
	}

	$message = (string) $data['message'];
	$all     = $message . ' ' . $data['name'] . ' ' . $data['email'];

	// 3. Any HTML in the body. Real people do not write tags into a cake enquiry.
	if ( preg_match( '~<\s*/?\s*[a-z][^>]*>~i', $message ) ) {
		return 'HTML tags in the message';
	}

	// 4. Links. One might be a Pinterest reference for a design, which is a
	//    genuine thing a customer does, so one is allowed and two is not.
	$links = preg_match_all( '~(https?://|www\.)~i', $message );
	if ( $links >= 2 ) {
		return sprintf( '%d links in the message', $links );
	}

	// 5. Non-Latin script. This site sells collection-only cakes in one Norfolk
	//    village; a message in Cyrillic or Chinese is not a customer.
	if ( preg_match( '~[\x{0400}-\x{04FF}\x{4E00}-\x{9FFF}\x{0600}-\x{06FF}\x{0590}-\x{05FF}]~u', $all ) ) {
		return 'non-Latin script';
	}

	// 6. Instant-block phrases: the shapes that show up in every contact form.
	$blocked = apply_filters(
		'bgc_enquiry_blocked_terms',
		array(
			'seo services', 'guest post', 'backlink', 'link building', 'crypto',
			'bitcoin', 'forex', 'casino', 'viagra', 'cialis', 'pharmacy',
			'loan offer', 'darknet', 'dark-net', 'tor market', '.onion',
			't.me/', 'telegram @', 'binary option', 'web design services',
			'increase your traffic', 'rank your website',
		)
	);
	$haystack = strtolower( $all );
	foreach ( $blocked as $term ) {
		if ( false !== strpos( $haystack, $term ) ) {
			return 'blocked phrase: ' . $term;
		}
	}

	// 7. Throwaway and bulk-sender domains.
	$domains = apply_filters(
		'bgc_enquiry_blocked_domains',
		array(
			'rambler.ru', 'mail.ru', 'yandex.ru', 'bk.ru', 'list.ru', 'inbox.ru',
			'mailinator.com', 'guerrillamail.com', 'sharklasers.com',
			'10minutemail.com', 'tempmail.com', 'trashmail.com',
		)
	);
	$at = strrchr( strtolower( $data['email'] ), '@' );
	if ( $at ) {
		$host = ltrim( $at, '@' );
		foreach ( $domains as $d ) {
			if ( $host === $d || substr( $host, - ( strlen( $d ) + 1 ) ) === '.' . $d ) {
				return 'throwaway email domain: ' . $host;
			}
		}
	}

	// 8. A message with no letters in it at all.
	if ( '' !== $message && ! preg_match( '~\p{L}~u', $message ) ) {
		return 'message contains no words';
	}

	return '';
}

/* -------------------------------------------------------------------------
 * The handler
 * ---------------------------------------------------------------------- */

/**
 * Handle the submission.
 *
 * On template_redirect: the query is resolved, so a failed submission can be
 * re-rendered in place with the values still in the boxes, and nothing has been
 * output yet so a redirect is still possible.
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

	// wp_unslash before sanitising: WordPress slashes $_POST, so O'Hagan arrives
	// as O\'Hagan and is stored that way if this step is skipped.
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

	$suspect = bgc_enquiry_spam_reason(
		$data,
		array(
			'website' => $field( 'bgc_website' ),
			't'       => $field( 'bgc_t' ),
		)
	);

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

	// STORE FIRST. Everything after this can fail without losing the order.
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
		update_post_meta( $post_id, '_bgc_emailed', 0 );
		update_post_meta( $post_id, '_bgc_suspect', $suspect );
	}

	if ( $suspect ) {
		// Stored, and that is the whole job. Show the same confirmation a real
		// customer sees: a bot told it failed simply submits again.
		wp_safe_redirect( home_url( '/?sent=1#order' ) );
		exit;
	}

	bgc_enquiry_notify( $post_id, $data, $type, $body );

	// Redirect after a successful post, so a refresh cannot send it twice.
	wp_safe_redirect( home_url( '/?sent=1#order' ) );
	exit;
}
add_action( 'template_redirect', 'bgc_enquiry_handle' );

/**
 * Send the notification.
 *
 * @param int|WP_Error $post_id Stored enquiry.
 * @param array        $data    Submission.
 * @param string       $type    Human label for the order type.
 * @param string       $body    Assembled message body.
 */
function bgc_enquiry_notify( $post_id, $data, $type, $body ) {

	$to = function_exists( 'bgc_opt' ) ? (string) bgc_opt( 'enquiry_to' ) : '';
	if ( '' === trim( $to ) ) {
		$to = get_option( 'admin_email' );
	}
	$to = array_filter( array_map( 'trim', explode( ',', $to ) ) );

	/*
	 * Copy the agency on every lead. The QC rule exists because a silent
	 * delivery failure is otherwise discovered by the client weeks later --
	 * if it reaches us too, we notice the day it stops.
	 */
	$cc = apply_filters( 'bgc_enquiry_cc', 'wpadmin@smilecreative.agency' );

	$site    = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$subject = sprintf(
		/* translators: 1: customer name, 2: what they want */
		__( 'NEW ENQUIRY: %1$s%2$s — Bloomin\' Good Cupcakes', 'bloomingood' ),
		$data['name'],
		$type ? ' — ' . $type : ''
	);

	$saved = ( $post_id && ! is_wp_error( $post_id ) )
		? sprintf( __( 'Saved on the site: %s', 'bloomingood' ), get_edit_post_link( $post_id, 'raw' ) )
		: __( 'Note: this one could not be saved on the site, so this email is the only copy.', 'bloomingood' );

	$mail_body = sprintf(
		"%s\n\n%s\n%s\n\n%s\n\n%s\n%s\n",
		__( 'A new enquiry has come in through the website.', 'bloomingood' ),
		sprintf( __( 'Name:  %s', 'bloomingood' ), $data['name'] ),
		sprintf( __( 'Email: %s', 'bloomingood' ), $data['email'] ),
		$body,
		__( 'Reply straight to this email to answer them.', 'bloomingood' ),
		$saved
	);

	/*
	 * From must be a mailbox on this domain. Sending "as" the customer's own
	 * address is the intuitive thing to do and it is what gets the message
	 * binned, because this server is not authorised by their domain's SPF.
	 * Reply-To is what actually makes "hit reply" work.
	 */
	$domain  = preg_replace( '/^www\./', '', (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
	$headers = array(
		sprintf( 'From: %s <no-reply@%s>', $site, $domain ),
		sprintf( 'Reply-To: %s <%s>', $data['name'], $data['email'] ),
	);
	if ( $cc && ! in_array( $cc, $to, true ) ) {
		$headers[] = 'Cc: ' . $cc;
	}

	$ok = wp_mail( $to, $subject, $mail_body, $headers );

	if ( $post_id && ! is_wp_error( $post_id ) ) {
		update_post_meta( $post_id, '_bgc_emailed', $ok ? 1 : 0 );
	}
}

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
