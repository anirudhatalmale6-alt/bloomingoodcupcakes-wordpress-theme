<?php
/**
 * Customizer settings.
 *
 * Everything Claire is likely to want to change, and nothing she is not. The
 * site ships complete: every field below has the approved copy as its default,
 * so an empty box means "unchanged", never "missing".
 *
 * @package bloomingood
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Defaults, in one place so the templates and the Customizer cannot drift.
 *
 * @return array<string,string>
 */
function bgc_defaults() {
	return array(
		'phone'          => '07752 661713',
		'email'          => 'hello@bloomingoodcupcakes.co.uk',
		'address'        => '5a Lynch Green, Hethersett, Norwich NR9 3JU',
		'hours'          => 'Monday to Saturday, 9am to 5pm. Closed Sunday.',
		'fhrs_rating'    => '5',
		'fhrs_authority' => 'South Norfolk',
		'fhrs_date'      => '14 July 2025',
		'fhrs_url'       => 'https://ratings.food.gov.uk/business/1227205',
		'notice'         => '',
		'prices_bouquet' => "Classic Cupcake Bouquet x 3 | £15\nClassic Cupcake Bouquet x 7 | £28\nDeluxe Cupcake Bouquet x 7 | £34\nDeluxe Cupcake Bouquet x 12 | £50\nDeluxe Cupcake Bouquet x 19 | £70",
		'prices_box'     => "Boxed Deluxe Cupcakes x 6 | £27\nBoxed Deluxe Cupcakes x 12 | £44",
		'prices_naked'   => "3 Layer 6 inch | £35\n3 Layer 7 inch | £40\n3 Layer 8 inch | £45",
		'prices_drip'    => "3 Layer 7 inch | £70\n2 Layer 8 inch | £80\n3 Layer 8 inch | £100",
	);
}

