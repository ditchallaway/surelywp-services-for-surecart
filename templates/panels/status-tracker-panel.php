<?php
/**
 * Service Status tracker panel.
 *
 * @package Services For SureCart
 * @author  SurelyWP
 * @version 1.0.0
 */

?>
<div class="status-tracker">
	<div class="status-tracker-heading">
		<?php esc_html_e( 'Status Tracker', 'surelywp-services' ); ?>
	</div>
	<div class="service-track-list">
		<!-- Order placed is always checked -->
		<sc-text class="hydrated checked">
			<div class="check-circle"></div>
			<div class="service-track-list-item"><?php esc_html_e( 'Order Placed', 'surelywp-services' ); ?> </div>
		</sc-text>

		<?php
		// Detect if current status is any *_service_start milestone.
		$is_service_start = (
			$service_status === 'service_start'
			|| preg_match( '/^service_start_\d+$/', $service_status ) // matches service_start_0, service_start_1, etc.
		);

		$is_milestone_submit = (
			$service_status === 'milestone_submit'
			|| preg_match( '/^milestone_submit_\d+$/', $service_status ) // matches milestone_submit_0, milestone_submit_1, etc.
		);

		$is_milestone_complete = (
			$service_status === 'milestone_complete'
			|| preg_match( '/^milestone_complete_\d+$/', $service_status ) // matches milestone_complete_0, milestone_complete_1, etc.
		);


		// Contract signed step.
		if ( ! empty( $ask_for_contract ) || ! empty( $is_have_contract ) ) {
			$contract_signed_checked = (
				'waiting_for_req' === $service_status
				|| $is_service_start
				|| $is_milestone_submit
				|| 'final_delivery_start' === $service_status
				|| 'service_submit' === $service_status
				|| 'service_complete' === $service_status
				|| 'service_close' === $service_status
			) ? 'checked' : '';
			?>
			<sc-text class="hydrated <?php echo esc_attr( $contract_signed_checked ); ?>">
				<div class="check-circle"></div>
				<div class="service-track-list-item"><?php esc_html_e( 'Contract Signed', 'surelywp-services' ); ?></div>
			</sc-text>
		<?php } ?>

		<?php
		// Requirements submitted step.
		if ( ! empty( $ask_for_requirements ) || ! empty( $is_have_requirement ) ) {
			$req_sub_checked_checked = (
				$is_service_start
				|| $is_milestone_submit
				|| 'final_delivery_start' === $service_status
				|| 'service_submit' === $service_status
				|| 'service_complete' === $service_status
				|| 'service_close' === $service_status
				|| $is_milestone_complete
			) ? 'checked' : '';
			?>
			<sc-text class="hydrated <?php echo esc_attr( $req_sub_checked_checked ); ?>">
				<div class="check-circle"></div>
				<div class="service-track-list-item"><?php esc_html_e( 'Requirements Submitted', 'surelywp-services' ); ?>
				</div>
			</sc-text>
		<?php } ?>

		<?php

		// Milestone steps (Work in Progress phases).
		if ( ! empty( $milestones_fields ) ) {
			foreach ( $milestones_fields as $key => $milestones_field ) {
				$milestone_key   = 'service_start_' . $key;
				$milestone_label = ! empty( $milestones_field['milestones_field_label'] )
					? $milestones_field['milestones_field_label']
					: esc_html__( 'Milestone', 'surelywp-services' );

				preg_match( '/\d+$/', $service_status, $matches );
				$milestone_id = $matches[0] ?? '';
				// Case 1: Already completed milestone (before current one).
				if ( in_array( $service_status, array( 'service_submit', 'service_complete', 'service_close', 'milestone_complete', 'final_delivery_start' ), true ) ) {
					$order_progress_checked = 'checked';
				} elseif ( $milestone_id !== null && $key < $milestone_id ) {
					$order_progress_checked = 'checked';
					$milestone_text         = $milestone_label;
				} elseif ( $milestone_id !== null && $key == $milestone_id && preg_match( '/^service_start_\d+$/', $service_status ) ) {  // Case 2: Current milestone (in progress).
					$order_progress_checked = ''; // Not checked yet.
					$milestone_text         = $milestone_label . ' In Progress';
				} else { // Case 3: Future milestones.
					$order_progress_checked = '';
					$milestone_text         = $milestone_label;
				}
				?>
				<sc-text class="hydrated <?php echo esc_attr( $order_progress_checked ); ?>">
					<div class="check-circle"></div>
					<div class="service-track-list-item">
						<?php echo esc_html( $milestone_label . ' In Progress' ); ?>
					</div>
					<?php
					if ( $milestone_id == $key && ! empty( $delivery_date ) ) {
						echo esc_html__( 'Delivery Date: ', 'surelywp-services' ) . $delivery_date_short;
						echo '<br>';
						if ( ! empty( $revision_allowed['remaining_revision'] ) && ! empty( $revision_allowed['milestones_require_approval'] ) ) {
							echo esc_html__( 'Revisions Remaining: ', 'surelywp-services' ) . $revision_allowed['remaining_revision'];
						} elseif ( $revision_allowed['remaining_revision'] == 0 && $revision_allowed['remaining_revision'] !== null && isset( $revision_allowed['milestones_revision_allowed'] ) ) {
							echo '<p class="sv-revision-limit-msg">' . esc_html__( 'Revisions Limit Reached', 'surelywp-services' ) . '</p>';
						}
					}
					?>
				</sc-text>
				<?php
			}
		} else {
			// Fallback if no milestones.
			$order_progress_checked = (
				'service_submit' === $service_status
				|| 'service_complete' === $service_status
				|| 'service_close' === $service_status
			) ? 'checked' : '';
			?>
			<sc-text class="hydrated <?php echo esc_attr( $order_progress_checked ); ?>">
				<div class="check-circle"></div>
				<div class="service-track-list-item"><?php esc_html_e( 'Work In Progress', 'surelywp-services' ); ?></div>
			</sc-text>
		<?php } ?>

		<?php
		if ( ! empty( $milestones_fields ) ) {
			$last_key = array_key_last( $milestones_fields );
			if ( isset( $milestones_fields[ $last_key ]['milestones_require_approval'] ) ) {
				// Final delivery approved.
				$review_checked = (
					'service_complete' === $service_status
					|| 'service_close' === $service_status
				) ? 'checked' : '';
				?>
				<sc-text class="hydrated <?php echo esc_attr( $review_checked ); ?>">
					<div class="check-circle"></div>
					<div class="service-track-list-item"><?php esc_html_e( 'Final Delivery', 'surelywp-services' ); ?>
					</div>
				</sc-text>
				<?php
			}
		}
		?>

		<?php
		// Order completed.
		$complete_checked = (
			'service_complete' === $service_status
			|| 'service_close' === $service_status
		) ? 'checked' : '';
		?>
		<sc-text class="hydrated <?php echo esc_attr( $complete_checked ); ?>">
			<div class="check-circle"></div>
			<div class="service-track-list-item"><?php esc_html_e( 'Order Completed', 'surelywp-services' ); ?></div>
		</sc-text>
	</div>
</div>