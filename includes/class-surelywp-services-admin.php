<?php
/**
 * Admin init class
 *
 * @package Services For SureCart
 * @author  SurelyWP
 * @version 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'SURELYWP_SERVICES' ) ) {
	exit;
}

if ( ! class_exists( 'Surelywp_Services_Admin' ) ) {

	/**
	 * Initiator class. Create and populate admin views.
	 *
	 * @package Services For SureCart
	 * @since   1.0.0
	 */
	class Surelywp_Services_Admin {

		/**
		 * Single instance of the class
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 * @var     \Surelywp_Services_Admin
		 */
		protected static $instance;

		/**
		 * Report panel
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 * @var string Panel hookname
		 */
		protected $panel = null;


		/**
		 * Tab name
		 *
		 * @var   string
		 * @since 1.0.0
		 */
		public $tab;

		/**
		 * Plugin options
		 *
		 * @var   array
		 * @since 1.0.0
		 */
		public $options;


		/**
		 * Plugin model
		 *
		 * @var   object
		 * @since 1.0.0
		 */
		public $model;

		/**
		 * List of available tab for Reports panel
		 *
		 * @var     array
		 * @access  public
		 */
		public $available_tabs = array();

		/**
		 * Returns single instance of the class
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 * @return \Surelywp_Services_Admin
		 */
		public static function get_instance() {

			if ( is_null( static::$instance ) ) {
				static::$instance = new static();
			}

			return static::$instance;
		}

		/**
		 * Constructor of the class
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function __construct() {

			global $surelywp_model;

			$this->model = $surelywp_model;

			// init admin processing.
			add_action( 'init', array( $this, 'surelywp_sv_init' ) );

			// Run Installation.
			add_action( 'init', array( $this, 'surelywp_sv_install' ) );

			// Reset Settings.
			add_action( 'admin_init', array( $this, 'surelywp_sv_reset_settings' ) );

			// Update Service.
			add_action( 'admin_init', array( $this, 'surelywp_sv_update_service' ) );

			add_action( 'admin_init', array( $this, 'surelywp_sv_on_settings_save' ) );

			add_action( 'admin_menu', array( $this, 'surelywp_sv_register_panel' ), 5 );

			add_action( 'admin_enqueue_scripts', array( $this, 'surelywp_sv_customer_scripts' ) );

			add_action( 'admin_menu', array( $this, 'set_register_setting' ) );

			// Add new Service menu in surecart admin menu.
			add_action( 'admin_menu', array( $this, 'surelywp_sv_add_admin_menu' ) );

			// Add Blocks on various locations.
			add_action( 'admin_footer', array( $this, 'surelywp_sv_add_blocks' ) );

			// add Service columnn in surecart admin order list table.
			add_action( 'admin_init', array( $this, 'surelywp_sv_order_columns' ) );

			// Add the plugin action link.
			add_filter( 'plugin_action_links_' . SURELYWP_SERVICES_INIT, array( $this, 'surelywp_sv_add_plugin_action_link' ) );

			// Delete the service.
			add_action( 'admin_init', array( $this, 'surelywp_sv_handle_delete_action' ) );
		}

		/**
		 * Function to handle delete service
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_handle_delete_action() {

			if (
				is_admin() &&
				isset( $_GET['page'], $_GET['sv_action'], $_GET['service_delete'] ) &&
				$_GET['page'] === 'sc-services' &&
				$_GET['sv_action'] === 'delete'
			) {
				$service_id = absint( $_GET['service_delete'] );

				if ( ! current_user_can( 'manage_options' ) ) {
					wp_die( 'Unauthorized user' );
				}

				if ( ! wp_verify_nonce( $_GET['_wpnonce'] ?? '', 'delete_service_' . $service_id ) ) {
					wp_die( 'Security check failed' );
				}

				global $surelywp_sv_model;
				$surelywp_sv_model->surelywp_sv_delete_service_by_id( $service_id );

				// Delete folder and contents.
				$uploads_dir = wp_upload_dir();
				$folder_path = $uploads_dir['basedir'] . '/surelywp-services-data/' . $service_id;

				if ( file_exists( $folder_path ) && is_dir( $folder_path ) ) {
					$this->surelywp_delete_directory_recursive( $folder_path );
				}

				wp_redirect( admin_url( 'admin.php?page=sc-services&deleted=1' ) );
				exit;
			}
		}

		/**
		 * Recursively deletes a directory and all its files and subdirectories.
		 *
		 * @param string $dir Absolute path to the directory to delete.
		 * @return void
		 */
		public function surelywp_delete_directory_recursive( $dir ) {

			if ( ! file_exists( $dir ) ) {
				return;
			}

			if ( is_file( $dir ) ) {
				unlink( $dir );
				return;
			}

			$items = scandir( $dir );
			foreach ( $items as $item ) {
				if ( $item === '.' || $item === '..' ) {
					continue;
				}

				$path = $dir . DIRECTORY_SEPARATOR . $item;

				if ( is_dir( $path ) ) {
					$this->surelywp_delete_directory_recursive( $path );
				} else {
					unlink( $path );
				}
			}
			rmdir( $dir );
		}


		/**
		 * Add Update Action Link.
		 *
		 * Will be remove this function after add on the all fw.
		 *
		 * @param array $links The array of the links.
		 * @package SurelyWP\PluginFramework\Classes
		 * @since   1.0.0
		 */
		public function surelywp_sv_add_plugin_action_link( $links ) {

			// If the added by the FW.
			if ( isset( $links['surelywp_services_panel'] ) ) {
				return $links;
			}

			$settings_link                    = '<a href="' . admin_url( 'admin.php?page=surelywp_services_panel' ) . '">' . esc_html__( 'Settings', 'surelywp-services' ) . '</a>';
			$links['surelywp_services_panel'] = $settings_link;
			return $links;
		}

		/**
		 * Add service column on admin order list table.
		 *
		 * @package Services For SureCart
		 * @since 1.5
		 */
		public function surelywp_sv_order_columns() {

			$page = isset( $_GET['page'] ) && ! empty( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

			if ( 'sc-orders' === $page ) {

				// filter to register a new column services.
				add_filter(
					'manage_' . $page . '_columns',
					function ( $columns ) {
						$service_plural_name = Surelywp_Services::get_sv_plural_name();
						$columns['services'] = esc_html( $service_plural_name );
						return $columns;
					}
				);

				add_action(
					'manage_' . $page . '_custom_column',
					function ( $column_name, $data ) {

						global $surelywp_sv_model;
						if ( 'services' === $column_name ) {
							$order_id = $data->id ?? '';
							if ( $order_id ) {
								$services = $surelywp_sv_model->surelywp_sv_get_service_by_order( $order_id );
								if ( $services ) {
									$last_key = array_key_last( $services );
									foreach ( $services as $key => $service ) {
										$service_id = $service->service_id ?? '';
										$view_url   = admin_url( 'admin.php' ) . '?page=sc-services&action=view&service_id=' . $service_id;
										printf( '<a class="service_view row-title" href="%s"> #%s%s</a>', esc_url( $view_url ), esc_html( $service_id ), $last_key !== $key ? ', ' : '' );
									}
								} else {
									echo '-';
								}
							} else {
								echo '-';
							}
						}
					},
					10,
					2
				);
			}
		}

		/**
		 * Add view service block.
		 *
		 * @package Services For SureCart
		 * @since 1.0.0
		 */
		public function surelywp_sv_add_blocks() {

			$page   = isset( $_GET['page'] ) && ! empty( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
			$action = isset( $_GET['action'] ) && ! empty( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
			$id     = isset( $_GET['id'] ) && ! empty( $_GET['id'] ) ? sanitize_text_field( wp_unslash( $_GET['id'] ) ) : '';

			if ( 'edit' === $action && ! empty( $id ) ) {

				if ( 'sc-orders' === $page ) { // add service list block for admin individual order view.
					$order_id = $id;
					include SURELYWP_SERVICES_TEMPLATE_PATH . '/admin/service-view-block.php';
				} elseif ( 'sc-products' === $page ) { // add service enable block on surecart product page.
					$product_id = $id;
					include SURELYWP_SERVICES_TEMPLATE_PATH . '/admin/product-service-block.php';
				} elseif ( 'sc-customers' === $page ) {
					$customer_id = $id;
					include SURELYWP_SERVICES_TEMPLATE_PATH . '/admin/customer-service-block.php'; // add customers services list block on surecart customer view page.
				}
			}
		}

		/**
		 * Reset Settings.
		 *
		 * @package Services For SureCart
		 * @since 1.0.0
		 */
		public function surelywp_sv_reset_settings() {

			global $surelywp_sv_model;

			$action             = isset( $_GET['action'] ) && ! empty( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
			$tab                = isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '';
			$is_reset           = isset( $_POST['surelywp_ric_settings_reset'] ) ? true : false;
			$service_setting_id = isset( $_GET['service_setting_id'] ) && ! empty( $_GET['service_setting_id'] ) ? sanitize_text_field( wp_unslash( $_GET['service_setting_id'] ) ) : '';
			$template           = isset( $_GET['template'] ) && ! empty( $_GET['template'] ) ? sanitize_text_field( wp_unslash( $_GET['template'] ) ) : '';

			if ( ! empty( $service_setting_id ) ) { // For individual Services.

				$surelywp_obj                 = Surelywp_Services();
				$surelywp_sv_settings_options = get_option( 'surelywp_sv_settings_options' );

				// For Remove Service.
				if ( ! empty( $action ) && 'remove_service' === $action ) {

					// Remove product meta.
					$surelywp_obj->surelywp_sv_remove_product_meta( $service_setting_id );

					// Cancel the services.
					$surelywp_sv_model->surelywp_sv_cancel_services( $service_setting_id );

					// Remove created Service.
					unset( $surelywp_sv_settings_options['surelywp_sv_settings_options'][ $service_setting_id ] );
					update_option( 'surelywp_sv_settings_options', $surelywp_sv_settings_options );

					$redirect_to_settings = add_query_arg(
						array(
							'page' => 'surelywp_services_panel',
							'tab'  => 'surelywp_sv_settings',
						),
						admin_url( 'admin.php' )
					);

					wp_safe_redirect( $redirect_to_settings );
					die();

				} elseif ( $is_reset ) { // For Reset Settings.

					// Remove product meta.
					$surelywp_obj->surelywp_sv_remove_product_meta( $service_setting_id );

					// Cancel the services.
					$surelywp_sv_model->surelywp_sv_cancel_services( $service_setting_id );

					$surelywp_sv_settings_options['surelywp_sv_settings_options'][ $service_setting_id ] = array( 'service_title' => $surelywp_sv_settings_options['surelywp_sv_settings_options'][ $service_setting_id ]['service_title'] ?? '' );
					update_option( 'surelywp_sv_settings_options', $surelywp_sv_settings_options );
					$redirect_to_settings = add_query_arg(
						array(
							'page'               => 'surelywp_services_panel',
							'tab'                => 'surelywp_sv_settings',
							'action'             => 'edit_service',
							'service_setting_id' => $service_setting_id,
						),
						admin_url( 'admin.php' )
					);

					wp_safe_redirect( $redirect_to_settings );
					die();
				}
			}

			// For Reset Email Templete Options.
			if ( $is_reset && ! empty( $template ) && 'surelywp_sv_email_templates' === $tab ) {

				$surelywp_sv_email_templates_options = get_option( 'surelywp_sv_email_templates_options' );

				$surelywp_sv_email_templates_options['surelywp_sv_email_templates_options'][ $template ] = array();
				update_option( 'surelywp_sv_email_templates_options', $surelywp_sv_email_templates_options );
					$redirect_to_settings = add_query_arg(
						array(
							'page'     => 'surelywp_services_panel',
							'tab'      => 'surelywp_sv_email_templates',
							'action'   => 'edit_sv_email_template',
							'template' => $template,
						),
						admin_url( 'admin.php' )
					);

					wp_safe_redirect( $redirect_to_settings );
					die();

			}

			// For Reset General Services Settings.
			if ( $is_reset && 'surelywp_sv_gen_settings' === $tab ) {
				$options_keyname = 'surelywp_sv_gen_settings_options';
				delete_option( $options_keyname );
			}
		}

		/**
		 * Update Service.
		 *
		 * @package Services For SureCart
		 * @since 1.0.0
		 */
		public function surelywp_sv_update_service() {

			global $surelywp_sv_model;

			$service_id = isset( $_GET['service_id'] ) && ! empty( $_GET['service_id'] ) ? sanitize_text_field( wp_unslash( $_GET['service_id'] ) ) : '';
			$status     = isset( $_GET['status'] ) && ! empty( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';

			$valid_status = array( 'waiting_for_req', 'waiting_for_contract', 'service_start', 'service_canceled', 'service_complete', 'service_submit' );

			if ( ! empty( $service_id ) && ! empty( $status ) && in_array( $status, $valid_status, true ) ) {

				$service_data['service_status'] = $status;
				if ( 'service_start' === $status ) {
					$service_setting_id            = $surelywp_sv_model->surelywp_sv_get_service_setting_id( $service_id );
					$service_data['delivery_date'] = Surelywp_Services()->surelywp_sv_calculate_delivery_date( $service_setting_id );
				} else {
					$service_data['delivery_date'] = current_time( 'mysql' );
				}

				$is_updated = $surelywp_sv_model->surelywp_sv_update_service( $service_id, $service_data );

				// Clear requirement reminder main cron.
				$hook = SURELYWP_SERVICES_REQ_REMINDER_CRON;
				$args = array( intval( $service_id ), 'requirement_reminder_email' );
				Surelywp_Services::surelywp_sv_unset_cron( $hook, $args );

				// Clear service auto complete hook.
				$auto_complete_sv_hook      = SURELYWP_SERVICES_AUTO_COMPLETE_CRON;
				$auto_complete_sv_hook_args = array( intval( $service_id ) );
				Surelywp_Services::surelywp_sv_unset_cron( $auto_complete_sv_hook, $auto_complete_sv_hook_args );

				// Add activity.
				if ( 'service_complete' === $status ) {

					/**
					 * Fire on service mark complete by service providers.
					 */
					do_action( 'surelywp_services_mark_complete', $service_id );

					$activity_data = array(
						'service_id'    => $service_id,
						'activity_type' => 'service_complete_by_admin',
					);

					$surelywp_sv_model->surelywp_sv_insert_activity( $activity_data );

				} elseif ( 'service_canceled' === $status ) {

					/**
					 * Fire on service mark cancelled by service providers.
					 */
					do_action( 'surelywp_services_mark_cancelled', $service_id );

					$activity_data = array(
						'service_id'    => $service_id,
						'activity_type' => 'service_canceled',
					);

					$surelywp_sv_model->surelywp_sv_insert_activity( $activity_data );

				} elseif ( 'service_start' === $status ) {

					/**
					 * Fire on service mark service start by service providers.
					 */
					do_action( 'surelywp_services_mark_start', $service_id );

					$activity_data = array(
						'service_id'    => $service_id,
						'activity_type' => 'service_start',
					);

					$surelywp_sv_model->surelywp_sv_insert_activity( $activity_data );
				}
			}
		}

		/**
		 * Update product meta.
		 *
		 * @package Services For SureCart
		 * @since 1.0.0
		 */
		public function surelywp_sv_on_settings_save() {

			global $surelywp_model, $surelywp_sv_model;

			$action             = isset( $_POST['action'] ) && ! empty( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : '';
			$option_page        = isset( $_POST['option_page'] ) && ! empty( $_POST['option_page'] ) ? sanitize_text_field( wp_unslash( $_POST['option_page'] ) ) : '';
			$current_setting_id = isset( $_POST['current_setting_id'] ) && ! empty( $_POST['current_setting_id'] ) ? sanitize_text_field( wp_unslash( $_POST['current_setting_id'] ) ) : '';

			if ( 'update' === $action && 'surelywp_sv_settings_options' === $option_page && ! empty( $current_setting_id ) ) {

				$service_option = isset( $_POST['surelywp_sv_settings_options']['surelywp_sv_settings_options'][ $current_setting_id ] ) && ! empty( $_POST['surelywp_sv_settings_options']['surelywp_sv_settings_options'][ $current_setting_id ] ) ? $surelywp_model->surelywp_escape_slashes_deep( $_POST['surelywp_sv_settings_options']['surelywp_sv_settings_options'][ $current_setting_id ] ) : array();

				if ( ! empty( $service_option ) ) {

					$surelywp_obj = Surelywp_Services();

					// update product meta for new specific to old specific case.
					if ( 'specific' === $service_option['services_product_type'] ) {

						$new_prodcuct_ids = isset( $service_option['service_products'] ) && null !== $service_option['service_products'] ? $service_option['service_products'] : array();

						$existing_settings         = get_option( 'surelywp_sv_settings_options' );
						$existing_settings         = $existing_settings['surelywp_sv_settings_options'][ $current_setting_id ] ?? array();
						$old_services_product_type = isset( $existing_settings['services_product_type'] ) && null !== $existing_settings['services_product_type'] ? $existing_settings['services_product_type'] : '';

						$ids_for_insert_meta = array();
						if ( 'specific' === $old_services_product_type ) {

							$old_product_ids     = isset( $existing_settings['service_products'] ) && null !== $existing_settings['service_products'] ? $existing_settings['service_products'] : array();
							$ids_for_remove_meta = array_diff( (array) $old_product_ids, (array) $new_prodcuct_ids );
							$ids_for_insert_meta = array_diff( (array) $new_prodcuct_ids, (array) $old_product_ids );

							if ( ! empty( $ids_for_remove_meta ) ) {
								$metadata = array(
									'is_service_enable'  => '',
									'service_setting_id' => '',
								);

								// remove meta from the product.
								foreach ( $ids_for_remove_meta as $product_id ) {
									$surelywp_obj->surelywp_sv_update_product( $product_id, $metadata );
								}
							}
						} else {
							$ids_for_insert_meta = $new_prodcuct_ids;
						}

						if ( ! empty( $ids_for_insert_meta ) ) {

							$metadata = array(
								'is_service_enable'  => 1,
								'service_setting_id' => $current_setting_id,
							);

							// insert meta into the product.
							foreach ( $ids_for_insert_meta as $product_id ) {
								$surelywp_obj->surelywp_sv_update_product( $product_id, $metadata );
							}
						}
					} elseif ( 'all' === $service_option['services_product_type'] || 'specific_collection' === $service_option['services_product_type'] ) { // update product meta for (new-all to old-specific case) or new-collection old-specific case.

						$surelywp_obj->surelywp_sv_remove_product_meta( $current_setting_id );
					}

					// Modified the service status for services created using this setting ID.
					$new_status           = '';
					$ask_for_contract     = $service_option['ask_for_contract'] ?? '';
					$ask_for_requirements = $service_option['ask_for_requirements'] ?? '';
					if ( ! empty( $ask_for_contract ) && ! empty( $ask_for_requirements ) ) {
						$new_status = 'waiting_for_contract';
					} elseif ( empty( $ask_for_contract ) && ! empty( $ask_for_requirements ) ) {
						$new_status = 'waiting_for_req';
					} elseif ( ! empty( $ask_for_contract ) && empty( $ask_for_requirements ) ) {
						$new_status = 'waiting_for_contract';
					} elseif ( empty( $ask_for_contract ) && empty( $ask_for_requirements ) ) {
						$new_status = 'service_start';
					}

					if ( ! empty( $new_status ) ) {
						$surelywp_sv_model->surelywo_sv_fixed_service_status( $current_setting_id, $new_status, $service_option );
					}
				}
			}
		}

		/**
		 * Add service admin menu for super admin.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_add_admin_menu() {

			global $surelywp_sv_model;
			$current_user_id  = get_current_user_id();
			$access_users_ids = Surelywp_Services::get_sv_access_users_ids();

			$in_progress_services_count = $surelywp_sv_model->surelywp_sv_get_total_in_progress_services();

			$count_tag = '';
			if ( $in_progress_services_count ) {
				$count_tag = '<span class="update-plugins count-1"><span class="update-count">' . $in_progress_services_count . '</span></span>';
			}

			if ( ! empty( $access_users_ids ) && in_array( $current_user_id, (array) $access_users_ids ) ) {

				$service_plural_name = Surelywp_Services::get_sv_plural_name();

				$surecart_menu_slug = 'sc-dashboard';
				if ( SureCart::account()->has_checklist && current_user_can( 'manage_options' ) ) {
					$surecart_menu_slug = 'sc-onboarding-checklist';
				}

				$entitlements = \SureCart::account()->entitlements;
				$page         = isset( $_GET['page'] ) && ! empty( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
				$priority     = in_array( $page, array( 'sc-products', 'sc-product-collections', 'sc-bumps', 'sc-upsells', 'sc-upsell-funnels', 'sc-product-groups' ) ) ? ( isset( $entitlements->product_groups ) && ! empty( $entitlements->product_groups ) ? 7 : 6 ) : 3;
				$priority     = in_array( $page, array( 'sc-orders', 'sc-abandoned-checkouts' ) ) ? 4 : $priority;
				// translators: %1$s: Service plural name, %2$s: Count tag HTML.
				add_submenu_page( $surecart_menu_slug, esc_html( $service_plural_name ), sprintf( esc_html__( '%1$s %2$s', 'surelywp-services' ), $service_plural_name, $count_tag ), 'edit_sc_orders', 'sc-services', array( $this, 'surelywp_sv_admin_services_dashboad' ), $priority );
			}
		}

		/**
		 * Admin Services Dashboad.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_admin_services_dashboad() {

			include SURELYWP_SERVICES_TEMPLATE_PATH . '/admin/services-list-table.php';
		}

		/**
		 * Run the installation
		 *
		 * @package Services For SureCart
		 * @since 1.0.0
		 */
		public function surelywp_sv_install() {

			if ( wp_doing_ajax() ) {
				return;
			}
			Surelywp_Services_Install()->surelwp_sv_init();
		}

		/**
		 * Function to Set register setting
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function set_register_setting() {

			register_setting( 'surelywp_sv_settings_options', 'surelywp_sv_settings_options', array( $this, 'surelywp_sv_sanitize_options' ) );
			register_setting( 'surelywp_sv_gen_settings_options', 'surelywp_sv_gen_settings_options', array( $this, 'surelywp_sv_sanitize_options' ) );
			register_setting( 'surelywp_sv_email_templates_options', 'surelywp_sv_email_templates_options', array( $this, 'surelywp_sv_sanitize_options' ) );
		}

		/**
		 * Function to Sanitize option data.
		 *
		 * @param array $input The option data for sanitize.
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_sanitize_options( $input ) {

			global $surelywp_sv_model;

			$action = isset( $_GET['action'] ) && ! empty( $_GET['action'] ) ? wp_unslash( sanitize_text_field( $_GET['action'] ) ) : '';

			// For individula services.
			if ( isset( $input['surelywp_sv_settings_options'] ) && ! empty( $input['surelywp_sv_settings_options'] ) ) {

				// require below for save multiple services options.
				if ( 'remove_service' !== $action ) {

					// Retrieve the existing settings.
					$existing_settings = get_option( 'surelywp_sv_settings_options' );

					if ( ! empty( $existing_settings ) ) {

						foreach ( $existing_settings['surelywp_sv_settings_options'] as $key => $value ) {

							if ( ! isset( $input['surelywp_sv_settings_options'][ $key ] ) ) {

								$input['surelywp_sv_settings_options'][ $key ] = $existing_settings['surelywp_sv_settings_options'][ $key ];
							}
						}
					}
				}

				// Data Sanitization - service Settings Options.
				foreach ( $input['surelywp_sv_settings_options'] as $service_setting_id => $service_option ) {

					foreach ( $service_option as $key => $value ) {

						if ( 'req_fields' === $key ) {

							foreach ( $value as $req_key => $field ) {
								if ( empty( $field['req_title'] ) ) { // remove empty fields.
									unset( $value[ $req_key ] );
								}
							}

							// if all filds blank.
							if ( empty( $value ) ) {

								// off the ask for the requirement.
								unset( $input['surelywp_sv_settings_options'][ $service_setting_id ]['ask_for_requirements'] );
							}
						}

						// delivery date validation.
						if ( 'delivery_time' === $key ) {
							$delivery_time = intval( $value );
							if ( $delivery_time < 0 || $delivery_time > 36500 ) { // value must me 0 < days < 100*365.
								$value = '1';
							}
						}

						if ( 'contract_details' === $key || 'req_fields' === $key ) {

							$input['surelywp_sv_settings_options'][ $service_setting_id ][ $key ] = $this->model->surelywp_escape_slashes_deep( $value, true, true );

						} else {

							$input['surelywp_sv_settings_options'][ $service_setting_id ][ $key ] = $this->model->surelywp_escape_slashes_deep( $value );
						}
					}
				}
			}

			// For General services.
			if ( isset( $input['surelywp_sv_gen_settings_options'] ) && ! empty( $input['surelywp_sv_gen_settings_options'] ) ) {

				foreach ( $input['surelywp_sv_gen_settings_options'] as $key => $value ) {

					// validation for service id staring number.
					// The starting number must be greater than the largest existing service number.
					if ( 'service_id_starting_number' === $key ) {

						$last_service_id = intval( $surelywp_sv_model->surelywp_sv_get_last_service_id() );
						$start_value     = intval( $value );
						if ( $start_value > $last_service_id ) {

							$result = $surelywp_sv_model->surelywp_sv_update_auto_increment_service_id( $start_value );

							if ( ! $result ) {
								$value = '';
							}
						} else {
							$value = '';
						}
					}

					// Default delivery date validation.
					if ( 'default_delivery_time' === $key ) {
						$default_delivery_time = intval( $value );
						if ( $default_delivery_time < 0 || $default_delivery_time > 36500 ) { // value must me 0 < days < 100*365.
							$value = '1';
						}
					}

					// Data Sanitization.
					$input['surelywp_sv_gen_settings_options'][ $key ] = $this->model->surelywp_escape_slashes_deep( $value );
				}
			}

			// For individual email templetes.
			if ( isset( $input['surelywp_sv_email_templates_options'] ) && ! empty( $input['surelywp_sv_email_templates_options'] ) ) {

				// Retrieve the existing settings.
				$existing_email_settings = get_option( 'surelywp_sv_email_templates_options' );

				if ( ! empty( $existing_email_settings ) ) {

					foreach ( $existing_email_settings['surelywp_sv_email_templates_options'] as $key => $value ) {

						if ( ! isset( $input['surelywp_sv_email_templates_options'][ $key ] ) ) {

							$input['surelywp_sv_email_templates_options'][ $key ] = $existing_email_settings['surelywp_sv_email_templates_options'][ $key ];
						}
					}
				}

				// Data Sanitization.
				foreach ( $input['surelywp_sv_email_templates_options'] as $templete_key => $templete_options ) {

					foreach ( $templete_options as $key => $value ) {

						if ( 'email_body' === $key ) {
							$input['surelywp_sv_email_templates_options'][ $templete_key ][ $key ] = $this->model->surelywp_escape_slashes_deep( $value, true, true );
						} else {
							$input['surelywp_sv_email_templates_options'][ $templete_key ][ $key ] = $this->model->surelywp_escape_slashes_deep( $value );
						}
					}
				}
			}

			return $input;
		}

		/**
		 * Function to enqueue style
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_customer_scripts() {

			$tab  = isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '';
			$page = isset( $_GET['page'] ) && ! empty( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

			// Register functions script.
			wp_register_script( 'surelywp-sv-functions', SURELYWP_SERVICES_ASSETS_URL . '/js/surelywp-sv-functions.js', array( 'jquery' ), SURELYWP_SERVICES_VERSION, true );
			wp_register_script( 'surelywp-sv-functions-min', SURELYWP_SERVICES_ASSETS_URL . '/js/surelywp-sv-functions.min.js', array( 'jquery' ), SURELYWP_SERVICES_VERSION, true );

			// Register open sans style.
			wp_register_style( 'open-sans-css', SURELYWP_SERVICES_ASSETS_URL . '/css/open-sans.css', array(), SURELYWP_SERVICES_VERSION, 'all' );

			// Register Google dancing script font.
			wp_register_style( 'dancing-script-font', SURELYWP_SERVICES_ASSETS_URL . '/css/dancing-script-font.css', array(), SURELYWP_SERVICES_VERSION, 'all' );

			// Register lightbox css and js.
			wp_register_style( 'lightbox-css', SURELYWP_SERVICES_ASSETS_URL . '/css/lightbox.min.css', array(), SURELYWP_SERVICES_VERSION, 'all' );
			wp_register_script( 'lightbox-js', SURELYWP_SERVICES_ASSETS_URL . '/js/lightbox.min.js', array( 'jquery' ), SURELYWP_SERVICES_VERSION, true );

			// Register filepond js ans csss.
			wp_register_style( 'filepond-css', SURELYWP_SERVICES_ASSETS_URL . '/css/filepond.min.css', array(), SURELYWP_SERVICES_VERSION, 'all' );
			wp_register_script( 'filepond-js', SURELYWP_SERVICES_ASSETS_URL . '/js/filepond.min.js', array( 'jquery' ), SURELYWP_SERVICES_VERSION, true );
			wp_register_script( 'filepond-plugins-js', SURELYWP_SERVICES_ASSETS_URL . '/js/filepond-plugins.min.js', array( 'jquery' ), SURELYWP_SERVICES_VERSION, true );

			wp_register_script( 'surelywp-sv-backend', SURELYWP_SERVICES_ASSETS_URL . '/js/surelywp-sv-backend.js', array( 'jquery', 'surelywp-sv-functions' ), SURELYWP_SERVICES_VERSION, true );
			wp_register_script( 'surelywp-sv-backend-min', SURELYWP_SERVICES_ASSETS_URL . '/js/surelywp-sv-backend.min.js', array( 'jquery', 'surelywp-sv-functions' ), SURELYWP_SERVICES_VERSION, true );

			wp_register_style( 'surelywp-sv-backend', SURELYWP_SERVICES_ASSETS_URL . '/css/surelywp-sv-backend.css', array(), SURELYWP_SERVICES_VERSION, 'all' );
			wp_register_style( 'surelywp-sv-backend-min', SURELYWP_SERVICES_ASSETS_URL . '/css/surelywp-sv-backend.min.css', array(), SURELYWP_SERVICES_VERSION, 'all' );

			// register common style and script.
			wp_register_style( 'surelywp-sv-common', SURELYWP_SERVICES_ASSETS_URL . '/css/surelywp-sv-common.css', array(), SURELYWP_SERVICES_VERSION, 'all' );
			wp_register_style( 'surelywp-sv-common-min', SURELYWP_SERVICES_ASSETS_URL . '/css/surelywp-sv-common.min.css', array(), SURELYWP_SERVICES_VERSION, 'all' );

			wp_register_script( 'surelywp-sv-common', SURELYWP_SERVICES_ASSETS_URL . '/js/surelywp-sv-common.js', array( 'jquery' ), SURELYWP_SERVICES_VERSION, true );
			wp_register_script( 'surelywp-sv-common-min', SURELYWP_SERVICES_ASSETS_URL . '/js/surelywp-sv-common.min.js', array( 'jquery' ), SURELYWP_SERVICES_VERSION, true );

			// alllow file extensions.
			$file_types = Surelywp_Services::surelywp_sv_get_allow_file_types();

			// file upload max size.
			$file_size = Surelywp_Services::get_sv_gen_option( 'file_size' );

			if ( empty( $file_size ) ) {
				$file_size = '5';
			}

			$localize = array(
				'ajax_url'      => admin_url( 'admin-ajax.php' ),
				'nonce'         => wp_create_nonce( 'admin-ajax-nonce' ),
				'file_types'    => $file_types,
				'file_types_ie' => array( 'application/json' ),
				'file_size'     => SURELYWP_SERVICES_IE_FILE_SIZE,
				'image_path'    => SURELYWP_SERVICES_ASSETS_URL . '/images/',
			);

			$allow_pages = array( 'sc-services', 'sc-orders', 'sc-products', 'sc-customers', 'surelywp_services_panel' );

			// Enqueue Script at admin Reports setting page.
			if ( ! empty( $page ) && ( in_array( $page, $allow_pages, true ) ) ) {

				// Enqueue jQuery UI For Sortable fields.
				if ( 'surelywp_services_panel' === $page && 'surelywp_sv_settings' === $tab ) {
					wp_enqueue_script( 'jquery-ui-core' );
					wp_enqueue_script( 'jquery-ui-sortable' );
				}

				wp_enqueue_editor();

				// For language transate in javascript files.
				wp_enqueue_script( 'wp-i18n' );

				// Surecart Componets.
				wp_enqueue_script( 'surecart-components' );
				wp_enqueue_style( 'surecart-themes-default' );
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
				// backend script and style.
				wp_enqueue_script( 'surelywp-sv-backend' . $min_file );
				wp_enqueue_style( 'surelywp-sv-backend' . $min_file );

				// comman script and style.
				wp_enqueue_script( 'surelywp-sv-common' . $min_file );
				wp_enqueue_style( 'surelywp-sv-common' . $min_file );

				// localize script.
				wp_localize_script( 'surelywp-sv-backend' . $min_file, 'sv_backend_ajax_object', $localize );
				wp_localize_script( 'surelywp-sv-common' . $min_file, 'sv_common_ajax_object', $localize );

				// For Handle language Translation.
				wp_set_script_translations( 'surelywp-sv-backend' . $min_file, 'surelywp-services' );
				wp_set_script_translations( 'surelywp-sv-common' . $min_file, 'surelywp-services' );
			}
		}

		/* === INITIALIZATION SECTION === */

		/**
		 * Initiator method. Initiate properties.
		 *
		 * @package Services For SureCart
		 * @return  void
		 * @access  private
		 * @since   1.0.0
		 */
		public function surelywp_sv_init() {

			/**
			 * APPLY_FILTERS: surelywp_sv_available_admin_tabs
			 *
			 * Filter the available tabs in the plugin panel.
			 *
			 * @package Services For SureCart
			 * @param   array $tabs Admin tabs
			 * @return  array
			 * @since   1.0.0
			 */
			$this->available_tabs = apply_filters(
				'surelywp_sv_available_admin_tabs',
				array(
					'overview'                    =>
					array(
						'title' => esc_html__( 'Overview', 'surelywp-services' ),
						'icon'  => "<div class='image documentation'><img src='" . esc_url( SURELYWP_SERVICES_ASSETS_URL . '/images/Home.svg' ) . "'></div>",
					),
					'surelywp_sv_gen_settings'    =>
					array(
						'title' => esc_html__( 'General Settings', 'surelywp-services' ),
						'icon'  => "<div class='image key'><img src='" . esc_url( SURELYWP_SERVICES_ASSETS_URL . '/images/new-Setting.svg' ) . "'></div>",
					),
					'surelywp_sv_settings'        =>
					array(
						'title' => esc_html__( 'Services', 'surelywp-services' ),
						'icon'  => "<div class='image key'><img src='" . esc_url( SURELYWP_SERVICES_ASSETS_URL . '/images/Services-settings.svg' ) . "'></div>",
					),
					'surelywp_sv_email_templates' =>
					array(
						'title' => __( 'Email Templates', 'surelywp-services' ),
						'icon'  => "<div class='image key'><img src='" . esc_url( SURELYWP_SERVICES_ASSETS_URL . '/images/Email.svg' ) . "'></div>",
					),
					'changelog'                   =>
					array(
						'title' => esc_html__( 'Changelog', 'surelywp-services' ),
						'icon'  => "<div class='image changelog'><img src='" . esc_url( SURELYWP_SERVICES_ASSETS_URL . '/images/changelog.svg' ) . "'></div>",
					),
					'license_key'                 =>
					array(
						'title' => esc_html__( 'License Key ', 'surelywp-services' ),
						'icon'  => "<div class='image key'><img src='" . esc_url( SURELYWP_SERVICES_ASSETS_URL . '/images/license-Key.svg' ) . "'></div>",
					),

					'surelywp_addons_settings'    =>
					array(
						'title' => esc_html__( 'SurelyWP Addons ', 'surelywp-services' ),
						'icon'  => "<div class='image addons'><img src='" . esc_url( SURELYWP_SERVICES_ASSETS_URL . '/images/Add.svg' ) . "'></div>",
					),
				)
			);
		}

		/**
		 * Get Current panel name
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public static function get_panel_name() {
			return 'surelywp_services_panel';
		}

		/**
		 * Register Service panel
		 *
		 * @package Services For SureCart
		 * @return  void
		 * @since   1.0.0
		 */
		public function surelywp_sv_register_panel() {

			$args = array(
				'create_menu_page'   => true,
				'parent_slug'        => '',
				'page_title'         => esc_html__( 'Services For SureCart', 'surelywp-services' ),
				'menu_title'         => esc_html__( 'Services', 'surelywp-services' ),
				'plugin_slug'        => 'surelywp-services',
				'plugin_description' => esc_html__( 'This plugin empowers you to sell services and custom deliverables with SureCart. Enjoy features like status and activity tracking, built-in messaging, and final delivery and approvals, all beautifully integrated directly into your website and customer dashboard.', 'surelywp-services' ),

				/**
				 * APPLY_FILTERS: surelywp_sv_settings_panel_capability
				 *
				 * Filter the capability used to access the plugin panel.
				 *
				 * @param string $capability Capability
				 *
				 * @return string
				 */
				'capability'         => 'manage_options',
				'parent'             => '',
				'parent_page'        => 'surelywp_plugin_panel',
				'page'               => 'surelywp_services_panel',
				'admin-tabs'         => $this->available_tabs,
				'options-path'       => SURELYWP_SERVICES_DIR . 'addons-options',
				'help_tab'           => array(),
			);

			// registers premium tab.
			if ( ! defined( 'SURELYWP_SERVICES_PREMIUM' ) ) {
				$args['premium_tab'] = array(
					'landing_page_url' => '',
					'premium_features' => array(),
					'main_image_url'   => '',
				);
			}
			$this->panel = new SurelyWP_Plugin_Panel_SureCart( $args );
		}
	}
}

/**
 * Unique access to instance of Surelywp_Services_Admin class
 *
 * @package Services For SureCart
 * @return  \Surelywp_Services_Admin
 * @since   1.0.0
 */
function Surelywp_Services_Admin() { // phpcs:ignore
	$instance = Surelywp_Services_Admin::get_instance();
	return $instance;
}
