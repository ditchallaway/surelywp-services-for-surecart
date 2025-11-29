<?php
/**
 * Admin servies lists.
 *
 * @author  Surelywp
 * @package Services For SureCart
 * @since 1.0.0
 */

$service_action = isset( $_GET['action'] ) && ! empty( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
$service_id     = isset( $_GET['service_id'] ) && ! empty( $_GET['service_id'] ) ? sanitize_text_field( wp_unslash( $_GET['service_id'] ) ) : '';

if ( empty( $service_action ) && empty( $service_id ) ) {

	// Show notice if deleted.
	if ( isset( $_GET['deleted'] ) && $_GET['deleted'] == 1 ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Service deleted successfully.', 'surelywp-services' ) . '</p></div>';
	}

	// Create an instance.
	$admin_service_list_table = new Surelywp_Services_Admin_List_Table();

	$service_singular_name = Surelywp_Services::get_sv_singular_name();
	$service_plural_name   = Surelywp_Services::get_sv_plural_name();

	// Fetch, prepare, sort, and filter our data...
	$admin_service_list_table->prepare_items();

	// Surecart admin header.
	\SureCart::render(
		'layouts/partials/admin-header',
		array(
			'breadcrumbs' => array(
				'services' => array(
					'title' => esc_html( $service_plural_name ),
				),
			),
		)
	);
	?>
	<div class="wrap">
		<h2>
			<?php esc_html( $service_plural_name ); ?>
		</h2>
		<?php
		$filter_value = isset( $_REQUEST['service_status'] ) && ! empty( $_REQUEST['service_status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['service_status'] ) ) : 'all';
		
		$service_status_type = preg_replace( '/_\d+$/', '', $filter_value );
		?>
		<ul class="subsubsub support-sub-filter">
			<li class="all">
				<a href="?page=<?php echo esc_attr( 'sc-services' ); ?>&service_status=all" class="<?php echo ( 'all' === $filter_value ) ? 'current' : ''; ?>">
					<?php esc_html_e( 'All', 'surelywp-services' ); ?>
				</a> |
			</li>
			<li class="filter-waiting-for-contract">
				<a href="?page=<?php echo esc_attr( 'sc-services' ); ?>&service_status=waiting_for_contract" class="<?php echo ( 'waiting_for_contract' === $filter_value ) ? 'current' : ''; ?>">
					<?php esc_html_e( 'Waiting For Contract', 'surelywp-services' ); ?>
				</a> |
			</li>
			<li class="filter-waiting-for-req">
				<a href="?page=<?php echo esc_attr( 'sc-services' ); ?>&service_status=waiting_for_req" class="<?php echo ( 'waiting_for_req' === $filter_value ) ? 'current' : ''; ?>">
					<?php esc_html_e( 'Waiting For Requirements', 'surelywp-services' ); ?>
				</a> |
			</li>
			<li class="filter-service-start">
				<a href="?page=<?php echo esc_attr( 'sc-services' ); ?>&service_status=service_start" class="<?php echo ( 'service_start' === $filter_value ) ? 'current' : ''; ?>">
					<?php esc_html_e( 'Work In Progress', 'surelywp-services' ); ?>
				</a> |
			</li>
			<li class="filter-service-submit">
				<a href="?page=<?php echo esc_attr( 'sc-services' ); ?>&service_status=service_submit" class="<?php echo ( 'service_submit' === $filter_value || 'milestone_submit' === $service_status_type ) ? 'current' : ''; ?>">
					<?php esc_html_e( 'Waiting For Approval', 'surelywp-services' ); ?>
				</a> |
			</li>
			<li class="filter-service-complete">
				<a href="?page=<?php echo esc_attr( 'sc-services' ); ?>&service_status=service_complete" class="<?php echo ( 'service_complete' === $filter_value ) ? 'current' : ''; ?>">
					<?php esc_html_e( 'Order Complete', 'surelywp-services' ); ?>
				</a> |
			</li>
			<li class="filter-service-canceled">
				<a href="?page=<?php echo esc_attr( 'sc-services' ); ?>&service_status=service_canceled" class="<?php echo ( 'service_canceled' === $filter_value ) ? 'current' : ''; ?>">
					<?php esc_html_e( 'Order Cancelled', 'surelywp-services' ); ?>
				</a>
			</li>
		</ul>
		<form id="posts-filter" method="get">
			<?php $page_name = isset( $_REQUEST['page'] ) && ! empty( $_REQUEST['page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) : ''; ?>
			<input type="hidden" name="page" value="<?php echo esc_attr( $page_name ); ?>">
			<?php $admin_service_list_table->display(); ?>
		</form>
	</div>
	<?php
} else {
	include SURELYWP_SERVICES_TEMPLATE_PATH . '/admin/service-view.php';
}
?>
<div class="surelywp-sv-modal">
	<div class="confirm-service-delete modal">
		<div class="modal-content">
			<span class="close-button close-modal-button">×</span>
			<div class="modal-top">
				<div class="heading"><?php echo esc_html__( 'Confirm', 'surelywp-services' ); ?><img class="hidden loader" src="<?php echo esc_url( SURELYWP_CORE_PLUGIN_URL ) . '/assets/images/wp-ajax-loader.gif'; ?>" /></div>
			</div>
			<div class="modal-bottom">
				<div class="delete-now-wrap">
					<p class="delete-msg"><?php esc_html_e( 'Are you sure you want to delete this service? When a service is deleted, all related data, including activities, contracts, messages, requirements, deliveries, and associated files, will also be permanently deleted.', 'surelywp-services' ); ?></p>
				</div>
			</div>
			<div class="approve-buttons">
				<a href="javascript:void(0)" class="btn-primary button-2 close-modal-button"><?php esc_html_e( 'Cancle', 'surelywp-services' ); ?></a>
				<a href="javascript:void(0)" id="confirm-service-delete" class="btn-secondary button-1"><?php esc_html_e( 'Delete Service', 'surelywp-services' ); ?></a>
			</div>
		</div>
	</div>
</div>