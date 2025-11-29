<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
$product_id  = $attributes['product_id'] ?? '';
$is_valid_id = Surelywp_Services::surelywp_sv_is_valid_product_id( $product_id );
if ( $is_valid_id ) {
	$is_user_service_provider = Surelywp_Services::surelywp_sv_is_user_service_provider();
	$service_setting_id       = Surelywp_Services::surelywp_sv_get_product_service_setting_id( $product_id );
	if ( ! $is_user_service_provider && $service_setting_id ) {

		$ask_for_requirements = Surelywp_Services::get_sv_option( $service_setting_id, 'ask_for_requirements' );

		if ( $ask_for_requirements ) {

			// enqueue requirement form script.
			Surelywp_Services::surelywp_sv_enqueue_req_form_script();
			$req_form_html = Surelywp_Services::surelywp_sv_get_req_form_html( $service_setting_id, $product_id );
			echo $req_form_html;
		}
	}
}
