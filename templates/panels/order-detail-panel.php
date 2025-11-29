<?php
/**
 * Service Order Detail panel.
 *
 * @package Services For SureCart
 * @author  SurelyWP
 * @version 1.0.0
 */

$current_user_id                = get_current_user_id();
$access_users_ids               = Surelywp_Services::get_sv_access_users_ids();
$order_again_button_on_service	= Surelywp_Services::get_sv_option( $service_setting_id, 'order_again_button_on_service');

?>
<div class="service-order-detail-panel">
	<div class="tab-card">
		<sc-stacked-list>
			<sc-stacked-list-row>
				<sc-columns>
					<sc-column>
						<p class="order-heading"><?php echo esc_html__( 'Order Details', 'surelywp-services' ); ?></p>
					</sc-column>
				</sc-columns>
			</sc-stacked-list-row>
		</sc-stacked-list>
	</div>
	<div class="order-track-list tab-card">
		<div class="order-track-list-wrap">
			<?php if ( ! empty( $access_users_ids ) && in_array( $current_user_id, (array) $access_users_ids ) && $customer  ) { ?>
				<sc-columns>
					<sc-column><p class="label"><?php esc_html_e( 'Customer Name:', 'surelywp-services' ); ?></p></sc-column>
					<?php
					$user_info    = get_user_by( 'id', $receiver_id );
					$display_name = $user_info->display_name ?? esc_html__( 'User not found', 'surelywp-services' );
					?>
					<sc-column>
						<p class="description">
							<a class="view-order-link" aria-label="<?php echo esc_attr__( 'Edit Customer', 'surelywp-services' ); ?>" href="<?php echo esc_url( \SureCart::getUrl()->edit( 'customers', $customer[0]->id ?? '#' ) ); ?>">
								<?php echo wp_kses_post( $customer[0]->name ?? $customer[0]->email ?? '' ); ?>
							</a>
						</p>
					</sc-column>
				</sc-columns>
			<?php } ?>
			<sc-columns>
				<sc-column><p class="label"><?php esc_html_e( 'Order Date:', 'surelywp-services' ); ?></p></sc-column>
				<?php $order_formated = isset( $order_date ) && ! empty( $order_date ) ? wp_date( get_option( 'date_format' ), $order_date ) : ''; ?>
				<sc-column><p class="description"><?php echo esc_html( $order_formated ); ?></p></sc-column>
			</sc-columns>
			<sc-columns>
				<sc-column><p class="label"><?php esc_html_e( 'Order Number:', 'surelywp-services' ); ?></p></sc-column>
				<sc-column>
					<p class="description">
						<a class="view-order-link" aria-label="<?php echo esc_attr__( 'View Order', 'surelywp-services' ); ?>" href="<?php echo esc_url( $order_view_url ); ?>">
							#<?php echo esc_html( $service_data['order_number'] ?? $order_id ); ?>
						</a>
					</p>
				</sc-column>
			</sc-columns>
			<sc-columns>
				<sc-column><p class="label"><?php esc_html_e( 'Amount:', 'surelywp-services' ); ?></p></sc-column>
				<sc-column><p class="description"><sc-format-number type="currency" currency="<?php echo esc_attr( $currency_type ); ?>" value="<?php echo (float) $paid_amount; ?>"></sc-format-number></p></sc-column>
			</sc-columns>
		</div>
		<?php
		if ( 'service_complete' === $service_status && ! empty( $access_users_ids ) && ! in_array( $current_user_id, (array) $access_users_ids ) && $order_again_button_on_service ) {
			?>
			<div class="buy-button">
				<?php echo do_shortcode( '[sc_buy_button]' . esc_html__( 'Order Again', 'surelywp-services' ) . '[sc_line_item price_id=' . $price_id . ' variant_id=' . $variant_id . ' quantity=1][/sc_buy_button]' ); ?>
			</div>
		<?php } ?>
	</div>
</div>