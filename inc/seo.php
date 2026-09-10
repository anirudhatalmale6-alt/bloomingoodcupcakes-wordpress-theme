<?php
/**
 * Search-engine plumbing.
 *
 * The audit's ninth finding was that the old site's structured data described
 * "a website with a logo" -- a bare Organization with no address, no phone, no
 * hours and no geography -- while the ranking depends almost entirely on the
 * map listing. This describes a Bakery in a place, which is how a search engine
 * confirms that the site and the map listing are the same business.
 *
 * @package bloomingood
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * LocalBusiness / Bakery JSON-LD.
 */
function bgc_schema() {
	if ( ! is_front_page() ) {
		return;
	}

	$phone = bgc_opt( 'phone' );
	$addr  = bgc_opt( 'address' );

	// "5a Lynch Green, Hethersett, Norwich NR9 3JU" -> parts. Postcode last,
	// town second from the end; anything before that is the street.
	$bits     = array_map( 'trim', explode( ',', $addr ) );
	$postcode = '';
	if ( preg_match( '/([A-Z]{1,2}\d[A-Z\d]?\s*\d[A-Z]{2})\s*$/i', $addr, $m ) ) {
		$postcode = strtoupper( trim( $m[1] ) );
		$bits[ count( $bits ) - 1 ] = trim( str_ireplace( $m[1], '', end( $bits ) ) );
	}
	$town   = count( $bits ) > 1 ? array_pop( $bits ) : '';
	$street = implode( ', ', $bits );

	$data = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Bakery',
		'@id'         => home_url( '/#business' ),
		'name'        => wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
		'url'         => home_url( '/' ),
		'image'       => BGC_URI . '/assets/img/hero-bouquet-19.jpg',
		'description' => wp_specialchars_decode( get_bloginfo( 'description' ), ENT_QUOTES ),
		'priceRange'  => '££',
		'servesCuisine' => 'Cakes',
	);
	if ( $phone ) {
		$data['telephone'] = $phone;
	}
	if ( $street || $town ) {
		$data['address'] = array_filter(
			array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => $street,
				'addressLocality' => $town,
				'postalCode'      => $postcode,
				'addressCountry'  => 'GB',
			)
		);
	}
	$hours = bgc_opt( 'hours' );
	if ( $hours ) {
		// Written out rather than parsed from prose: a wrong opening-hours
		// statement in structured data is worse than none at all.
		$data['openingHoursSpecification'] = array(
			array(
				'@type'     => 'OpeningHoursSpecification',
				'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ),
				'opens'     => '09:00',
				'closes'    => '17:00',
			),
		);
	}
	$data['areaServed'] = array(
		array( '@type' => 'City', 'name' => 'Norwich' ),
		array( '@type' => 'Place', 'name' => 'Hethersett' ),
	);

	// The price list, as Offers, so thirteen published prices become something
	// a search engine can read rather than thirteen lines of decoration.
	$offers = array();
	foreach ( array( 'prices_bouquet', 'prices_box', 'prices_naked', 'prices_drip' ) as $key ) {
		foreach ( bgc_prices( $key ) as $row ) {
			$amount = preg_replace( '/[^0-9.]/', '', $row[1] );
			if ( '' === $amount ) {
				continue;
			}
			$offers[] = array(
				'@type'         => 'Offer',
				'name'          => $row[0],
				'price'         => $amount,
				'priceCurrency' => 'GBP',
				'availability'  => 'https://schema.org/InStock',
			);
		}
	}
	if ( $offers ) {
		$data['makesOffer'] = $offers;
	}

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
	);
}
add_action( 'wp_head', 'bgc_schema', 20 );

/**
 * Share card.
 *
 * The old site declared a stock photograph of macarons -- a product this
 * bakery does not sell -- as its Open Graph image, so every share on WhatsApp
 * or Facebook previewed somebody else's work. This declares the hero bouquet.
 */
function bgc_og() {
	if ( ! is_front_page() ) {
		return;
	}
	$tags = array(
		'og:type'        => 'website',
		'og:site_name'   => wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
		'og:title'       => wp_get_document_title(),
		'og:description' => wp_specialchars_decode( get_bloginfo( 'description' ), ENT_QUOTES ),
		'og:url'         => home_url( '/' ),
		'og:image'       => BGC_URI . '/assets/img/share-card.jpg',
	);
	foreach ( $tags as $k => $v ) {
		printf( '<meta property="%s" content="%s">' . "\n", esc_attr( $k ), esc_attr( $v ) );
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
}
add_action( 'wp_head', 'bgc_og', 5 );
