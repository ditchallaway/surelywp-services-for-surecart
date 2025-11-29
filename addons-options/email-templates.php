<?php
/**
 * Settings
 *
 * @author  Surelywp
 * @package Services For SureCart
 * @version 1.0.0
 */

global $surelywp_model;

$sv_email_templates = Surelywp_Services::surelywp_sv_get_all_email_templetes();

if ( ! isset( $_GET['action'] ) ) { ?>

	<table class="form-table surelywp-ric-settings-box services-templates-table">
		<tbody>

			<!-- Admin Email Templetes -->
			<tr class="surelywp-field-label admin-templetes">
				<td>
					<h4 class="heading-text"><?php echo esc_html_e( 'Service Provider', 'surelywp-services' ); ?></h4>
					<?php
					foreach ( $sv_email_templates as $key => $templates ) {
						if ( 'admin' === $templates['for'] ) {
							?>
							<div class="form-control services-templates">
								<div class="image key">
									<img src="<?php echo esc_url( SURELYWP_SERVICES_URL . '/assets/images/email_reports.svg' ); ?>" alt="<?php esc_attr_e( 'email', 'surelywp-services' ); ?>">
								</div>
								<div class="surelywp-sv-lable-bagde">
									<div class="input-label">
										<?php echo esc_html( $templates['name'] ); ?>
									</div> 
									<?php
									if ( ! empty( $addons_option[$key] ) ) {
										$badge = $addons_option[ $key ]['enable_email'] ? __( 'Enabled', 'surelywp-services' ) : __( 'Disabled', 'surelywp-services' );
									} else {
										$badge = __( 'Enabled', 'surelywp-services' );
									}
									?>
									<span class="surelywp-sv-badge <?php echo esc_html( $badge ); ?>"><?php echo esc_html( $badge ); ?></span>
								</div>
								<div class="image edit-icon">
								<?php
									$sv_email_templates_edit_url = add_query_arg(
										array(
											'action'   => 'edit_sv_email_template',
											'template' => $key,
										),
										get_permalink()
									);
								?>
									<a href="<?php echo esc_url( $sv_email_templates_edit_url ); ?>">
										<img src="<?php echo esc_url( SURELYWP_SERVICES_URL . '/assets/images/list_edit.svg' ); ?>" alt="<?php esc_attr_e( 'email_edit', 'surelywp-services' ); ?>">
									</a>
								</div>
							</div>
							<?php
						}
					}
					?>
				</td>
			</tr>
			<!-- Customer Email Templetes -->
			<tr class="surelywp-field-label">
				<td>
					<h4 class="heading-text"><?php echo esc_html_e( 'Customer', 'surelywp-services' ); ?></h4>
					<?php
					foreach ( $sv_email_templates as $key => $templates ) {
						if ( 'customer' === $templates['for'] ) {
							?>
							<div class="form-control services-templates">
								<div class="image key">
									<img src="<?php echo esc_url( SURELYWP_SERVICES_URL . '/assets/images/email_reports.svg' ); ?>" alt="<?php esc_attr_e( 'email', 'surelywp-services' ); ?>">
								</div>
								<div class="surelywp-sv-lable-bagde">
									<div class="input-label">
										<?php echo esc_html( $templates['name'] ); ?>
									</div> 
									<?php
									if ( ! empty( $addons_option[$key] ) ) {
										$badge = $addons_option[ $key ]['enable_email'] ? __( 'Enabled', 'surelywp-services' ) : __( 'Disabled', 'surelywp-services' );
									} else {
										$badge = __( 'Enabled', 'surelywp-services' );
									}
									?>
									<span class="surelywp-sv-badge <?php echo esc_html( $badge ); ?>"><?php echo esc_html( $badge ); ?></span>
								</div>
								<div class="image edit-icon">
								<?php
									$sv_email_templates_edit_url = add_query_arg(
										array(
											'action'   => 'edit_sv_email_template',
											'template' => $key,
										),
										get_permalink()
									);
								?>
									<a href="<?php echo esc_url( $sv_email_templates_edit_url ); ?>">
										<img src="<?php echo esc_url( SURELYWP_SERVICES_URL . '/assets/images/list_edit.svg' ); ?>" alt="<?php esc_attr_e( 'email_edit', 'surelywp-services' ); ?>">
									</a>
								</div>
							</div>
							<?php
						}
					}
					?>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
} elseif ( isset( $_GET['action'] ) && ( 'edit_sv_email_template' === $_GET['action'] ) ) {

	$email_template_key = ( isset( $_GET['template'] ) && ! empty( $_GET['template'] ) ) ? sanitize_text_field( wp_unslash( $_GET['template'] ) ) : '';

	if ( ! empty( $addons_option ) ) {
		$addons_option = $addons_option[ $email_template_key ] ?? array();
	}
	$template_name     = $_GET['template'];
	$addons_option_key = $option_key . '_options[surelywp_sv_email_templates_options][' . $template_name . ']';

	// set default options.
	if ( ! isset( $addons_option['sv_update_email_options'] ) ) {

		$addons_option['enable_email'] = '1';
	}
	?>
	<table class="form-table surelywp-ric-settings-box email-template-edit">
		<tbody>
			<tr class="surelywp-field-label">
				<td>
				<div class="form-control">
					<div class="input-label"><?php esc_html_e( 'Enable Email', 'surelywp-services' ); ?></div>
					<label><?php esc_html_e( 'Choose to enable or disable sending for this email.', 'surelywp-services' ); ?></label>
					<label class="toggleSwitch xlarge" onclick="">
						<input type="hidden" name="<?php echo esc_attr( $addons_option_key ) . '[sv_update_email_options]'; ?>" value="<?php echo esc_attr( $surelywp_model->surelywp_escape_attr( 'updated' ) ); ?>" />
						<input type="checkbox" id="surelywp-services-email-enable" name="<?php echo esc_attr( $addons_option_key ) . '[enable_email]'; ?>" value="<?php echo esc_attr( $surelywp_model->surelywp_escape_attr( '1' ) ); ?>" <?php echo ( isset( $addons_option['enable_email'] ) ) ? checked( $addons_option['enable_email'], 1, true ) : ''; ?> size="10" />
						<span>
							<span><?php esc_html_e( 'No', 'surelywp-services' ); ?></span>
							<span><?php esc_html_e( 'Yes', 'surelywp-services' ); ?></span>
						</span>
						<a></a>
					</label>
				</div>
					<div class="form-control">
						<div class="input-label"><?php esc_html_e( 'Email Subject', 'surelywp-services' ); ?></div>
						<label><?php esc_html_e( 'Enter custom text you want to use for email subject.', 'surelywp-services' ); ?></label>
						<?php
							$default_email_subject = $sv_email_templates[ $email_template_key ]['email_subject'];
							$email_subject         = ( isset( $addons_option['email_subject'] ) ) ? $addons_option['email_subject'] : $default_email_subject;
						?>
						<input type="text" class="widefat" name="<?php echo esc_attr( $addons_option_key ) . '[email_subject]'; ?>" value="<?php echo esc_attr( $surelywp_model->surelywp_escape_attr( $email_subject ) ); ?>">
					</div>
					<div class="form-control">
						<div class="input-label"><?php esc_html_e( 'Email Body', 'surelywp-services' ); ?></div>
						<label><?php esc_html_e( 'Enter custom text you want to use for email body.', 'surelywp-services' ); ?></label>
						<?php
							$default_email_body = $sv_email_templates[ $email_template_key ]['email_body'];
							$email_body         = ( isset( $addons_option['email_body'] ) ) ? $addons_option['email_body'] : $default_email_body;

							// Add the TinyMCE editor script.
							wp_editor(
								$email_body, // Initial content, you can fetch saved content here.
								'sv-email-body', // Editor ID, must be unique.
								array(
									'textarea_name' => $addons_option_key . '[email_body]', // Name attribute of the textarea.
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
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}