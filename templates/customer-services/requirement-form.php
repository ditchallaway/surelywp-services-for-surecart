<?php
/**
 * Service Requirement Form.
 *
 * @author  Surelywp
 * @package Services For SureCart
 * @since 1.4
 */

global $surelywp_sv_model, $surelywp_model, $post;
$req_fields = Surelywp_Services::get_sv_option( $service_setting_id, 'req_fields' );
if ( empty( $req_fields ) ) {
	return '';
}

$is_product_page = false;
if ( isset( $post->post_type ) && 'sc_product' === $post->post_type ) {
	$is_product_page = true;
}
?>
<div class="services-requirements-form external">
	<img class="loader hidden" src="<?php echo esc_url( SURELYWP_CORE_PLUGIN_URL ) . '/assets/images/wp-ajax-loader.gif'; ?>" />
	<sc-form class="surelywp-sv-requirement-form">
		<?php wp_nonce_field( 'surelywp_sv_req_form_action', 'surelywp_sv_req_form_submit_nonce' ); ?>
		<input type="hidden" id="surelywp-sv-service-setting-id" name="service_setting_id" value="<?php echo esc_attr( $surelywp_model->surelywp_escape_attr( $service_setting_id ) ); ?>">
		<input type="hidden" id="surelywp-sv-product-id" name="service_product_id" value="<?php echo esc_attr( $surelywp_model->surelywp_escape_attr( $product_id ) ); ?>">
		<input type="hidden" id="is-external-req-form" name="is_requirement_form" value="1">
		<input type="hidden" id="surelywp-sv-is-product-page" name="is_product_page" value="<?php echo esc_attr( $surelywp_model->surelywp_escape_attr( $is_product_page ) ); ?>">
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
									'editor_class'  => 'service-requirement-desc',
									'textarea_rows' => 5, // Number of rows.
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
				<?php
			} elseif ( 'dropdown' === $field['req_field_type'] ) {
				if ( ! empty( $field['req_field_dropdown_options'] ) && is_array( $field['req_field_dropdown_options'] ) ) {
					?>
					<div class="sv-requirements">
						<sc-form-control help="true" label="<?php echo esc_html( $field['req_title'] ); ?>" <?php echo esc_attr( $is_required_field ); ?>>
							<span slot="help-text"><?php echo wp_kses_post( $field['req_desc'] ); ?></span>
							<select class="service-requirement-dropdown" id="sv-requirement-<?php echo esc_attr( $key ); ?>" name="service_requirement_dropdown[<?php echo esc_attr( $key ); ?>]" <?php echo esc_attr( $is_required_field ); ?>>
								<option value=""><?php esc_html_e( 'Select an option', 'surelywp-services' ); ?></option>
								<?php
								foreach ( $field['req_field_dropdown_options'] as $option ) {
									if ( ! empty( $option ) ) {
										?>
										<option value="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $option ); ?></option>
										<?php
									}
								}
								?>
							</select>
						</sc-form-control>
					</div>
					<?php
				}
			}
			?>
			<sc-input type="hidden" hidden="true" class="service-requirement-data hidden" name="service_requirement_data[<?php echo esc_html( $key ); ?>]" value="<?php echo esc_html( $serialized_req_data ); ?>"></sc-input>
		<?php } ?>
		<?php if ( ! $is_product_page ) { ?>
			<sc-button id="surelywp-sv-requirement-form-btn" type="primary" submit="true"><?php esc_html_e( 'Submit', 'surelywp-services' ); ?></sc-button>
		<?php } ?>
	</sc-form>
</div>