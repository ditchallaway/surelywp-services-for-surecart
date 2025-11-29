<?php
/**
 * Services all tabs.
 *
 * @package Services For SureCart
 * @author  SurelyWP
 * @version 1.0.0
 */

?>
<div class="services-tabs">
	<ul>
		<li class="messages <?php echo 'messages' === $service_tab ? 'active' : ''; ?>">
			<a href="javascript:void(0)"><?php esc_html_e( 'Messages', 'surelywp-services' ); ?></a>
		</li>
		<li class="activity <?php echo 'activity' === $service_tab ? 'active' : ''; ?>">
			<a href="javascript:void(0)"><?php esc_html_e( 'Activity', 'surelywp-services' ); ?></a>
		</li>
		<?php
		if ( ! empty( $ask_for_contract ) || ! empty( $is_have_contract ) ) {
			?>
			<li class="contract <?php echo 'contract' === $service_tab ? 'active' : ''; ?>">
				<a href="javascript:void(0)"><?php esc_html_e( 'Contract', 'surelywp-services' ); ?></a>
			</li>
		<?php } ?>
		<?php if ( ! empty( $ask_for_requirements ) || ! empty( $is_have_requirement ) ) { ?>
			<li class="requirements <?php echo 'requirements' === $service_tab ? 'active' : ''; ?>">
				<a href="javascript:void(0)"><?php esc_html_e( 'Requirements', 'surelywp-services' ); ?></a>
			</li>
		<?php } ?>
		<li class="delivery <?php echo 'delivery' === $service_tab ? 'active' : ''; ?>">
			<a href="javascript:void(0)"><?php esc_html_e( 'Delivery', 'surelywp-services' ); ?></a>
		</li>
	</ul>
</div>