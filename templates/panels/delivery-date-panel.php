<?php
/**
 * Delivery Now and Extend delivery panel.
 *
 * @package Services For SureCart
 * @author  SurelyWP
 * @version 1.0.0
 */

$timestamp = strtotime( $delivery_date_from_db );
?>
<div class="service-delivery-panel" id="service-delivery-panel" data-delivery-date="<?php echo esc_attr( $timestamp ); ?>">
	<div class="card-heading">
		<?php esc_html_e( 'Time Left To Deliver', 'surelywp-services' ); ?>
	</div>
	<div class="service-delivery-card tab-card">
		<div class="timer">
			<div class="days"><span class="count"></span><span class="label"><?php esc_html_e( 'Days', 'surelywp-services' ); ?></span></div>
			<div class="hours"><span class="count"></span><span class="label"><?php esc_html_e( 'Hours', 'surelywp-services' ); ?></span></div>
			<div class="minutes"><span class="count"></span><span class="label"><?php esc_html_e( 'Minutes', 'surelywp-services' ); ?></span></div>
			<div class="seconds"><span class="count"></span><span class="label"><?php esc_html_e( 'Seconds', 'surelywp-services' ); ?></span></div>
		</div>
		<div class="delivery-now">
			<a href="javascript:void(0)" id="delivery-now-button" class=""><?php esc_html_e( 'Deliver Now', 'surelywp-services' ); ?></a>
		</div>
		<div class="extend-delivery">
			<a href="javascript:void(0)" id="extend-delivery-button" class=""><?php esc_html_e( 'Extend Delivery Date', 'surelywp-services' ); ?></a>
		</div>
	</div>
</div>

<!-- Extend Delivery date modal -->
<div class="surelywp-sv-modal">
	<div class="exdent-delivery-date modal">
		<div class="modal-content">
			<span class="close-button">×</span>
			<div class="modal-top">
				<div class="heading"><?php echo esc_html__( 'Extend Delivery Date', 'surelywp-services' ); ?><img class="hidden loader" src="<?php echo esc_url( SURELYWP_CORE_PLUGIN_URL ) . '/assets/images/wp-ajax-loader.gif'; ?>" /></div>
			</div>
			<div class="modal-bottom">
				<div class="delivery-time-wrap">
					<label for="delivery-date"><?php echo esc_html__( 'Select New Delivery Date:', 'surelywp-services' ); ?></label>
					<?php wp_nonce_field( 'surelywp_sv_delivery_change_action', 'surelywp_sv_delivery_change_nonce' ); ?>
					<input type="date" id="change-delivery-time" class="datetime-click-able delivery-date" value="<?php echo esc_attr( date( 'Y-m-d', strtotime( $delivery_date_from_db ) ) ); ?>" min="<?php echo esc_attr( date( 'Y-m-d', time() ) ); ?>">
				</div>
			</div>
			<div class="approve-buttons">
				<a href="javascript:void(0)" id="cancel-delivery-date-change" class="btn-primary button-2"><?php esc_html_e( 'Cancel', 'surelywp-services' ); ?></a>
				<a href="javascript:void(0)" id="confirm-change-delivery-date" class="btn-secondary button-1"><?php esc_html_e( 'Save Changes', 'surelywp-services' ); ?></a>
			</div>
		</div>
	</div>
</div>

<!-- Delivery now modal -->
<div class="surelywp-sv-modal">
	<div class="delivery-now modal">
		<div class="modal-content">
			<span class="close-button">×</span>
			<div class="modal-top">
				<div class="heading"><?php echo esc_html__( 'Send Delivery', 'surelywp-services' ); ?><img class="hidden loader" src="<?php echo esc_url( SURELYWP_CORE_PLUGIN_URL ) . '/assets/images/wp-ajax-loader.gif'; ?>" /></div>
			</div>
			<div class="modal-bottom">
				<div class="delivery-now-wrap">
					<sc-form class="surelywp-sv-delivery-now-form">
						<div class="chat-inputs">
							<?php
								// Add the TinyMCE editor script.
								wp_editor(
									'', // Initial content, you can fetch saved content here.
									'service-message-input-delivery-now', // Editor ID, must be unique.
									array(
										'textarea_name' => 'service_message', // Name attribute of the textarea.
										'editor_class'  => 'service-message-input md-15',
										'textarea_rows' => 4, // Number of rows.
										'media_buttons' => false, // Show media button in the editor.
										'tinymce'       => array(
											'toolbar1' => 'bold,italic,underline,|,bullist,numlist,|,link,|,undo,redo',
											'toolbar2' => '', // Leave empty if you don't want a second toolbar.
											'content_style' => 'body, p, div { font-family: Open Sans, sans-serif; color: #4c5866;}', // Properly escape font-family.
										),
										'quicktags'     => array(
											'buttons' => 'strong,em,link,ul,ol,li,quote',
										),
									)
								);
								?>
							<div class="attachment-file">
								<?php
								// file upload max size.
								$file_size = Surelywp_Services::get_sv_gen_option( 'file_size' );
								if ( empty( $file_size ) ) {
									$file_size = '5';
								}
								?>
								<input type="file" class="delivery-now-filepond" id="delivery-now-attachment-file" name="dn_msg_attachment_file[]" multiple  data-max-file-size="<?php echo esc_attr( $file_size . 'MB' ); ?>" data-max-files="20">
							</div>
						</div>
					</sc-form>
				</div>
			</div>
			<div class="approve-buttons">
				<a href="javascript:void(0)" id="cancel-delivery-now" class="btn-primary button-2"><?php esc_html_e( 'Cancel', 'surelywp-services' ); ?></a>
				<a href="javascript:void(0)" id="confirm-delivery-now" class="btn-secondary button-1"><?php esc_html_e( 'Send Delivery', 'surelywp-services' ); ?></a>
			</div>
		</div>
	</div>
</div>