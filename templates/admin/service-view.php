<?php

/**
 * View admin Services template
 *
 * @author  Surelywp
 * @package Services For SureCart
 * @since 1.0.0
 */

use SureCart\Models\Order;
use SureCart\Models\Customer;

// Exit if accessed directly.
if ( ! defined( 'SURELYWP_SERVICES' ) ) {
	exit;
}


$service_singular_name = Surelywp_Services::get_sv_singular_name();
$service_plural_name   = Surelywp_Services::get_sv_plural_name();
?>
<div class="surelywp-surecart-admin css-1ssgtuh">
	<div class="surelywp-surecart-header">
		<div class="css-152nnxu">
			<div class="css-t9ogfm">
				<div class="css-18zr9hy">
					<h1 class="css-1cvf1y6">
						<div class="css-6zq5xv"><sc-button circle="" size="small"
								href="<?php echo esc_url( admin_url( 'admin.php' ) . '?page=sc-services' ); ?>"
								type="default" class="hydrated"><sc-icon name="arrow-left"
									class="hydrated"></sc-icon></sc-button><sc-breadcrumbs
								class="hydrated"><sc-breadcrumb class="hydrated"><svg viewBox="0 0 174 32" fill="none"
										xmlns="http://www.w3.org/2000/svg" width="125" style="display: block;">
										<path fill-rule="evenodd" clip-rule="evenodd"
											d="M40.2246 22.5298L41.6057 20.5358C42.2395 21.2544 43.0826 21.8591 44.1348 22.3501C45.1871 22.8411 46.2932 23.0866 47.4532 23.0866C48.7805 23.0866 49.8149 22.8052 50.5562 22.2423C51.3096 21.6795 51.6863 20.943 51.6863 20.0328C51.6863 19.458 51.483 18.973 51.0764 18.5778C50.6698 18.1826 50.1437 17.8772 49.498 17.6616C48.8522 17.4461 48.1407 17.2485 47.3635 17.0689C46.5862 16.8892 45.803 16.6796 45.0138 16.4401C44.2365 16.2006 43.525 15.9072 42.8793 15.5599C42.2335 15.2126 41.7074 14.7216 41.3008 14.0869C40.8942 13.4402 40.691 12.6738 40.691 11.7876C40.691 10.3265 41.2769 9.11101 42.4488 8.14098C43.6207 7.15897 45.229 6.66797 47.2738 6.66797C49.9763 6.66797 52.1646 7.53022 53.8387 9.25472L52.5652 11.1589C52.0151 10.4882 51.2737 9.94931 50.341 9.54213C49.4202 9.12299 48.3978 8.91341 47.2738 8.91341C46.09 8.91341 45.1393 9.18286 44.4218 9.72177C43.7044 10.2487 43.3456 10.9133 43.3456 11.7157C43.3456 12.2307 43.5489 12.6678 43.9555 13.0271C44.362 13.3744 44.8822 13.6498 45.516 13.8534C46.1617 14.045 46.8732 14.2306 47.6505 14.4103C48.4397 14.5779 49.2229 14.7875 50.0002 15.039C50.7894 15.2905 51.5009 15.6018 52.1347 15.9731C52.7804 16.3323 53.3066 16.8533 53.7131 17.5359C54.1197 18.2185 54.323 19.0269 54.323 19.961C54.323 21.5178 53.7131 22.8052 52.4934 23.8231C51.2857 24.8291 49.5817 25.3321 47.3814 25.3321C44.4039 25.3321 42.0183 24.398 40.2246 22.5298ZM57.3378 19.6556V7.11706H60.0822V18.8113C60.0822 20.2963 60.423 21.3442 61.1046 21.9549C61.7862 22.5537 62.7787 22.8531 64.0821 22.8531C65.1105 22.8531 66.103 22.5956 67.0596 22.0807C68.0162 21.5657 68.7636 20.931 69.3017 20.1765V7.11706H72.064V24.883H69.3017V22.3861C68.5603 23.2244 67.6336 23.9249 66.5215 24.4878C65.4094 25.0506 64.2316 25.3321 62.9879 25.3321C59.2212 25.3321 57.3378 23.4399 57.3378 19.6556ZM76.1012 24.883H78.8456V12.3085C79.2521 11.578 79.9517 10.9253 80.9442 10.3505C81.9367 9.76368 82.8575 9.47028 83.7065 9.47028C83.9815 9.47028 84.3343 9.49423 84.7647 9.54213V6.7039C83.6168 6.7039 82.5346 7.00329 81.5182 7.60207C80.5017 8.18888 79.6109 8.97329 78.8456 9.9553V7.11706H76.1012V24.883ZM85.7168 15.9731C85.7168 14.2725 86.0875 12.7157 86.8289 11.3026C87.5823 9.87745 88.6286 8.75174 89.9679 7.92542C91.3072 7.08712 92.8079 6.66797 94.47 6.66797C96.2159 6.66797 97.7405 7.09311 99.0439 7.94338C100.347 8.79365 101.328 9.93733 101.986 11.3744C102.655 12.7995 102.99 14.4102 102.99 16.2066V16.9072H88.6585C88.7661 18.6556 89.3879 20.1167 90.5239 21.2903C91.6719 22.4639 93.1666 23.0507 95.0081 23.0507C96.0485 23.0507 97.047 22.8531 98.0036 22.4579C98.9722 22.0627 99.8152 21.4999 100.533 20.7693L101.842 22.5657C99.9647 24.4099 97.615 25.3321 94.7929 25.3321C92.1382 25.3321 89.9619 24.4638 88.2639 22.7274C86.5658 20.9909 85.7168 18.7395 85.7168 15.9731ZM88.6226 14.8414H100.264C100.252 14.1588 100.126 13.4821 99.887 12.8115C99.6598 12.1409 99.319 11.5121 98.8646 10.9253C98.4102 10.3385 97.8003 9.86548 97.035 9.50621C96.2697 9.13496 95.4027 8.94934 94.4341 8.94934C93.5253 8.94934 92.7003 9.12897 91.9589 9.48824C91.2175 9.84751 90.6136 10.3206 90.1472 10.9074C89.6928 11.4822 89.3341 12.1109 89.071 12.7935C88.8079 13.4642 88.6585 14.1468 88.6226 14.8414ZM123.25 20.896C121.985 23.1411 119.696 25.3321 115.766 25.3321C110.354 25.3321 106.182 21.5452 106.182 16C106.182 10.4278 110.354 6.66797 115.766 6.66797C119.696 6.66797 121.985 8.80487 123.25 11.077L119.965 12.7C119.212 11.2393 117.596 10.0762 115.766 10.0762C112.481 10.0762 110.112 12.5918 110.112 16C110.112 19.4082 112.481 21.9238 115.766 21.9238C117.596 21.9238 119.212 20.7607 119.965 19.3001L123.25 20.896ZM135.168 6.96551L142.087 25.0075H137.752L136.622 21.9509H128.922L127.791 25.0075H123.457L130.376 6.96551H135.168ZM129.972 18.5697H135.572L132.772 10.8065L129.972 18.5697ZM158.31 25.0075L154.245 18.1369C156.183 17.6771 158.202 15.9459 158.202 12.7811C158.202 9.42701 155.887 6.96551 152.145 6.96551H143.745V25.0075H147.568V18.5968H150.368L153.895 25.0075H158.31ZM151.606 15.2156H147.568V10.3467H151.606C153.114 10.3467 154.271 11.2664 154.271 12.7541C154.271 14.2959 153.114 15.2156 151.606 15.2156ZM168.777 10.3467V25.0075H164.927V10.3467H159.678V6.96551H174V10.3467H168.777Z"
											fill="#002E33"></path>
										<path fill-rule="evenodd" clip-rule="evenodd"
											d="M15.9573 31.9978C24.7703 31.9978 31.9146 24.8353 31.9146 16C31.9146 7.16466 24.7703 0.00219727 15.9573 0.00219727C7.14433 0.00219727 0 7.16466 0 16C0 24.8353 7.14433 31.9978 15.9573 31.9978ZM16.026 8.0011C14.7447 8.0011 12.9716 8.73571 12.0655 9.6419L9.60482 12.1031H21.8701L25.9713 8.0011H16.026ZM19.8284 22.3581C18.9224 23.2643 17.1492 23.9989 15.8679 23.9989H5.92266L10.0239 19.8969H22.2891L19.8284 22.3581ZM23.8147 14.1541H7.55865L6.79078 14.9232C4.97257 16.564 5.51182 17.8459 8.05815 17.8459H24.3582L25.1263 17.0768C26.9269 15.4456 26.361 14.1541 23.8147 14.1541Z"
											fill="#01824C"></path>
									</svg></sc-breadcrumb><sc-breadcrumb
									href="<?php echo esc_url( admin_url( 'admin.php' ) . '?page=sc-services' ); ?>"
									class="hydrated"><?php echo esc_html( $service_plural_name ); ?></sc-breadcrumb><sc-breadcrumb
									class="hydrated">
									<?php
									// translators: %s is the singular name of the service.
									printf( esc_html__( 'View %s', 'surelywp-services' ), esc_html( $service_singular_name ) );
									?>
								</sc-breadcrumb></sc-breadcrumbs></div>
					</h1>
				</div>
				<?php
				$make_complete_url  = add_query_arg(
					array(
						'page'       => 'sc-services',
						'action'     => 'view',
						'service_id' => $service_id,
						'status'     => 'service_complete',
					),
					admin_url( 'admin.php' )
				);
				$cancel_service_url = add_query_arg(
					array(
						'page'       => 'sc-services',
						'action'     => 'view',
						'service_id' => $service_id,
						'status'     => 'service_canceled',
					),
					admin_url( 'admin.php' )
				);
				$start_service_url  = add_query_arg(
					array(
						'page'       => 'sc-services',
						'action'     => 'view',
						'service_id' => $service_id,
						'status'     => 'service_start',
					),
					admin_url( 'admin.php' )
				);
				?>
				<sc-dropdown position="bottom-right" placement="bottom-start" close-on-select=""
					style="--panel-width: 14em;" class="hydrated">
					<sc-button id="service-action-btn" type="primary" slot="trigger" caret="" loading="false"
						size="medium" class="hydrated">
						<?php echo esc_html__( 'Actions', 'surelywp-services' ); ?>
					</sc-button>
					<sc-menu class="hydrated">
						<sc-menu-item href="javascript:void(0)" value="<?php echo esc_attr( 'service_start' ); ?>"
							class="hydrated service-start-action hidden sv-action-menu-item">
							<?php
							// translators: %s is the singular name of the service.
							printf( esc_html__( 'Start %s', 'surelywp-services' ), esc_html( $service_singular_name ) );
							?>
						</sc-menu-item>
						<sc-menu-item href="javascript:void(0)" value="<?php echo esc_attr( 'service_complete' ); ?>"
							class="hydrated service-complete-action sv-action-menu-item">
							<?php
							// translators: %s is the singular name of the service.
							printf( esc_html__( 'Complete %s', 'surelywp-services' ), esc_html( $service_singular_name ) );
							?>
						</sc-menu-item>
						<sc-menu-item href="javascript:void(0)" value="<?php echo esc_attr( 'service_canceled' ); ?>"
							class="hydrated service-canceled-action sv-action-menu-item">
							<?php
							// translators: %s is the singular name of the service.
							printf( esc_html__( 'Cancel %s', 'surelywp-services' ), esc_html( $service_singular_name ) );
							?>
						</sc-menu-item>
						<?php
						$delete_url = wp_nonce_url(
							admin_url( sprintf( 'admin.php?page=sc-services&sv_action=delete&service_delete=%d', $service_id ) ),
							'delete_service_' . $service_id
						);
						?>
						<sc-menu-item href="<?php echo esc_url( $delete_url ); ?>"
							value="<?php echo esc_attr( 'service_delete' ); ?>"
							class="hydrated service-delete-action hidden surelywp-sv-delete-service sv-action-menu-item">
							<?php
							// translators: %s is the singular name of the service.
							printf( esc_html__( 'Delete %s', 'surelywp-services' ), esc_html( $service_singular_name ) );
							?>
						</sc-menu-item>
					</sc-menu>
				</sc-dropdown>
			</div>
		</div>
	</div>
	<!-- Delivery now modal -->
	<div class="surelywp-sv-modal">
		<div class="confirm-service-update modal">
			<div class="modal-content">
				<span class="close-button">×</span>
				<div class="modal-top">
					<div class="heading"><?php echo esc_html__( 'Confirm', 'surelywp-services' ); ?><img
							class="hidden loader"
							src="<?php echo esc_url( SURELYWP_CORE_PLUGIN_URL ) . '/assets/images/wp-ajax-loader.gif'; ?>" />
					</div>
				</div>
				<div class="modal-bottom">
					<div class="delivery-now-wrap">
						<p class="confirm-service-complete hidden">
							<?php
							// translators: %s is the singular name of the service.
							printf( esc_html__( 'Are you sure you wish to complete the %s?', 'surelywp-services' ), esc_html( $service_singular_name ) );
							?>
						</p>
						<p class="confirm-service-cancel hidden">
							<?php
							// translators: %s is the singular name of the service.
							printf( esc_html__( 'Are you sure you wish to cancel the %s?', 'surelywp-services' ), esc_html( $service_singular_name ) );
							?>
						</p>
						<p class="confirm-service-start hidden">
							<?php
							// translators: %s is the singular name of the service.
							printf( esc_html__( 'Are you sure you wish to start the %s?', 'surelywp-services' ), esc_html( $service_singular_name ) );
							?>
						</p>
					</div>
				</div>
				<div class="approve-buttons">
					<a href="javascript:void(0)" id="cancel-service-update"
						class="btn-primary button-2"><?php esc_html_e( 'Go Back', 'surelywp-services' ); ?></a>
					<a href="<?php echo esc_url( $make_complete_url ); ?>" id="confirm-service-complete"
						class="confirm-service-complete hidden btn-secondary button-1"><?php esc_html_e( 'Complete Service', 'surelywp-services' ); ?></a>
					<a href="<?php echo esc_url( $cancel_service_url ); ?>" id="confirm-service-cancel"
						class="confirm-service-cancel hidden btn-secondary button-1"><?php esc_html_e( 'Cancel Service', 'surelywp-services' ); ?></a>
					<a href="<?php echo esc_url( $start_service_url ); ?>" id="confirm-service-start"
						class="confirm-service-start hidden btn-secondary button-1"><?php esc_html_e( 'Start Service', 'surelywp-services' ); ?></a>
				</div>
			</div>
		</div>
	</div>
	<?php
	global $surelywp_sv_model, $surelywp_model;
	$services_obj            = Surelywp_Services();
	$service_action          = isset( $_GET['action'] ) && ! empty( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
	$service_id              = isset( $_GET['service_id'] ) && ! empty( $_GET['service_id'] ) ? sanitize_text_field( wp_unslash( $_GET['service_id'] ) ) : '';
	$service_tab             = isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'messages';
	$service_status          = '';
	$order_id                = '';
	$delivery_date           = 'NA';
	$receiver_id             = 0;
	$ask_for_contract        = '';
	$ask_for_requirements    = '';
	$is_have_contract        = '';
	$is_have_requirement     = '';
	$order_date              = '';
	$customer                = array();
	$product_variant_options = array();

	// service id not get and action is services.
	if ( ! empty( $service_id ) && ! empty( $service_action ) && 'view' === $service_action ) {

		$service = $surelywp_sv_model->surelywp_sv_get_service( $service_id );
		do_action( 'surelywp_sv_before_admin_view', $service_id );

		?>
		<div class="surelywp-sv-serview-view admin">
			<?php if ( $service ) { ?>

				<?php do_action( 'surelywp_sv_admin_view_top', $service ); ?>

				<div class="view-left">
					<?php
					$service_setting_id      = $service[0]->service_setting_id ?? '';
					$recurring_service_id    = $service[0]->recurring_service_id ?? '';
					$services_remaining      = $service[0]->services_remaining ?? 0;
					$order_id                = $service[0]->order_id ?? '';
					$product_id              = $service[0]->product_id ?? '';
					$receiver_id             = $service[0]->user_id ?? '';
					$service_status          = $service[0]->service_status ?? '';
					$delivery_date           = $service[0]->delivery_date ?? '';
					$service_updated_at      = $service[0]->updated_at ?? '';
					$service_data            = $services_obj->surelywp_sv_get_service_data( $order_id, $product_id );
					$price                   = $services_obj->surelywp_sv_get_product_paid_price( $order_id, $product_id );
					$service_status_text     = $services_obj->surelywp_sv_get_service_status( $service_status, $product_id, $service_id );
					$order_date              = $service_data['order_date'];
					$order_mode              = $service_data['order_mode'];
					$order_view_url          = \SureCart::getUrl()->edit( 'order', $order_id );
					$paid_amount             = $price['amount'] ?? '';
					$currency_type           = $price['currency'];
					$price_id                = $price['id'] ?? '';
					$variant_id              = $price['variant_id'];
					$product_variant_options = $price['product_variant_options'] ?? '';
					$delivery_date_from_db   = '';
					if ( ! empty( $delivery_date ) ) {
						$delivery_date_from_db = $delivery_date;
						$date_format           = get_option( 'date_format', 'j F Y' );
						$delivery_date         = wp_date( $date_format, strtotime( $delivery_date ) );
					} else {
						$delivery_date = 'NA';
					}
					// Delivery Date Short.
					if ( ! empty( $delivery_date ) ) {
						// Force short month format (e.g., Oct 16, 2025)
						$date_format         = 'M j, Y';
						$delivery_date_short = wp_date( $date_format, strtotime( $delivery_date ) );
					} else {
						$delivery_date_short = 'NA';
					}

					// For Remaining Revision.
					$milestones_fields   = Surelywp_Services::get_sv_option( $service_setting_id, 'milestones_fields' );
					$service_status_type = preg_replace( '/_\d+$/', '', $service_status );
					$milestone_data      = Surelywp_Services::surelywp_sv_get_milestone_details( $service_status, $milestones_fields );
					$milestone_id        = $milestone_data['id'] ?? '';
					$service_status_type = preg_replace( '/_\d+$/', '', $service_status );

					if ( $service_status_type == 'milestone_submit' || $service_status_type == 'service_start' ) {

						$revision_allowed = Surelywp_Services::surelywp_sv_number_of_revisions_remaining( $milestone_id, $service_setting_id, $service_id, $milestone_data );
					}
					$ask_for_requirements = Surelywp_Services::get_sv_option( $service_setting_id, 'ask_for_requirements' );
					$ask_for_contract     = Surelywp_Services::get_sv_option( $service_setting_id, 'ask_for_contract' );
					$is_have_contract     = $surelywp_sv_model->surelywp_sv_get_service_contract( $service_id );
					$is_have_requirement  = $surelywp_sv_model->surelywp_sv_get_service_requirements( $service_id );
					$milestones_fields    = Surelywp_Services::get_sv_option( $service_setting_id, 'milestones_fields' );

					// get user customer id.
					$customer_id = '';
					if ( ! empty( $receiver_id ) && ! empty( $order_mode ) ) {

						$user_data = get_userdata( $receiver_id );
						if ( ! empty( $user_data ) ) {

							$customer = Customer::where(
								array(
									'email'     => strtolower( $user_data->user_email ),
									'live_mode' => 'live' === $order_mode ? true : false,
								)
							)->get();

							if ( ! is_wp_error( $customer ) && ! empty( $customer ) ) {

								$customer_id = $customer[0]->id;
							}
						}
					}

					do_action( 'surelywp_sv_admin_view_before_heading', $service, $service_data );
					?>
					<div class="services-item-list-heading">
						<div class="service-number">
							<input type="hidden" id="surelywp-sv-service-id" name="service_id"
								value="<?php echo esc_attr( $surelywp_model->surelywp_escape_attr( $service_id ) ); ?>">
							<input type="hidden" id="surelywp-sv-service-setting-id" name="service_setting_id"
								value="<?php echo esc_attr( $surelywp_model->surelywp_escape_attr( $service_setting_id ) ); ?>">
							<input type="hidden" id="surelywp-sv-service-status" name="current_service_status"
								value="<?php echo esc_attr( $surelywp_model->surelywp_escape_attr( $service_status ) ); ?>">
							<input type="hidden" id="surelywp-sv-customer-id" name="sv_customer_id"
								value="<?php echo esc_attr( $surelywp_model->surelywp_escape_attr( $customer_id ) ); ?>">
							<h1><?php echo esc_html( '#' . $service_id ); ?></h1>
							<?php $tag_type = 'service_complete' === $service_status ? 'success' : ( 'service_canceled' === $service_status ? 'danger' : 'warning' ); ?>
							<div class="service-status-tag">
								<sc-tag type="<?php echo esc_attr( $tag_type ); ?>" size="medium" pill=""
									class="hydrated"><?php echo esc_html( $service_status_text ); ?></sc-tag>
							</div>
						</div>
						<p class="created-at">
							<?php
							$date_format = get_option( 'date_format', 'j F Y' );
							$time_format = get_option( 'time_format', 'H:i' );
							$format      = $date_format . ' \a\t ' . $time_format;
							// translators: %s is the service creation date.
							printf( esc_html__( 'Created on %s', 'surelywp-services' ), wp_date( $format, (int) $service_data['order_date'] ) );
							?>
						</p>
						<input type="hidden" id="surelywp-sv-service-id" name="service_id"
							value="<?php echo esc_attr( $surelywp_model->surelywp_escape_attr( $service_id ) ); ?>">
					</div>
					<?php

					do_action( 'surelywp_sv_admin_view_after_heading', $service, $service_data );

					// after order placed.
					if ( ! empty( $service_data ) ) {

						$requirement_id = $surelywp_sv_model->surelywp_sv_is_service_have_req( $service_id );

						if ( empty( $requirement_id ) && 'NA' !== $delivery_date && ( 'service_start' === $service_status ) ) {
							?>
							<div class="note">
								<sc-alert class="work-start-note" open closable type="primary"
									title="<?php esc_html_e( 'You can start working on the order!', 'surelywp-services' ); ?>">
									<?php esc_html_e( 'The countdown has started, and no additional requirements are needed from the customer.', 'surelywp-services' ); ?>
								</sc-alert>
							</div>
						<?php } elseif ( 'NA' !== $delivery_date && ( 'service_start' === $service_status ) ) { ?>
							<div class="note">
								<sc-alert class="work-start-note" open closable type="primary"
									title="<?php esc_html_e( 'You can start working on the order!', 'surelywp-services' ); ?>">
									<?php esc_html_e( 'The customer has sent the required information, and the countdown has started.', 'surelywp-services' ); ?>
								</sc-alert>
							</div>
						<?php } ?>

						<?php do_action( 'surelywp_sv_admin_view_before_product_details', $service, $service_data ); ?>

						<div class="service-order-detail md-24">
							<sc-table class="hydrated">
								<sc-table-cell slot="head"></sc-table-cell>
								<sc-table-cell slot="head" class="product-name"><sc-text
										class="hydrated"><?php esc_html_e( 'Product', 'surelywp-services' ); ?>
									</sc-text></sc-table-cell>
								<?php if ( ! empty( $product_variant_options ) ) { ?>
									<sc-table-cell slot="head"
										class="variants"><?php esc_html_e( 'Variants', 'surelywp-services' ); ?></sc-table-cell>
								<?php } ?>
								<sc-table-cell slot="head" class="delivery-date"> <sc-text
										class="hydrated"><?php esc_html_e( 'Delivery Date', 'surelywp-services' ); ?>
									</sc-text></sc-table-cell>
								<sc-table-row>
									<sc-table-cell>
										<div class="service-product-image">
											<img src="<?php echo ! empty( $service_data['product_img_url'] ) ? esc_url( $service_data['product_img_url'] ) : esc_url( trailingslashit( \SureCart::core()->assets()->getUrl() ) . 'images/placeholder.jpg' ); ?>"
												alt="service_product_image">
										</div>
									</sc-table-cell>
									<sc-table-cell>
										<sc-text class="hydrated">
											<a class="view-product-link"
												aria-label="<?php echo esc_attr__( 'View Product', 'surelywp-services' ); ?>"
												href="<?php echo esc_url( \SureCart::getUrl()->edit( 'product', $product_id ) ); ?>">
												<?php echo esc_html( $service_data['product_name'] ); ?>
											</a>
										</sc-text>
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
									<sc-table-cell><sc-text
											class="hydrated delivery-time"><?php echo esc_html( $delivery_date ); ?></sc-text></sc-table-cell>
								</sc-table-row>
							</sc-table>
						</div>

						<?php do_action( 'surelywp_sv_admin_view_after_product_details', $service, $service_data ); ?>

						<?php
						if ( ! empty( $ask_for_contract ) && 'waiting_for_contract' === $service_status ) {
							?>
							<div class="wating-for-contract">
								<div class="services-contract-wrap service-tab">
									<div class="heading" no-padding>
										<sc-stacked-list>
											<sc-stacked-list-row><?php printf( esc_html__( 'Contract', 'surelywp-services' ) ); ?></sc-stacked-list-row>
										</sc-stacked-list>
									</div>
									<div class="services-contract-card tab-card" no-padding>
										<div class="surelywp-sv-contract" id="surelywp-sv-contract">
											<p class="no-found">
												<?php echo esc_html__( 'The customer has not yet submitted a contract.', 'surelywp-services' ); ?>
											</p>
										</div>
									</div>
								</div>
							</div>
							<?php
						} else {
							?>

							<?php do_action( 'surelywp_sv_admin_view_before_tabs', $service, $service_data ); ?>

							<!-- Tabs lists -->
							<?php require SURELYWP_SERVICES_TEMPLATE_PATH . '/tabs/tabs-lists.php'; ?>

							<?php do_action( 'surelywp_sv_admin_view_after_tabs', $service, $service_data ); ?>

							<!-- messages tab -->
							<div class="services-chats-wrap service-tab admin">
								<div class="heading" no-padding>
									<sc-stacked-list>
										<sc-stacked-list-row><?php esc_html_e( 'Messages', 'surelywp-services' ); ?></sc-stacked-list-row>
									</sc-stacked-list>
								</div>
								<div class="services-message-card tab-card" no-padding>
									<div class="surelywp-sv-messages admin" id="surelywp-sv-messages">
										<?php wp_nonce_field( 'surelywp_sv_message_form_action', 'surelywp_sv_message_form_submit_nonce' ); ?>
										<?php
										$service_messages = $surelywp_sv_model->surelywp_sv_get_service_messages( $service_id );
										if ( ! empty( $service_messages ) ) {

											$service_messages = array_reverse( $service_messages );
											foreach ( $service_messages as $message ) {
												$current_user_id       = get_current_user_id();
												$message_id            = $message->message_id ?? '';
												$sender_id             = $message->sender_id ?? '';
												$message_text          = $message->message_text ?? '';
												$message_time          = isset( $message->created_at ) && ! empty( $message->created_at ) ? wp_date( 'M d, h:i A', strtotime( $message->created_at ) ) : '';
												$complete_message_time = isset( $message->created_at ) && ! empty( $message->created_at ) ? wp_date( 'Y-m-d H:i:s', strtotime( $message->created_at ) ) : '';
												$sender                = get_userdata( $sender_id );
												$message_class         = $current_user_id === (int) $sender_id ? 'right' : 'left';
												$is_final_delivery     = $message->is_final_delivery ?? '';
												$is_approved_delivery  = $message->is_approved_delivery ?? '';

												$file_names = $message->attachment_file_name ?? '';

												if ( ! empty( $sender ) && ! empty( $message_text ) ) {

													$sender_username = $sender->display_name ?? '';
													$sender_img      = get_avatar_url( $sender->user_email );
													require SURELYWP_SERVICES_TEMPLATE_PATH . '/customer-services/service-message.php';
												}
											}
										} elseif ( 'service_complete' === $service_status ) {
											$current_user_id  = get_current_user_id();
											$access_users_ids = Surelywp_Services::get_sv_access_users_ids();
											if ( ! empty( $access_users_ids ) && in_array( $current_user_id, (array) $access_users_ids ) ) {
												?>
												<p class="no-found"><?php echo esc_html__( 'There are no messages.', 'surelywp-services' ); ?>
												</p>
											<?php } else { ?>
												<p class="no-found"><?php echo esc_html__( 'There are no messages.', 'surelywp-services' ); ?>
												</p>
											<?php } ?>
										<?php } ?>
									</div>
									<div class="surelywp-sv-message-form-wrap">
										<sc-form class="surelywp-sv-message-form" id="surelywp-sv-message-form">
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
												?>
												<div class="attachment-file">
													<?php
													// file upload max size.
													$file_size = Surelywp_Services::get_sv_gen_option( 'file_size' );

													if ( empty( $file_size ) ) {
														$file_size = '5';
													}
													?>
													<input type="file" class="messages-filepond" name="msg_attachment_file[]"
														id="msg-attachment-file" multiple
														data-max-file-size="<?php echo esc_attr( $file_size . 'MB' ); ?>"
														data-max-files="20">
												</div>
												<?php
												if ( 'final_delivery_start' !== $service_status ) {
													if ( ! empty( $milestones_fields ) ) {
														$delivery_milestone_label = '';
														foreach ( $milestones_fields as $key => $milestones_field ) {
															$milestone_key   = 'service_start_' . $key;
															$milestone_label = ! empty( $milestones_field['milestones_field_label'] )
																? $milestones_field['milestones_field_label']
																: esc_html__( 'Milestone', 'surelywp-services' );

															// Match your target milestone key
															if ( $milestone_key === $service_status ) {
																$data_milestone           = 'milestone_submit_' . $key;
																$delivery_milestone_label = sprintf(
																	__( 'Is this the %s delivery?', 'surelywp-services' ),
																	$milestone_label
																);
																break; // stop loop once matched
															}
														}
														?>
														<?php if ( ! empty( $delivery_milestone_label ) ) : ?>
															<div class="is-final-delivery">
																<input type="checkbox" name="is_final_delivery" id="is-final-delivery" value="1"
																	data-milestone="<?php echo esc_html( $data_milestone ); ?>">
																<label for="is-final-delivery">
																	<?php echo esc_html( $delivery_milestone_label ); ?>
																</label>
															</div>
														<?php endif; ?>
														<?php
													}
												} elseif ( 'service_complete' !== $service_status ) {
													?>
													<div class="is-final-delivery">
														<input type="checkbox" name="is_final_delivery" id="is-final-delivery" value="1"
															data-milestone="0">
														<label
															for="is-final-delivery"><?php echo esc_html_e( 'Is this the final delivery?', 'surelywp-services' ); ?></label>
													</div>
												<?php } ?>
												<sc-button id="surelywp-sv-send-message-btn" type="primary"
													submit="true"><?php esc_html_e( 'Send', 'surelywp-services' ); ?></sc-button>
											</div>
										</sc-form>
									</div>
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

							do_action( 'surelywp_sv_admin_view_after_tab_content', $service, $service_data );
						}
					}
					?>
				</div>
				<div class="service-track view-right">
					<?php

					do_action( 'surelywp_sv_admin_view_before_delivery_date_panel', $service, $service_data );

					if ( preg_match( '/^service_start(?:_\d+)?$/', $service_status ) ) {
						// Delivery date panel.
						require SURELYWP_SERVICES_TEMPLATE_PATH . '/panels/delivery-date-panel.php';
					}

					do_action( 'surelywp_sv_admin_view_after_delivery_date_panel', $service, $service_data );

					// status tracker panel.
					require SURELYWP_SERVICES_TEMPLATE_PATH . '/panels/status-tracker-panel.php';

					do_action( 'surelywp_sv_admin_view_after_status_tracker_panel', $service, $service_data );

					// Order Detail panel.
					require SURELYWP_SERVICES_TEMPLATE_PATH . '/panels/order-detail-panel.php';

					do_action( 'surelywp_sv_admin_view_after_order_details_panel', $service, $service_data );

					// Plan details.
					require SURELYWP_SERVICES_TEMPLATE_PATH . '/panels/plan-detail-panel.php';

					do_action( 'surelywp_sv_admin_view_after_plan_details_panel', $service, $service_data );
					?>
				</div>
			<?php } ?>
		</div>
		<?php do_action( 'surelywp_sv_after_admin_view', $service_id ); ?>
	<?php } ?>
</div>