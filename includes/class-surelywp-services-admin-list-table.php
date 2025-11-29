<?php
/**
 * Admin services list table class
 *
 * @package Services For SureCart
 * @author  SurelyWP
 * @version 1.0.0
 */

use SureCart\Models\Product;
use SureCart\Models\Order;
use SureCart\Models\Customer;

/**
 * Create a new table class that will extend the WP_List_Table
 */
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
/**
 * Services list Class
 *
 * @package Services For SureCart
 * @since   1.0.0
 */
class Surelywp_Services_Admin_List_Table extends WP_List_Table {

	/**
	 * Class model variable.
	 *
	 * @var $model the class model variable.
	 */
	public $model;

	/**
	 * Per page.
	 *
	 * @var $per_page per paqge services.
	 */
	public $per_page;


	/**
	 * Order ids.
	 *
	 * @var $order_ids the order ids.
	 */
	public $order_ids = array();

	/**
	 * The Class cinstructer.
	 *
	 * @package Services For SureCart
	 * @since   1.0.0
	 */
	public function __construct() {

		global $surelywp_sv_model, $page;

		// Set parent defaults.
		parent::__construct(
			array(
				'singular' => 'service',     // singular name of the listed records.
				'plural'   => 'services',    // plural name of the listed records.
				'ajax'     => false,        // does this table support ajax?.
			)
		);

		$this->model    = $surelywp_sv_model;
		$this->per_page = apply_filters( 'surelywp_admin_services_per_page', 10 ); // Per page.
	}

