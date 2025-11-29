<?php
/**
 * Requirement Tab.
 *
 * @package Services For SureCart
 * @author  SurelyWP
 * @version 1.0.0
 */

$current_user_id  = get_current_user_id();
$access_users_ids = Surelywp_Services::get_sv_access_users_ids();
?>
<div class="services-requirements-wrap service-tab <?php echo 'requirements' === $service_tab ? '' : 'hidden'; ?>">
	<div class="heading" no-padding>
		<sc-stacked-list>
			<sc-stacked-list-row><?php esc_html_e( 'Requirements', 'surelywp-services' ); ?></sc-stacked-list-row>
		</sc-stacked-list>
	</div>
	<div class="services-requirements-card tab-card" no-padding>
		<div class="surelywp-sv-requirements" id="surelywp-sv-requirements">
			<?php
			$requirements = $surelywp_sv_model->surelywp_sv_get_service_requirements( $service_id );

			// Display Requirement if Submitted.
			if ( ! empty( $requirements ) ) {

				?>
				<div class="accordion">
					<?php
					$requirement_count = 1;
					$paths             = $services_obj->surelywp_sv_get_req_path( $service_id );
					$folder_url        = $paths['folder_url'];
					$folder_path       = $paths['folder_path'];

					foreach ( $requirements as $key => $requirement ) {
						$requirement_id         = $requirement->requirement_id ?? '';
						$requirement_type       = $requirement->requirement_type ?? '';
						$requirement_title      = $requirement->requirement_title ?? '';
						$requirement_desc       = $requirement->requirement_desc ?? '';
						$requirement            = $requirement->requirement ?? '';
						$requirement_created_at = $requirement->created_at ?? '';
						?>
						<div class="accordion-item">
							<div class="accordion-header">
								<div class="accordion-question"><span class="question-count"><?php printf( '%02d', esc_html( $requirement_count ) ); ?></span>
									<?php if ( ! empty( $requirement_title ) ) { ?>
										<p><?php echo esc_html( $requirement_title ); ?></p>
									<?php } ?>
								</div>
							</div>
							<?php if ( ! empty( $requirement ) ) { ?>
								<div class="accordion-content">
									<?php if ( 'textarea' === $requirement_type || 'text' === $requirement_type || 'dropdown' === $requirement_type ) { ?>
										<p><?php echo wp_kses_post( $requirement ); ?></p>
										<?php
									} elseif ( 'file' === $requirement_type ) {

										$requirements = json_decode( $requirement, true );
										?>
										<div class="attachetment-inner">

										<?php
										foreach ( $requirements as $requirement ) {

											$requirement_file_url = $folder_url . '/' . $requirement;
											$file_path            = $folder_path . '/' . $requirement;
											$display_file_name    = preg_replace( '/-\d+(?=\.[^.]+$)/', '', $requirement );
											$file_info            = Surelywp_Services::surelywp_sv_get_file_info( $requirement_file_url, $file_path );
											$image_extension      = Surelywp_Services::surelywp_sv_get_image_extensions();
											$extension            = $file_info['extension'] ?? '';
											$file_size            = $file_info['size'] ?? '';

											?>
											<div class="attachetment-inner-wrap">
												<div class="attachetment-img">
													<?php
													$attachment_img_url  = '';
													$lightbox_attributes = '';
													if ( $extension && in_array( $extension, $image_extension ) ) {
														$attachment_img_url  = $requirement_file_url;
														$lightbox_attributes = 'data-lightbox="requirement-images-' . $requirement_id . '" data-title="' . esc_attr( $display_file_name ) . '"';
													} else {
														$attachment_img_url = SURELYWP_SERVICES_ASSETS_URL . '/images/file-pre.png';
													}
													?>
													<a href="<?php echo esc_html( $requirement_file_url ); ?>" <?php echo esc_html( $lightbox_attributes ); ?>>
														<img src="<?php echo esc_url( $attachment_img_url ); ?>" alt="attachetment">
													</a>
												</div>
												<div class="attachetment-title">
													<div class="title">
														<?php
															// translators: %1$s is the file name and %2$s is the file size.
															printf( esc_html__( '%1$s (%2$s)', 'surelywp-services' ), esc_html( $display_file_name ), esc_html( $file_size ) );
														?>
													</div>
													<div class="attachetment-buttons">
														<a href="<?php echo esc_url( $requirement_file_url ); ?>" download="<?php echo esc_attr( $display_file_name ); ?>"><img class="download-arrow" src="<?php echo esc_url( SURELYWP_SERVICES_ASSETS_URL . '/images/download-arrow.svg' ); ?>" alt="download"></a>
													</div>
												</div>
											</div>
										<?php } ?>
										</div>
									<?php } ?>
								</div>
							<?php } ?>
						</div>
						<?php
						++$requirement_count;
					}
					?>
				</div>
				<?php } elseif ( ! empty( $access_users_ids ) && in_array( $current_user_id, (array) $access_users_ids ) ) { ?>
					<p class="no-found"><?php echo esc_html__( 'The customer hasn\'t submitted any requirements yet.', 'surelywp-services' ); ?></p>
				<?php
				} elseif ( ( 'waiting_for_req' === $service_status || 'waiting_for_contract' === $service_status ) && '1' === $ask_for_requirements && ! empty( $req_fields ) ) {

					// Requirement Form.
					// if admin set req_fields and customer have not submitted Requirements.
					?>
					<div class="surelywp-sv-requirement-form-wrap">
						<sc-form class="surelywp-sv-requirement-form">
							<?php wp_nonce_field( 'surelywp_sv_req_form_action', 'surelywp_sv_req_form_submit_nonce' ); ?>
							<input type="hidden" id="surelywp-sv-service-id" name="service_id" value="<?php echo esc_attr( $surelywp_model->surelywp_escape_attr( $service_id ) ); ?>">
							<input type="hidden" id="surelywp-sv-service-setting-id" name="service_setting_id" value="<?php echo esc_attr( $surelywp_model->surelywp_escape_attr( $service_setting_id ) ); ?>">
							<?php
							foreach ( $req_fields as $key => $field ) {

								$serialized_req_data = serialize( $field );
								$is_required_field   = isset( $field['is_required_field'] ) && '1' === $field['is_required_field'] ? 'required' : '';
								if ( 'text' === $field['req_field_type'] ) {
									?>
										<div class="sv-requirements">
											<sc-form-control help="true" label="<?php echo esc_html( $field['req_title'] ); ?>" <?php echo esc_attr( $is_required_field ); ?>>
												<span slot="help-text"><?php echo wp_kses_post( $field['req_desc'] ); ?></span>
												<input type="text" class="service-requirement-text" id="sv-requirement-<?php echo esc_attr( $key ); ?>"  name="service_requirement_text[<?php echo esc_attr( $key ); ?>]" >
											</sc-form-control>
										</div>
									<?php
								} elseif ( 'textarea' === $field['req_field_type'] ) {
									?>
									<div class="sv-requirements">
											<sc-form-control help="true" label="<?php echo esc_html( $field['req_title'] ); ?>" <?php echo esc_attr( $is_required_field ); ?>>
											<span slot="help-text"><?php echo wp_kses_post( $field['req_desc'] ); ?></span>
											<?php
											if ( isset( $field['is_enable_rich_text_editor'] ) ) {
												// Add the TinyMCE editor script.
												wp_editor(
													'', // Initial content, you can fetch saved content here.
													'sv-requirement-' . $key, // Editor ID, must be unique.
													array(
														'textarea_name' => 'service_requirement[' . $key . ']', // Name attribute of the textarea.
														'editor_class' => 'service-requirement-desc',
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
											} else {
												?>
												<textarea class="service-requirement-desc normal" id="sv-requirement-<?php echo esc_attr( $key ); ?>"  name="service_requirement[<?php echo esc_attr( $key ); ?>]"></textarea>
											<?php } ?>
										</sc-form-control>
									</div>
									<?php
								} elseif ( 'file' === $field['req_field_type'] ) {
									?>
									<div class="service-req-attachment">
										<sc-form-control help="true" label="<?php echo esc_html( $field['req_title'] ); ?>" <?php echo esc_attr( $is_required_field ); ?>>
										<span slot="help-text"><?php echo wp_kses_post( $field['req_desc'] ); ?></span>
											<?php
											// file upload max size.
											$file_size = Surelywp_Services::get_sv_gen_option( 'file_size' );
											if ( empty( $file_size ) ) {
												$file_size = '5';
											}
											?>
											<input type="file" class="service-req-file service-req-filepond" name="service_requirement[<?php echo esc_html( $key ); ?>]" multiple  data-max-file-size="<?php echo esc_attr( $file_size . 'MB' ); ?>" data-max-files="20" <?php echo esc_attr( $is_required_field ); ?> >
										</sc-form-control>
									</div>
								<?php } elseif ( 'dropdown' === $field['req_field_type'] ) { ?>
								<div class="sv-requirements">
									<sc-form-control help="true" label="<?php echo esc_html( $field['req_title'] ); ?>" <?php echo esc_attr( $is_required_field ); ?>>
										<span slot="help-text"><?php echo wp_kses_post( $field['req_desc'] ); ?></span>
										<select class="service-requirement-dropdown" id="sv-requirement-<?php echo esc_attr( $key ); ?>" name="service_requirement_dropdown[<?php echo esc_attr( $key ); ?>]" <?php echo esc_attr( $is_required_field ); ?>>
											<option value=""><?php esc_html_e( 'Select an option', 'surelywp-services' ); ?></option>
											<?php
											if ( isset( $field['req_field_dropdown_options'] ) && is_array( $field['req_field_dropdown_options'] ) ) {
												foreach ( $field['req_field_dropdown_options'] as $option ) {
													?>
													<option value="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $option ); ?></option>
													<?php
												}
											}
											?>
										</select>
									</sc-form-control>
								</div>
							<?php } ?>
								<sc-input type="hidden" hidden="true" class="service-requirement-data hidden" name="service_requirement_data[<?php echo esc_html( $key ); ?>]" value="<?php echo esc_html( $serialized_req_data ); ?>"></sc-input>
							<?php } ?>	
							<sc-button id="surelywp-sv-requirement-form-btn" type="primary" submit="true"><?php esc_html_e( 'Submit', 'surelywp-services' ); ?></sc-button>
						</sc-form>
					</div>
				<?php } else { ?>
					<p class="no-found"><?php echo esc_html__( 'You haven\'t provide any requirements', 'surelywp-services' ); ?></p>
				<?php } ?>
		</div>
	</div>
</div>