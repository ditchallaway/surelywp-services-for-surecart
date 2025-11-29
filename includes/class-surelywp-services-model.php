<?php
/**
 * Services Model Class.
 *
 * @package Services For SureCart
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'SURELYWP_SERVICES' ) ) {
	exit;
}

// Global Model Variable.
global $surelywp_sv_model;

if ( ! class_exists( 'Surelywp_Services_Model' ) ) {

	/**
	 * Plugin Model Class
	 *
	 * Handles generic functionailties
	 *
	 * @since 1.0.0
	 */
	class Surelywp_Services_Model {

		/**
		 *
		 * Class constructor
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function __construct() {
		}


		/**
		 * The single instance of the class.
		 *
		 * @var SurelyWP_Assets
		 */
		private static $instance;

		/**
		 * Singleton implementation.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}


		/**
		 * Insert service Data.
		 *
		 * @param array $data The data of the services.
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_insert_service( $data ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_services';

			if ( ! empty( $data['user_id'] ) ) {
				$user_id = $data['user_id'];
			} else {
				$user_id = get_current_user_id();
			}

			if ( ! empty( $data ) ) {

				$inserted_row = $wpdb->insert(
					$table_name,
					array(
						'user_id'              => $user_id,
						'service_setting_id'   => $data['service_setting_id'],
						'recurring_service_id' => $data['recurring_service_id'] ?? null,
						'order_id'             => $data['order_id'],
						'product_id'           => $data['product_id'],
						'services_remaining'   => $data['services_remaining'] ?? null,
						'service_status'       => $data['service_status'],
						'delivery_date'        => $data['delivery_date'],
					),
					array( '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
				);

				$service_id = $wpdb->insert_id ?? 0;
				return $service_id;

			} else {
				return false;
			}
		}

		/**
		 * Insert Recurring service Data.
		 *
		 * @param array $data The data of the Recurring services.
		 * @package Services For SureCart
		 * @since   1.5
		 */
		public function surelywp_sv_insert_recurring_service( $data ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_recurring_services';

			if ( empty( $data['user_id'] ) ) {
				$data['user_id'] = get_current_user_id();
			}

			if ( ! empty( $data ) ) {

				$inserted_row = $wpdb->insert(
					$table_name,
					$data,
					array( '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
				);

				$recurring_service_id = $wpdb->insert_id ?? 0;
				return $recurring_service_id;

			} else {
				return false;
			}
		}

		/**
		 * Insert Recurring service setting Data.
		 *
		 * @param array $data The data of the Recurring services.
		 * @package Services For SureCart
		 * @since   1.5
		 */
		public function surelywp_sv_insert_recurring_service_setting( $data ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_recurring_services_setting';

			if ( ! empty( $data ) ) {

				$inserted_row = $wpdb->insert(
					$table_name,
					$data,
					array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
				);

				$recurring_service_setting_id = $wpdb->insert_id ?? 0;
				return $recurring_service_setting_id;

			} else {
				return false;
			}
		}


		/**
		 * Insert service Message.
		 *
		 * @param array $data The data of the services.
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_insert_service_msg( $data ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_messages';

			if ( ! empty( $data ) ) {

				$inserted_row = $wpdb->insert(
					$table_name,
					$data,
					array( '%s', '%s', '%s', '%s', '%s', '%s' )
				);

				$message_id = $wpdb->insert_id ?? 0;
				return $message_id;

			} else {
				return false;
			}
		}

		/**
		 * Insert service Activities.
		 *
		 * @param array $data The data of the services activities.
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_insert_activity( $data ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_activities';

			if ( ! empty( $data ) ) {

				$inserted_row = $wpdb->insert(
					$table_name,
					$data,
					array( '%s', '%s', '%s', '%s' )
				);

				return $inserted_row;

			} else {
				return false;
			}
		}


		/**
		 * Get All Services.
		 *
		 * @param int $per_page The number of the services per page.
		 * @param int $page_number The current page number.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_all_services( $per_page = 10, $page_number = 1 ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_services';

			// Determine the total number of services.
			$total_services = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );

			// Calculate the total number of pages.
			$total_pages = ceil( $total_services / $per_page );

			// Calculate the SQL LIMIT for pagination.
			$offset = ( $page_number - 1 ) * $per_page;

			// Query for retrieve services with pagination.
			$query  = "SELECT * FROM $table_name ORDER BY order_id DESC LIMIT $per_page OFFSET $offset";
			$result = $wpdb->get_results( $query );

			if ( ! empty( $result ) ) {

				// Prepare pagination data.
				$pagination_data = array(
					'total_pages'    => $total_pages,
					'current_page'   => $page_number,
					'total_services' => $total_services,
				);

				// Return both results and pagination data.
				return array(
					'services'   => $result,
					'pagination' => $pagination_data,
				);
			}
			return false;
		}

		/**
		 * Get All Services Activies.
		 *
		 * @param int $service_id The id of the service.
		 * @param int $per_page The number of the services per page.
		 * @param int $page_number The current page number.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_service_activities( $service_id, $per_page = 30, $page_number = 1 ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_activities';

			// Determine the total number of services.
			$total_services = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE service_id = $service_id" );

			// Calculate the total number of pages.
			$total_pages = ceil( $total_services / $per_page );

			// Calculate the SQL LIMIT for pagination.
			$offset = ( $page_number - 1 ) * $per_page;

			// Query for retrieve services with pagination.
			$query  = "SELECT * FROM $table_name WHERE service_id = $service_id ORDER BY created_at ASC LIMIT $per_page OFFSET $offset";
			$result = $wpdb->get_results( $query );

			if ( ! empty( $result ) ) {

				// Prepare pagination data.
				$pagination_data = array(
					'total_pages'    => $total_pages,
					'current_page'   => $page_number,
					'total_services' => $total_services,
				);

				// Return both results and pagination data.
				return array(
					'activities' => $result,
					'pagination' => $pagination_data,
				);
			}
			return false;
		}

		/**
		 * Get customer Services.
		 *
		 * @param int $per_page The number of the services per page.
		 * @param int $page_number The current page number.
		 * @param int $user_id The id of the user.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_user_services( $per_page = 10, $page_number = 1, $user_id = '' ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_services';

			if ( empty( $user_id ) ) {

				$user_id = get_current_user_id();
			}

			// Determine the total number of services.
			$total_services = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE user_id = $user_id" );

			// Calculate the total number of pages.
			$total_pages = ceil( $total_services / $per_page );

			// Calculate the SQL LIMIT for pagination.
			$offset = ( intval( $page_number ) - 1 ) * $per_page;

			// Query for retrieve services with pagination.
			$query  = "SELECT * FROM $table_name WHERE user_id = $user_id ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
			$result = $wpdb->get_results( $query );

			if ( ! empty( $result ) ) {

				// Prepare pagination data.
				$pagination_data = array(
					'total_pages'    => $total_pages,
					'current_page'   => $page_number,
					'total_services' => $total_services,
					'total_show'     => count( $result ),
				);

				// Return both results and pagination data.
				return array(
					'services'   => $result,
					'pagination' => $pagination_data,
				);
			}
			return false;
		}

		/**
		 * Get milestone status by id.
		 *
		 * @param int $milestone_id the id of the milestone.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function get_milestone_status_by_id( $milestone_id ) {

			global $wpdb;

			$messages_table  = $wpdb->prefix . 'surelywp_sv_messages';
			$activities_table = $wpdb->prefix . 'surelywp_sv_activities';

			$query = $wpdb->prepare("
				SELECT 
					m.message_id,
					m.service_id,
					m.milestone_id,
					m.service_status,
					a.activity_type,
					CASE 
						WHEN a.activity_type IS NOT NULL THEN 1 
						ELSE 0 
					END AS is_accepted
				FROM {$messages_table} AS m
				LEFT JOIN {$activities_table} AS a 
					ON a.service_id = m.service_id
					AND a.activity_type = CONCAT('delivery_accept_milestone_', m.milestone_id)
					WHERE m.service_status = %s
					AND m.milestone_id = %d
				", 'milestone_submit', $milestone_id );

			$results = $wpdb->get_results( $query );
			if ( ! empty( $results ) ) {
				return $results;
			} else {
				return false;
			}
		}

		/**
		 * Get service by id.
		 *
		 * @param int $service_id the id of the service.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_service( $service_id ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_services';

			$query  = "SELECT * FROM $table_name WHERE service_id = $service_id ";
			$result = $wpdb->get_results( $query );

			if ( ! empty( $result ) ) {
				return $result;
			}
			return false;
		}

		/**
		 * Get service service contract.
		 *
		 * @param int $service_id the id of the service.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_service_contract( $service_id ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_contracts';

			$query  = "SELECT * FROM $table_name WHERE service_id = $service_id LIMIT 1";
			$result = $wpdb->get_results( $query );

			if ( ! empty( $result ) ) {
				return $result;
			}
			return false;
		}

		/**
		 * Get customer service by id.
		 *
		 * @param int $service_id the id of the service.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_customer_service( $service_id ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_services';

			$user_id = get_current_user_id();

			$query  = "SELECT * FROM $table_name WHERE service_id = $service_id AND user_id = $user_id";
			$result = $wpdb->get_results( $query );

			if ( ! empty( $result ) ) {
				return $result;
			}
			return false;
		}

		/**
		 * Get service by order id.
		 *
		 * @param int $order_id the id of the order.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_service_by_order( $order_id ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_services';

			$query  = "SELECT * FROM $table_name WHERE order_id = '$order_id'";
			$result = $wpdb->get_results( $query );
			if ( ! empty( $result ) ) {
				return $result;
			}
			return false;
		}

		/**
		 * Get service by order id and product id.
		 *
		 * @param int $order_id the id of the order.
		 * @param int $product_id the id of the product.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_service_by_product( $order_id, $product_id ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_services';

			$query  = "SELECT * FROM $table_name WHERE order_id = '$order_id' AND product_id = '$product_id'";
			$result = $wpdb->get_results( $query );
			if ( ! empty( $result ) ) {
				return $result;
			}
			return false;
		}

		/**
		 * Get service by customer order id.
		 *
		 * @param int $order_id the id of the order.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_service_by_customer_order( $order_id ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_services';
			$user_id    = get_current_user_id();

			$query  = "SELECT * FROM $table_name WHERE order_id = '$order_id' AND user_id = $user_id";
			$result = $wpdb->get_results( $query );
			if ( ! empty( $result ) ) {
				return $result;
			}
			return false;
		}

		/**
		 * Get message by service id.
		 *
		 * @param int $service_id the id of the service.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_service_messages( $service_id ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_messages';

			$query  = "SELECT * FROM $table_name WHERE service_id = $service_id ORDER BY created_at DESC LIMIT 20";
			$result = $wpdb->get_results( $query );

			if ( ! empty( $result ) ) {
				return $result;
			}
			return false;
		}


		/**
		 * Fetch message by service id.
		 *
		 * @param int    $service_id the id of the service.
		 * @param string $last_message_datatime the time of the last message.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_fetch_service_latest_messages( $service_id, $last_message_datatime ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_messages';
			$query      = $wpdb->prepare( "SELECT * FROM $table_name WHERE service_id = %d AND created_at > %s ORDER BY created_at ASC", $service_id, $last_message_datatime );
			$result     = $wpdb->get_results( $query );

			if ( ! empty( $result ) ) {
				return $result;
			}
			return false;
		}

		/**
		 * Fetch message by service id.
		 *
		 * @param int    $service_id the id of the service.
		 * @param string $first_message_datatime the time of the first message.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_load_more_messages( $service_id, $first_message_datatime ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_messages';
			$query      = $wpdb->prepare( "SELECT * FROM $table_name WHERE service_id = %d AND created_at < %s ORDER BY created_at DESC limit 20", $service_id, $first_message_datatime );
			$result     = $wpdb->get_results( $query );

			if ( ! empty( $result ) ) {
				return $result;
			}
			return false;
		}

		/**
		 * Function to add service contract.
		 *
		 * @param array $contract_data the array of the contract.
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_add_service_contract( $contract_data ) {

			global $wpdb;

			$contract_table = $wpdb->prefix . 'surelywp_sv_contracts';
			$inserted_row   = 0;
			if ( ! empty( $contract_data ) ) {

				$inserted_row = $wpdb->insert(
					$contract_table,
					$contract_data,
					array( '%s', '%s', '%s' )
				);

				return $inserted_row ? true : false;
			}

			return false;
		}

		/**
		 * Function to add service requiement.
		 *
		 * @param array $requirements_data the array of the requirement.
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_add_service_requirement( $requirements_data ) {

			global $wpdb;

			$requirement_table = $wpdb->prefix . 'surelywp_sv_requirements';
			$inserted_row      = 0;
			if ( ! empty( $requirements_data ) ) {
				foreach ( $requirements_data as $requirement ) {
					$inserted_row += $wpdb->insert(
						$requirement_table,
						$requirement,
						array( '%s', '%s', '%s', '%s' )
					);
				}

				return $inserted_row ? true : false;
			}

			return false;
		}

		/**
		 * Function to get final delivery message.
		 *
		 * @param string $service_id the id of the service.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_final_delivery_message( $service_id ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_messages';

			$query = "SELECT * FROM $table_name WHERE service_id = $service_id AND is_final_delivery = 1 ORDER BY created_at DESC LIMIT 1";

			// update service table.
			$result = $wpdb->get_results( $query );

			if ( $result ) {
				return $result;
			}
			return false;
		}


		/**
		 * Function to get all final delivery messages.
		 *
		 * @param string $service_id the id of the service.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_final_deliveries( $service_id ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_messages';

			$query = "SELECT * FROM $table_name WHERE service_id = $service_id AND is_final_delivery = 1 ORDER BY created_at ASC";

			// update service table.
			$result = $wpdb->get_results( $query );

			if ( $result ) {
				return $result;
			}
			return false;
		}

		/**
		 * Function to approve or rejcet delivery.
		 *
		 * @param string $message_id the name id of the message.
		 * @param string $is_approve is approve or not delivery.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_approve_delivery( $message_id, $is_approve ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_messages';

			// Data to update.
			$data = array(
				'is_approved_delivery' => $is_approve,
				'updated_at'           => current_time( 'mysql' ),
			);

			// Where clause to specify which row(s) to update.
			$where = array(
				'message_id' => $message_id, // Assuming 'column_id' is the primary key column.
			);

			// update service table.
			$result = $wpdb->update( $table_name, $data, $where );

			if ( $result ) {
				return $result;
			}
			return false;
		}

		/**
		 * Function to change the delivery date.
		 *
		 * @param string $service_id the name id of the message.
		 * @param string $delivery_date the date of the delivery.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_change_delivery_date( $service_id, $delivery_date ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_services';

			// Data to update.
			$data = array(
				'delivery_date' => $delivery_date,
				'updated_at'    => current_time( 'mysql' ),
			);

			// Where clause to specify which row(s) to update.
			$where = array(
				'service_id' => $service_id, // Assuming 'column_id' is the primary key column.
			);

			// update service table.
			$result = $wpdb->update( $table_name, $data, $where );

			if ( $result ) {
				return $result;
			}
			return false;
		}


		/**
		 * Function to add service requiement.
		 *
		 * @param string $service_id the id of the service.
		 * @param string $service_status the status of the service.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_update_service_status( $service_id, $service_status ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_services';

			// Data to update.
			$data = array(
				'service_status' => $service_status,
				'updated_at'     => current_time( 'mysql' ),
			);

			// Where clause to specify which row(s) to update.
			$where = array(
				'service_id' => $service_id, // Assuming 'column_id' is the primary key column.
			);

			// Run the update query.
			$result = $wpdb->update( $table_name, $data, $where );

			if ( $result ) {
				return $result;
			}
			return false;
		}


		/**
		 * Function to update service.
		 *
		 * @param string $order_id the id of the service.
		 * @param string $product_id the status of the service.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_update_service_by_order( $order_id, $product_id, $service_data ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_services';

			// Data to update.
			$service_data['updated_at'] = current_time( 'mysql' );

			// Where clause to specify which row(s) to update.
			$where = array(
				'order_id'   => $order_id, // Assuming 'column_id' is the primary key column.
				'product_id' => $product_id, // Assuming 'column_id' is the primary key column.
			);

			// Run the update query.
			$result = $wpdb->update( $table_name, $service_data, $where );

			if ( $result ) {
				return $result;
			}
			return false;
		}

		/**
		 * Function to update service table.
		 *
		 * @param string $service_id the id of the service.
		 * @param array  $service_data the id of the service.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_update_service( $service_id, $service_data ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_services';

			// Data to update.
			$service_data['updated_at'] = current_time( 'mysql' );

			// Where clause to specify which row(s) to update.
			$where = array(
				'service_id' => $service_id, // Assuming 'column_id' is the primary key column.
			);

			// Run the update query.
			$result = $wpdb->update( $table_name, $service_data, $where );

			if ( $result ) {
				return $result;
			}
			return false;
		}

		/**
		 * Check Service have requrements.
		 *
		 * Services For SureCart
		 *
		 * @param int $service_id The service id.
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_is_service_have_req( $service_id ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_requirements';

			if ( ! empty( $service_id ) ) {

				$result = $wpdb->get_var( "SELECT requirement_id FROM $table_name WHERE service_id = $service_id LIMIT 1" );

				return $result;

			} else {
				return '';
			}
		}

		/**
		 * Check last Delivery approve or not.
		 *
		 * Services For SureCart
		 *
		 * @param int $message_id The id of message.
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_is_approved_delivery( $message_id ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_messages';

			if ( ! empty( $message_id ) ) {

				$result = $wpdb->get_var( "SELECT is_approved_delivery FROM $table_name WHERE message_id = $message_id" );

				return $result;

			} else {
				return '';
			}
		}

		/**
		 * Check is this final deliverly message.
		 *
		 * Services For SureCart
		 *
		 * @param int $message_id The service id.
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_is_final_delivery_message( $message_id ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_messages';

			if ( ! empty( $message_id ) ) {

				$result = $wpdb->get_var( "SELECT is_final_delivery FROM $table_name WHERE message_id = $message_id" );

				return $result;

			} else {
				return '';
			}
		}


		/**
		 * Get service all requiremens.
		 *
		 * Services For SureCart
		 *
		 * @param int $service_id The service id.
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_service_requirements( $service_id ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_requirements';

			if ( ! empty( $service_id ) ) {

				$query  = "SELECT * FROM $table_name WHERE service_id = $service_id";
				$result = $wpdb->get_results( $query );

				return $result;

			} else {
				return '';
			}
		}

		/**
		 * Get any field value from any table by service id.
		 *
		 * @param string $tb_name The name of the table.
		 * @param string $service_id The id of the service.
		 * @param string $field_name The name of the field.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_table_field( $tb_name, $service_id, $field_name ) {

			global $wpdb;

			$table_name = $wpdb->prefix . $tb_name;

			if ( ! empty( $service_id ) ) {

				$query  = "SELECT $field_name FROM $table_name WHERE service_id = $service_id";
				$result = $wpdb->get_var( $query );

				return $result;

			} else {
				return '';
			}
		}

		/**
		 * Get service status.
		 *
		 * Services For SureCart
		 *
		 * @param int $service_id The service id.
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_service_status( $service_id ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_services';

			if ( ! empty( $service_id ) ) {

				$result = $wpdb->get_var( "SELECT service_status FROM $table_name WHERE service_id = $service_id" );

				return $result;

			} else {
				return '';
			}
		}

		/**
		 * Get service setting id.
		 *
		 * Services For SureCart
		 *
		 * @param int $service_id The service id.
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_service_setting_id( $service_id ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_services';

			if ( ! empty( $service_id ) ) {

				$result = $wpdb->get_var( "SELECT service_setting_id FROM $table_name WHERE service_id = $service_id" );

				return $result;

			} else {
				return '';
			}
		}


		/**
		 * Get service data data.
		 *
		 * Services For SureCart
		 *
		 * @param int   $service_id The service id.
		 * @param array $column_names The array of the column names.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_db_service_data( $service_id, $column_names ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_services';

			$columns = implode( ', ', $column_names );

			if ( ! empty( $service_id ) && ! empty( $column_names ) ) {

				$result = $wpdb->get_row( $wpdb->prepare( "SELECT {$columns} FROM {$table_name} WHERE service_id = %d", $service_id ), ARRAY_A );

				return $result;

			} else {
				return '';
			}
		}

		/**
		 * Get all services for list table.
		 *
		 * @param array $args The array of the args.
		 *
		 * @package Services For SureCart
		 * @since 1.0.0
		 */
		public function surelywp_sv_get_services_for_list_table( $args = array() ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_services';

			$sql = 'SELECT * FROM ' . $table_name . ' WHERE 1=1';

			$filter = '';
			if ( isset( $args['service_status'] ) && ! empty( $args['service_status'] ) && 'all' !== $args['service_status'] ) {

				$service_status = $args['service_status'];
				$filter        .= " AND service_status = '$service_status'";
			}

			$sql .= $filter;
			if ( isset( $args['orderby'] ) && ! empty( $args['orderby'] ) ) {
				$sql .= ' ORDER BY ' . $args['orderby'];
			}
			if ( isset( $args['order'] ) && ! empty( $args['order'] ) ) {
				$sql .= ' ' . $args['order'];
			}
			if ( isset( $args['offset'] ) ) {
				$sql .= ' LIMIT ' . $args['offset'];
			}
			if ( isset( $args['posts_per_page'] ) && ! empty( $args['posts_per_page'] ) ) {
				$sql .= ' , ' . $args['posts_per_page'];
			}
			$result = $wpdb->get_results( $sql, 'ARRAY_A' );

			$data_res['data'] = $result;

			// Get Total count of Items.
			$data_res['total'] = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $table_name . ' WHERE 1=1' . $filter );

			return $data_res;
		}

		/**
		 * Function get last service id number.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_last_service_id() {

			global $wpdb;

			$service_table   = $wpdb->prefix . 'surelywp_sv_services';
			$last_service_id = $wpdb->get_var( "SELECT MAX(service_id) FROM $service_table" );

			if ( null === $last_service_id ) {

				// Handle the case when there are no entries in the table.
				$last_service_id = 0;
			}
			return $last_service_id;
		}


		/**
		 * Function to change autoincrement number of service id.
		 *
		 * @param int $start_value The Service id staring number.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_update_auto_increment_service_id( $start_value ) {

			global $wpdb;

			$service_table = $wpdb->prefix . 'surelywp_sv_services';
			$alter_sql     = "ALTER TABLE {$service_table} AUTO_INCREMENT = $start_value;";
			$result        = $wpdb->query( $alter_sql );

			return $result;
		}

		/**
		 * Function get total in progress services.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_total_in_progress_services() {

			global $wpdb;

			$service_table = $wpdb->prefix . 'surelywp_sv_services';
			$total         = $wpdb->get_var( "SELECT COUNT(*) FROM $service_table WHERE service_status = 'service_start' " );

			if ( null === $total ) {

				// Handle the case when there are no entries in the table.
				$total = 0;
			}
			return $total;
		}

		/**
		 * Function get total notification count.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_notification_count() {

			global $wpdb;

			$user_id       = get_current_user_id() ?? '';
			$service_table = $wpdb->prefix . 'surelywp_sv_services';
			$total         = $wpdb->get_var( "SELECT COUNT(*) FROM $service_table WHERE user_id = $user_id AND service_status IN ('waiting_for_req', 'waiting_for_contract', 'service_submit');" );

			if ( null === $total ) {

				// Handle the case when there are no entries in the table.
				$total = 0;
			}
			return $total;
		}

		/**
		 * Function get Services ids of completed and canceled stataus.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_cancel_or_complete_service_ids() {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_services';

			$results = array();

			// Check if the table exists.
			if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) === $table_name ) {

				// SQL query to get service IDs with status 'service_canceled' or 'service_complete'.
				$query = "SELECT service_id FROM {$table_name} WHERE service_status IN ('service_complete', 'service_canceled')";

				$results = $wpdb->get_col( $query );

			}

			return ! empty( $results ) ? $results : array();
		}

		/**
		 * Function get Customer Revision Message.
		 *
		 * @param int $service_id the customer service id.
		 * @param int $final_delivery_msg_id the final delivery message id.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_customer_revision_msg( $service_id, $final_delivery_msg_id ) {

			global $wpdb;

			// Table name, adjust as necessary.
			$table_name = $wpdb->prefix . 'surelywp_sv_messages';

			// Prepare SQL query.
			$sql = $wpdb->prepare(
				"SELECT m1.*
				FROM $table_name m1
				WHERE m1.service_id = %d
				  AND m1.message_id > %d
				  AND m1.sender_id != (
					SELECT m2.sender_id
					FROM $table_name m2
					WHERE m2.message_id = %d
					  AND m2.service_id = %d
				  )
				ORDER BY m1.message_id
				LIMIT 1",
				$service_id,
				$final_delivery_msg_id,
				$final_delivery_msg_id,
				$service_id
			);

			// Execute query.
			$result = $wpdb->get_row( $sql, ARRAY_A ); // Fetch as an associative array.

			return $result;
		}

		/**
		 * Function get Services which have service_created or Service_contract_signed status.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_old_status_services() {
			global $wpdb;

			// Table name, adjust as necessary.
			$table_name = $wpdb->prefix . 'surelywp_sv_services';

			$result = array();

			// Check if the table exists.
			if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) === $table_name ) {

				// Prepare SQL query.
				$sql = "SELECT service_id, service_setting_id, service_status FROM $table_name WHERE service_status = 'service_created' OR service_status = 'service_contract_signed'";

				// Execute query.
				$result = $wpdb->get_results( $sql, ARRAY_A ); // Fetch multiple rows as an associative array.
			}

			return ! empty( $result ) ? $result : array();
		}


		/**
		 * Function to Changes services status on service setting save.
		 *
		 * @param string $service_setting_id the name id of the setting.
		 * @param string $new_status the status for service.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywo_sv_fixed_service_status( $service_setting_id, $new_status, $service_option ) {

			global $wpdb;

			$service_table  = $wpdb->prefix . 'surelywp_sv_services';  // Replace with your actual table name.
			$contract_table = $wpdb->prefix . 'surelywp_sv_contracts';  // Replace with your actual table name.
			$req_table      = $wpdb->prefix . 'surelywp_sv_requirements';  // Replace with your actual table name.

			$sql = '';
			if ( 'waiting_for_req' === $new_status ) {

				// if requirement already submited then service will be service_start or not then waiting_for_contract.
				$sql = $wpdb->prepare(
					"UPDATE $service_table s
					LEFT JOIN $req_table r ON s.service_id = r.service_id
					SET s.service_status = CASE
						WHEN r.service_id IS NULL THEN 'waiting_for_req'  -- For services without a requirement
						ELSE 'service_start'               -- For services with a requirement
					END
					WHERE s.service_setting_id = %s
					  AND s.service_status = 'waiting_for_contract'",
					$service_setting_id
				);

			} elseif ( 'waiting_for_contract' === $new_status ) {

				$else_status          = 'service_start';
				$ask_for_requirements = $service_option['ask_for_requirements'] ?? '';
				if ( ! empty( $ask_for_requirements ) ) {
					$else_status = 'waiting_for_req';
				}

				// To update the service_status in the service_table where the service_setting_id is equal to a given value, the service_status is 'waiting_for_req', and the corresponding service_id does not exist in the contract_table.
				$sql = $wpdb->prepare(
					"UPDATE $service_table s
					LEFT JOIN $contract_table c ON s.service_id = c.service_id
					SET s.service_status = CASE
						WHEN c.service_id IS NULL THEN 'waiting_for_contract'  -- For services without a contract
						ELSE %s               -- For services with a contract
					END
					WHERE s.service_setting_id = %s
					  AND s.service_status = 'waiting_for_req'",
					$else_status,
					$service_setting_id
				);
			} elseif ( 'service_start' === $new_status ) {
				$sql = "UPDATE $service_table SET service_status = '$new_status' WHERE service_setting_id = '$service_setting_id' AND service_status IN ('waiting_for_req', 'waiting_for_contract')";
			}

			// Execute the query.
			$result = $wpdb->query( $sql );

			// Check if the query was successful.
			if ( false !== $result ) {
				return $result;  // Returns the number of rows affected.
			} else {
				return false;
			}
		}

		/**
		 * Function to cancel the services by service setting id.
		 *
		 * @param string $service_setting_id the name id of the setting.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_cancel_services( $service_setting_id ) {

			global $wpdb;

			$service_table = $wpdb->prefix . 'surelywp_sv_services';  // Replace with your actual table name.

			$sql = "UPDATE $service_table SET service_status = 'service_canceled' WHERE service_setting_id = '$service_setting_id' AND service_status IN ('waiting_for_req', 'waiting_for_contract')";

			// Execute the query.
			$result = $wpdb->query( $sql );

			// Check if the query was successful.
			if ( false !== $result ) {
				return $result;  // Returns the number of rows affected.
			} else {
				return false;
			}
		}

		/**
		 * Function to add recurring service id in service table.
		 *
		 * @package Services For SureCart
		 * @since   1.5
		 */
		public function surelywp_sv_add_recurring_service_id_column() {

			global $wpdb;
			$table_name   = $wpdb->prefix . 'surelywp_sv_services';
			$table_exists = $wpdb->get_var(
				$wpdb->prepare(
					'SHOW TABLES LIKE %s',
					$table_name
				)
			);

			if ( $table_exists ) {

				// Check if the column exists.
				$column_exists = $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM `$table_name` LIKE %s", 'recurring_service_id' ) );

				if ( empty( $column_exists ) ) {
					// Add the column.
					$wpdb->query( "ALTER TABLE `$table_name` ADD `recurring_service_id` BIGINT UNSIGNED NULL DEFAULT NULL" );
				}
			} else {
				error_log( "Table '$table_name' does not exist." );
			}
		}


		/**
		 * Function to add remaining service column in service table.
		 *
		 * @package Services For SureCart
		 * @since   1.5.1
		 */
		public function surelywp_sv_add_remaining_service_column() {

			global $wpdb;
			$table_name   = $wpdb->prefix . 'surelywp_sv_services';
			$table_exists = $wpdb->get_var(
				$wpdb->prepare(
					'SHOW TABLES LIKE %s',
					$table_name
				)
			);

			if ( $table_exists ) {

				// Check if the column exists.
				$column_exists = $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM `$table_name` LIKE %s", 'services_remaining' ) );

				if ( empty( $column_exists ) ) {

					// Add the column.
					$is_added = $wpdb->query( "ALTER TABLE `$table_name` ADD `services_remaining` BIGINT UNSIGNED NULL DEFAULT NULL" );

					// Update the column value. Set it to 0 for all existing service where recurring service is null.
					if ( $is_added ) {
						$wpdb->query( $wpdb->prepare( "UPDATE `$table_name` SET `services_remaining` = %d WHERE `recurring_service_id` IS NULL", 0 ) );
					}
				}
			} else {
				error_log( "Table '$table_name' does not exist." );
			}
		}

		/**
		 * Function to get customers recurring Order product ids.
		 *
		 * @package Surelywp Services
		 * @since   1.5
		 */
		public function surelywp_sv_get_customer_recurring_order_ids() {

			global $wpdb;

			$user_id = get_current_user_id();

			$table_name = $wpdb->prefix . 'surelywp_sv_recurring_services';

			$query = "SELECT DISTINCT order_id FROM $table_name WHERE user_id = $user_id ";

			$result = $wpdb->get_col( $query );

			if ( ! empty( $result ) ) {
				return $result;
			}
			return false;
		}

		/**
		 * Function to get customers recurring order product ids.
		 *
		 * @package Surelywp Services
		 * @since   1.5
		 */
		public function surelywp_sv_get_customer_recurring_product_ids() {

			global $wpdb;

			$user_id = get_current_user_id();

			$table_name = $wpdb->prefix . 'surelywp_sv_recurring_services';

			$query = "SELECT DISTINCT product_id FROM $table_name WHERE user_id = $user_id ";

			$result = $wpdb->get_col( $query );

			if ( ! empty( $result ) ) {
				return $result;
			}
			return false;
		}


		/**
		 * Function to get customers recurring order ids and product ids.
		 *
		 * @package Surelywp Services
		 * @since   1.5
		 */
		public function surelywp_sv_get_customer_recurring_order_product_ids() {

			global $wpdb;

			$user_id = get_current_user_id();

			$table_name = $wpdb->prefix . 'surelywp_sv_recurring_services';

			$query = "SELECT order_id, product_id FROM $table_name WHERE user_id = $user_id AND status = 1";

			$result = $wpdb->get_results( $query, ARRAY_A );

			if ( ! empty( $result ) ) {
				return $result;
			}
			return false;
		}

		/**
		 * Function to get customers remainging service order ids and product ids.
		 *
		 * @package Surelywp Services
		 * @since   1.5.1
		 */
		public function surelywp_sv_get_customer_remaining_order_product_ids() {

			global $wpdb;

			$user_id = get_current_user_id();

			$table_name = $wpdb->prefix . 'surelywp_sv_services';

			$query = "SELECT DISTINCT order_id, product_id FROM $table_name WHERE user_id = $user_id AND services_remaining != 0 AND services_remaining IS NOT NULL";

			$result = $wpdb->get_results( $query, ARRAY_A );

			if ( ! empty( $result ) ) {
				return $result;
			}
			return false;
		}

		/**
		 * Function to get recurring service by order and product.
		 *
		 * @param int $order_id The id of the order.
		 * @param int $product_id The id of the product.
		 *
		 * @package Surelywp Services
		 * @since   1.5
		 */
		public function surelywp_sv_get_rc_service_by_order_product( $order_id, $product_id ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_recurring_services';

			$query = "SELECT * FROM $table_name WHERE order_id = '$order_id' AND product_id = '$product_id'";

			$result = $wpdb->get_row( $query, ARRAY_A );

			if ( ! empty( $result ) ) {
				return $result;
			}
			return false;
		}

		/**
		 * Function to get service setting id by order and product.
		 *
		 * @param int $order_id The id of the order.
		 * @param int $product_id The id of the product.
		 *
		 * @package Surelywp Services
		 * @since   1.5
		 */
		public function surelywp_sv_get_sv_setting_id( $order_id, $product_id ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_services';

			$result = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT DISTINCT service_setting_id FROM `$table_name` WHERE `order_id` = %s AND `product_id` = %s",
					$order_id,
					$product_id
				)
			);

			if ( ! empty( $result ) ) {
				return $result;
			}
			return array();
		}


		/**
		 * Function to get remaining service count by order and product.
		 *
		 * @param int $order_id The id of the order.
		 * @param int $product_id The id of the product.
		 *
		 * @package Surelywp Services
		 * @since   1.5
		 */
		public function surelywp_sv_get_remaining_service_count( $order_id, $product_id ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_services';

			$result = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT DISTINCT services_remaining FROM `$table_name` WHERE `order_id` = %s AND `product_id` = %s",
					$order_id,
					$product_id
				)
			);

			if ( ! empty( $result ) ) {
				return $result;
			}
			return array();
		}

		/**
		 * Function to update service.
		 *
		 * @param array $where the where to update.
		 * @param array $data the service data.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_update_service_data( $where, $data ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_services';

			$result = $wpdb->update( $table_name, $data, $where );

			if ( $result ) {
				return $result;
			}
			return false;
		}


		/**
		 * Function to get recurring service by order and product.
		 *
		 * @param int $id The id of the recurring service setting id.
		 * @package Surelywp Services
		 * @since   1.5
		 */
		public function surelywp_sv_get_rc_settings_by_rc_id( $id ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_recurring_services_setting';

			$query = "SELECT * FROM $table_name WHERE recurring_service_id = $id";

			$result = $wpdb->get_row( $query, ARRAY_A );

			if ( ! empty( $result ) ) {
				return $result;
			}
			return false;
		}

		/**
		 * Function to get recurring service by subsctiption id.
		 *
		 * @param int $subscription_id The id of subscription.
		 * @package Surelywp Services
		 * @since   1.5
		 */
		public function surelywp_sv_get_rc_by_sub_id( $subscription_id ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_recurring_services';

			$query = "SELECT * FROM $table_name WHERE subscription_id = '$subscription_id'";

			$result = $wpdb->get_row( $query, ARRAY_A );

			if ( ! empty( $result ) ) {
				return $result;
			}
			return false;
		}

		/**
		 * Function to get recurring service by id.
		 *
		 * @param int $id The id of the recurring service setting id.
		 * @package Surelywp Services
		 * @since   1.5
		 */
		public function surelywp_sv_get_rc_service( $id ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_recurring_services';

			$query = "SELECT * FROM $table_name WHERE id = $id";

			$result = $wpdb->get_row( $query, ARRAY_A );

			if ( ! empty( $result ) ) {
				return $result;
			}
			return false;
		}


		/**
		 * Function to update recurring service quota.
		 *
		 * @param string $id the recurring service id.
		 * @param int    $quota the amount of available quota.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_update_rc_quota( $id, $quota ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_recurring_services';

			// Data to update.
			$data = array(
				'quota' => $quota,
			);

			$where = array(
				'id' => $id,
			);

			$result = $wpdb->update( $table_name, $data, $where );

			if ( $result ) {
				return $result;
			}
			return false;
		}

		/**
		 * Function to update recurring service quota.
		 *
		 * @param string $id the recurring service id.
		 * @param array  $data the recurring service data.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_update_rc_service( $id, $data ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_recurring_services';

			$where = array(
				'id' => $id,
			);

			$result = $wpdb->update( $table_name, $data, $where );

			if ( $result ) {
				return $result;
			}
			return false;
		}


		/**
		 * Function to update recurring service quota.
		 *
		 * @param array $where the where to update.
		 * @param array $data the recurring service data.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_update_rc_service_data( $where, $data ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_recurring_services';

			$result = $wpdb->update( $table_name, $data, $where );

			if ( $result ) {
				return $result;
			}
			return false;
		}

		/**
		 * Function to update recurring service settings.
		 *
		 * @param array $where the where to update.
		 * @param array $data the recurring service settings data.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_update_rc_settings( $where, $data ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_recurring_services_setting';

			$result = $wpdb->update( $table_name, $data, $where );

			if ( $result ) {
				return $result;
			}
			return false;
		}

		/**
		 * Get the total of remaining service of the custimer.
		 *
		 * Services For SureCart
		 *
		 * @param int $user_id the user id.
		 * @package Services For SureCart
		 * @since   1.5.1
		 */
		public function surelywp_sv_get_customer_remaining_service( $user_id ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_services';

			$result = $wpdb->get_var( "SELECT SUM(services_remaining) AS total_services_remaining FROM ( SELECT DISTINCT order_id, product_id, services_remaining FROM $table_name WHERE user_id = $user_id AND services_remaining != 0 AND services_remaining IS NOT NULL ) AS subquery;" );

			if ( ! empty( $result ) ) {
				return $result;
			} else {
				return '';
			}
		}

		/**
		 * Get the total of recurring service remaining quota of the customer.
		 *
		 * Services For SureCart
		 *
		 * @param int $user_id the user id.
		 * @package Services For SureCart
		 * @since   1.5.1
		 */
		public function surelywp_sv_get_remaining_rc_service( $user_id ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_recurring_services';

			$result = $wpdb->get_var( "SELECT SUM(quota) AS total_quota FROM $table_name WHERE user_id = $user_id AND status = 1;" );

			if ( ! empty( $result ) ) {
				return $result;
			} else {
				return '';
			}
		}

		/**
		 * Get the all Recurring services.
		 *
		 * Services For SureCart
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_all_rc_sv() {

			global $wpdb;

			$table_name = $wpdb->prefix . 'surelywp_sv_recurring_services';

			$result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `$table_name`" ), ARRAY_A );

			if ( ! empty( $result ) ) {
				return $result;
			} else {
				return array();
			}
		}

		/**
		 * Updates the activity table schema for existing installations.
		 *
		 * - Checks if the activity table exists.
		 * - Verifies whether a foreign key from `service_id` to the service table exists.
		 * - Adds the foreign key with ON DELETE CASCADE if it's missing.
		 *
		 * This ensures existing users receive the updated schema without breaking current data.
		 *
		 * @global wpdb $wpdb WordPress database abstraction object.
		 */
		public function surelywp_sv_add_foreign_key_activity_table() {

			global $wpdb;

			$activity_table = $wpdb->prefix . 'surelywp_sv_activities';
			$service_table  = $wpdb->prefix . 'surelywp_sv_services';

			// Check if the activity table exists.
			$table_exists = $wpdb->get_var(
				$wpdb->prepare(
					'SHOW TABLES LIKE %s',
					$wpdb->esc_like( $activity_table )
				)
			);

			if ( $table_exists !== $activity_table ) {
				return; // Table doesn't exist, skip update.
			}

			// Check if the foreign key already exists.
			$foreign_key_exists = $wpdb->get_var(
				"SELECT CONSTRAINT_NAME 
				 FROM information_schema.KEY_COLUMN_USAGE 
				 WHERE TABLE_NAME = '{$activity_table}' 
				 AND REFERENCED_TABLE_NAME = '{$service_table}' 
				 AND CONSTRAINT_SCHEMA = DATABASE()"
			);

			if ( ! $foreign_key_exists ) {
				// Add foreign key with ON DELETE CASCADE.
				$wpdb->query(
					"ALTER TABLE {$activity_table} 
					 ADD CONSTRAINT fk_activity_service_id 
					 FOREIGN KEY (service_id) 
					 REFERENCES {$service_table}(service_id) 
					 ON DELETE CASCADE"
				);
			}
		}


		/**
		 * Updates the requirement table schema for existing installations.
		 *
		 * - Checks if the activity table exists.
		 * - Verifies whether a foreign key from `service_id` to the service table exists.
		 * - Adds the foreign key with ON DELETE CASCADE if it's missing.
		 *
		 * This ensures existing users receive the updated schema without breaking current data.
		 *
		 * @global wpdb $wpdb WordPress database abstraction object.
		 */
		public function surelywp_sv_add_foreign_key_requirement_table() {

			global $wpdb;

			$requirement_table = $wpdb->prefix . 'surelywp_sv_requirements';
			$service_table     = $wpdb->prefix . 'surelywp_sv_services';

			// Check if the activity table exists.
			$table_exists = $wpdb->get_var(
				$wpdb->prepare(
					'SHOW TABLES LIKE %s',
					$wpdb->esc_like( $requirement_table )
				)
			);

			if ( $table_exists !== $requirement_table ) {
				return; // Table doesn't exist, skip update.
			}

			// Check if the foreign key already exists.
			$foreign_key_exists = $wpdb->get_var(
				"SELECT CONSTRAINT_NAME 
				 FROM information_schema.KEY_COLUMN_USAGE 
				 WHERE TABLE_NAME = '{$requirement_table}' 
				 AND REFERENCED_TABLE_NAME = '{$service_table}' 
				 AND CONSTRAINT_SCHEMA = DATABASE()"
			);

			if ( ! $foreign_key_exists ) {
				// Add foreign key with ON DELETE CASCADE.
				$wpdb->query(
					"ALTER TABLE {$requirement_table} 
					 ADD CONSTRAINT fk_requirement_service_id 
					 FOREIGN KEY (service_id) 
					 REFERENCES {$service_table}(service_id) 
					 ON DELETE CASCADE"
				);
			}
		}

		/**
		 * Updates the message table schema for existing installations.
		 *
		 * - Checks if the activity table exists.
		 * - Verifies whether a foreign key from `service_id` to the service table exists.
		 * - Adds the foreign key with ON DELETE CASCADE if it's missing.
		 *
		 * This ensures existing users receive the updated schema without breaking current data.
		 *
		 * @global wpdb $wpdb WordPress database abstraction object.
		 */
		public function surelywp_sv_add_foreign_key_message_table() {

			global $wpdb;

			$message_table = $wpdb->prefix . 'surelywp_sv_messages';
			$service_table = $wpdb->prefix . 'surelywp_sv_services';

			// Check if the activity table exists.
			$table_exists = $wpdb->get_var(
				$wpdb->prepare(
					'SHOW TABLES LIKE %s',
					$wpdb->esc_like( $message_table )
				)
			);

			if ( $table_exists !== $message_table ) {
				return; // Table doesn't exist, skip update.
			}

			// Check if the foreign key already exists.
			$foreign_key_exists = $wpdb->get_var(
				"SELECT CONSTRAINT_NAME 
				 FROM information_schema.KEY_COLUMN_USAGE 
				 WHERE TABLE_NAME = '{$message_table}' 
				 AND REFERENCED_TABLE_NAME = '{$service_table}' 
				 AND CONSTRAINT_SCHEMA = DATABASE()"
			);

			if ( ! $foreign_key_exists ) {
				// Add foreign key with ON DELETE CASCADE.
				$wpdb->query(
					"ALTER TABLE {$message_table} 
					 ADD CONSTRAINT fk_message_service_id 
					 FOREIGN KEY (service_id) 
					 REFERENCES {$service_table}(service_id) 
					 ON DELETE CASCADE"
				);
			}
		}

		/**
		 * Updates the contract table schema for existing installations.
		 *
		 * - Checks if the activity table exists.
		 * - Verifies whether a foreign key from `service_id` to the service table exists.
		 * - Adds the foreign key with ON DELETE CASCADE if it's missing.
		 *
		 * This ensures existing users receive the updated schema without breaking current data.
		 *
		 * @global wpdb $wpdb WordPress database abstraction object.
		 */
		public function surelywp_sv_add_foreign_key_contract_table() {

			global $wpdb;

			$contract_table = $wpdb->prefix . 'surelywp_sv_contracts';
			$service_table  = $wpdb->prefix . 'surelywp_sv_services';

			// Check if the activity table exists.
			$table_exists = $wpdb->get_var(
				$wpdb->prepare(
					'SHOW TABLES LIKE %s',
					$wpdb->esc_like( $contract_table )
				)
			);

			if ( $table_exists !== $contract_table ) {
				return; // Table doesn't exist, skip update.
			}

			// Check if the foreign key already exists.
			$foreign_key_exists = $wpdb->get_var(
				"SELECT CONSTRAINT_NAME 
				 FROM information_schema.KEY_COLUMN_USAGE 
				 WHERE TABLE_NAME = '{$contract_table}' 
				 AND REFERENCED_TABLE_NAME = '{$service_table}' 
				 AND CONSTRAINT_SCHEMA = DATABASE()"
			);

			if ( ! $foreign_key_exists ) {
				// Add foreign key with ON DELETE CASCADE.
				$wpdb->query(
					"ALTER TABLE {$contract_table} 
					 ADD CONSTRAINT fk_contract_service_id 
					 FOREIGN KEY (service_id) 
					 REFERENCES {$service_table}(service_id) 
					 ON DELETE CASCADE"
				);
			}
		}

		/**
		 * Function to add required column for Milestone.
		 *
		 * @package Services For SureCart
		 * @since   1.7
		 */
		public function surelywp_sv_add_column_for_milestone() {

			global $wpdb;
			$table_name_sv_services   = $wpdb->prefix . 'surelywp_sv_services';
			$table_name_sv_messages   = $wpdb->prefix . 'surelywp_sv_messages';
			$table_exists_sv_services = $wpdb->get_var(
				$wpdb->prepare(
					'SHOW TABLES LIKE %s',
					$table_name_sv_services
				)
			);
			$table_exists_sv_messages = $wpdb->get_var(
				$wpdb->prepare(
					'SHOW TABLES LIKE %s',
					$table_name_sv_messages
				)
			);

			if ( $table_exists_sv_services ) {

				// Check if the column exists.
				$column_exists = $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM `$table_name_sv_services` LIKE %s", 'revisions_remaining' ) );

				if ( empty( $column_exists ) ) {

					// Add the column.
					$wpdb->query( "ALTER TABLE `$table_name_sv_services` ADD `revisions_remaining` INT NULL DEFAULT NULL" );
				}
			} else {
				error_log( "Table '$table_name_sv_services' does not exist." );
			}

			if ( $table_exists_sv_messages ) {

				// Check if the column exists.
				$column_exists_service_status = $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM `$table_name_sv_messages` LIKE %s", 'service_status' ) );
				$column_exists_milestone_id   = $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM `$table_name_sv_messages` LIKE %s", 'milestone_id' ) );

				if ( empty( $column_exists_service_status ) ) {
					// Add the column.
					$wpdb->query( "ALTER TABLE `$table_name_sv_messages` ADD `service_status` VARCHAR(50) NULL DEFAULT NULL" );
				}
				if ( empty( $column_exists_milestone_id ) ) {
					// Add the column.
					$wpdb->query( "ALTER TABLE `$table_name_sv_messages` ADD `milestone_id` INT NULL DEFAULT NULL" );
				}
			} else {
				error_log( "Table '$table_name_sv_messages' does not exist." );
			}

			/** Update Milestone status for existing customer  */
			if(  $table_exists_sv_services ) {

				$records = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT * FROM $table_name_sv_services WHERE service_status = %s",
						'service_start'
					)
				);

				// If records exist, update them.
				if ( ! empty( $records ) ) {
					$updated = $wpdb->query(
						$wpdb->prepare(
							"UPDATE $table_name_sv_services SET service_status = %s WHERE service_status = %s",
							'service_start_0',
							'service_start'
						)
					);
				}
			}
		}

		/**
		 * Function to delete service by id.
		 *
		 * @param string $service_id The id of the service.
		 */
		public function surelywp_sv_delete_service_by_id( $service_id ) {

			global $wpdb;
			$table = $wpdb->prefix . 'surelywp_sv_services';
			$wpdb->delete( $table, array( 'service_id' => $service_id ), array( '%d' ) );
		}
	}
}

$surelywp_sv_model = Surelywp_Services_Model::instance();