	/**
	 * Displaying Coupons
	 *
	 * Does prepare the data for displaying the coupons in the table.
	 *
	 * @package Services For SureCart
	 * @since   1.0.0
	 */
	public function service_display() {

		// Taking parameter.
		$orderby        = isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'created_at';
		$order          = isset( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'DESC';
		$service_status = isset( $_GET['service_status'] ) && ! empty( $_GET['service_status'] ) ? sanitize_text_field( wp_unslash( $_GET['service_status'] ) ) : 'all';

		$args = array(
			'posts_per_page' => $this->per_page,
			'page'           => isset( $_GET['paged'] ) ? $_GET['paged'] : null,
			'orderby'        => $orderby,
			'order'          => $order,
			'service_status' => $service_status,
			'offset'         => ( $this->get_pagenum() - 1 ) * $this->per_page,
		);

		// call function to retrive data from table.
		$data = $this->model->surelywp_sv_get_services_for_list_table( $args );

		$this->order_ids = ( ! empty( $data['data'] ) ) ? wp_list_pluck( $data['data'], 'order_id' ) : array();

		if (! empty( $this->order_ids ) ) {

			$order_obj = Order::where( array( 'ids' => $this->order_ids ) )->with( array( 'checkout', 'checkout.customer' ) )->get();

			// Cache the results for 1 hour (20 seconds).
			set_transient( 'surelywp_orders_obj', $order_obj, 20 );
		}

		return $data;
	}

	/**
	 * Mange column data
	 *
	 * Default Column for listing table
	 * Does to add the column to the listing page
	 * column name must be same as in function {get_columns}
	 *
	 * @param array  $item The array of the column item.
	 * @param string $column_name The name of the column.
	 *
	 * @package Services For SureCart
	 * @since   1.0.0
	 */
	public function column_default( $item, $column_name ) {

		switch ( $column_name ) {
			case 'service_id':
				return $this->get_service_view_link( $item[ $column_name ] );
			case 'user_name':
			case 'user_email':
				return $this->get_customer_email( $item['user_id'] );
			case 'order_id':
			case 'product_id':
				return $this->get_product_name( $item[ $column_name ] );
			case 'service_status':
				return $this->get_service_status( $item[ $column_name ], $item['product_id'], $item['service_id'] );
			case 'created_at':
				return $this->get_created_at( $item, $column_name );
			default:
				return $item[ $column_name ];
		}
	}

	/**
	 * Get product name.
	 *
	 * @param array  $item the column item.
	 * @param string $column_name the column name.
	 *
	 * @package Services For SureCart
	 * @since   1.0.0
	 */
	public function get_created_at( $item, $column_name ) {
		$date_format = get_option( 'date_format', 'j F Y' );
		$time_format = get_option( 'time_format', 'H:i' );
		$format      = $date_format . ' \a\t ' . $time_format;
		$date_string = wp_date( $format, strtotime( $item[ $column_name ] ) );
		$column_html = apply_filters( 'surelywp_sv_admin_list_column_created_at', $date_string, $item, $column_name );
		return $column_html;
	}

	/**
	 * Set User Customer Name.
	 *
	 * @param array $item the array of column items.
	 *
	 * @package Services For SureCart
	 * @since   1.0.0
	 */
	public function column_user_name( $item ) {

		$order_id = $item['order_id'];

		if ( ! empty( $order_id ) ) {

			$surelywp_orders_obj = get_transient( 'surelywp_orders_obj' );
			if ( false === $surelywp_orders_obj ) {

				$orders_obj = Order::where( array( 'ids' => $this->order_ids ) )->with( array( 'checkout', 'checkout.customer' ) )->get();

				// Cache the results for 1 hour (20 seconds).
				set_transient( 'surelywp_orders_obj', $orders_obj, 20 );
			}

			$order_obj = '';
			if ( ! empty( $surelywp_orders_obj ) ) {
				foreach ( $surelywp_orders_obj as $key => $order ) {
					if ( $order_id === $order->id ) {
						$order_obj = $surelywp_orders_obj[ $key ];
						break;
					}
				}
			}

			if ( ! is_wp_error( $order_obj ) && ! empty( $order_obj ) ) {

				$customer = $order_obj->checkout->customer ?? '';

				if ( isset( $customer ) && ! is_wp_error( $customer ) && ! empty( $customer ) ) {
					ob_start();
					?>
					<a aria-label="<?php echo esc_attr__( 'Edit Customer', 'surelywp-services' ); ?>" href="<?php echo esc_url( \SureCart::getUrl()->edit( 'customers', $customer->id ) ); ?>">
						<?php echo wp_kses_post( $customer->name ?? $customer->email ); ?>
					</a>
					<?php
					$column_html = apply_filters( 'surelywp_sv_admin_list_column_user_name', ob_get_clean(), $item, $customer );
					return $column_html;
				}
			}
		}
	}

	/**
	 * Set order column.
	 *
	 * @param array $item the array of column items.
	 *
	 * @package Services For SureCart
	 * @since   1.0.0
	 */
	public function column_order_id( $item ) {

		$order_id = $item['order_id'];

		$surelywp_orders_obj = get_transient( 'surelywp_orders_obj' );
		if ( false === $surelywp_orders_obj ) {

			$orders_obj = Order::where( array( 'ids' => $this->order_ids ) )->with( array( 'checkout', 'checkout.customer' ) )->get();

			// Cache the results for 1 hour (20 seconds).
			set_transient( 'surelywp_orders_obj', $orders_obj, 20 );
		}

		$order_obj = '';
		if ( ! empty( $surelywp_orders_obj ) ) {
			foreach ( $surelywp_orders_obj as $key => $order ) {
				if ( $order_id === $order->id ) {
					$order_obj = $surelywp_orders_obj[ $key ];
					break;
				}
			}
		}

		if ( is_wp_error( $order_obj ) && isset( $order_obj->errors['http_request_failed'] ) ) {
			$this->column_order_id( $item );
		}

		if ( isset( $order_obj ) && ! is_wp_error( $order_obj ) && ! empty( $order_obj ) ) {

			ob_start();
			?>
			<a aria-label="<?php echo esc_attr__( 'Edit Order', 'surelywp-services' ); ?>" href="<?php echo esc_url( \SureCart::getUrl()->edit( 'order', $order_obj->id ) ); ?>">
				#<?php echo esc_html( $order_obj->number ?? $order_obj->id ); ?>
			</a>
			<?php
			$column_html = apply_filters( 'surelywp_sv_admin_list_column_order_id', ob_get_clean(), $item, $order_obj );
			return $column_html;
		}
	}

	/**
	 * Get product name.
	 *
	 * @param string $product_id the id of the product.
	 *
	 * @package Services For SureCart
	 * @since   1.0.0
	 */
	public function get_product_name( $product_id ) {

		$product = Product::find( $product_id );

		if ( isset( $product ) && ! is_wp_error( $product ) && ! empty( $product ) ) {

			$product_name = $product->name;
			$product_url  = \SureCart::getUrl()->edit( 'product', $product_id ) ?? '';

			ob_start();
			?>
			<a aria-label="<?php echo esc_attr__( 'Edit Product', 'surelywp-services' ); ?>" href="<?php echo esc_url( $product_url ); ?>">
				<?php echo esc_html( $product_name ); ?>
			</a>
			<?php
			$column_html = apply_filters( 'surelywp_sv_admin_list_column_product_name', ob_get_clean(), $product );
			return $column_html;
		}
	}

	/**
	 * Get Customer email.
	 *
	 * @param int $user_id The id of the user.
	 *
	 * @package Services For SureCart
	 * @since   1.0.0
	 */
	public function get_customer_email( $user_id ) {

		$user_data = get_userdata( $user_id );

		$user_email = '';
		if ( ! empty( $user_data ) ) {
			$user_email = $user_data->user_email ?? '';
		}
		$column_html = apply_filters( 'surelywp_sv_admin_list_column_customer_email', $user_email, $user_id );
		return $column_html;
	}

	/**
	 * Get service status.
	 *
	 * @param string $status The status of the service.
	 *
	 * @package Services For SureCart
	 * @since   1.0.0
	 */
	public function get_service_status( $status, $product_id, $service_id ) {

		$service_obj = Surelywp_Services();
		$status_txt  = $service_obj->surelywp_sv_get_service_status( $status, $product_id, $service_id );
		$type        = 'service_complete' === $status ? 'success' : ( 'service_canceled' === $status ? 'danger' : 'warning' );
		$column_html = '<sc-tag type="' . esc_attr( $type ) . '" size="medium" class="hydrated">' . $status_txt . '</sc-tag>';
		$column_html = apply_filters( 'surelywp_sv_admin_list_column_service_status', $column_html, $service_obj, $status_txt );
		return $column_html;
	}

	/**
	 * Get service view link.
	 *
	 * @param int $service_id The id of the service.
	 *
	 * @package Services For SureCart
	 * @since   1.0.0
	 */
	public function get_service_view_link( $service_id ) {

		$view_url = admin_url( 'admin.php' ) . '?page=sc-services&action=view&service_id=' . $service_id;
		return '<a class="service_view row-title" href="' . esc_url( $view_url ) . '">' . $service_id . '</a>';
	}

	/**
	 * Display Columns
	 *
	 * Handles to show the minimum columns into the table
	 *
	 * @package Services For SureCart
	 * @since   1.0.0
	 */
	public function get_columns() {

		$columns = array(
			// 'cb'             => '<input type="checkbox" />', // Render a checkbox instead of text.
			'service_id'     => esc_html__( 'ID', 'surelywp-services' ),
			'user_name'      => esc_html__( 'Name', 'surelywp-services' ),
			'user_email'     => esc_html__( 'Email', 'surelywp-services' ),
			'order_id'       => esc_html__( 'Order', 'surelywp-services' ),
			'product_id'     => esc_html__( 'Product', 'surelywp-services' ),
			'service_status' => esc_html__( 'Status', 'surelywp-services' ),
			'created_at'     => esc_html__( 'Date', 'surelywp-services' ),
		);
		return $columns;
	}

	/**
	 * Manage view service.
	 *
	 * @param array $item The array of column items.
	 *
	 * @package Services For SureCart
	 * @since   1.0.0
	 */
	public function column_service_id( $item ) {

		// Build row actions.
		$actions = array(
			'view' => sprintf( '<a href="?page=%s&action=%s&service_id=%s">' . esc_html__( 'View', 'surelywp-services' ) . '</a>', 'sc-services', 'view', $item['service_id'] ),
		);

		$delete_url = wp_nonce_url(
			admin_url( sprintf( 'admin.php?page=sc-services&sv_action=delete&service_delete=%d', $item['service_id'] ) ),
			'delete_service_' . $item['service_id']
		);

		if ( 'service_canceled' === $item['service_status'] ) {
			$actions['delete'] = sprintf(
				'<a class="surelywp-sv-delete-service" href="%s">%s</a>',
				$delete_url,
				esc_html__( 'Delete', 'surelywp-services' )
			);
		}

		// Return the title contents.
		$column_html = sprintf(
			'%1$s %2$s',
			/*$1%s*/ $this->get_service_view_link( $item['service_id'] ),
			/*$2%s*/ $this->row_actions( $actions )
		);

		$column_html = apply_filters( 'surelywp_sv_admin_list_column_service_id', $column_html, $item );

		return $column_html;
	}

	/**
	 * Sortable Columns
	 *
	 * Handles sortable column in list table
	 * it will automatically manage ascending and descending functionality of table
	 *
	 * @package Services For SureCart
	 * @since   1.0.0
	 */
	public function get_sortable_columns() {

		$sortable_columns = array(
			'created_at' => array( 'created_at', true ),
			'service_id' => array( 'service_id', true ),
		);
		return $sortable_columns;
	}
	/**
	 * No items
	 *
	 * Handles the message when no records available in table
	 *
	 * @package Services For SureCart
	 * @since   1.0.0
	 */
	public function no_items() {

		$service_plural_name = Surelywp_Services::get_sv_plural_name();
		// translators: %s is the plural name of the services.
		printf( esc_html__( 'No %s found.', 'surelywp-services' ), esc_html( $service_plural_name ) );
	}

	/**
	 * Process Bulk actions
	 *
	 * Handles Process of bulk action which is call on bulk action
	 *
	 * @package WP List Table (Custom Table)
	 * @since 1.0.0
	 */
	public function process_bulk_action() {

		// Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			wp_die( esc_html__( 'Items deleted (or they would be if we had items to delete)!', 'surelywp-services' ) );
		}
	}

	/**
	 * Prepare Items
	 *
	 * Does prepare all our data to show into the page
	 *
	 * @package Services For SureCart
	 * @since   1.0.0
	 */
	public function prepare_items() {

		// Get how many records per page to show.
		$per_page = $this->per_page;

		// Get All, Hidden, Sortable columns.
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		// Get final column header.
		$this->_column_headers = array( $columns, $hidden, $sortable );

		// Get Data of particular page.
		$data_res = $this->service_display();

		$data = $data_res['data'];

		// Get current page number.
		$current_page = $this->get_pagenum();

		// Get total count.
		$total_items = $data_res['total'];

		// Get page items.
		$this->items = $data;

		// We also have to register our pagination options & calculations.
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,                  // WE have to calculate the total number of items.
				'per_page'    => $per_page,                     // WE have to determine how many items to show on a page.
				'total_pages' => ceil( $total_items / $per_page ),   // WE have to calculate the total number of pages.
			)
		);
	}
}
