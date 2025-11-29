<?php
/**
 * Surecart admin product page service block
 *
 * @author  Surelywp
 * @package Services For SureCart
 * @since 1.0.0
 */

$surelywp_sv_settings_options = get_option( 'surelywp_sv_settings_options' );
$surelywp_sv_settings_options = $surelywp_sv_settings_options['surelywp_sv_settings_options'] ?? '';
$product                      = SureCart\Models\Product::find( $product_id );
$is_service_enable            = 0;
$sv_setting_id                = '';
if ( is_object( $product ) && ! is_wp_error( $product ) ) {

	$is_service_enable = $product->metadata->is_service_enable ?? 0;
	$sv_setting_id     = $product->metadata->service_setting_id ?? 0;
}

$service_singular_name = Surelywp_Services::get_sv_singular_name();
if ( ! empty( $surelywp_sv_settings_options ) ) {
	?>
	<div class="product-service-block" id="product-service-block">
		<div data-wp-c16t="true" data-wp-component="Card" class="components-surface components-card css-1st819u css-443a5t e19lxcc00 css-talpf1">
			<div class="css-10klw3m e19lxcc00">
				<div data-wp-c16t="true" data-wp-component="CardHeader" class="components-flex components-card__header components-card-header css-15hyllm css-1w3sexp e19lxcc00 css-1v6oc13"><sc-text tag="h2" class="hydrated" style="--font-size: 15px; --font-weight: var(--sc-font-weight-bold); width: 100%;"><?php echo esc_html( $service_singular_name ); ?></sc-text></div>
				<div data-wp-c16t="true" data-wp-component="CardBody" class="components-card__body components-card-body css-1xfafpv css-1dzvnua e19lxcc00 css-1pa1sky">
				<?php wp_nonce_field( 'surelywp_sv_manage_product_service_action', 'surelywp_sv_manage_product_service_nonce' ); ?>
					<sc-switch checked="<?php echo $is_service_enable ? 'true' : 'false'; ?>" value="on" class="hydrated surelywp-product-service-enable-switch">
						<?php
							// translators: %s is the singular name of the service.
							printf( esc_html__( 'Enable %s', 'surelywp-services' ), esc_html( $service_singular_name ) );
						?>
						<span slot="description">
						<?php
							// translators: %s is the singular name of the service.
							printf( esc_html__( 'Enable %s features for this product.', 'surelywp-services' ), esc_html( $service_singular_name ) );
						?>
						</span></sc-switch>
					<input type="hidden" id="sv-product-id" value="<?php echo esc_attr( $product_id ); ?>">
					<input type="hidden" id="current-service-setting-id" value="<?php echo esc_attr( $sv_setting_id ); ?>">
					<div class="surelywp-services-selection-wrap <?php echo $is_service_enable ? '' : 'hidden'; ?>">
						<sc-select id="surelywp-services-selection" class="surelywp-services-selection customer-role" label="<?php echo esc_html__( 'Associated Service', 'surelywp-services' ); ?>" help="<?php echo esc_html__( 'Associate this product with a specific service. You can add new services or manage existing ones in the SurelyWP addon settings. NOTE: Services will not appear in the dropdown if they are already associated with another specific product, or if a different service is set to apply to all products or the collection that this product belongs to.', 'surelywp-services' ); ?>" placeholder="<?php echo esc_html__( 'Select..', 'surelywp-services' ); ?>" value="<?php echo esc_html( $sv_setting_id ); ?>" search></sc-select>
						<script>
							document.querySelector('sc-select').choices = [
								<?php
								foreach ( $surelywp_sv_settings_options as $service_setting_id => $options ) {
									$service_title         = $options['service_title'] ?? 'NO NAME';
									$services_product_type = $options['services_product_type'] ?? '';
									if ( 'specific' === $services_product_type ) {
										echo "{label: '" . esc_html( $service_title ) . "', value: '" . esc_html( $service_setting_id ) . "'},";
									}
								}
								?>
							];
						</script>
					</div>
				</div>
			</div>
			<div data-wp-c16t="true" data-wp-component="Elevation" class="components-elevation css-7g516l e19lxcc00" aria-hidden="true"></div>
			<div data-wp-c16t="true" data-wp-component="Elevation" class="components-elevation css-7g516l e19lxcc00" aria-hidden="true"></div>
		</div>
	</div>
<?php } ?>