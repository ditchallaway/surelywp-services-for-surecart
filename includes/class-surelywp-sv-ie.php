<?php
/**
 * Main class for Import/Export.
 *
 * @package Services For SureCart
 * @author  SurelyWP
 * @version 1.5.7
 */

// Check if the main plugin constant is defined, exit if not defined.
if ( ! defined( 'SURELYWP_SERVICES' ) ) {
	exit;
}


if ( ! class_exists( 'Surelywp_Sv_Ie' ) ) {

	/**
	 * Main Supports Class
	 *
	 * @package Services For SureCart
	 * @since   1.5.7
	 */
	class Surelywp_Sv_Ie {

		/**
		 * Single instance of the class
		 *
		 * @var \Surelywp_Sv_Ie
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @package Services For SureCart
		 * @since   1.5.7
		 * @return  \Surelywp_Sv_Ie
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor function for the Surelywp Supports class.
		 *
		 * Initializes the class and sets up various actions and filters.
		 *
		 * @package Services For SureCart
		 * @since   1.5.7
		 */
		public function __construct() {
			add_action( 'admin_init', array( $this, 'surelywp_sv_handle_export' ) );
			add_action( 'wp_ajax_surelywp_sv_import_settings', array( $this, 'surelywp_sv_handle_import' ) );
		}

		/**
		 * Return all option keys we want to manage for import export.
		 *
		 * @package Services For SureCart
		 * @since 1.5.7
		 */
		public static function get_keys() {
			return array(
				'surelywp_sv_gen_settings_options',
				'surelywp_sv_settings_options',
				'surelywp_sv_email_templates_options',
			);
		}

		/**
		 * The Function will export all the Services plugin settings.
		 *
		 * @package Services For SureCart
		 * @since 1.5.7
		 */
		public function surelywp_sv_handle_export() {
			if ( isset( $_POST['surelywp_sv_export'] ) && check_admin_referer( 'surelywp_sv_export_settings', 'surelywp_export_nonce' ) ) {
				$settings = array();

				foreach ( self::get_keys() as $key ) {
					$settings[ $key ] = get_option( $key );
				}

				header( 'Content-Disposition: attachment; filename=surelywp-services-settings.json' );
				header( 'Content-Type: application/json; charset=utf-8' );
				echo wp_json_encode( $settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
				exit;
			}
		}



		/**
		 * The Function will import all the Services plugin settings.
		 *
		 * @package Services For SureCart
		 * @since 1.5.7
		 */
		public function surelywp_sv_handle_import() {
			if ( check_admin_referer( 'surelywp_sv_import_settings', 'surelywp_import_nonce' ) ) {
				$import_settings_sv_tmp_name = isset( $_FILES['import_sv_file']['tmp_name'] ) ? sanitize_text_field( wp_unslash( $_FILES['import_sv_file']['tmp_name'] ) ) : '';
				if ( empty( $import_settings_sv_tmp_name ) ) {
					wp_send_json_error( array( 'message' => __( 'No file uploaded.', 'surelywp-services' ) ) );
					wp_die();
				}
				if ( ! empty( $import_settings_sv_tmp_name ) ) {
					if ( is_uploaded_file( $import_settings_sv_tmp_name ) ) {
						$data     = file_get_contents( $import_settings_sv_tmp_name );
						$settings = json_decode( $data, true );
					} else {
						wp_send_json_error( array( 'message' => __( 'Invalid file upload.', 'surelywp-services' ) ) );
						wp_die();
					}

					if ( is_array( $settings ) ) {
						foreach ( self::get_keys() as $key ) {
							if ( isset( $settings[ $key ] ) ) {
								update_option( $key, $settings[ $key ] );
							} else {
								wp_send_json_error( array( 'message' => __( 'Invalid Services Plugin Settings', 'surelywp-services' ) ) );
								wp_die();
							}
						}
					} else {
						wp_send_json_error( array( 'message' => __( 'Invalid JSON file.', 'surelywp-services' ) ) );
						wp_die();
					}
				}
			}
			wp_die();
		}
	}

	/**
	 * Unique access to instance of Surelywp_Sv_Ie class
	 *
	 * @package Services For SureCart
	 * @since   1.5.7
	 */
	function Surelywp_Sv_Ie() {  // phpcs:ignore
		$instance = Surelywp_Sv_Ie::get_instance();
		return $instance;
	}

}
