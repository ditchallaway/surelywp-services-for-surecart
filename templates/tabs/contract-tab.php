<?php
/**
 * Contract Tab.
 *
 * @package Services For SureCart
 * @author  SurelyWP
 * @version 1.1
 */

global $surelywp_sv_model;

$contract = $surelywp_sv_model->surelywp_sv_get_service_contract( $service_id );

?>
<div class="services-contract-wrap service-tab <?php echo 'contract' === $service_tab ? '' : 'hidden'; ?>">
	<div class="heading" no-padding>
		<sc-stacked-list>
			<sc-stacked-list-row><?php printf( esc_html__( 'Contract', 'surelywp-services' ) ); ?></sc-stacked-list-row>
		</sc-stacked-list>
	</div>
	<div class="services-contract-card tab-card" no-padding>
		<div class="surelywp-sv-contract" id="surelywp-sv-contract">
		<?php
		if ( ! empty( $contract ) ) {
			$contract_id      = $contract[0]->contract_id ?? '';
			$contract_details = $contract[0]->contract_details ?? '';
			$signature        = $contract[0]->signature ?? '';
			$created_at       = $contract[0]->created_at ?? '';
			?>
			<div class="contract-details">
				<div class="contract-content">
					<?php echo wp_kses_post( nl2br( $contract_details ) ); ?>
				</div>
				<sc-divider></sc-divider>
				<div class="signature">
					<?php
					$is_user_service_provider = Surelywp_Services::surelywp_sv_is_user_service_provider();
					$prefix_text              = $is_user_service_provider ? esc_html__( 'Customer', 'surelywp-services' ) : esc_html__( 'Your', 'surelywp-services' );
					?>
					<div class="signatute-text"><?php echo esc_html( $signature ); ?></div>
					<div class="signatute-info">
						<?php
							// translators: %s is the prefix text.
							printf( esc_html__( '%s Signature', 'surelywp-services' ), esc_html( $prefix_text ) );
						?>
					</div>
				</div>
			</div>
		<?php } else { ?>
			<p class="no-found"><?php echo esc_html__( 'No contract submission was found.', 'surelywp-services' ); ?></p>
		<?php } ?>
		</div>
	</div>
</div>