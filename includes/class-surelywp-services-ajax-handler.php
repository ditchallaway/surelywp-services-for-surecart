<?php
/**
 * Class that will handle all ajax calls
 *
 * @author  Surelywp
 * @package Services For SureCart
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'SURELYWP_SERVICES' ) ) {
	exit;
}

if ( ! class_exists( 'Surelywp_Services_Ajax_Handler' ) ) {

	/**
	 * AJAX Handler Class
	 *
	 * @package Services For SureCart
	 * @since   1.0.0
	 */
	class Surelywp_Services_Ajax_Handler {

		/**
		 * Performs all required add_actions to handle ajax events
		 *
		 * @package Services For SureCart
		 * @return  void
		 * @since   1.0.0
		 */
		public function __construct() {

			// get sevices list view with pagination.
			add_action( 'wp_ajax_surelywp_sv_get_user_service_paginate', array( $this, 'surelywp_sv_get_user_service_paginate' ) );

			// Get services list admin block with pagination.
			add_action( 'wp_ajax_surelywp_sv_get_user_service_block_paginate', array( $this, 'surelywp_sv_get_user_service_block_paginate' ) );

			// handle Contract submit form.
			add_action( 'wp_ajax_surelywp_sv_contract_form_sumbit_callback', array( $this, 'surelywp_sv_contract_form_sumbit_callback' ) );

			// handle requirement submit form.
			add_action( 'wp_ajax_surelywp_sv_req_form_sumbit_callback', array( $this, 'surelywp_sv_req_form_sumbit_callback' ) );

			// handle message submit form.
			add_action( 'wp_ajax_surelywp_sv_message_form_sumbit_callback', array( $this, 'surelywp_sv_message_form_sumbit_callback' ) );

			// handle fetch sevices messages.
			add_action( 'wp_ajax_surelywp_sv_fetch_service_messages', array( $this, 'surelywp_sv_fetch_service_messages' ) );

			// Handle load more service message.
			add_action( 'wp_ajax_surelywp_sv_load_more_service_messages', array( $this, 'surelywp_sv_load_more_service_messages' ) );

			// check delivery status.
			add_action( 'wp_ajax_surelywp_sv_check_delivery_status', array( $this, 'surelywp_sv_check_delivery_status' ) );

			// change the delivery date.
			add_action( 'wp_ajax_surelywp_sv_change_delivery_date', array( $this, 'surelywp_sv_change_delivery_date' ) );

			// check delivery status.
			add_action( 'wp_ajax_surelywp_sv_handle_delivery', array( $this, 'surelywp_sv_handle_delivery' ) );

			// Manage product service.
			add_action( 'wp_ajax_surelywp_sv_manage_product_service', array( $this, 'surelywp_sv_manage_product_service' ) );

			// Send service Message Mail.
			add_action( 'wp_ajax_surelywp_sv_send_message_mail', array( $this, 'surelywp_sv_send_message_mail' ) );

			// Product page Requrement form.
			add_action( 'wp_ajax_surelywp_sv_external_req_form_sumbit_callback', array( $this, 'surelywp_sv_external_req_form_sumbit_callback' ) );
			add_action( 'wp_ajax_nopriv_surelywp_sv_external_req_form_sumbit_callback', array( $this, 'surelywp_sv_external_req_form_sumbit_callback' ) );

			// New Service Request.
			add_action( 'wp_ajax_surelywp_sv_new_service_req', array( $this, 'surelywp_sv_new_service_req' ) );
		}


		/**
		 * Function to manage new service request.
		 *
		 * @package Services For SureCart
		 * @since   1.4
		 */
		public function surelywp_sv_new_service_req() {

			if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'surelywp_sv_service_request_form_action' ) ) {

				global $surelywp_sv_model;

				$service_obj       = Surelywp_Services();
				$service_order     = isset( $_POST['service_order'] ) && ! empty( $_POST['service_order'] ) ? sanitize_text_field( wp_unslash( $_POST['service_order'] ) ) : '';
				$dashboard_page_id = isset( $_POST['dashboard_page_id'] ) && ! empty( $_POST['dashboard_page_id'] ) ? sanitize_text_field( wp_unslash( $_POST['dashboard_page_id'] ) ) : '';

				$options_values_parts = explode( '-surelywp-sv-separate-', $service_order );
				if ( $options_values_parts ) {

					$order_id   = $options_values_parts[0] ?? '';
					$product_id = $options_values_parts[1] ?? '';

					$rc_service = $surelywp_sv_model->surelywp_sv_get_rc_service_by_order_product( $order_id, $product_id );

					$service_singular_name = Surelywp_Services::get_sv_singular_name();
					$service_plural_name   = Surelywp_Services::get_sv_plural_name();

					if ( $rc_service ) { // For Subsctiption product.

						$rc_service_id  = $rc_service['id'] ?? '';
						$current_time   = current_time( 'mysql' );
						$next_update_on = $rc_service['next_update_on'] ?? '';

						if ( $current_time >= $next_update_on ) {
							$rc_service = $service_obj->surelywp_sv_update_recurring_service_quota( $rc_service );
						}

						$quota = $rc_service['quota'] ?? '';
						if ( $quota > 0 ) {

							$rc_sv_setting             = $surelywp_sv_model->surelywp_sv_get_rc_settings_by_rc_id( $rc_service_id );
							$enable_recurring_services = $rc_sv_setting['enable_recurring_services'] ?? '';

							if ( $enable_recurring_services ) {

								$service_setting_id = $rc_service['service_setting_id'];
								$user_id            = $rc_service['user_id'];

								$services_enable = Surelywp_Services::get_sv_option( $service_setting_id, 'status' );
								if ( $services_enable ) {

									$ask_for_requirements = Surelywp_Services::get_sv_option( $service_setting_id, 'ask_for_requirements' );
									$ask_for_contract     = Surelywp_Services::get_sv_option( $service_setting_id, 'ask_for_contract' );

									$delivery_date = null;
									if ( ! empty( $ask_for_contract ) && ! empty( $ask_for_requirements ) ) {
										$service_status = 'waiting_for_contract';
									} elseif ( empty( $ask_for_contract ) && ! empty( $ask_for_requirements ) ) {
										$service_status = 'waiting_for_req';
									} elseif ( ! empty( $ask_for_contract ) && empty( $ask_for_requirements ) ) {
										$service_status = 'waiting_for_contract';
									} elseif ( empty( $ask_for_contract ) && empty( $ask_for_requirements ) ) {
										$service_status      = 'service_start_0';
										$revisions_remaining = Surelywp_Services::surelywp_sv_get_revisions_allowed( $service_setting_id, 0 );
										$delivery_date       = Surelywp_Services::surelywp_sv_calculate_delivery_date( $service_setting_id );
									}

									$service_data = array(
										'service_setting_id' => $service_setting_id,
										'order_id'       => $order_id,
										'product_id'     => $product_id,
										'service_status' => $service_status,
										'delivery_date'  => $delivery_date,
										'user_id'        => $user_id,
										'recurring_service_id' => $rc_service_id,
										'revisions_remaining' => $revisions_remaining,
									);

									$service_id = $service_obj->surelywp_sv_create_rc_service( $service_data );

									if ( $service_id ) {

										$surelywp_sv_model->surelywp_sv_update_rc_quota( $rc_service_id, $quota - 1 );

										$services_url = add_query_arg(
											array(
												'action' => 'index',
												'model'  => 'services',
												'service_id' => $service_id,
											),
											get_permalink( $dashboard_page_id )
										);

										/**
										 * Fire for every new service creation.
										 */
										do_action( 'surelywp_services_create', $service_data );

										echo wp_json_encode(
											array(
												'status'  => true,
												// translators: %s is the singular name of the service.
												'message' => sprintf( esc_html__( 'Your new %s has been successfully created.', 'surelywp-services' ), esc_html( $service_singular_name ) ),
												'redirect_url' => $services_url,
											)
										);
										wp_die();
									}
								} else {

									echo wp_json_encode(
										array(
											'status'  => false,
											// translators: %s is the singular name of the service.
											'message' => sprintf( esc_html__( '%s is disabled by the service provider.', 'surelywp-services' ), esc_html( $service_singular_name ) ),
										)
									);
									wp_die();
								}
							}
						} else {
							echo wp_json_encode(
								array(
									'status'  => false,
									// translators: %s is the plural name of the services.
									'message' => sprintf( esc_html__( 'You have reached the maximum number of %s allowed for this cycle.', 'surelywp-services' ), esc_html( $service_plural_name ) ),
								)
							);
							wp_die();
						}
					} else { // For one time price and installment price product.

						$service_setting_id = $surelywp_sv_model->surelywp_sv_get_sv_setting_id( $order_id, $product_id )[0] ?? '';
						$services_enable    = Surelywp_Services::get_sv_option( $service_setting_id, 'status' );
						if ( $services_enable ) {

							$remaining_services = $surelywp_sv_model->surelywp_sv_get_remaining_service_count( $order_id, $product_id )[0] ?? '';
							if ( $remaining_services ) {

								$ask_for_requirements = Surelywp_Services::get_sv_option( $service_setting_id, 'ask_for_requirements' );
								$ask_for_contract     = Surelywp_Services::get_sv_option( $service_setting_id, 'ask_for_contract' );

								$delivery_date = null;
								if ( ! empty( $ask_for_contract ) && ! empty( $ask_for_requirements ) ) {
									$service_status = 'waiting_for_contract';
								} elseif ( empty( $ask_for_contract ) && ! empty( $ask_for_requirements ) ) {
									$service_status = 'waiting_for_req';
								} elseif ( ! empty( $ask_for_contract ) && empty( $ask_for_requirements ) ) {
									$service_status = 'waiting_for_contract';
								} elseif ( empty( $ask_for_contract ) && empty( $ask_for_requirements ) ) {
									$service_status = 'service_start';
									$delivery_date  = Surelywp_Services::surelywp_sv_calculate_delivery_date( $service_setting_id );
								}

								$service_data = array(
									'service_setting_id' => $service_setting_id,
									'order_id'           => $order_id,
									'product_id'         => $product_id,
									'services_remaining' => $remaining_services,
									'service_status'     => $service_status,
									'delivery_date'      => $delivery_date,
									'user_id'            => get_current_user_id(),
								);

								$service_id = $service_obj->surelywp_sv_create_service( $service_data );

								if ( $service_id ) {

									if ( $remaining_services > 0 ) {
										$remaining_services = intval( $remaining_services ) - 1;
									}

									// Update the remaining service.
									$surelywp_sv_model->surelywp_sv_update_service_data(
										array(
											'order_id'   => $order_id,
											'product_id' => $product_id,
										),
										array( 'services_remaining' => $remaining_services )
									);

									$services_url = add_query_arg(
										array(
											'action'     => 'index',
											'model'      => 'services',
											'service_id' => $service_id,
										),
										get_permalink( $dashboard_page_id )
									);

									/**
									 * Fire for every new service creation.
									 */
									do_action( 'surelywp_services_create', $service_data );

									echo wp_json_encode(
										array(
											'status'       => true,
											// translators: %s is the singular name of the service.
											'message'      => sprintf( esc_html__( 'Your new %s has been successfully created.', 'surelywp-services' ), esc_html( $service_singular_name ) ),
											'redirect_url' => $services_url,
										)
									);
									wp_die();
								}
							}
						}
					}
				}

				echo wp_json_encode(
					array(
						'status'  => false,
						'message' => esc_html__( 'Something went wrong please try again.', 'surelywp-services' ),
					)
				);
				wp_die();
			}
		}

		/**
		 * Function to manage product service.
		 *
		 * @package Services For SureCart
		 * @since   1.4
		 */
		public function surelywp_sv_external_req_form_sumbit_callback() {

			if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'surelywp_sv_req_form_action' ) ) {

				global $surelywp_model;

				$service_obj                  = Surelywp_Services();
				$service_setting_id           = isset( $_POST['service_setting_id'] ) && ! empty( $_POST['service_setting_id'] ) ? sanitize_text_field( wp_unslash( $_POST['service_setting_id'] ) ) : '';
				$product_id                   = isset( $_POST['product_id'] ) && ! empty( $_POST['product_id'] ) ? sanitize_text_field( wp_unslash( $_POST['product_id'] ) ) : '';
				$service_requirement          = isset( $_POST['service_requirement'] ) && ! empty( $_POST['service_requirement'] ) ? $surelywp_model->surelywp_escape_slashes_deep( $_POST['service_requirement'], true, true ) : '';
				$service_requirement_text     = isset( $_POST['service_requirement_text'] ) && ! empty( $_POST['service_requirement_text'] ) ? $surelywp_model->surelywp_escape_slashes_deep( $_POST['service_requirement_text'], true, true ) : '';
				$service_requirement_dropdown = isset( $_POST['service_requirement_dropdown'] ) && ! empty( $_POST['service_requirement_dropdown'] ) ? $surelywp_model->surelywp_escape_slashes_deep( $_POST['service_requirement_dropdown'], true, true ) : '';
				$requirement_files            = isset( $_FILES['requirement_files'] ) && ! empty( $_FILES['requirement_files'] ) ? $_FILES['requirement_files'] : '';
				$service_requirement_data     = isset( $_POST['service_requirement_data'] ) && ! empty( $_POST['service_requirement_data'] ) ? $surelywp_model->surelywp_escape_slashes_deep( $_POST['service_requirement_data'], true, true ) : '';
				$requirements_data            = array();
				$requirement_files_arranged   = array();
				$requirement_file_path        = array();

				// Modify $_FILES array.
				if ( ! empty( $requirement_files ) ) {
					foreach ( $requirement_files as $file_key => $requirement_file ) {
						foreach ( $requirement_file as $key => $values ) {
							foreach ( $values as $numer => $value ) {
								$requirement_files_arranged[ $key ][ $numer ][ $file_key ] = $value;
							}
						}
					}
				}

				// store file and get file path.
				if ( ! empty( $requirement_files_arranged ) ) {

					foreach ( $requirement_files_arranged as $file_key => $files ) {
						foreach ( $files as $key => $file ) {

							$requirement_file_path[ $file_key ][] = $service_obj->surelywp_sv_store_req_temp_file( $file );
						}
					}
				}

				if ( ! empty( $product_id ) && ! empty( $service_requirement_data ) ) {

					foreach ( $service_requirement_data as $key => $requirement_data ) {

						$unserialize_requirement_data = unserialize( $service_requirement_data[ $key ] );
						$req_field_type               = $unserialize_requirement_data['req_field_type'] ?? '';
						$is_required_field            = isset( $unserialize_requirement_data['is_required_field'] ) && '1' === $unserialize_requirement_data['is_required_field'] ? true : false;

						// Validations.
						if ( 'file' === $req_field_type ) {

							if ( empty( $requirement_file_path ) ) {

								if ( $is_required_field ) {
									echo wp_json_encode(
										array(
											'success' => false,
											'message' => esc_html__( 'Please Fill Required Fields.', 'surelywp-services' ),
										)
									);
									wp_die();

								} else {
									continue; // Skip loop is user not have uplode attachment.
								}
							}
						} elseif ( 'textarea' === $req_field_type ) {

							if ( isset( $service_requirement[ $key ] ) && empty( $service_requirement[ $key ] ) ) {

								if ( $is_required_field ) {
									echo wp_json_encode(
										array(
											'success' => false,
											'message' => esc_html__( 'Please Fill Required Fields.', 'surelywp-services' ),
										)
									);
									wp_die();
								} else {
									continue; // Skip loop for that textarea.
								}
							}
						} elseif ( 'text' === $req_field_type ) {

							if ( isset( $service_requirement_text[ $key ] ) && empty( $service_requirement_text[ $key ] ) ) {

								if ( $is_required_field ) {
									echo wp_json_encode(
										array(
											'success' => false,
											'message' => esc_html__( 'Please Fill Required Fields.', 'surelywp-services' ),
										)
									);
									wp_die();
								} else {
									continue; // Skip loop for that textarea.
								}
							}
						} elseif ( 'dropdown' === $req_field_type ) {

							if ( isset( $service_requirement_dropdown[ $key ] ) && empty( $service_requirement_dropdown[ $key ] ) ) {

								if ( $is_required_field ) {
									echo wp_json_encode(
										array(
											'success' => false,
											'message' => esc_html__( 'Please Fill Required Fields.', 'surelywp-services' ),
										)
									);
									wp_die();
								} else {
									continue; // Skip loop for that dropdown.
								}
							}
						}

						$requirements_data[] = array(
							'requirement_type'  => $req_field_type,
							'requirement_title' => $unserialize_requirement_data['req_title'] ?? '',
							'requirement_desc'  => $unserialize_requirement_data['req_desc'] ?? '',
							'requirement'       => 'textarea' === $req_field_type ? ( $service_requirement[ $key ] ?? '' ) : ( 'text' === $req_field_type ? ( $service_requirement_text[ $key ] ?? '' ) : ( 'dropdown' === $req_field_type ? ( $service_requirement_dropdown[ $key ] ?? '' ) : $requirement_file_path[ $key ] ) ),
						);
					}

					if ( ! empty( $requirements_data ) ) {

						Surelywp_Services::surelywp_save_product_requirements( $product_id, $requirements_data );
					}

					echo wp_json_encode(
						array(
							'success' => true,
							'message' => esc_html__( 'Requirements Submitted Successfully', 'surelywp-services' ),
						)
					);
					wp_die();
				} else {

					echo wp_json_encode(
						array(
							'success' => false,
							'message' => esc_html__( 'Something went wrong please try again.', 'surelywp-services' ),
						)
					);
					wp_die();
				}
			} else {

				echo wp_json_encode(
					array(
						'success' => false,
						'status'  => 'fail',
						'error'   => esc_html__( 'Ajax Nonce Not Verify', 'surelywp-services' ),
						'message' => esc_html__( 'Something went wrong please try again.', 'surelywp-services' ),
					)
				);
				wp_die();
			}
		}

		/**
		 * Function to manage product service.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_manage_product_service() {

			if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'surelywp_sv_manage_product_service_action' ) ) {

				global $surelywp_sv_model;
				$service_obj = Surelywp_Services();

				$product_id                 = isset( $_POST['product_id'] ) && ! empty( $_POST['product_id'] ) ? sanitize_text_field( wp_unslash( $_POST['product_id'] ) ) : '';
				$service_setting_id         = isset( $_POST['service_setting_id'] ) && ! empty( $_POST['service_setting_id'] ) ? sanitize_text_field( wp_unslash( $_POST['service_setting_id'] ) ) : '';
				$current_service_setting_id = isset( $_POST['current_service_setting_id'] ) && ! empty( $_POST['current_service_setting_id'] ) ? sanitize_text_field( wp_unslash( $_POST['current_service_setting_id'] ) ) : '';
				$is_service_enable          = isset( $_POST['is_service_enable'] ) && ! empty( $_POST['is_service_enable'] ) ? sanitize_text_field( wp_unslash( $_POST['is_service_enable'] ) ) : '';

				if ( ! empty( $product_id ) ) {

					$metadata = array(
						'is_service_enable'  => $is_service_enable,
						'service_setting_id' => $service_setting_id,
					);

					// Update the product meta.
					$service_obj->surelywp_sv_update_product( $product_id, $metadata );

					// update setting options.
					if ( ! empty( $current_service_setting_id ) ) {
						$service_obj->surelywp_sv_update_setting_option( $product_id, $current_service_setting_id, 'remove' );
						$service_obj->surelywp_sv_update_setting_option( $product_id, $service_setting_id, 'add' );
					} else {
						$service_obj->surelywp_sv_update_setting_option( $product_id, $service_setting_id, 'add' );
					}

					echo wp_json_encode(
						array(
							'success'        => true,
							'is_meta_update' => true,
						)
					);
					wp_die();
				}
			} else {

				echo wp_json_encode(
					array(
						'success' => false,
						'status'  => 'fail',
						'error'   => esc_html__( 'Ajax Nonce Not Verify', 'surelywp-services' ),
					)
				);
				wp_die();
			}

			echo wp_json_encode(
				array(
					'success' => false,
					'message' => esc_html__( 'Something went wrong please try again.', 'surelywp-services' ),
				)
			);
			wp_die();
		}

		/**
		 * Approve or Reject Final Service Delivery.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_handle_delivery() {

			if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'surelywp_sv_handle_delivery_action' ) ) {

				global $surelywp_sv_model;
				$service_obj        = Surelywp_Services();
				$service_id         = isset( $_POST['service_id'] ) && ! empty( $_POST['service_id'] ) ? sanitize_text_field( wp_unslash( $_POST['service_id'] ) ) : '';
				$service_setting_id = isset( $_POST['service_setting_id'] ) && ! empty( $_POST['service_setting_id'] ) ? sanitize_text_field( wp_unslash( $_POST['service_setting_id'] ) ) : '';
				$message_id         = isset( $_POST['approve_message_id'] ) && ! empty( $_POST['approve_message_id'] ) ? sanitize_text_field( wp_unslash( $_POST['approve_message_id'] ) ) : '';
				$is_approved        = isset( $_POST['is_approved'] ) && ! empty( $_POST['is_approved'] ) ? sanitize_text_field( wp_unslash( $_POST['is_approved'] ) ) : '';
				$milestone_id       = isset( $_POST['milestone_id'] ) ? sanitize_text_field( wp_unslash( $_POST['milestone_id'] ) ) : 'no';

				if ( ! empty( $service_id ) && ! empty( $message_id ) ) {

					$is_update_delivery = $surelywp_sv_model->surelywp_sv_approve_delivery( $message_id, $is_approved );

					// Clear service auto complete hook.
					$auto_complete_sv_hook      = SURELYWP_SERVICES_AUTO_COMPLETE_CRON;
					$auto_complete_sv_hook_args = array( intval( $service_id ) );
					Surelywp_Services::surelywp_sv_unset_cron( $auto_complete_sv_hook, $auto_complete_sv_hook_args );

					$status = '';
					if ( $is_approved ) {
						/**
						 * Fire on customer approve Final Delivery.
						 */
						do_action( 'surelywp_services_customer_approve_delivery', $service_id, $message_id );

						$status = 'service_complete';
						if ( 'no' !== $milestone_id ) {
							if ( is_numeric( $milestone_id ) ) {
								// simple number case.
								$status              = 'service_start_' . ( $milestone_id + 1 ); // next milestone.
								$milestone_id        = (int) $milestone_id;
								$revisions_remaining = Surelywp_Services::surelywp_sv_get_revisions_allowed( $service_setting_id, $milestone_id + 1 );
								// Update Delivery Date for new Milestone.
								$milestone_delivery_date = Surelywp_Services::surelywp_sv_calculate_delivery_date( $service_setting_id, $milestone_id + 1 );
							} elseif ( preg_match( '/^milestone_complete_\d+$/', $milestone_id ) ) { // Last milestone_complete_X case.
								// milestone_complete_X.
								preg_match( '/_(\d+)$/', $milestone_id, $matches );
								// $status       = 'final_delivery_start';
								$status       = 'service_complete';
								$milestone_id = isset( $matches[1] ) ? (int) $matches[1] : null;
							}
						}

						/**
						 * Fire on service complete.
						 */
						do_action( 'surelywp_services_complete', $service_id );

						if ( 'no' !== $milestone_id ) {
							$service_data['delivery_date'] = $milestone_delivery_date;
						} else {
							$service_data['delivery_date'] = current_time( 'mysql' );
						}
						$service_data['revisions_remaining'] = isset( $revisions_remaining ) ? $revisions_remaining : 0;
					} else {

						$revision_message = $surelywp_sv_model->surelywp_sv_get_customer_revision_msg( $service_id, $message_id );

						/**
						 * Fire on customer request revision.
						 */
						do_action( 'surelywp_services_customer_request_revision', $service_id, $revision_message );

						// $status = 'final_delivery_start';
						if ( isset( $milestone_id ) ) {
							if ( is_numeric( $milestone_id ) ) {
								$status           = 'service_start_' . $milestone_id;
								$milestone_data   = Surelywp_Services::get_sv_option( $service_setting_id, 'milestones_fields' );
								$revision_allowed = Surelywp_Services::surelywp_sv_number_of_revisions_remaining( $milestone_id, $service_setting_id, $service_id, $milestone_data[ $milestone_id ] );
								if ( null !== $revision_allowed['remaining_revision'] ) {
									$service_data['revisions_remaining'] = max( 0, $revision_allowed['remaining_revision'] - 1 );
								}
							} elseif ( preg_match( '/^milestone_complete_\d+$/', $milestone_id ) ) { // Last milestone_complete_X case.
								preg_match( '/_(\d+)$/', $milestone_id, $matches );
								$status           = 'service_start_' . $matches[1];
								$milestone_data   = Surelywp_Services::get_sv_option( $service_setting_id, 'milestones_fields' );
								$revision_allowed = Surelywp_Services::surelywp_sv_number_of_revisions_remaining( $milestone_id, $service_setting_id, $service_id, $milestone_data[ $milestone_id ] );
								if ( null !== $revision_allowed['remaining_revision'] ) {
									$service_data['revisions_remaining'] = max( 0, $revision_allowed['remaining_revision'] - 1 );
								}
							}
						}
					}

					$milestone_progress   = array();
					$milestone_progress[] = array(
						'milestone_id' => $milestone_id,
						'status'       => 'completed', // you can also make this dynamic.
					);

					$service_data['service_status'] = $status;

					// Update service status and delivery date delivery is approved.
					$is_update_service_status = $surelywp_sv_model->surelywp_sv_update_service( $service_id, $service_data );

					if ( 'no' !== $milestone_id ) {
						$delivery_accept = 'delivery_accept_milestone_' . $milestone_id;
					} else {
						// Add Activity for delivery.
						$delivery_accept = 'delivery_accept';
					}

					$activity_data = array(
						'service_id'    => $service_id,
						'activity_type' => $is_approved ? $delivery_accept : 'delivery_reject',
						'activity_info' => $message_id,
					);

					$surelywp_sv_model->surelywp_sv_insert_activity( $activity_data );

					// Add Activity for service complete.
					if ( $is_approved ) {

						// Send Email to admins for delivery approved.
						$service_obj->surelywp_sv_send_service_email( $service_id, 'delivery_approve_email' );

						if ( 'service_complete' == $status ) {
							$activity_data['activity_type'] = 'service_complete';
							// Add Activity for complete service.
							$surelywp_sv_model->surelywp_sv_insert_activity( $activity_data );
						}
					} else {

						// Send Email to admins for delivery Rejected.
						$service_obj->surelywp_sv_send_service_email( $service_id, 'delivery_reject_email' );
					}

					echo wp_json_encode(
						array(
							'success' => true,
						)
					);
					wp_die();
				}
			} else {

				echo wp_json_encode(
					array(
						'success' => false,
						'status'  => 'fail',
						'error'   => esc_html__( 'Ajax Nonce Not Verify', 'surelywp-services' ),
					)
				);
				wp_die();
			}

			echo wp_json_encode(
				array(
					'success' => false,
					'message' => esc_html__( 'Something went wrong please try again.', 'surelywp-services' ),
				)
			);
			wp_die();
		}

		/**
		 * Function to handle change the delivery date.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_change_delivery_date() {

			if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'surelywp_sv_delivery_change_action' ) ) {

				global $surelywp_sv_model;
				$service_obj   = Surelywp_Services();
				$service_id    = isset( $_POST['service_id'] ) && ! empty( $_POST['service_id'] ) ? sanitize_text_field( wp_unslash( $_POST['service_id'] ) ) : '';
				$delivery_date = isset( $_POST['delivery_date'] ) && ! empty( $_POST['delivery_date'] ) ? sanitize_text_field( wp_unslash( $_POST['delivery_date'] ) ) : '';

				if ( ! empty( $delivery_date ) && ! empty( $service_id ) ) {

					// Validate date.
					$current_date = date( 'Y-m-d' );
					if ( $delivery_date < $current_date || $delivery_date > '2124-01-01' ) {

						echo wp_json_encode(
							array(
								'success' => false,
								'status'  => 'fail',
								'error'   => esc_html__( 'Invalid Date', 'surelywp-services' ),
							)
						);
						wp_die();
					}

					// Check delivery date change.
					$is_change = $surelywp_sv_model->surelywp_sv_change_delivery_date( $service_id, $delivery_date );

					if ( $is_change ) {

						/**
						 * Fire on delivery date change.
						 */
						do_action( 'surelywp_services_delivery_date_change', $service_id, $delivery_date );

						// Send Email to customer for delivery date change.
						$service_obj->surelywp_sv_send_service_email( $service_id, 'delivery_date_change_email' );

						// Add Activity.
						$activity_data = array(
							'service_id'    => $service_id,
							'activity_type' => 'delivery_date_change',
						);
						$surelywp_sv_model->surelywp_sv_insert_activity( $activity_data );

						echo wp_json_encode(
							array(
								'success'          => true,
								'is_status_update' => true,
							)
						);
						wp_die();
					}
				}
			} else {

				echo wp_json_encode(
					array(
						'success' => false,
						'status'  => 'fail',
						'error'   => esc_html__( 'Ajax Nonce Not Verify', 'surelywp-services' ),
					)
				);
				wp_die();
			}

			echo wp_json_encode(
				array(
					'success' => false,
					'message' => esc_html__( 'Something went wrong please try again.', 'surelywp-services' ),
				)
			);
			wp_die();
		}

		/**
		 * Function to handle message form submit action.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_check_delivery_status() {

			if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'surelywp_sv_message_form_action' ) ) {

				global $surelywp_sv_model;
				$service_id = isset( $_POST['service_id'] ) && ! empty( $_POST['service_id'] ) ? sanitize_text_field( wp_unslash( $_POST['service_id'] ) ) : '';
				$message_id = isset( $_POST['message_id'] ) && ! empty( $_POST['message_id'] ) ? sanitize_text_field( wp_unslash( $_POST['message_id'] ) ) : '';

				if ( ! empty( $message_id ) && ! empty( $service_id ) ) {

					// Check delivery is appove or not.
					$is_approved_delivery = $surelywp_sv_model->surelywp_sv_is_approved_delivery( $message_id );

					if ( '1' === $is_approved_delivery || '0' === $is_approved_delivery ) {

						echo wp_json_encode(
							array(
								'success'          => true,
								'is_status_update' => true,
							)
						);
						wp_die();
					} else {
						echo wp_json_encode(
							array(
								'success'          => true,
								'is_status_update' => false,
							)
						);
						wp_die();
					}
				}
			} else {

				echo wp_json_encode(
					array(
						'success' => false,
						'status'  => 'fail',
						'error'   => esc_html__( 'Ajax Nonce Not Verify', 'surelywp-services' ),
					)
				);
				wp_die();
			}

			echo wp_json_encode(
				array(
					'success' => false,
					'message' => esc_html__( 'Something went wrong please try again.', 'surelywp-services' ),
				)
			);
			wp_die();
		}

		/**
		 * Function to get user service with pagination.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_user_service_paginate() {

			if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) ) {

				$db_page_id      = isset( $_POST['db_page_id'] ) && ! empty( $_POST['db_page_id'] ) ? sanitize_text_field( wp_unslash( $_POST['db_page_id'] ) ) : '';
				$surecart_db_tab = isset( $_POST['surecart_db_tab'] ) && ! empty( $_POST['surecart_db_tab'] ) ? sanitize_text_field( wp_unslash( $_POST['surecart_db_tab'] ) ) : '';
				$current_page    = isset( $_POST['current_page'] ) && ! empty( $_POST['current_page'] ) ? sanitize_text_field( wp_unslash( $_POST['current_page'] ) ) : '';
				$next_page       = isset( $_POST['next_page'] ) && ! empty( $_POST['next_page'] ) ? true : false;
				$per_page        = 10;

				if ( ! empty( $current_page ) ) {

					$services_obj = Surelywp_Services();

					// Global Model Variable.
					global $surelywp_sv_model;

					if ( $next_page ) {
						$page = ++$current_page;
					} else {
						$page = --$current_page;
					}

					$services_data = $surelywp_sv_model->surelywp_sv_get_user_services( $per_page, $page );
					$services      = $services_data['services'];

					$dashboard_url = '';
					if ( ! empty( $db_page_id ) ) {
						$dashboard_url = get_permalink( $db_page_id );
					} else {

						$dashboard_page_id = get_option( 'surecart_dashboard_page_id' );
						$dashboard_url     = get_permalink( $db_page_id );
					}

					if ( ! empty( $surecart_db_tab ) ) {
						$dashboard_url = add_query_arg(
							array(
								'tab' => $surecart_db_tab,
							),
							$dashboard_url
						);
					}

					ob_start();
					require SURELYWP_SERVICES_TEMPLATE_PATH . '/customer-services/services-list-table.php';
					$service_table = ob_get_clean();
					echo wp_json_encode(
						array(
							'success'       => true,
							'service_table' => $service_table,
						)
					);
					wp_die();
				}
			} else {

				echo wp_json_encode(
					array(
						'success' => false,
						'status'  => 'fail',
						'error'   => esc_html__( 'Ajax Nonce Not Verify', 'surelywp-services' ),
					)
				);
				wp_die();
			}
		}

		/**
		 * Function to get user service with admin block pagination.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_get_user_service_block_paginate() {

			if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'admin-ajax-nonce' ) ) {

				// Global Model Variable.
				global $surelywp_sv_model;

				$user_id   = isset( $_POST['user_id'] ) && ! empty( $_POST['user_id'] ) ? sanitize_text_field( wp_unslash( $_POST['user_id'] ) ) : '';
				$next_page = isset( $_POST['next_page'] ) && ! empty( $_POST['next_page'] ) ? sanitize_text_field( wp_unslash( $_POST['next_page'] ) ) : '';
				$per_page  = 10;

				if ( ! empty( $next_page ) ) {

					$services_obj  = Surelywp_Services();
					$services_data = $surelywp_sv_model->surelywp_sv_get_user_services( $per_page, $next_page, intval( $user_id ) );
					$services      = $services_data['services'];
					ob_start();
					require SURELYWP_SERVICES_TEMPLATE_PATH . '/admin/block-service-list-table.php';
					$service_table = ob_get_clean();
					echo wp_json_encode(
						array(
							'success'       => true,
							'service_table' => $service_table,
						)
					);
					wp_die();
				}
			} else {

				echo wp_json_encode(
					array(
						'success' => false,
						'status'  => 'fail',
						'error'   => esc_html__( 'Ajax Nonce Not Verify', 'surelywp-services' ),
					)
				);
				wp_die();
			}
		}

		/**
		 * Function to handle message form submit action.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_message_form_sumbit_callback() {

			if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'surelywp_sv_message_form_action' ) ) {

				global $surelywp_sv_model;
				$service_obj                = Surelywp_Services();
				$message_text               = isset( $_POST['service_message'] ) && ! empty( $_POST['service_message'] ) ? wp_kses_post( wp_unslash( $_POST['service_message'] ) ) : '';
				$service_id                 = isset( $_POST['service_id'] ) && ! empty( $_POST['service_id'] ) ? sanitize_text_field( wp_unslash( $_POST['service_id'] ) ) : '';
				$receiver_id                = isset( $_POST['receiver_id'] ) && ! empty( $_POST['receiver_id'] ) ? sanitize_text_field( wp_unslash( $_POST['receiver_id'] ) ) : '';
				$is_final_delivery          = isset( $_POST['is_final_delivery'] ) && ! empty( $_POST['is_final_delivery'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['is_final_delivery'] ) ) : 0;
				$is_milestone_delivery_flag = isset( $_POST['is_milestone_delivery_flag'] ) && ! empty( $_POST['is_milestone_delivery_flag'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['is_milestone_delivery_flag'] ) ) : 0;
				$is_milestone_delivery      = isset( $_POST['is_milestone_delivery'] ) && ! empty( $_POST['is_milestone_delivery'] ) ? wp_kses_post( wp_unslash( $_POST['is_milestone_delivery'] ) ) : '';
				$is_data_milestone_revision = isset( $_POST['is_data_milestone_revision'] ) && ! empty( $_POST['is_data_milestone_revision'] ) ? wp_kses_post( wp_unslash( $_POST['is_data_milestone_revision'] ) ) : '';
				$msg_attachment_files       = isset( $_FILES['msg_attachment_file'] ) && ! empty( $_FILES['msg_attachment_file'] ) ? $_FILES['msg_attachment_file'] : '';
				$service_setting_id         = isset( $_POST['service_setting_id'] ) && ! empty( $_POST['service_setting_id'] ) ? sanitize_text_field( wp_unslash( $_POST['service_setting_id'] ) ) : '';
				$file_names                 = null;

				// Extract milestone ID if $is_milestone_delivery is like 'milestone_submit_1'.
				$milestone_id = '';
				if ( preg_match( '/^milestone_submit_(\d+)$/', $is_milestone_delivery, $matches ) ) {
					$milestone_id = $matches[1];
				}

				// Modify $_FILES array.
				if ( ! empty( $msg_attachment_files ) ) {

					foreach ( $msg_attachment_files as $file_key => $msg_attachment_file ) {

						foreach ( $msg_attachment_file as $key => $value ) {
							$msg_attachment_file_arranged[ $key ][ $file_key ] = $value;
						}
					}
				}

				// store attachment if available.
				if ( ! empty( $msg_attachment_file_arranged ) ) {

					foreach ( $msg_attachment_file_arranged as $file ) {

						$file_names[] = $service_obj->surelywp_sv_store_msg_file( $service_id, $file );
					}
				}

				if ( ! empty( $file_names ) ) {
					$file_names = json_encode( $file_names );
				}

				if ( $is_milestone_delivery_flag ) {
					$service_status = 'milestone_submit';
				} elseif ( 0 == $is_final_delivery && ! empty( $is_data_milestone_revision ) ) {
					$service_status = 'milestone_reject';
				} else {
					$service_status = 'service_submit';
				}

				if ( ! empty( $message_text ) && ! empty( $service_id ) ) {

					$sender_id    = get_current_user_id();
					$message_data = array(
						'sender_id'            => $sender_id,
						'receiver_id'          => $receiver_id,
						'service_id'           => $service_id,
						'message_text'         => $message_text,
						'attachment_file_name' => $file_names,
						'is_final_delivery'    => $is_final_delivery,
						'milestone_id'         => '' !== ( $milestone_id ) ? $milestone_id : null,
						'service_status'       => $service_status,
					);

					$message_id   = $surelywp_sv_model->surelywp_sv_insert_service_msg( $message_data );

					// Update service status.
					if ( $is_final_delivery ) {

						/**
						 * Fire on Final Delivery Submit.
						 */
						do_action( 'surelywp_services_final_delivery_send', $message_data );

						$is_enable_auto_order_complete = Surelywp_Services::get_sv_gen_option( 'is_enable_auto_order_complete' );
						if ( $is_enable_auto_order_complete ) {

							// Set cron for auto complete service.
							$service_obj->surelywp_sv_set_service_auto_complete_cron( $service_id );
						}

						// Require Approval For Milestone Completion.
						$milestone_data = Surelywp_Services::get_sv_option( $service_setting_id, 'milestones_fields' );

						$service_status_type = '';
						$next_status = '';
						if ( is_numeric( $milestone_id ) ) {
							if ( ! isset( $milestone_data[ $milestone_id ]['milestones_require_approval'] ) ) {
								$service_status_type    = preg_replace( '/_\d+$/', '', $is_milestone_delivery );
								$next_status            = preg_replace( '/\d+$/', $milestone_id + 1, $is_milestone_delivery );
								$is_next_milestone_data = Surelywp_Services::surelywp_sv_get_milestone_details( $next_status, $milestone_data );
								if ( empty( $is_next_milestone_data ) ) {
									$milestone_id = 'milestone_complete_' . $milestone_id;
								} else {
									$milestone_id = $is_next_milestone_data['id'] ?? '';
								}
							}
						}
						if ( ! isset( $milestone_data[ $milestone_id ]['milestones_require_approval'] ) ) {
							if ( is_numeric( $milestone_id ) && $is_milestone_delivery_flag ) {

								// simple number case.
								$status        = 'service_start_' . ( $milestone_id ); // next milestone.
								$delivery_type = $is_milestone_delivery;
							} elseif ( preg_match( '/^milestone_complete_\d+$/', $milestone_id ) ) { // Last milestone_complete_X case.
								// milestone_complete_X.
								preg_match( '/_(\d+)$/', $is_milestone_delivery, $matches );
								// $status        = 'final_delivery_start';
								$status        = 'service_complete';
								$delivery_type = $is_milestone_delivery;
								$activity_data = array(
									'service_id'    => $service_id,
									'activity_type' => $delivery_type,
									'activity_info' => $message_id,
								);
								$surelywp_sv_model->surelywp_sv_insert_activity( $activity_data );
								$delivery_type = 'service_complete';
							} elseif ( ! $is_milestone_delivery_flag ) { // Final delivery submit case.

								// $delivery_type = 'delivery_send';
								// $status        = 'service_submit';
								$delivery_type = 'service_complete';
								$status        = 'service_complete';
							}
						} else {
							// Require Approval For Milestone Completion End.
							if ( ! empty( $is_milestone_delivery ) ) {
								$delivery_type = $is_milestone_delivery;
								$status        = $is_milestone_delivery;
							} else {
								// $delivery_type = 'delivery_send';
								// $status        = 'service_submit';
								$delivery_type = 'service_complete';
								$status        = 'service_complete';
							}
						}
						// $status           = 'service_submit';
						$is_status_update = $surelywp_sv_model->surelywp_sv_update_service_status( $service_id, $status );

						// Add Activity.
						$activity_data = array(
							'service_id'    => $service_id,
							'activity_type' => $delivery_type,
							'activity_info' => $message_id,
						);
						$surelywp_sv_model->surelywp_sv_insert_activity( $activity_data );
					}

					if ( $message_id ) {

						echo wp_json_encode(
							array(
								'success'      => true,
								'message'      => esc_html__( 'Message saved', 'surelywp-services' ),
								'message_data' => $message_data,
							)
						);
						wp_die();
					}
				}
			} else {

				echo wp_json_encode(
					array(
						'success' => false,
						'status'  => 'fail',
						'error'   => esc_html__( 'Ajax Nonce Not Verify', 'surelywp-services' ),
					)
				);
				wp_die();
			}

			echo wp_json_encode(
				array(
					'success' => false,
					'message' => esc_html__( 'Something went wrong please try again.', 'surelywp-services' ),
				)
			);
			wp_die();
		}

		/**
		 * Function to handle contract form submit action.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_contract_form_sumbit_callback() {

			if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'surelywp_sv_contract_form_action' ) ) {

				global $surelywp_sv_model, $surelywp_model;

				$service_obj        = Surelywp_Services();
				$service_id         = isset( $_POST['service_id'] ) && ! empty( $_POST['service_id'] ) ? sanitize_text_field( wp_unslash( $_POST['service_id'] ) ) : '';
				$signature          = isset( $_POST['signature'] ) && ! empty( $_POST['signature'] ) ? sanitize_text_field( wp_unslash( $_POST['signature'] ) ) : '';
				$contract_details   = isset( $_POST['contract_details'] ) && ! empty( $_POST['contract_details'] ) ? wp_kses_post( wp_unslash( $_POST['contract_details'] ) ) : '';
				$service_setting_id = isset( $_POST['service_setting_id'] ) && ! empty( $_POST['service_setting_id'] ) ? sanitize_text_field( wp_unslash( $_POST['service_setting_id'] ) ) : '';
				$service_status     = '';

				if ( ! empty( $service_id ) && ! empty( $signature ) && ! empty( $contract_details ) && ! empty( $service_setting_id ) ) {

					$contract_data = array(
						'service_id'       => $service_id,
						'signature'        => $signature,
						'contract_details' => $contract_details,
					);

					$is_insert = $surelywp_sv_model->surelywp_sv_add_service_contract( $contract_data );

					if ( $is_insert ) {

						/**
						 * Fire on contract Submit.
						 */
						do_action( 'surelywp_services_contract_submit', $contract_data );

						$ask_for_requirements = Surelywp_Services::get_sv_option( $service_setting_id, 'ask_for_requirements' );

						$requirements = $surelywp_sv_model->surelywp_sv_get_service_requirements( $service_id );

						if ( empty( $requirements ) && ! empty( $ask_for_requirements ) ) { // if customer have not submitted requirement on product page.

							// Send Email to customer for .
							$service_obj->surelywp_sv_send_service_email( $service_id, 'requirement_ask_email' );

							// Set cron requirement submit reminder.
							$service_obj->surelywp_sv_set_service_req_reminder_cron( $service_id );

							// update service status.
							$service_status                 = 'waiting_for_req';
							$service_data['service_status'] = $service_status;
							$is_updated                     = $surelywp_sv_model->surelywp_sv_update_service( $service_id, $service_data );

						} else {

							// update service status and add delivery date.
							$service_status                      = 'service_start_0';
							$delivery_date                       = $service_obj->surelywp_sv_calculate_delivery_date( $service_setting_id );
							$service_data['service_status']      = $service_status;
							$service_data['delivery_date']       = $delivery_date;
							$service_data['revisions_remaining'] = Surelywp_Services::surelywp_sv_get_revisions_allowed( $service_setting_id, 0 );
							$is_updated                          = $surelywp_sv_model->surelywp_sv_update_service( $service_id, $service_data );
						}

						// Add Activities.
						$surelywp_sv_model->surelywp_sv_insert_activity(
							array(
								'service_id'    => $service_id,
								'activity_type' => 'service_contract_signed',
								'activity_info' => null,
							)
						);

						if ( 'service_start_0' === $service_status ) {

							$surelywp_sv_model->surelywp_sv_insert_activity(
								array(
									'service_id'    => $service_id,
									'activity_type' => 'service_start',
									'activity_info' => null,
								)
							);
						}

						echo wp_json_encode(
							array(
								'success' => true,
								'message' => esc_html__( 'Contract Submitted Successfully', 'surelywp-services' ),
							)
						);
						wp_die();
					}
				}
			} else {

				echo wp_json_encode(
					array(
						'success' => false,
						'status'  => 'fail',
						'error'   => esc_html__( 'Ajax Nonce Not Verify', 'surelywp-services' ),
					)
				);
				wp_die();
			}

			echo wp_json_encode(
				array(
					'success' => false,
					'message' => esc_html__( 'Something went wrong please try again.', 'surelywp-services' ),
				)
			);
			wp_die();
		}


		/**
		 * Function to handle requrement form submit action.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_req_form_sumbit_callback() {

			if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'surelywp_sv_req_form_action' ) ) {

				global $surelywp_sv_model, $surelywp_model;

				$service_obj                  = Surelywp_Services();
				$service_id                   = isset( $_POST['service_id'] ) && ! empty( $_POST['service_id'] ) ? sanitize_text_field( wp_unslash( $_POST['service_id'] ) ) : '';
				$service_setting_id           = isset( $_POST['service_setting_id'] ) && ! empty( $_POST['service_setting_id'] ) ? sanitize_text_field( wp_unslash( $_POST['service_setting_id'] ) ) : '';
				$service_requirement          = isset( $_POST['service_requirement'] ) && ! empty( $_POST['service_requirement'] ) ? $surelywp_model->surelywp_escape_slashes_deep( $_POST['service_requirement'], true, true ) : '';
				$service_requirement_text     = isset( $_POST['service_requirement_text'] ) && ! empty( $_POST['service_requirement_text'] ) ? $surelywp_model->surelywp_escape_slashes_deep( $_POST['service_requirement_text'], true, true ) : '';
				$service_requirement_dropdown = isset( $_POST['service_requirement_dropdown'] ) && ! empty( $_POST['service_requirement_dropdown'] ) ? $surelywp_model->surelywp_escape_slashes_deep( $_POST['service_requirement_dropdown'], true, true ) : '';
				$requirement_files            = isset( $_FILES['requirement_files'] ) && ! empty( $_FILES['requirement_files'] ) ? $_FILES['requirement_files'] : '';
				$service_requirement_data     = isset( $_POST['service_requirement_data'] ) && ! empty( $_POST['service_requirement_data'] ) ? $surelywp_model->surelywp_escape_slashes_deep( $_POST['service_requirement_data'], true, true ) : '';
				$requirements_data            = array();
				$requirement_file_names       = array();
				$requirement_files_arranged   = array();

				// Modify $_FILES array.
				if ( ! empty( $requirement_files ) ) {
					foreach ( $requirement_files as $file_key => $requirement_file ) {
						foreach ( $requirement_file as $key => $values ) {
							foreach ( $values as $numer => $value ) {
								$requirement_files_arranged[ $key ][ $numer ][ $file_key ] = $value;
							}
						}
					}
				}

				// store file and get file name.
				if ( ! empty( $requirement_files_arranged ) ) {

					foreach ( $requirement_files_arranged as $file_key => $files ) {
						foreach ( $files as $key => $file ) {

							$requirement_file_names[ $file_key ][] = $service_obj->surelywp_sv_store_req_file( $service_id, $file );
						}
					}
				}

				if ( ! empty( $service_id ) && ! empty( $service_requirement_data ) ) {

					foreach ( $service_requirement_data as $key => $requirement_data ) {

						$unserialize_requirement_data = unserialize( $service_requirement_data[ $key ] );
						$req_field_type               = $unserialize_requirement_data['req_field_type'] ?? '';

						$attachments_file_names = '';

						$is_required_field = isset( $unserialize_requirement_data['is_required_field'] ) && '1' === $unserialize_requirement_data['is_required_field'] ? true : false;

						// Validations.
						if ( 'file' === $req_field_type ) {

							$attachments_file_names = isset( $requirement_file_names[ $key ] ) && ! empty( $requirement_file_names[ $key ] ) ? json_encode( $requirement_file_names[ $key ] ) : '';

							if ( empty( $attachments_file_names ) ) {

								if ( $is_required_field ) {
									echo wp_json_encode(
										array(
											'success' => false,
											'message' => esc_html__( 'Please Fill Required Fields.', 'surelywp-services' ),
										)
									);
									wp_die();

								} else {
									continue; // Skip loop is user not have uplode attachment.
								}
							}
						} elseif ( 'textarea' === $req_field_type ) {

							if ( isset( $service_requirement[ $key ] ) && empty( $service_requirement[ $key ] ) ) {

								if ( $is_required_field ) {
									echo wp_json_encode(
										array(
											'success' => false,
											'message' => esc_html__( 'Please Fill Required Fields.', 'surelywp-services' ),
										)
									);
									wp_die();
								} else {
									continue; // Skip loop for that textarea.
								}
							}
						} elseif ( 'text' === $req_field_type ) {

							if ( isset( $service_requirement_text[ $key ] ) && empty( $service_requirement_text[ $key ] ) ) {

								if ( $is_required_field ) {
									echo wp_json_encode(
										array(
											'success' => false,
											'message' => esc_html__( 'Please Fill Required Fields.', 'surelywp-services' ),
										)
									);
									wp_die();
								} else {
									continue; // Skip loop for that textarea.
								}
							}
						} elseif ( 'dropdown' === $req_field_type ) {

							if ( isset( $service_requirement_dropdown[ $key ] ) && empty( $service_requirement_dropdown[ $key ] ) ) {

								if ( $is_required_field ) {
									echo wp_json_encode(
										array(
											'success' => false,
											'message' => esc_html__( 'Please Fill Required Fields.', 'surelywp-services' ),
										)
									);
									wp_die();
								} else {
									continue; // Skip loop for that dropdown.
								}
							}
						}

						$requirements_data[] = array(
							'service_id'        => $service_id,
							'requirement_type'  => $req_field_type,
							'requirement_title' => $unserialize_requirement_data['req_title'] ?? '',
							'requirement_desc'  => $unserialize_requirement_data['req_desc'] ?? '',
							'requirement'       => 'textarea' === $req_field_type ? ( $service_requirement[ $key ] ?? '' ) : ( 'text' === $req_field_type ? ( $service_requirement_text[ $key ] ?? '' ) : ( 'dropdown' === $req_field_type ? ( $service_requirement_dropdown[ $key ] ?? '' ) : $attachments_file_names ) ),
						);
					}

					if ( ! empty( $requirements_data ) ) {

						$surelywp_sv_model->surelywp_sv_add_service_requirement( $requirements_data );
					}

					/**
					 * Fire on Requirement Submit.
					 */
					do_action( 'surelywp_services_requirement_submit', $requirements_data );

					// Send Email to admins for customer requirements.
					$service_obj->surelywp_sv_send_service_email( $service_id, 'customer_requirement_notification' );

					// Clear requirement reminder main cron.
					$hook = SURELYWP_SERVICES_REQ_REMINDER_CRON;
					$args = array( intval( $service_id ), 'requirement_reminder_email' );
					Surelywp_Services::surelywp_sv_unset_cron( $hook, $args );

					// update service status and add delivery date.
					$service_status                      = 'service_start_0';
					$delivery_date                       = $service_obj->surelywp_sv_calculate_delivery_date( $service_setting_id );
					$service_data['service_status']      = $service_status;
					$service_data['delivery_date']       = $delivery_date;
					$service_data['revisions_remaining'] = Surelywp_Services::surelywp_sv_get_revisions_allowed( $service_setting_id, 0 );
					$is_updated                          = $surelywp_sv_model->surelywp_sv_update_service( $service_id, $service_data );

					// Add Activity.
					$surelywp_sv_model->surelywp_sv_insert_activity(
						array(
							'service_id'    => $service_id,
							'activity_type' => 'service_req_received',
							'activity_info' => null,
						)
					);

					$surelywp_sv_model->surelywp_sv_insert_activity(
						array(
							'service_id'    => $service_id,
							'activity_type' => 'service_start',
							'activity_info' => null,
						)
					);

					echo wp_json_encode(
						array(
							'success' => true,
							'message' => esc_html__( 'Requirements Submitted Successfully', 'surelywp-services' ),
						)
					);
					wp_die();
				} else {

					echo wp_json_encode(
						array(
							'success' => false,
							'message' => esc_html__( 'Something went wrong please try again.', 'surelywp-services' ),
						)
					);
					wp_die();
				}
			} else {

				echo wp_json_encode(
					array(
						'success' => false,
						'status'  => 'fail',
						'error'   => esc_html__( 'Ajax Nonce Not Verify', 'surelywp-services' ),
					)
				);
				wp_die();
			}
		}

		/**
		 * Function to handle fetch service messages.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_fetch_service_messages() {

			if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'surelywp_sv_message_form_action' ) ) {

				global $surelywp_sv_model;
				$services_obj          = Surelywp_Services();
				$service_id            = isset( $_POST['service_id'] ) && ! empty( $_POST['service_id'] ) ? sanitize_text_field( wp_unslash( $_POST['service_id'] ) ) : '';
				$last_message_datatime = isset( $_POST['last_message_datatime'] ) && ! empty( $_POST['last_message_datatime'] ) ? sanitize_text_field( wp_unslash( $_POST['last_message_datatime'] ) ) : '';
				if ( ! empty( $service_id ) ) {
					$service_messages = $surelywp_sv_model->surelywp_sv_fetch_service_latest_messages( $service_id, date( 'Y-m-d H:i:s', strtotime( $last_message_datatime ) ) );
					if ( ! empty( $service_messages ) ) {

						ob_start();
						foreach ( $service_messages as $message ) {
							$current_user_id      = get_current_user_id();
							$message_id           = $message->message_id ?? '';
							$sender_id            = $message->sender_id ?? '';
							$receiver_id          = $message->receiver_id ?? '';
							$is_final_delivery    = isset( $message->is_final_delivery ) && ! empty( $message->is_final_delivery ) ? true : false;
							$is_approved_delivery = $message->is_approved_delivery ?? '';
							$message_text         = $message->message_text ?? '';
							$file_names           = $message->attachment_file_name ?? '';

							$message_time          = isset( $message->created_at ) && ! empty( $message->created_at ) ? wp_date( 'M d, h:i A', strtotime( $message->created_at ) ) : '';
							$complete_message_time = isset( $message->created_at ) && ! empty( $message->created_at ) ? wp_date( 'Y-m-d H:i:s', strtotime( $message->created_at ) ) : '';
							$sender                = get_userdata( $sender_id );
							$message_class         = $current_user_id === (int) $sender_id ? 'right' : 'left';
							if ( ! empty( $sender ) && ! empty( $message_text ) ) {

								$sender_username = $sender->display_name ?? '';
								$sender_img      = get_avatar_url( $sender->user_email );
								require SURELYWP_SERVICES_TEMPLATE_PATH . '/customer-services/service-message.php';
							}
						}
						$message_html = ob_get_clean();
						echo wp_json_encode(
							array(
								'success'           => true,
								'message'           => esc_html__( 'fetched new message', 'surelywp-services' ),
								'is_final_delivery' => $is_final_delivery,
								'message_time'      => $complete_message_time,
								'message_html'      => $message_html,
							)
						);
						wp_die();
					} else {
						echo wp_json_encode(
							array(
								'success' => false,
								'message' => esc_html__( 'not found any new message', 'surelywp-services' ),
							)
						);
						wp_die();
					}
				}
			} else {

				echo wp_json_encode(
					array(
						'success' => false,
						'status'  => 'fail',
						'error'   => esc_html__( 'Ajax Nonce Not Verify', 'surelywp-services' ),
					)
				);
				wp_die();
			}
		}


		/**
		 * Function to handle load more service messages.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_load_more_service_messages() {

			if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'surelywp_sv_message_form_action' ) ) {

				global $surelywp_sv_model;
				$service_id             = isset( $_POST['service_id'] ) && ! empty( $_POST['service_id'] ) ? sanitize_text_field( wp_unslash( $_POST['service_id'] ) ) : '';
				$first_message_datatime = isset( $_POST['first_message_datatime'] ) && ! empty( $_POST['first_message_datatime'] ) ? sanitize_text_field( wp_unslash( $_POST['first_message_datatime'] ) ) : '';

				if ( ! empty( $service_id ) ) {

					$service_messages = $surelywp_sv_model->surelywp_sv_load_more_messages( $service_id, date( 'Y-m-d H:i:s', strtotime( $first_message_datatime ) ) );
					if ( ! empty( $service_messages ) ) {
						$service_messages = array_reverse( $service_messages );

						ob_start();
						foreach ( $service_messages as $message ) {
							$current_user_id      = get_current_user_id();
							$message_id           = $message->message_id ?? '';
							$sender_id            = $message->sender_id ?? '';
							$receiver_id          = $message->receiver_id ?? '';
							$message_text         = $message->message_text ?? '';
							$is_final_delivery    = $message->is_final_delivery ?? '';
							$is_approved_delivery = $message->is_approved_delivery ?? '';
							$file_names           = $message->attachment_file_name ?? '';

							$message_time          = isset( $message->created_at ) && ! empty( $message->created_at ) ? wp_date( 'M d, h:i A', strtotime( $message->created_at ) ) : '';
							$complete_message_time = isset( $message->created_at ) && ! empty( $message->created_at ) ? wp_date( 'Y-m-d H:i:s', strtotime( $message->created_at ) ) : '';
							$sender                = get_userdata( $sender_id );
							$message_class         = $current_user_id === (int) $sender_id ? 'right' : 'left';

							if ( ! empty( $sender ) && ! empty( $message_text ) ) {

								$sender_username = $sender->display_name ?? '';
								$sender_img      = get_avatar_url( $sender->user_email );
								require SURELYWP_SERVICES_TEMPLATE_PATH . '/customer-services/service-message.php';
							}
						}
						$message_html = ob_get_clean();
						echo wp_json_encode(
							array(
								'success'      => true,
								'message'      => esc_html__( 'loaded new messages', 'surelywp-services' ),
								'message_time' => $complete_message_time,
								'message_html' => $message_html,
							)
						);
						wp_die();
					} else {
						echo wp_json_encode(
							array(
								'success' => false,
								'message' => esc_html__( 'not found any old messages', 'surelywp-services' ),
							)
						);
						wp_die();
					}
				}
			} else {

				echo wp_json_encode(
					array(
						'success' => false,
						'status'  => 'fail',
						'error'   => esc_html__( 'Ajax Nonce Not Verify', 'surelywp-services' ),
					)
				);
				wp_die();
			}
		}

		/**
		 * Function to send message emails.
		 *
		 * @package Services For SureCart
		 * @since   1.0.0
		 */
		public function surelywp_sv_send_message_mail() {

			if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'surelywp_sv_message_form_action' ) ) {

				global $surelywp_model;

				$message_data = isset( $_POST['message_data'] ) && ! empty( $_POST['message_data'] ) ? $surelywp_model->surelywp_escape_slashes_deep( $_POST['message_data'] ) : '';

				// Validation.
				if ( ! empty( $message_data ) ) {

					/**
					 * Fire on Message send.
					 */
					do_action( 'surelywp_services_message_send', $message_data );

					Surelywp_Services()->surelywp_sv_send_msg_email( $message_data );

					echo wp_json_encode(
						array(
							'success'  => true,
							'messages' => esc_html__( 'Message Mail Send Successfully.', 'surelywp-services' ),
						)
					);

					wp_die();
				}

				echo wp_json_encode(
					array(
						'success'  => false,
						'messages' => esc_html__( 'Mail Not send.', 'surelywp-services' ),
					)
				);
				wp_die();

			} else {

				echo wp_json_encode(
					array(
						'success'  => false,
						'messages' => esc_html__( 'Ajax Nonce Not Verify.', 'surelywp-services' ),
					)
				);
				wp_die();
			}
		}
	}
}
$er_ajax_handler = new Surelywp_Services_Ajax_Handler();
