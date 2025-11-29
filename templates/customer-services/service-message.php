<?php
/**
 * Service Message tab mesaage.
 *
 * @package Services For SureCart
 * @author  SurelyWP
 * @version 1.0.0
 */

$message_service_status = $message->service_status;
$message_milestone_id   = $message->milestone_id;
if ( $is_final_delivery ) {

	if ( 'service_submit' == $message_service_status ) {
		// translators: %1$s: sender username or 'you' if current user is sender.
		$final_delivery_msg = sprintf( esc_html__( 'The final delivery has been sent by %1$s.', 'surelywp-services' ), ( $current_user_id === (int) $sender_id ? 'you' : $sender_username ) );
		?>
		<div class="delivery-message top">
			<sc-alert class="msg-alert" open
				type="info"><strong><?php echo esc_html( $final_delivery_msg ); ?></strong></sc-alert>
		</div>
		<?php
	} else {
		if ( ! empty( $service_setting_id ) ) {
			$milestone_data = Surelywp_Services::surelywp_sv_get_milestone_details_by_id( $message->milestone_id, $service_setting_id );
		}

		if ( ! empty( $milestone_data ) ) {
			$milestones_field_label = $milestone_data['milestones_field_label'];
			$status_only            = $message_service_status;
			if ( 'milestone_submit' === $status_only ) {
				// translators: %1$s: sender username or 'you' if current user is sender.
				$milestone_delivery_msg = sprintf( esc_html__( 'The %1$s delivery has been sent by %2$s.', 'surelywp-services' ), $milestones_field_label, ( $current_user_id === (int) $sender_id ? 'you' : $sender_username ) );
				?>
				<div class="delivery-message top">
					<sc-alert class="msg-alert" open
						type="info"><strong><?php echo esc_html( $milestone_delivery_msg ); ?></strong></sc-alert>
				</div>
				<?php
			}
		}
	}
}
?>
<div class="surelywp-sv-msg <?php echo esc_html( $message_class ); ?>"
	data-is-final-delivery="<?php echo esc_html( $is_final_delivery ); ?>"
	data-message-id="<?php echo esc_html( $message_id ); ?>">
	<div class="user-img">
		<img src="<?php echo esc_url( $sender_img ); ?>" alt="user">
	</div>
	<div class="surelywp-sv-msg-content">
		<div class="user">
			<p class="user-name"><?php echo esc_html( $sender_username ); ?></p>
			<p class="datetime"><?php echo esc_html( $message_time ); ?></p>
			<p class="hidden complete-datetime"><?php echo esc_html( $complete_message_time ); ?></p>
		</div>
		<div class="msg">
			<p class="msg-text"><?php echo wp_kses_post( make_clickable( $message_text ) ); ?></p>
		</div>
		<?php
		if ( ! empty( $file_names ) ) {
			?>
			<div class="attachetment-inner <?php echo esc_html( $message_class ); ?>">
				<?php
				$file_names = json_decode( $file_names, true );
				foreach ( $file_names as $file_name ) {

					// if attachment available.
					$file_url          = '';
					$paths             = $services_obj->surelywp_sv_get_msg_attachment_path( $service_id );
					$folder_url        = $paths['folder_url'] ?? '';
					$folder_path       = $paths['folder_path'] ?? '';
					$file_url          = $folder_url . '/' . $file_name;
					$file_path         = $folder_path . '/' . $file_name;
					$display_file_name = preg_replace( '/-\d+(?=\.[^.]+$)/', '', $file_name );
					$download_url      = $file_url;

					$file_info       = Surelywp_Services::surelywp_sv_get_file_info( $file_url, $file_path );
					$image_extension = Surelywp_Services::surelywp_sv_get_image_extensions();
					$extension       = $file_info['extension'] ?? '';
					$file_size       = $file_info['size'] ?? '';
					?>
					<div class="attachetment-inner-wrap">
						<div class="attachetment-img">
							<?php
							$attachment_img_url  = '';
							$lightbox_attributes = '';
							if ( $extension && in_array( $extension, $image_extension ) ) {
								$attachment_img_url  = $file_url;
								$lightbox_attributes = 'data-lightbox="message-images-' . $message_id . '" data-title="' . esc_attr( $display_file_name ) . '"';
							} else {
								$attachment_img_url = SURELYWP_SERVICES_ASSETS_URL . '/images/file-pre.png';
							}
							?>
							<a href="<?php echo esc_html( $file_url ); ?>" download="<?php echo esc_attr( $display_file_name ); ?>"
								<?php echo esc_attr( $lightbox_attributes ); ?>>
								<img src="<?php echo esc_url( $attachment_img_url ); ?>" alt="attachetment">
							</a>
						</div>
						<div class="attachetment-title">
							<div class="title">
								<?php printf( esc_html( '%1$s (%2$s)', 'surelywp-services' ), esc_html( $display_file_name ), esc_html( $file_size ) ); ?>
							</div>
							<div class="attachetment-buttons">
								<a href="<?php echo esc_url( $file_url ); ?>"
									download="<?php echo esc_attr( $display_file_name ); ?>"><img class="download-arrow"
										src="<?php echo esc_url( SURELYWP_SERVICES_ASSETS_URL . '/images/download-arrow.svg' ); ?>"
										alt="download"></a>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
</div>
<?php
// Messages for revision request.
if ( $is_final_delivery && '0' === $is_approved_delivery ) {
	$status_only = $message_service_status;
	if ( 'milestone_submit' === $status_only ) {
		$current_milestones_id = $message_milestone_id;
		if ( $current_user_id === (int) $sender_id ) {
			$declined_msg = sprintf( esc_html__( 'The customer has requested revisions of %1$s.', 'surelywp-services' ), $milestones_fields[ $current_milestones_id ]['milestones_field_label'] );
		} else {
			$declined_msg = sprintf( esc_html__( 'You requested revisions of %1$s.', 'surelywp-services' ), $milestones_fields[ $current_milestones_id ]['milestones_field_label'] );
		}
			?>
			<div class="delivery-message bottom declined">
				<sc-alert class="msg-alert" open type="danger"><strong><?php echo esc_html( $declined_msg ); ?></strong></sc-alert>
			</div>
			<?php
	} else {
		if ( $current_user_id === (int) $sender_id ) {
			$declined_msg = esc_html__( 'The customer has requested revisions.', 'surelywp-services' );
		} else {
			$declined_msg = esc_html__( 'You requested revisions.', 'surelywp-services' );
		}
		?>
		<div class="delivery-message bottom declined">
			<sc-alert class="msg-alert" open type="danger"><strong><?php echo esc_html( $declined_msg ); ?></strong></sc-alert>
		</div>
		<?php
	}
} elseif ( $is_final_delivery && '1' === $is_approved_delivery ) { // Messages for approved delivery.

	// Prevent duplicate final delivery message
	static $final_delivery_displayed = false;
	if ( 'service_submit' === $message_service_status && ! $final_delivery_displayed ) {
		// Final delivery message (only once)
		$final_delivery_displayed = true;
		if ( $current_user_id === (int) $sender_id ) {
			$msg = esc_html__( 'The customer has accepted the final delivery.', 'surelywp-services' );
		} else {
			$msg = esc_html__( 'You have accepted the final delivery.', 'surelywp-services' );
		}
		?>
		<div class="delivery-message bottom accepted">
			<sc-alert class="msg-alert" open type="success"><strong><?php echo esc_html( $msg ); ?></strong></sc-alert>
		</div>
		<?php
	} elseif ( 'service_complete' !== $message_service_status ) {

		$status_only           = $message_service_status;
		$current_milestones_id = $message_milestone_id;

		// Show flags for each milestone's status.
		switch ( $status_only ) {
			case 'milestone_submit':
				$milestone_status = $surelywp_sv_model->get_milestone_status_by_id( $message_milestone_id );

				if ( $milestone_status ) {
					// Admin submitted milestone delivery.
					if ( $current_user_id === (int) $sender_id ) {
						$msg = sprintf(
							esc_html__( 'The customer has accepted the %1$s delivery.', 'surelywp-services' ),
							$milestones_fields[ $current_milestones_id ]['milestones_field_label']
						);
					} else {
						$msg = sprintf(
							esc_html__( 'You have accepted the %1$s delivery.', 'surelywp-services' ),
							$milestones_fields[ $current_milestones_id ]['milestones_field_label']
						);
					}
					?>
					<div class="delivery-message bottom accepted">
						<sc-alert class="msg-alert" open type="success"><strong><?php echo esc_html( $msg ); ?></strong></sc-alert>
					</div>
					<?php
				}
				break;
		}
	}
} ?>