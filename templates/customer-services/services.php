<?php
/**
 * Services template
 *
 * @author  Surelywp
 * @package Services For SureCart
 * @since 1.0.0
 */

global $surelywp_sv_model, $surelywp_model;

use SureCart\Models\Order;

// Exit if accessed directly.
if ( ! defined( 'SURELYWP_SERVICES' ) ) {
	exit;
}


$service_singular_name = Surelywp_Services::get_sv_singular_name();
$service_plural_name   = Surelywp_Services::get_sv_plural_name();

$dashboard_url     = '';
$dashboard_page_id = '';
if ( isset( $view_page_id ) && ! empty( $view_page_id ) ) {
	$dashboard_page_id = $view_page_id;
	$dashboard_url     = get_permalink( $view_page_id );
} else {

	$dashboard_page_id = get_option( 'surecart_dashboard_page_id' );
	$dashboard_url     = get_permalink( $dashboard_page_id );
}

$dashboard_url = apply_filters( 'surelywp_sv_dashboard_url', $dashboard_url );


$services_url = add_query_arg(
	array(
		'action' => 'index',
		'model'  => 'services',
	),
	$dashboard_url
);

// For Custom Surecart page.
$surecart_db_tab = isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '';
if ( ! empty( $surecart_db_tab ) ) {
	$dashboard_url = add_query_arg(
		array(
			'tab' => $surecart_db_tab,
		),
		$dashboard_url
	);
}

$services_obj = Surelywp_Services();
$model        = isset( $_GET['model'] ) && ! empty( $_GET['model'] ) ? sanitize_text_field( wp_unslash( $_GET['model'] ) ) : '';

// Set model services shortcode and block.
if ( isset( $is_service_list_shortcode ) ) {
	$model = 'services';
}

$service_id           = isset( $_GET['service_id'] ) && ! empty( $_GET['service_id'] ) ? sanitize_text_field( wp_unslash( $_GET['service_id'] ) ) : '';
$view                 = isset( $_GET['view'] ) && ! empty( $_GET['view'] ) ? sanitize_text_field( wp_unslash( $_GET['view'] ) ) : '';
$service_req_order_id = isset( $_GET['order_id'] ) && ! empty( $_GET['order_id'] ) ? sanitize_text_field( wp_unslash( $_GET['order_id'] ) ) : '';
$service_tab          = 'messages';

// Get the ID of the supre administrator.
$super_admin_id   = 0;
$all_super_admins = get_super_admins();

if ( ! empty( $all_super_admins ) ) {

	$first_super_admin_username = reset( $all_super_admins );
	$super_admin                = get_user_by( 'login', $first_super_admin_username );

	if ( $super_admin ) {
		$super_admin_id = $super_admin->ID;
	}
}

