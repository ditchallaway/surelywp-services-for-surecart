<?php
/**
 * Main class for the SurelyWP Services plugin.
 *
 * @package Services For SureCart
 * @author  SurelyWP
 * @version 1.0.0
 */

// Check if the main plugin constant is defined, exit if not defined.
if ( ! defined( 'SURELYWP_SERVICES' ) ) {
	exit;
}

use SureCart\Models\Product;
use SureCart\Models\Price;
use SureCart\Models\Subscription;
use SureCart\Models\Customer;
use SureCart\Models\Checkout;
use SureCart\Models\Order;
use SureCart\Models\ApiToken;
use SureCart\Models\ProductCollection;
use SureCart\Models\RegisteredWebhook;
use SureCart\Models\LineItem;

if ( ! class_exists( 'Surelywp_Services' ) ) {

	/**
	 * Main Services Class
	 *
	 * @package Services For SureCart
	 * @since   1.0.0
	 */
	class Surelywp_Services {


		/**
		 * Single instance of the class
		 *
		 * @var \Surelywp_Services
		 */
		protected static $instance;


		/**
		 * Returns single instance of the class
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 * @return  \Surelywp_Services
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor function for the Surelywp Services class.
		 *
		 * Initializes the class and sets up various actions and filters.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function __construct() {

			// Check Licence and Reports is enable or not.
			$activation = surelywp_check_license_avtivation( SURELYWP_SERVICES_PLUGIN_TITLE, SURELYWP_SERVICES_FILE );

			if ( isset( $activation['sc_activation_id'] ) && ! empty( $activation ) ) {

				// Enqueue sctipt and styles.
				add_action( 'wp_enqueue_scripts', array( $this, 'surelywp_sv_front_script' ) );

				// Override Surecart Customer Dashboard.
				add_filter( 'template_include', array( $this, 'surelywp_sv_override_template' ) );

				// add service menu to surecart navation.
				add_filter( 'surelywp_surecart_customer_dashboard_data', array( $this, 'surelywp_sv_add_customer_service_menu' ), 10, 2 );

				// add customer service content.
				add_action( 'surelywp_surecart_dashboard_right', array( $this, 'surelywp_sv_surecart_dashboard_right_content' ) );
				add_filter( 'the_content', array( $this, 'surelywp_sv_view_customer_service' ) );

				// Add Html blocks on footer.
				add_action( 'wp_footer', array( $this, 'surelywp_sv_add_blocks' ) );

				// send mail for service requirement submit reminder.
				add_action( 'surelywp_sv_send_req_reminder_email', array( $this, 'surelywp_sv_send_service_email' ), 10, 2 );

				// auto complete service cron.
				add_action( 'surelywp_sv_auto_complete_service', array( $this, 'surelywp_sv_auto_complete_service_callback' ), 10, 1 );

				// Daily update service cron.
				add_action( 'surelywp_sv_daily_updates', array( $this, 'surelywp_sv_daily_updates' ) );

				// when purchase revoked.
				add_action( 'surecart/purchase_revoked', array( $this, 'surelywp_sv_cancel_service_on_revoked' ), 10, 1 );

				// For Service creation.
				add_action( 'surecart/purchase_created', array( $this, 'surelywp_sv_create_service_at_purchase' ), 10, 1 );

				// Add Customer Services Shortcode.
				add_shortcode( 'surelywp_customer_services', array( $this, 'surelywp_sv_customer_services_shortcode' ) );
				add_filter( 'surelywp_sv_customer_services_shortcode_content', array( $this, 'surelywp_sv_customer_services_shortcode_callback' ), 10, 2 );

				// Add Services Alert Shortcode.
				add_shortcode( 'surelywp_services_alert', array( $this, 'surelywp_sv_services_alert_shortcode' ) );
				add_filter( 'surelywp_sv_services_alert_shortcode_content', array( $this, 'surelywp_sv_services_alert_shortcode_callback' ), 10, 4 );

				// Add Services Requirements From Shortcode.
				add_shortcode( 'surelywp_services_requirements_form', array( $this, 'surelywp_sv_req_form_shortcode' ) );
				add_filter( 'surelywp_services_requirements_form_content', array( $this, 'surelywp_sv_req_form_shortcode_callback' ), 10, 1 );

				add_shortcode( 'surelywp_services_checkout_requirements', array( $this, 'surelywp_sv_product_requirements_shortcode' ) );
				add_filter( 'surelywp_services_product_requirements_content', array( $this, 'surelywp_sv_product_requirements_shortcode_callback' ) );

				// Set custom redirect url for after checkout.
				add_action( 'template_redirect', array( $this, 'surelywp_sv_redirect_to_services' ) );

				// Add Services alert on customer dashboard.
				add_filter( 'surecart/dashboard/block/before', array( $this, 'surelywp_sv_before_customer_dashboard_blocks' ), 10, 2 );

				// Display requirement form.
				add_filter( 'render_block', array( $this, 'surelywp_sv_render_requirement_form' ), 11, 2 );

				add_action( 'init', array( $this, 'surelywp_sv_init' ) );

				// Update the Rucurring service on subscription update.
				add_action( 'surecart/subscription_renewed', array( $this, 'surelywp_sv_subscription_renewed' ), 10, 2 );

				// Canceled the rucurring service.
				add_action( 'surecart/subscription_canceled', array( $this, 'surelywp_sv_subscription_canceled' ), 10, 2 );

				add_filter( 'surecart/request/args', array( $this, 'surelywp_sv_sc_request' ), 10, 2 );

				// Remove the notice of the User Registration & Membership plugin.
				add_filter( 'surelywp_wc_plugin_panel_current_tab', array( $this, 'surelywp_sv_remove_urm_plugin_notice' ) );

				// change the guest transient key to user transient key.
				add_action( 'surecart/order_created', array( $this, 'surelywp_sv_change_transient_guest_to_userid' ) );
				add_action( 'surecart/checkout_confirmed', array( $this, 'surelywp_sv_change_transient_guest_to_userid' ), 10, 2 );

				add_action( 'wp_ajax_surelywp_sv_delete_transient_empty_cart', array( $this, 'surelywp_sv_delete_transient_empty_cart' ) );
				add_action( 'wp_ajax_nopriv_surelywp_sv_delete_transient_empty_cart', array( $this, 'surelywp_sv_delete_transient_empty_cart' ) );
			}
		}

		/**
		 * Delete the transient if present in database when cart is empty.
		 *
		 * @package Surelywp Services
		 * @since 1.5.7
		 */
		public function surelywp_sv_delete_transient_empty_cart() {
			if ( isset( $_POST['lineItemsCount'] ) ) {
				$line_items_count       = sanitize_text_field( $_POST['lineItemsCount'] );
				$transient_key          = '';
				$user_id = get_current_user_id();
				if ( $line_items_count == 0 ) {
					if ( is_user_logged_in() ) {
						$transient_key = 'surelywp_sv_product_requirements_user_' . $user_id;
					} elseif ( isset( $_COOKIE['surelywp_guest_id'] ) && ! empty( $_COOKIE['surelywp_guest_id'] ) ) {
						$guest_id      = sanitize_text_field( wp_unslash( $_COOKIE['surelywp_guest_id'] ) );
						$transient_key = 'surelywp_sv_product_requirements_guest_' . $guest_id;
					}
					if ( false !== get_transient( $transient_key ) ) {
						delete_transient( $transient_key );
						wp_send_json_success( array( 'message' => esc_html__( 'Transient deleted successfully.', 'surelywp-services' ) ) );
					} else {
						wp_send_json_success(
							array(
								'message' => esc_html__( "Transient key not found.", 'surelywp-services' ),
							)
						);
					}
				}
			} else {
				wp_send_json_error( array( 'message' => 'No cart items found to delete' ) );
			}
			wp_die();
		}

		/**
		 * Remove the notice of the User Registration & Membership plugin.
		 *
		 * @param string $current_tab The setting current tab.
		 *
		 * @package Surelywp Services
		 * @since 1.5.2
		 */
		public function surelywp_sv_remove_urm_plugin_notice( $current_tab ) {

			if ( ! empty( $current_tab ) && class_exists( 'UR_Admin_Notices' ) ) {
				remove_action( 'admin_print_styles', array( 'UR_Admin_Notices', 'add_notices' ) );
			}
			return $current_tab;
		}

		/**
		 * Get service singular name.
		 *
		 * @package Surelywp Services
		 * @since 1.5.1
		 */
		public static function get_sv_singular_name() {

			$service_singular_name = self::get_sv_gen_option( 'service_singular_name' );
			if ( empty( $service_singular_name ) ) {
				$service_singular_name = esc_html__( 'Service', 'surelywp-services' );
			}

			return $service_singular_name;
		}

		/**
		 * Get service plural name.
		 *
		 * @package Surelywp Services
		 * @since 1.5.1
		 */
		public static function get_sv_plural_name() {

			$service_plural_name = self::get_sv_gen_option( 'service_plural_name' );
			if ( empty( $service_plural_name ) ) {
				$service_plural_name = esc_html__( 'Services', 'surelywp-services' );
			}
			return $service_plural_name;
		}

		/**
		 * Service Daily updates.
		 *
		 * @package Surelywp Services
		 * @since 1.5.1
		 */
		public function surelywp_sv_daily_updates() {

			global $surelywp_sv_model;

			// Update the customer recurring service quota for custom frequency.
			$rc_services = $surelywp_sv_model->surelywp_sv_get_all_rc_sv();
			if ( ! empty( $rc_services ) ) {
				foreach ( $rc_services as $rc_service ) {
					$current_time   = current_time( 'mysql' );
					$next_update_on = $rc_service['next_update_on'] ?? '';
					if ( $current_time >= $next_update_on ) {
						$this->surelywp_sv_update_recurring_service_quota( $rc_service );
					}
				}
			}
		}

		/**
		 * Surecart Request.
		 *
		 * @param array  $args The request args.
		 * @param string $endpoint The request endponint.
		 * @package Surelywp Services
		 * @since 1.0.3
		 */
		public function surelywp_sv_sc_request( $args, $endpoint ) {

			// Remove the product requirement from the transient when customer remove product form cart.
			if ( strpos( $endpoint, 'line_items' ) === 0 && isset( $args['method'] ) && 'DELETE' === $args['method'] ) {

				$endpoint_parts = explode( '/', $endpoint );
				$line_item_id   = $endpoint_parts[1] ?? '';
				if ( $line_item_id ) {
					$line_item_obj = LineItem::with( array( 'price', 'price.product' ) )->find( $line_item_id );
					$product_id    = $line_item_obj->price->product->id ?? '';
					$user_id       = get_current_user_id();
					if ( ! empty( $product_id ) && ! empty( $user_id ) ) {
						$transient_data = self::surelywp_get_product_requirements( $product_id, $user_id );
						if ( isset( $transient_data ) && ! empty( $transient_data ) ) {
							$this->surelywp_sv_delete_transient_product_requirements( $product_id, $user_id );
						}
					}
				}
			}
			return $args;
		}

		/**
		 * Handles the subscription canceled event.
		 *
		 * This function is triggered when a subscription is successfully renewed.
		 * It processes the subscription and the associated webhook data.
		 *
		 * @param object $subscription  The subscription object containing details about the renewed subscription.
		 * @param array  $webhook_data  The webhook data array received during the subscription renewal process.
		 * @package Surelywp Services
		 * @since   1.5
		 */
		public function surelywp_sv_subscription_canceled( $subscription, $webhook_data ) {

			global $surelywp_sv_model;

			$subscription_id = $subscription->id ?? '';

			if ( $subscription_id ) {

				$where = array(
					'subscription_id' => $subscription_id,
				);

				$rc_data['status'] = 0;

				// canceled the recurring service.
				$surelywp_sv_model->surelywp_sv_update_rc_service_data( $where, $rc_data );
			}
		}

		/**
		 * Handles the subscription renewal event.
		 *
		 * This function is triggered when a subscription is successfully renewed.
		 * It processes the subscription and the associated webhook data.
		 *
		 * @param object $subscription  The subscription object containing details about the renewed subscription.
		 * @param array  $webhook_data The webhook data array received during the subscription renewal process.
		 * @package Surelywp Services
		 * @since   1.5
		 */
		public function surelywp_sv_subscription_renewed( $subscription, $webhook_data ) {

			global $surelywp_sv_model;
			$subscription_id = $subscription->id ?? '';
			if ( $subscription_id ) {
				$rc_service = $surelywp_sv_model->surelywp_sv_get_rc_by_sub_id( $subscription_id );
				if ( $rc_service ) {
					$rc_service_id = $rc_service['id'];
					$quota         = $rc_service['quota'];
					if ( $rc_service_id ) {

						// Update the Rucurring service settings.
						$rc_sv_settings = self::surelywp_sv_update_rc_sv_settings( $rc_service, $subscription );

						$is_enable_recurring_services = $rc_sv_settings['enable_recurring_services'];
						$is_auto_create_new_service   = $rc_sv_settings['is_auto_create_new_service'];
						$recurring_based_on           = $rc_sv_settings['recurring_based_on'];
						$number_of_services_allow     = $rc_sv_settings['number_of_services_allow'];
						$rollover                     = $rc_sv_settings['rollover'];

						// Check product still available on service.
						$service_setting_id = $rc_service['service_setting_id'];
						$service_status     = self::get_sv_option( $service_setting_id, 'status' );

						// If service status is disable or product is remove from service then Recurring service status will be 0.
						if ( $service_status ) {
							$is_have = self::surelywp_sv_is_product_on_sv( $service_setting_id, $rc_service['product_id'] );
							if ( ! $is_have ) {
								$is_enable_recurring_services = 0;
							}
						} else {
							$is_enable_recurring_services = 0;
						}

						if ( ! $is_enable_recurring_services ) {

							$rc_data['status'] = 0;
							$surelywp_sv_model->surelywp_sv_update_rc_service( $rc_service_id, $rc_data );

						} elseif ( 'subscription_cycle' === $recurring_based_on ) {

							if ( 'rollover' === $rollover ) {
								$quota += $number_of_services_allow;
							} elseif ( 'expire' === $rollover ) {
								$quota = $number_of_services_allow;
							}
							self::surelywp_sv_set_timezone();
							$rc_data = array(
								'quota'          => $is_auto_create_new_service ? $quota - 1 : $quota,
								'next_update_on' => date( 'Y-m-d H:i:s', $subscription->current_period_end_at ?? '' ),
							);

							$is_updated = $surelywp_sv_model->surelywp_sv_update_rc_service( $rc_service_id, $rc_data );

							if ( $is_updated && $is_auto_create_new_service ) {

								$ask_for_requirements = self::get_sv_option( $service_setting_id, 'ask_for_requirements' );
								$ask_for_contract     = self::get_sv_option( $service_setting_id, 'ask_for_contract' );

								$delivery_date = null;
								if ( ! empty( $ask_for_contract ) && ! empty( $ask_for_requirements ) ) {
									$service_status = 'waiting_for_contract';
								} elseif ( empty( $ask_for_contract ) && ! empty( $ask_for_requirements ) ) {
									$service_status = 'waiting_for_req';
								} elseif ( ! empty( $ask_for_contract ) && empty( $ask_for_requirements ) ) {
									$service_status = 'waiting_for_contract';
								} elseif ( empty( $ask_for_contract ) && empty( $ask_for_requirements ) ) {
									$service_status      = 'service_start_0';
									$delivery_date       = $this->surelywp_sv_calculate_delivery_date( $service_setting_id );
									$revisions_remaining = $this->surelywp_sv_get_revisions_allowed( $service_setting_id, 0 );
								}

								$service_data = array(
									'service_setting_id'   => $service_setting_id,
									'order_id'             => $rc_service['order_id'],
									'product_id'           => $rc_service['product_id'],
									'service_status'       => $service_status,
									'delivery_date'        => $delivery_date,
									'user_id'              => $rc_service['user_id'],
									'recurring_service_id' => $rc_service_id,
									'revisions_remaining'  => $revisions_remaining,
								);

								$this->surelywp_sv_create_rc_service( $service_data );
							}
						}
					}
				}
			}
		}

		/**
		 * Function to update recurring service settings.
		 *
		 * @param string $service_setting_id the service setting id.
		 * @param string $product_id the service product id.
		 *
		 * @package Surelywp Services
		 * @since   1.5
		 */
		public static function surelywp_sv_is_product_on_sv( $service_setting_id, $product_id ) {

			$services_product_type = self::get_sv_option( $service_setting_id, 'services_product_type' );
			if ( 'all' === $services_product_type ) {
				return true;
			} elseif ( 'specific' === $services_product_type ) {
				$service_products = self::get_sv_option( $service_setting_id, 'service_products' );
				if ( $product_id && $service_products && in_array( $product_id, $service_products, true ) ) {
					return true;
				}
			} elseif ( 'specific_collection' === $services_product_type ) {
				$service_products_collections = self::get_sv_option( $service_setting_id, 'service_products_collections' );
				if ( ! empty( $service_products_collections ) ) {
					foreach ( $service_products_collections as $collection_id ) {
						$collection_product_ids = self::surelywp_sv_get_collection_product_ids( $collection_id );
						if ( $product_id && $collection_product_ids && in_array( $product_id, $collection_product_ids, true ) ) {
							return true;
						}
					}
				}
			}

			return false;
		}

		/**
		 * Function to update recurring service settings.
		 *
		 * @param array  $rc_service the rurring service.
		 * @param object $subscription_obj the subscription object.
		 *
		 * @package Surelywp Services
		 * @since   1.5
		 */
		public static function surelywp_sv_update_rc_sv_settings( $rc_service, $subscription_obj ) {

			global $surelywp_sv_model;
			$service_setting_id           = $rc_service['service_setting_id'];
			$rc_service_id                = $rc_service['id'];
			$is_enable_recurring_services = self::get_sv_option( $service_setting_id, 'is_enable_recurring_services' );
			$number_of_services_allow     = self::get_sv_option( $service_setting_id, 'number_of_services_allow' );
			$recurring_based_on           = self::get_sv_option( $service_setting_id, 'recurring_based_on' );
			$custom_frequency             = self::get_sv_option( $service_setting_id, 'custom_frequency' );
			$is_auto_create_new_service   = self::get_sv_option( $service_setting_id, 'is_auto_create_new_service' );
			$rollover                     = self::get_sv_option( $service_setting_id, 'rollover' );

			$frequency                = '';
			$recurring_interval_count = 1;
			if ( 'subscription_cycle' === $recurring_based_on ) {
				$price_id                 = $subscription_obj->price ?? '';
				$price_obj                = Price::find( $price_id );
				$recurring_interval_count = $price_obj->recurring_interval_count;
				if ( 'month' === $price_obj->recurring_interval ) {
					$frequency = 'monthly';
				} elseif ( 'week' === $price_obj->recurring_interval ) {
					$frequency = 'weekly';
				} elseif ( 'year' === $price_obj->recurring_interval ) {
					$frequency = 'yearly';
				} elseif ( 'day' === $price_obj->recurring_interval ) {
					$frequency = 'daily';
				}
			} elseif ( 'custom_frequency' === $recurring_based_on ) {

				$frequency = $custom_frequency;
			}

			$recurring_service_setting_data = array(
				'enable_recurring_services'  => $is_enable_recurring_services,
				'number_of_services_allow'   => $number_of_services_allow,
				'recurring_based_on'         => $recurring_based_on,
				'recurring_interval_count'   => $recurring_interval_count,
				'frequency'                  => $frequency,
				'is_auto_create_new_service' => $is_auto_create_new_service,
				'rollover'                   => $rollover,
			);

			$where = array(
				'recurring_service_id' => $rc_service_id,
			);

			// Update the db table.
			$surelywp_sv_model->surelywp_sv_update_rc_settings( $where, $recurring_service_setting_data );

			return $recurring_service_setting_data;
		}

		/**
		 * Function to update recurring service quota.
		 *
		 * @param array $rc_service Update the recurring service.
		 *
		 * @package Surelywp Services
		 * @since   1.5
		 */
		public static function surelywp_sv_update_recurring_service_quota( $rc_service ) {

			global $surelywp_sv_model;
			$rc_service_id  = $rc_service['id'] ?? '';
			$current_time   = current_time( 'mysql' );
			$next_update_on = $rc_service['next_update_on'] ?? '';

			$rc_sv_settings           = $surelywp_sv_model->surelywp_sv_get_rc_settings_by_rc_id( $rc_service_id );
			$number_of_services_allow = $rc_sv_settings['number_of_services_allow'];
			$recurring_based_on       = $rc_sv_settings['recurring_based_on'];
			$frequency                = $rc_sv_settings['frequency'];
			$rollover                 = $rc_sv_settings['rollover'];

			if ( 'custom_frequency' === $recurring_based_on ) {

				$quota = $rc_service['quota'];
				while ( $current_time >= $next_update_on ) {
					$quota         += $number_of_services_allow;
					$next_update_on = self::surelyewp_sv_cal_next_update_time_for_cf( $frequency, $next_update_on );
				}

				if ( 'expire' === $rollover ) {
					$quota = $number_of_services_allow;
				}

				$rc_data = array(
					'quota'          => $quota,
					'next_update_on' => $next_update_on,
				);

				$is_updated = $surelywp_sv_model->surelywp_sv_update_rc_service( $rc_service_id, $rc_data );

				if ( $is_updated ) {

					$rc_service['quota']          = $quota;
					$rc_service['next_update_on'] = $next_update_on;

					return $rc_service;
				}
			}

			return $rc_service;
		}

		/**
		 * Function to calculate recurring service next update time for custom frequency.
		 *
		 * @param string $frequency The Custome Frequency.
		 * @param string $next_update_on The next update datetime.
		 *
		 * @package Services For SureCart
		 * @since   1.5
		 */
		public static function surelyewp_sv_cal_next_update_time_for_cf( $frequency, $next_update_on ) {

			self::surelywp_sv_set_timezone();
			$date = new DateTime( $next_update_on );
			if ( 'daily' === $frequency ) {
				$date->add( new DateInterval( 'P1D' ) ); // Add 1 day.
			} elseif ( 'weekly' === $frequency ) {
				$date->add( new DateInterval( 'P1W' ) ); // Add 1 Week.
			} elseif ( 'monthly' === $frequency ) {
				$date->add( new DateInterval( 'P1M' ) ); // Add 1 month.
			} elseif ( 'yearly' === $frequency ) {
				$date->add( new DateInterval( 'P1Y' ) ); // Add 1 year.
			}

			return $date->format( 'Y-m-d H:i:s' );
		}

		/**
		 * Function to get customers recurring order ids.
		 *
		 * @package Surelywp Services
		 * @since   1.5
		 */
		public static function surelywp_sp_get_customer_recurring_service_selection() {

			global $surelywp_sv_model;

			$options = $surelywp_sv_model->surelywp_sv_get_customer_recurring_order_product_ids();

			$order_ids   = array();
			$product_ids = array();

			if ( ! empty( $options ) ) {
				foreach ( $options as $item ) {
					$order_ids[]   = $item['order_id'];
					$product_ids[] = $item['product_id'];
				}
			}

			// Remove duplicates.
			$order_ids   = array_unique( $order_ids );
			$product_ids = array_unique( $product_ids );

			$order_obj   = Order::where( array( 'ids' => $order_ids ) )->get();
			$product_obj = Product::where( array( 'ids' => $product_ids ) )->get();

			$product_options = array();
			if ( ! is_wp_error( $product_obj ) && ! empty( $product_obj ) ) {
				foreach ( $product_obj as $product ) {
					$product_options[ $product->id ] = $product->name;
				}
			}

			$order_options = array();
			if ( ! is_wp_error( $order_obj ) && ! empty( $order_obj ) ) {
				foreach ( $order_obj as $order ) {
					$order_options[ $order->id ] = $order->number;
				}
			}

			$selection_options = array();
			if ( ! empty( $options ) ) {
				foreach ( $options as $option ) {
					$selection_options[] = array(
						'label' => $order_options[ $option['order_id'] ] . ' - ' . $product_options[ $option['product_id'] ],
						'value' => $option['order_id'] . '-surelywp-sv-separate-' . $option['product_id'],
					);
				}
			}
			return $selection_options;
		}

		/**
		 * Function to get customers remaining services.
		 *
		 * @package Surelywp Services
		 * @since   1.5
		 */
		public static function surelywp_sp_get_customer_remaining_service_selection() {

			global $surelywp_sv_model;

			$options     = $surelywp_sv_model->surelywp_sv_get_customer_remaining_order_product_ids();
			$order_ids   = array();
			$product_ids = array();

			if ( ! empty( $options ) ) {
				foreach ( $options as $item ) {
					$order_ids[]   = $item['order_id'];
					$product_ids[] = $item['product_id'];
				}
			}

			// Remove duplicates.
			$order_ids   = array_unique( $order_ids );
			$product_ids = array_unique( $product_ids );

			$order_obj   = Order::where( array( 'ids' => $order_ids ) )->get();
			$product_obj = Product::where( array( 'ids' => $product_ids ) )->get();

			$product_options = array();
			if ( ! is_wp_error( $product_obj ) && ! empty( $product_obj ) ) {
				foreach ( $product_obj as $product ) {
					$product_options[ $product->id ] = $product->name;
				}
			}

			$order_options = array();
			if ( ! is_wp_error( $order_obj ) && ! empty( $order_obj ) ) {
				foreach ( $order_obj as $order ) {
					$order_options[ $order->id ] = $order->number;
				}
			}

			$selection_options = array();
			if ( ! empty( $options ) ) {
				foreach ( $options as $option ) {
					$selection_options[] = array(
						'label' => $order_options[ $option['order_id'] ] . ' - ' . $product_options[ $option['product_id'] ],
						'value' => $option['order_id'] . '-surelywp-sv-separate-' . $option['product_id'],
					);
				}
			}

			return $selection_options;
		}

		/**
		 * Function check product id is valid.
		 *
		 * @param int $product_id The id of the product.
		 * @package Surelywp Services.
		 * @since   1.4.
		 */
		public static function surelywp_sv_is_valid_product_id( $product_id ) {

			if ( $product_id ) {
				$product = Product::find( $product_id );
				if ( ! empty( $product ) && ! is_wp_error( $product ) ) {
					return true;
				}
			}
		}

		/**
		 * Checking if SureCart version is 3 or higher).
		 *
		 * @package Surelywp Services.
		 * @since   1.4.
		 */
		public static function surelywp_sv_is_sc_v3_or_higher() {

			$current_sc_version = \SureCart::plugin()->version();
			$result             = version_compare( $current_sc_version, '3.0.0', '>=' );
			return $result;
		}

		/**
		 * Function to get product.
		 *
		 * @package Surelywp services.
		 * @since   1.4
		 */
		public static function surelywp_sv_get_current_product() {

			// backwards compatibility.
			if ( get_query_var( 'surecart_current_product' ) ) {
				return get_query_var( 'surecart_current_product' );
			}

			global $post;

			// allow getting the product by sc_id.
			if ( is_string( $post ) ) {
				$posts = get_posts(
					array(
						'post_type'  => 'sc_product',
						'meta_query' => array(
							'key'   => 'sc_id',
							'value' => $post,
						),
					)
				);
				$post  = count( $posts ) > 0 ? $posts[0] : get_post( $post );
			} else {
				$post = get_post( $post );
			}

			// no post.
			if ( ! $post ) {
				return null;
			}

			// get the product.
			$product = get_post_meta( $post->ID, 'product', true );
			if ( empty( $product ) ) {
				return null;
			}

			if ( is_array( $product ) ) {
				$decoded = json_decode( wp_json_encode( $product ) );
				if ( json_last_error() !== JSON_ERROR_NONE ) {
					wp_trigger_error( '', 'JSON decode error: ' . json_last_error_msg() );
				}
				$product = new Product( $decoded );
				return $product;
			}

			// decode the product.
			if ( is_string( $product ) ) {
				$decoded = json_decode( $product );
				if ( json_last_error() !== JSON_ERROR_NONE ) {
					wp_trigger_error( '', 'JSON decode error: ' . json_last_error_msg() );
				}
				$product = new Product( $decoded );
				return $product;
			}

			// return the product.
			return $product;
		}

		/**
		 * Function to display Requirement Form.
		 *
		 * @param array $block_content The Content of block.
		 * @param array $block The name of block.
		 *
		 * @package Surelywp Services
		 * @since   1.4
		 */
		public function surelywp_sv_render_requirement_form( $block_content, $block ) {

			if ( 'surecart/product-buy-buttons' === $block['blockName'] ) {

				$service_form = self::surelywp_sv_is_show_req_form();

				if ( $service_form ) {

					$service_setting_id = $service_form['service_setting_id'];
					$product_id         = $service_form['product_id'];

					// get form position.
					$req_form_position = self::get_sv_option( $service_setting_id, 'req_form_position' );

					if ( 'buy-button' === $req_form_position ) {
						$block_content = $block_content . self::surelywp_sv_get_req_form_html( $service_setting_id, $product_id );
					}
				}
			} elseif ( 'surecart/product-quantity' === $block['blockName'] ) {

				$service_form = self::surelywp_sv_is_show_req_form();
				if ( $service_form ) {
					$service_setting_id = $service_form['service_setting_id'];
					$product_id         = $service_form['product_id'];

					// get form position.
					$req_form_position = self::get_sv_option( $service_setting_id, 'req_form_position' );

					if ( 'quantity-selector' === $req_form_position ) {
						$block_content = $block_content . self::surelywp_sv_get_req_form_html( $service_setting_id, $product_id );
					}
				}
			} elseif ( ( 'surecart/product-price-choices' === $block['blockName'] && ! self::surelywp_sv_is_sc_v3_or_higher() ) || 'surecart/product-price-chooser' === $block['blockName'] ) {

				$service_form = self::surelywp_sv_is_show_req_form();
				if ( $service_form ) {
					$service_setting_id = $service_form['service_setting_id'];
					$product_id         = $service_form['product_id'];

						// get form position.
					$req_form_position = self::get_sv_option( $service_setting_id, 'req_form_position' );

					if ( 'price-choice' === $req_form_position ) {

						$block_content = $block_content . self::surelywp_sv_get_req_form_html( $service_setting_id, $product_id );
					}
				}
			} elseif ( 'surecart/product-title' === $block['blockName'] ) {

				$service_form = self::surelywp_sv_is_show_req_form();
				if ( $service_form ) {
					$service_setting_id = $service_form['service_setting_id'];
					$product_id         = $service_form['product_id'];

					// get form position.
					$req_form_position = self::get_sv_option( $service_setting_id, 'req_form_position' );

					if ( 'title' === $req_form_position ) {

						$block_content = $block_content . self::surelywp_sv_get_req_form_html( $service_setting_id, $product_id );
					}
				}
			} elseif ( ( 'surecart/product-price' === $block['blockName'] && ! self::surelywp_sv_is_sc_v3_or_higher() ) || 'surecart/product-selected-price-fees' === $block['blockName'] ) {

				$service_form = self::surelywp_sv_is_show_req_form();
				if ( $service_form ) {
					$service_setting_id = $service_form['service_setting_id'];
					$product_id         = $service_form['product_id'];

					// get form position.
					$req_form_position = self::get_sv_option( $service_setting_id, 'req_form_position' );

					if ( 'price' === $req_form_position ) {

						$block_content = $block_content . self::surelywp_sv_get_req_form_html( $service_setting_id, $product_id );
					}
				}
			} elseif ( 'surecart/product-description' === $block['blockName'] ) {

				$service_form = self::surelywp_sv_is_show_req_form();
				if ( $service_form ) {
					$service_setting_id = $service_form['service_setting_id'];
					$product_id         = $service_form['product_id'];

					// get form position.
					$req_form_position = self::get_sv_option( $service_setting_id, 'req_form_position' );

					if ( 'description' === $req_form_position ) {

						$block_content = $block_content . self::surelywp_sv_get_req_form_html( $service_setting_id, $product_id );
					}
				}
			}

			return $block_content;
		}

		/**
		 * Function to show is show requirement form on the product page.
		 *
		 * @package Surelywp Services
		 * @since   1.5.1
		 */
		public static function surelywp_sv_is_show_req_form() {

			// If shop page then return.
			$sc_shop_page_id = SureCart::pages()->getId( 'shop' );
			if ( is_page( $sc_shop_page_id ) ) {
				return false;
			}

			$is_user_service_provider = self::surelywp_sv_is_user_service_provider();

			if ( $is_user_service_provider ) {
				return false;
			}

			$product = self::surelywp_sv_get_current_product();

			if ( ! empty( $product ) && ! is_wp_error( $product ) ) {

				$product_id = $product->id ?? '';

				if ( $product_id ) {

					$service_setting_id = self::surelywp_sv_get_product_service_setting_id( $product_id );

					if ( $service_setting_id ) {

						$ask_for_requirements           = self::get_sv_option( $service_setting_id, 'ask_for_requirements' );
						$is_display_req_on_product_page = self::get_sv_option( $service_setting_id, 'is_display_req_on_product_page' );

						if ( $ask_for_requirements && $is_display_req_on_product_page ) {

							return array(
								'product_id'         => $product_id,
								'service_setting_id' => $service_setting_id,
							);
						}
					}
				}
			}

			return false;
		}

		/**
		 * Function handle service init.
		 *
		 * @package Surelywp Services
		 * @since   1.4
		 */
		public function surelywp_sv_init() {

			$webhook = RegisteredWebhook::get();
			$events  = $webhook->webhook_events ?? array();
			if ( ! in_array( 'subscription.renewed', $events ) ) {
				$events         = array_merge( $events, array( 'subscription.renewed' ) );
				$webhook_events = array(
					'webhook_events' => $events,
				);
				RegisteredWebhook::update( $webhook_events );
			}
		}

		/**
		 * Function to update the account webhook events.
		 *
		 * Add Subscription Renewed event.
		 *
		 * @package Surelywp Services
		 * @since   1.5
		 */
		public static function surelywp_sv_update_webhook_events() {

			$webhook = RegisteredWebhook::get();
			$events  = $webhook->webhook_events ?? array();

			$required_events = array( 'subscription.renewed', 'subscription.canceled' );
			$missing_events  = array_diff( $required_events, $events );
			if ( $missing_events ) {

				$events         = array_merge( $events, $missing_events );
				$webhook_events = array(
					'webhook_events' => $events,
				);
				RegisteredWebhook::update( $webhook_events );
			}
		}

		/**
		 * Function get requirement form html.
		 *
		 * @param int $service_setting_id The id of the service setting.
		 * @param int $product_id The id of the product.
		 *
		 * @package Surelywp Services
		 * @since   1.0.1
		 */
		public static function surelywp_sv_get_req_form_html( $service_setting_id, $product_id ) {

			ob_start();
			require SURELYWP_SERVICES_TEMPLATE_PATH . '/customer-services/requirement-form.php';
			return ob_get_clean();
		}

		/**
		 * Function to applicable service id for product.
		 *
		 * @param int $product_id The id of the product.
		 *
		 * @package Surelywp Services
		 * @since   1.0.1
		 */
		public static function surelywp_sv_get_product_service_setting_id( $product_id ) {

			$surelywp_sv_settings_options = self::surelywp_sv_get_sort_options();

			if ( ! empty( $surelywp_sv_settings_options ) ) {

				foreach ( $surelywp_sv_settings_options as $service_setting_id => $options ) {

					$status = $options['status'] ?? '';
					if ( $status ) {

						$services_product_type = $options['services_product_type'];
						if ( 'specific' === $services_product_type ) {

							$service_products = $options['service_products'] ?? array();
							if ( $product_id && $service_products && in_array( $product_id, $service_products, true ) ) {
								return $service_setting_id;
							}
						} elseif ( 'specific_collection' === $services_product_type ) {

							$service_products_collections = $options['service_products_collections'] ?? array();

							if ( ! empty( $service_products_collections ) ) {
								foreach ( $service_products_collections as $collection_id ) {
									$collection_product_ids = self::surelywp_sv_get_collection_product_ids( $collection_id );
									if ( $product_id && $collection_product_ids && in_array( $product_id, $collection_product_ids, true ) ) {
										return $service_setting_id;
									}
								}
							}
						} elseif ( 'all' === $services_product_type ) {

							return $service_setting_id;
						}
					}
				}
			}

			return false;
		}

		/**
		 * Sort tha array for get first specific selection then collection and all.
		 *
		 * @package Services For SureCart
		 * @since 1.4.
		 */
		public static function surelywp_sv_get_sort_options() {

			$surelywp_sv_settings_options = get_option( 'surelywp_sv_settings_options' );
			$surelywp_sv_settings_options = $surelywp_sv_settings_options['surelywp_sv_settings_options'] ?? '';

			if ( ! empty( $surelywp_sv_settings_options ) ) {

				// sort tha array for get first specific selection then collection and all.
				uasort(
					$surelywp_sv_settings_options,
					function ( $a, $b ) {

						// Define the priority order: 'specific' -> 'collection' -> 'all'.
						$priority_order = array(
							'specific'            => 1,
							'specific_collection' => 2,
							'all'                 => 3,
						);

						// Get the product types and assign default priority (high priority for missing/unknown types).
						$services_product_type_a = $priority_order[ $a['services_product_type'] ?? 'all' ] ?? 4;
						$services_product_type_b = $priority_order[ $b['services_product_type'] ?? 'all' ] ?? 4;

						// Compare based on priority.
						return $services_product_type_a <=> $services_product_type_b;
					}
				);

				return $surelywp_sv_settings_options;
			}
		}

		/**
		 * Function to run on plugin active.
		 *
		 * @package Services For SureCart
		 * @since 1.1.2
		 */
		public function surelywp_sv_on_plugin_active() {

			// Services Tables.
			Surelywp_Services_Install()->surelwp_sv_init( true );

			// Update the webhooks.
			$this->surelywp_sv_update_webhook_events();

			// Set the cron for update the recurring service quota for custom frequency.
			self::surelywp_sv_set_cron( array(), 'daily', SURELYWP_SERVICES_DAILY_UPDATES, strtotime( 'midnight' ) );

			// For plugin fw header plugin list.
			$surelywp_addons_json = get_option( 'surelywp_addons_json' );
			$search_slug          = SURELYWP_SERVICES_SLUG;
			if ( empty( $surelywp_addons_json ) ) {
				surelywp_update_addons_json();
			} else {

				// Extract the plugin slugs into a separate array.
				$plugin_slugs = array_map(
					function ( $plugin ) {
						return $plugin->plugin_slug;
					},
					$surelywp_addons_json
				);

				// Check if the desired plugin slug exists in the array.
				if ( in_array( $search_slug, $plugin_slugs, true ) ) {
					return true;
				} else {
					surelywp_update_addons_json(); // for plugin first installation.
				}
			}
		}

		/**
		 * Clear the requirement reminder mail cron if service is already start.
		 *
		 * @param int    $service_id the id of the service.
		 * @param string $template the mail template name.
		 * @package Services For SureCart
		 * @since   1.2.4
		 */
		public function surelywp_sv_clear_req_cron( $service_id, $template = '' ) {

			global $surelywp_sv_model;

			$service_id = intval( $service_id );

			if ( ! empty( $service_id ) ) {
				$service_status = $surelywp_sv_model->surelywp_sv_get_service_status( $service_id );
				if ( 'waiting_for_req' != $service_status ) {

					// Clear requirement reminder main cron.
					$reminder_hook      = SURELYWP_SERVICES_REQ_REMINDER_CRON;
					$reminder_hook_args = array( intval( $service_id ), 'requirement_reminder_email' );
					self::surelywp_sv_unset_cron( $reminder_hook, $reminder_hook_args );
				}
			}
		}

		/**
		 * Get admin emails.
		 *
		 * @package Services For SureCart
		 * @since   1.2.4
		 */
		public static function get_admin_emails() {

			$admin_emails = array();

			// Query users with the specified role using WP_User_Query.
			$user_query = new WP_User_Query(
				array(
					'role'   => array( 'administrator' ),
					'fields' => 'user_email', // Return only user IDs.
				)
			);

			if ( ! empty( $user_query ) ) {

				// Get the results.
				$admin_emails = $user_query->get_results();
			}

			return $admin_emails;
		}

		/**
		 * Check Current user is service provider or customer.
		 *
		 * @package Services For SureCart
		 * @since 1.2.3
		 */
		public static function surelywo_sv_get_user_type() {

			$current_user_id  = get_current_user_id();
			$access_users_ids = self::get_sv_access_users_ids();
			$type             = '';

			if ( ! empty( $access_users_ids ) && in_array( $current_user_id, (array) $access_users_ids ) ) {
				$type = 'service_provider';
			} else {
				$type = 'customer';
			}

			return $type;
		}

		/**
		 * Function to add Notification alert on customer dashboard.
		 *
		 * @param array  $attributes    Array of login form arguments.
		 * @param string $content Content to display. Default empty.
		 *
		 * @package Services For SureCart
		 * @since 1.1
		 */
		public function surelywp_sv_before_customer_dashboard_blocks( $attributes, $content ) {

			global $surelywp_sv_model;
			$model  = isset( $_GET['model'] ) && ! empty( $_GET['model'] ) ? sanitize_text_field( wp_unslash( $_GET['model'] ) ) : '';
			$action = isset( $_GET['action'] ) && ! empty( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';

			if ( empty( $model ) && empty( $action ) ) {

				global $surelywp_sv_model;

				$in_progress_services_count = $surelywp_sv_model->surelywp_sv_get_notification_count() ?? '';

				if ( empty( $in_progress_services_count ) ) {
					return;
				}

				$service_plural_name = self::get_sv_plural_name();
				// translators: %s is the plural name of the services.
				$alert_title         = sprintf( esc_html__( 'Action Required: %s Pending', 'surelywp-services' ), esc_html( $service_plural_name ) );
				// translators: %s is the plural name of the services.
				$alert_desc          = sprintf( esc_html__( 'You have pending %s that need your attention. Please review them promptly and take the necessary actions to ensure timely delivery.', 'surelywp-services' ), esc_html( $service_plural_name ) );

				ob_start();
				require_once SURELYWP_SERVICES_TEMPLATE_PATH . '/customer-services/services-alert.php';
				return ob_get_clean();
			}
		}

		/**
		 * Function to redirect to customer services.
		 *
		 * @package Services For SureCart
		 * @since 1.1
		 */
		public function surelywp_sv_update_version() {

			global $surelywp_sv_model;

			$plugin_version   = get_option( 'surelywp_services_db_version' );
			$is_status_update = get_option( 'surelywp_services_status_updated' );
			$is_cron_fixed    = get_option( 'surelywp_services_cron_fixed' );

			if ( empty( $plugin_version ) ) {

				// Update services setting options for uplode field make required.
				$this->surelywp_sv_update_file_uplode_field_required();
			}

			// add  foreign key.
			if ( ! empty( $plugin_version ) && version_compare( $plugin_version, '1.5.6', '<' ) ) {
				$surelywp_sv_model->surelywp_sv_add_foreign_key_activity_table();
				$surelywp_sv_model->surelywp_sv_add_foreign_key_requirement_table();
				$surelywp_sv_model->surelywp_sv_add_foreign_key_message_table();
				$surelywp_sv_model->surelywp_sv_add_foreign_key_contract_table();
			}

			// add column services remaining on service table .
			if ( ! empty( $plugin_version ) && version_compare( $plugin_version, '1.5.1', '<' ) ) {
				$surelywp_sv_model->surelywp_sv_add_remaining_service_column();
			}

			// add column on service table for recurring services.
			if ( ! empty( $plugin_version ) && version_compare( $plugin_version, '1.5', '<' ) ) {

				$surelywp_sv_model->surelywp_sv_add_recurring_service_id_column();

				// make auto complete toogle enable for exesting customer.
				$sv_update_gen_options = self::get_sv_gen_option( 'sv_update_gen_options' );
				if ( ! empty( $sv_update_gen_options ) ) {
					$surelywp_sv_gen_settings_options = get_option( 'surelywp_sv_gen_settings_options' );
					$surelywp_sv_gen_settings_options['surelywp_sv_gen_settings_options']['is_enable_auto_order_complete'] = '1';
					update_option( 'surelywp_sv_gen_settings_options', $surelywp_sv_gen_settings_options );
				}
			}

			// Update services settings options for required field.
			if ( version_compare( $plugin_version, '1.4', '<' ) ) {
				$this->surelywp_sv_update_field_required_option();
			}

			// add default milestone to services of previous version.
			if ( ! empty( $plugin_version ) && version_compare( $plugin_version, '1.7', '>=' ) ) {
				$this->surelywp_sv_add_default_milestone_field();
				$surelywp_sv_model->surelywp_sv_add_column_for_milestone();
			}

			// Update plugin db version.
			if ( version_compare( $plugin_version, SURELYWP_SERVICES_VERSION, '<' ) ) {
				update_option( 'surelywp_services_db_version', SURELYWP_SERVICES_VERSION );
			}

			// Clear Requirement submit cron for services which are Complete or Cancel but cron still exists.
			if ( empty( $is_cron_fixed ) ) {

				$service_ids = $surelywp_sv_model->surelywp_sv_get_cancel_or_complete_service_ids();
				if ( ! empty( $service_ids ) ) {

					foreach ( $service_ids as $service_id ) {

						// Clear requirement reminder main cron.
						$reminder_hook      = SURELYWP_SERVICES_REQ_REMINDER_CRON;
						$reminder_hook_args = array( intval( $service_id ), 'requirement_reminder_email' );
						self::surelywp_sv_unset_cron( $reminder_hook, $reminder_hook_args );

						// Clear service auto complete hook.
						$auto_complete_sv_hook      = SURELYWP_SERVICES_AUTO_COMPLETE_CRON;
						$auto_complete_sv_hook_args = array( intval( $service_id ) );
						self::surelywp_sv_unset_cron( $auto_complete_sv_hook, $auto_complete_sv_hook_args );
					}
				}

				update_option( 'surelywp_services_cron_fixed', true );
			}

			// service Status updating.
			if ( empty( $is_status_update ) ) {

				// Get services which have service_created or Service_contract_signed status.
				$services = $surelywp_sv_model->surelywp_sv_get_old_status_services();

				if ( ! empty( $services ) ) {

					foreach ( $services as $service ) {

						$service_id         = $service['service_id'];
						$service_setting_id = $service['service_setting_id'];
						$service_status     = $service['service_status'];

						$ask_for_contract     = self::get_sv_option( $service_setting_id, 'ask_for_contract' );
						$ask_for_requirements = self::get_sv_option( $service_setting_id, 'ask_for_requirements' );

						$new_status = '';

						if ( 'service_created' === $service_status ) {
							if ( ! empty( $ask_for_contract ) && ! empty( $ask_for_requirements ) ) {
								$new_status = 'waiting_for_contract';
							} elseif ( empty( $ask_for_contract ) && ! empty( $ask_for_requirements ) ) {
								$new_status = 'waiting_for_req';
							} elseif ( ! empty( $ask_for_contract ) && empty( $ask_for_requirements ) ) {
								$new_status = 'waiting_for_contract';
							} elseif ( empty( $ask_for_contract ) && empty( $ask_for_requirements ) ) {
								$new_status = 'service_start';
							}
						} elseif ( 'service_contract_signed' === $service_status ) {

							if ( ! empty( $ask_for_requirements ) ) {
								$new_status = 'waiting_for_req';
							} else {
								$new_status = 'service_start';
							}
						}

						if ( ! empty( $new_status ) ) {
							// Update on db.
							$surelywp_sv_model->surelywp_sv_update_service_status( $service_id, $new_status );
						}
					}
				}

				update_option( 'surelywp_services_status_updated', true );
			}
		}

		/**
		 * Function to update service requirement field option.
		 *
		 * For make File Uplode filed required.
		 *
		 * @package Services For SureCart
		 * @since 1.1
		 */
		public function surelywp_sv_update_file_uplode_field_required() {

			$surelywp_sv_settings_options_all = get_option( 'surelywp_sv_settings_options' );
			$surelywp_sv_settings_options     = $surelywp_sv_settings_options_all['surelywp_sv_settings_options'] ?? '';

			if ( ! empty( $surelywp_sv_settings_options ) ) {

				foreach ( $surelywp_sv_settings_options as $service_setting_id => $settings ) {

					$req_fields = $settings['req_fields'] ?? '';
					if ( ! empty( $req_fields ) ) {
						foreach ( $req_fields as $key => $field ) {

							$req_field_type = $field['req_field_type'] ?? '';

							if ( 'file' == $req_field_type ) {
								$surelywp_sv_settings_options_all['surelywp_sv_settings_options'][ $service_setting_id ]['req_fields'][ $key ]['is_required_field'] = '1';
							}
						}
					}
				}

				update_option( 'surelywp_sv_settings_options', $surelywp_sv_settings_options_all );
			}
		}

		/**
		 * Function to update service requirement field option.
		 *
		 * For make File Uplode and textarea filed required for old version customer.
		 *
		 * @package Services For SureCart
		 * @since 1.1
		 */
		public function surelywp_sv_update_field_required_option() {

			$surelywp_sv_settings_options_all = get_option( 'surelywp_sv_settings_options' );
			$surelywp_sv_settings_options     = $surelywp_sv_settings_options_all['surelywp_sv_settings_options'] ?? '';

			if ( ! empty( $surelywp_sv_settings_options ) ) {

				foreach ( $surelywp_sv_settings_options as $service_setting_id => $settings ) {

					$req_fields = $settings['req_fields'] ?? '';
					if ( ! empty( $req_fields ) ) {
						foreach ( $req_fields as $key => $field ) {

							$req_field_type          = $field['req_field_type'] ?? '';
							$is_required_file_upload = $field['is_required_file_upload'] ?? '';

							if ( 'file' === $req_field_type && $is_required_file_upload ) {
								$surelywp_sv_settings_options_all['surelywp_sv_settings_options'][ $service_setting_id ]['req_fields'][ $key ]['is_required_field'] = '1';
							} elseif ( 'textarea' === $req_field_type ) {
								$surelywp_sv_settings_options_all['surelywp_sv_settings_options'][ $service_setting_id ]['req_fields'][ $key ]['is_required_field'] = '1';
							}
						}
					}
				}

				update_option( 'surelywp_sv_settings_options', $surelywp_sv_settings_options_all );
			}
		}

		/**
		 * Function to redirect to customer services.
		 *
		 * @package Services For SureCart
		 * @since 1.1
		 */
		public function surelywp_sv_redirect_to_services() {

			global $wp;

			$dashboard_page_id = get_option( 'surecart_dashboard_page_id' ) ?? '';

			$is_redirect_on_customer_services = get_option( 'surelywp_sv_redirect_on_customer_services' ) ? true : false;

			if ( is_page( $dashboard_page_id ) && $is_redirect_on_customer_services ) {

				// Remove option for redirection.
				delete_option( 'surelywp_sv_redirect_on_customer_services' );

				// Get the current URL.
				$current_url = home_url( add_query_arg( array(), $wp->request ) );

				// Add query parameters.
				$redirect_url = add_query_arg(
					array(
						'action' => 'index',
						'model'  => 'services',

					),
					$current_url
				);

				// Redirect to the new URL with query parameters.
				wp_safe_redirect( $redirect_url, 301 );
				exit;
			}
		}

		/**
		 * Function to render coupon code and timer
		 *
		 * @param string $title The title for the block.
		 * @param string $page_id The page id for the service_view.
		 * @package Services For SureCart
		 * @since 1.0.0
		 */
		public function surelywp_sv_customer_services_shortcode_callback( $title, $page_id ) {

			$is_service_list_shortcode = true;
			$heading_title             = $title ?? '';
			$view_page_id              = $page_id ?? '';

			self::surelywp_sv_enqueue_front_script();
			ob_start();
			?>
			<div class="surelyp-sv-customer-services-block">
				<?php include SURELYWP_SERVICES_TEMPLATE_PATH . '/customer-services/services.php'; ?>
			</div>
			<?php
			return ob_get_clean();
		}

		/**
		 * Function to display services alert.
		 *
		 * @param string $alert_title The title for the alert.
		 * @param string $alert_desc The descrioption for the alert.
		 * @param int    $page_id The goto service page id.
		 * @param int    $in_progress_services_count The Count of the Notification.
		 *
		 * @package Services For SureCart
		 * @since 1.0.0
		 */
		public function surelywp_sv_services_alert_shortcode_callback( $alert_title, $alert_desc, $page_id, $in_progress_services_count ) {

			$dashboard_page_id = get_option( 'surecart_dashboard_page_id' ) ?? '';
			if ( ( is_page( $dashboard_page_id ) && ! is_user_logged_in() ) || empty( $in_progress_services_count ) ) {
				return;
			}

			$localize = array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'ajax-nonce' ),
			);

			// Enqueue Script and Styles.
			$script_name = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? 'surelywp-sv-front-shortcode' : 'surelywp-sv-front-shortcode-min';
			$style_name  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? 'surelywp-sv-front' : 'surelywp-sv-front-min';

			wp_enqueue_script( $script_name );
			wp_enqueue_style( $style_name );
			wp_localize_script( $script_name, 'ajax_object', $localize );

			ob_start();
			require SURELYWP_SERVICES_TEMPLATE_PATH . '/customer-services/services-alert.php';
			return ob_get_clean();
		}

		/**
		 * Shortcode for Customer Services to display on any page.
		 *
		 * [surelywp_customer_services]
		 *
		 * @param string $attr The attributes of shortcode.
		 * @param string $content The content of shortcode.
		 * @package Services For SureCart
		 * @since 1.0.0
		 */
		public function surelywp_sv_customer_services_shortcode( $attr, $content ) {

			$dashboard_page_id   = get_option( 'surecart_dashboard_page_id' ) ?? '';
			$service_plural_name = self::get_sv_plural_name();

			$attr = shortcode_atts(
				array(
					'title'   => esc_html( $service_plural_name ),
					'page_id' => $dashboard_page_id,
				),
				$attr
			);

			$content_main  = '';
			$heading_title = $attr['title'] ?? '';
			$view_page_id  = $attr['page_id'] ?? '';
			$content      .= apply_filters( 'surelywp_sv_customer_services_shortcode_content', $heading_title, $view_page_id );
			$content_main  = '<div class="surlywp-sv-customer-services-shortcode">' . do_shortcode( $content ) . '</div>';
			return $content_main;
		}

		/**
		 * Shortcode for Services Alert to display on any page.
		 *
		 * [surelywp_services_alert]
		 *
		 * @param string $attr The attributes of shortcode.
		 * @param string $content The content of shortcode.
		 * @package Services For SureCart
		 * @since 1.0.0
		 */
		public function surelywp_sv_services_alert_shortcode( $attr, $content ) {

			global $surelywp_sv_model;

			$in_progress_services_count = $surelywp_sv_model->surelywp_sv_get_notification_count() ?? '';
			$service_plural_name        = self::get_sv_plural_name();

			$attr = shortcode_atts(
				array(
					// translators: %s is the plural name of the services.
					'title'       => sprintf( esc_html__( 'Action Required: %s Pending', 'surelywp-services' ), esc_html( $service_plural_name ) ),
					// translators: %s is the plural name of the services.
					'description' => sprintf( esc_html__( 'You have pending %s that need your attention. Please review them promptly and take the necessary actions to ensure timely delivery.', 'surelywp-services' ), esc_html( $service_plural_name ) ),
					'page_id'     => '',
				),
				$attr
			);

			$content_main = '';
			$alert_title  = $attr['title'] ?? '';
			$alert_desc   = $attr['description'] ?? '';
			$page_id      = $attr['page_id'] ?? '';
			$alert_desc   = str_replace( '{count}', $in_progress_services_count, $alert_desc );
			$content     .= apply_filters( 'surelywp_sv_services_alert_shortcode_content', $alert_title, $alert_desc, $page_id, $in_progress_services_count );
			$content_main = '<div class="surlywp-sv-services-alert-shortcode">' . do_shortcode( $content ) . '</div>';
			return $content_main;
		}

		/**
		 * Shortcode for Services Requirements Form display on any page.
		 *
		 * [surelywp_services_requirements_form]
		 *
		 * @param string $attr The attributes of shortcode.
		 * @param string $content The content of shortcode.
		 * @package Services For SureCart
		 * @since 1.4
		 */
		public function surelywp_sv_req_form_shortcode( $attr, $content ) {

			$attr = shortcode_atts(
				array(
					'product_id' => '',
				),
				$attr
			);

			$content_main = '';
			$product_id   = '';
			if ( isset( $attr['product_id'] ) && ! empty( $attr['product_id'] ) ) {
				$product_id = $attr['product_id'];
			} else {
				$product = self::surelywp_sv_get_current_product();
				if ( ! empty( $product ) ) {
					$product_id = $product->id;
				}
			}

			$is_user_service_provider = self::surelywp_sv_is_user_service_provider();
			$is_valid_id              = self::surelywp_sv_is_valid_product_id( $product_id );

			if ( $is_valid_id && ! $is_user_service_provider ) {

				$content     .= apply_filters( 'surelywp_services_requirements_form_content', $product_id );
				$content_main = '<div class="surelywp-services-requirements-form-shortcode">' . do_shortcode( $content ) . '</div>';
			}

			return $content_main;
		}

		/**
		 * Shortcode for Services Product Requirements displays.
		 *
		 * [surelywp_services_product_requirements]
		 *
		 * @param string $attr The attributes of shortcode.
		 * @param string $content The content of shortcode.
		 * @package Services For SureCart
		 * @since 1.4
		 */
		public function surelywp_sv_product_requirements_shortcode( $attr, $content ) {
			$content_main = '';
			$content     .= apply_filters( 'surelywp_services_product_requirements_content', $content );
			$content_main = '<div class="surelywp-services-product-requirements-shortcode">' . do_shortcode( $content ) . '</div>';

			return $content_main;
		}

		/**
		 * Function to render services requirements form
		 *
		 * @param int $product_id The id of the product.
		 * @package Services For SureCart
		 * @since 1.5
		 */
		public function surelywp_sv_req_form_shortcode_callback( $product_id ) {

			$service_setting_id = self::surelywp_sv_get_product_service_setting_id( $product_id );

			if ( $service_setting_id ) {

				$ask_for_requirements = $this->get_sv_option( $service_setting_id, 'ask_for_requirements' );

				if ( $ask_for_requirements ) {

					// enqueue requirement form script.
					self::surelywp_sv_enqueue_req_form_script();
					$req_form_html = self::surelywp_sv_get_req_form_html( $service_setting_id, $product_id );
					return $req_form_html;
				}
			}
		}

		/**
		 * Function to render services requirements form
		 *
		 * @package Services For SureCart
		 * @since 1.5
		 */
		public function surelywp_sv_product_requirements_shortcode_callback() {
			self::surelywp_sv_enqueue_product_req_script();
			ob_start();
			require_once SURELYWP_SERVICES_TEMPLATE_PATH . '/customer-services/product-requirements.php';
			return ob_get_clean();
		}

		/**
		 * Funcation to add customer service menu.
		 *
		 * @param array $data the data for customer dashboard.
		 * @param array $controller the controller for customer dashboard.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_add_customer_service_menu( $data, $controller ) {

			// Service tab label.
			$service_tab_lable = self::get_sv_gen_option( 'service_tab_lable' );
			if ( empty( $service_tab_lable ) ) {
				$service_tab_lable = esc_html__( 'Services', 'surelywp-services' );
			}

			// Service tab icon.
			$service_tab_icon = self::get_sv_gen_option( 'service_tab_icon' );
			if ( empty( $service_tab_icon ) ) {
				$service_tab_icon = esc_html( 'check-circle' );
			}

			// Add Sevices Tab.
			$dashboard_url = get_permalink( get_the_ID() );
			$services_url  = add_query_arg(
				array(
					'action' => 'index',
					'model'  => 'services',
				),
				$dashboard_url
			);
			$services_tab  = array(
				'services' => array(
					'icon_name'            => apply_filters( 'surelywp_sv_services_tab_icon_name', $service_tab_icon ),
					'name'                 => apply_filters( 'surelywp_sv_services_tab_name', esc_html( $service_tab_lable ) ),
					'active'               => $controller->isActive( 'services' ),
					'href'                 => $services_url,
					'surelywp_custom_menu' => true,
				),
			);

			$orders_tab_index = array_search( 'orders', array_keys( $data['navigation'] ), true );

			if ( false !== $orders_tab_index ) {

				$data['navigation'] = array_merge(
					array_slice( $data['navigation'], 0, $orders_tab_index + 1 ),
					$services_tab,
					array_slice( $data['navigation'], $orders_tab_index + 1 )
				);

			} else {

				// Handle the case where 'orders' is not found in the navigation array.
				// For example, you might want to append the services tab at the end.
				$data['navigation'] = array_merge( $data['navigation'], $services_tab );
			}

			return $data;
		}

		/**
		 * Funcation to add customer service content.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_surecart_dashboard_right_content() {

			$model = isset( $_GET['model'] ) && ! empty( $_GET['model'] ) ? sanitize_text_field( wp_unslash( $_GET['model'] ) ) : '';
			if ( 'services' === $model ) {

				require SURELYWP_SERVICES_TEMPLATE_PATH . '/customer-services/services.php';
			}
		}

		/**
		 * Funcation to view Customer service.
		 *
		 * @param string $content The page content.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_view_customer_service( $content ) {

			global $post;
			$model                     = isset( $_GET['model'] ) && ! empty( $_GET['model'] ) ? sanitize_text_field( wp_unslash( $_GET['model'] ) ) : '';
			$action                    = isset( $_GET['action'] ) && ! empty( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
			$service_id                = isset( $_GET['service_id'] ) && ! empty( $_GET['service_id'] ) ? sanitize_text_field( wp_unslash( $_GET['service_id'] ) ) : '';
			$is_service_list_shortcode = true;

			if ( strpos( $content, '[surelywp_customer_services' ) !== false ) {
				return $content; // Shortcode found on the page.
			} elseif ( isset( $post->ID ) && ! empty( $post->ID ) && has_block( 'surelywp/surelywp-sv-customer-services', get_post( $post->ID ) ) ) {
				return $content;
			}

			$dashboard_page_id = get_option( 'surecart_dashboard_page_id' );
			if ( 'services' === $model && 'index' === $action && ! empty( $service_id ) && ! is_page( $dashboard_page_id ) ) {
				self::surelywp_sv_enqueue_front_script();
				ob_start();
				echo '<div class="surelyp-sv-customer-services-block">';
				include SURELYWP_SERVICES_TEMPLATE_PATH . '/customer-services/services.php';
				echo '</div>';
				$content .= ob_get_clean();
			}
			return $content;
		}

		/**
		 * Funcation to cancel the service.
		 *
		 * @param object $purchase the object of the purchase.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_cancel_service_on_revoked( $purchase ) {

			if ( is_object( $purchase ) && ! is_wp_error( $purchase ) && ! empty( $purchase ) ) {

				global $surelywp_sv_model;

				$product_id = '';
				$order_id   = $purchase->initial_order ?? '';
				$product    = $purchase->product ?? '';
				if ( is_object( $product ) ) {
					$product_id = $product->id;
				} else {
					$product_id = $product;
				}

				$service_data['service_status'] = 'service_canceled';

				$service = $surelywp_sv_model->surelywp_sv_get_service_by_product( $order_id, $product_id );

				if ( ! empty( $service ) ) {

					$service_status       = $service[0]->service_status;
					$service_id           = $service[0]->service_id;
					$recurring_service_id = $service[0]->recurring_service_id;

					if ( 'service_canceled' !== $service_status && ! $recurring_service_id ) {

						// cancel the service.
						$surelywp_sv_model->surelywp_sv_update_service_by_order( $order_id, $product_id, $service_data );

						// Clear requirement reminder main cron.
						$hook = SURELYWP_SERVICES_REQ_REMINDER_CRON;
						$args = array( intval( $service_id ), 'requirement_reminder_email' );
						self::surelywp_sv_unset_cron( $hook, $args );

						// Clear service auto complete hook.
						$auto_complete_sv_hook      = SURELYWP_SERVICES_AUTO_COMPLETE_CRON;
						$auto_complete_sv_hook_args = array( intval( $service_id ) );
						self::surelywp_sv_unset_cron( $auto_complete_sv_hook, $auto_complete_sv_hook_args );

						/**
						 * Fire on service cancel.
						 */
						do_action( 'surelywp_services_cancel', $service_id, $order_id, $product_id );

						// add activity.
						$activity_data = array(
							'service_id'    => $service_id,
							'activity_type' => 'service_canceled',
						);

						$surelywp_sv_model->surelywp_sv_insert_activity( $activity_data );
					}
				}
			}
		}

		/**
		 * Individual order services lists and notification icon.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_add_blocks() {

			$user_type = self::surelywo_sv_get_user_type();
			if ( 'service_provider' === $user_type ) {
				return '';
			}

			global $surelywp_sv_model;
			$model             = isset( $_GET['model'] ) && ! empty( $_GET['model'] ) ? sanitize_text_field( wp_unslash( $_GET['model'] ) ) : '';
			$action            = isset( $_GET['action'] ) && ! empty( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
			$order_id          = isset( $_GET['id'] ) && ! empty( $_GET['id'] ) ? sanitize_text_field( wp_unslash( $_GET['id'] ) ) : '';
			$dashboard_page_id = get_option( 'surecart_dashboard_page_id' );

			// add services list for customer individual order view.
			if ( 'order' === $model && 'show' === $action && ! empty( $order_id ) ) {

				include SURELYWP_SERVICES_TEMPLATE_PATH . '/customer-services/order-services.php';
			}

			// Add Notification icon.
			if ( ! empty( $dashboard_page_id ) && is_page( $dashboard_page_id ) ) {
				$in_progress_services_count = $surelywp_sv_model->surelywp_sv_get_notification_count();
				if ( $in_progress_services_count ) {
					?>
					<div class="icon-button customer-service-notification-icon hidden" id="customer-service-notification-icon">
						<span class="icon-button__badge"><?php echo esc_html( $in_progress_services_count ); ?></span>
					</div>
					<?php
				}
			}
		}

		/**
		 * Function to create service at purchase it created.
		 *
		 * @param object $purchase The purchase object.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_create_service_at_purchase( $purchase ) {

			if ( is_object( $purchase ) && ! is_wp_error( $purchase ) && ! empty( $purchase ) ) {

				global $surelywp_sv_model;

				$purchase_id     = $purchase->id ?? '';
				$product_id      = '';
				$order_id        = $purchase->initial_order ?? '';
				$product         = $purchase->product ?? '';
				$subscription_id = $purchase->subscription ?? '';

				$is_service_enable = 0;
				$sv_setting_id     = 0;
				$create_rc_sv      = false;
				$subscription_obj  = array();

				if ( is_object( $product ) ) {

					$product_id        = $product->id;
					$is_service_enable = $product->metadata->is_service_enable ?? 0;
					$sv_setting_id     = $product->metadata->service_setting_id ?? 0;

				} else {

					$product_id = $product;
					$product    = SureCart\Models\Product::find( $product_id );
					if ( is_object( $product ) && ! is_wp_error( $product ) ) {

						$is_service_enable = $product->metadata->is_service_enable ?? 0;
						$sv_setting_id     = $product->metadata->service_setting_id ?? 0;
					}
				}

				$customer_id      = $purchase->customer;
				$customer_user_id = '';

				if ( ! empty( $customer_id ) ) {

					$customer = Customer::find( $customer_id );

					$customer_email = $customer->email ?? '';

					if ( ! empty( $customer_email ) ) {

						// Retrieve the user by email.
						$user = get_user_by( 'email', $customer_email );

						if ( ! empty( $user ) ) {
							$customer_user_id = $user->ID;
						}
					}
				}

				// Return if the customer id not found.
				if ( ! $customer_user_id ) {
					return;
				}

				$service = $surelywp_sv_model->surelywp_sv_get_service_by_product( $order_id, $product_id );

				$is_processing = get_transient( $purchase_id . '-surelywp-sv-processing' );

				if ( ! $is_processing && empty( $service ) && ! empty( $product_id ) && ! empty( $order_id ) ) {

					// Set processing true.
					set_transient( $purchase_id . '-surelywp-sv-processing', true, 5 );

					$access_users_ids = self::get_sv_access_users_ids();
					$service_data     = array();

					// skip service creation.
					if ( ! empty( $access_users_ids ) && in_array( $customer_user_id, (array) $access_users_ids ) ) {
						return;
					}

					$surelywp_sv_settings_options = get_option( 'surelywp_sv_settings_options' );
					$surelywp_sv_settings_options = $surelywp_sv_settings_options['surelywp_sv_settings_options'] ?? '';
					$service_status               = '';
					$delivery_date                = null;

					if ( $subscription_id ) {
						$subscription_obj = Subscription::find( $subscription_id );
					}

					if ( ! empty( $surelywp_sv_settings_options ) ) {

						// If Product level setting available.
						$settings_options = $surelywp_sv_settings_options[ $sv_setting_id ] ?? array();
						if ( $is_service_enable && $sv_setting_id && ! empty( $settings_options ) ) {

							$is_enable_recurring_services = $settings_options['is_enable_recurring_services'] ?? '';

							// Check if this Subscription product.
							if ( $is_enable_recurring_services ) { // if Enable Subscription-Based Service Creation is enable.

								if ( empty( $subscription_id ) ) { // product must be a subscription product.
									return '';
								}

								if ( $subscription_obj->finite ) { // product must be a recurring subscription.
									return '';
								}

								$create_rc_sv = true;

							} elseif ( ! empty( $subscription_id ) && ! $subscription_obj->finite ) { // if Enable Subscription-Based Service Creation is disabled and  product must be a finite subscription.
								return '';
							}

							$services_enable      = $settings_options['status'] ?? '';
							$ask_for_requirements = $settings_options['ask_for_requirements'] ?? '';
							$ask_for_contract     = $settings_options['ask_for_contract'] ?? '';

							if ( ! empty( $ask_for_contract ) && ! empty( $ask_for_requirements ) ) {
								$service_status = 'waiting_for_contract';
							} elseif ( empty( $ask_for_contract ) && ! empty( $ask_for_requirements ) ) {
								$service_status = 'waiting_for_req';
							} elseif ( ! empty( $ask_for_contract ) && empty( $ask_for_requirements ) ) {
								$service_status = 'waiting_for_contract';
							} elseif ( empty( $ask_for_contract ) && empty( $ask_for_requirements ) ) {
								$service_status = 'service_start';
								$delivery_date  = $this->surelywp_sv_calculate_delivery_date( $sv_setting_id );
							}

							if ( ! empty( $services_enable ) ) {
								$service_data = array(
									'service_setting_id' => $sv_setting_id,
									'order_id'           => $order_id,
									'product_id'         => $product_id,
									'service_status'     => $service_status,
									'delivery_date'      => $delivery_date,
									'user_id'            => $customer_user_id,
								);
							}
						} else {

							// check services_product_type and create service accordingly.
							foreach ( $surelywp_sv_settings_options as $service_setting_id => $options ) {

								$is_enable_recurring_services = $options['is_enable_recurring_services'] ?? '';

								// Check if this Subscription product.
								if ( $is_enable_recurring_services ) { // if Enable Subscription-Based Service Creation is enable.

									if ( empty( $subscription_id ) ) { // product must be a subscription product.
										continue;
									}

									if ( $subscription_obj->finite ) { // product must be a recurring subscription.
										continue;
									}

									$create_rc_sv = true;

								} elseif ( ! empty( $subscription_id ) && ! $subscription_obj->finite ) { // if Enable Subscription-Based Service Creation is disabled and product must be a finite subscription.
									continue;
								}

								$services_product_type = $options['services_product_type'] ?? '';
								$services_enable       = $options['status'] ?? '';
								$ask_for_requirements  = $options['ask_for_requirements'] ?? '';
								$ask_for_contract      = $options['ask_for_contract'] ?? '';

								if ( ! empty( $ask_for_contract ) && ! empty( $ask_for_requirements ) ) {
									$service_status = 'waiting_for_contract';
								} elseif ( empty( $ask_for_contract ) && ! empty( $ask_for_requirements ) ) {
									$service_status = 'waiting_for_req';
								} elseif ( ! empty( $ask_for_contract ) && empty( $ask_for_requirements ) ) {
									$service_status = 'waiting_for_contract';
								} elseif ( empty( $ask_for_contract ) && empty( $ask_for_requirements ) ) {
									$service_status = 'service_start';
									$delivery_date  = $this->surelywp_sv_calculate_delivery_date( $sv_setting_id );
								}

								if ( ! empty( $services_product_type ) && ! empty( $services_enable ) ) {

									if ( 'all' === $services_product_type ) {

										$service_data = array(
											'service_setting_id' => $service_setting_id,
											'order_id'   => $order_id,
											'product_id' => $product_id,
											'service_status' => $service_status,
											'delivery_date' => $delivery_date,
											'user_id'    => $customer_user_id,
										);

										// break the loop so that not create multiple service for same product.
										break;

									} elseif ( 'specific' === $services_product_type ) {

										$service_products = $options['service_products'] ?? '';

										if ( ! empty( $product_id ) && in_array( $product_id, $service_products, true ) ) {

											$service_data = array(
												'service_setting_id' => $service_setting_id,
												'order_id' => $order_id,
												'product_id' => $product_id,
												'service_status' => $service_status,
												'delivery_date' => $delivery_date,
												'user_id'  => $customer_user_id,
											);

											break;
										}
									} elseif ( 'specific_collection' === $services_product_type ) {

										$service_products_collections = $options['service_products_collections'] ?? '';

										if ( ! empty( $service_products_collections ) ) {

											foreach ( $service_products_collections as $collection_id ) {

												$collection_products = self::surelywp_sv_get_collection_product_ids( $collection_id );

												if ( ! empty( $product_id ) && in_array( $product_id, $collection_products, true ) ) {

													$service_data = array(
														'service_setting_id' => $service_setting_id,
														'order_id'   => $order_id,
														'product_id' => $product_id,
														'service_status' => $service_status,
														'delivery_date' => $delivery_date,
														'user_id'        => $customer_user_id,
													);

													break;
												}
											}

											if ( ! empty( $service_data ) ) {
												break;
											}
										}
									}
								}
							}
						}

						// if have service data.
						if ( ! empty( $service_data ) ) {

							$service_setting_id = $service_data['service_setting_id'] ?? '';

							// if product is subscription product.
							if ( $create_rc_sv ) {

								$recurring_based_on       = self::get_sv_option( $service_setting_id, 'recurring_based_on' );
								$frequency                = '';
								$sub_frequency            = '';
								$recurring_interval_count = 1;

								$price_id  = $subscription_obj->price ?? '';
								$price_obj = Price::find( $price_id );
								if ( ! is_wp_error( $price_obj ) && ! empty( $price_obj ) ) {
									if ( 'month' === $price_obj->recurring_interval ) {
										$sub_frequency = 'monthly';
									} elseif ( 'week' === $price_obj->recurring_interval ) {
										$sub_frequency = 'weekly';
									} elseif ( 'year' === $price_obj->recurring_interval ) {
										$sub_frequency = 'yearly';
									} elseif ( 'day' === $price_obj->recurring_interval ) {
										$sub_frequency = 'daily';
									}
								}

								if ( 'subscription_cycle' === $recurring_based_on ) {
									$recurring_interval_count = $price_obj->recurring_interval_count;
									$frequency                = $sub_frequency;
								} elseif ( 'custom_frequency' === $recurring_based_on ) {

									$frequency               = self::get_sv_option( $service_setting_id, 'custom_frequency' );
									$sub_frequency_number    = self::surelyewp_sv_get_freq_number( $sub_frequency );
									$custom_frequency_number = self::surelyewp_sv_get_freq_number( $frequency );

									// If the purchased product is a daily subscription but the custom frequency is set to weekly, monthly, or yearly (i.e., any frequency higher than daily), then the service not created.
									if ( $custom_frequency_number > $sub_frequency_number ) {
										return false;
									}
								}

								$is_auto_create_new_service = self::get_sv_option( $service_setting_id, 'is_auto_create_new_service' );

								// Create the recurring service.
								$number_of_services_allow = self::get_sv_option( $service_setting_id, 'number_of_services_allow' );
								$next_update_on           = self::surelyewp_sv_calculate_next_update_time( $service_setting_id, $subscription_obj );

								$recurring_service_data = array(
									'order_id'           => $order_id,
									'product_id'         => $product_id,
									'subscription_id'    => $subscription_id,
									'service_setting_id' => $service_setting_id,
									'user_id'            => $customer_user_id,
									'quota'              => ( $number_of_services_allow - 1 ),
									'next_update_on'     => $next_update_on,
								);

								// Add Recurring Service entry on db.
								$recurring_service_id = $surelywp_sv_model->surelywp_sv_insert_recurring_service( $recurring_service_data );

								if ( $recurring_service_id ) {

									$rollover = self::get_sv_option( $service_setting_id, 'rollover' );

									// Add Recurring Service setting entry on db.
									$recurring_service_setting_data = array(
										'recurring_service_id' => $recurring_service_id,
										'enable_recurring_services' => $is_enable_recurring_services,
										'number_of_services_allow' => $number_of_services_allow,
										'recurring_based_on' => $recurring_based_on,
										'recurring_interval_count' => $recurring_interval_count,
										'frequency' => $frequency,
										'is_auto_create_new_service' => $is_auto_create_new_service,
										'rollover'  => $rollover,
									);

									$recurring_service_setting_id = $surelywp_sv_model->surelywp_sv_insert_recurring_service_setting( $recurring_service_setting_data );
									if ( $recurring_service_setting_id ) {

										$service_data['recurring_service_id'] = $recurring_service_id;

										$this->surelywp_sv_create_rc_service( $service_data ); // Create the rc service.
									}
								}
							} else {

								$number_of_allow_sv_per_order = self::get_sv_option( $service_data['service_setting_id'], 'number_of_allow_sv_per_order' ) ?? 0;
								if ( $number_of_allow_sv_per_order > 0 ) {
									$number_of_allow_sv_per_order = intval( $number_of_allow_sv_per_order ) - 1;
								}
								$service_data['services_remaining'] = $number_of_allow_sv_per_order;
								$this->surelywp_sv_create_service( $service_data ); // create the service.
							}

							// Delete the transient data when purchase created.
							$user_id = $service_data['user_id'] ?? '';
							$this->surelywp_sv_delete_transient_product_requirements( $product_id, $user_id );
							/**
							 * Fire for every new service creation.
							 */
							do_action( 'surelywp_services_create', $service_data );

							// Remove Alert Cookie.
							if ( isset( $_COOKIE['surelywp_sv_notification_count'] ) ) {

								$domain_name = isset( $_SERVER['SERVER_NAME'] ) && ! empty( $_SERVER['SERVER_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '';

								// Remove the cookie by setting it with an expiration time in the past.
								setcookie( 'surelywp_sv_notification_count', '', time() - 86400, '/', $domain_name, true );
							}

							$sv_update_gen_options = self::get_sv_gen_option( 'sv_update_gen_options' );
							$redirect_to_services  = self::get_sv_gen_option( 'redirect_to_services' );

							if ( ! empty( $redirect_to_services ) || empty( $sv_update_gen_options ) ) {

								// Set option for redirection.
								update_option( 'surelywp_sv_redirect_on_customer_services', true );

							}
						}
					}
				}
			}
		}

		/**
		 * Function to get Frequency based number.
		 *
		 * @param string $freq the freq number.
		 *
		 * @package Services For SureCart
		 * @since   1.5
		 */
		public static function surelyewp_sv_get_freq_number( $freq ) {

			if ( 'daily' === $freq ) {
				return 1;
			} elseif ( 'weekly' === $freq ) {
				return 2;
			} elseif ( 'monthly' === $freq ) {
				return 3;
			} elseif ( 'yearly' === $freq ) {
				return 4;
			}

			return false;
		}

		/**
		 * Function to calculate recurring service next update time.
		 *
		 * @param int    $service_setting_id The id of the service setting.
		 * @param object $subscription_obj The subscription object.
		 *
		 * @package Services For SureCart
		 * @since   1.5
		 */
		public function surelyewp_sv_calculate_next_update_time( $service_setting_id, $subscription_obj ) {

			$recurring_based_on = self::get_sv_option( $service_setting_id, 'recurring_based_on' );

			self::surelywp_sv_set_timezone();

			if ( 'subscription_cycle' === $recurring_based_on ) {

				return date( 'Y-m-d H:i:s', $subscription_obj->current_period_end_at ?? '' );

			} elseif ( 'custom_frequency' === $recurring_based_on ) {

				$custom_frequency = self::get_sv_option( $service_setting_id, 'custom_frequency' );
				$current_time     = new DateTime(); // Current time.

				if ( 'daily' === $custom_frequency ) {
					$current_time->add( new DateInterval( 'P1D' ) ); // Add 1 day.
				} elseif ( 'weekly' === $custom_frequency ) {
					$current_time->add( new DateInterval( 'P1W' ) ); // Add 1 Week.
				} elseif ( 'monthly' === $custom_frequency ) {
					$current_time->add( new DateInterval( 'P1M' ) ); // Add 1 month.
				} elseif ( 'yearly' === $custom_frequency ) {
					$current_time->add( new DateInterval( 'P1Y' ) ); // Add 1 year.
				}

				return $current_time->format( 'Y-m-d H:i:s' );
			}
		}

		/**
		 * Function to insert service, send mail, set cron and add activity.
		 *
		 * @param array $service_data The data of service.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_create_service( $service_data ) {

			global $surelywp_sv_model, $surelywp_model;

			if ( ! empty( $service_data ) ) {

				// Added service entry in service table.
				$service_id = $surelywp_sv_model->surelywp_sv_insert_service( $service_data );

				// Added activity entry in activity table.
				if ( $service_id ) {

					$service_setting_id = $service_data['service_setting_id'] ?? '';

					$ask_for_requirements = self::get_sv_option( $service_setting_id, 'ask_for_requirements' );
					$ask_for_contract     = self::get_sv_option( $service_setting_id, 'ask_for_contract' );

					// Send Email to admins for new service create.
					$this->surelywp_sv_send_service_email( $service_id, 'new_service_notification' );

					$product_id = $service_data['product_id'];
					$user_id    = $service_data['user_id'] ?? '';
					if ( ! empty( $ask_for_requirements ) ) {

						$transient_data = self::surelywp_get_product_requirements( $product_id, $user_id );
						if ( isset( $transient_data ) && ! empty( $transient_data ) ) {

							$req_submit_time = sanitize_text_field( $transient_data['submit_time'] );

							$requirements_data = $surelywp_model->surelywp_escape_slashes_deep( $transient_data, true, true );
							$req_fields        = self::get_sv_option( $service_setting_id, 'req_fields' );
							$milestones_fields = self::get_sv_option( $service_setting_id, 'milestones_fields' );

							$ordered_requirements = array();
							if ( ! empty( $req_fields ) ) {
								foreach ( $req_fields as $req_field ) {
									$req_title = $req_field['req_title'];
									foreach ( $requirements_data as $key => $requirement ) {
										if ( isset( $requirement['requirement_title'] ) && $requirement['requirement_title'] === $req_title ) {
											$ordered_requirements[] = $requirement;
											break;
										}
									}
								}
							}
							$status_milestones = array();
							if ( ! empty( $milestones_fields ) ) {
								foreach ( $milestones_fields as $key => $milestones_field ) {
									$status_milestones[] = 'service_start_' . $key;
								}
							}
							foreach ( $ordered_requirements as $req_key => $requirement ) {

								$ordered_requirements[ $req_key ]['service_id'] = $service_id;

								// handle file uplode.
								if ( 'file' === $requirement['requirement_type'] ) {

									$requirement_file_names     = array();
									$requirement_files_arranged = $requirement['requirement'];

									if ( ! empty( $requirement_files_arranged ) ) {

										foreach ( $requirement_files_arranged as $key => $file_path ) {
											$file_name = $this->surelywp_sv_move_req_temp_file( $service_id, $file_path );
											if ( $file_name ) {
												$requirement_file_names[ $req_key ][] = $file_name;
											}
										}

										// Get only names.
										$ordered_requirements[ $req_key ]['requirement'] = isset( $requirement_file_names[ $req_key ] ) && ! empty( $requirement_file_names[ $req_key ] ) ? json_encode( $requirement_file_names[ $req_key ] ) : '';

									}
								}
							}

							$surelywp_sv_model->surelywp_sv_add_service_requirement( $ordered_requirements );

							/**
							 * Fire on Requirement Submit.
							 */
							do_action( 'surelywp_services_requirement_submit', $ordered_requirements );

							// Send Email to admins for customer requirements.
							$this->surelywp_sv_send_service_email( $service_id, 'customer_requirement_notification' );

							if ( empty( $ask_for_contract ) && empty( $service_data['delivery_date'] ) ) { // if contract is not enable then calculate the date and service will be start.

								$delivery_date                         = $this->surelywp_sv_calculate_delivery_date( $service_setting_id );
								$update_service['delivery_date']       = $delivery_date;
								$update_service['service_status']      = $status_milestones[0];
								$update_service['revisions_remaining'] = $this->surelywp_sv_get_revisions_allowed( $service_setting_id, 0 );
								$surelywp_sv_model->surelywp_sv_update_service( $service_id, $update_service );

								$service_data['delivery_date'] = $delivery_date;
							}

							// Add activity for service requirement submmited.
							$surelywp_sv_model->surelywp_sv_insert_activity(
								array(
									'service_id'    => $service_id,
									'activity_type' => 'service_req_received',
									'activity_info' => null,
									'created_at'    => $req_submit_time,
								)
							);


						} elseif ( empty( $ask_for_contract ) ) {

							// Send Email to customer for .
							$this->surelywp_sv_send_service_email( $service_id, 'requirement_ask_email' );

							// Set cron requirement submit reminder.
							$this->surelywp_sv_set_service_req_reminder_cron( $service_id );
						}
					}

					// Add Activities.
					$activity_data = array(
						'service_id'    => $service_id,
						'activity_type' => 'service_created',
						'activity_info' => null,
					);

					$surelywp_sv_model->surelywp_sv_insert_activity( $activity_data );

					if ( null !== $service_data['delivery_date'] ) {

						$activity_data = array(
							'service_id'    => $service_id,
							'activity_type' => 'service_start',
							'activity_info' => '0',
						);
						$surelywp_sv_model->surelywp_sv_insert_activity( $activity_data );
					}

					return $service_id;
				}
			}
		}


		/**
		 * Function to insert service, send mail, set cron and add activity.
		 *
		 * @param array $service_data The data of service.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_create_rc_service( $service_data ) {

			global $surelywp_sv_model, $surelywp_model;

			if ( ! empty( $service_data ) ) {

				// Added service entry in service table.
				$service_id = $surelywp_sv_model->surelywp_sv_insert_service( $service_data );

				// Added activity entry in activity table.
				if ( $service_id ) {

					$service_setting_id = $service_data['service_setting_id'] ?? '';

					$ask_for_requirements = self::get_sv_option( $service_setting_id, 'ask_for_requirements' );
					$ask_for_contract     = self::get_sv_option( $service_setting_id, 'ask_for_contract' );

					// Send Email to admins for new service create.
					$this->surelywp_sv_send_service_email( $service_id, 'new_service_notification' );

					$product_id = $service_data['product_id'];
					$user_id    = $service_data['user_id'] ?? '';
					if ( ! empty( $ask_for_requirements ) ) {

						$transient_data = self::surelywp_get_product_requirements( $product_id, $user_id );
						if ( isset( $transient_data ) && ! empty( $transient_data ) ) {

							$req_submit_time = sanitize_text_field( $transient_data['submit_time'] );

							$requirements_data = $surelywp_model->surelywp_escape_slashes_deep( $transient_data, true, true );
							$req_fields        = self::get_sv_option( $service_setting_id, 'req_fields' );
							$milestones_fields = self::get_sv_option( $service_setting_id, 'milestones_fields' );

							$ordered_requirements = array();
							if ( ! empty( $req_fields ) ) {
								foreach ( $req_fields as $req_field ) {
									$req_title = $req_field['req_title'];
									foreach ( $requirements_data as $key => $requirement ) {
										if ( isset( $requirement['requirement_title'] ) && $requirement['requirement_title'] === $req_title ) {
											$ordered_requirements[] = $requirement;
											break;
										}
									}
								}
							}

							$status_milestones = array();
							if ( ! empty( $milestones_fields ) ) {
								foreach ( $milestones_fields as $key => $milestones_field ) {
									$status_milestones[] = 'service_start_' . $key;
								}
							}

							foreach ( $ordered_requirements as $req_key => $requirement ) {

								$ordered_requirements[ $req_key ]['service_id'] = $service_id;

								// handle file uplode.
								if ( 'file' === $requirement['requirement_type'] ) {

									$requirement_file_names     = array();
									$requirement_files_arranged = $requirement['requirement'];

									if ( ! empty( $requirement_files_arranged ) ) {

										foreach ( $requirement_files_arranged as $key => $file_path ) {
											$file_name = $this->surelywp_sv_move_req_temp_file( $service_id, $file_path );
											if ( $file_name ) {
												$requirement_file_names[ $req_key ][] = $file_name;
											}
										}

										// Get only names.
										$ordered_requirements[ $req_key ]['requirement'] = isset( $requirement_file_names[ $req_key ] ) && ! empty( $requirement_file_names[ $req_key ] ) ? json_encode( $requirement_file_names[ $req_key ] ) : '';

									}
								}
							}

							$surelywp_sv_model->surelywp_sv_add_service_requirement( $ordered_requirements );

							/**
							 * Fire on Requirement Submit.
							 */
							do_action( 'surelywp_services_requirement_submit', $ordered_requirements );

							// Send Email to admins for customer requirements.
							$this->surelywp_sv_send_service_email( $service_id, 'customer_requirement_notification' );

							if ( empty( $ask_for_contract ) && empty( $service_data['delivery_date'] ) ) { // if contract is not enable then calculate the date and service will be start.

								$delivery_date                    = $this->surelywp_sv_calculate_delivery_date( $service_setting_id );
								$update_service['delivery_date']  = $delivery_date;
								// $update_service['service_status'] = 'service_start';
								$update_service['service_status'] = $status_milestones[0];
								$update_service['revisions_remaining'] = $this->surelywp_sv_get_revisions_allowed( $service_setting_id, 0 );
								$surelywp_sv_model->surelywp_sv_update_service( $service_id, $update_service );

								$service_data['delivery_date'] = $delivery_date;
							}

							// Add activity for service requirement submmited.
							$surelywp_sv_model->surelywp_sv_insert_activity(
								array(
									'service_id'    => $service_id,
									'activity_type' => 'service_req_received',
									'activity_info' => null,
									'created_at'    => $req_submit_time,
								)
							);

						} elseif ( empty( $ask_for_contract ) ) {

							// Send Email to customer for .
							$this->surelywp_sv_send_service_email( $service_id, 'requirement_ask_email' );

							// Set cron requirement submit reminder.
							$this->surelywp_sv_set_service_req_reminder_cron( $service_id );
						}
					}

					// Add Activities.
					$activity_data = array(
						'service_id'    => $service_id,
						'activity_type' => 'rc_service_created',
						'activity_info' => null,
					);

					$surelywp_sv_model->surelywp_sv_insert_activity( $activity_data );

					if ( null !== $service_data['delivery_date'] ) {

						$activity_data = array(
							'service_id'    => $service_id,
							'activity_type' => 'service_start',
							'activity_info' => '0',
						);
						$surelywp_sv_model->surelywp_sv_insert_activity( $activity_data );
					}

					return $service_id;
				}
			}
		}

		/**
		 * Function to set service requirement reminder cron.
		 *
		 * @param int $service_id The id of the service.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_set_service_req_reminder_cron( $service_id ) {

			$service_id = intval( $service_id );

			if ( ! empty( $service_id ) ) {

				$hook              = SURELYWP_SERVICES_REQ_REMINDER_CRON;
				$args              = array( $service_id, 'requirement_reminder_email' );
				$req_reminder_time = self::get_sv_gen_option( 'req_reminder_time' );
				$recurrence        = '';
				if ( empty( $req_reminder_time ) || '12' === $req_reminder_time ) {
					$recurrence = 'twicedaily';
				} else {
					$recurrence = 'daily';
				}

				$time = time();
				self::surelywp_sv_set_cron( $args, $recurrence, $hook, $time );
			}
		}

		/**
		 * Function to check product is present on other service.
		 *
		 * @param int $current_setting_id The id of current setting.
		 * @param int $product_id The id of the product.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function surelywp_sv_is_product_have_service( $current_setting_id, $product_id ) {

			if ( ! empty( $product_id ) ) {

				$surelywp_sv_settings_options = get_option( 'surelywp_sv_settings_options' );
				$surelywp_sv_settings_options = $surelywp_sv_settings_options['surelywp_sv_settings_options'] ?? '';

				if ( ! empty( $surelywp_sv_settings_options ) ) {
					foreach ( $surelywp_sv_settings_options as $service_setting_id => $options ) {

						// skip for check in current setting.
						if ( $current_setting_id === $service_setting_id ) {
							continue;
						}

						$services_product_type = $options['services_product_type'] ?? '';
						if ( 'specific' === $services_product_type ) {
							$service_products = $options['service_products'] ?? array();

							if ( ! empty( $service_products ) && in_array( $product_id, $service_products, true ) ) {

								return true;
							}
						}
					}
				}
			}
			return false;
		}

		/**
		 * Function to set service auto complete cron.
		 *
		 * @param int $service_id The id of the service.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_set_service_auto_complete_cron( $service_id ) {

			$service_id = intval( $service_id );

			if ( ! empty( $service_id ) ) {

				$hook               = SURELYWP_SERVICES_AUTO_COMPLETE_CRON;
				$args               = array( $service_id );
				$auto_complete_time = self::get_sv_gen_option( 'auto_complete_time' );

				if ( empty( $auto_complete_time ) ) {
					$auto_complete_time = '5';
				}

				$time = strtotime( '+' . $auto_complete_time . ' days' );

				// clear old cron if schedule.
				self::surelywp_sv_unset_cron( $hook, $args );

				wp_schedule_single_event( $time, $hook, $args );
			}
		}

		/**
		 * Function to auto complete service.
		 *
		 * @param int $service_id The id of the service.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_auto_complete_service_callback( $service_id ) {

			$service_id = intval( $service_id );

			if ( ! empty( $service_id ) ) {

				global $surelywp_sv_model;

				// update service status.
				$status                         = 'service_complete';
				$service_data['service_status'] = $status;
				$service_data['delivery_date']  = current_time( 'mysql' );
				$is_updated                     = $surelywp_sv_model->surelywp_sv_update_service( $service_id, $service_data );

				// Add activity.
				if ( $is_updated ) {

					/**
					 * Fire on service auto complete.
					 */
					do_action( 'surelywp_services_auto_complete', $service_id );

					$activity_data = array(
						'service_id'    => $service_id,
						'activity_type' => 'service_auto_completed',
					);

					$surelywp_sv_model->surelywp_sv_insert_activity( $activity_data );
				}
			}
		}

		/**
		 * Function to get service data.
		 *
		 * @param object $order_id The order id.
		 * @param object $product_id The product id.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_service_data( $order_id, $product_id ) {

			$service_data = array();

			if ( ! empty( $order_id ) && ! empty( $product_id ) ) {

				$order_data   = Order::find( $order_id );
				$product_data = Product::find( $product_id );

				if ( ! empty( $order_data ) ) {

					$service_data['order_number'] = $order_data->number ?? '';
					$service_data['order_date']   = $order_data->created_at ?? '';
					$service_data['order_mode']   = isset( $order_data->live_mode ) && ! empty( $order_data->live_mode ) ? 'live' : 'test';
				}

				if ( ! empty( $product_data ) ) {

					$service_data['product_name']      = $product_data->name ?? '';
					$service_data['product_permalink'] = $product_data->permalink ?? '';

					if ( isset( $product_data->image_url ) && ! empty( $product_data->image_url ) ) {
						$service_data['product_img_url'] = $product_data->image_url ?? '';
					} elseif ( isset( $product_data->metadata->gallery_ids ) && ! empty( $product_data->metadata->gallery_ids ) ) {

						$attachment_ids                  = json_decode( $product_data->metadata->gallery_ids, true );
						$attachment_url                  = isset( $attachment_ids[0] ) ? wp_get_attachment_url( $attachment_ids[0] ) : '';
						$service_data['product_img_url'] = $attachment_url;
					}
				}
			}

			return $service_data;
		}

		/**
		 * Function to get services data.
		 *
		 * @param array $services The services.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_sv_data( $services ) {

			$service_data = array();

			if ( ! empty( $services ) ) {

				$order_ids   = wp_list_pluck( $services, 'order_id' );
				$product_ids = wp_list_pluck( $services, 'product_id' );

				if ( ! empty( $order_ids ) ) {
					$order_data = Order::where( array( 'ids' => $order_ids ) )->get();

					if ( ! is_wp_error( $order_data ) && ! empty( $order_data ) ) {

						foreach ( $order_data as $order ) {
							$service_data['order'][ $order->id ]['order_number'] = $order->number ?? '';
							$service_data['order'][ $order->id ]['order_date']   = $order->created_at ?? '';
							$service_data['order'][ $order->id ]['order_mode']   = isset( $order->live_mode ) && ! empty( $order->live_mode ) ? 'live' : 'test';
						}
					}
				}

				if ( ! empty( $product_ids ) ) {

					$product_data = Product::where( array( 'ids' => $product_ids ) )->get();

					if ( ! is_wp_error( $product_data ) && ! empty( $product_data ) ) {

						foreach ( $product_data as $product ) {
							$service_data['product'][ $product->id ]['product_name']      = $product->name ?? '';
							$service_data['product'][ $product->id ]['product_permalink'] = $product->permalink ?? '';

							if ( isset( $product->image_url ) && ! empty( $product->image_url ) ) {
								$service_data['product'][ $product->id ]['product_img_url'] = $product->image_url ?? '';
							} elseif ( isset( $product->metadata->gallery_ids ) && ! empty( $product->metadata->gallery_ids ) ) {
								$attachment_ids = json_decode( $product->metadata->gallery_ids, true );
								$attachment_url = isset( $attachment_ids[0] ) ? wp_get_attachment_url( $attachment_ids[0] ) : '';
								$service_data['product'][ $product->id ]['product_img_url'] = $attachment_url;
							}
						}
					}
				}
			}

			return $service_data;
		}

		/**
		 * Function to get service data.
		 *
		 * @param object $order_id The order id.
		 * @param object $product_id The product id.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_product_paid_price( $order_id, $product_id ) {

			$price = array();

			if ( ! empty( $order_id ) && ! empty( $product_id ) ) {

				$order_data = Order::with( array( 'checkout', 'checkout.line_items', 'line_items.price', 'price.product', 'product.variant_options' ) )->find( $order_id );

				if ( ! empty( $order_data ) ) {

					$order_line_items = $order_data->checkout->line_items->data ?? '';

					if ( ! empty( $order_line_items ) ) {

						foreach ( $order_line_items as $line_item ) {

							$order_product_id = $line_item->price->product->id ?? '';

							if ( $product_id === $order_product_id ) {

								$price['id']                      = $line_item->price->id ?? '';
								$price['variant_id']              = $line_item->variant ?? '';
								$price['amount']                  = $line_item->full_amount ?? '';
								$price['currency']                = $line_item->price->currency ?? '';
								$variant_options                  = $line_item->variant_options ?? '';
								$price['product_variant_options'] = array();

								// For product variants names.
								if ( ! empty( $variant_options ) ) {

									$product_variants_data = $line_item->price->product->variant_options->data ?? '';
									if ( ! empty( $product_variants_data ) ) {

										foreach ( $product_variants_data as $key => $variant ) {

											$price['product_variant_options'][ $key ]['name']  = $variant->name;
											$intersected_values                                = array_intersect( $variant->values, $variant_options );
											$price['product_variant_options'][ $key ]['value'] = array_pop( $intersected_values );
										}
									}
								}
							}
						}
					}
				}
			}

			return $price;
		}

		/**
		 * Function to get service status.
		 *
		 * @param string $status the service_status key.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_service_status( $status, $product_id, $service_id ) {
			$service_setting_id = self::surelywp_sv_get_product_service_setting_id( $product_id );
			$milestones_fields  = self::get_sv_option( $service_setting_id, 'milestones_fields' );
			$status_milestones  = array();
			if ( ! empty( $milestones_fields ) ){
				foreach ( $milestones_fields as $key => $milestones_field ) {
					$milestone_start    = 'service_start_' . $key;
					$milestone_approval = 'milestone_submit_' . $key;
					$milestone_label    = ! empty( $milestones_field['milestones_field_label'] )
						? $milestones_field['milestones_field_label']
						: esc_html__( 'In Progress', 'surelywp-services' ); // fallback.

					$status_milestones[ $milestone_start ]    = esc_html__( 'Work In Progress', 'surelywp-services' );
					$status_milestones[ $milestone_approval ] = esc_html__( 'Waiting For Approval', 'surelywp-services' );
				}
			}
			$service_status = array(
				'waiting_for_contract' => esc_html__( 'Waiting For Contract', 'surelywp-services' ),
				'waiting_for_req'      => esc_html__( 'Waiting For Requirements', 'surelywp-services' ),
				'service_start'        => esc_html__( 'Work In Progress', 'surelywp-services' ),
				'service_submit'       => esc_html__( 'Waiting For Approval', 'surelywp-services' ),
				'final_delivery_start' => esc_html__( 'Work In Progress', 'surelywp-services' ),
				'service_complete'     => esc_html__( 'Order Complete', 'surelywp-services' ),
				'service_canceled'     => esc_html__( 'Order Cancelled', 'surelywp-services' ),
			);

			// merge in the milestone statuses (just like hard-coded ones).
			$service_status = array_merge( $service_status, $status_milestones );

			return $service_status[ $status ] ?? '';
		}

		/**
		 * Function to get activity description.
		 *
		 * @param string $activity_type The type of the activity.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_activity_desc( $activity_type ) {

			$current_user_id  = get_current_user_id();
			$access_users_ids = self::get_sv_access_users_ids();

			$service_singular_name = self::get_sv_gen_option( 'service_singular_name' );
			if ( empty( $service_singular_name ) ) {
				$service_singular_name = esc_html__( 'Service', 'surelywp-services' );
			}
			$service_plural_name = self::get_sv_gen_option( 'service_plural_name' );
			if ( empty( $service_plural_name ) ) {
				$service_plural_name = esc_html__( 'Services', 'surelywp-services' );
			}

			$activity_desc = array();
			if ( ! empty( $access_users_ids ) && in_array( $current_user_id, (array) $access_users_ids ) ) { // for seller's admin.
				$activity_desc = array(
					// translators: %s is the singular name of the service.
					'rc_service_created'        => sprintf( esc_html__( '%s Request Has Been Received', 'surelywp-services' ), esc_html( $service_singular_name ) ),
					'service_created'           => esc_html__( 'Order Placed', 'surelywp-services' ),
					'service_contract_signed'   => esc_html__( 'Contract Signed', 'surelywp-services' ),
					'service_req_received'      => esc_html__( 'Customer Submitted Requirements', 'surelywp-services' ),
					// translators: %s is the singular name of the service.
					'service_start'             => sprintf( esc_html__( '%s Started', 'surelywp-services' ), esc_html( $service_singular_name ) ),
					'milestone_send'            => esc_html__( 'Milestone Sent to Customer', 'surelywp-services' ),
					'delivery_date_change'      => esc_html__( 'Delivery Date Changed', 'surelywp-services' ),
					'delivery_send'             => esc_html__( 'Final Delivery Sent to Customer', 'surelywp-services' ),
					'delivery_reject'           => esc_html__( 'Customer Requested Revisions', 'surelywp-services' ),
					'delivery_accept'           => esc_html__( 'Customer Approved Final Delivery', 'surelywp-services' ),
					'service_complete'          => esc_html__( 'Order Completed', 'surelywp-services' ),
					'service_auto_completed'    => esc_html__( 'Order Automatically Completed', 'surelywp-services' ),
					'service_complete_by_admin' => esc_html__( 'Order Manually Completed', 'surelywp-services' ),
					'service_canceled'          => esc_html__( 'Order Canceled', 'surelywp-services' ),
					'milestone_submit'          => esc_html__( '%s Sent to Customer', 'surelywp-services' ),
					'delivery_accept_milestone' => esc_html__( 'Customer Approved %s delivery', 'surelywp-services' ),
				);
			} else { // For customers.

				$activity_desc = array(
					// translators: %s is the singular name of the service.
					'rc_service_created'        => sprintf( esc_html__( 'Your %s Request Has Been Received', 'surelywp-services' ), esc_html( $service_singular_name ) ),
					// translators: %s is the singular name of the service.
					'service_created'           => sprintf( esc_html__( 'Your %s Request Has Been Received', 'surelywp-services' ), esc_html( $service_singular_name ) ),
					'service_contract_signed'   => esc_html__( 'Contract Signed', 'surelywp-services' ),
					'service_req_received'      => esc_html__( 'You Submitted The Requirements', 'surelywp-services' ),
					// translators: %s is the singular name of the service.
					'service_start'             => sprintf( esc_html__( '%s Started', 'surelywp-services' ), esc_html( $service_singular_name ) ),
					'delivery_date_change'      => esc_html__( 'Delivery Date Changed', 'surelywp-services' ),
					'delivery_send'             => esc_html__( 'You Received The Final Delivery', 'surelywp-services' ),
					'delivery_reject'           => esc_html__( 'You Requested Revisions', 'surelywp-services' ),
					'delivery_accept'           => esc_html__( 'You Accepted The Final Delivery', 'surelywp-services' ),
					'service_complete'          => esc_html__( 'Order completed', 'surelywp-services' ),
					'service_auto_completed'    => esc_html__( 'Order Automatically Completed', 'surelywp-services' ),
					'service_complete_by_admin' => esc_html__( 'Order Manually Completed', 'surelywp-services' ),
					'service_canceled'          => esc_html__( 'Your Order Has Been Canceled', 'surelywp-services' ),
					'milestone_submit'          => esc_html__( 'You received the %s', 'surelywp-services' ),
					'delivery_accept_milestone' => esc_html__( 'You Accepted The %s Delivery', 'surelywp-services' ),
				);
			}

			return $activity_desc[ $activity_type ];
		}

		/**
		 * Function for override customer dashboard.
		 *
		 * @param string $template The path of the template.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_override_template( $template ) {

			$current_user_id  = get_current_user_id();
			$access_users_ids = self::get_sv_access_users_ids();

			// skip to add service tab.
			if ( ! empty( $access_users_ids ) && in_array( $current_user_id, (array) $access_users_ids ) ) {
				return $template;
			}

			global $post;

			// Get the template name.
			$template_name = basename( $template );

			// Override surecart customer template.
			if ( 'template-surecart-dashboard.php' === $template_name ) {

				$customer_dashboard_path = SURELYWP_SERVICES_TEMPLATE_PATH . '/surecart-customer-template/' . $template_name;
				if ( file_exists( $customer_dashboard_path ) ) {
					return $customer_dashboard_path;
				}
			}
			return $template;
		}

		/**
		 * Function for surecart api request.
		 *
		 * @param string $url The url of the api.
		 * @param string $method The request method of the api.
		 * @param array  $arg The arguments for the api.
		 * @param array  $other_args other The arguments for the api.
		 * @param array  $headers The  headers for the api.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function surelywp_sv_api_request( $url, $method = 'GET', $arg = null, $other_args = null, $headers = null ) {

			$default_hearders = array(
				'Accept'       => 'application/json',
				'content-type' => 'application/json',
			);

			if ( $headers == null ) {

				$headers = $default_hearders;
			}

			$args = array(
				'method'  => $method,
				'headers' => $headers,
			);

			if ( $arg != null ) {
				$args['body'] = json_encode( $arg );
			}

			if ( $other_args != null ) {
				$args = array_merge( $args, $other_args );
			}
			$response = wp_remote_request( $url, $args );
			if ( ( ! is_wp_error( $response ) ) && ( 200 === wp_remote_retrieve_response_code( $response ) ) ) {

				$response = json_decode( $response['body'] );
				return $response;
			} else {

				return false;
			}
		}

		/**
		 * Function to enqueue scripts and styles for the front end.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_front_script() {

			// alllow file extensions.
			$file_types = self::surelywp_sv_get_allow_file_types();

			// file upload max size.
			$file_size = self::get_sv_gen_option( 'file_size' );

			// Service tab label.
			$service_tab_lable = self::get_sv_gen_option( 'service_tab_lable' );
			if ( empty( $service_tab_lable ) ) {
				$service_tab_lable = esc_html__( 'Services', 'surelywp-services' );
			}

			if ( empty( $file_size ) ) {
				$file_size = '5';
			}

			$localize = array(
				'ajax_url'          => admin_url( 'admin-ajax.php' ),
				'nonce'             => wp_create_nonce( 'ajax-nonce' ),
				'file_types'        => $file_types,
				'file_size'         => $file_size,
				'service_tab_lable' => $service_tab_lable,
			);

			// Register functions script.
			wp_register_script( 'surelywp-sv-functions', SURELYWP_SERVICES_ASSETS_URL . '/js/surelywp-sv-functions.js', array( 'jquery' ), SURELYWP_SERVICES_VERSION, true );
			wp_register_script( 'surelywp-sv-functions-min', SURELYWP_SERVICES_ASSETS_URL . '/js/surelywp-sv-functions.min.js', array( 'jquery' ), SURELYWP_SERVICES_VERSION, true );

			// Register open sans style.
			wp_register_style( 'open-sans-css', SURELYWP_SERVICES_ASSETS_URL . '/css/open-sans.css', array(), SURELYWP_SERVICES_VERSION, 'all' );

			// Register Google dancing script font.
			wp_register_style( 'dancing-script-font', SURELYWP_SERVICES_ASSETS_URL . '/css/dancing-script-font.css', array(), SURELYWP_SERVICES_VERSION, 'all' );

			wp_register_script( 'surelywp-sv-front', SURELYWP_SERVICES_ASSETS_URL . '/js/surelywp-sv-front.js', array( 'jquery', 'surelywp-sv-functions' ), SURELYWP_SERVICES_VERSION, true );
			wp_register_script( 'surelywp-sv-front-min', SURELYWP_SERVICES_ASSETS_URL . '/js/surelywp-sv-front.min.js', array( 'jquery', 'surelywp-sv-functions-min' ), SURELYWP_SERVICES_VERSION, true );

			wp_register_style( 'surelywp-sv-requirement-form', SURELYWP_SERVICES_ASSETS_URL . '/css/surelywp-sv-requirement-form.css', array(), SURELYWP_SERVICES_VERSION, 'all' );
			wp_register_style( 'surelywp-sv-requirement-form-min', SURELYWP_SERVICES_ASSETS_URL . '/css/surelywp-sv-requirement-form.min.css', array(), SURELYWP_SERVICES_VERSION, 'all' );

			// shortcodes scripts.
			wp_register_script( 'surelywp-sv-front-shortcode', SURELYWP_SERVICES_ASSETS_URL . '/js/surelywp-sv-front-shortcode.js', array( 'jquery', 'surelywp-sv-functions' ), SURELYWP_SERVICES_VERSION, true );
			wp_register_script( 'surelywp-sv-front-shortcode-min', SURELYWP_SERVICES_ASSETS_URL . '/js/surelywp-sv-front-shortcode.min.js', array( 'jquery', 'surelywp-sv-functions-min' ), SURELYWP_SERVICES_VERSION, true );

			wp_register_style( 'surelywp-sv-front', SURELYWP_SERVICES_ASSETS_URL . '/css/surelywp-sv-front.css', array(), SURELYWP_SERVICES_VERSION, 'all' );
			wp_register_style( 'surelywp-sv-front-min', SURELYWP_SERVICES_ASSETS_URL . '/css/surelywp-sv-front.min.css', array(), SURELYWP_SERVICES_VERSION, 'all' );

			// register common style.
			wp_register_style( 'surelywp-sv-common', SURELYWP_SERVICES_ASSETS_URL . '/css/surelywp-sv-common.css', array(), SURELYWP_SERVICES_VERSION, 'all' );
			wp_register_style( 'surelywp-sv-common-min', SURELYWP_SERVICES_ASSETS_URL . '/css/surelywp-sv-common.min.css', array(), SURELYWP_SERVICES_VERSION, 'all' );

			wp_register_script( 'surelywp-sv-common', SURELYWP_SERVICES_ASSETS_URL . '/js/surelywp-sv-common.js', array( 'jquery' ), SURELYWP_SERVICES_VERSION, true );
			wp_register_script( 'surelywp-sv-common-min', SURELYWP_SERVICES_ASSETS_URL . '/js/surelywp-sv-common.min.js', array( 'jquery' ), SURELYWP_SERVICES_VERSION, true );

			// Register line items script(Delete the transient if present in database when cart is empty).
			wp_register_script( 'surelywp-sv-line-items', SURELYWP_SERVICES_ASSETS_URL . '/js/surelywp-sv-line-items.js', array( 'jquery' ), SURELYWP_SERVICES_VERSION, true );
			wp_register_script( 'surelywp-sv-line-items-min', SURELYWP_SERVICES_ASSETS_URL . '/js/surelywp-sv-line-items.min.js', array( 'jquery' ), SURELYWP_SERVICES_VERSION, true );

			// Register lightbox.
			wp_register_style( 'lightbox-css', SURELYWP_SERVICES_ASSETS_URL . '/css/lightbox.min.css', array(), SURELYWP_SERVICES_VERSION, 'all' );
			wp_register_script( 'lightbox-js', SURELYWP_SERVICES_ASSETS_URL . '/js/lightbox.min.js', array( 'jquery' ), SURELYWP_SERVICES_VERSION, true );

			// Register filepond js ans csss.
			wp_register_style( 'filepond-css', SURELYWP_SERVICES_ASSETS_URL . '/css/filepond.min.css', array(), SURELYWP_SERVICES_VERSION, 'all' );
			wp_register_script( 'filepond-js', SURELYWP_SERVICES_ASSETS_URL . '/js/filepond.min.js', array( 'jquery' ), SURELYWP_SERVICES_VERSION, true );
			wp_register_script( 'filepond-plugins-js', SURELYWP_SERVICES_ASSETS_URL . '/js/filepond-plugins.min.js', array( 'jquery' ), SURELYWP_SERVICES_VERSION, true );

			$min_file = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '-min';
			wp_localize_script( 'surelywp-sv-front' . $min_file, 'sv_front_ajax_object', $localize );
			wp_localize_script( 'surelywp-sv-common' . $min_file, 'sv_common_ajax_object', $localize );
			wp_localize_script( 'surelywp-sv-line-items' . $min_file, 'sv_common_ajax_object', $localize );

			wp_enqueue_script( 'surelywp-sv-line-items' . $min_file );
			wp_set_script_translations( 'surelywp-sv-line-items' . $min_file, 'surelywp-services' );
			$dashboard_page_id = get_option( 'surecart_dashboard_page_id' );

			if ( ! empty( $dashboard_page_id ) && is_page( $dashboard_page_id ) ) {

				self::surelywp_sv_enqueue_front_script();
			}

			$sc_shop_page_id = SureCart::pages()->getId( 'shop' );
			if ( ! is_page( $sc_shop_page_id ) ) {
				$product    = self::surelywp_sv_get_current_product();
				$product_id = $product->id ?? '';
				if ( ! empty( $product_id ) ) {

					// enqueue requirement form script.
					self::surelywp_sv_enqueue_req_form_script();
				}
			}
		}

		/**
		 * Function to enqueue requirement form script.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function surelywp_sv_enqueue_req_form_script() {

			// Tinymce scripts.
			wp_enqueue_editor();

			// For language transate in javascript files.
			wp_enqueue_script( 'wp-i18n' );

			// Enqueue open sans.
			wp_enqueue_style( 'open-sans-css' );

			// Enqueue filepond script and style.
			wp_enqueue_style( 'filepond-css' );
			wp_enqueue_script( 'filepond-js' );
			wp_enqueue_script( 'filepond-plugins-js' );
			$min_file = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '-min';

			wp_enqueue_script( 'surelywp-sv-front' . $min_file );
			wp_enqueue_style( 'surelywp-sv-requirement-form' . $min_file );
		}

		/**
		 * Function to enqueue requirement form script.
		 *
		 * @package Services For SureCart
		 * @since   1.5
		 */
		public static function surelywp_sv_enqueue_product_req_script() {

			$min_file = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '-min';

			wp_enqueue_style( 'surelywp-sv-common' . $min_file );
		}

		/**
		 * Function to enqueue scripts.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function surelywp_sv_enqueue_front_script() {

			// enqueue surecart assets.
			\SureCart::assets()->enqueueComponents();

			// Tinymce scripts.
			wp_enqueue_editor();

			// For language transate in javascript files.
			wp_enqueue_script( 'wp-i18n' );

			// Enqueue open sans.
			wp_enqueue_style( 'open-sans-css' );

			// Enqueue google dancing script font.
			wp_enqueue_style( 'dancing-script-font' );

			// Enqueue light box script and style.
			wp_enqueue_style( 'lightbox-css' );
			wp_enqueue_script( 'lightbox-js' );

			// Enqueue filepond script and style.
			wp_enqueue_style( 'filepond-css' );
			wp_enqueue_script( 'filepond-js' );
			wp_enqueue_script( 'filepond-plugins-js' );

			$min_file = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '-min';

			wp_enqueue_script( 'surelywp-sv-front' . $min_file );
			wp_enqueue_script( 'surelywp-sv-common' . $min_file );

			wp_enqueue_style( 'surelywp-sv-front' . $min_file );
			wp_enqueue_style( 'surelywp-sv-common' . $min_file );

			// For Handle language Translation.
			wp_set_script_translations( 'surelywp-sv-front' . $min_file, 'surelywp-services' );
			wp_set_script_translations( 'surelywp-sv-common' . $min_file, 'surelywp-services' );
		}

		/**
		 * Function to return all products.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function surelywp_sv_get_all_products() {

			$products = Product::where(
				array(
					'archived' => false,
				)
			)->with(
				array(
					'variant',
				)
			)->get();

			return $products ?? '';
		}

		/**
		 * Function to store metadata in product.
		 *
		 * @param string $product_id The id of the product.
		 * @param int    $metadata  the metadata for the product.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_update_product( $product_id, $metadata = array() ) {

			$product_obj = new Product(
				array(
					'id'       => $product_id,
					'metadata' => $metadata,
				),
			);

			if ( ! empty( $product_obj ) ) {

				$product_obj->save();
				return $product_obj;
			} else {

				return false;
			}
		}

		/**
		 * Function to remove metadata in product.
		 *
		 * @param string $service_setting_id the id of the service.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_remove_product_meta( $service_setting_id ) {

			if ( ! empty( $service_setting_id ) ) {

				$existing_settings         = get_option( 'surelywp_sv_settings_options' );
				$existing_settings         = $existing_settings['surelywp_sv_settings_options'][ $service_setting_id ] ?? array();
				$old_services_product_type = isset( $existing_settings['services_product_type'] ) && null !== $existing_settings['services_product_type'] ? $existing_settings['services_product_type'] : '';

				if ( 'specific' === $old_services_product_type ) {

					$old_product_ids = isset( $existing_settings['service_products'] ) && null !== $existing_settings['service_products'] ? $existing_settings['service_products'] : array();
					$metadata        = array(
						'is_service_enable'  => '',
						'service_setting_id' => '',
					);

					// remove meta from the product.
					foreach ( $old_product_ids as $product_id ) {
						$this->surelywp_sv_update_product( $product_id, $metadata );
					}
				}
			}
		}


		/**
		 * Function to remove all setting metadata in product.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_remove_all_product_meta() {

			$surelywp_sv_settings_options = get_option( 'surelywp_sv_settings_options' );
			$surelywp_sv_settings_options = $surelywp_sv_settings_options['surelywp_sv_settings_options'] ?? array();

			if ( ! empty( $surelywp_sv_settings_options ) ) {

				foreach ( $surelywp_sv_settings_options as $existing_settings ) {

					$old_services_product_type = isset( $existing_settings['services_product_type'] ) && null !== $existing_settings['services_product_type'] ? $existing_settings['services_product_type'] : '';

					if ( 'specific' === $old_services_product_type ) {

						$old_product_ids = isset( $existing_settings['service_products'] ) && null !== $existing_settings['service_products'] ? $existing_settings['service_products'] : array();
						$metadata        = array(
							'is_service_enable'  => '',
							'service_setting_id' => '',
						);

						// remove meta from the product.
						foreach ( $old_product_ids as $product_id ) {
							$this->surelywp_sv_update_product( $product_id, $metadata );
						}
					}
				}
			}
		}


		/**
		 * Function to update setting option.
		 *
		 * @param string $product_id The id of the product.
		 * @param string $service_setting_id The id of the service setting id.
		 * @param string $update_action the action add or remove.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_update_setting_option( $product_id, $service_setting_id, $update_action ) {

			if ( ! empty( $service_setting_id ) && ! empty( $product_id ) ) {

				$surelywp_sv_settings_options = get_option( 'surelywp_sv_settings_options' );
				$settings_options             = $surelywp_sv_settings_options['surelywp_sv_settings_options'][ $service_setting_id ] ?? '';
				$service_products             = $settings_options['service_products'] ?? array();

				if ( 'add' === $update_action ) {

					if ( ! empty( $service_products ) ) {
						array_push( $surelywp_sv_settings_options['surelywp_sv_settings_options'][ $service_setting_id ]['service_products'], $product_id );
					} else {
						$surelywp_sv_settings_options['surelywp_sv_settings_options'][ $service_setting_id ]['service_products'] = array( $product_id );
					}
				} elseif ( 'remove' === $update_action ) {

					if ( ! empty( $service_products ) ) {

						foreach ( $service_products as $key => $product ) {
							if ( $product_id === $product ) {
								unset( $surelywp_sv_settings_options['surelywp_sv_settings_options'][ $service_setting_id ]['service_products'][ $key ] );
							}
						}
					}
				}
				update_option( 'surelywp_sv_settings_options', $surelywp_sv_settings_options );
			}
		}

		/**
		 * Function to return all products collections.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function surelywp_sv_get_all_product_collections() {

			$surecart_api_token     = ApiToken::get();
			$url                    = 'https://api.surecart.com/v1/product_collections';
			$headers                = array(
				'Accept'        => 'application/json',
				'authorization' => 'Bearer ' . $surecart_api_token . '',
			);
			$product_collection_obj = self::surelywp_sv_api_request( $url, 'GET', null, null, $headers );
			return $product_collection_obj ?? '';
		}

		/**
		 * Function to unset Cron.
		 *
		 * @param   string $hook The cron hook name.
		 * @param   array  $args The arguments for cron.
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function surelywp_sv_unset_cron( $hook, $args ) {

			// Clear old cron if exist.
			if ( wp_next_scheduled( $hook, $args ) ) {

				wp_clear_scheduled_hook( $hook, $args );
			}
		}

		/**
		 * Function to Set Cron.
		 *
		 * @param   array  $args The arguments for cron.
		 * @param   string $recurrence The cron recurrence time.
		 * @param   string $hook The cron hook name.
		 * @param   string $time The time for cron.
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function surelywp_sv_set_cron( $args, $recurrence, $hook, $time ) {

			// Get local time zone.
			self::surelywp_sv_set_timezone();

			// clear old cron if schedule.
			self::surelywp_sv_unset_cron( $hook, $args );

			// Schedule new cron.
			wp_schedule_event( $time, $recurrence, $hook, $args );
		}

		/**
		 * Function to get products ids from collection id.
		 *
		 * @param String $collection_id The Id of the collection.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function surelywp_sv_get_collection_product_ids( $collection_id ) {

			$collection_obj = ProductCollection::with( array( 'products' ) )->find( $collection_id );
			$product_ids    = array();
			foreach ( $collection_obj->products->data as $key => $product_obj ) {

				if ( ! empty( $product_obj ) ) {

					$product_ids[] = $product_obj->id;
				}
			}

			return $product_ids;
		}

		/**
		 * Function to Get option by Name.
		 *
		 * @param string $option_name The option name of setting.
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function get_sv_gen_option( $option_name ) {

			$options = get_option( 'surelywp_sv_gen_settings_options' );
			if ( isset( $options['surelywp_sv_gen_settings_options'][ $option_name ] ) ) {
				$options_position = $options['surelywp_sv_gen_settings_options'][ $option_name ];
			} else {
				$options_position = '';
			}

			return $options_position;
		}

		/**
		 * Function to get service option by id.
		 *
		 * @param   string $service_setting_id   The service setting id.
		 * @param   string $option_name The option name of setting.
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function get_sv_option( $service_setting_id, $option_name ) {

			$options = get_option( 'surelywp_sv_settings_options' );
			if ( isset( $options['surelywp_sv_settings_options'][ $service_setting_id ][ $option_name ] ) ) {
				$options_value = $options['surelywp_sv_settings_options'][ $service_setting_id ][ $option_name ];
			} else {
				$options_value = '';
			}

			return $options_value;
		}


		/**
		 * Function to get email templete option by id.
		 *
		 * @param   string $template_key   The service setting id.
		 * @param   string $option_name The option name of setting.
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function get_sv_email_templete_option( $template_key, $option_name = null ) {

			$options_value = '';
			$options       = get_option( 'surelywp_sv_email_templates_options' );
			if ( isset( $options['surelywp_sv_email_templates_options'][ $template_key ] ) ) {
				if ( $option_name ) {
					$options_value = $options['surelywp_sv_email_templates_options'][ $template_key ][ $option_name ];
				} else {
					$options_value = $options['surelywp_sv_email_templates_options'][ $template_key ];
				}
			}

			return $options_value;
		}


		/**
		 * Get user timezone with ip.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function surelywp_sv_set_timezone() {

			$user_ip_obj = self::surelywp_sv_api_request( 'https://api.ipify.org?format=json' );

			if ( ! empty( $user_ip_obj ) ) {
				$user_ip = $user_ip_obj->ip ?? '';
				if ( ! empty( $user_ip ) ) {
					$data_obj = self::surelywp_sv_api_request( 'http://www.geoplugin.net/json.gp?ip=' . $user_ip );
					if ( ! empty( $data_obj ) ) {
						$country_code = $data_obj->geoplugin_countryCode ?? '';
						if ( ! empty( $country_code ) ) {
							$timezone = \DateTimeZone::listIdentifiers( \DateTimeZone::PER_COUNTRY, $country_code );
							date_default_timezone_set( $timezone[0] );
							return true;
						}
					}
				}
			}
			return false;
		}

		/**
		 * Get user ids which have access of services.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function get_sv_access_users_ids() {
			$sv_access_user_roles  = self::get_sv_gen_option( 'sv_access_user_roles' );
			$sv_update_gen_options = self::get_sv_gen_option( 'sv_update_gen_options' );

			$user_ids = array();

			if ( empty( $sv_update_gen_options ) && empty( $sv_access_user_roles ) ) {
				$sv_access_user_roles = array( 'administrator' );
			}

			if ( ! empty( $sv_access_user_roles ) && is_array( $sv_access_user_roles ) ) {
				foreach ( $sv_access_user_roles as $role ) {
					// Ensure role is not empty.
					if ( ! empty( $role ) ) {

						// Query users with the specified role using WP_User_Query.
						$user_query = new WP_User_Query(
							array(
								'role'   => $role,
								'fields' => 'ID', // Return only user IDs.
							)
						);

						// Get the results.
						$users = $user_query->get_results();

						// Check if there are users.
						if ( ! empty( $users ) ) {
							// Add user IDs to the array.
							$user_ids = array_merge( $user_ids, $users );
						}
					}
				}
			}
			return $user_ids;
		}


		/**
		 * Get user emails which have access of services.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function get_sv_access_users_emails() {

			$user_emails       = array();
			$recipient_emails  = self::get_sv_gen_option( 'recipient_emails' );
			$sp_update_options = self::get_sv_gen_option( 'sv_update_gen_options' );

			if ( empty( $recipient_emails ) && empty( $sp_update_options ) ) {
				$user_emails = self::get_admin_emails();
			} else {
				$user_emails = $recipient_emails;
			}

			return $user_emails;
		}


		/**
		 * Function to get uploads folder and file path.
		 *
		 * @param   string $service_id The id of the report.
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_req_path( $service_id ) {

			$service_id = (string) $service_id;

			// Get the uploads directory path.
			$uploads_dir = wp_upload_dir();

			// Define the custom folder name.
			$path['folder_path'] = $uploads_dir['basedir'] . '/surelywp-services-data/' . $service_id . '/requirement';
			$path['folder_url']  = $uploads_dir['baseurl'] . '/surelywp-services-data/' . $service_id . '/requirement';

			return $path;
		}

		/**
		 * Function to get temp uploads folder and file path.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function surelywp_sv_get_temp_req_path() {

			// Get the uploads directory path.
			$uploads_dir = wp_upload_dir();

			// Define the custom folder name.
			$path['folder_path'] = $uploads_dir['basedir'] . '/surelywp-services-temp/requirement';
			$path['folder_url']  = $uploads_dir['baseurl'] . '/surelywp-services-temp/requirement';

			return $path;
		}

		/**
		 * Function to get message attachment file path.
		 *
		 * @param   string $service_id The id of the report.
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_msg_attachment_path( $service_id ) {

			$service_id = (string) $service_id;

			// Get the uploads directory path.
			$uploads_dir = wp_upload_dir();

			// Define the custom folder name.
			$path['folder_path'] = $uploads_dir['basedir'] . '/surelywp-services-data/' . $service_id . '/messages';
			$path['folder_url']  = $uploads_dir['baseurl'] . '/surelywp-services-data/' . $service_id . '/messages';

			return $path;
		}

		/**
		 * Function to store req temp file in uploads folder.
		 *
		 * @param array $file the file array.
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_store_req_temp_file( $file ) {

			$uploads_dir = wp_upload_dir();

			// Define the custom folder name.
			$path['folder_path'] = $uploads_dir['basedir'] . '/surelywp-services-temp/requirement';
			$path['folder_url']  = $uploads_dir['baseurl'] . '/surelywp-services-temp/requirement';
			$folder_path         = $path['folder_path'];

			// Create the custom folder if it doesn't exist.
			if ( ! is_dir( $folder_path ) ) {
				wp_mkdir_p( $folder_path );
			}

			// alllow file extensions.
			$file_types = self::surelywp_sv_get_allow_file_types();

			// file upload max size.
			$file_size = self::get_sv_gen_option( 'file_size' );

			if ( empty( $file_size ) ) {
				$file_size = '5'; // default size 5 MB.
			}

			$file_name      = pathinfo( $file['name'], PATHINFO_FILENAME ); // Get the file name without extension.
			$file_extension = pathinfo( $file['name'], PATHINFO_EXTENSION ); // Get the file extension.
			$file_name      = str_replace( ' ', '-', $file_name ) . '-' . time() . '.' . $file_extension; // Replace spaces with '-' and add timestamp before extension.
			$file_type      = $file['type'];
			$file_temp      = $file['tmp_name'];
			$file_error     = $file['error'];
			$max_size       = intval( $file_size ) * 1024 * 1024;

			$target_path = $folder_path . '/' . $file_name;

			if ( UPLOAD_ERR_OK === $file_error && in_array( $file_type, $file_types ) && $file['size'] <= $max_size ) {

				$is_upload = move_uploaded_file( $file_temp, $target_path );

				if ( $is_upload ) {

					return $target_path;
				}
			}

			return false;
		}


		/**
		 * Function to store req temp file to service data.
		 *
		 * @param string $service_id The id of the service.
		 * @param string $file_path the file array.
		 * @package Services For SureCart
		 * @since   1.4
		 */
		public function surelywp_sv_move_req_temp_file( $service_id, $file_path ) {

			if ( ! empty( $file_path ) && file_exists( $file_path ) ) {

				$path        = $this->surelywp_sv_get_req_path( $service_id );
				$folder_path = $path['folder_path'];

				// Create the custom folder if it doesn't exist.
				if ( ! is_dir( $folder_path ) ) {
					wp_mkdir_p( $folder_path );
				}

				$file_name = basename( $file_path );

				$target_path = $folder_path . '/' . $file_name;

				if ( rename( $file_path, $target_path ) ) {
					return $file_name;
				}
			}

			return false;
		}


		/**
		 * Function to store req file in uploads folder.
		 *
		 * @param  string $service_id The id of the service.
		 * @param array  $file the file array.
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_store_req_file( $service_id, $file ) {

			$path        = $this->surelywp_sv_get_req_path( $service_id );
			$folder_path = $path['folder_path'];

			// Create the custom folder if it doesn't exist.
			if ( ! is_dir( $folder_path ) ) {
				wp_mkdir_p( $folder_path );
			}

			// alllow file extensions.
			$file_types = self::surelywp_sv_get_allow_file_types();

			// file upload max size.
			$file_size = self::get_sv_gen_option( 'file_size' );

			if ( empty( $file_size ) ) {
				$file_size = '5'; // default size 5 MB.
			}

			$file_name      = pathinfo( $file['name'], PATHINFO_FILENAME ); // Get the file name without extension.
			$file_extension = pathinfo( $file['name'], PATHINFO_EXTENSION ); // Get the file extension.
			$file_name      = str_replace( ' ', '-', $file_name ) . '-' . time() . '.' . $file_extension; // Replace spaces with '-' and add timestamp before extension.
			$file_type      = $file['type'];
			$file_temp      = $file['tmp_name'];
			$file_error     = $file['error'];
			$max_size       = intval( $file_size ) * 1024 * 1024;

			$target_path = $folder_path . '/' . $file_name;

			if ( UPLOAD_ERR_OK === $file_error && in_array( $file_type, $file_types ) && $file['size'] <= $max_size ) {

				$is_upload = move_uploaded_file( $file_temp, $target_path );

				if ( $is_upload ) {

					return $file_name;
				}
			}

			return false;
		}

		/**
		 * Function to store data in uploads folder.
		 *
		 * @param  string $service_id The id of the service.
		 * @param array  $file the file array.
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_store_msg_file( $service_id, $file ) {

			$path        = $this->surelywp_sv_get_msg_attachment_path( $service_id );
			$folder_path = $path['folder_path'];

			// Create the custom folder if it doesn't exist.
			if ( ! is_dir( $folder_path ) ) {
				wp_mkdir_p( $folder_path );
			}
			// alllow file extensions.
			$file_types = self::surelywp_sv_get_allow_file_types();

			// file upload max size.
			$file_size = self::get_sv_gen_option( 'file_size' );

			if ( empty( $file_size ) ) {
				$file_size = '5'; // default size 5 MB.
			}

			$file_name      = pathinfo( $file['name'], PATHINFO_FILENAME ); // Get the file name without extension.
			$file_extension = pathinfo( $file['name'], PATHINFO_EXTENSION ); // Get the file extension.
			$file_name      = str_replace( ' ', '-', $file_name ) . '-' . time() . '.' . $file_extension; // Replace spaces with '-' and add timestamp before extension.
			$file_type      = $file['type'];
			$file_temp      = $file['tmp_name'];
			$file_error     = $file['error'];
			$max_size       = intval( $file_size ) * 1024 * 1024;

			$target_path = $folder_path . '/' . $file_name;
			if ( UPLOAD_ERR_OK === $file_error && in_array( $file_type, $file_types ) && $file['size'] <= $max_size ) {

				$is_upload = move_uploaded_file( $file_temp, $target_path );

				if ( $is_upload ) {

					return $file_name;
				}
			}

			return false;
		}

		/**
		 * Function to get allow file types.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function surelywp_sv_get_allow_file_types() {

			// alllow file extensions.
			$file_types = self::get_sv_gen_option( 'file_types' );

			if ( empty( $file_types ) ) {

				$file_types = array(
					'image/jpeg',
					'image/png',
					'image/svg+xml',
					'application/zip',
					'application/x-zip-compressed',
					'application/octet-stream',
					'application/x-compressed-zip',
					'multipart/x-zip', // allow all zip mime types.
					'application/x-zip',
					'application/x-compress',
					'application/zip-compressed',
					'application/x-zip-compressed-fs',
					'application/x-zip-archive',
					'application/pdf', // allow all pdf mime types.
					'application/x-pdf',
					'application/acrobat',
					'application/vnd.pdf',
					'application/pdfxml',
					'application/pdf-embed',
					'application/pdfa',
					'application/msword', // allow all word mime types.
					'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
					'application/vnd.ms-word.document.macroEnabled.12',
					'application/vnd.ms-word.template.macroEnabled.12',
					'application/vnd.ms-word.template',
					'application/rtf',
					'application/vnd.ms-excel', // allow all excel mime types.
					'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
					'application/vnd.ms-excel.sheet.macroEnabled.12',
					'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
					'application/csv',
				);

			} else {

				// include zip all mime types.
				if ( in_array( 'application/zip', $file_types, true ) ) {

					$zip_mime_types = array(
						'application/x-zip-compressed',
						'application/octet-stream',
						'application/x-compressed-zip',
						'multipart/x-zip',
						'application/x-zip',
						'application/x-compress',
						'application/zip-compressed',
						'application/x-zip-compressed-fs',
						'application/x-zip-archive',
					);

					// push in allow file_types.
					$file_types = array_merge( $file_types, $zip_mime_types );
				}

				// inlcude pdf all mime types.
				if ( in_array( 'application/pdf', $file_types, true ) ) {

					$pdf_mime_types = array(
						'application/x-pdf',
						'application/acrobat',
						'application/vnd.pdf',
						'application/pdfxml',
						'application/pdf-embed',
						'application/pdfa',
					);

					// push in allow file_types.
					$file_types = array_merge( $file_types, $pdf_mime_types );
				}

				// inlcude word all mime types.
				if ( in_array( 'application/msword', $file_types, true ) ) {

					$word_mime_types = array(
						'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
						'application/vnd.ms-word.document.macroEnabled.12',
						'application/vnd.ms-word.template.macroEnabled.12',
						'application/vnd.ms-word.template',
						'application/rtf',
					);

					// push in allow file_types.
					$file_types = array_merge( $file_types, $word_mime_types );
				}

				// inlcude excel all mime types.
				if ( in_array( 'application/vnd.ms-excel', $file_types, true ) ) {

					$excel_mime_types = array(
						'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
						'application/vnd.ms-excel.sheet.macroEnabled.12',
						'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
						'application/csv',
					);

					// push in allow file_types.
					$file_types = array_merge( $file_types, $excel_mime_types );
				}
			}

			// add custom mime type.
			$custom_mime_types = self::get_sv_gen_option( 'custom_mime_types' );
			if ( ! empty( $custom_mime_types ) ) {
				$file_types = array_merge( $file_types, $custom_mime_types );
			}

			return $file_types;
		}

		/**
		 * Function to generate random id.
		 *
		 * @param  int $length The length of the id.
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function generate_random_id( $length = 8 ) {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$random_id  = '';

			for ( $i = 0; $i < $length; $i++ ) {
				$random_id .= $characters[ wp_rand( 0, strlen( $characters ) - 1 ) ];
			}

			return $random_id;
		}

		/**
		 * Function to calculate delivery date.
		 *
		 * @param string $service_setting_id The service setting id.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function surelywp_sv_calculate_delivery_date( $service_setting_id, $milestone_id = 0 ) {
			$milestones_fields = self::get_sv_option( $service_setting_id, 'milestones_fields' );
			$sv_delivery_time  = self::get_sv_option( $service_setting_id, 'delivery_time' );

			$delivery_days = '';
			if ( ! empty( $milestones_fields ) && isset( $milestones_fields[ $milestone_id ] ) ) {
				$milestone = $milestones_fields[ $milestone_id ];
				if ( ! empty( $milestone['milestone_delivery_days'] ) ) {
					$delivery_days = (int) $milestone['milestone_delivery_days'];
				}
			}
			if ( empty( $delivery_days ) ) {
				$delivery_days = 3;
			}

			// Current date.
			$current_date = new DateTime();

			// Add days.
			$current_date->modify( '+' . $delivery_days . ' days' );

			// Format the date as MySQL format.
			$formatted_date = $current_date->format( 'Y-m-d' );
			return $formatted_date;
		}

		/**
		 * Function to check current user is service provider.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function surelywp_sv_is_user_service_provider() {

			$current_user_id  = get_current_user_id();
			$access_users_ids = self::get_sv_access_users_ids();
			$result           = ! empty( $access_users_ids ) && in_array( $current_user_id, (array) $access_users_ids ) ? true : false;
			return $result;
		}

		/**
		 * Function to get file info.
		 *
		 * @param string $url the url of the file.
		 * @param string $path the path of the file.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function surelywp_sv_get_file_info( $url, $path ) {

			// Get the file path from the URL.
			$file_path = wp_parse_url( $url, PHP_URL_PATH );

			// Extract file extension.
			$file['extension'] = pathinfo( $file_path, PATHINFO_EXTENSION );

			// Get the file size in bytes.
			$file_size_bytes = filesize( $path );

			// Format the file size.
			$file['size'] = size_format( $file_size_bytes );

			return $file;
		}

		/**
		 * Function to get image extensions.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function surelywp_sv_get_image_extensions() {
			$extensions = array( 'jpg', 'jpeg', 'svg', 'png' );
			return $extensions;
		}


		/**
		 * Function to send service message notification.
		 *
		 * @param array $message_data the array of the message data.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_send_msg_email( $message_data ) {

			global $surelywp_sv_model;
			$service_id           = $message_data['service_id'] ?? '';
			$message_text         = $message_data['message_text'] ?? '';
			$sender_id            = $message_data['sender_id'] ?? '';
			$receiver_id          = $message_data['receiver_id'] ?? '';
			$attachment_file_name = $message_data['attachment_file_name'] ?? '';
			$is_final_delivery    = $message_data['is_final_delivery'] ?? '';
			$service              = $surelywp_sv_model->surelywp_sv_get_service( $service_id );
			$customer_email       = '';
			$customer_name        = '';

			if ( ! empty( $service ) ) { // if have service and user data.

				$sender_data       = get_userdata( $sender_id );
				$sender_user_email = $sender_data->user_email ?? '';
				$sender_user_name  = $sender_data->display_name ?? '';
				$access_users_ids  = self::get_sv_access_users_ids();

				if ( in_array( $sender_id, $access_users_ids ) ) { // send mail to customer for admin new messsage or final delivery.
					$receiver_data     = get_userdata( $receiver_id );
					$emails            = array( $receiver_data->user_email );
					$customer_name     = $receiver_data->display_name;
					$dashboard_page_id = get_option( 'surecart_dashboard_page_id' );
					$dashboard_url     = get_permalink( $dashboard_page_id );
					$view_url          = add_query_arg(
						array(
							'action'     => 'index',
							'model'      => 'services',
							'service_id' => $service_id,
						),
						$dashboard_url
					);

					if ( $is_final_delivery ) {
						$email_template_key = 'final_delivery_email';
					} else {
						$email_template_key = 'admin_msg_notification';
					}
				} else {
					$customer_email     = $sender_user_email;
					$customer_name      = $sender_user_name;
					$emails             = self::get_sv_access_users_emails();
					$email_template_key = 'customer_msg_notification'; // send mail to admins for customer new messsage.
					$view_url           = admin_url( 'admin.php' ) . '?page=sc-services&action=view&service_id=' . $service_id;
				}

				$sv_email_templates = self::surelywp_sv_get_all_email_templetes();
				$email_option       = self::get_sv_email_templete_option( $email_template_key );
				$enable_email       = $email_option['enable_email'] ?? '';

				if ( $enable_email || ! isset( $email_option['sv_update_email_options'] ) ) { // if email is enable.

					$order_id       = $service[0]->order_id ?? '';
					$service_status = $service[0]->service_status ?? '';
					$product_id     = $service[0]->product_id ?? '';
					$service_data   = $this->surelywp_sv_get_service_data( $order_id, $product_id );
					$order_number   = $service_data['order_number'] ?? $order_id;
					$product_name   = $service_data['product_name'] ?? '';
					$website_name   = get_bloginfo( 'name' );

					if ( ! empty( $attachment_file_name ) ) {

						$paths       = $this->surelywp_sv_get_msg_attachment_path( $service_id );
						$folder_url  = $paths['folder_url'];
						$folder_path = $paths['folder_path'];

						$attachment_file_names = json_decode( $attachment_file_name, true );

						$attachment_div = '';
						ob_start();
						foreach ( $attachment_file_names as $key => $attachment_file_name ) {

							$file_url          = $folder_url . '/' . $attachment_file_name;
							$file_path         = $folder_path . '/' . $attachment_file_name;
							$file_info         = self::surelywp_sv_get_file_info( $file_url, $file_path );
							$image_extension   = self::surelywp_sv_get_image_extensions();
							$extension         = $file_info['extension'] ?? '';
							$display_file_name = preg_replace( '/-\d+(?=\.[^.]+$)/', '', $attachment_file_name );
							if ( $extension && in_array( $extension, $image_extension ) ) {
								$attachment_img_url = $file_url;
							} else {
								$attachment_img_url = SURELYWP_SERVICES_ASSETS_URL . '/images/file-pre.png';
							}
							?>
							<div style="width: 200px; border: 1px solid #E5E7EB; min-height: 125px;margin-top: 10px; margin-right: 5px;">
								<a href="<?php echo esc_url( $file_url ); ?>" download="<?php echo esc_attr( $display_file_name ); ?>">
									<img src="<?php echo esc_url( $attachment_img_url ); ?>" alt="attachment-image" style="width: 100%; object-fit: cover;-webkit-touch-callout: none;-webkit-user-select: none;-khtml-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;">
								</a>
								<br>
								<a href="<?php echo esc_url( $file_url ); ?>" download="<?php echo esc_attr( $display_file_name ); ?>" style="padding: 6px 8px; border: 0; border-top: 1px solid #E5E7EB; font-family: 'Open Sans'; font-size: 13px; font-weight: 600; line-height: 20px; color: #6B7280; text-align: center; word-wrap: break-word;-webkit-touch-callout: none;-webkit-user-select: none;-khtml-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;"><?php echo esc_html__( 'Download', 'surelywp-services' ); ?></a>
							</div>
							<?php
						}

						$attachment_div = ob_get_clean();
						$message_text   = $message_text . $attachment_div;

					}

					// Mail heders.
					$headers       = array( 'Content-Type: text/html; charset=UTF-8' );
					$email_subject = $email_option['email_subject'] ?? $sv_email_templates[ $email_template_key ]['email_subject'];
					$email_body    = $email_option['email_body'] ?? $sv_email_templates[ $email_template_key ]['email_body'];
					$view_link     = '<a href="' . esc_url( $view_url ) . '">' . esc_html__( 'Click Here', 'surelywp-services' ) . '</a>';

					// Replace Email Subject Variable.
					$email_subject = str_replace( '{order_id}', $order_number, $email_subject );
					$email_subject = str_replace( '{product_name}', $product_name, $email_subject );
					$email_subject = str_replace( '{website_name}', $website_name, $email_subject );

					// Replace Email Body Variable.
					$email_body = str_replace( '{service_id}', $service_id, $email_body );
					$email_body = str_replace( '{sender_name}', $sender_user_name, $email_body );
					$email_body = str_replace( '{order_id}', $order_number, $email_body );
					$email_body = str_replace( '{product_name}', $product_name, $email_body );
					$email_body = str_replace( '{customer_name}', $customer_name, $email_body );
					$email_body = str_replace( '{customer_email}', $customer_email, $email_body );
					$email_body = str_replace( '{message_content}', $message_text, $email_body );
					$email_body = str_replace( '{service_link}', $view_link, $email_body );
					$email_body = str_replace( '{website_name}', $website_name, $email_body );

					// Replace newline characters with <br> tags to preserve line breaks in HTML.
					$email_body = wpautop( make_clickable( $email_body ) );

					if ( ! empty( $email_body ) ) {

						if ( ! empty( $emails ) ) {
							// Send Mail to every recipient.
							foreach ( $emails  as $email ) {

								if ( ! empty( $email ) ) {

									// set admin name in email body.
									if ( 'admin' === $sv_email_templates[ $email_template_key ]['for'] ) {

										// Get user data by email.
										$admin_data            = get_user_by( 'email', $email );
										$service_provider_name = $admin_data->display_name ?? '';
										$email_body            = str_replace( '{service_provider_name}', $service_provider_name, $email_body );
									} else {
										$email_body = str_replace( '{service_provider_name}', $sender_user_name, $email_body );
									}

									// Send Mail.
									wp_mail( $email, $email_subject, $email_body, $headers );
								}
							}
						}
					}
				}
			}
		}


		/**
		 * Function to send email for below :
		 * Delivery date change email.
		 * Service approve or reject email.
		 * Customer submitted service requirement.
		 * Customer buy new service product.
		 *
		 * @param string $service_id The id of the service.
		 * @param string $email_template_key The key of the email template.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_send_service_email( $service_id, $email_template_key ) {

			global $surelywp_sv_model;
			$service = $surelywp_sv_model->surelywp_sv_get_service( $service_id );

			if ( ! empty( $service ) ) { // if have service data.

				$receiver_id    = $service[0]->user_id ?? '';
				$service_status = $service[0]->service_status ?? '';

				// Clear requirement reminder main cron if service status is changed.
				if ( 'waiting_for_req' !== $service_status && 'requirement_reminder_email' === $email_template_key ) {

					$reminder_hook      = SURELYWP_SERVICES_REQ_REMINDER_CRON;
					$reminder_hook_args = array( intval( $service_id ), 'requirement_reminder_email' );
					self::surelywp_sv_unset_cron( $reminder_hook, $reminder_hook_args );
					return '';
				}

				$delivery_date           = $service[0]->delivery_date ?? '';
				$date_format             = get_option( 'date_format', 'j M Y' );
				$formatted_delivery_date = '<strong>' . wp_date( $date_format, (int) strtotime( $delivery_date ) ) . '</strong>';
				$customer_data           = get_userdata( $receiver_id );
				$customer_email          = $customer_data->user_email ?? '';
				$customer_name           = $customer_data->display_name ?? '';
				$dashboard_page_id       = get_option( 'surecart_dashboard_page_id' );
				$dashboard_url           = get_permalink( $dashboard_page_id );

				// Set emails for mail and service view url.
				$emails                 = array();
				$requirements           = '';
				$view_url               = '';
				$templates_for_admin    = array( 'delivery_approve_email', 'delivery_reject_email', 'customer_requirement_notification', 'new_service_notification' );
				$templates_for_customer = array( 'delivery_date_change_email', 'requirement_reminder_email' );

				if ( in_array( $email_template_key, $templates_for_customer, true ) ) { // Email Templetes for customers.

					$view_url = add_query_arg(
						array(
							'action'     => 'index',
							'model'      => 'services',
							'service_id' => $service_id,
						),
						$dashboard_url
					);
					$emails   = array( $customer_email );

				} elseif ( in_array( $email_template_key, $templates_for_admin, true ) ) { // Email Templetes for service providers(admin).

					$view_url = admin_url( 'admin.php' ) . '?page=sc-services&action=view&service_id=' . $service_id;
					$emails   = self::get_sv_access_users_emails();
				}

				// Get customer requirements.
				if ( 'customer_requirement_notification' === $email_template_key ) {

					ob_start();
					require SURELYWP_SERVICES_TEMPLATE_PATH . '/emails/customer-requirements.php';
					$requirements = ob_get_clean();
				}

				$sv_email_templates = self::surelywp_sv_get_all_email_templetes();
				$email_option       = self::get_sv_email_templete_option( $email_template_key );
				$enable_email       = $email_option['enable_email'] ?? '';

				if ( $enable_email || ! isset( $email_option['sv_update_email_options'] ) ) { // if email is enable.

					// Mail heders.
					$headers = array( 'Content-Type: text/html; charset=UTF-8' );

					$order_id       = $service[0]->order_id ?? '';
					$service_status = $service[0]->service_status ?? '';
					$product_id     = $service[0]->product_id ?? '';
					$service_data   = $this->surelywp_sv_get_service_data( $order_id, $product_id );
					$order_number   = $service_data['order_number'] ?? $order_id;
					$product_name   = $service_data['product_name'] ?? '';
					$website_name   = get_bloginfo( 'name' );

					$email_subject = $email_option['email_subject'] ?? $sv_email_templates[ $email_template_key ]['email_subject'];
					$email_body    = $email_option['email_body'] ?? $sv_email_templates[ $email_template_key ]['email_body'];
					$view_link     = '<a href="' . esc_url( $view_url ) . '">' . esc_html__( 'Click Here', 'surelywp-services' ) . '</a>';

					// Replace Email Subject Variable.
					$email_subject = str_replace( '{order_id}', $order_number, $email_subject );
					$email_subject = str_replace( '{product_name}', $product_name, $email_subject );
					$email_subject = str_replace( '{website_name}', $website_name, $email_subject );

					// Replace Email Body Variable.
					$email_body = str_replace( '{service_id}', $service_id, $email_body );
					$email_body = str_replace( '{delivery_date}', $formatted_delivery_date, $email_body );
					$email_body = str_replace( '{order_id}', $order_number, $email_body );
					$email_body = str_replace( '{product_name}', $product_name, $email_body );
					$email_body = str_replace( '{customer_name}', $customer_name, $email_body );
					$email_body = str_replace( '{customer_email}', $customer_email, $email_body );
					$email_body = str_replace( '{service_link}', $view_link, $email_body );
					$email_body = str_replace( '{website_name}', $website_name, $email_body );

					// Replace newline characters with <br> tags to preserve line breaks in HTML.
					$email_body = nl2br( $email_body );

					$email_body = str_replace( '{requirements}', $requirements, $email_body );

					if ( ! empty( $email_body ) ) {

						$original_email_body = $email_body;

						if ( ! empty( $emails ) ) {

							foreach ( $emails  as $email ) {

								if ( ! empty( $email ) ) {

									// set admin name in email body.
									if ( 'admin' === $sv_email_templates[ $email_template_key ]['for'] ) {

										// Get user data by email.
										$admin_data            = get_user_by( 'email', $email );
										$service_provider_name = $admin_data->display_name ?? '';
										$email_body            = str_replace( '{service_provider_name}', $service_provider_name, $original_email_body );
									}

									// Send Mail.
									wp_mail( $email, $email_subject, $email_body, $headers );
								}
							}
						}
					}
				}
			}
		}

		/**
		 * Function to save product requirements data in transient.
		 *
		 * @param int   $product_id The ID of the product.
		 * @param array $requirements_data The requirements data to save.
		 *
		 * @package Services For SureCart
		 * @since   1.5.7
		 */
		public static function surelywp_save_product_requirements( $product_id, $requirements_data ) {
			// Add submit time.
			$requirements_data['submit_time'] = current_time( 'mysql' );
			// Determine user or guest key.
			if ( is_user_logged_in() ) {
				$user_id       = get_current_user_id();
				$transient_key = 'surelywp_sv_product_requirements_user_' . $user_id;
			} else {
				if ( isset( $_COOKIE['surelywp_guest_id'] ) ) {
					$guest_id = '';
					if ( isset( $_COOKIE['surelywp_guest_id'] ) && ! empty( $_COOKIE['surelywp_guest_id'] ) ) {
						$guest_id = sanitize_text_field( wp_unslash( $_COOKIE['surelywp_guest_id'] ) );
					}
				} else {
					$guest_id = wp_generate_uuid4(); // Generate unique ID.
					setcookie( 'surelywp_guest_id', $guest_id, time() + 24 * HOUR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
				}
				$transient_key = 'surelywp_sv_product_requirements_guest_' . $guest_id;
			}

			// Get existing data.
			$existing = get_transient( $transient_key );
			if ( ! is_array( $existing ) ) {
				$existing = array();
			}

			// Set/Update product data.
			$existing[ $product_id ] = $requirements_data;

			// Save transient (timeout = 1 day, adjust as needed).
			set_transient( $transient_key, $existing, 24 * HOUR_IN_SECONDS );
		}

		/**
		 * Function to get product requirements data from transient.
		 *
		 * @param int $product_id The ID of the product.
		 * @param int $user_id The ID of the user.
		 *
		 * @return array|false The requirements data if available, false otherwise.
		 *
		 * @package Services For SureCart
		 * @since   1.5.7
		 */
		public static function surelywp_get_product_requirements( $product_id, $user_id ) {
			if ( is_user_logged_in() ) {
				if ( isset( $_COOKIE['surelywp_guest_id'] ) && ! empty( $_COOKIE['surelywp_guest_id'] ) ) {
					$guest_id = sanitize_text_field( wp_unslash( $_COOKIE['surelywp_guest_id'] ) );
					setcookie( 'surelywp_guest_id', '', time() - 3600, COOKIEPATH );
					unset( $_COOKIE['surelywp_guest_id'] ); // Clear the cookie after use.
					$transient_key = 'surelywp_sv_product_requirements_guest_' . $guest_id;
				} else {
					$transient_key = 'surelywp_sv_product_requirements_user_' . $user_id;
				}
			} elseif ( isset( $_COOKIE['surelywp_guest_id'] ) && ! empty( $_COOKIE['surelywp_guest_id'] ) ) {
				$guest_id = sanitize_text_field( wp_unslash( $_COOKIE['surelywp_guest_id'] ) );
				$transient_key = 'surelywp_sv_product_requirements_guest_' . $guest_id;
			} else {
				return false;
			}

			$data = get_transient( $transient_key );

			if ( isset( $data[ $product_id ] ) ) {
				return $data[ $product_id ];
			}

			return false;
		}

		/**
		 * Function to delete product requirements transient.
		 *
		 * @param int $product_id The ID of the product.
		 * @param int $user_id The ID of the user.
		 *
		 * @return bool True if transient was deleted, false otherwise.
		 *
		 * @package Services For SureCart
		 * @since   1.5.7
		 */
		public static function surelywp_sv_delete_transient_product_requirements( $product_id, $user_id ) {
			// Determine the transient key.
			if ( is_user_logged_in() ) {
				$transient_key = 'surelywp_sv_product_requirements_user_' . $user_id;
			} elseif ( isset( $_COOKIE['surelywp_guest_id'] ) && ! empty( $_COOKIE['surelywp_guest_id'] ) ) {
				$guest_id      = sanitize_text_field( wp_unslash( $_COOKIE['surelywp_guest_id'] ) );
				$transient_key = 'surelywp_sv_product_requirements_guest_' . $guest_id;
			} else {
				return; // No key found.
			}

			// Get current transient data.
			$existing = get_transient( $transient_key );
			if ( ! is_array( $existing ) ) {
				return; // Nothing to delete.
			}

			// Remove only this product.
			if ( isset( $existing[ $product_id ] ) ) {
				unset( $existing[ $product_id ] );

				// Save back if there’s still data, or delete transient entirely if empty.
				if ( ! empty( $existing ) ) {
					set_transient( $transient_key, $existing, 24 * HOUR_IN_SECONDS );
				} else {
					delete_transient( $transient_key );
					setcookie( 'surelywp_guest_id', '', time() - 3600, COOKIEPATH );
					unset( $_COOKIE['surelywp_guest_id'] ); // Clear the cookie after use.
				}
			}
		}

		/**
		 * Function to change transient guest to user id.
		 *
		 * @param object $checkout The checkout object.
		 *
		 * @package Services For SureCart
		 * @since   1.5.7
		 */
		public function surelywp_sv_change_transient_guest_to_userid( $checkout ) {
			if ( is_wp_error( $checkout ) || empty( $checkout ) ) {
				return;
			}

			$checkout_obj = $checkout->toArray() ?? array();

			if ( ! is_array( $checkout_obj ) || empty( $checkout_obj ) ) {
				return;
			}

			$customer_id = $checkout_obj['customer']['id'] ?? '';
			// fetch user id from customer id.
			$customer = Customer::find( $customer_id );

			if ( empty( $customer ) ) {
				return;
			}

			$customer_user_id = '';
			if ( $customer ) {
				$customer_email = $customer->email ?? '';
				if ( ! empty( $customer_email ) ) {
					// Retrieve the user by email.
					$user = get_user_by( 'email', $customer_email );

					if ( ! empty( $user ) ) {
						$customer_user_id = $user->ID;
					}
				}

				$guest_id = '';
				if ( isset( $_COOKIE['surelywp_guest_id'] ) && ! empty( $_COOKIE['surelywp_guest_id'] ) ) {
					$guest_id = sanitize_text_field( wp_unslash( $_COOKIE['surelywp_guest_id'] ) );
				}
				$old_key = '_transient_surelywp_sv_product_requirements_guest_' . $guest_id;
				$new_key = '_transient_surelywp_sv_product_requirements_user_' . $customer_user_id;

				$transient_key   = 'surelywp_sv_product_requirements_guest_' . $guest_id;
				$guest_transient = get_transient( $transient_key );
				if ( false !== $guest_transient ) {
					// Store under new transient name.
					set_transient( 'surelywp_sv_product_requirements_user_' . $customer_user_id, $guest_transient, WEEK_IN_SECONDS );
					// Optionally delete old transient.
					delete_transient( $transient_key );
				}
			}
		}

		/**
		 * Function to get milestone details by status text.
		 *
		 * @param string $status_text The text of the status.
		 * @param array  $milestones_fields The array of the milestones fields.
		 *
		 * @return array|string The milestone details if found, empty string otherwise.
		 *
		 * @package Services For SureCart
		 * @since   1.7
		 */
		public static function surelywp_sv_get_milestone_details( $status_text, $milestones_fields ){

			$parts        = explode( '_', $status_text );
			$milestone_id = end( $parts );

			if ( ! empty( $milestones_fields ) ) {
				foreach ( $milestones_fields as $key => $milestones_field ) {
					if ( $milestone_id == $key ) {
						$milestones_field['id'] = $key;
						return $milestones_field;
					}
				}
			}

			return '';
		}

		/**
		 * Function to get milestone details by id.
		 *
		 * @param string $milestone_id The id of the milestone.
		 * @param string $service_setting_id The Services Setting ID.
		 *
		 * @return array|string The milestone details if found, empty string otherwise.
		 *
		 * @package Services For SureCart
		 * @since   1.7
		 */
		public static function surelywp_sv_get_milestone_details_by_id( $milestone_id, $service_setting_id ){
			$milestones_fields = self::get_sv_option( $service_setting_id, 'milestones_fields' );

			if ( ! empty( $milestones_fields ) ) {
				foreach ( $milestones_fields as $key => $milestones_field ) {
					if ( $milestone_id == $key ) {
						$milestones_field['id'] = $key;
						return $milestones_field;
					}
				}
			}

			return '';
		}

		/**
		 * Function to get Number Of Revisions Remaining Of The Milestone.
		 *
		 * @param string $milestone_id The id of the milestone.
		 * @param string $service_setting_id The Services Setting ID.
		 * @param int    $service_id the id of the service.
		 * @param array  $milestone_data The Array of the Milestone Fields Data.
		 *
		 * @return array|string The milestone details if found, empty string otherwise.
		 *
		 * @package Services For SureCart
		 * @since   1.7
		 */
		public static function surelywp_sv_number_of_revisions_remaining( $milestone_id, $service_setting_id, $service_id, $milestone_data ) {
			global $wpdb;
			$table_name          = $wpdb->prefix . 'surelywp_sv_services';
			$check_status        = 'milestone_submit_' . $milestone_id;
			$revisions_remaining = $wpdb->get_var(
				$wpdb->prepare( "SELECT revisions_remaining FROM $table_name WHERE service_id = %d", $service_id )
			);

			if ( isset( $milestone_data['milestones_revision_allowed'] ) ) {
				$milestones_revision_allowed = $milestone_data['milestones_revision_allowed'];
			}
			if ( isset( $milestone_data['milestones_require_approval'] ) ) {
				$milestones_require_approval = $milestone_data['milestones_require_approval'];
			}
			$is_limit_reached = false;
			if ( null === $revisions_remaining ) {
				$is_limit_reached  = null;
			} elseif ( $revisions_remaining == 0 ) {
				$is_limit_reached = ( $revisions_remaining == 0 ) ? true : false;
			}

			if ( isset( $milestones_revision_allowed ) ) {
				return array(
					'is_limit_reached'            => $is_limit_reached,
					'remaining_revision'          => $revisions_remaining,
					'milestones_revision_allowed' => $milestones_revision_allowed,
					'milestones_require_approval' => $milestones_require_approval,
				);
			} else {
				return array(
					'is_limit_reached'   => $is_limit_reached,
					'remaining_revision' => $revisions_remaining,
				);
			}
		}

		/**
		 * Function to add Default Milestone in services of previous version of pluign.
		 *
		 * @package Services For SureCart
		 * @since   1.7
		 */
		public function surelywp_sv_add_default_milestone_field() {

			$surelywp_sv_settings_options_all = get_option( 'surelywp_sv_settings_options' );
			$surelywp_sv_settings_options     = $surelywp_sv_settings_options_all['surelywp_sv_settings_options'] ?? '';

			if ( empty( $surelywp_sv_settings_options ) ) {
				return;
			}

			foreach ( $surelywp_sv_settings_options as $service_setting_id => $settings ) {

				$milestones_fields = $settings['milestones_fields'] ?? array();

				// Only add if milestone fields are missing or empty.
				if ( empty( $milestones_fields ) ) {
					$delivery_time = $surelywp_sv_settings_options_all['surelywp_sv_settings_options'][ $service_setting_id ]['delivery_time'];
					$default_milestone = array(
						array(
							'milestones_field_label'      => __( 'Final Delivery', 'surelywp-services' ),
							'milestones_require_approval' => '1',
							'milestones_revision_allowed' => '1',
							'milestone_revisions_number'  => '3',
							'milestone_delivery_days'     => $delivery_time ?? 3,
						),
					);

					$surelywp_sv_settings_options_all['surelywp_sv_settings_options'][ $service_setting_id ]['milestones_fields'] = $default_milestone;
					$surelywp_sv_settings_options_all['surelywp_sv_settings_options'][ $service_setting_id ]['milestone_fields_count'] = '1';
				}
			}

			update_option( 'surelywp_sv_settings_options', $surelywp_sv_settings_options_all );
		}

		/**
		 * Function to set revisions remaining of the milestone.
		 *
		 * @param string $service_setting_id The Services Setting ID.
		 * @param string $milestone_id The id of the milestone.
		 *
		 * @package Services For SureCart
		 * @since   1.7
		 */
		public static function surelywp_sv_get_revisions_allowed( $service_setting_id, $milestone_id ) {
			$milestones_fields = self::get_sv_option( $service_setting_id, 'milestones_fields' );

			if ( ! empty( $milestones_fields ) ) {
				foreach ( $milestones_fields as $key => $milestones_field ) {
					if ( $milestone_id == $key ) {
						$milestones_field['id'] = $key;
						return $milestones_field['milestone_revisions_number'] ?? '';
					}
				}
			}

			return '';
		}

		/**
		 * Function to get all email templetes.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function surelywp_sv_get_all_email_templetes() {

			$sv_email_templates = array(

				'new_service_notification'          => array(
					'id'            => 'new-service-notification',
					'for'           => 'admin',
					'name'          => esc_html__( 'New Service Order Notification Email', 'surelywp-services' ),
					'email_subject' => esc_html__( 'A New Service Order #{order_id} Has Been Placed', 'surelywp-services' ),
					'email_body'    => wp_kses_post(
						__(
							'Hi {service_provider_name},

A new service order has been placed by a customer!

<b>Order Details:</b>
Service ID: {service_id}
Order ID: {order_id}
Product: {product_name}
Customer Name: {customer_name}
Customer Email Address: {customer_email}

<b>Next Steps:</b>
To view the service details, please click the link below:
{service_link}
',
							'surelywp-services'
						)
					),
				),
				'customer_requirement_notification' => array(
					'id'            => 'customer-requirement-notification',
					'for'           => 'admin',
					'name'          => esc_html__( 'Service Requirements Received Notification Email', 'surelywp-services' ),
					'email_subject' => esc_html__( 'Service Requirements Received: Order #{order_id}', 'surelywp-services' ),
					'email_body'    => wp_kses_post(
						__(
							'Hi {service_provider_name},

Great news! Your customer has submitted the required service details for their order.

<b>Order Details:</b>
Service ID: {service_id}
Order ID: {order_id}
Product: {product_name}
Customer Name: {customer_name}
Customer Email Address: {customer_email}

<b>Service Requirements:</b>
{requirements}

<b>Next Steps:</b>
Please review the submitted requirements and proceed with fulfilling the service at your earliest convenience. You can access the order details and communicate with the customer directly within the website. You can access the service details with this link: 
{service_link}',
							'surelywp-services'
						)
					),
				),
				'customer_msg_notification'         => array(
					'id'            => 'customer-msg-notification',
					'for'           => 'admin',
					'name'          => esc_html__( 'New Message From Customer Notification Email', 'surelywp-services' ),
					'email_subject' => esc_html__( 'New Message From Customer Regarding Order #{order_id}', 'surelywp-services' ),
					'email_body'    => wp_kses_post(
						__(
							'Hi {service_provider_name},

You have a new message from a customer regarding their order for {product_name} (Order #{order_id}).

<b>Customer Details:</b>
Service ID: {service_id}
Customer Name: {customer_name}
Email Address: {customer_email}

<b>Message:</b>
{message_content}

<b>Next Steps:</b>
You can view the message and respond directly to the customer within the website using the link below:
{service_link}',
							'surelywp-services'
						)
					),
				),
				'delivery_reject_email'             => array(
					'id'            => 'delivery-reject-email',
					'for'           => 'admin',
					'name'          => esc_html__( 'Customer Requested Revisions Notification Email', 'surelywp-services' ),
					'email_subject' => esc_html__( 'Action Needed: Customer Requested Revisions Delivery Order #{order_id} For ({product_name})', 'surelywp-services' ),
					'email_body'    => wp_kses_post(
						__(
							'Hi {service_provider_name},

The customer has requested revisions for the order #{order_id} ({product_name}).

<b>Order Details:</b>
Service ID: {service_id}
Order ID: {order_id}
Product: {product_name}
Customer Name: {customer_name}
Email Address: {customer_email}

<b>Next Steps:</b>
The customer may have already provided additional details about revisions needed within the website, so be sure to check their messages for any additional information.

You can access the order details and communicate with the customer directly within the website using the link below:
{service_link}',
							'surelywp-services'
						)
					),
				),
				'delivery_approve_email'            => array(
					'id'            => 'delivery-approve-email',
					'for'           => 'admin',
					'name'          => esc_html__( 'Delivery Approved By The Customer Notification Email', 'surelywp-services' ),
					'email_subject' => esc_html__( 'Great news! Yours Customer Approved The Final Delivery For Order #{order_id} For {product_name}', 'surelywp-services' ),
					'email_body'    => wp_kses_post(
						__(
							'Hi {service_provider_name},

We\'re happy to inform you that the customer has approved the final delivery for Order #{order_id} ({product_name})!

<b>Order Details:</b>
Service ID: {service_id}
Order ID: {order_id}
Product: {product_name}
Customer Name: {customer_name}
Email Address: {customer_email}

<b>Next Steps (Optional):</b>
Depending on your service and post-delivery needs, you may want to consider reaching out to the customer:

<b>Thank You Message:</b> A quick thank-you message for their approval and business can be a nice touch.
<b>Follow-up Survey:</b> If you gather customer feedback, you can send a brief survey to understand their experience.
<b>Delivery Instructions (Optional):</b> If the product or service requires specific instructions for use, you can send them to the customer at this point.

You can access the order details and communicate with the customer directly within the app using the link below:
{service_link}',
							'surelywp-services'
						)
					),
				),
				'requirement_ask_email'             => array(
					'id'            => 'requirement-ask-notification',
					'for'           => 'customer',
					'name'          => esc_html__( 'Submit Service Requirements Notification Email', 'surelywp-services' ),
					'email_subject' => esc_html__( 'Action Required: Provide Details For Order #{order_id}', 'surelywp-services' ),
					'email_body'    => wp_kses_post(
						__(
							'Dear {customer_name},

Thank you for your order #{order_id} for {product_name}. We\'re excited to get started!

If you have not yet provided the services requirement details, please go to your customer dashboard and provide that information before we can proceed. Please submit this information as soon as possible so we can start working on your order. You can easily submit the details by clicking the link below:
{service_link}

If you have any questions about what information is needed, please don\'t hesitate to reply to this email, and we will be happy to help!

Sincerely,
The {website_name} Team',
							'surelywp-services'
						),
					),
				),
				'requirement_reminder_email'        => array(
					'id'            => 'requirement-reminder-notification',
					'for'           => 'customer',
					'name'          => esc_html__( 'Submit Service Requirements Reminder Notification Email', 'surelywp-services' ),
					'email_subject' => esc_html__( 'Gentle Reminder: Complete Your Order For {product_name} (#{order_id})', 'surelywp-services' ),
					'email_body'    => wp_kses_post(
						__(
							'Dear {customer_name},

We hope you\'re doing well! We are just following up on your recent order (Order #{order_id}) for {product_name}. We are excited to get started and deliver it to you as soon as possible.

However, we have not yet received the required information about your order. We kindly ask you to provide the information as soon as possible to ensure we can start working on your order and get it delivered to you sooner! You can easily submit the details by clicking the link below:
{service_link}

We understand that things can get busy, so if you need a bit more time to gather the information, please let us know. If you have any questions about what information is needed, please don\'t hesitate to reply to this email, and we\'ll be happy to assist you.

Sincerely,
The {website_name} Team',
							'surelywp-services'
						),
					),
				),
				'admin_msg_notification'            => array(
					'id'            => 'admin-msg-notification',
					'for'           => 'customer',
					'name'          => esc_html__( 'New Message From Service Provider Notification Email', 'surelywp-services' ),
					'email_subject' => esc_html__( 'New Message From {website_name} Regarding Order #{order_id}', 'surelywp-services' ),
					'email_body'    => wp_kses_post(
						__(
							'Hi {customer_name},

You have a new message from {service_provider_name}, the service provider for your order (Order #{order_id}) for {product_name}.

<b>Message:</b>
{message_content}

<b>Next Steps:</b>
You can view the message and respond directly to the service provider within the website using the link below:
{service_link}

Sincerely,
The {website_name} Team',
							'surelywp-services'
						)
					),
				),
				'delivery_date_change_email'        => array(
					'id'            => 'delivery-date-change-email',
					'for'           => 'customer',
					'name'          => esc_html__( 'Delivery Date Changed Notification Email', 'surelywp-services' ),
					'email_subject' => esc_html__( 'Order Update: Delivery Date Changed For Your Order For {product_name}', 'surelywp-services' ),
					'email_body'    => wp_kses_post(
						__(
							'Hi {customer_name},

We\'re reaching out to inform you that the delivery date for your service for {product_name} has been changed.

The new delivery date is {delivery_date}. We understand this may be an inconvenience, and we sincerely apologize for the delay. We are doing everything we can to ensure that your service reaches you as soon as possible.

If you have any questions or concerns about the revised delivery date, please don\'t hesitate to send us a message on the website:
{service_link}

Thank you for your understanding and patience.

Sincerely,
The {website_name} Team',
							'surelywp-services'
						),
					),
				),
				'final_delivery_email'              => array(
					'id'            => 'final-delivery-email',
					'for'           => 'customer',
					'name'          => esc_html__( 'Final Delivery Notification Email', 'surelywp-services' ),
					'email_subject' => esc_html__( 'You Have Received A Delivery For Your Service Order For {product_name}', 'surelywp-services' ),
					'email_body'    => wp_kses_post(
						__(
							'Hi {customer_name},

We are pleased to inform you that your service for {product_name} has been successfully delivered!

<b>Service Details:</b>
Service ID: {service_id}
Order ID: {order_id}
Product: {product_name}

<b>Review And Approve Your Delivery:</b>
Please take a moment to review and approve the final delivery. If you\'re happy with it, you can easily approve it by clicking the link below:
{service_link}

We hope you\'re happy with your completed service! If you need any additional revisions, or if you have any questions or concerns about the service provided, please let us know by responding within the website.

Sincerely,
The {website_name} Team',
							'surelywp-services'
						),
					),
				),
			);

			return $sv_email_templates;
		}
	}
	/**
	 * Unique access to instance of Surelywp_Services class
	 *
	 * @package Services For SureCart
	 * @since   1.0.0
	 */
	function Surelywp_Services() {  // phpcs:ignore
		$instance = Surelywp_Services::get_instance();
		return $instance;
	}
}