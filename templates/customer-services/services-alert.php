<?php
/**
 * Services Alert.
 *
 * @package Services For SureCart
 * @author  SurelyWP
 * @version 1.2
 */

$services_url      = '';
$dashboard_page_id = '';
if ( isset( $page_id ) && ! empty( $page_id ) ) {
	$dashboard_page_id = $page_id;
} else {
	$dashboard_page_id = get_option( 'surecart_dashboard_page_id' );
}
$sv_tab = isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '';


if ( $dashboard_page_id ) {

	$dashboard_url = get_permalink( $dashboard_page_id );

	if ( ! empty( $sv_tab ) ) {
		$dashboard_url = add_query_arg( array( 'tab' => $sv_tab ), $dashboard_url );
	}

	$services_url = add_query_arg(
		array(
			'action' => 'index',
			'model'  => 'services',
		),
		$dashboard_url
	);
}
$service_plural_name = Surelywp_Services::get_sv_plural_name();
?>
<div class="surelywp-sv-notification-alert" data-notification-count="<?php echo esc_html( $in_progress_services_count ); ?>">
	<sc-alert open type="primary" title="<?php echo esc_html( $alert_title ); ?>">
		<?php echo esc_html( $alert_desc ); ?>
		<sc-flex style="margin-top: 0.5rem;" justify-content="flex-start" class="hydrated">
			<sc-button href="<?php echo esc_url( $services_url ); ?>" type="primary" size="small" class="hydrated">
				<?php
					// translators: %s is the plural name of the services.
					printf( esc_html__( 'Go To %s', 'surelywp-services' ), esc_html( $service_plural_name ) );
				?>
			</sc-button>
			<sc-button type="text" size="small" class="dismiss-notification hydrated"><?php echo esc_html__( 'Dismiss', 'surelywp-services' ); ?></sc-button>
		</sc-flex>
	</sc-alert>
</div>