$heading_title = $heading_title ?? $service_plural_name;
?>
<?php
// if service id not get and model is services.
if ( empty( $service_id ) && ! empty( $model ) && 'services' === $model ) {

	if ( 'request_service' === $view ) {
		?>
		<div class="new-service-request">
			<div class="breadbcrumbs">
				<sc-breadcrumbs>
					<sc-breadcrumb
						href="<?php echo esc_url( $dashboard_url ); ?>"><?php esc_html_e( 'Dashboard', 'surelywp-services' ); ?></sc-breadcrumb>
					<sc-breadcrumb
						href="<?php echo esc_url( $services_url ); ?>"><?php echo esc_html( $service_plural_name ); ?></sc-breadcrumb>
					<sc-breadcrumb>
						<?php
						// translators: %s is the singular name of the service.
						printf( esc_html__( 'New %s', 'surelywp-services' ), esc_html( $service_singular_name ) );
						?>
					</sc-breadcrumb>
				</sc-breadcrumbs>
			</div>
			<div class="new-service-request-wrap">
				<div class="services-item-list-heading">
					<sc-heading>
						<?php
						// translators: %s is the singular name of the service.
						printf( esc_html__( 'New %s', 'surelywp-services' ), esc_html( $service_singular_name ) );
						?>
					</sc-heading>
				</div>
			</div>
			<div class="new-service-request-form-wrap">
				<sc-form class="new-service-request-form" id="new-service-request-form">
					<?php wp_nonce_field( 'surelywp_sv_service_request_form_action', 'surelywp_sv_service_request_form_nonce' ); ?>
					<sc-input type="hidden" hidden="true" id="dashboard-page-id" class="dashboard-page-id hidden"
						name="sv_dashboard_page_id" value="<?php echo esc_html( $dashboard_page_id ); ?>"></sc-input>
					<sc-input type="hidden" hidden="true" class="dashboard-tab hidden" name="sv_support_tab"
						value="<?php echo esc_html( $surecart_db_tab ); ?>"></sc-input>
					<div class="service-order-selection-wrap">
						<sc-form-control name="service_order_id" help="true"
							label="<?php esc_html_e( 'Select Associated Order', 'surelywp-services' ); ?>" required>
							<span slot="help-text">
								<?php
								// translators: %s is the singular name of the service.
								printf( esc_html__( 'Select the order associated with the %s you want to create.', 'surelywp-services' ), esc_html( $service_singular_name ) );
								?>
							</span>
							<sc-select id="service-order-selection" name="service_order"
								class="service-order-selection customer-role"
								placeholder="<?php esc_html_e( 'Select...', 'surelywp-services' ); ?>"
								value="<?php echo esc_attr( $service_req_order_id ); ?>" search required></sc-select>
							<script>
								document.querySelector('#service-order-selection').choices = [
									<?php
									$services_options    = Surelywp_Services::surelywp_sp_get_customer_remaining_service_selection();
									$rc_services_options = Surelywp_Services::surelywp_sp_get_customer_recurring_service_selection();
									$options             = array_merge( $services_options, $rc_services_options );
									if ( ! empty( $options ) ) {
										$options = array_reverse( $options );
										foreach ( $options as $option ) {
											echo "{label: '" . esc_html( $option['label'] ) . "', value: '" . esc_html( $option['value'] ) . "'},";
										}
									}
									?>
								];
							</script>
						</sc-form-control>
					</div>
					<sc-button class="surelywp-sv-request-service-form-submit-btn"
						id="surelywp-sv-request-service-form-submit-btn" type="primary"
						submit="true"><?php esc_html_e( 'Continue', 'surelywp-services' ); ?></sc-button>
				</sc-form>
			</div>
		</div>
		<?php
	} else {

		$service_per_page = 10;
		$page_num         = 1;
		$services_data    = $surelywp_sv_model->surelywp_sv_get_user_services( $service_per_page, $page_num );
		$services         = $services_data['services'] ?? array();
		$pagination_data  = $services_data['pagination'] ?? array();

		$user_id                    = get_current_user_id();
		$total_services_remaining   = $surelywp_sv_model->surelywp_sv_get_customer_remaining_service( $user_id );
		$total_rc_sv_quota          = $surelywp_sv_model->surelywp_sv_get_remaining_rc_service( $user_id );
		$cutomer_total_remaining_sv = intval( $total_services_remaining ) + intval( $total_rc_sv_quota );
		?>
		<div class="services-item-list">
			<?php if ( ! isset( $is_service_list_shortcode ) ) { ?>
				<div class="breadbcrumbs">
					<sc-breadcrumbs>
						<sc-breadcrumb
							href="<?php echo esc_url( $dashboard_url ); ?>"><?php esc_html_e( 'Dashboard', 'surelywp-services' ); ?></sc-breadcrumb>
						<sc-breadcrumb><?php echo esc_html( $service_plural_name ); ?></sc-breadcrumb>
					</sc-breadcrumbs>
				</div>
			<?php } ?>
			<div class="services-item-list-heading-wrap">
				<div class="services-item-list-heading">
					<sc-heading><?php echo esc_html( $heading_title ); ?></sc-heading>
				</div>
				<div class="create-new-service">
					<?php
					$request_service_url = add_query_arg(
						array(
							'view' => 'request_service',
							'tab'  => $surecart_db_tab,
						),
						$services_url
					);
					?>
					<?php if ( $cutomer_total_remaining_sv > 0 ) { ?>
						<div class="sv-list-btns">
							<div class="services-remaining">
								<?php
								// translators: %1$s is the total remaining services, %2$s is the plural name of the services.
								printf( esc_html__( '%1$s %2$s Remaining', 'surelywp-services' ), esc_html( $cutomer_total_remaining_sv ), esc_html( $service_plural_name ) );
								?>
							</div>
							<sc-button class="create-new-service-btn" type="primary"
								href="<?php echo esc_url( $request_service_url ); ?>">
								<sc-icon slot="prefix" name="plus" class="hydrated"></sc-icon>
								<?php
								// translators: %s is the singular name of the service.
								printf( esc_html__( 'New %s', 'surelywp-services' ), esc_html( $service_singular_name ) );
								?>
							</sc-button>
						</div>
					<?php } ?>
				</div>
			</div>
			<div class="hydrated services-list-table-card">
				<div id="services-list-table" class="services-list-table"
					data-dashboard-page-id="<?php echo esc_attr( $dashboard_page_id ); ?>"
					data-surecart-db-tab="<?php echo esc_attr( $surecart_db_tab ); ?>">
					<?php require SURELYWP_SERVICES_TEMPLATE_PATH . '/customer-services/services-list-table.php'; ?>
				</div>
			</div>
			<?php if ( ! empty( $pagination_data ) ) { ?>
				<div class="services-pagination">
					<sc-pagination class="sv-user-services-paginate" id="sv-user-services-paginate"
						page="<?php echo esc_html( $pagination_data['current_page'] ); ?>"
						per-page="<?php echo esc_html( $service_per_page ); ?>"
						total="<?php echo esc_html( $pagination_data['total_services'] ); ?>"
						total-showing="<?php echo esc_html( $pagination_data['total_show'] ); ?>"
						total-pages="<?php echo esc_html( $pagination_data['total_pages'] ); ?>"></sc-pagination>
				</div>
			<?php } ?>
		</div>
		<?php
	}
} elseif ( ! empty( $service_id ) ) {

	$service = $surelywp_sv_model->surelywp_sv_get_customer_service( $service_id );

	if ( $service ) {

		$service_setting_id      = $service[0]->service_setting_id ?? '';
		$order_id                = $service[0]->order_id ?? '';
		$product_id              = $service[0]->product_id ?? '';
		$service_status          = $service[0]->service_status ?? '';
		$service_updated_at      = $service[0]->updated_at ?? '';
		$delivery_date           = $service[0]->delivery_date ?? '';
		$recurring_service_id    = $service[0]->recurring_service_id ?? '';
		$services_remaining      = $service[0]->services_remaining ?? 0;
		$service_status_text     = $services_obj->surelywp_sv_get_service_status( $service_status, $product_id, $service_id );
		$service_data            = $services_obj->surelywp_sv_get_service_data( $order_id, $product_id );
		$price                   = $services_obj->surelywp_sv_get_product_paid_price( $order_id, $product_id );
		$paid_amount             = $price['amount'] ?? '';
		$currency_type           = $price['currency'];
		$price_id                = $price['id'] ?? '';
		$variant_id              = $price['variant_id'];
		$product_variant_options = $price['product_variant_options'] ?? '';
		$order_view_url          = $dashboard_url . '?action=show&model=order&id=' . $order_id;
		$product_view_url        = $service_data['product_permalink'];
		$order_date              = $service_data['order_date'];
		$ask_for_contract        = '';
		$ask_for_requirements    = '';
		$is_have_contract        = '';
		$is_have_requirement     = '';
		$delivery_date_from_db = '';
		if ( ! empty( $delivery_date ) ) {
			$delivery_date_from_db = $delivery_date;
			$date_format   = get_option( 'date_format', 'j F Y' );
			$delivery_date = wp_date( $date_format, strtotime( $delivery_date ) );
		} else {
			$delivery_date = 'NA';
		}
		// Delivery Date Short.
		if ( ! empty( $delivery_date ) ) {
			// Force short month format (e.g., Oct 16, 2025)
			$date_format = 'M j, Y';
			$delivery_date_short = wp_date( $date_format, strtotime( $delivery_date ) );
		} else {
			$delivery_date_short = 'NA';
		}

		$ask_for_requirements = Surelywp_Services::get_sv_option( $service_setting_id, 'ask_for_requirements' );
		$ask_for_contract     = Surelywp_Services::get_sv_option( $service_setting_id, 'ask_for_contract' );
		$is_have_contract     = $surelywp_sv_model->surelywp_sv_get_service_contract( $service_id );
		$is_have_requirement  = $surelywp_sv_model->surelywp_sv_get_service_requirements( $service_id );
		$milestones_fields    = Surelywp_Services::get_sv_option( $service_setting_id, 'milestones_fields' );

		$service_status_type = preg_replace( '/_\d+$/', '', $service_status );

		$milestone_data = Surelywp_Services::surelywp_sv_get_milestone_details( $service_status, $milestones_fields );
		$milestone_id   = $milestone_data['id'] ?? '';

		if ( $service_status_type == 'milestone_submit' || $service_status_type == 'service_start' ) {
			$revision_allowed = Surelywp_Services::surelywp_sv_number_of_revisions_remaining( $milestone_id, $service_setting_id, $service_id, $milestone_data );
		}
		if ( preg_match( '/(\d+)$/', $service_status, $matches ) ) {
			$current_id      = (int) $matches[1];
			$next_status     = preg_replace( '/\d+$/', $current_id + 1, $service_status );
			$milestone_data1 = Surelywp_Services::surelywp_sv_get_milestone_details( $next_status, $milestones_fields );
			if ( empty( $milestone_data1 ) ) {
				$milestone_id = 'milestone_complete_' . $current_id;
			} else {
				$milestone_id = $milestone_data['id'] ?? '';
			}
		}
		?>
		<div class="surelywp-sv-serview-view customer">

			<?php do_action( 'surelywp_sv_customer_view_top', $service, $service_data ); ?>

			<div class="view-left">
				<?php if ( ! isset( $is_service_list_shortcode ) ) { ?>
					<div class="breadbcrumbs">
						<sc-breadcrumbs>
							<sc-breadcrumb
								href="<?php echo esc_url( $dashboard_url ); ?>"><?php esc_html_e( 'Dashboard', 'surelywp-services' ); ?></sc-breadcrumb>
							<sc-breadcrumb
								href="<?php echo esc_url( $services_url ); ?>"><?php echo esc_html( $service_plural_name ); ?></sc-breadcrumb>
							<sc-breadcrumb><?php echo esc_html( $service_singular_name ); ?></sc-breadcrumb>
						</sc-breadcrumbs>
					</div>
				<?php } ?>

				<?php do_action( 'surelywp_sv_customer_view_before_heading', $service, $service_data ); ?>

				<div class="services-item-list-heading">
					<div class="services-heading">
						<sc-heading><?php echo esc_html( $service_singular_name ); ?></sc-heading>
						<?php $tag_type = 'service_complete' === $service_status ? 'success' : ( 'service_canceled' === $service_status ? 'danger' : 'warning' ); ?>
						<sc-tag type="<?php echo esc_attr( $tag_type ); ?>" size="small" pill=""
							class="hydrated"><?php echo esc_html( $service_status_text ); ?></sc-tag>
					</div>
					<div class="time">
						<p class="created-at">
							<?php
							$date_format = get_option( 'date_format', 'j F Y' );
							$time_format = get_option( 'time_format', 'H:i' );
							$format      = $date_format . ' \a\t ' . $time_format;
							// translators: %s is the service creation date.
							printf( esc_html__( 'Created on %s', 'surelywp-services' ), wp_date( $format, (int) $service_data['order_date'] ) );
							?>
						</p>
					</div>
				</div>
				<?php

				do_action( 'surelywp_sv_customer_view_after_heading', $service, $service_data );

				// after order placed.
				if ( ! empty( $service_data ) ) {

					do_action( 'surelywp_sv_customer_view_before_product_details', $service, $service_data );

					$req_fields             = $services_obj->get_sv_option( $service_setting_id, 'req_fields' );
					$ask_for_requirements   = $services_obj->get_sv_option( $service_setting_id, 'ask_for_requirements' );
					$submitted_requirements = $surelywp_sv_model->surelywp_sv_get_service_requirements( $service_id );

					// set requirement tab active.
					if ( ( 'waiting_for_req' === $service_status || 'waiting_for_contract' === $service_status ) && '1' === $ask_for_requirements && ! empty( $req_fields ) && empty( $submitted_requirements ) ) {
						$service_tab = 'requirements';
					}
					?>
					<div class="service-order-detail">
						<sc-table>
							<sc-table-cell slot="head"></sc-table-cell>
							<sc-table-cell slot="head"
								class="product-name"><?php esc_html_e( 'Product', 'surelywp-services' ); ?></sc-table-cell>
							<?php if ( ! empty( $product_variant_options ) ) { ?>
								<sc-table-cell slot="head"
									class="variants"><?php esc_html_e( 'Variants', 'surelywp-services' ); ?></sc-table-cell>
							<?php } ?>
							<sc-table-cell slot="head" class="delivery-date">
								<?php esc_html_e( 'Delivery Date', 'surelywp-services' ); ?></sc-table-cell>
							<sc-table-row>
								<sc-table-cell>
									<div class="service-product-image">
										<img src="<?php echo !empty($service_data['product_img_url']) ? esc_url($service_data['product_img_url']) : esc_url(trailingslashit(\SureCart::core()->assets()->getUrl()) . 'images/placeholder.jpg'); //phpcs:ignore ?>"
											alt="service_product_image">
									</div>
								</sc-table-cell>
								<sc-table-cell>
									<a class="view-product-link"
										aria-label="<?php echo esc_attr__( 'View Product', 'surelywp-services' ); ?>"
										href="<?php echo esc_url( $product_view_url ); ?>">
										<?php echo esc_html( $service_data['product_name'] ); ?>
									</a>
								</sc-table-cell>
								<?php if ( ! empty( $product_variant_options ) ) { ?>
									<sc-table-cell>
										<?php
										foreach ( $product_variant_options as $variant ) {
											if ( ! empty( $variant['value'] ) ) {
												?>
												<div class="variants-wrap">
													<span class="label"><?php echo esc_html( $variant['name'] ) . ':'; ?></span>
													<span class="description"><?php echo esc_html( $variant['value'] ); ?></span>
												</div>
											<?php } ?>
											<?php
										}
										?>
									</sc-table-cell>
								<?php } ?>
								<sc-table-cell
									style="text-align: left;"><?php echo esc_html($delivery_date); //phpcs:ignore ?></sc-table-cell>
							</sc-table-row>
						</sc-table>
					</div>
					<?php

					do_action( 'surelywp_sv_customer_view_after_product_details', $service, $service_data );

					if ( ! empty( $ask_for_contract ) && 'waiting_for_contract' === $service_status ) {

						// Service Contract form.
						require SURELYWP_SERVICES_TEMPLATE_PATH . '/customer-services/contract-form.php';

					} else {
						?>
						<?php if ( ( 'waiting_for_req' === $service_status || 'waiting_for_contract' === $service_status ) && '1' === $ask_for_requirements && ! empty( $req_fields ) && empty( $submitted_requirements ) ) { ?>
							<div class="note">
								<sc-alert open type="primary"
									title="<?php esc_html_e( 'Please Submit The Requirements', 'surelywp-services' ); ?>">
									<?php esc_html_e( 'We need specific information to begin processing your order. Please review the requirements and provide the necessary details as soon as possible to ensure timely delivery. Once you\'ve submitted the required information, we will start working on your order.', 'surelywp-services' ); ?>
								</sc-alert>
							</div>
						<?php } ?>

						<?php do_action( 'surelywp_sv_customer_view_before_tabs', $service, $service_data ); ?>

						<!-- Tabs lists -->
						<?php require SURELYWP_SERVICES_TEMPLATE_PATH . '/tabs/tabs-lists.php'; ?>

						<?php do_action( 'surelywp_sv_customer_view_after_tabs', $service, $service_data ); ?>

						<!-- Service Messages tab  -->
						<div class="services-chats-wrap service-tab <?php echo 'messages' === $service_tab ? '' : 'hidden'; ?>">
							<div class="heading" no-padding>
								<sc-stacked-list>
									<sc-stacked-list-row><?php esc_html_e( 'Messages', 'surelywp-services' ); ?></sc-stacked-list-row>
								</sc-stacked-list>
							</div>
							<div class="services-message-card tab-card" no-padding>
								<div class="surelywp-sv-messages customer" id="surelywp-sv-messages">
									<?php wp_nonce_field( 'surelywp_sv_message_form_action', 'surelywp_sv_message_form_submit_nonce' ); ?>
									<input type="hidden" id="surelywp-sv-service-id" name="service_id"
										value="<?php echo $surelywp_model->surelywp_escape_attr($service_id); //phpcs:ignore  ?>">
									<?php
									$service_messages = $surelywp_sv_model->surelywp_sv_get_service_messages( $service_id );
									if ( ! empty( $service_messages ) ) {
										$service_messages = array_reverse( $service_messages );
										foreach ( $service_messages as $message ) {

											$current_user_id        = get_current_user_id();
											$message_id             = $message->message_id ?? '';
											$sender_id              = $message->sender_id ?? '';
											$receiver_id            = $message->receiver_id ?? '';
											$message_text           = $message->message_text ?? '';
											$file_names             = $message->attachment_file_name ?? '';
											$is_final_delivery      = $message->is_final_delivery ?? '';
											$is_approved_delivery   = $message->is_approved_delivery ?? '';
											$message_service_status = $message->service_status ?? '';
											$message_milestone_id   = $message->milestone_id ?? '';

											$message_time          = isset( $message->created_at ) && ! empty( $message->created_at ) ? wp_date( 'M d, h:i A', strtotime( $message->created_at ) ) : '';
											$complete_message_time = isset( $message->created_at ) && ! empty( $message->created_at ) ? wp_date( 'Y-m-d H:i:s', strtotime( $message->created_at ) ) : '';
											$sender                = get_userdata( $sender_id );
											$message_class         = $current_user_id === (int) $sender_id ? 'right' : 'left';

											if ( ! empty( $sender ) && ! empty( $message_text ) ) {

												$sender_username = $sender->display_name ?? '';
												$sender_img      = get_avatar_url( $sender->user_email );
												?>
												<?php require SURELYWP_SERVICES_TEMPLATE_PATH . '/customer-services/service-message.php'; ?>
												<?php
											}
										}
									} elseif ( 'service_complete' === $service_status ) {
										$current_user_id  = get_current_user_id();
										$access_users_ids = Surelywp_Services::get_sv_access_users_ids();
										if ( ! empty( $access_users_ids ) && in_array( $current_user_id, (array) $access_users_ids ) ) {
											?>
											<p class="no-found"><?php echo esc_html__( 'There are no messages.', 'surelywp-services' ); ?></p>
										<?php } else { ?>
											<p class="no-found"><?php echo esc_html__( 'There are no messages.', 'surelywp-services' ); ?></p>
										<?php } ?>
									<?php } ?>
								</div>
											
								<?php if ( 'service_submit' !== $service_status && 'milestone_submit' !== $service_status_type ) { ?>
									
									<div class="surelywp-sv-message-form-wrap">
										<sc-form class="surelywp-sv-message-form" id="surelywp-sv-message-form">
											<?php
											$receiver_id = $super_admin_id; // set admin as receiver.
											?>
											<input type="hidden" id="message-receiver-id" name="receiver_id"
												value="<?php echo esc_attr( $surelywp_model->surelywp_escape_attr( $receiver_id ) ); ?>">
											<div class="chat-inputs-wrap">
												<?php
												// Add the TinyMCE editor script.
												wp_editor(
													'', // Initial content, you can fetch saved content here.
													'service-message-input', // Editor ID, must be unique.
													array(
														'textarea_name' => 'service_message', // Name attribute of the textarea.
														'editor_class' => 'service-message-input md-15',
														'textarea_rows' => 4, // Number of rows.
														'media_buttons' => false, // Show media button in the editor.
														'tinymce' => array(
															'toolbar1' => 'bold,italic,underline,|,bullist,numlist,|,link,|,undo,redo',
															'toolbar2' => '', // Leave empty if you don't want a second toolbar.
															'content_style' => 'body, p, div { font-family: Open Sans, sans-serif; color: #4c5866;}', // Properly escape font-family.
														),
														'quicktags' => array(
															'buttons' => 'strong,em,link,ul,ol,li,quote',
														),
													)
												);
												// file upload max size.
												$file_size = Surelywp_Services::get_sv_gen_option( 'file_size' );

												if ( empty( $file_size ) ) {
													$file_size = '5';
												}
												$is_enable_allow_file_uploads = Surelywp_Services::get_sv_gen_option( 'is_enable_allow_file_uploads' );
												if ( ! empty( $is_enable_allow_file_uploads ) ) {
													?>
													<div class="attachment-file">
														<input type="file" class="messages-filepond" name="msg_attachment_file[]"
															id="msg-attachment-file" multiple
															data-max-file-size="<?php echo esc_attr( $file_size . 'MB' ); ?>" data-max-files="20">
													</div>
													<?php
												}
												?>
												<sc-button id="surelywp-sv-send-message-btn" type="primary"
													submit="true"><?php esc_html_e( 'Send', 'surelywp-services' ); ?></sc-button>
											</div>
										</sc-form>
									</div>
									<?php
								} elseif ( 'service_submit' === $service_status ) {
									$final_delivery_msg = $surelywp_sv_model->surelywp_sv_get_final_delivery_message( $service_id );
									if ( ! empty( $final_delivery_msg ) ) {
										$message_id      = $final_delivery_msg[0]->message_id ?? '';
										$final_msg_time  = isset( $final_delivery_msg[0]->created_at ) && ! empty( $final_delivery_msg[0]->created_at ) ? wp_date( 'M d, h:i A', strtotime( $final_delivery_msg[0]->created_at ) ) : '';
										$sender_id       = $final_delivery_msg[0]->sender_id ?? '';
										$sender          = get_userdata( $sender_id );
										$sender_username = $sender->display_name ?? '';
										?>
										<div class="dellvery-approve">
											<div class="dellvery-block">
												<div class="dellvery-content">
													<div class="dellvery-top">
														<label>
															<?php
															// translators: Placeholder 1 is the sender username.
															printf( esc_html__( 'You received the final delivery from %1$s.', 'surelywp-services' ), esc_html( $sender_username ) );
															?>
														</label>
														<div class="date"><?php echo esc_html( $final_msg_time ); ?></div>
														<img class="hidden loader"
															src="<?php echo esc_url( SURELYWP_CORE_PLUGIN_URL ) . '/assets/images/wp-ajax-loader.gif'; ?>" />
													</div>
													<p><?php esc_html_e( 'Are you pleased with the delivery and ready to approve It?', 'surelywp-services' ); ?>
													</p>
												</div>
											</div>
											<div class="dellvery-button">
												<?php wp_nonce_field( 'surelywp_sv_handle_delivery_action', 'surelywp_sv_handle_delivery_nonce' ); ?>
												<input type="hidden" name="service_id" id="service-id"
													value="<?php echo esc_attr( $service_id ); ?>">
												<input type="hidden" name="approve_message_id" id="approve-message-id"
													value="<?php echo esc_attr( $message_id ); ?>">
												<a href="javascript:void(0)" id="delivery-approve-button"
													class="btn-primary button-1"><?php esc_html_e( 'Yes, I Approve The Final Delivery', 'surelywp-services' ); ?></a>
												<a href="javascript:void(0)" id="delivery-revision-button"
													class="btn-secondary button-2"><?php esc_html_e( 'I Need Some Revisions', 'surelywp-services' ); ?></a>

											</div>
											<!-- final delivery approve modal -->
											<div class="surelywp-sv-modal">
												<div class="final-delivery-approve modal">
													<div class="modal-content">
														<span class="close-button">×</span>
														<div class="modal-top">
															<div class="heading">
																<?php echo esc_html__( 'Approve Final Delivery', 'surelywp-services' ); ?><img
																	class="hidden loader"
																	src="<?php echo esc_url( SURELYWP_CORE_PLUGIN_URL ) . '/assets/images/wp-ajax-loader.gif'; ?>" />
															</div>
														</div>
														<div class="modal-bottom">
															<p><?php echo esc_html__( 'Got everything you need? Great! Once you approve this delivery, your order will be marked as complete.', 'surelywp-services' ); ?>
															</p>
															<div class="approve-buttons">
																<a href="javascript:void(0)" id="cancel-delivery-change"
																	class="btn-primary button-2"><?php esc_html_e( 'Cancel', 'surelywp-services' ); ?></a>
																<a href="javascript:void(0)" id="confirm-approve-delivery"
																	class="btn-secondary button-1"><?php esc_html_e( 'Approve Final Delivery', 'surelywp-services' ); ?></a>
															</div>
														</div>
													</div>
												</div>
											</div>
											<!-- final delivery revision modal -->
											<div class="surelywp-sv-modal">
												<div class="final-delivery-revision modal">
													<div class="modal-content">
														<span class="close-button">×</span>
														<div class="modal-top">
															<div class="heading">
																<?php echo esc_html__( 'Request Revisions', 'surelywp-services' ); ?><img
																	class="hidden loader"
																	src="<?php echo esc_url( SURELYWP_CORE_PLUGIN_URL ) . '/assets/images/wp-ajax-loader.gif'; ?>" />
															</div>
														</div>
														<div class="modal-bottom">
															<div class="delivery-revision-wrap">
																<sc-form class="surelywp-sv-delivery-revision-form">
																	<div class="chat-inputs">
																		<?php
																		// Add the TinyMCE editor script.
																		wp_editor(
																			'', // Initial content, you can fetch saved content here.
																			'delivery-revision-msg-text', // Editor ID, must be unique.
																			array(
																				'textarea_name' => 'service_message', // Name attribute of the textarea.
																				'editor_class' => 'service-message-input md-15',
																				'textarea_rows' => 4, // Number of rows.
																				'media_buttons' => false, // Show media button in the editor.
																				'tinymce' => array(
																					'toolbar1' => 'bold,italic,underline,|,bullist,numlist,|,link,|,undo,redo',
																					'toolbar2' => '', // Leave empty if you don't want a second toolbar.
																					'content_style' => 'body, p, div { font-family: Open Sans, sans-serif; color: #4c5866;}', // Properly escape font-family.
																				),
																				'quicktags' => array(
																					'buttons' => 'strong,em,link,ul,ol,li,quote',
																				),
																			)
																		);
																		?>
																		<div class="attachment-file">
																			<?php
																			// file upload max size.
																			$file_size = Surelywp_Services::get_sv_gen_option( 'file_size' );

																			if ( empty( $file_size ) ) {
																				$file_size = '5';
																			}
																			?>
																			<input type="file" class="revision-msg-filepond"
																				id="delivery-revision-attachment-file"
																				name="msg_attachment_file" multiple
																				data-max-file-size="<?php echo esc_attr( $file_size . 'MB' ); ?>"
																				data-max-files="20">
																		</div>
																	</div>
																</sc-form>
															</div>
														</div>
														<div class="approve-buttons">
															<a href="javascript:void(0)" id="cancel-delivery-revision"
																class="btn-primary button-2"><?php esc_html_e( 'Cancel', 'surelywp-services' ); ?></a>
															
																<a href="javascript:void(0)" id="confirm-delivery-revision"
																	class="btn-secondary button-1"><?php esc_html_e( 'Request Revisions', 'surelywp-services' ); ?></a>
														</div>
													</div>
												</div>
											</div>
										</div>
										<?php
									}
								} elseif ( 'milestone_submit' === $service_status_type ) {
									$final_delivery_msg = $surelywp_sv_model->surelywp_sv_get_final_delivery_message( $service_id );
									if ( ! empty( $final_delivery_msg ) ) {

										$message_id      = $final_delivery_msg[0]->message_id ?? '';
										$final_msg_time  = isset( $final_delivery_msg[0]->created_at ) && ! empty( $final_delivery_msg[0]->created_at ) ? wp_date( 'M d, h:i A', strtotime( $final_delivery_msg[0]->created_at ) ) : '';
										$sender_id       = $final_delivery_msg[0]->sender_id ?? '';
										$sender          = get_userdata( $sender_id );
										$sender_username = $sender->display_name ?? '';
										$milestone_label = $milestone_data['milestones_field_label'];
										?>
										<div class="dellvery-approve">
											<div class="dellvery-block">
												<div class="dellvery-content">
													<div class="dellvery-top">
														<label>
															<?php
															// translators: Placeholder 1 is the sender username.
															printf( esc_html__( 'You received the milestone delivery from %1$s.', 'surelywp-services' ), esc_html( $sender_username ) );
															?>
														</label>
														<div class="date"><?php echo esc_html( $final_msg_time ); ?></div>
														<img class="hidden loader"
															src="<?php echo esc_url( SURELYWP_CORE_PLUGIN_URL ) . '/assets/images/wp-ajax-loader.gif'; ?>" />
													</div>
													<p><?php esc_html_e( 'Are you pleased with the delivery and ready to approve It?', 'surelywp-services' ); ?>
													</p>
												</div>
											</div>
											<div class="dellvery-button">
												<?php wp_nonce_field( 'surelywp_sv_handle_delivery_action', 'surelywp_sv_handle_delivery_nonce' ); ?>
												<input type="hidden" name="service_id" id="service-id"
													value="<?php echo esc_attr( $service_id ); ?>">
												<input type="hidden" name="service_setting_id" id="service-setting-id"
													value="<?php echo esc_attr( $service_setting_id ); ?>">
												<input type="hidden" name="milestone_id" id="milestone-id"
													value="<?php echo esc_attr( $milestone_id ); ?>">
												<input type="hidden" name="approve_message_id" id="approve-message-id"
													value="<?php echo esc_attr( $message_id ); ?>">
												<a href="javascript:void(0)" id="delivery-approve-button" class="btn-primary button-1">
													<?php
													// translators: %s is the milestone label.
													printf( esc_html__( 'Yes, I Approve The %s delivery', 'surelywp-services' ), esc_html( $milestone_label ) );
													?>
												</a>
												<a href="javascript:void(0)" id="delivery-revision-button"
													class="btn-secondary button-2"><?php esc_html_e( 'I Need Some Revisions', 'surelywp-services' ); ?></a>

											</div>
											<!-- final delivery approve modal -->
											<div class="surelywp-sv-modal">
												<div class="final-delivery-approve modal">
													<div class="modal-content">
														<span class="close-button">×</span>
														<div class="modal-top">
															<div class="heading">
																<?php printf( esc_html__( 'Approve %s delivery', 'surelywp-services' ), esc_html( $milestone_label ) ); ?>
																<img class="hidden loader"
																	src="<?php echo esc_url( SURELYWP_CORE_PLUGIN_URL ) . '/assets/images/wp-ajax-loader.gif'; ?>" />
															</div>
														</div>
														<div class="modal-bottom">
															<p>
																<?php printf( esc_html__( 'Got everything you need? Great! Once you approve this delivery, your %s will be marked as complete.', 'surelywp-services' ), esc_html( $milestone_label ) ); ?>
															</p>
															<div class="approve-buttons">
																<a href="javascript:void(0)" id="cancel-delivery-change"
																	class="btn-primary button-2"><?php esc_html_e( 'Cancel', 'surelywp-services' ); ?></a>
																<a href="javascript:void(0)" id="confirm-approve-delivery"
																	class="btn-secondary button-1"><?php printf( esc_html__( 'Approve %s delivery', 'surelywp-services' ), esc_html( $milestone_label ) ); ?></a>
															</div>
														</div>
													</div>
												</div>
											</div>
											<!-- final delivery revision modal -->
											<div class="surelywp-sv-modal">
												<div class="final-delivery-revision modal">
													<div class="modal-content">
														<span class="close-button">×</span>
														<div class="modal-top">
															<div class="heading">
																<?php echo esc_html__( 'Request Revisions', 'surelywp-services' ); ?><img
																	class="hidden loader"
																	src="<?php echo esc_url( SURELYWP_CORE_PLUGIN_URL ) . '/assets/images/wp-ajax-loader.gif'; ?>" />
															</div>
														</div>
														<div class="modal-bottom">
															<div class="delivery-revision-wrap">
																<sc-form class="surelywp-sv-delivery-revision-form">
																	<div class="chat-inputs">
																		<?php
																		// Add the TinyMCE editor script.
																		wp_editor(
																			'', // Initial content, you can fetch saved content here.
																			'delivery-revision-msg-text', // Editor ID, must be unique.
																			array(
																				'textarea_name' => 'service_message', // Name attribute of the textarea.
																				'editor_class' => 'service-message-input md-15',
																				'textarea_rows' => 4, // Number of rows.
																				'media_buttons' => false, // Show media button in the editor.
																				'tinymce' => array(
																					'toolbar1' => 'bold,italic,underline,|,bullist,numlist,|,link,|,undo,redo',
																					'toolbar2' => '', // Leave empty if you don't want a second toolbar.
																					'content_style' => 'body, p, div { font-family: Open Sans, sans-serif; color: #4c5866;}', // Properly escape font-family.
																				),
																				'quicktags' => array(
																					'buttons' => 'strong,em,link,ul,ol,li,quote',
																				),
																			)
																		);
																		?>
																		<div class="attachment-file">
																			<?php
																			// file upload max size.
																			$file_size = Surelywp_Services::get_sv_gen_option( 'file_size' );

																			if ( empty( $file_size ) ) {
																				$file_size = '5';
																			}
																			?>
																			<input type="file" class="revision-msg-filepond"
																				id="delivery-revision-attachment-file"
																				name="msg_attachment_file" multiple
																				data-max-file-size="<?php echo esc_attr( $file_size . 'MB' ); ?>"
																				data-max-files="20">
																		</div>
																	</div>
																</sc-form>
															</div>
														</div>
														<div class="approve-buttons sv-ml-revision">
															<a href="javascript:void(0)" id="cancel-delivery-revision"
																class="btn-primary button-2"><?php esc_html_e( 'Cancel', 'surelywp-services' ); ?></a>
															<?php
															if ( $revision_allowed['is_limit_reached'] && isset( $revision_allowed['milestones_revision_allowed'] ) ) {
																echo '<p class="sv-revision-limit-msg">' . esc_html__( 'You Have Reached Limit of the Revision', 'surelywp-services' ) . '</p>';
															} else {
																?>
																<a href="javascript:void(0)" id="confirm-delivery-revision" data-milestone-revision="<?php echo esc_attr( $milestone_label ); ?>"
																	class="btn-secondary button-1"><?php esc_html_e( 'Request Revisions', 'surelywp-services' ); ?></a>
																<?php
															}
															?>
														</div>
													</div>
												</div>
											</div>
										</div>
										<?php
									}
								}
								?>
							</div>
						</div>
						<?php

						// Service Activities Tab.
						require SURELYWP_SERVICES_TEMPLATE_PATH . '/tabs/activity-tab.php';

						// Service Contract Tab.
						if ( ! empty( $ask_for_contract ) || ! empty( $is_have_contract ) ) {
							require SURELYWP_SERVICES_TEMPLATE_PATH . '/tabs/contract-tab.php';
						}

						// Service Requirements Tab.
						if ( ! empty( $ask_for_requirements ) || ! empty( $is_have_requirement ) ) {
							require SURELYWP_SERVICES_TEMPLATE_PATH . '/tabs/requirement-tab.php';
						}

						// Service Delivery Tab.
						require SURELYWP_SERVICES_TEMPLATE_PATH . '/tabs/delivery-tab.php';

						do_action( 'surelywp_sv_customer_view_after_tab_content', $service, $service_data );
					}
				}
				?>
			</div>
			<div class="service-track view-right">
				<?php

				do_action( 'surelywp_sv_customer_view_before_status_tracker_panel', $service, $service_data );

				// status tracker panel.
				require SURELYWP_SERVICES_TEMPLATE_PATH . '/panels/status-tracker-panel.php';

				do_action( 'surelywp_sv_customer_view_after_status_tracker_panel', $service, $service_data );

				// order details panel.
				require SURELYWP_SERVICES_TEMPLATE_PATH . '/panels/order-detail-panel.php';

				do_action( 'surelywp_sv_customer_view_after_order_details_panel', $service, $service_data );

				require SURELYWP_SERVICES_TEMPLATE_PATH . '/panels/plan-detail-panel.php';

				do_action( 'surelywp_sv_customer_view_after_plan_details_panel', $service, $service_data );
				?>
			</div>
		</div>
	<?php }
} ?>