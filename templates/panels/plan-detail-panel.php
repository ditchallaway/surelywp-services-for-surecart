<?php
/**
 * Service Order Plan Detail panel.
 *
 * @package Services For SureCart
 * @author  SurelyWP
 * @version 1.5
 */

global $surelywp_sv_model;

$sv_status  = false;
$rc_service = array();
if ( $recurring_service_id ) {
	$rc_service = $surelywp_sv_model->surelywp_sv_get_rc_service( $recurring_service_id );
	if ( $rc_service ) {
		$current_time   = current_time( 'mysql' );
		$next_update_on = $rc_service['next_update_on'] ?? '';
		if ( $current_time >= $next_update_on ) {
			$rc_service = Surelywp_Services()->surelywp_sv_update_recurring_service_quota( $rc_service );
		}
		$sv_status          = $rc_service['status'];
		$services_remaining = $rc_service['quota'];
	}
} else {

	$number_of_allow_sv_per_order = Surelywp_Services::get_sv_option( $service_setting_id, 'number_of_allow_sv_per_order' );
	if ( 1 === intval( $number_of_allow_sv_per_order ) && 0 === intval( $services_remaining ) ) {
		return '';
	}
	$sv_status = 'service_canceled' === $service_status ? false : true;
}
?>
<div class="service-order-detail-panel order-plan-detail">
	<div class="tab-card">
		<sc-stacked-list>
			<sc-stacked-list-row>
				<sc-columns>
					<sc-column>
						<p class="order-heading"><?php echo esc_html__( 'Plan Details', 'surelywp-services' ); ?></p>
					</sc-column>
				</sc-columns>
			</sc-stacked-list-row>
		</sc-stacked-list>
	</div>
	<div class="order-track-list tab-card">
		<div class="order-track-list-wrap">
			<?php if ( $sv_status ) { ?>
				<sc-columns>
					<sc-column><p class="label remaining-sv">
						<?php
							// translators: %s is the plural name of the services.
							printf( esc_html__( 'Remaining %s:', 'surelywp-services' ), esc_html( $service_plural_name ) );
						?>
						</p></sc-column>
					<sc-column><p class="description"><?php echo esc_html( $services_remaining ); ?></p></sc-column>
				</sc-columns>
				<?php if ( $recurring_service_id && isset( $rc_service['next_update_on'] ) ) { ?>
					<sc-columns>
						<sc-column><p class="label"><?php esc_html_e( 'Next Renewal Date:', 'surelywp-services' ); ?></p></sc-column>
						<?php $next_update_on = isset( $rc_service['next_update_on'] ) && ! empty( $rc_service['next_update_on'] ) ? wp_date( get_option( 'date_format' ), strtotime( $rc_service['next_update_on'] ) ) : ''; ?>
						<sc-column><p class="description"><?php echo esc_html( $next_update_on ); ?></p></sc-column>
					</sc-columns>
				<?php } ?>
			<?php } else { ?>
				<sc-columns>
					<sc-column><p class="label"><?php esc_html_e( 'Status:', 'surelywp-services' ); ?></p></sc-column>
					<sc-column><p class="description"><sc-tag type="danger"><?php esc_html_e( 'Canceled', 'surelywp-services' ); ?></sc-tag></p></sc-column>
				</sc-columns>
			<?php } ?>
		</div>
	</div>
</div>