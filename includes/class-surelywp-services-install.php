<?php
/**
 * Install file
 *
 * @author Surlywp
 * @package Services For SureCart
 * @since 1.0.0
 */

if ( ! defined( 'SURELYWP_SERVICES' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Surelywp_Services_Install' ) ) {

	/**
	 * Install plugin table.
	 */
	class Surelywp_Services_Install {

		/**
		 * Single instance of the class
		 *
		 * @var \Surelywp_Services_Install
		 */
		protected static $instance;

		/**
		 * Service table name
		 *
		 * @var string
		 * @access private
		 */
		public $service_table;


		/**
		 * Messgae table name
		 *
		 * @var string
		 * @access private
		 */
		public $message_table;


		/**
		 * Requirement table name
		 *
		 * @var string
		 * @access private
		 */
		public $requirement_table;

		/**
		 * Activity table name
		 *
		 * @var string
		 * @access private
		 */
		public $activity_table;

		/**
		 * Contract table name
		 *
		 * @var string
		 * @access private
		 */
		public $contract_table;


		/**
		 * Recurring Services table name
		 *
		 * @var string
		 * @access private
		 */
		public $recurring_services_table;

		/**
		 * Recurring Services Setting table name
		 *
		 * @var string
		 * @access private
		 */
		public $recurring_services_setting_table;

		/**
		 * Returns single instance of the class
		 *
		 * @package Services For SureCart
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @package Services For SureCart
		 * @since 1.0.0
		 */
		public function __construct() {
			global $wpdb;

			// define local private attribute.
			$this->service_table                    = $wpdb->prefix . 'surelywp_sv_services';
			$this->message_table                    = $wpdb->prefix . 'surelywp_sv_messages';
			$this->requirement_table                = $wpdb->prefix . 'surelywp_sv_requirements';
			$this->activity_table                   = $wpdb->prefix . 'surelywp_sv_activities';
			$this->contract_table                   = $wpdb->prefix . 'surelywp_sv_contracts';
			$this->recurring_services_table         = $wpdb->prefix . 'surelywp_sv_recurring_services';
			$this->recurring_services_setting_table = $wpdb->prefix . 'surelywp_sv_recurring_services_setting';

			// add custom field to global $wpdb.
			$wpdb->surelywp_sv_services                         = $this->service_table;
			$wpdb->surelywp_sv_messages                         = $this->message_table;
			$wpdb->surelywp_sv_requirements                     = $this->requirement_table;
			$wpdb->surelywp_sv_activities                       = $this->activity_table;
			$wpdb->surelywp_sv_contracts                        = $this->contract_table;
			$wpdb->surelywp_sv_recurring_services               = $this->recurring_services_table;
			$wpdb->surelywp_sv_recurring_services_setting_table = $this->recurring_services_setting_table;
		}

		/**
		 * Init db structure of the plugin.
		 *
		 * @param bool $flag The flag for update.
		 * @package Services For SureCart
		 * @since 1.0.0
		 */
		public function surelwp_sv_init( $flag = false ) {

			if ( $this->needs_db_update() || $flag ) {
				$this->surelywp_sv_add_tables();
			}
		}

		/**
		 * The DB needs to be updated?
		 *
		 * @return bool
		 */
		public function needs_db_update() {

			$current_db_version = get_option( 'surelywp_services_db_version', null );

			if ( is_null( $current_db_version ) ) {
				return true;
			} elseif ( version_compare( $current_db_version, SURELYWP_SERVICES_VERSION, '<' ) ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Check if the table of the plugin already exists.
		 *
		 * @param string $table_name The name of the table.
		 *
		 * @package Services For SureCart
		 * @since 1.0.0
		 */
		public function is_installed( $table_name ) {

			global $wpdb;
			$number_of_tables = $wpdb->query( $wpdb->prepare( 'SHOW TABLES LIKE %s', "{$table_name}%" ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			return (bool) ( 1 === (int) $number_of_tables );
		}

		/**
		 * Add tables for a fresh installation
		 *
		 * @package Services For SureCart
		 * @since 1.0.0
		 */
		private function surelywp_sv_add_tables() {
			$this->surelywp_sv_add_services_table();
			$this->surelywp_sv_add_services_message_table();
			$this->surelywp_sv_add_services_requirement_table();
			$this->surelywp_sv_add_services_activity_table();
			$this->surelywp_sv_add_services_contract_table();
			$this->surelywp_sv_add_services_recurring_services_table();
			$this->surelywp_sv_add_services_recurring_services_setting_table();
		}

		/**
		 * Add the table to the database for recurring services setting.
		 *
		 * @package Services For SureCart
		 * @since 1.5
		 */
		private function surelywp_sv_add_services_recurring_services_setting_table() {

			if ( ! $this->is_installed( $this->recurring_services_setting_table ) ) {

				$sql = "CREATE TABLE {$this->recurring_services_setting_table} (
						id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
						recurring_service_id BIGINT UNSIGNED NOT NULL,
						enable_recurring_services TINYINT(1) NOT NULL DEFAULT 0,
						number_of_services_allow INT UNSIGNED NOT NULL DEFAULT 1,
						recurring_based_on VARCHAR(255) DEFAULT NULL,
						recurring_interval_count INT UNSIGNED NOT NULL DEFAULT 1,
						frequency ENUM('daily', 'weekly', 'monthly', 'yearly') DEFAULT NULL,
						is_auto_create_new_service TINYINT(1) NOT NULL DEFAULT 0,
						rollover ENUM('expire', 'rollover') NOT NULL DEFAULT 'expire',
						created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
						updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
						PRIMARY KEY (id)
						) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );
			}
		}

		/**
		 * Add the table to the database for recurring services.
		 *
		 * @package Services For SureCart
		 * @since 1.5
		 */
		private function surelywp_sv_add_services_recurring_services_table() {

			if ( ! $this->is_installed( $this->recurring_services_table ) ) {

				$sql = "CREATE TABLE {$this->recurring_services_table} (
							id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
							order_id VARCHAR( 255 ) NULL DEFAULT NULL,
							product_id VARCHAR( 255 ) NULL DEFAULT NULL,
							subscription_id VARCHAR( 255 ) NULL DEFAULT NULL,
							service_setting_id VARCHAR( 8 ) NULL DEFAULT NULL,
							user_id BIGINT UNSIGNED NOT NULL,
							quota INT UNSIGNED NOT NULL DEFAULT 0,
							next_update_on DATETIME DEFAULT NULL,
							status TINYINT(1) NOT NULL DEFAULT 1,
							created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
							updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
							PRIMARY KEY (id)
						) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );
			}
		}

		/**
		 * Add the table to the database.
		 *
		 * @package Services For SureCart
		 * @since 1.0.0
		 */
		private function surelywp_sv_add_services_table() {

			if ( ! $this->is_installed( $this->service_table ) ) {

				$sql = "CREATE TABLE {$this->service_table} (
							service_id BIGINT( 20 ) NOT NULL AUTO_INCREMENT,
							service_setting_id VARCHAR( 8 ) NULL DEFAULT NULL,
							recurring_service_id BIGINT UNSIGNED NULL DEFAULT NULL,
							user_id INT( 10 ) NULL DEFAULT NULL,
							order_id VARCHAR( 255 ) NULL DEFAULT NULL,
							product_id VARCHAR( 255 ) NULL DEFAULT NULL,
							services_remaining BIGINT UNSIGNED NULL DEFAULT NULL,
							service_status VARCHAR( 255 ) NULL DEFAULT NULL,
							delivery_date DATE NULL DEFAULT NULL,
							created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
							updated_at timestamp NULL DEFAULT NULL,
							PRIMARY KEY  ( service_id )
						) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );
			}
		}

		/**
		 * Add the table to the database.
		 *
		 * @package Services For SureCart
		 * @since 1.0.0
		 */
		private function surelywp_sv_add_services_message_table() {

			if ( ! $this->is_installed( $this->message_table ) ) {

				$sql = "CREATE TABLE {$this->message_table} (
							message_id BIGINT( 20 ) NOT NULL AUTO_INCREMENT,
							sender_id INT( 10 ) NULL DEFAULT NULL,
							receiver_id INT( 10 ) NULL DEFAULT NULL,
							service_id BIGINT( 20 ) NULL DEFAULT NULL,
							message_text TEXT NULL DEFAULT NULL,
							attachment_file_name TEXT NULL DEFAULT NULL,
							is_final_delivery TINYINT(1) NOT NULL DEFAULT 0,
							is_approved_delivery TINYINT(1) NULL DEFAULT NULL,
							created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
							updated_at timestamp NULL DEFAULT NULL,
							PRIMARY KEY  ( message_id ),
							CONSTRAINT fk_message_service_id FOREIGN KEY (service_id)
       						REFERENCES {$this->service_table}(service_id) ON DELETE CASCADE
						) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );
			}
		}

		/**
		 * Add the table to the database.
		 *
		 * @package Services For SureCart
		 * @since 1.0.0
		 */
		private function surelywp_sv_add_services_contract_table() {

			if ( ! $this->is_installed( $this->contract_table ) ) {

				$sql = "CREATE TABLE {$this->contract_table} (
							contract_id BIGINT( 20 ) NOT NULL AUTO_INCREMENT,
							service_id BIGINT( 20 ) NULL DEFAULT NULL,
							contract_details TEXT NULL DEFAULT NULL,
							`signature` TEXT NULL DEFAULT NULL,
							created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
							PRIMARY KEY  ( contract_id ),
							CONSTRAINT fk_contract_service_id FOREIGN KEY (service_id)
       						REFERENCES {$this->service_table}(service_id) ON DELETE CASCADE
						) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );
			}
		}

		/**
		 * Add the table to the database.
		 *
		 * @package Services For SureCart
		 * @since 1.0.0
		 */
		private function surelywp_sv_add_services_requirement_table() {

			if ( ! $this->is_installed( $this->requirement_table ) ) {

				$sql = "CREATE TABLE {$this->requirement_table} (
							requirement_id BIGINT( 20 ) NOT NULL AUTO_INCREMENT,
							service_id BIGINT( 20 ) NULL DEFAULT NULL,
							requirement_type TEXT NULL DEFAULT NULL,
							requirement_title TEXT NULL DEFAULT NULL,
							requirement_desc TEXT NULL DEFAULT NULL,
							requirement TEXT NULL DEFAULT NULL,
							created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
							updated_at timestamp NULL DEFAULT NULL,
							PRIMARY KEY  ( requirement_id ),
							CONSTRAINT fk_requirement_service_id FOREIGN KEY (service_id)
       						REFERENCES {$this->service_table}(service_id) ON DELETE CASCADE
						) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );
			}
		}

		/**
		 * Add the table to the database.
		 *
		 * @package Services For SureCart
		 * @since 1.0.0
		 */
		private function surelywp_sv_add_services_activity_table() {

			if ( ! $this->is_installed( $this->activity_table ) ) {

				$sql = "CREATE TABLE {$this->activity_table} (
							activity_id BIGINT( 20 ) NOT NULL AUTO_INCREMENT,
							service_id BIGINT( 20 ) NULL DEFAULT NULL,
							activity_type VARCHAR( 255 ) NULL DEFAULT NULL,
							activity_info TEXT NULL DEFAULT NULL,
							created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
							PRIMARY KEY  ( activity_id ),
							CONSTRAINT fk_activity_service_id FOREIGN KEY (service_id)
       						REFERENCES {$this->service_table}(service_id) ON DELETE CASCADE
						) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );
			}
		}
	}
}

/**
 * Unique access to instance of Surelywp_Services_Install class.
 *
 * @package Services For SureCart
 * @since 1.0.0
 */
function Surelywp_Services_Install() { // phpcs:ignore 
	return Surelywp_Services_Install::get_instance();
}
