<?php
/**
 * Customer Contract Form.
 *
 * @package Services For SureCart
 * @author  SurelyWP
 * @version 1.1
 */

?>
<div class="surelywp-sv-contract-form-wrap">
	<sc-form class="surelywp-sv-contract-form">
		<?php wp_nonce_field( 'surelywp_sv_contract_form_action', 'surelywp_sv_contract_form_nonce' ); ?>
		<?php
		$contract_details = Surelywp_Services::get_sv_option( $service_setting_id, 'contract_details' );
		$ds_title         = Surelywp_Services::get_sv_option( $service_setting_id, 'ds_title' );
		$ds_desc          = Surelywp_Services::get_sv_option( $service_setting_id, 'ds_desc' );
		?>
		<div class="contract-details">
			<sc-card>
				<div class="contract-title">
					<?php echo esc_html__( 'Contract', 'surelywp-services' ); ?>
				</div>
				<sc-divider></sc-divider>
				<div class="contract-content">
					<?php echo wp_kses_post( nl2br( $contract_details ) ); ?>
				</div>
				<sc-divider></sc-divider>
				<div class="signature">
					<sc-input class="service-contract-sign" name="signature" label="<?php echo esc_html( $ds_title ); ?>" help="<?php echo esc_html( $ds_desc ); ?>" placeholder="<?php esc_html_e( 'Enter Your Name...', 'surelywp-services' ); ?>" value="" required></sc-input>
					<sc-input type="hidden" class="service-id hidden" name="service_id" value="<?php echo $surelywp_model->surelywp_escape_attr( $service_id ); ?>"></sc-input>
					<sc-input type="hidden" class="contract-details hidden" name="contract_details" value="<?php echo $surelywp_model->surelywp_escape_attr( $contract_details ); ?>"></sc-input>
					<sc-input type="hidden" class="service-setting-id hidden" name="service_setting_id" value="<?php echo $surelywp_model->surelywp_escape_attr( $service_setting_id ); ?>"></sc-input>
				</div>
				<div class="accept-btn">
					<sc-button id="surelywp-sv-contract-form-btn" type="primary" submit="true"><?php esc_html_e( 'Accept', 'surelywp-services' ); ?></sc-button>
				</div>
			</sc-card>
		</div>
	</sc-form>
</div>