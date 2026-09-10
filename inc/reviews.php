<?php
/**
 * Google reviews, fetched and cached.
 *
 * WHICH API, AND WHY IT MATTERS
 * There are two different Google APIs here and they are not interchangeable.
 * The Business Profile API v4 -- the one that can REPLY to reviews -- is gated
 * behind an approval form the business owner has to submit themselves, with a
 * verified profile and a stated use case, and new Cloud projects start on zero
 * quota. That is not what this is. DISPLAYING reviews on a website uses the
 * Places API, which needs only a Google Cloud key with billing enabled. No
 * approval, no waiting.
 *
 * WHAT IT WILL AND WILL NOT DO
 * Places returns at most FIVE reviews and Google chooses which. There is no way
 * to page through the rest, and no supported way to get all of them. Any plugin
 * claiming otherwise is either scraping (against Google's terms, and it breaks)
 * or asking the client to paste them in by hand.
 *
 * THE FLOOR
 * As of the audit this listing carried ONE review, against a median of 60 across
 * the eight rivals holding the local pack. A live feed showing a single review
 * is worse than the two curated quotes it would replace -- it advertises the
 * weakness. So the section only switches over once there are at least
 * `reviews_min` of them (three by default). Configure it now, and it turns
 * itself on when the reviews arrive.
 *
 * @package bloomingood
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const BGC_REVIEWS_TRANSIENT = 'bgc_google_reviews';
const BGC_REVIEWS_BACKUP    = 'bgc_google_reviews_last_good';

/**
 * Fetch the listing's reviews, cached.
 *
 * @param bool $force Ignore the cache.
 * @return array{rating:float,count:int,reviews:array,url:string}|null
 */
function bgc_google_reviews( $force = false ) {
	$place = trim( (string) bgc_opt( 'place_id' ) );
	$key   = trim( (string) bgc_opt( 'places_key' ) );
	if ( '' === $place || '' === $key ) {
		return null;
	}

	if ( ! $force ) {
		$cached = get_transient( BGC_REVIEWS_TRANSIENT );
		if ( is_array( $cached ) ) {
			return $cached;
		}
	}

	$response = wp_remote_get(
		'https://places.googleapis.com/v1/places/' . rawurlencode( $place ),
		array(
			'timeout' => 12,
			'headers' => array(
				'X-Goog-Api-Key'   => $key,
				// Ask only for what is rendered. The field mask is what Google
				// bills on, so a lazy "*" here costs real money per request.
				'X-Goog-FieldMask' => 'rating,userRatingCount,googleMapsUri,reviews',
			),
		)
	);

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		/*
		 * Do not empty the section because Google had a bad minute. Serve the
		 * last good answer and retry sooner than the normal cache window.
		 */
		$last = get_option( BGC_REVIEWS_BACKUP );
		set_transient( BGC_REVIEWS_TRANSIENT, is_array( $last ) ? $last : array(), 15 * MINUTE_IN_SECONDS );
		return is_array( $last ) ? $last : null;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! is_array( $body ) ) {
		return null;
	}

	$out = array(
		'rating'  => isset( $body['rating'] ) ? (float) $body['rating'] : 0.0,
		'count'   => isset( $body['userRatingCount'] ) ? (int) $body['userRatingCount'] : 0,
		'url'     => isset( $body['googleMapsUri'] ) ? (string) $body['googleMapsUri'] : '',
		'reviews' => array(),
	);

	foreach ( (array) ( isset( $body['reviews'] ) ? $body['reviews'] : array() ) as $r ) {
		$text = '';
		if ( isset( $r['originalText']['text'] ) ) {
			$text = $r['originalText']['text'];
		} elseif ( isset( $r['text']['text'] ) ) {
			$text = $r['text']['text'];
		}
		$text = trim( wp_strip_all_tags( $text ) );
		if ( '' === $text ) {
			continue;   // a bare star rating has nothing to show
		}
		$out['reviews'][] = array(
			'text'   => $text,
			'author' => isset( $r['authorAttribution']['displayName'] ) ? $r['authorAttribution']['displayName'] : '',
			'link'   => isset( $r['authorAttribution']['uri'] ) ? $r['authorAttribution']['uri'] : '',
			'when'   => isset( $r['relativePublishTimeDescription'] ) ? $r['relativePublishTimeDescription'] : '',
			'stars'  => isset( $r['rating'] ) ? (int) $r['rating'] : 0,
		);
	}

	/*
	 * Twelve hours. Google's terms allow caching Place IDs indefinitely but not
	 * the content, and this is comfortably inside any reading of that. It also
	 * turns an unbounded per-visit cost into two requests a day.
	 */
	set_transient( BGC_REVIEWS_TRANSIENT, $out, 12 * HOUR_IN_SECONDS );
	update_option( BGC_REVIEWS_BACKUP, $out, false );
	return $out;
}

/**
 * Should the live feed replace the curated quotes?
 *
 * @return bool
 */
function bgc_reviews_ready() {
	$data = bgc_google_reviews();
	if ( ! $data || empty( $data['reviews'] ) ) {
		return false;
	}
	$min = max( 1, (int) bgc_opt( 'reviews_min' ) );
	// Judge on what can actually be RENDERED, not on the listing's total. A
	// listing with 40 ratings and 2 written reviews still shows 2.
	return count( $data['reviews'] ) >= $min;
}

/**
 * Refresh in the background rather than making a visitor wait for Google.
 */
function bgc_reviews_cron() {
	if ( ! wp_next_scheduled( 'bgc_refresh_reviews' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'twicedaily', 'bgc_refresh_reviews' );
	}
}
add_action( 'init', 'bgc_reviews_cron' );
add_action( 'bgc_refresh_reviews', function () { bgc_google_reviews( true ); } );

/**
 * Fold the aggregate rating into the Bakery schema when it is genuinely there.
 *
 * @param array $data Schema.
 * @return array
 */
function bgc_reviews_schema( $data ) {
	$live = bgc_google_reviews();
	if ( $live && $live['count'] > 0 && $live['rating'] > 0 ) {
		$data['aggregateRating'] = array(
			'@type'       => 'AggregateRating',
			'ratingValue' => (string) $live['rating'],
			'reviewCount' => (string) $live['count'],
		);
	}
	return $data;
}
add_filter( 'bgc_schema_data', 'bgc_reviews_schema' );
