<?php
/**
 * Email Template for customer requirements mail.
 *
 * @package Services For SureCart
 * @author  SurelyWP
 * @version 1.0.0
 */

global $surelywp_sv_model;
$services_obj = Surelywp_Services();
?>
<br />
<div class="surelywp-sv-requirements">
	<?php
	$requirements = $surelywp_sv_model->surelywp_sv_get_service_requirements( $service_id );
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
				echo 1 !== $requirement_count ? '<br />' : '';
				?>
				<div class="accordion-item">
					<?php if ( ! empty( $requirement_title ) ) { ?>
						<div class="accordion-header">
							<div class="accordion-question"><?php printf( '%02d %s', esc_html( $requirement_count ), esc_html( $requirement_title ) ); ?></div>
						</div>
					<?php } ?>
					<?php if ( ! empty( $requirement ) ) { ?>
						<div class="accordion-content">
							<?php
							if ( 'text' === $requirement_type ) {
								?>
								<div><?php echo esc_html( $requirement ); ?></div>
								<?php
							} elseif ( 'textarea' === $requirement_type ) {
								?>
								<div><?php echo wp_kses_post( $requirement ); ?></div>
								<?php
							} elseif ( 'dropdown' === $requirement_type ) {
								?>
								<div><?php echo wp_kses_post( $requirement ); ?></div>
								<?php
							} elseif ( 'file' === $requirement_type ) {
								$file_names = json_decode( $requirement, true );

								$attachment_div = '';
								foreach ( $file_names as $key => $attachment_file_name ) {

									$file_url        = $folder_url . '/' . $attachment_file_name;
									$file_path       = $folder_path . '/' . $attachment_file_name;
									$file_info       = self::surelywp_sv_get_file_info( $file_url, $file_path );
									$image_extension = self::surelywp_sv_get_image_extensions();
									$extension       = $file_info['extension'] ?? '';

									if ( $extension && in_array( $extension, $image_extension ) ) {
										$attachment_img_url = $file_url;
									} else {
										$attachment_img_url = SURELYWP_SERVICES_ASSETS_URL . '/images/file-pre.png';
									}
									?>
									<div style="width: 200px; border: 1px solid #E5E7EB; min-height: 125px;margin-top: 10px; margin-right: 5px;">
										<img src="<?php echo esc_url( $attachment_img_url ); ?>" alt="attachment-image" style="width: 100%; object-fit: cover;">
										<br>
										<a href="<?php echo esc_url( $file_url ); ?>" download style="padding: 6px 8px; border: 0; border-top: 1px solid #E5E7EB; font-family: 'Open Sans'; font-size: 13px; font-weight: 600; line-height: 20px; color: #6B7280; text-align: center; word-wrap: break-word;"><?php echo esc_html__( 'Download', 'surelywp-services' ); ?></a>
									</div>
									<?php
								}
							}
							?>
						</div>
					<?php } ?>
				</div>
				<?php
				++$requirement_count;
			}
			?>
		</div>
		<?php
	}
	?>
</div>