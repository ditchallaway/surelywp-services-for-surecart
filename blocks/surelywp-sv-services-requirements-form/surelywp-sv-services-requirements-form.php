<?php
/**
 * Services alert Root file.
 *
 * @package Surelywp Service
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function create_block_surelywp_sv_services_req_form() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', 'create_block_surelywp_sv_services_req_form' );

if ( ! function_exists( 'surelywp_sv_add_block_category' ) ) {

	/**
	 * Add New Catagery for Block
	 *
	 * @param array $categories The array of the categories.
	 */
	function surelywp_sv_add_block_category( $categories ) {

		$surelywp_category = array(
			array(
				'slug'  => 'surelywp',
				'title' => esc_html__( 'SurelyWP', 'surelywp-services' ),
			),
		);

		// Find the index of 'surecart' category.
		$index = array_search( 'surecart', array_column( $categories, 'slug' ), true );

		// Insert surelywp category after 'surecart'.
		if ( false !== $index ) {
			array_splice( $categories, $index + 1, 0, $surelywp_category );
		}

		return $categories;
	}
}
add_filter( 'block_categories_all', 'surelywp_sv_add_block_category', 10, 1 );
