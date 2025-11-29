<?php
/**
 * Display Product Requirements.
 *
 * @package Services For SureCart
 * @author  SurelyWP
 * @version 1.5
 */

use SureCart\Models\Product;

global $surelywp_model;
?>

<div class="surelywp-sv-product-requirements services-requirements-wrap">
	<?php
	$user_id = get_current_user_id();
	if ( is_user_logged_in() ) {
		if ( isset( $_COOKIE['surelywp_guest_id'] ) && ! empty( $_COOKIE['surelywp_guest_id'] ) ) {
			$guest_id      = sanitize_text_field( wp_unslash( $_COOKIE['surelywp_guest_id'] ) );
			$transient_key = 'surelywp_sv_product_requirements_guest_' . $guest_id;
		} else {
			$transient_key = 'surelywp_sv_product_requirements_user_' . $user_id;
		}
	} elseif ( isset( $_COOKIE['surelywp_guest_id'] ) && ! empty( $_COOKIE['surelywp_guest_id'] ) ) {
		$guest_id      = sanitize_text_field( wp_unslash( $_COOKIE['surelywp_guest_id'] ) );
		$transient_key = 'surelywp_sv_product_requirements_guest_' . $guest_id;
	} else {
		return false;
	}

	$product_requirements = get_transient( $transient_key );
	// $product_requirements = isset( $_SESSION['surelywp_sv_product_requirements'] ) && ! empty( $_SESSION['surelywp_sv_product_requirements'] ) ? $surelywp_model->surelywp_escape_slashes_deep( $_SESSION['surelywp_sv_product_requirements'], true, true ) : '';

	if ( ! empty( $product_requirements ) ) {
		?>
		<div>
			<?php
			foreach ( $product_requirements as $product_id => $requirements ) {
				$service_setting_id = Surelywp_Services::surelywp_sv_get_product_service_setting_id( $product_id );

				$req_fields = Surelywp_Services::get_sv_option( $service_setting_id, 'req_fields' );

				$product_obj = Product::find( $product_id );
				?>
				<div class="sv-product-requirements">
					<div class="sv-product-requirements">
						<div class="requirements surelywp-sv-requirements" style="text-align:left">
							<div class="sv-product accordion">
								<div class="sv-product-name accordion-item">
									<div class="service-product-name accordion-header">
										<div class="accordion-question 1">
											<a class="view-product-link" aria-label="<?php echo esc_attr__( 'View Product', 'surelywp-services' ); ?>" href="<?php echo esc_url( $product_obj->permalink ?? '' ); ?>">
												<p class="product-name">
													<?php
														// translators: %s is the product name.
														echo isset( $product_obj->name ) ? sprintf( esc_html__( '%s Service Requirements', 'surelywp-services' ), esc_html( $product_obj->name ) ) : '';
													?>
												</p>
											</a>
										</div>
									</div>
								</div>
							</div>
							<?php
							if ( ! empty( $requirements ) ) {

								$ordered_requirements = array();
								if ( ! empty( $req_fields ) ) {
									foreach ( $req_fields as $req_field ) {
										$req_title = $req_field['req_title'];
										foreach ( $requirements as $key => $requirement ) {
											if ( isset( $requirement['requirement_title'] ) && $requirement['requirement_title'] === $req_title ) {
												$ordered_requirements[] = $requirement;
												break;
											}
										}
									}
								}

								if ( isset( $requirements['submit_time'] ) ) {

									unset( $requirements['submit_time'] );
								}
								?>
								<div class="accordion">
									<?php
									$requirement_count = 1;
									$paths             = Surelywp_Services::surelywp_sv_get_temp_req_path();
									$folder_url        = $paths['folder_url'];
									$folder_path       = $paths['folder_path'];

									foreach ( $ordered_requirements as $key => $requirement ) {

										$requirement_type  = $requirement['requirement_type'] ?? '';
										$requirement_title = $requirement['requirement_title'] ?? '';
										$requirement_desc  = $requirement['requirement_desc'] ?? '';
										$requirement       = $requirement['requirement'] ?? '';
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

														$req_file_paths = $requirement;
														?>
														<div class="attachetment-inner">
				
														<?php

														foreach ( $req_file_paths as $key => $req_path ) {

															$file_name            = basename( $req_path );
															$requirement_file_url = $folder_url . '/' . $file_name;
															$display_file_name    = preg_replace( '/-\d+(?=\.[^.]+$)/', '', $file_name );
															$file_info            = Surelywp_Services::surelywp_sv_get_file_info( $requirement_file_url, $req_path );
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
																		$lightbox_attributes = 'data-lightbox="requirement-images-' . $product_id . '" data-title="' . esc_attr( $display_file_name ) . '"';
																	} else {
																		$attachment_img_url = SURELYWP_SERVICES_ASSETS_URL . '/images/file-pre.png';
																	}
																	?>
																	<a href="<?php echo esc_html( $requirement_file_url ); ?>" <?php echo esc_attr( $lightbox_attributes ); ?>>
																		<img src="<?php echo esc_url( $attachment_img_url ); ?>" alt="attachetment">
																	</a>
																</div>
																<div class="attachetment-title">
																	<div class="title">
																		<?php
																			// translators: %1$s is the file name, %2$s is the file size.
																			printf( esc_html__( '%1$s (%2$s)', 'surelywp-services' ), esc_html( $display_file_name ), esc_html( $file_size ) );
																		?>
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
								<?php
							}
							?>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
	<?php } ?>
</div>
