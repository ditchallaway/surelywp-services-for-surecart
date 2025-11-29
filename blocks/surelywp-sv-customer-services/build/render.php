<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

$is_service_list_shortcode = true;
$heading_title             = $attributes['title'] ?? '';
$view_page_id              = $attributes['page_id'] ?? '';

Surelywp_Services::surelywp_sv_enqueue_front_script();
?>
<div class="surelyp-sv-customer-services-block">
	<?php include SURELYWP_SERVICES_TEMPLATE_PATH . '/customer-services/services.php'; ?>
</div>