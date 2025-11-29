<?php
/**
 * Inside Customer view services list.
 *
 * @package Services For SureCart
 * @author  SurelyWP
 * @version 1.0.0
 */

use SureCart\Models\User;
global $surelywp_sv_model;


$user = User::findByCustomerId( $customer_id );

$services        = array();
$pagination_data = array();

if ( ! is_wp_error( $user ) && ! empty( $user ) ) {
	$user_id = $user->ID ?? '';
	if ( $user_id ) {
		$service_per_page = 10;
		$page_num         = 1;
		$services_data    = $surelywp_sv_model->surelywp_sv_get_user_services( $service_per_page, $page_num, $user_id );
		$services         = $services_data['services'] ?? array();
		$pagination_data  = $services_data['pagination'] ?? array();
	}
}
$services_obj        = Surelywp_Services();
$service_plural_name = Surelywp_Services::get_sv_plural_name();

if ( ! empty( $services ) ) {
	?>
	<div class="css-1hp6akg customer-services-list-block hidden" id="customer-services-list-block">
		<div data-wp-c16t="true" data-wp-component="Card" class="components-surface components-card css-1st819u css-443a5t e19lxcc00 css-talpf1">
			<div class="css-10klw3m e19lxcc00">
				<div data-wp-c16t="true" data-wp-component="CardHeader" class="components-flex components-card__header components-card-header css-15hyllm css-1w3sexp e19lxcc00 css-1v6oc13"><sc-text tag="h2" style="--font-size: 15px; --font-weight: var(--sc-font-weight-bold); width: 100%;" class="hydrated"><?php echo esc_html( $service_plural_name ); ?></sc-text></div>
				<div data-wp-c16t="true" data-wp-component="CardBody" class="components-card__body components-card-body css-1xfafpv css-1dzvnua e19lxcc00 css-1pa1sky">
					<div class="hidden customer-services-skeleton" id="customer-services-skeleton" >
						<sc-skeleton style="margin-bottom: 1rem; width: 95%;"></sc-skeleton>
						<sc-skeleton style="margin-bottom: 1rem; width: 80%;"></sc-skeleton>
					</div>
					<div class="customer-services-admin-block">
						<?php require SURELYWP_SERVICES_TEMPLATE_PATH . '/admin/block-service-list-table.php'; ?>
					</div>
				</div>
			</div>
			<div data-wp-c16t="true" data-wp-component="Elevation" class="components-elevation css-7g516l e19lxcc00" aria-hidden="true"></div>
			<div data-wp-c16t="true" data-wp-component="Elevation" class="components-elevation css-7g516l e19lxcc00" aria-hidden="true"></div>
			<?php if ( ! empty( $pagination_data ) && ( $pagination_data['total_pages'] ?? '' ) > 1 ) { ?>
			<div class="services-pagination">
				<div data-wp-c16t="true" data-wp-component="CardFooter" class="components-flex components-card__footer components-card-footer css-gogc1z e19lxcc00">
					<sc-flex justify-content="space-between" align-items="center" class="hydrated" style="width: 100%;">
						<input type="hidden" id="customer-services-pagination-data" data-current-page="<?php echo esc_attr( $pagination_data['current_page'] ); ?>" data-total-pages="<?php echo esc_attr( $pagination_data['total_pages'] ); ?>" data-user-id="<?php echo esc_attr( $user_id ); ?>" >
						<sc-button size="small" loading="false" disabled="" type="default" class="hydrated service-block-previous">
							<sc-icon slot="prefix" name="arrow-left" class="hydrated"></sc-icon>
							<?php echo esc_html__( 'Previous', 'surelywp-services' ); ?>
						</sc-button>
						<sc-button size="small" disabled="false" loading="false" type="default" class="hydrated service-block-next">
							<sc-icon slot="suffix" name="arrow-right" class="hydrated"></sc-icon><?php echo esc_html__( 'Next', 'surelywp-services' ); ?>
						</sc-button>
					</sc-flex>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
<?php } ?>