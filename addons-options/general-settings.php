<?php
/**
 * Settings
 *
 * @author  Surelywp
 * @package Services For SureCart
 * @version 1.0.0
 */

global $surelywp_model, $surelywp_sv_model;

// Options name key.
$addons_option_key = $option_key . '_options[surelywp_sv_gen_settings_options]';

$days_options = array(
	'1'  => esc_html__( '1 Days', 'surelywp-services' ),
	'2'  => esc_html__( '2 Days', 'surelywp-services' ),
	'3'  => esc_html__( '3 Days', 'surelywp-services' ),
	'4'  => esc_html__( '4 Days', 'surelywp-services' ),
	'5'  => esc_html__( '5 Days', 'surelywp-services' ),
	'6'  => esc_html__( '6 Days', 'surelywp-services' ),
	'7'  => esc_html__( '7 Days', 'surelywp-services' ),
	'8'  => esc_html__( '8 Days', 'surelywp-services' ),
	'9'  => esc_html__( '9 Days', 'surelywp-services' ),
	'10' => esc_html__( '10 Days', 'surelywp-services' ),
);

$req_reminder_hours_options = array(
	'12' => esc_html__( '12 Hours', 'surelywp-services' ),
	'24' => esc_html__( '24 Hours', 'surelywp-services' ),
);

$allow_file_types = array(
	'svg' => 'image/svg+xml',
);

// Add WordPress default file types.
$site_enable_mime_types = get_allowed_mime_types();
if ( ! empty( $site_enable_mime_types ) ) {
	$allow_file_types = array_merge( $site_enable_mime_types, $allow_file_types );
}

$file_size_limits = array(
	'1'  => esc_html__( '1 MB', 'surelywp-services' ),
	'5'  => esc_html__( '5 MB', 'surelywp-services' ),
	'10' => esc_html__( '10 MB', 'surelywp-services' ),
	'15' => esc_html__( '15 MB', 'surelywp-services' ),
	'20' => esc_html__( '20 MB', 'surelywp-services' ),
);

