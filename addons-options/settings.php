<?php
/**
 * Settings
 *
 * @author  Surelywp
 * @package Services For SureCart
 * @version 1.0.0
 */

global $surelywp_model;

?>
<?php if ( ! isset( $_GET['action'] ) ) { ?>
	<table class="form-table surelywp-ric-settings-box services-templates-table">
		<tbody>
			<tr class="surelywp-field-label">
				<td>
					<?php
					$service_setting_id = 0;
					if ( ! empty( $addons_option ) ) {

						foreach ( $addons_option as $key => $service_setting ) {
							?>
							<div class="form-control services-templates">
								<div class="image key">
									<img src="<?php echo esc_url( SURELYWP_SERVICES_URL . '/assets/images/services.svg' ); ?>" alt="<?php esc_attr_e( 'services', 'surelywp-services' ); ?>">
								</div>
								<div class="surelywp-sv-lable-bagde">
									<div class="input-label">
										<?php echo esc_html( $addons_option[ $key ]['service_title'] ?? '' ); ?>
									</div>
									<?php
									if ( ! empty( $addons_option[ $key ]['status'] ) ) {
										$badge = esc_html( $addons_option[ $key ]['status'] ? __( 'Enabled', 'surelywp-services' ) : __( 'Disabled', 'surelywp-services' ) );
									} else {
										$badge = esc_html( __( 'Disabled', 'surelywp-services' ) );
									} ?>
									<span class="surelywp-sv-badge <?php echo esc_html( $badge ); ?>"><?php echo esc_html( $badge ); ?></span>
								</div>
								<div class="image edit-icon">
									<?php
									$servcie_edit_url = add_query_arg(
										array(
											'page'   => 'surelywp_services_panel',
											'tab'    => 'surelywp_sv_settings',
											'action' => 'edit_service',
											'service_setting_id' => $key,
										),
										admin_url( 'admin.php' )
									);
									?>
									<a href="<?php echo esc_url( $servcie_edit_url ); ?>">
										<img src="<?php echo esc_url( SURELYWP_SERVICES_URL . '/assets/images/list_edit.svg' ); ?>" alt="<?php esc_attr_e( 'list_edit', 'surelywp-services' ); ?>">
									</a>
								</div>
								<div class="image remove-icon">
									<?php
									$service_remove_url = add_query_arg(
										array(
											'page'   => 'surelywp_services_panel',
											'tab'    => 'surelywp_sv_settings',
											'action' => 'remove_service',
											'service_setting_id' => $key,
										),
										admin_url( 'admin.php' )
									);
									?>
									<a id="sv-remove-associate-service" href="<?php echo esc_url( $service_remove_url ); ?>">
										<img src="<?php echo esc_url( SURELYWP_SERVICES_URL . '/assets/images/remove-icon.svg' ); ?>" alt="<?php esc_attr_e( 'list_edit', 'surelywp-services' ); ?>">
									</a>
								</div>
							</div>
							<?php
						}
					}
					?>
				</td>
			</tr>
			<tr class="surelywp-field-label add-new-services">
				<td>
					<div class="addons-btn-wrap">
						<?php
						$service_setting_id = Surelywp_Services::generate_random_id();
						$service_add_url    = add_query_arg(
							array(
								'page'               => 'surelywp_services_panel',
								'tab'                => 'surelywp_sv_settings',
								'action'             => 'add_new_service',
								'service_setting_id' => $service_setting_id,
							),
							admin_url( 'admin.php' )
						);
						?>
						<a href="<?php echo esc_url( $service_add_url ); ?>" class="button-primary">
							<img src="<?php echo esc_url( SURELYWP_SERVICES_URL . '/assets/images/Add_new.svg' ); ?>" alt="<?php esc_attr_e( 'add_new', 'surelywp-services' ); ?>"><?php esc_html_e( 'Add New Service', 'surelywp-services' ); ?>
						</a>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
} elseif ( isset( $_GET['action'] ) && ( 'add_new_service' === $_GET['action'] || 'edit_service' === $_GET['action'] ) ) {

	$service_setting_id = 0;
	if ( isset( $_GET['service_setting_id'] ) && ! empty( $_GET['service_setting_id'] ) ) {
		$service_setting_id = sanitize_text_field( wp_unslash( $_GET['service_setting_id'] ) );
		if ( ! empty( $addons_option ) ) {
			$addon_option = $addons_option[ $service_setting_id ] ?? array();
		}
		$addon_option_key = $option_key . '_options[surelywp_sv_settings_options][' . $service_setting_id . ']';
	}
	?>
	<table id="services-settings-tab" class="form-table surelywp-ric-settings-box services-templates-table">
		<tbody>
			<tr class="surelywp-field-label">
				<td>
					<div class="form-control">
						<div class="services-settings-tabs settings-tabs">
							<div class="surelywp-tab">
								<a href="javascript:void(0)" id="sv-general-tab" class="surelywp-btn"><?php esc_html_e( 'General', 'surelywp-services' ); ?></a>
							</div>
							<div class="surelywp-tab">
								<a href="javascript:void(0)" id="sv-products-tab" class="surelywp-btn"><?php esc_html_e( 'Associated Products', 'surelywp-services' ); ?></a>
							</div>
							<div class="surelywp-tab">
								<a href="javascript:void(0)" id="sv-rules-tab" class="surelywp-btn"><?php esc_html_e( 'Rules', 'surelywp-services' ); ?></a>
							</div>
							<div class="surelywp-tab">
								<a href="javascript:void(0)" id="sv-contract-tab" class="surelywp-btn"><?php esc_html_e( 'Contract', 'surelywp-services' ); ?></a>
							</div>
							<div class="surelywp-tab">
								<a href="javascript:void(0)" id="sv-requirements-tab" class="surelywp-btn"><?php esc_html_e( 'Requirements', 'surelywp-services' ); ?></a>
							</div>
							<div class="surelywp-tab">
								<a href="javascript:void(0)" id="sv-milestones-tab" class="surelywp-btn"><?php esc_html_e( 'Milestones', 'surelywp-services' ); ?></a>
							</div>
						</div>
					</div>
				</td>
			</tr>
			<tr class="surelywp-field-label sv-general-settings tab-settings">
				<td>
					<h4 class="heading-text first-heading"><?php echo esc_html_e( 'General', 'surelywp-services' ); ?></h4>
					<div class="form-control">
						<div class="input-label"><?php esc_html_e( 'Service Title', 'surelywp-services' ); ?></div>
						<label><?php esc_html_e( 'Enter a title for the service.', 'surelywp-services' ); ?></label>
						<?php
						$service_title = ( isset( $addon_option['service_title'] ) ) ? $addon_option['service_title'] : '';
						?>
						<input type="text" class="widefat" name="<?php echo $addon_option_key . '[service_title]'; ?>" value="<?php echo $surelywp_model->surelywp_escape_attr( $service_title ); //phpcs:ignore?>">
						<input type="hidden" id="surelywp-sv-service-setting-id" class="widefat" name="<?php echo $addon_option_key . '[service_setting_id]'; ?>" value="<?php echo $surelywp_model->surelywp_escape_attr( $service_setting_id ); //phpcs:ignore?>">
						<input type="hidden" id="current-setting-id" class="widefat" name="current_setting_id" value="<?php echo $surelywp_model->surelywp_escape_attr( $service_setting_id ); //phpcs:ignore?>">
					</div>
					<div class="form-control">
						<div class="input-label"><?php echo esc_html_e( 'Enable Service', 'surelywp-services' ); ?></div>
						<label><?php esc_html_e( 'Choose enable or disable this service.', 'surelywp-services' ); ?></label>
						<?php
						if ( ! isset( $addon_option['sv_update_options'] ) ) {
							$addons_option          = array();
							$addon_option['status'] = 0;
						}
						?>
						<label class="toggleSwitch xlarge" onclick="">
							<input type="checkbox" id="surelywp-sv-settings-status" name="<?php echo $addon_option_key . '[status]'; //phpcs:ignore?>" value="<?php echo $surelywp_model->surelywp_escape_attr( '1' ); ?>" <?php echo ( isset( $addon_option['status'] ) ) ? checked( $addon_option['status'], 1, true ) : ''; ?> size="10" />
							<input type="hidden" name="<?php echo $addon_option_key . '[sv_update_options]'; //phpcs:ignore?>" value="<?php echo $surelywp_model->surelywp_escape_attr( 'updated' ); ?>" />
							<?php $is_sv_options_updated = isset( $addon_option['sv_update_options'] ) ? '1' : '0'; ?>
							<?php $surelywp_sv_option_nonce = wp_create_nonce( 'surelywp_sv_option_nonce' ); ?>
							<input type="hidden" name="surelywp_sv_option_nonce" value="<?php echo $surelywp_model->surelywp_escape_attr( $surelywp_sv_option_nonce ); //phpcs:ignore?>" />
							<span>
								<span><?php echo esc_html_e( 'No', 'surelywp-services' ); ?></span>
								<span><?php echo esc_html_e( 'Yes', 'surelywp-services' ); ?></span>
							</span>
							<a></a>
						</label>
					</div>
				</td>
			</tr>
			<?php
			if ( isset( $addon_option['status'] ) && '1' === $addon_option['status'] ) {
				$hidden_class = '';
			} else {
				$hidden_class = 'hidden';
			}
			?>
			<tr class="surelywp-field-label sv-general-settings tab-settings " id="surelywp-sv-order-again-settings">
				<td>
					<hr>
					<h4 class="heading-text first-heading"><?php echo esc_html_e( 'Order Again Button', 'surelywp-services' ); ?></h4>
					<div class="form-control">
						<div class="input-label"><?php esc_html_e( 'Show Order Again Button On Service', 'surelywp-services' ); ?></div>
						<label><?php esc_html_e( 'Enable this setting to add an "Order Again" button in the induvial service Order Details section in the sidebar after the service is completed. When clicked, it will initiate the "Buy Now" feature, placing the same product configuration into the checkout for quick reordering.', 'surelywp-services' ); ?></label>
						<label class="toggleSwitch xlarge" onclick="">
							<input type="checkbox"  name="<?php echo $addon_option_key . '[order_again_button_on_service]'; //phpcs:ignore?>" value="<?php echo $surelywp_model->surelywp_escape_attr( '1' ); ?>" <?php echo ( isset( $addon_option['order_again_button_on_service'] ) ) ? checked( $addon_option['order_again_button_on_service'], 1, true ) : ''; ?> size="10" />
							<span>
								<span><?php echo esc_html_e( 'No', 'surelywp-services' ); ?></span>
								<span><?php echo esc_html_e( 'Yes', 'surelywp-services' ); ?></span>
							</span>
							<a></a>
						</label>
					</div>
				</td>
			</tr>
			<tr class="surelywp-field-label sv-products-settings tab-settings " id="surelywp-sv-product-selection-settings">
				<td>
				<h4 class="heading-text first-heading"><?php echo esc_html_e( 'Associated Products', 'surelywp-services' ); ?></h4>
				<div id="surelywp-sv-product-selection-settings-div">
					<div class="form-control">
						<div class="input-label"><?php esc_html_e( 'Choose Products For Services', 'surelywp-services' ); ?></div>
						<label><?php esc_html_e( 'Choose which SureCart products you want to associate with this service.', 'surelywp-services' ); ?></label>
						<select id="services-product-type" name="<?php echo $addon_option_key . '[services_product_type]';  //phpcs:ignore  ?>">
							<option <?php echo (isset($addon_option['services_product_type']) && 'all' === $addon_option['services_product_type']) ? 'selected' : ''; ?> value="<?php echo $surelywp_model->surelywp_escape_attr('all'); ?>"><?php esc_html_e('All Products', 'surelywp-services'); //phpcs:ignore ?>
							</option>
							<option <?php echo (isset($addon_option['services_product_type']) && 'specific' === $addon_option['services_product_type']) ? 'selected' : ''; ?> value="<?php echo $surelywp_model->surelywp_escape_attr('specific'); ?>"><?php esc_html_e('Specific Products', 'surelywp-services'); //phpcs:ignore ?>
							</option>
							<option <?php echo (isset($addon_option['services_product_type']) && 'specific_collection' === $addon_option['services_product_type']) ? 'selected' : ''; ?> value="<?php echo $surelywp_model->surelywp_escape_attr('specific_collection'); ?>"><?php esc_html_e('Specific Product Collections', 'surelywp-services'); //phpcs:ignore ?>
							</option>
						</select>
					</div>
					<div class="form-control multi-selection <?php echo (isset($addon_option['services_product_type']) && 'specific' === $addon_option['services_product_type']) ? $hidden_class : 'hidden'; //phpcs:ignore ?>" id="specific-product-selection-div">
						<div class="input-label"><?php esc_html_e( 'Select Specific Products', 'surelywp-services' ); ?></div>
						<label><?php esc_html_e( 'Select the SureCart products you want to associate with this service.', 'surelywp-services' ); ?></label>
						<select multiple="multiple" id="surelywp-sv-specific-products" class="customer-role" name="<?php echo $addon_option_key . '[service_products][]'; //phpcs:ignore ?>">
							<?php
							$products = Surelywp_Services::surelywp_sv_get_all_products();
							if ( ! empty( $products ) && ! is_wp_error( $products ) ) {
								foreach ( $products as $product ) {
									$is_selected_on_other_service = Surelywp_Services::surelywp_sv_is_product_have_service( $service_setting_id, $product->id );
									if ( $is_selected_on_other_service ) {
										continue;
									}
									?>
									<option <?php echo isset($addon_option['service_products']) && in_array($product->id, (array)$addon_option['service_products']) ? 'selected' : ''; ?> value="<?php echo $surelywp_model->surelywp_escape_attr($product->id); ?>"><?php echo $surelywp_model->surelywp_escape_attr($product->name); //phpcs:ignore ?>
									</option>
									<?php
								}
							}
							?>
						</select>
					</div>
					<?php
					$product_collection_obj = SureCart\Models\ProductCollection::get();
					?>
					<div class="form-control multi-selection <?php echo (isset($addon_option['services_product_type']) && 'specific_collection' === $addon_option['services_product_type']) ? $hidden_class : 'hidden'; //phpcs:ignore ?>" id="specific-product-collection-selection-div">
						<div class="input-label"><?php esc_html_e( 'Select Specific Product Collections', 'surelywp-services' ); ?></div>
						<label><?php esc_html_e( 'Select the SureCart product collection you want to associate all of its products with this service.', 'surelywp-services' ); ?></label>
						<select multiple="multiple" class="customer-role" name="<?php echo $addon_option_key . '[service_products_collections][]'; //phpcs:ignore ?>">
							<?php
							if ( ! empty( $product_collection_obj ) ) {
								foreach ( $product_collection_obj as $collection ) {
									?>
									<option <?php echo isset($addon_option['service_products_collections']) && in_array($collection->id, (array)$addon_option['service_products_collections'], true) ? 'selected' : ''; //phpcs:ignore ?>
											value="<?php echo $surelywp_model->surelywp_escape_attr( $collection->id ); ?>"><?php esc_html_e( $collection->name ); //phpcs:ignore ?></option>
									<?php
								}
							}
							?>
						</select>
					</div>
				</div>

				<hr>
					<h4 class="heading-text"><?php echo esc_html_e( 'Delivery', 'surelywp-services' ); ?></h4>
					<div class="form-control" id="delivery-time-options">
						<div class="input-label"><?php esc_html_e( 'Delivery Deadline Days', 'surelywp-services' ); ?></div>
						<label><?php esc_html_e( 'Enter the number of days for the default delivery deadline after the order requirements are submitted. This delivery date only affects this service and overrides the default delivery days setting in the general settings.', 'surelywp-services' ); ?></label>
						<?php
						$delivery_time = isset( $addon_option['delivery_time'] ) && ! empty( $addon_option['delivery_time'] ) ? $addon_option['delivery_time'] : Surelywp_Services::get_sv_gen_option( 'default_delivery_time' );
						if ( empty( $delivery_time ) ) {
							$delivery_time = '3';
						}
						?>
						<input type="number" class="widefat" id="delivery-time" name="<?php echo $addon_option_key . '[delivery_time]'; //phpcs:ignore?>" id="delivery-time" value="<?php echo esc_attr( $delivery_time );?>" min="1" max="36500">
					</div>
				</td>
			</tr>
			 
			<tr class="surelywp-field-label sv-rules-settings tab-settings " id="surelywp-sv-recurring-services-settings">
				<td>
					<h4 class="heading-text first-heading"><?php echo esc_html_e( 'Service Creation Rules', 'surelywp-services' ); ?></h4>
					<div class="form-control <?php echo isset( $addon_option['is_enable_recurring_services'] ) ? 'hidden' : ''; ?>" id="surelywp-sv-number-of-allow-sv-per-order-option">
						<div class="input-label"><?php esc_html_e( 'Number Of Services Allowed Per Order', 'surelywp-services' ); ?></div>
						<label><?php esc_html_e( 'Enter the maximum number of services that can be created from a single order. This applies to one-time purchases and installment plans. When the number is greater than one, a button will appear in the customer dashboard, allowing customers to create new services as needed within their limit.', 'surelywp-services' ); ?></label>
						<?php
						$number_of_allow_sv_per_order = isset( $addon_option['number_of_allow_sv_per_order'] ) && ! empty( $addon_option['number_of_allow_sv_per_order'] ) ? $addon_option['number_of_allow_sv_per_order'] : '1';
						if ( empty( $number_of_allow_sv_per_order ) ) {
							$number_of_allow_sv_per_order = '1';
						}
						?>
						<input type="number" class="widefat" name="<?php echo $addon_option_key . '[number_of_allow_sv_per_order]'; //phpcs:ignore?>" value="<?php echo esc_attr( $number_of_allow_sv_per_order );?>" min="1" max=<?php echo PHP_INT_MAX; ?>>
					</div>
					<div class="form-control">
						<div class="input-label"><?php esc_html_e( 'Enable Subscription-Based Service Creation', 'surelywp-services' ); ?></div>
						<label><?php esc_html_e( 'Allow customers to create and manage services based on their subscription billing cycles. This feature is only available for SureCart products with subscription-based pricing and does not apply to one-time purchases or installment plans.', 'surelywp-services' ); ?></label>
						<label class="toggleSwitch xlarge" onclick="">
							<input type="checkbox" id="surelywp-sv-is-enable-recurring-services" name="<?php echo $addon_option_key . '[is_enable_recurring_services]'; //phpcs:ignore?>" value="<?php echo $surelywp_model->surelywp_escape_attr( '1' ); ?>" <?php echo ( isset( $addon_option['is_enable_recurring_services'] ) ) ? checked( $addon_option['is_enable_recurring_services'], 1, true ) : ''; ?> size="10" />
							<span>
								<span><?php echo esc_html_e( 'No', 'surelywp-services' ); ?></span>
								<span><?php echo esc_html_e( 'Yes', 'surelywp-services' ); ?></span>
							</span>
							<a></a>
						</label>
					</div>
					<div class="surelywp-sv-recurring-services-settings-options <?php echo isset( $addon_option['is_enable_recurring_services'] ) ? '' : 'hidden'; ?>" id="surelywp-sv-recurring-services-settings-options">
						<div class="form-control">
							<div class="input-label"><?php esc_html_e( 'Number Of Services Allowed Per Subscription Cycle', 'surelywp-services' ); ?></div>
							<label><?php esc_html_e( 'Enter the maximum number of services that can be created per subscription billing cycle or a custom frequency. Ensure this value is set appropriately to balance customer needs and subscription management. When the number is greater than one, a button will appear in the customer dashboard, allowing customers to create new services as needed within their limit.', 'surelywp-services' ); ?></label>
							<?php
							$number_of_services_allow = isset( $addon_option['number_of_services_allow'] ) && ! empty( $addon_option['number_of_services_allow'] ) ? $addon_option['number_of_services_allow'] : '1';
							if ( empty( $number_of_services_allow ) ) {
								$number_of_services_allow = '3';
							}
							?>
							<input type="number" class="widefat" name="<?php echo $addon_option_key . '[number_of_services_allow]'; //phpcs:ignore?>" value="<?php echo esc_attr( $number_of_services_allow );?>" min="1" max=<?php echo PHP_INT_MAX; ?>>
						</div>
						<div class="form-control">
							<div class="input-label"><?php esc_html_e( 'Number Of Services Allowed Based On', 'surelywp-services' ); ?></div>
							<label><?php esc_html_e( 'Select the frequency used to determine the maximum number of services allowed. You can choose between the SureCart subscription billing cycle or a custom frequency. If selecting a custom frequency, note that it must be equal to or shorter than the SureCart subscription billing period. For example, if the subscription is billed monthly, the custom frequency can be daily, weekly, or monthly.', 'surelywp-services' ); ?></label>
							<select id="recurring-base-on-options" name="<?php echo $addon_option_key . '[recurring_based_on]';  //phpcs:ignore  ?>">
								<option <?php echo (isset($addon_option['recurring_based_on']) && 'subscription_cycle' === $addon_option['recurring_based_on']) ? 'selected' : ''; ?> value="<?php echo $surelywp_model->surelywp_escape_attr('subscription_cycle'); ?>"><?php esc_html_e('SureCart Subscription Billing Cycle', 'surelywp-services'); //phpcs:ignore ?>
								</option>
								<option <?php echo (isset($addon_option['recurring_based_on']) && 'custom_frequency' === $addon_option['recurring_based_on']) ? 'selected' : ''; ?> value="<?php echo $surelywp_model->surelywp_escape_attr('custom_frequency'); ?>"><?php esc_html_e('Custom Frequency', 'surelywp-services'); //phpcs:ignore ?>
								</option>
							</select>
						</div>
						<div class="form-control <?php echo isset( $addon_option['recurring_based_on'] ) && 'custom_frequency' === $addon_option['recurring_based_on'] ? '' : 'hidden'; ?>" id="custom-frequency-options">
							<div class="input-label"><?php esc_html_e( 'Custom Frequency', 'surelywp-services' ); ?></div>
							<label><?php esc_html_e( 'Define the custom frequency for determining the number of allowed services. Options include daily, weekly, monthly, or yearly, but the selected frequency must not exceed the subscription billing period. For example, a subscription billed yearly can allow services on any frequency, while a subscription billed weekly can only allow weekly or daily frequencies.', 'surelywp-services' ); ?></label>
							<select name="<?php echo $addon_option_key . '[custom_frequency]';  //phpcs:ignore  ?>">
								<option <?php echo (isset($addon_option['custom_frequency']) && 'daily' === $addon_option['custom_frequency']) ? 'selected' : ''; ?> value="<?php echo $surelywp_model->surelywp_escape_attr('daily'); ?>"><?php esc_html_e('Daily', 'surelywp-services'); //phpcs:ignore ?>
								</option>
								<option <?php echo (isset($addon_option['custom_frequency']) && 'weekly' === $addon_option['custom_frequency']) ? 'selected' : ''; ?> value="<?php echo $surelywp_model->surelywp_escape_attr('weekly'); ?>"><?php esc_html_e('Weekly', 'surelywp-services'); //phpcs:ignore ?>
								</option>
								<option <?php echo (isset($addon_option['custom_frequency']) && 'monthly' === $addon_option['custom_frequency']) ? 'selected' : ''; ?> value="<?php echo $surelywp_model->surelywp_escape_attr('monthly'); ?>"><?php esc_html_e('Monthly', 'surelywp-services'); //phpcs:ignore ?>
								</option>
								<option <?php echo (isset($addon_option['custom_frequency']) && 'yearly' === $addon_option['custom_frequency']) ? 'selected' : ''; ?> value="<?php echo $surelywp_model->surelywp_escape_attr('yearly'); ?>"><?php esc_html_e('Yearly', 'surelywp-services'); //phpcs:ignore ?>
								</option>
							</select>
						</div>
						<div class="form-control">
							<div class="input-label"><?php esc_html_e( 'Automatically Create A New Service For Each Billing Cycle', 'surelywp-services' ); ?></div>
							<label><?php esc_html_e( 'Enable this option to automatically create and start a new service each time a subscription billing cycle renews. This is particularly helpful for anyone who wants at least one service to begin without requiring the customer to manually create it, regardless of whether the limit for services per cycle is one or more.', 'surelywp-services' ); ?></label>
							<label class="toggleSwitch xlarge" onclick="">
								<input type="checkbox" name="<?php echo $addon_option_key . '[is_auto_create_new_service]'; //phpcs:ignore?>" value="<?php echo $surelywp_model->surelywp_escape_attr( '1' ); ?>" <?php echo ( isset( $addon_option['is_auto_create_new_service'] ) ) ? checked( $addon_option['is_auto_create_new_service'], 1, true ) : ''; ?> size="10" />
								<span>
									<span><?php echo esc_html_e( 'No', 'surelywp-services' ); ?></span>
									<span><?php echo esc_html_e( 'Yes', 'surelywp-services' ); ?></span>
								</span>
								<a></a>
							</label>
						</div>
						<div class="form-control">
							<div class="input-label"><?php esc_html_e( 'Rollover', 'surelywp-services' ); ?></div>
							<label><?php esc_html_e( 'Choose whether unused services should expire at the end of their subscription billing cycle or custom frequency, or if they should roll over and accumulate into the next period. Rollover can be useful for customers who may not fully utilize their service quota within a given period.', 'surelywp-services' ); ?></label>
							<select id="recurring-base-on-options" name="<?php echo $addon_option_key . '[rollover]';  //phpcs:ignore  ?>">
								<option <?php echo (isset($addon_option['rollover']) && 'expire' === $addon_option['rollover']) ? 'selected' : ''; ?> value="<?php echo $surelywp_model->surelywp_escape_attr('expire'); ?>"><?php esc_html_e('Expire', 'surelywp-services'); //phpcs:ignore ?>
								</option>
								<option <?php echo (isset($addon_option['rollover']) && 'rollover' === $addon_option['rollover']) ? 'selected' : ''; ?> value="<?php echo $surelywp_model->surelywp_escape_attr('rollover'); ?>"><?php esc_html_e('Rollover To Next Billing Period ', 'surelywp-services'); //phpcs:ignore ?>
								</option>
							</select>
						</div>
					</div>
				</td>
			</tr>
			<tr class="surelywp-field-label sv-contract-settings tab-settings " id="surelywp-sv-contract-settings">
				<td>
					<h4 class="heading-text first-heading"><?php echo esc_html_e( 'Contract', 'surelywp-services' ); ?></h4>
					<div class="form-control">
						<div class="input-label"><?php echo esc_html_e( 'Enable Contract And Require Digital Signature', 'surelywp-services' ); ?></div>
						<label><?php esc_html_e( 'Choose to enable a contract and digital signature field for the customer to confirm they agree to the contract details about this service before starting on their order.', 'surelywp-services' ); ?></label>
						<label class="toggleSwitch xlarge" onclick="">
							<input type="checkbox" id="ask-for-contract" name="<?php echo $addon_option_key . '[ask_for_contract]'; ?>" value="<?php echo $surelywp_model->surelywp_escape_attr( '1' ); //phpcs:ignore?>" <?php echo ( isset( $addon_option['ask_for_contract'] ) ) ? checked( $addon_option['ask_for_contract'], 1, true ) : ''; ?> size="10" />
							<span>
								<span><?php echo esc_html_e( 'No', 'surelywp-services' ); ?></span>
								<span><?php echo esc_html_e( 'Yes', 'surelywp-services' ); ?></span>
							</span>
							<a></a>
						</label>
					</div>
					<div class="service-contract-fields <?php echo (isset($addon_option['ask_for_contract']) ) ? $hidden_class : 'hidden'; //phpcs:ignore ?>" id="service-contract-fields">
						<div class="form-control">
							<div class="input-label"><?php esc_html_e( 'Contract Details', 'surelywp-services' ); ?></div>
							<label><?php esc_html_e( 'Write the contract details which the customer must agree to by signing their digital signature.', 'surelywp-services' ); ?></label>
							<?php
							$contract_details = ( isset( $addon_option['contract_details'] ) ) ? $addon_option['contract_details'] : '';
							// Add the TinyMCE editor script.
							wp_editor(
								$contract_details, // Initial content, you can fetch saved content here.
								'sv-contract-body', // Editor ID, must be unique.
								array(
									'textarea_name' => $addon_option_key . '[contract_details]', // Name attribute of the textarea.
									'textarea_rows' => 16, // Number of rows.
									'media_buttons' => false, // Show media button in the editor.
									'tinymce'       => array(
										'toolbar1'      => 'bold,italic,underline,|,bullist,numlist,|,link,|,undo,redo',
										'toolbar2'      => '', // Leave empty if you don't want a second toolbar.
										'content_style' => 'body, p, div { font-family: Open Sans, sans-serif; color: #4c5866;}', // Properly escape font-family.
									),
									'quicktags'     => array(
										'buttons' => 'strong,em,link,ul,ol,li,quote',
									),
								)
							);
							?>
						</div>
						<div class="form-control">
							<div class="input-label"><?php esc_html_e( 'Digital Signature Field Title', 'surelywp-services' ); ?></div>
							<label><?php esc_html_e( 'Write a title about the digital signature needed from the customer.', 'surelywp-services' ); ?></label>
							<?php
							$ds_title = ( isset( $addon_option['ds_title'] ) ) ? $addon_option['ds_title'] : esc_html__( 'Digital Signature', 'surelywp-services' );
							?>
							<input type="text" class="req-title widefat" name="<?php echo $addon_option_key . '[ds_title]'; ?>" value="<?php echo $surelywp_model->surelywp_escape_attr( $ds_title ); //phpcs:ignore?>">
						</div>
						<div class="form-control">
							<div class="input-label"><?php esc_html_e( 'Digital Signature Field Description', 'surelywp-services' ); ?></div>
							<label><?php esc_html_e( 'Write an optional description to provide more information about the digital signature needed from the customer.', 'surelywp-services' ); ?></label>
							<?php
							$ds_desc = ( isset( $addon_option['ds_desc'] ) ) ? $addon_option['ds_desc'] : esc_html__( 'Please read through the contact details and terms carefully. After reviewing, kindly sign your digital signature in the field below to confirm your agreement and understanding. Your signature is required to proceed.', 'surelywp-services' );
							?>
							<textarea name="<?php echo $addon_option_key . '[ds_desc]'; ?>" class="contract-desc widefat" rows="5"><?php echo $surelywp_model->surelywp_escape_attr( $ds_desc ); //phpcs:ignore?></textarea>
						</div>
					</div>
				</td>
			</tr>
			<tr class="surelywp-field-label sv-requirements-settings tab-settings " id="surelywp-sv-requirement-settings">
				<td>
					<h4 class="heading-text first-heading"><?php echo esc_html_e( 'Requirements', 'surelywp-services' ); ?></h4>
					<div class="form-control">
						<div class="input-label"><?php echo esc_html_e( 'Ask For Service Requirements', 'surelywp-services' ); ?></div>
						<label><?php esc_html_e( 'Choose to enable service requirements for the customer to provide details about this service before starting on their order.', 'surelywp-services' ); ?></label>
						<label class="toggleSwitch xlarge" onclick="">
							<input type="checkbox" id="ask-for-req" name="<?php echo $addon_option_key . '[ask_for_requirements]'; ?>" value="<?php echo $surelywp_model->surelywp_escape_attr( '1' ); //phpcs:ignore?>" <?php echo ( isset( $addon_option['ask_for_requirements'] ) ) ? checked( $addon_option['ask_for_requirements'], 1, true ) : ''; ?> size="10" />
							<span>
								<span><?php echo esc_html_e( 'No', 'surelywp-services' ); ?></span>
								<span><?php echo esc_html_e( 'Yes', 'surelywp-services' ); ?></span>
							</span>
							<a></a>
						</label>
					</div>
					<div class="service-requirements-fields <?php echo (isset($addon_option['ask_for_requirements']) ) ? $hidden_class : 'hidden'; //phpcs:ignore ?>" id="service-requirements-fields">
						<?php
						if ( empty( $addon_option['req_fields'] ) ) {
							$addon_option['req_fields'] = array(
								array(
									'req_field_label'   => '',
									'req_field_type'    => 'textarea',
									'is_required_field' => '1',
									'req_title'         => '',
									'req_desc'          => '',
								),
							);
						}
						$req_fields_count = 0;
						foreach ( $addon_option['req_fields'] as $key => $field ) {
							$req_field_key    = $addon_option_key . '[req_fields][' . $key . ']';
							$req_fields_count = $key;
							?>
							<div class="service-requirements-field ">
								<div class="service-requirements-top">
									<div class="top-left">
										<div class="req-toogle-btn">
											<img class="req-open-icon" src="<?php echo esc_url( SURELYWP_SERVICES_URL . 'assets/images/open-down-icon.svg' ); ?>" alt="<?php esc_attr_e( 'drag', 'surelywp-services' ); ?>">
											<img class="req-close-icon hidden" src="<?php echo esc_url( SURELYWP_SERVICES_URL . 'assets/images/open-up-icon.svg' ); ?>" alt="<?php esc_attr_e( 'drag', 'surelywp-services' ); ?>">
										</div>
										<div class="req-field-label-top open">
											<?php
											if ( isset( $field['req_field_label'] ) && ! empty( $field['req_field_label'] ) ) {
												echo esc_html( $field['req_field_label'] );
											} elseif ( isset( $field['req_title'] ) && ! empty( $field['req_title'] ) ) {
													echo esc_html( $field['req_title'] );
											}
											?>
										</div>
									</div>
									<div class="field-actions">
										<div class="service-requirements-field-remove <?php echo 0 === $key ? 'hidden' : ''; ?>">
											<img src="<?php echo esc_url( SURELYWP_SERVICES_URL . '/assets/images/remove-icon.svg' ); ?>" alt="<?php esc_attr_e( 'close', 'surelywp-services' ); ?>">
										</div>
										<div class="field-drag-handle">
											<img src="<?php echo esc_url( SURELYWP_SERVICES_URL . 'assets/images/drag-icon.svg' ); ?>" alt="<?php esc_attr_e( 'drag', 'surelywp-services' ); ?>">
										</div>
									</div>
								</div>
								<div class="sv-requirements-options hidden">
									<div class="form-control">
										<div class="input-label"><?php esc_html_e( 'Field Label', 'surelywp-services' ); ?></div>
										<label><?php esc_html_e( 'Enter a unique admin label for this field. This label is for internal reference and will help you easily identify the field within the form settings. It does not appear on the frontend.', 'surelywp-services' ); ?></label>
										<?php
										$req_field_label = ( isset( $field['req_field_label'] ) ) ? $field['req_field_label'] : '';
										?>
										<input type="text" class="req-field-label widefat" name="<?php echo $req_field_key . '[req_field_label]'; ?>" value="<?php echo $surelywp_model->surelywp_escape_attr( $req_field_label ); //phpcs:ignore?>">
									</div>
									<div class="form-control">
										<div class="input-label"><?php esc_html_e( 'Requirement Field Type', 'surelywp-services' ); ?></div>
										<label><?php esc_html_e( 'Select the type of field to use for the customer to provide the details about this requirement.', 'surelywp-services' ); ?></label>
										<select class="req-field-type" name="<?php echo $req_field_key . '[req_field_type]';  //phpcs:ignore  ?>">
											<option <?php echo (isset($field['req_field_type']) && 'text' === $field['req_field_type']) ? 'selected' : ''; ?> value="<?php echo $surelywp_model->surelywp_escape_attr('text'); ?>"><?php esc_html_e('Input', 'surelywp-services'); //phpcs:ignore ?>
											</option>
											<option <?php echo (isset($field['req_field_type']) && 'textarea' === $field['req_field_type']) ? 'selected' : ''; ?> value="<?php echo $surelywp_model->surelywp_escape_attr('textarea'); ?>"><?php esc_html_e('Text Area', 'surelywp-services'); //phpcs:ignore ?>
											</option>
											<option <?php echo (isset($field['req_field_type']) && 'file' === $field['req_field_type']) ? 'selected' : ''; ?> value="<?php echo $surelywp_model->surelywp_escape_attr('file'); ?>"><?php esc_html_e('File Upload', 'surelywp-services'); //phpcs:ignore ?>
											</option>
											<option <?php echo (isset($field['req_field_type']) && 'dropdown' === $field['req_field_type']) ? 'selected' : ''; ?> value="<?php echo $surelywp_model->surelywp_escape_attr('dropdown'); ?>"><?php esc_html_e('Dropdown', 'surelywp-services'); //phpcs:ignore ?>
											</option>
										</select>
									</div>
									<div class="form-control">
										<div id="dropdown-fields-wrapper">
											<?php
											// Convert stored options string to array.
											$dropdown_options = maybe_unserialize( $field['req_field_dropdown_options'] );
											if ( ! is_array( $dropdown_options ) ) {
												$dropdown_options = explode( "\n", $field['req_field_dropdown_options'] );
											}

											foreach ( $dropdown_options as $dropdown_fields_count => $dropdown_option ) {
												$dropdown_field_key = $req_field_key . '[req_field_dropdown_options][' . $dropdown_fields_count . ']';
												?>
												<div class="req-input-dropdown <?php echo ( isset( $field['req_field_type'] ) && 'dropdown' === $field['req_field_type'] ) ? '' : 'hidden-label'; ?>">
													<input type="text" class="req-input-dropdown-options widefat" name="<?php echo $dropdown_field_key; ?>" value="<?php echo esc_attr( $dropdown_option ); ?>">

													<div class="field-actions">
														<div class="dropdown-field-drag-handle">
															<img src="<?php echo esc_url( SURELYWP_SERVICES_URL . 'assets/images/drag-icon.svg' ); ?>" alt="<?php esc_attr_e( 'drag', 'surelywp-services' ); ?>">
														</div>
														<div class="service-requirements-dropdown-field-remove">
															<img src="<?php echo esc_url( SURELYWP_SERVICES_URL . '/assets/images/remove-dropdown-Icon.svg' ); ?>" alt="<?php esc_attr_e( 'close', 'surelywp-services' ); ?>">
														</div>
													</div>
												</div>
												<?php
											}
											?>
										</div>
										<div class="form-control addons-btn-wrap dropdown-add-btn no-sort <?php echo ( isset( $field['req_field_type'] ) && 'dropdown' === $field['req_field_type'] ) ? '' : 'hidden-label'; ?>">
											<input type="hidden" id="dropdown-fields-count" value="<?php echo count( $dropdown_options ) - 1; ?>">
											<a href="javascript:void(0)" id="add-new-dropdown-btn" class="button-primary add-new-dropdown-btn">
												<img src="<?php echo esc_url( SURELYWP_SERVICES_URL . '/assets/images/Add_new.svg' ); ?>" alt="<?php esc_attr_e( 'add_new_dropdown', 'surelywp-services' ); ?>">
												<?php esc_html_e( 'Add New Option', 'surelywp-services' ); ?>
											</a>
										</div>
									</div>
									<div class="form-control">
										<div class="input-label"><?php echo esc_html_e( 'Required Field', 'surelywp-services' ); ?></div>
										<label class="file-required-label <?php echo ( isset( $field['req_field_type'] ) && 'file' === $field['req_field_type'] ) ? '' : 'hidden-label'; ?>"><?php esc_html_e( 'Choose to make this a required field. If enabled, the customer must upload a file to this field in order to submit the requirements.', 'surelywp-services' ); ?></label>
										<label class="textarea-required-label <?php echo ( isset( $field['req_field_type'] ) && 'textarea' === $field['req_field_type'] ) ? '' : 'hidden-label'; ?>"><?php esc_html_e( 'Choose to make this a required field. If enabled, the customer must enter text in this field in order to submit the requirements.', 'surelywp-services' ); ?></label>
										<label class="dropdown-required-label <?php echo ( isset( $field['req_field_type'] ) && 'dropdown' === $field['req_field_type'] ) ? '' : 'hidden-label'; ?>"><?php esc_html_e( 'Choose to make this a required field. If enabled, the customer must select the option from dropdown in order to submit the requirements.', 'surelywp-services' ); ?></label>
										<label class="toggleSwitch xlarge" onclick="">
											<input type="checkbox" class="is-require-field" name="<?php echo $req_field_key . '[is_required_field]'; ?>" value="<?php echo $surelywp_model->surelywp_escape_attr( '1' ); //phpcs:ignore?>" <?php echo ( isset( $field['is_required_field'] ) ) ? checked( $field['is_required_field'], 1, true ) : ''; ?> size="10" />
											<span>
												<span><?php echo esc_html_e( 'No', 'surelywp-services' ); ?></span>
												<span><?php echo esc_html_e( 'Yes', 'surelywp-services' ); ?></span>
											</span>
											<a></a>
										</label>
									</div>
									<div class="form-control">
										<div class="input-label"><?php esc_html_e( 'Requirement Title', 'surelywp-services' ); ?></div>
										<label><?php esc_html_e( 'Write a title or question about the details needed from the customer for this requirement.', 'surelywp-services' ); ?></label>
										<?php
										$req_title = ( isset( $field['req_title'] ) ) ? $field['req_title'] : '';
										?>
										<input type="text" class="req-title widefat" name="<?php echo $req_field_key . '[req_title]'; ?>" value="<?php echo $surelywp_model->surelywp_escape_attr( $req_title ); //phpcs:ignore?>">
									</div>
									<div class="form-control">
										<div class="input-label"><?php esc_html_e( 'Requirement Description', 'surelywp-services' ); ?></div>
										<label class="req-input-label-desc"><?php esc_html_e( 'Write an optional description to provide more information about the details needed from the customer for this requirement.', 'surelywp-services' ); ?></label>
										<?php
										$req_desc = ( isset( $field['req_desc'] ) ) ? $field['req_desc'] : '';

										// Add the TinyMCE editor script.
										wp_editor(
											$req_desc, // Initial content, you can fetch saved content here.
											'sv-requirement-' . $key, // Editor ID, must be unique.
											array(
												'textarea_name' => $req_field_key . '[req_desc]', // Name attribute of the textarea.
												'editor_class' => 'req-desc',
												'textarea_rows' => 5, // Number of rows.
												'media_buttons' => false, // Show media button in the editor.
												'tinymce' => array(
													'toolbar1' => 'bold,italic,underline,|,bullist,numlist,|,link,|,undo,redo',
													'toolbar2' => '', // Leave empty if you don't want a second toolbar.
													'content_style' => 'body, p, div { font-family: Open Sans, sans-serif; color: #4c5866;}', // Properly escape font-family.
												),
												'quicktags' => array(
													'buttons' => 'strong,em,link,ul,ol,li,quote',
												),
											)
										);
										?>
										<div class="note <?php echo ( isset( $field['req_field_type'] ) && 'file' === $field['req_field_type'] ) ? '' : 'hidden'; ?>"><?php esc_html_e( 'NOTE: You can configure your allowed file type and upload preferences in the main services settings.', 'surelywp-services' ); ?></div>
									</div>
									<div class="form-control is-enable-rich-text-editor-option <?php echo ( isset( $field['req_field_type'] ) && 'textarea' === $field['req_field_type'] ) ? '' : 'hidden'; ?>">
										<div class="input-label"><?php echo esc_html_e( 'Enable Rich Text Editor For Customer', 'surelywp-services' ); ?></div>
										<label class="rich-text-edtior-label"><?php esc_html_e( 'Enable this setting to display a rich text editor for the text area field, allowing customers to format their text with options such as bold, italics, underline, links, and lists.', 'surelywp-services' ); ?></label>
										<label class="toggleSwitch xlarge" onclick="">
											<input type="checkbox" class="is-enable-rich-text-editor" name="<?php echo $req_field_key . '[is_enable_rich_text_editor]'; ?>" value="<?php echo $surelywp_model->surelywp_escape_attr( '1' ); //phpcs:ignore?>" <?php echo ( isset( $field['is_enable_rich_text_editor'] ) ) ? checked( $field['is_enable_rich_text_editor'], 1, true ) : ''; ?> size="10" />
											<span>
												<span><?php echo esc_html_e( 'No', 'surelywp-services' ); ?></span>
												<span><?php echo esc_html_e( 'Yes', 'surelywp-services' ); ?></span>
											</span>
											<a></a>
										</label>
									</div>
								</div>
							</div>
						<?php } ?>
						<div class="form-control addons-btn-wrap no-sort">
							<input type="hidden" id="req-fields-count" name="<?php echo $addon_option_key . '[req_fields_count]';  //phpcs:ignore  ?>" value="<?php echo esc_html( $req_fields_count ); ?>">
							<a href="javascript:void(0)" id="add-new-req-btn" class="button-primary">
								<img src="<?php echo esc_url( SURELYWP_SERVICES_URL . '/assets/images/Add_new.svg' ); ?>" alt="<?php esc_attr_e( 'add_new', 'surelywp-services' ); ?>"><?php esc_html_e( 'Add New Requirement', 'surelywp-services' ); ?>
							</a>
						</div>
						<div class="form-control">
							<div class="input-label"><?php echo esc_html_e( 'Display Requirements Form On Product Page', 'surelywp-services' ); ?></div>
							<label class="display-req-product-page"><?php esc_html_e( 'Choose to display the service requirements form on the product page for the customer to provide customization or service details before placing their order.', 'surelywp-services' ); ?></label>
							<label class="toggleSwitch xlarge" onclick="">
								<input type="checkbox" class="is-display-req-form-on-product-page" id="is-display-req-form-on-product-page" name="<?php echo $addon_option_key . '[is_display_req_on_product_page]'; ?>" value="<?php echo $surelywp_model->surelywp_escape_attr( '1' ); //phpcs:ignore?>" <?php echo ( isset( $addon_option['is_display_req_on_product_page'] ) ) ? checked( $addon_option['is_display_req_on_product_page'], 1, true ) : ''; ?> size="10" />
								<span>
									<span><?php echo esc_html_e( 'No', 'surelywp-services' ); ?></span>
									<span><?php echo esc_html_e( 'Yes', 'surelywp-services' ); ?></span>
								</span>
								<a></a>
							</label>
						</div>
						<?php
						if ( ! isset( $addon_option['req_form_position'] ) ) {
							$addon_option['req_form_position'] = 'quantity-selector';
						}
						?>
						<div class="sv-req-form-position-options <?php echo ( isset( $addon_option['is_display_req_on_product_page'] ) ) ? '' : 'hidden'; ?>" id="surelywp-sv-req-form-options">
							<div class="form-control">
								<div class="input-label"><?php esc_html_e( 'Requirements Form Display Location On Product Pages', 'surelywp-services' ); ?></div>
								<label><?php esc_html_e( 'Choose where to show the service requirements form on default SureCart product pages based on relative position to the other elements.', 'surelywp-services' ); ?></label>
								<select id="req-displ" name="<?php echo esc_attr( $addon_option_key . '[req_form_position]' ); ?>">

									<option <?php echo ( isset( $addon_option['req_form_position'] ) && $addon_option['req_form_position'] == 'price' ) ? 'selected' : ''; ?> value="<?php echo esc_attr( $surelywp_model->surelywp_escape_attr( 'price' ) ); ?>"><?php esc_html_e( 'After Price', 'surelywp-services' ); ?></option>

									<option <?php echo ( isset( $addon_option['req_form_position'] ) && $addon_option['req_form_position'] == 'price-choice' ) ? 'selected' : ''; ?> value="<?php echo esc_attr( $surelywp_model->surelywp_escape_attr( 'price-choice' ) ); ?>"><?php esc_html_e( 'After Pricing Options', 'surelywp-services' ); ?></option>

									<option <?php echo ( isset( $addon_option['req_form_position'] ) && $addon_option['req_form_position'] == 'title' ) ? 'selected' : ''; ?> value="<?php echo esc_attr( $surelywp_model->surelywp_escape_attr( 'title' ) ); ?>"><?php esc_html_e( 'After Title', 'surelywp-services' ); ?></option>

									<option <?php echo ( isset( $addon_option['req_form_position'] ) && $addon_option['req_form_position'] == 'description' ) ? 'selected' : ''; ?> value="<?php echo esc_attr( $surelywp_model->surelywp_escape_attr( 'description' ) ); ?>"><?php esc_html_e( 'After Description', 'surelywp-services' ); ?></option>

									<option <?php echo ( isset( $addon_option['req_form_position'] ) && $addon_option['req_form_position'] == 'quantity-selector' ) ? 'selected' : ''; ?> value="<?php echo esc_attr( $surelywp_model->surelywp_escape_attr( 'quantity-selector' ) ); ?>"><?php esc_html_e( 'After Quantity Selector', 'surelywp-services' ); ?></option>

									<option <?php echo ( isset( $addon_option['req_form_position'] ) && $addon_option['req_form_position'] == 'buy-button' ) ? 'selected' : ''; ?> value="<?php echo esc_attr( $surelywp_model->surelywp_escape_attr( 'buy-button' ) ); ?>"><?php esc_html_e( 'After Add To Cart And Buy Now Buttons', 'surelywp-services' ); ?></option>
								</select>
							</div>
						</div>
						<div class="form-control">
							<div class="input-label"><?php echo esc_html_e( 'Requirements Form Shortcode', 'surelywp-services' ); ?></div>
							<label><?php esc_html_e( 'If you are not using the default SureCart product pages but are displaying your SureCart products on other pages or post types, you can place the shortcode [surelywp_services_requirements_form] anywhere in your content. This shortcode will display the associated service\'s requirements form.', 'surelywp-services' ); ?></label>
							<br />
							<label><?php esc_html_e( 'This shortcode requires a product ID if used on a non-product page. You can find the product ID by going to SureCart > Products and checking the URL of the individual product page. The product ID is visible in the URL slug in your browser address bar. An example of the shortcode would be: [surelywp_services_requirements_form product_id="5420a0f8-06d0-491f-9fcf-987b838c406c"]', 'surelywp-services' ); ?></label>
						</div>
						<div class="form-control">
							<div class="input-label"><?php echo esc_html_e( 'Requirements Form Block', 'surelywp-services' ); ?></div>
							<label><?php esc_html_e( 'If you are not using the default SureCart product pages but are displaying your SureCart products on other pages or post types using the WordPress block editor, you can place the Services Requirements Form] block anywhere in your content. This block will display the associated service\'s requirements form.', 'surelywp-services' ); ?></label>
							<br />
							<label><?php esc_html_e( 'This block requires a product ID if used on a non-product page. You can find the product ID by going to SureCart > Products and checking the URL of the individual product page.', 'surelywp-services' ); ?></label>
						</div>
						<div class="form-control">
							<div class="input-label"><?php echo esc_html_e( 'Requirements Checkout Shortcode', 'surelywp-services' ); ?></div>
							<label><?php esc_html_e( 'To display a summary of the customer’s submitted requirements on the SureCart checkout page, use the shortcode [surelywp_services_checkout_requirements]. You can add this shortcode to the checkout form, which is editable from your SureCart > Checkout menu. When added, the shortcode will show a summary of the information entered into the service’s requirements form, ensuring clarity before purchase.', 'surelywp-services' ); ?></label>
						</div>

						
					</div>
				</td>
			</tr>
			<tr class="surelywp-field-label sv-milestones-settings tab-settings " id="surelywp-sv-milestones-settings">
				<td>
					<!-- /** Milestone Features*/ -->
					<h4 class="heading-text first-heading"><?php echo esc_html_e( 'Milestones', 'surelywp-services' ); ?></h4>
					<div class="form-control">
						<div class="input-label"><?php echo esc_html_e( 'Service Milestones', 'surelywp-services' ); ?></div>
						<label><?php esc_html_e( 'Milestones are a structured process where you can share deliverables with the customer and receive their approval or revision requests. By default, there is one delivery milestone, but you can add as many milestones as needed. Each milestone has its own title and custom settings, including whether customer approval is required, how many revisions are allowed, and the deadline for delivering the milestone.', 'surelywp-services' ); ?></label>
					</div>

					<div class="service-milestones-fields" id="service-milestones-fields">
						<?php
						if ( empty( $addon_option['milestones_fields'] ) ) {
							$addon_option['milestones_fields'] = array(
								array(
									'req_field_label'   => '',
									'req_field_type'    => 'textarea',
									'is_required_field' => '1',
									'req_title'         => '',
									'req_desc'          => '',
								),
							);
						}
						$milestones_fields_count = 0;
						foreach ( $addon_option['milestones_fields'] as $key => $field ) {
							$milestones_field_key    = $addon_option_key . '[milestones_fields][' . $key . ']';
							$milestones_fields_count = $key;
							?>
							<div class="service-milestones-field ">
								<div class="service-milestones-top">
									<div class="top-left">
										<div class="mil-toogle-btn">
											<img class="mil-open-icon" src="<?php echo esc_url( SURELYWP_SERVICES_URL . 'assets/images/open-down-icon.svg' ); ?>" alt="<?php esc_attr_e( 'drag', 'surelywp-services' ); ?>">
											<img class="mil-close-icon hidden" src="<?php echo esc_url( SURELYWP_SERVICES_URL . 'assets/images/open-up-icon.svg' ); ?>" alt="<?php esc_attr_e( 'drag', 'surelywp-services' ); ?>">
										</div>
										<div class="mil-field-label-top open">
											<?php
											if ( isset( $field['milestones_field_label'] ) && ! empty( $field['milestones_field_label'] ) ) {
												echo esc_html( $field['milestones_field_label'] );
											} elseif ( isset( $field['milestones_title'] ) && ! empty( $field['milestones_title'] ) ) {
													echo esc_html( $field['milestones_title'] );
											}
											?>
										</div>
									</div>
									<div class="field-actions">
										<div class="service-milestones-field-remove <?php echo 0 === $key ? 'hidden' : ''; ?>">
											<img src="<?php echo esc_url( SURELYWP_SERVICES_URL . '/assets/images/remove-icon.svg' ); ?>" alt="<?php esc_attr_e( 'close', 'surelywp-services' ); ?>">
										</div>
										<div class="field-drag-handle">
											<img src="<?php echo esc_url( SURELYWP_SERVICES_URL . 'assets/images/drag-icon.svg' ); ?>" alt="<?php esc_attr_e( 'drag', 'surelywp-services' ); ?>">
										</div>
									</div>
								</div>
									<div class="sv-milestones-options hidden">
										<div class="form-control">
											<div class="input-label"><?php esc_html_e( 'Field Label', 'surelywp-services' ); ?></div>
											<label><?php esc_html_e( 'Provide a short title for this service milestone. This title will be used to identify the milestone throughout the system, including in the status tracker, activity log, and delivery records.', 'surelywp-services' ); ?></label>
											<?php
											$milestones_field_label = ( isset( $field['milestones_field_label'] ) ) ? $field['milestones_field_label'] : esc_html( 'Final delivery', 'surelywp-services' );
											?>
											<input type="text" class="milestones-field-label widefat" name="<?php echo $milestones_field_key . '[milestones_field_label]'; ?>" value="<?php echo $surelywp_model->surelywp_escape_attr( $milestones_field_label ); //phpcs:ignore?>">
										</div>

										<div class="form-control">
											<div class="input-label"><?php echo esc_html_e( 'Require Approval For Milestone Completion', 'surelywp-services' ); ?></div>
											<label class="required-label "><?php esc_html_e( 'Choose to require approval from the customer for this milestone to be completed. If enabled, the customer must approve the milestone before moving on to the next step. The customer will be given opportunity to request revisions as needed before approving the milestone.', 'surelywp-services' ); ?></label>
											<label class="toggleSwitch xlarge" onclick="">
												<input type="checkbox" class="is-require-field" name="<?php echo $milestones_field_key . '[milestones_require_approval]'; ?>" value="<?php echo $surelywp_model->surelywp_escape_attr( '1' ); //phpcs:ignore?>" <?php echo ( isset( $field['milestones_require_approval'] ) ) ? checked( $field['milestones_require_approval'], 1, true ) : ''; ?> size="10" />
												<span>
													<span><?php echo esc_html_e( 'No', 'surelywp-services' ); ?></span>
													<span><?php echo esc_html_e( 'Yes', 'surelywp-services' ); ?></span>
												</span>
												<a></a>
											</label>
										</div>

										<div class="form-control">
											<div class="input-label"><?php echo esc_html_e( 'Limit The Number Of Revisions Allowed', 'surelywp-services' ); ?></div>
											<label class="required-label"><?php esc_html_e( 'Enable this option to set a limit on the number of revisions the customer can request. If disabled, the customer can request unlimited revisions.', 'surelywp-services' ); ?></label>
											<label class="toggleSwitch xlarge" onclick="">
												<input type="checkbox" class="is-require-field" id="milestones-revision-allowed" name="<?php echo $milestones_field_key . '[milestones_revision_allowed]'; ?>" value="<?php echo $surelywp_model->surelywp_escape_attr( '1' ); //phpcs:ignore?>" <?php echo ( isset( $field['milestones_revision_allowed'] ) ) ? checked( $field['milestones_revision_allowed'], 1, true ) : ''; ?> size="10" />
												<span>
													<span><?php echo esc_html_e( 'No', 'surelywp-services' ); ?></span>
													<span><?php echo esc_html_e( 'Yes', 'surelywp-services' ); ?></span>
												</span>
												<a></a>
											</label>
										</div>

										<div class="form-control surelywp-sv-revision-number-options <?php echo isset( $field['milestones_revision_allowed'] ) ? '' : 'hidden'; ?>" id="surelywp-sv-revision-number-options">
											<div class="input-label"><?php esc_html_e( 'Number Of Revisions Allowed', 'surelywp-services' ); ?></div>
											<label><?php esc_html_e( 'Specify the maximum number of revisions allowed for this milestone. Once this limit is reached, the customer will no longer be able to request further revisions, and after sharing the final delivery, the service will automatically move to the next step.', 'surelywp-services' ); ?></label>
											<?php
											$milestone_revisions_number = ( isset( $field['milestone_revisions_number'] ) ) ? $field['milestone_revisions_number'] : '';
											if ( empty( $milestone_revisions_number ) ) {
												$milestone_revisions_number = '3';
											}
											?>
											<input type="number" min="1" class="mil-title widefat" name="<?php echo $milestones_field_key . '[milestone_revisions_number]'; ?>" value="<?php echo $surelywp_model->surelywp_escape_attr( $milestone_revisions_number ); //phpcs:ignore?>">
										</div>

										<div class="form-control">
											<div class="input-label"><?php esc_html_e( 'Milestone Delivery (Days)', 'surelywp-services' ); ?></div>
											<label><?php esc_html_e( 'Specify the maximum number of days for completing this milestone after the customer has completed the previous step. The admin is responsible for sharing the initial milestone delivery within this timeframe. The countdown timer will update accordingly to keep the admin on track. This deadline applies only to the current milestone. This deadline is for the initial delivery, and is not related to the time needed to complete any revisions requested after the initial delivery.', 'surelywp-services' ); ?></label>
											<?php
											$milestone_delivery_days = ( isset( $field['milestone_delivery_days'] ) ) ? $field['milestone_delivery_days'] : '';
											if ( empty( $milestone_delivery_days ) ) {
												$milestone_delivery_days = '3';
											}
											?>
											<input type="number" min="1" class="mil-title widefat" name="<?php echo $milestones_field_key . '[milestone_delivery_days]'; ?>" value="<?php echo $surelywp_model->surelywp_escape_attr( $milestone_delivery_days ); //phpcs:ignore?>">
										</div>
										 
									</div>
								
							</div>
						<?php } ?>

						<div class="form-control addons-btn-wrap no-sort">
							<input type="hidden" id="milestone-fields-count" name="<?php echo $addon_option_key . '[milestone_fields_count]';  //phpcs:ignore  ?>" value="<?php echo esc_html( $milestones_fields_count ); ?>">
							<a href="javascript:void(0)" id="add-new-milestone-btn" class="button-primary">
								<img src="<?php echo esc_url( SURELYWP_SERVICES_URL . '/assets/images/Add_new.svg' ); ?>" alt="<?php esc_attr_e( 'add_new', 'surelywp-services' ); ?>"><?php esc_html_e( 'Add New Milestone', 'surelywp-services' ); ?>
							</a>
						</div>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
<?php } ?>
<div class="associative-service-delete modal">
	<div class="modal-content">
		<span class="close-button close-modal-button">×</span>
		<h4><?php esc_html_e( 'Are you sure you want to delete this service configuration?', 'surelywp-services' ); ?></h4>
		<div class="modal-btn-wrap">
			<div class="text">
				<?php esc_html_e( 'Deleting this service will also cancel any associated services with a status that is not completed. This action cannot be undone.', 'surelywp-services' ); ?>
			</div>
			<div class="modal-btns">
				<a href="javascript:void(0)" id="cancel-as-service-delete" class="btn-primary button-2 close-modal-button"><?php esc_html_e( 'Cancel', 'surelywp-services' ); ?></a>
				<a href="javascript:void(0)" id="confirm-as-service-delete" class="confirm-as-service-delete btn-secondary button-1"><?php esc_html_e( 'Delete Service', 'surelywp-services' ); ?></a>				
			</div>
		</div>
	</div>
</div>