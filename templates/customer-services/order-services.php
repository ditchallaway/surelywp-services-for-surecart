<?php
/**
 * Individual order services list.
 *
 * @author  Surelywp
 * @package Services For SureCart
 * @since 1.0.0
 */

global $surelywp_sv_model;

$services          = $surelywp_sv_model->surelywp_sv_get_service_by_customer_order( $order_id );
$services_obj      = Surelywp_Services();
$dashboard_page_id = get_option( 'surecart_dashboard_page_id' );
$dashboard_url     = get_permalink( $dashboard_page_id );
if ( $services ) {
	?>
	<div class="customer-order-services hidden">
		<sc-dashboard-module heading="<?php esc_html_e( 'Services', 'surelywp-services' ); ?>">
			<sc-card no-padding>
				<sc-stacked-list>
					<?php
					foreach ( $services as $service ) {

						$service_id          = $service->service_id ?? '';
						$product_id          = $service->product_id ?? '';
						$service_status      = $service->service_status ?? '';
						$service_data        = $services_obj->surelywp_sv_get_service_data( $order_id, $product_id );
						$service_status_text = $services_obj->surelywp_sv_get_service_status( $service_status, $product_id, $service_id );
						$service_url         = $dashboard_url . '?action=index&model=services&service_id=' . $service_id;
						$price               = $services_obj->surelywp_sv_get_product_paid_price( $order_id, $product_id );
						$paid_amount         = $price['amount'] ?? '';
						?>
						<sc-stacked-list-row href="<?php echo esc_url( $service_url ); ?>" mobile-size="500" class="hydrated" style="--columns: 4;">
							<div>
								<sc-spacing class="hydrated" style="--spacing: var(--sc-spacing-xx--small);">
									<div><strong><?php echo esc_html( $service_data['product_name'] ); ?> </strong></div>
								</sc-spacing>
							</div>
							<div>
								<sc-tag type="warning" size="medium"><?php echo esc_html( $service_status_text ); ?>
								</sc-tag>
							</div>
							<div>
								<sc-format-number type="currency" value="<?php echo (float) $paid_amount; ?>"></sc-format-number>
							</div>
							<div class="arrow-icon">
								<sc-icon slot="suffix" name="chevron-right" class="hydrated"></sc-icon>
							</div>
						</sc-stacked-list-row>
					<?php } ?>
				</sc-stacked-list>
			</sc-card>
		</sc-dashboard-module>
	</div>
<?php } ?>