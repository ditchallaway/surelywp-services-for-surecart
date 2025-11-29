<?php
/**
 * Delivery Tab.
 *
 * @package Services For SureCart
 * @author  SurelyWP
 * @version 1.0.0
 */

global $surelywp_sv_model;

$final_deliveries = $surelywp_sv_model->surelywp_sv_get_final_deliveries( $service_id );
if ( ! empty( $final_deliveries ) ) {
	foreach ( $final_deliveries as $key => $delivery ) {
		$message_id            = $delivery->message_id ?? '';
		$sender_id             = $delivery->sender_id ?? '';
		$receiver_id           = $delivery->receiver_id ?? '';
		$message_text          = $delivery->message_text ?? '';
		$attachment_file_names = $delivery->attachment_file_name ?? '';
		$is_approved_delivery  = $delivery->is_approved_delivery ?? '';
		$message_time          = isset( $delivery->created_at ) && ! empty( $delivery->created_at ) ? wp_date( 'M d, h:i A', strtotime( $delivery->created_at ) ) : '';
		$updated_at            = $delivery->updated_at ?? '';
		$sender                = get_userdata( $sender_id );
		$sender_username       = '';
		$sender_img            = '';
		if ( ! empty( $sender ) ) {

			$sender_username = $sender->display_name ?? '';
			$sender_img      = get_avatar_url( $sender->user_email );
		}
		?>
		<div class="services-delivery-wrap service-tab <?php echo 'delivery' === $service_tab ? '' : 'hidden'; ?>">
			<div class="heading" no-padding>
				<sc-stacked-list>
					<sc-stacked-list-row>
						<?php
							// translators: %1$s is the delivery number.
							printf( esc_html__( 'Delivery #%1$s', 'surelywp-services' ), esc_html( $key + 1 ) );
						?>
					</sc-stacked-list-row>
				</sc-stacked-list>
			</div>
			<div class="services-delivery-card tab-card" no-padding>
				<div class="surelywp-sv-delivery">
				<div class="user-img">
					<img src="<?php echo esc_url( $sender_img ); ?>" alt="user">
				</div>
				<div class="surelywp-sv-msg-content">
					<div class="user">
						<p class="user-name"><?php echo esc_html( $sender_username ); ?></p>
						<p class="datetime"><?php echo esc_html( $message_time ); ?></p>
					</div>
					<div class="msg">
						<p class="msg-text"><?php echo wp_kses_post( make_clickable( $message_text ) ); ?></p>
					</div>
					<?php
					if ( ! empty( $attachment_file_names ) ) {

						$attachment_file_names = json_decode( $attachment_file_names, true );
						?>
						<div class="attachetment">
						<h3><?php echo esc_html__( 'ATTACHMENTS', 'surelywp-services' ); ?></h3>
						<div class="attachetment-inner">
						<?php
						foreach ( $attachment_file_names as $attachment_file_name ) {

							$paths             = $services_obj->surelywp_sv_get_msg_attachment_path( $service_id );
							$folder_url        = $paths['folder_url'];
							$folder_path       = $paths['folder_path'];
							$file_url          = $folder_url . '/' . $attachment_file_name;
							$file_path         = $folder_path . '/' . $attachment_file_name;
							$display_file_name = preg_replace( '/-\d+(?=\.[^.]+$)/', '', $attachment_file_name );
							$file_info         = Surelywp_Services::surelywp_sv_get_file_info( $file_url, $file_path );
							$image_extension   = Surelywp_Services::surelywp_sv_get_image_extensions();
							$extension         = $file_info['extension'] ?? '';
							$file_size         = $file_info['size'] ?? '';

							?>
								<div class="attachetment-inner-wrap">
									<div class="attachetment-img">
										<?php
										$attachment_img_url  = '';
										$lightbox_attributes = '';
										if ( $extension && in_array( $extension, $image_extension ) ) {
											$attachment_img_url  = $file_url;
											$lightbox_attributes = 'data-lightbox="delivery-images-' . $message_id . '" data-title="' . esc_attr( $display_file_name ) . '"';
										} else {
											$attachment_img_url = SURELYWP_SERVICES_ASSETS_URL . '/images/file-pre.png';
										}
										?>
										<a href="<?php echo esc_html( $file_url ); ?>" download="<?php echo esc_attr( $display_file_name ); ?>" <?php echo esc_attr( $lightbox_attributes ); ?>>
											<img src="<?php echo esc_url( $attachment_img_url ); ?>" alt="attachetment">
										</a>
									</div>
									<div class="attachetment-title">
										<div class="title">
											<?php printf( esc_html( '%1$s (%2$s)', 'surelywp-services' ), esc_html( $display_file_name ), esc_html( $file_size ) ); ?>
										</div>
										<div class="attachetment-buttons">
											<a href="<?php echo esc_url( $file_url ); ?>" download="<?php echo esc_attr( $display_file_name ); ?>"><img class="download-arrow" src="<?php echo esc_url( SURELYWP_SERVICES_ASSETS_URL . '/images/download-arrow.svg' ); ?>" alt="download"></a>
										</div>
									</div>
								</div>
							<?php } ?>
							</div>
						</div>
					<?php } ?>
				</div>
				</div>
			</div>
		</div>
		<?php
	}
} else {

	$current_user_id  = get_current_user_id();
	$access_users_ids = Surelywp_Services::get_sv_access_users_ids();

	?>
	<div class="services-delivery-wrap service-tab <?php echo 'delivery' === $service_tab ? '' : 'hidden'; ?>">
			<div class="heading" no-padding>
				<sc-stacked-list>
					<sc-stacked-list-row><?php echo esc_html__( 'Delivery', 'surelywp-services' ); ?></sc-stacked-list-row>
				</sc-stacked-list>
			</div>
			<div class="services-delivery-card" no-padding>
				<div class="surelywp-sv-delivery">
					<?php if ( ! empty( $access_users_ids ) && in_array( $current_user_id, (array) $access_users_ids ) ) { ?>
						<p class="no-found"><?php echo esc_html__( 'You haven\'t sent any deliveries yet.', 'surelywp-services' ); ?></p>
					<?php } else { ?>
						<p class="no-found"><?php echo esc_html__( 'You haven\'t received any deliveries yet.', 'surelywp-services' ); ?></p>
					<?php } ?>
				</div>
			</div>
		</div>
<?php } ?>