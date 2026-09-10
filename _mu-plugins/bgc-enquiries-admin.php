<?php
/**
 * Plugin Name: Bloomin' Good Cupcakes — enquiries admin
 * Description: The Enquiries list screen. Separate file so the delivery path in
 *              bgc-enquiries.php stays readable.
 * Version:     1.1.0
 * Author:      Smile Creative
 *
 * @package bloomingood
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Columns.
 *
 * @param array $cols Existing columns.
 * @return array
 */
function bgc_enquiry_columns( $cols ) {
	return array(
		'cb'        => isset( $cols['cb'] ) ? $cols['cb'] : '',
		'title'     => __( 'From', 'bloomingood' ),
		'bgc_what'  => __( 'Looking for', 'bloomingood' ),
		'bgc_when'  => __( 'Date needed', 'bloomingood' ),
		'bgc_reach' => __( 'Contact', 'bloomingood' ),
		'bgc_mail'  => __( 'Emailed', 'bloomingood' ),
		'date'      => __( 'Received', 'bloomingood' ),
	);
}
add_filter( 'manage_bgc_enquiry_posts_columns', 'bgc_enquiry_columns' );

/**
 * Column content.
 *
 * @param string $col     Column id.
 * @param int    $post_id Post id.
 */
function bgc_enquiry_column( $col, $post_id ) {
	switch ( $col ) {
		case 'bgc_what':
			echo esc_html( get_post_meta( $post_id, '_bgc_type_label', true ) ?: '—' );
			break;

		case 'bgc_when':
			echo esc_html( get_post_meta( $post_id, '_bgc_date', true ) ?: '—' );
			break;

		case 'bgc_reach':
			$email = get_post_meta( $post_id, '_bgc_email', true );
			$phone = get_post_meta( $post_id, '_bgc_phone', true );
			if ( $email ) {
				printf( '<a href="mailto:%1$s">%1$s</a>', esc_attr( $email ) );
			}
			if ( $phone ) {
				printf(
					'<br><a href="tel:%s">%s</a>',
					esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ),
					esc_html( $phone )
				);
			}
			break;

		case 'bgc_mail':
			// A held enquiry is not a failure and must not read like one.
			$suspect = get_post_meta( $post_id, '_bgc_suspect', true );
			if ( $suspect ) {
				printf(
					'<span title="%s" style="color:#8A6D1F">%s</span>',
					esc_attr( sprintf( __( 'Spam check: %s. Stored, not emailed.', 'bloomingood' ), $suspect ) ),
					esc_html__( 'Held', 'bloomingood' )
				);
				break;
			}
			// 0/1 set at submission. No flag at all predates the flag; say so
			// rather than guessing.
			$flag = get_post_meta( $post_id, '_bgc_emailed', true );
			if ( '' === $flag ) {
				echo '—';
			} elseif ( $flag ) {
				echo '<span style="color:#1F5B31">' . esc_html__( 'Sent', 'bloomingood' ) . '</span>';
			} else {
				echo '<strong style="color:#8A2020">' . esc_html__( 'NOT sent', 'bloomingood' ) . '</strong>';
			}
			break;
	}
}
add_action( 'manage_bgc_enquiry_posts_custom_column', 'bgc_enquiry_column', 10, 2 );

/**
 * The whole enquiry beside the message on the edit screen.
 */
function bgc_enquiry_metabox() {
	add_meta_box(
		'bgc_enquiry_detail',
		__( 'Enquiry details', 'bloomingood' ),
		function ( $post ) {
			$rows = array(
				__( 'Name', 'bloomingood' )        => get_post_meta( $post->ID, '_bgc_name', true ),
				__( 'Email', 'bloomingood' )       => get_post_meta( $post->ID, '_bgc_email', true ),
				__( 'Phone', 'bloomingood' )       => get_post_meta( $post->ID, '_bgc_phone', true ),
				__( 'Looking for', 'bloomingood' ) => get_post_meta( $post->ID, '_bgc_type_label', true ),
				__( 'Date needed', 'bloomingood' ) => get_post_meta( $post->ID, '_bgc_date', true ),
				__( 'Spam check', 'bloomingood' )  => get_post_meta( $post->ID, '_bgc_suspect', true ),
			);
			echo '<table class="widefat striped"><tbody>';
			foreach ( $rows as $k => $v ) {
				printf(
					'<tr><th style="width:150px">%s</th><td>%s</td></tr>',
					esc_html( $k ),
					esc_html( $v ?: '—' )
				);
			}
			echo '</tbody></table>';
		},
		'bgc_enquiry',
		'side'
	);
}
add_action( 'add_meta_boxes', 'bgc_enquiry_metabox' );

/**
 * Nobody types an enquiry by hand, so remove the "Add new" affordances.
 */
function bgc_enquiry_no_new() {
	global $submenu;
	if ( isset( $submenu['edit.php?post_type=bgc_enquiry'] ) ) {
		foreach ( $submenu['edit.php?post_type=bgc_enquiry'] as $i => $item ) {
			if ( isset( $item[2] ) && 'post-new.php?post_type=bgc_enquiry' === $item[2] ) {
				unset( $submenu['edit.php?post_type=bgc_enquiry'][ $i ] );
			}
		}
	}
}
add_action( 'admin_menu', 'bgc_enquiry_no_new', 999 );
