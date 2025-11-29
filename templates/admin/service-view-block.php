<?php
/**
 * Inside order services list.
 *
 * @package Services For SureCart
 * @author  SurelyWP
 * @version 1.0.0
 */

global $surelywp_sv_model;

$services     = $surelywp_sv_model->surelywp_sv_get_service_by_order( $order_id );
$services_obj = Surelywp_Services();

$service_plural_name = Surelywp_Services::get_sv_plural_name();
if ( $services ) {
	?>
	<div class="css-1hp6akg order-services-list-block">
		<div data-wp-c16t="true" data-wp-component="Card" class="components-surface components-card css-1st819u css-443a5t e19lxcc00 css-talpf1">
			<div class="css-10klw3m e19lxcc00">
				<div data-wp-c16t="true" data-wp-component="CardHeader" class="components-flex components-card__header components-card-header css-15hyllm css-1w3sexp e19lxcc00 css-1v6oc13"><sc-text tag="h2" style="--font-size: 15px; --font-weight: var(--sc-font-weight-bold); width: 100%;" class="hydrated"><?php echo esc_html( $service_plural_name ); ?></sc-text></div>
				<div data-wp-c16t="true" data-wp-component="CardBody" class="components-card__body components-card-body css-1xfafpv css-1dzvnua e19lxcc00 css-1pa1sky">
					<sc-table class="hydrated" style="--shadow: none; --border-radius: 0; border-left: 0px; border-right: 0px;">
					<sc-table-cell slot="head" class="hydrated" style="width: 200px;" ><?php esc_html_e( 'Status', 'surelywp-services' ); ?></sc-table-cell>
					<sc-table-cell slot="head" class="hydrated" ><?php esc_html_e( 'Product', 'surelywp-services' ); ?></sc-table-cell>
					<sc-table-cell slot="head" class="hydrated"><?php esc_html_e( 'AMOUNT', 'surelywp-services' ); ?></sc-table-cell>
					<sc-table-cell slot="head" class="hydrated"><?php esc_html_e( 'Created', 'surelywp-services' ); ?></sc-table-cell>
					<sc-table-cell slot="head" class="hydrated" ></sc-table-cell>
					<?php
					foreach ( $services as $service ) {
						$service_id          = $service->service_id ?? '';
						$product_id          = $service->product_id ?? '';
						$service_status      = $service->service_status ?? '';
						$service_status_text = $services_obj->surelywp_sv_get_service_status( $service_status, $product_id, $service_id );
						$service_data        = $services_obj->surelywp_sv_get_service_data( $order_id, $product_id );
						$price               = $services_obj->surelywp_sv_get_product_paid_price( $order_id, $product_id );
						$paid_amount         = $price['amount'] ?? '';
						?>
						<sc-table-row class="hydrated">
							<sc-table-cell>
							<?php $tag_type = 'service_complete' === $service_status ? 'success' : ( 'service_canceled' === $service_status ? 'danger' : 'warning' ); ?>
								<sc-tag type="<?php echo esc_attr( $tag_type ); ?>" size="medium"><?php echo esc_html( $service_status_text ); ?></sc-tag>
							</sc-table-cell>
							<?php $product_url = admin_url( 'admin.php' ) . '?page=sc-products&action=edit&id=' . $product_id; ?>
							<sc-table-cell class="hydrated">
								<a href="<?php echo esc_url( $product_url ); ?>"><?php echo esc_html( $service_data['product_name'] ); ?></a>
							</sc-table-cell>
							<sc-table-cell>
								<sc-format-number type="currency" value="<?php echo (float) $paid_amount; ?>"></sc-format-number>
							</sc-table-cell>
							<sc-table-cell>
								<?php
								$date_format = get_option( 'date_format', 'j M Y' );
								echo esc_html( wp_date( $date_format, (int)$service_data['order_date'] ) ); //phpcs:ignore ?>
							</sc-table-cell>
							<?php $service_url = admin_url( 'admin.php' ) . '?page=sc-services&action=view&service_id=' . $service_id; ?>
							<sc-table-cell class="hydrated"><sc-button href="<?php echo esc_url( $service_url ); ?>" size="small" type="default" class="hydrated"><?php esc_html_e( 'View', 'surelywp-services' ); ?></sc-button>
							</sc-table-cell>
						</sc-table-row>
					<?php } ?>
					</sc-table>
				</div>
			</div>
			<div data-wp-c16t="true" data-wp-component="Elevation" class="components-elevation css-7g516l e19lxcc00" aria-hidden="true"></div>
			<div data-wp-c16t="true" data-wp-component="Elevation" class="components-elevation css-7g516l e19lxcc00" aria-hidden="true"></div>
		</div>
	</div>
<?php } ?>