// set default options.
if ( ! isset( $addons_option['sv_update_gen_options'] ) ) {

	$addons_option['sv_access_user_roles']  = array( 'administrator' );
	$addons_option['recipient_emails']      = Surelywp_Services::get_admin_emails();
	$addons_option['default_delivery_time'] = '3';
	$addons_option['req_reminder_time']     = '12';
	$addons_option['auto_complete_time']    = '5';
	$addons_option['file_types']            = array(
		'image/jpeg',
		'image/png',
		'image/svg+xml',
		'application/zip',
		'application/pdf',
		'application/msword',
		'application/vnd.ms-excel',
	);
	$addons_option['file_size']             = '5'; // In MB.
	$addons_option['redirect_to_services']  = '1';

}
?>
<table class="form-table surelywp-ric-settings-box services-templates-table">
	<tbody>
		<tr class="surelywp-field-label" id="surelywp-sv-role-settings">
			<td>
				<h4 class="heading-text"><?php echo esc_html_e( 'General', 'surelywp-services' ); ?></h4>
				<div class="form-control multi-selection" id="user-role-selection-div">
					<div class="input-label"><?php esc_html_e( 'User Roles With Services Access', 'surelywp-services' ); ?></div>
					<label><?php esc_html_e( 'Select which user roles have permission to access services. Any user with the selected user roles will be able to view and reply to services.', 'surelywp-services' ); ?></label>
					<input type="hidden" name="<?php echo $addons_option_key . '[sv_update_gen_options]'; ?>" value="<?php echo $surelywp_model->surelywp_escape_attr( 'updated' ); //phpcs:ignore ?>" />
					<select multiple="multiple" id="surelywp-sv-user-roles" class="customer-role" name="<?php echo $addons_option_key . '[sv_access_user_roles][]'; //phpcs:ignore?>">
						<?php
						$roles      = wp_roles();
						$role_names = $roles->get_names();
						if ( ! empty( $role_names ) ) {
							foreach ( $role_names as $role_key => $role_name ) {
								if ( get_role( $role_key )->has_cap( 'edit_sc_orders' ) ) {
									?>
									<option value="<?php echo $surelywp_model->surelywp_escape_attr( $role_key ); //phpcs:ignore?>" <?php echo isset( $addons_option['sv_access_user_roles'] ) && in_array( $role_key, (array)$addons_option['sv_access_user_roles'], true ) ? 'selected' : ''; ?>><?php echo esc_html( $role_name ); ?></option>
									<?php
								}
							}
						}
						?>
					</select>
				</div>
				<div class="form-control multi-selection" id="sv-recipient-email-selection-div">
						<div class="input-label"><?php esc_html_e( 'Recipient Email Addresses For Services Notifications', 'surelywp-services' ); ?></div>
						<label><?php esc_html_e( 'Enter email addresses of the recipients who should receive email notifications for services.', 'surelywp-services' ); ?></label>
						<select multiple="multiple" data-tags="true" id="sv-recipient-email" name="<?php echo esc_attr( $addons_option_key ) . '[recipient_emails][]'; ?>">
							<?php
							if ( isset( $addons_option['recipient_emails'] ) && ! empty( $addons_option['recipient_emails'] ) ) {
								foreach ( $addons_option['recipient_emails'] as $email ) {
									?>
									<option selected><?php echo esc_html( $email ); ?></option>
									<?php
								}
							}
							?>
						</select>
					</div>
				<div class="form-control" id="default-delivery-options">
					<div class="input-label"><?php esc_html_e( 'Default Delivery Deadline Days', 'surelywp-services' ); ?></div>
					<label><?php esc_html_e( 'Enter the number of days for the default delivery deadline after the order requirements are submitted.', 'surelywp-services' ); ?></label>
					<?php $default_delivery_time = isset( $addons_option['default_delivery_time'] ) && ! empty( $addons_option['default_delivery_time'] ) ? $addons_option['default_delivery_time'] : '3'; ?>
					<input type="number" class="widefat" name="<?php echo $addons_option_key . '[default_delivery_time]'; //phpcs:ignore?>" id="default-delivery-time" value="<?php echo esc_attr( $default_delivery_time );?>" min="1" max="36500">
				</div>
				<div class="form-control" id="redirect-to-service-tab">
					<div class="input-label"><?php esc_html_e( 'Redirect Customer To Service After Checkout', 'surelywp-services' ); ?></div>
					<label><?php esc_html_e( 'Choose to redirect the customer to the individual service page rather than the Customer Dashboard after they complete the checkout in SureCart.', 'surelywp-services' ); ?></label>
					<label class="toggleSwitch xlarge" onclick="">
						<input type="checkbox" id="redirect-to-services" name="<?php echo $addons_option_key . '[redirect_to_services]'; //phpcs:ignore ?>" value="<?php echo $surelywp_model->surelywp_escape_attr( '1' ); ?>" <?php echo ( isset( $addons_option['redirect_to_services'] ) ) ? checked( $addons_option['redirect_to_services'], 1, true ) : ''; ?> size="10" />
						<span>
							<span><?php esc_html_e( 'No', 'surelywp-services' ); ?></span>
							<span><?php esc_html_e( 'Yes', 'surelywp-services' ); ?></span>
						</span>
						<a></a>
					</label>
				</div>
				<div class="form-control" id="requirement-reminder-option">
					<div class="input-label"><?php esc_html_e( 'Send Customer Requirements Reminder After', 'surelywp-services' ); ?></div>
					<label><?php esc_html_e( 'Choose the amount of time after the order is place to send a reminder email to the customer if they have not yet submitted the requirements.', 'surelywp-services' ); ?></label>
					<select name="<?php echo $addons_option_key . '[req_reminder_time]'; //phpcs:ignore?>" id="requirement-reminder-time">
						<?php
						foreach ( $req_reminder_hours_options as $time => $time_text ) {
							?>
							<option <?php echo ( isset( $addons_option['req_reminder_time'] ) && (string) $time === $addons_option['req_reminder_time'] ) ? 'selected' : ''; ?> value="<?php echo $surelywp_model->surelywp_escape_attr( $time ); //phpcs:ignore?>"><?php echo esc_html( $time_text ); ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-control">
					<div class="input-label"><?php esc_html_e( 'Auto Complete Orders', 'surelywp-services' ); ?></div>
					<label><?php esc_html_e( 'Choose to automatically complete orders if the customer does not approve the final delivery after a specific number of days.', 'surelywp-services' ); ?></label>
					<label class="toggleSwitch xlarge" onclick="">
						<input type="checkbox" id="is-enable-auto-order-complete" name="<?php echo $addons_option_key . '[is_enable_auto_order_complete]'; //phpcs:ignore ?>" value="<?php echo $surelywp_model->surelywp_escape_attr( '1' ); ?>" <?php echo ( isset( $addons_option['is_enable_auto_order_complete'] ) ) ? checked( $addons_option['is_enable_auto_order_complete'], 1, true ) : ''; ?> size="10" />
						<span>
							<span><?php esc_html_e( 'No', 'surelywp-services' ); ?></span>
							<span><?php esc_html_e( 'Yes', 'surelywp-services' ); ?></span>
						</span>
						<a></a>
					</label>
				</div>
				<div class="form-control <?php echo isset( $addons_option['is_enable_auto_order_complete'] ) ? '' : 'hidden'; ?>" id="auto-complete-option">
					<div class="input-label"><?php esc_html_e( 'Auto Complete Order After', 'surelywp-services' ); ?></div>
					<label><?php esc_html_e( 'Choose the number of days after the deliver is sent to automatically complete the order if the customer does not approve the final delivery.', 'surelywp-services' ); ?></label>
					<select name="<?php echo $addons_option_key . '[auto_complete_time]'; //phpcs:ignore?>" id="auto-complete-time">
						<?php
						foreach ( $days_options as $time => $time_text ) {
							?>
							<option <?php echo ( isset( $addons_option['auto_complete_time'] ) && (string) $time === $addons_option['auto_complete_time'] ) ? 'selected' : ''; ?> value="<?php echo $surelywp_model->surelywp_escape_attr( $time ); //phpcs:ignore?>"><?php echo esc_html( $time_text ); ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-control" id="service-id-staring-number-option">
					<div class="input-label"><?php esc_html_e( 'Starting Service ID Number', 'surelywp-services' ); ?></div>
					<?php $last_service_id = $surelywp_sv_model->surelywp_sv_get_last_service_id(); ?>
					<label>
						<?php
							// translators: %s is the last service ID.
							printf( esc_html__( 'Choose the starting service ID number assigned to each new service. The service IDs will be sequential by default. NOTE: The starting number must be greater than the largest existing service number. Current largest service number: %s ', 'surelywp-services' ), esc_html( $last_service_id ) );
						?>
					</label>
					<?php $service_id_starting_number = $addons_option['service_id_starting_number'] ?? ''; ?>
					<input type="number" class="req-title widefat" name="<?php echo $addons_option_key . '[service_id_starting_number]'; ?>" value="<?php echo $surelywp_model->surelywp_escape_attr( $service_id_starting_number ); //phpcs:ignore?>">
				</div>
				<div class="form-control">
					<div class="input-label"><?php esc_html_e( 'Rename Service Singular Name', 'surelywp-services' ); ?></div>
					<label><?php esc_html_e( 'Enter the singular term you want to use instead of "Service." This will replace the word "Service" across the interface where a single service is referenced. For example, if you offer coaching, you might enter "Coaching Call" instead.', 'surelywp-services' ); ?></label>
					<?php $service_singular_name = $addons_option['service_singular_name'] ?? esc_html__( 'Service', 'surelywp-services' ); ?>
					<input type="text" class="widefat" name="<?php echo $addons_option_key . '[service_singular_name]'; ?>" value="<?php echo $surelywp_model->surelywp_escape_attr( $service_singular_name ); //phpcs:ignore?>">
				</div>
				<div class="form-control">
					<div class="input-label"><?php esc_html_e( 'Rename Services Plural Name', 'surelywp-services' ); ?></div>
					<label><?php esc_html_e( 'Enter the plural term you want to use instead of "Services." This will replace the word "Services" across the interface where multiple services are referenced. For example, if you offer coaching, you might enter "Coaching Calls" instead.', 'surelywp-services' ); ?></label>
					<?php $service_plural_name = $addons_option['service_plural_name'] ?? esc_html__( 'Services', 'surelywp-services' ); ?>
					<input type="text" class="widefat" name="<?php echo $addons_option_key . '[service_plural_name]'; ?>" value="<?php echo $surelywp_model->surelywp_escape_attr( $service_plural_name ); //phpcs:ignore?>">
				</div>
				<div class="form-control">
					<div class="input-label"><?php esc_html_e( 'Rename Customer Dashboard Tab Text', 'surelywp-services' ); ?></div>
					<label><?php esc_html_e( 'Enter custom text to display in the Customer Dashboard menu for the services tab. ', 'surelywp-services' ); ?></label>
					<?php $service_tab_lable = $addons_option['service_tab_lable'] ?? esc_html__( 'Services', 'surelywp-services' ); ?>
					<input type="text" class="widefat" name="<?php echo $addons_option_key . '[service_tab_lable]'; ?>" value="<?php echo $surelywp_model->surelywp_escape_attr( $service_tab_lable ); //phpcs:ignore?>">
				</div>
				<div class="form-control">
					<div class="input-label"><?php esc_html_e( 'Replace Customer Dashboard Tab Icon', 'surelywp-services' ); ?></div>
					<label><?php esc_html_e( 'Enter the name of an icon from https://feathericons.com/ to display as the icon in the Customer Dashboard menu for the services tab. ', 'surelywp-services' ); ?></label>
					<?php $service_tab_icon = $addons_option['service_tab_icon'] ?? esc_html( 'check-circle' ); ?>
					<input type="text" class="widefat" name="<?php echo $addons_option_key . '[service_tab_icon]'; ?>" value="<?php echo $surelywp_model->surelywp_escape_attr( $service_tab_icon ); //phpcs:ignore?>">
				</div>
			</td>
		</tr>
		<tr class="surelywp-field-label" id="surelywp-sv-files-settings">
			<td>
				<hr>
				<h4 class="heading-text"><?php echo esc_html_e( 'Files', 'surelywp-services' ); ?></h4>
				<div class="form-control">
					<div class="input-label"><?php esc_html_e( 'Allow File Uploads', 'surelywp-services' ); ?></div>
					<label><?php esc_html_e( 'Choose to allow file uploads within services.', 'surelywp-services' ); ?></label>
					<label class="toggleSwitch xlarge" onclick="">
						<input type="checkbox" id="is-enable-allow-file-uploads" name="<?php echo $addons_option_key . '[is_enable_allow_file_uploads]'; //phpcs:ignore ?>" value="<?php echo $surelywp_model->surelywp_escape_attr( '1' ); ?>" <?php echo ( isset( $addons_option['is_enable_allow_file_uploads'] ) ) ? checked( $addons_option['is_enable_allow_file_uploads'], 1, true ) : ''; ?> size="10" />
						<span>
							<span><?php esc_html_e( 'No', 'surelywp-services' ); ?></span>
							<span><?php esc_html_e( 'Yes', 'surelywp-services' ); ?></span>
						</span>
						<a></a>
					</label>
				</div>
				<div class="form-control multi-selection <?php echo isset( $addons_option['is_enable_allow_file_uploads'] ) ? '' : 'hidden'; ?>" id="file-type-option">
					<div class="input-label"><?php esc_html_e( 'Allowed Files Types', 'surelywp-services' ); ?></div>
					<label><?php esc_html_e( 'Choose the type of files allowed to be uploaded in the requirement details, messages, and final delivery. Keep in mind that WordPress or your hosting server may have restrictions on certain file types. Ensure the selected file types are compatible with your systems to avoid issues.', 'surelywp-services' ); ?></label>
					<select multiple="multiple" name="<?php echo $addons_option_key . '[file_types][]'; //phpcs:ignore?>" id="file-types" class="customer-role">
						<?php
						foreach ( $allow_file_types as $key => $type ) {
							?>
							<option <?php echo isset( $addons_option['file_types'] ) && in_array( $type, (array) $addons_option['file_types'], true ) ? 'selected' : ''; ?> value="<?php echo $surelywp_model->surelywp_escape_attr( $type ); //phpcs:ignore?>"><?php echo esc_html( $key ); ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-control multi-selection <?php echo isset( $addons_option['is_enable_allow_file_uploads'] ) ? '' : 'hidden'; ?>" id="add-mime-type-option">
					<div class="input-label"><?php esc_html_e( 'Add Custom Mime Types', 'surelywp-services' ); ?></div>
					<label><?php esc_html_e( 'Enter the new MIME type you want to allow for file uploads. Keep in mind that WordPress or your hosting server may have restrictions on certain file types, and the email inboxes of both the customer and admin might also have limitations that could affect file delivery. Ensure the selected file types are compatible with these systems to avoid issues.', 'surelywp-services' ); ?></label>
					<select multiple="multiple" name="<?php echo $addons_option_key . '[custom_mime_types][]'; //phpcs:ignore?>" id="sv-add-mime-types" class="sv-add-mime-types">
						<?php
						if ( isset( $addons_option['custom_mime_types'] ) && ! empty( $addons_option['custom_mime_types'] ) ) {
							foreach ( $addons_option['custom_mime_types'] as $mime_types ) {
								?>
									<option selected><?php echo esc_html( $mime_types ); ?></option>
									<?php
							}
						}
						?>
					</select>
				</div>
				<div class="form-control <?php echo isset( $addons_option['is_enable_allow_file_uploads'] ) ? '' : 'hidden'; ?>" id="file-size-option">
					<div class="input-label"><?php esc_html_e( 'File Upload Size Limit', 'surelywp-services' ); ?></div>
					<label><?php esc_html_e( 'Select the maximum file size allowed for uploads. Keep in mind that WordPress or your hosting server might have file upload limits that need to be adjusted.', 'surelywp-services' ); ?></label>
					<select name="<?php echo $addons_option_key . '[file_size]'; //phpcs:ignore?>" id="file-size">
						<?php
						foreach ( $file_size_limits as $key => $sizes ) {
							?>
							<option <?php echo ( isset( $addons_option['file_size'] ) && (string) $key === $addons_option['file_size'] ) ? 'selected' : ''; ?> value="<?php echo $surelywp_model->surelywp_escape_attr( $key ); //phpcs:ignore?>"><?php echo esc_html( $sizes ); ?></option>
						<?php } ?>
					</select>
				</div>
			</td>
		</tr>
		<tr class="surelywp-field-label" id="surelywp-sv-block-info">
			<td>
				<hr>
				<h4 class="heading-text"><?php echo esc_html_e( 'Customer Services List', 'surelywp-services' ); ?></h4>
				<div class="form-control">
					<div class="input-label"><?php esc_html_e( 'Services List Shortcode', 'surelywp-services' ); ?></div>
					<label><?php esc_html_e( 'If you want to display the customer services list on any page using a page builder, you can use the shortcode [surelywp_customer_services]. The shortcode supports a title attribute to customize the heading, and a page_id attribute to specify the page where the shortcode is placed. The page_id is crucial when using the shortcode on pages other than the default SureCart dashboard, as it ensures that the individual service links redirect to the correct URL instead of the default SureCart customer dashboard URL.', 'surelywp-services' ); ?></label>
				</div>
				<div class="form-control">
					<div class="input-label"><?php esc_html_e( 'Services List Block', 'surelywp-services' ); ?></div>
					<label><?php esc_html_e( 'If you want to display the customer services list on any page using the WordPress block editor, you can use the Services List block. The block includes options to set a custom title and specify the page ID. The page ID is crucial when using the block on pages other than the default SureCart dashboard, as it ensures that individual service links redirect to the correct URL instead of the default SureCart customer dashboard URL.', 'surelywp-services' ); ?></label>
				</div>
			</td>
		</tr>
	</tbody>
</table>