<?php

/**
 * Services List Table.
 *
 * @package Services For SureCart
 * @author  SurelyWP
 * @version 1.0.0
 */

if ( ! wp_is_mobile() ) {
	?>
	<sc-table>
		<sc-table-cell slot="head" class="service-id"><?php esc_html_e( 'ID', 'surelywp-services' ); ?></sc-table-cell>
		<sc-table-cell slot="head" class="order-id"><?php esc_html_e( 'Order', 'surelywp-services' ); ?></sc-table-cell>
		<sc-table-cell slot="head" class="product-name"> <?php esc_html_e( 'Product', 'surelywp-services' ); ?></sc-table-cell>
		<sc-table-cell slot="head" class="service-date"> <?php esc_html_e( 'Date', 'surelywp-services' ); ?></sc-table-cell>
		<sc-table-cell slot="head" class="service-status"> <?php esc_html_e( 'Status', 'surelywp-services' ); ?></sc-table-cell>
		<sc-table-cell slot="head" class="service-details"> <?php esc_html_e( 'Details', 'surelywp-services' ); ?></sc-table-cell>
		<?php
		if ( $services ) {

			$service_data = $services_obj->surelywp_sv_get_sv_data( $services );

			foreach ( $services as $service ) {

				$service_id          = $service->service_id ?? '';
				$order_id            = $service->order_id ?? '';
				$product_id          = $service->product_id ?? '';
				$service_status      = $service->service_status ?? '';
				$service_status_text = $services_obj->surelywp_sv_get_service_status( $service_status, $product_id, $service_id );
				$order_view_url      = $dashboard_url . '?action=show&model=order&id=' . $order_id;
				$product_view_url    = $service_data['product'][ $service->product_id ]['product_permalink'];

				if ( ! empty( $service_data ) ) {
					?>
					<sc-table-row>
						<sc-table-cell class="sv-id-cell"><?php echo esc_html( $service_id ); ?> </sc-table-cell>
						<sc-table-cell class="sv-order-link-cell">
							<a class="view-order-link" aria-label="<?php echo esc_attr__( 'View Order', 'surelywp-services' ); ?>" href="<?php echo esc_url( $order_view_url ); ?>">
								#<?php echo esc_html( $service_data['order'][ $service->order_id ]['order_number'] ?? $order_id ); ?>
							</a>
						</sc-table-cell>
						<sc-table-cell class="sv-product-link-cell">
							<a class="view-product-link" aria-label="<?php echo esc_attr__( 'View Product', 'surelywp-services' ); ?>" href="<?php echo esc_url( $product_view_url ); ?>">
								<?php echo esc_html( $service_data['product'][ $service->product_id ]['product_name'] ); ?>
							</a>
						</sc-table-cell>
						<sc-table-cell class="sv-date-cell">
							<?php
							$date_format = get_option( 'date_format', 'j F Y' );
							echo esc_html( wp_date( $date_format, (int) $service_data['order'][ $service->order_id ]['order_date'] ) );
							?>
						</sc-table-cell>
						<sc-table-cell class="sv-status-cell">
							<?php $tag_type = ( 'service_complete' === $service_status ) ? 'success' : ( 'service_canceled' === $service_status ? 'danger' : 'warning' ); ?>
							<?php if ( ! wp_is_mobile() ) { ?>
								<sc-tag type="<?php echo esc_attr( $tag_type ); ?>" size="medium"><?php echo esc_html( $service_status_text ); ?></sc-tag>
							<?php } else { ?>
								<span class="sv-tag sv-tag-<?php echo esc_attr( $tag_type ); ?>"><?php echo esc_html( $service_status_text ); ?></span>
							<?php } ?>
						</sc-table-cell>
						<sc-table-cell class="sv-view-btn-cell" style="text-align: left;">
							<?php
							$view_service_url = add_query_arg(
								array(
									'action'     => 'index',
									'model'      => 'services',
									'service_id' => $service_id,
								),
								$dashboard_url
							);
							?>
							<sc-button class="service-view-btn" type="primary" href="<?php echo esc_url( $view_service_url ); ?>">
								<?php echo esc_html_e( 'View', 'surelywp-services' ); ?>
							</sc-button></sc-table-cell>
					</sc-table-row>
					<?php
				}
			}
		} else {
			?>
			<sc-table-row><sc-table-cell>
					<sc-text class="not-found">
						<?php
						$service_plural_name = Surelywp_Services::get_sv_plural_name();
						// translators: %s is the plural name of the services.
						printf( esc_html__( '%s Not Found', 'surelywp-services' ), esc_html( $service_plural_name ) );
						?>
					</sc-text>
				</sc-table-cell></sc-table-row>
		<?php } ?>
	</sc-table>
	<?php
} elseif ( $services ) {
	$service_data = $services_obj->surelywp_sv_get_sv_data( $services );

	?>
	<div class="surelyp-service-list">
		<?php
		foreach ( $services as $service ) {

			$service_id          = $service->service_id ?? '';
			$order_id            = $service->order_id ?? '';
			$product_id          = $service->product_id ?? '';
			$service_status      = $service->service_status ?? '';
			$service_status_text = $services_obj->surelywp_sv_get_service_status( $service_status, $product_id, $service_id );
			$order_view_url      = $dashboard_url . '?action=show&model=order&id=' . $order_id;
			$product_view_url    = $service_data['product'][ $service->product_id ]['product_permalink'];

			if ( ! empty( $service_data ) ) {
				?>
				<div class="surelyp-service-block">
					<div class="surelyp-service-item">
						<div class="surelyp-service-label"><?php esc_html_e( 'ID', 'surelywp-services' ); ?></div>
						<div class="surelyp-service-detail"><?php echo esc_html( $service_id ); ?> </div>
					</div>
					<div class="surelyp-service-item">
						<div class="surelyp-service-label"><?php esc_html_e( 'Order', 'surelywp-services' ); ?></div>
						<div class="surelyp-service-detail"><a class="view-order-link" aria-label="<?php echo esc_attr__( 'View Order', 'surelywp-services' ); ?>" href="<?php echo esc_url( $order_view_url ); ?>">#<?php echo esc_html( $service_data['order'][ $service->order_id ]['order_number'] ?? $order_id ); ?></a>
						</div>
					</div>
					<div class="surelyp-service-item">
						<div class="surelyp-service-label"> <?php esc_html_e( 'Product', 'surelywp-services' ); ?></div>
						<div class="surelyp-service-detail">
							<a class="view-product-link" aria-label="<?php echo esc_attr__( 'View Product', 'surelywp-services' ); ?>" href="<?php echo esc_url( $product_view_url ); ?>">
								<?php echo esc_html( $service_data['product'][ $service->product_id ]['product_name'] ); ?>
							</a>
						</div>
					</div>
					<div class="surelyp-service-item">
						<div class="surelyp-service-label"> <?php esc_html_e( 'Date', 'surelywp-services' ); ?></div>
						<div class="surelyp-service-detail">
							<?php
							$date_format = get_option( 'date_format', 'j F Y' );
							echo esc_html( wp_date( $date_format, (int) $service_data['order'][ $service->order_id ]['order_date'] ) );
							?>
						</div>
					</div>
					<div class="surelyp-service-item">
						<div class="surelyp-service-label"> <?php esc_html_e( 'Status', 'surelywp-services' ); ?></div>
						<div class="surelyp-service-detail">
							<?php $tag_type = ( 'service_complete' === $service_status ) ? 'success' : ( 'service_canceled' === $service_status ? 'danger' : 'warning' ); ?>
							<?php if ( ! wp_is_mobile() ) { ?>
								<sc-tag type="<?php echo esc_attr( $tag_type ); ?>" size="medium"><?php echo esc_html( $service_status_text ); ?></sc-tag>
							<?php } else { ?>
								<span class="sv-tag sv-tag-<?php echo esc_attr( $tag_type ); ?>"><?php echo esc_html( $service_status_text ); ?></span>
							<?php } ?>
						</div>
					</div>
					<div class="surelyp-service-item">
						<div class="surelyp-service-label"> <?php esc_html_e( 'Details', 'surelywp-services' ); ?></div>
						<div class="surelyp-service-detail">
							<?php
							$view_service_url = add_query_arg(
								array(
									'action'     => 'index',
									'model'      => 'services',
									'service_id' => $service_id,
								),
								$dashboard_url
							);
							?>
							<sc-button class="service-view-btn" type="primary" href="<?php echo esc_url( $view_service_url ); ?>">
								<?php echo esc_html_e( 'View', 'surelywp-services' ); ?>
							</sc-button>
						</div>
					</div>
				</div>
				<?php
			}
			?>
	
			<?php
		}
	?> </div> <?php 
} else {
	?>

<div class="surelyp-service-block">
	<div class="surelyp-service-item">
	<div class="not-found">
		<?php
		$service_plural_name = Surelywp_Services::get_sv_plural_name();
		// translators: %s is the plural name of the services.
		printf( esc_html__( '%s Not Found', 'surelywp-services' ), esc_html( $service_plural_name ) );
		?>
	</div>
	</div>
</div>
<?php } ?>