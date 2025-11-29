<?php
/**
 * Activity Tab.
 *
 * @package Services For SureCart
 * @author  SurelyWP
 * @version 1.0.0
 */

?>
<div class="services-activity-wrap service-tab <?php echo 'activity' === $service_tab ? '' : 'hidden'; ?>">
	<div class="heading">
		<sc-stacked-list>
			<sc-stacked-list-row><?php esc_html_e( 'Activity', 'surelywp-services' ); ?></sc-stacked-list-row>
		</sc-stacked-list>
	</div>
	<div class="services-activity-card tab-card">
		<div class="surelywp-sv-activity" id="surelywp-sv-activity">
			<?php
			$activities_data = $surelywp_sv_model->surelywp_sv_get_service_activities( $service_id );
			$activities      = $activities_data['activities']??'';

			if ( ! empty( $activities ) ) {
				foreach ( $activities as $activity ) {
					$activity_id         = $activity->activity_id ?? '';
					$activity_type       = $activity->activity_type ?? '';
					$activity_info       = $activity->activity_info ?? '';
					$activity_created_at = $activity->created_at ?? '';
					$milestone_name      = '';
					$milestone_status    = '';

					if ( preg_match( '/^(.*)_(\d+)$/', $activity_type, $matches ) ) {
						$milestone_status = $matches[1]; 
						$milestone_id     = $matches[2];
						$milestone_name   = $milestones_fields[$milestone_id]['milestones_field_label'];
					}

					if( $milestone_status == 'milestone_submit' ){
						$activity_type = 'milestone_submit';
					} elseif ( $milestone_status == 'milestone_complete' ) {
						$activity_type = 'milestone_complete';
					} elseif ( $milestone_status == 'delivery_accept_milestone' ) {
						$activity_type = 'delivery_accept_milestone';
					}
					$activities_desc     = $services_obj->surelywp_sv_get_activity_desc( $activity_type );

					?>
					<sc-stacked-list>
						<sc-stacked-list-row>
							<div class="row-content">
								<?php

								// Set icon and square bg color class.
								switch ( $activity_type ) {

									case 'rc_service_created':
										$activity_image_class = 'rc-service-created';
										$square_image_name    = 'service-created.svg';
										break;
									case 'service_created':
										$activity_image_class = 'service-created';
										$square_image_name    = 'service-created.svg';
										break;
									case 'service_contract_signed':
										$activity_image_class = 'service-contract-signed';
										$square_image_name    = 'service-contract-signed.svg';
										break;
									case 'service_req_received':
										$activity_image_class = 'service-req-received';
										$square_image_name    = 'service-req-received.svg';
										break;
									case 'service_start':
										$activity_image_class = 'service-start';
										$square_image_name    = 'service-start.svg';
										break;
									case 'delivery_date_change':
										$activity_image_class = 'delivery-date-change';
										$square_image_name    = 'delivery-date-change.svg';
										break;
									case 'milestone_submit':
										$activity_image_class = 'service-start';
										$square_image_name    = 'milestone-send.svg';
										break;
									case 'delivery_send':
										$activity_image_class = 'delivery-send';
										$square_image_name    = 'delivery-send.svg';
										break;
									case 'delivery_reject':
										$activity_image_class = 'delivery-reject';
										$square_image_name    = 'delivery-reject.svg';
										break;
									case 'delivery_accept':
										$activity_image_class = 'delivery-accept';
										$square_image_name    = 'delivery-accept.svg';
										break;
									case 'delivery_accept_milestone':
										$activity_image_class = 'delivery-accept';
										$square_image_name    = 'delivery-accept.svg';
										break;
									case 'service_complete':
										$activity_image_class = 'service-complete';
										$square_image_name    = 'service-complete.svg';
										break;
									case 'service_auto_completed':
										$activity_image_class = 'service-auto-completed';
										$square_image_name    = 'service-complete.svg';
										break;
									case 'service_complete_by_admin':
										$activity_image_class = 'service-complete-by-admin';
										$square_image_name    = 'service-complete.svg';
										break;
									case 'service_canceled':
										$activity_image_class = 'service-canceled';
										$square_image_name    = 'service-canceled.svg';
										break;
								}
								?>
								<div class="image <?php echo esc_html( $activity_image_class ); ?>"><img src="<?php echo esc_url( SURELYWP_SERVICES_URL . '/assets/images/' . $square_image_name ); ?>" alt="square"></div>
								<?php
								if( $activity_type == 'milestone_submit' || $activity_type == 'milestone_complete' || $activity_type == 'delivery_accept_milestone' ){ 
									$activities_desc = sprintf(
										esc_html__( $activities_desc, 'surelywp-services' ),
										esc_html( $milestone_name )
									);
									?>
									<div class="desc"><?php echo esc_html( $activities_desc ); ?></div>
									<?php
								} else { ?>
									<div class="desc"><?php echo esc_html( $activities_desc ); ?></div>
									<?php
								} ?>

								<div class="time"><?php echo esc_html( wp_date( 'M d, h:i A', strtotime( $activity_created_at ) ) ); ?></div>
								<?php if ( 'service_req_received' === $activity_type && '0' !== $activity_info ) { ?>
									<div class="service-requirements">
										<a href="javascript:void(0)" id="service-requirements-view"><?php echo esc_html__( 'View Requirements', 'surelywp-services' ); ?></a>
									</div>
								<?php } ?>
							</div>
						</sc-stacked-list-row>
					</sc-stacked-list>
					<?php
				}
			}
			?>
		</div>
	</div>
</div>