/**
 * Register the panel.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function bgc_customize( $wp_customize ) {
	$d = bgc_defaults();

	$wp_customize->add_panel(
		'bgc',
		array(
			'title'       => __( 'Bloomin\' Good Cupcakes', 'bloomingood' ),
			'description' => __( 'Everything on the page that changes. Anything left blank keeps what the site shipped with.', 'bloomingood' ),
			'priority'    => 20,
		)
	);

	/* ---- Contact --------------------------------------------------------- */
	$wp_customize->add_section(
		'bgc_contact',
		array(
			'title'       => __( 'Phone, email and address', 'bloomingood' ),
			'panel'       => 'bgc',
			'description' => __( 'The phone number becomes a tap-to-call link everywhere it appears.', 'bloomingood' ),
		)
	);
	$fields = array(
		'phone'   => array( __( 'Phone number', 'bloomingood' ), 'text' ),
		'email'   => array( __( 'Email address', 'bloomingood' ), 'text' ),
		'address' => array( __( 'Collection address', 'bloomingood' ), 'text' ),
		'hours'   => array( __( 'Opening hours', 'bloomingood' ), 'text' ),
	);
	foreach ( $fields as $key => $meta ) {
		$wp_customize->add_setting(
			'bgc_' . $key,
			array(
				'default'           => $d[ $key ],
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'bgc_' . $key,
			array( 'label' => $meta[0], 'section' => 'bgc_contact', 'type' => $meta[1] )
		);
	}

	/* ---- Notice ---------------------------------------------------------- */
	$wp_customize->add_section(
		'bgc_notice',
		array(
			'title'       => __( 'Notice bar', 'bloomingood' ),
			'panel'       => 'bgc',
			'description' => __( 'A single line above the header, for when you are fully booked, on holiday, or taking Christmas orders. Leave it empty and the bar does not appear at all.', 'bloomingood' ),
		)
	);
	$wp_customize->add_setting(
		'bgc_notice',
		array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' )
	);
	$wp_customize->add_control(
		'bgc_notice',
		array( 'label' => __( 'Notice', 'bloomingood' ), 'section' => 'bgc_notice', 'type' => 'text' )
	);

	/* ---- Hygiene rating -------------------------------------------------- */
	$wp_customize->add_section(
		'bgc_fhrs',
		array(
			'title'       => __( 'Food hygiene rating', 'bloomingood' ),
			'panel'       => 'bgc',
			'description' => __( 'The rating is shown with the authority that awarded it, the date, and a link to the public register, so a customer can check it in about ten seconds. A claim somebody can verify is a different class of proof from a badge.', 'bloomingood' ),
		)
	);
	foreach ( array(
		'fhrs_rating'    => __( 'Rating', 'bloomingood' ),
		'fhrs_authority' => __( 'Awarded by', 'bloomingood' ),
		'fhrs_date'      => __( 'Date awarded', 'bloomingood' ),
		'fhrs_url'       => __( 'Link to the register entry', 'bloomingood' ),
	) as $key => $label ) {
		$wp_customize->add_setting(
			'bgc_' . $key,
			array(
				'default'           => $d[ $key ],
				'sanitize_callback' => ( 'fhrs_url' === $key ) ? 'esc_url_raw' : 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'bgc_' . $key,
			array( 'label' => $label, 'section' => 'bgc_fhrs', 'type' => 'text' )
		);
	}

	/* ---- Prices ---------------------------------------------------------- */
	$wp_customize->add_section(
		'bgc_prices',
		array(
			'title'       => __( 'Prices', 'bloomingood' ),
			'panel'       => 'bgc',
			'description' => __( 'One line per item, written as: Name | £Price. This is the only place prices are stored, so the page cannot quote two different figures for the same cake the way the old site did.', 'bloomingood' ),
		)
	);
	foreach ( array(
		'prices_bouquet' => __( 'Bouquets', 'bloomingood' ),
		'prices_box'     => __( 'Boxes', 'bloomingood' ),
		'prices_naked'   => __( 'Naked cakes', 'bloomingood' ),
		'prices_drip'    => __( 'Overload drip cakes', 'bloomingood' ),
	) as $key => $label ) {
		$wp_customize->add_setting(
			'bgc_' . $key,
			array(
				'default'           => $d[ $key ],
				'sanitize_callback' => 'bgc_sanitize_lines',
			)
		);
		$wp_customize->add_control(
			'bgc_' . $key,
			array( 'label' => $label, 'section' => 'bgc_prices', 'type' => 'textarea' )
		);
	}

	/* ---- Where enquiries go ---------------------------------------------- */
	$wp_customize->add_section(
		'bgc_leads',
		array(
			'title'       => __( 'Where enquiries go', 'bloomingood' ),
			'panel'       => 'bgc',
			'description' => __( 'Every enquiry is saved on the site under Enquiries whether or not the email gets through, so nothing is ever lost to a mail problem.', 'bloomingood' ),
		)
	);
	$wp_customize->add_setting(
		'bgc_enquiry_to',
		array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' )
	);
	$wp_customize->add_control(
		'bgc_enquiry_to',
		array(
			'label'       => __( 'Send enquiries to', 'bloomingood' ),
			'description' => __( 'Leave blank to use the site admin address. Separate several with commas.', 'bloomingood' ),
			'section'     => 'bgc_leads',
			'type'        => 'text',
		)
	);
}
add_action( 'customize_register', 'bgc_customize' );

/**
 * Sanitise a multi-line "Name | £Price" box.
 *
 * sanitize_textarea_field would do most of this, but it also collapses the
 * newlines this format depends on, so the whole price list would arrive as one
 * line and render as one row.
 *
 * @param string $value Raw value.
 * @return string
 */
function bgc_sanitize_lines( $value ) {
	$out = array();
	foreach ( preg_split( '/\R/', (string) $value ) as $line ) {
		$line = sanitize_text_field( $line );
		if ( '' !== trim( $line ) ) {
			$out[] = $line;
		}
	}
	return implode( "\n", $out );
}
