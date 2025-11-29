<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

global $surelywp_sv_model;
$in_progress_services_count = $surelywp_sv_model->surelywp_sv_get_notification_count() ?? '0';
$dashboard_page_id          = get_option( 'surecart_dashboard_page_id' ) ?? '';
if ( ( is_page( $dashboard_page_id ) && ! is_user_logged_in() ) || empty( $in_progress_services_count ) ) {
	return;
}

$alert_title = $attributes['title'] ?? '';
$alert_desc  = $attributes['description'] ?? '';
$page_id     = $attributes['page_id'] ?? '';

$alert_desc = str_replace( '{count}', $in_progress_services_count, $alert_desc );

// Services Alert.
require_once SURELYWP_SERVICES_TEMPLATE_PATH . '/customer-services/services-alert.php';
