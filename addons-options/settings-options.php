<?php
/**
 * Settings options
 *
 * @author  Surelywp
 * @package Services For SureCart
 * @version 1.0.0
 */

// Get Addons all options.
$surelywp_addons_options = get_option( $option_key . '_options' );

$tab_name = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'overview';

$addons_options = ( ! empty( $surelywp_addons_options ) ) ? $surelywp_addons_options : $option_key . '_options';
if ( ! empty( $surelywp_addons_options ) ) {
	$get_current_tab_data = $surelywp_addons_options;
} else {
	$get_current_tab_data = $$addons_options ?? '';
}
if ( ! empty( $get_current_tab_data ) ) {

	$addons_option = $get_current_tab_data[ $option_key . '_options' ];
} else {
	$addons_option = array();
}
?>
<div id="<?php echo esc_attr( $container_id ); ?>" class="surelywp-plugin-fw surelywp-admin-panel-container <?php echo esc_html( $tab_name ); ?> <?php echo ( 'license_key' === $option_key ) ? 'surelywp-licensekey-wrap' : ''; ?>">
	<div class="<?php echo esc_attr( $content_class ); ?>">
		<div class="reset-modal modal">
			<div class="modal-content">
				<span class="close-button">×</span>
				<h4><?php esc_html_e( 'Reset Settings To Default?', 'surelywp-services' ); ?></h4>
				<div class="modal-btn-wrap">
					<div class="text">
						<?php esc_html_e( 'You are about to reset the plugin settings to their default state. Any change you have made will be permanently erased. Are you sure you want to do this?', 'surelywp-services' ); ?>
					</div>
					<form method="post" action="">
						<a href="javascript:void(0)" class="btn-primary button-2 close-modal-button">Cancel</a>
						<input id="surelywp_reset" type="submit" class="button-primary " name="surelywp_ric_settings_reset" class="" value="<?php echo esc_html__( 'Confirm Reset', 'surelywp-services' ); ?>" />
					</form>
				</div>
			</div>
		</div>
		<?php
		$activation        = surelywp_check_license_avtivation( SURELYWP_SERVICES_PLUGIN_TITLE );
		$is_licence_active = ! isset( $activation['sc_activation_id'] ) && empty( $activation ) ? false : true;
		if ( ! $is_licence_active ) {
			?>
			<div class="licence-notice">
				<div class="licence-notice-icon">
					<img src="<?php echo esc_url( SURELYWP_SERVICES_ASSETS_URL . '/images/licence-notice-icon.svg' ); ?>" alt="surelywp">	
				</div>
				<div class="licence-notice-text-wrap">
					<div class="licence-notice-heading"><?php printf( esc_html__( 'Welcome to the %s plugin by SurelyWP!', 'surelywp-services' ), SURELYWP_SERVICES_PLUGIN_TITLE ); // phpcs:ignore?></div>
					<?php
					$licence_page_url = add_query_arg(
						array(
							'page' => $panel->settings['page'] ?? '',
							'tab'  => 'license_key',
						),
						admin_url( 'admin.php' )
					);
					echo '<div class="licence-notice-sub-heading">' .
					sprintf(
						/* translators: %s: Link to license input */
						esc_html__( 'To begin using the plugin, %s', 'surelywp-services' ),
						'<a href="' . esc_url( $licence_page_url ) . '">' . esc_html__( 'please enter your license key.', 'surelywp-services' ) . '</a>',
					) .
					'</div>';
					?>
				</div>
				<div class="licence-notice-close">
					<img src="<?php echo esc_url( SURELYWP_SERVICES_ASSETS_URL . '/images/close-icon.svg' ); ?>" alt="surelywp">
				</div>
			</div>
		<?php } ?>
		<?php if ( 'license_key' !== $option_key ) { ?>
			<form id="services-settings-form" class="cursor-not-allow services-settings" method="post" action="options.php" enctype='multipart/form-data'>
				<div class="surelywp-body-content-header ">
					<div class="surelywp-content-title">
						<h2>
							<?php
							if ( isset( $_GET['action'] ) && ! empty( $_GET['action'] ) ) {
								$back_icon = '<img src="' . esc_url( SURELYWP_SERVICES_URL . 'assets/images/Back.svg' ) . '" alt="back_icon" >';
								if ( isset( $_GET['service_setting_id'] ) && ! empty( $_GET['service_setting_id'] ) ) {
									$service_setting_id = sanitize_text_field( wp_unslash( $_GET['service_setting_id'] ) );
									$option_title       = isset( $addons_option[ $service_setting_id ]['service_title'] ) && ! empty( $addons_option[ $service_setting_id ]['service_title'] ) ? esc_html( $addons_option[ $service_setting_id ]['service_title'] ) : '';
									$back_url           = admin_url( 'admin.php' ) . '?page=surelywp_services_panel&tab=surelywp_sv_settings';
								} elseif ( isset( $_GET['template'] ) && ! empty( $_GET['template'] ) ) {
									$template_key       = sanitize_text_field( wp_unslash( $_GET['template'] ) );
									$sv_email_templates = Surelywp_Services::surelywp_sv_get_all_email_templetes();
									$option_title       = $sv_email_templates[ $template_key ]['name'] ?? '';
									$back_url           = admin_url( 'admin.php' ) . '?page=surelywp_services_panel&tab=surelywp_sv_email_templates';
								}
								echo '<a href="' . esc_url( $back_url ) . '">' . $back_icon . '</a>' . $option_title; //phpcs:ignore
							} elseif ( isset( $_GET['tab'] ) && 'surelywp_import_export' === sanitize_text_field( $_GET['tab'] ) ) {
								esc_html_e( 'Import/Export Settings', 'surelywp-services' );
							} else {
								echo esc_html( $tab_title['title'] );
							}
							?>
						</h2>
					</div>
					<div class="header-button-wrap">
						<?php if ( ( 'surelywp_addons_settings' !== $option_key && isset( $_GET['action'] ) && ! empty( $_GET['action'] ) ) || 'surelywp_sv_gen_settings' === $option_key ) { ?>
							<?php if ( 'surelywp_sv_settings' !== $option_key ) { ?>
								<input type="hidden" id="is-licence-active" name="is_licence_active" value="<?php echo esc_html( $is_licence_active ); ?>" />
							<?php } ?>
							<div class="surelywp-options-reset">
								<a href="javascript:void(0)" class="surelywp-ric-settings-reset reset-trigger">
									<img id="surelywp-er-reset-settings" src="<?php echo esc_url( SURELYWP_CORE_PLUGIN_URL . '/assets/images/reset.svg' ); ?>">
									<?php esc_html_e( 'Reset Settings', 'surelywp-services' ); ?>
								</a>
							</div>
							<div class="surelywp-options-save">
								<input id="surelywp_save" type="submit" class="button-primary surelywp-ric-settings-save" name="surelywp_ric_settings_save" value="<?php esc_html_e( 'Save Changes', 'surelywp-services' ); ?>" />
							</div>
						<?php } ?>
					</div>
					<?php
					if ( 'surelywp_addons_settings' !== $option_key && '_overview' === $option_key ) {
						$endpoint_url = surelywp_api_endpoint_url();
						?>
						<div class="surelywp-options-save">
							<a href="<?php echo esc_url( $endpoint_url ); ?>" target="_blank" class="button-primary surelywp-ric-settings-save"><?php esc_html_e( 'Documantation', 'surelywp-services' ); ?></a>
						</div>
					<?php } ?>

				</div>
				<div class="surelywp-ric-settings-box-wrap">
					<?php

					settings_fields( $option_key . '_options' );

					switch ( $option_key ) :
						case 'overview':
							require_once 'overview.php';
							break;
						case 'surelywp_sv_gen_settings':
							require_once 'general-settings.php';
							break;
						case 'surelywp_sv_settings':
							require_once 'settings.php';
							break;
						case 'surelywp_sv_email_templates':
							require_once 'email-templates.php';
							break;
						case 'surelywp_import_export':
							require_once 'import-export-tab.php';
							break;
						case 'changelog':
							require_once 'changelog.php';
							break;
						case 'surelywp_addons_settings':
							require_once 'addons-settings.php';
							break;
						default:
							?>
							<table>

							</table>
							<?php
							break;

					endswitch;
					?>
				</div>
			</form>
		<?php } ?>
		<?php
		if ( 'license_key' === $option_key ) {
			?>
			<div class="license_key_main">
				<?php
				global $client_sv;
				$client_sv->set_textdomain( $panel->settings['plugin_slug'] );
				$endpoint_url   = surelywp_api_endpoint_url();
				$url_activate   = add_query_arg(
					array(
						'page' => $panel->settings['page'],
						'tab'  => 'license_key',
					),
					admin_url( 'admin.php' )
				);
				$url_deactivate = add_query_arg(
					array(
						'page'   => $panel->settings['page'],
						'tab'    => 'license_key',
						'status' => 'deactivate',
					),
					admin_url( 'admin.php' )
				);
				$client_sv->settings()->add_page(
					array(
						'type'                 => 'submenu',                        // Can be: menu, options, submenu.
						'parent_slug'          => $panel->settings['plugin_slug'],  // add your plugin menu slug.
						'page_title'           => esc_html__( 'License Key', 'surelywp-services' ),
						'menu_title'           => esc_html__( 'Licensing', 'surelywp-services' ),
						'capability'           => 'manage_options',
						'menu_slug'            => $panel->settings['page'],
						'icon_url'             => esc_url( $endpoint_url . 'surecart-addons/assets/surelywp-services/icon-128x128.png' ),
						'position'             => null,
						'activated_redirect'   => $url_activate,
						'deactivated_redirect' => $url_deactivate,
						'plugin_name'          => $panel->settings['page_title'],
					)
				);
				$client_sv->settings()->settings_output();
				?>
			</div>
			<?php
		}
		?>
	</div>
</div>