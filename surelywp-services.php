<?php
/**
 * Plugin Name: Services For SureCart
 * Plugin URI: https://surelywp.com
 * Description: This plugin empowers you to sell services and custom deliverables with SureCart. Enjoy features like status and activity tracking, built-in messaging, and final delivery and approvals, all beautifully integrated directly into your website and customer dashboard.
 * Version: 1.7.2
 * Tested up to: 6.8.3
 * Author: SurelyWP
 * Author URI: https://surelywp.com
 * Text Domain: surelywp-services
 * Domain Path: /languages/
 *
 * @package Services For SureCart
 * @author SurelyWP
 * @category Core
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Basic Plugin Definitions
 *
 * @package Services For SureCart
 * @since 1.0.0
 */
if ( ! defined( 'SURELYWP_SERVICES_VERSION' ) ) {
	define( 'SURELYWP_SERVICES_VERSION', '1.7.2' );
}
if ( ! defined( 'SURELYWP_SERVICES_INIT' ) ) {
	define( 'SURELYWP_SERVICES_INIT', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'SURELYWP_SERVICES' ) ) {
	define( 'SURELYWP_SERVICES', true );
}
if ( ! defined( 'SURELYWP_SERVICES_FILE' ) ) {
	define( 'SURELYWP_SERVICES_FILE', __FILE__ );
}
if ( ! defined( 'SURELYWP_SERVICES_URL' ) ) {
	define( 'SURELYWP_SERVICES_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'SURELYWP_SERVICES_DIR' ) ) {
	define( 'SURELYWP_SERVICES_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'SURELYWP_SERVICES_PLUGIN_TITLE' ) ) {
	define( 'SURELYWP_SERVICES_PLUGIN_TITLE', 'Services For SureCart' );
}

if ( ! defined( 'SURELYWP_SERVICES_TEMPLATE_PATH' ) ) {
	define( 'SURELYWP_SERVICES_TEMPLATE_PATH', SURELYWP_SERVICES_DIR . 'templates' );
}
if ( ! defined( 'SURELYWP_SERVICES_ASSETS_URL' ) ) {
	define( 'SURELYWP_SERVICES_ASSETS_URL', SURELYWP_SERVICES_URL . 'assets' );
}
if ( ! defined( 'SURELYWP_SERVICES_SLUG' ) ) {
	define( 'SURELYWP_SERVICES_SLUG', 'surelywp-services' );
}

if ( ! defined( 'SURELYWP_SERVICES_BASENAME' ) ) {
	define( 'SURELYWP_SERVICES_BASENAME', basename( SURELYWP_SERVICES_DIR ) );
}

if ( ! defined( 'SURELYWP_SERVICES_REQ_REMINDER_CRON' ) ) {
	define( 'SURELYWP_SERVICES_REQ_REMINDER_CRON', 'surelywp_sv_send_req_reminder_email' );
}

if ( ! defined( 'SURELYWP_SERVICES_AUTO_COMPLETE_CRON' ) ) {
	define( 'SURELYWP_SERVICES_AUTO_COMPLETE_CRON', 'surelywp_sv_auto_complete_service' );
}

if ( ! defined( 'SURELYWP_SERVICES_DAILY_UPDATES' ) ) {
	define( 'SURELYWP_SERVICES_DAILY_UPDATES', 'surelywp_sv_daily_updates' );
}

if ( ! defined( 'SURELYWP_SERVICES_IE_FILE_SIZE' ) ) {
	define( 'SURELYWP_SERVICES_IE_FILE_SIZE', '5' );
}


/**
 * Error message if Surecart is not installed
 *
 * @package Services For SureCart
 * @since 1.0.0
 */
function surelywp_sv_install_admin_notice() {
	?>
	<div class="error">
		<p><?php esc_html_e( 'Services For SureCart For SureCart is enabled but not effective. It requires SureCart in order to work.', 'surelywp-services' ); ?></p>
	</div>
	<?php
}

/**
 * Plugin Framework Version Check
 *
 * @package Services For SureCart
 * @since 1.0.0
 */
if ( ! function_exists( 'surelywp_load_framework' ) && file_exists( SURELYWP_SERVICES_DIR . 'framework/framework.php' ) ) {
	require_once SURELYWP_SERVICES_DIR . 'framework/framework.php';
}
surelywp_load_framework( SURELYWP_SERVICES_DIR );

/**
 * Activation Hook
 *
 * Register plugin activation hook.
 *
 * @package Services For SureCart
 * @since 1.0.0
 */
register_activation_hook( __FILE__, 'surelywp_plugin_registration_hook' );

if ( ! function_exists( 'surelywp_plugin_registration_hook' ) ) {
	require_once 'framework/surelywp-plugin-registration-hook.php';
}

/**
 * Deactivation Hook
 *
 * Register plugin deactivation hook.
 *
 * @package Services For SureCart
 * @since 1.0.0
 */
function surelywp_sv_deactivation_hook() {

	surelywp_register_deactivation_hook( plugin_basename( __FILE__ ) );
	if ( ! class_exists( 'SureCart' ) ) {
		return;
	}
	update_option( 'surelywp_sv_plugin_activated', false );
}
register_deactivation_hook( __FILE__, 'surelywp_sv_deactivation_hook' );


/**
 * Initialize global variables
 *
 * @package Services For SureCart
 * @since 1.0.0
 */
global $client_sv;

/**
 * Init plugin
 *
 * @package Services For SureCart
 * @since 1.0.0
 */
function surelywp_sv_constructor() {

	if ( ! class_exists( 'SureCart' ) ) {
		add_action( 'admin_notices', 'surelywp_sv_install_admin_notice' );
		return;
	}

	// Load Plugin TextDomain.
	load_plugin_textdomain( 'surelywp-services', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	require_once SURELYWP_SERVICES_DIR . 'framework/surelywp-functions.php';
	require_once SURELYWP_SERVICES_DIR . 'framework/surelywp-addons.php';

	if ( is_admin() ) {
		require_once 'includes/class-surelywp-services-admin.php';
		require_once 'includes/class-surelywp-services-admin-list-table.php';
		$services_admin_obj = new Surelywp_Services_Admin();
		require_once 'includes/class-surelywp-sv-ie.php';
		Surelywp_Sv_Ie();
	}

	require_once 'includes/class-surelywp-services.php';
	$services_obj = Surelywp_Services();
	require_once 'includes/class-surelywp-services-install.php';
	require_once 'includes/class-surelywp-services-model.php';
	require_once 'includes/class-surelywp-services-ajax-handler.php';

	// load blocks.
	require_once 'blocks/surelywp-sv-customer-services/surelywp-sv-customer-services.php';
	require_once 'blocks/surelywp-sv-services-alert/surelywp-sv-services-alert.php';
	require_once 'blocks/surelywp-sv-services-requirements-form/surelywp-sv-services-requirements-form.php';

	$services_obj->surelywp_sv_update_version();

	// Code to run when the plugin is activated.
	if ( ! get_option( 'surelywp_sv_plugin_activated' ) ) {

		$services_obj->surelywp_sv_on_plugin_active();

		// Set a flag to indicate that the plugin has been activated.
		update_option( 'surelywp_sv_plugin_activated', true );
	}
}
add_action( 'plugins_loaded', 'surelywp_sv_constructor', 11 );

/* Licensing */
if ( ! class_exists( 'SureCart\Licensing\Client' ) ) {
	require_once SURELYWP_SERVICES_DIR . 'licensing/src/Client.php';
}

if ( class_exists( 'SureCart\Licensing\Client' ) ) {

	require_once SURELYWP_SERVICES_DIR . 'framework/surelywp-functions.php';

	add_action(
		'init',
		function () {

			global $client_sv;

			// Default public API token.
			$sc_public_api_token = '';
			if ( function_exists( 'surelywp_get_public_token' ) ) {
				$sc_public_api_token = surelywp_get_public_token();
			}

			if ( ! empty( $sc_public_api_token ) ) {
				$client_sv = new \SureCart\Licensing\Client( SURELYWP_SERVICES_PLUGIN_TITLE, $sc_public_api_token, __FILE__ );
			} else {
				$client_sv = new \SureCart\Licensing\Client( SURELYWP_SERVICES_PLUGIN_TITLE, __FILE__ );
			}
		}
	);
}
