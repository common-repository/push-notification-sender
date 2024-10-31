<?php
/**
 * The file that defines the class to render the list table.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://gentryx.com/bishalsaha
 * @since      1.0.0
 *
 * @package    Push_Notification_Sender
 * @subpackage Push_Notification_Sender/includes
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * The Sender List Table class.
 *
 * This is used to define function to send push notification to iOS devices.
 *
 * @since      1.0.0
 * @package    Push_Notification_Sender
 * @subpackage Push_Notification_Sender/includes
 * @author     Bishal Saha <bishal.saha@gmail.com>
 */
class Push_Notification_Sender_List_Table extends WP_List_Table {
	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the singular and plural name
	 *
	 * @since    1.0.0
	 */
	function __construct() {
		// et parent defaults.
		parent::__construct( array(
			'singular' => 'delete_id',     // singular name of the listed records
			'plural'   => 'delete_ids',    // plural name of the listed records
			'ajax'     => false,            // does this table support ajax?
		) );
	}

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the singular and plural name
	 *
	 * @since    1.0.0
	 *
	 * @param array  $item  list items array.
	 * @param  string $column_name  column names.
	 *
	 * @return  array  $item return value
	 */
	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'log_id':
			case 'push_title':
				return $item[ $column_name ];
			case 'push_message':
				return $item[ $column_name ];
			case 'push_send_date':
				return $item[ $column_name ];
			case 'token_id':
				return $item[ $column_name ];
			default:
				return $item; // Show the whole array for troubleshooting purposes.
		}
	}

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the singular and plural name
	 *
	 * @since    1.0.0
	 *
	 * @param  array $item  list items array.
	 *
	 * @return array  print
	 */
	function column_title( $item ) {
		// Build row actions.
		$actions = array(
			'edit'   => sprintf( '<a href="?page=%s&action=%s&delete_id=%s">Edit</a>', $_REQUEST['page'], 'edit', $item['log_id'] ),
			'delete' => sprintf( '<a href="?page=%s&action=%s&delete_id=%s">Delete</a>', $_REQUEST['page'], 'delete', $item['log_id'] ),
		);

		// Return the title contents.
		return sprintf( '%1$s %2$s',
			$item['log_id'],
			$item['push_title'],
			$item['push_message'],
			$item['push_send_date'],
			$item['token_id'],
			$this->row_actions( $actions )
		);
	}

	/**
	 * Define column call back.
	 *
	 * Set the singular and plural name
	 *
	 * @since    1.0.0
	 *
	 * @param  array $item  list items array.
	 *
	 * @return array  print
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],  // Let's simply repurpose the table's singular label ("movie").
			$item['log_id'],           // The value of the checkbox should be the record's id.
			$item['push_title'],
			$item['push_message'],
			$item['push_send_date'],
			$item['token_id']
		);
	}

	/**
	 * DGet column.
	 *
	 * Set the singular and plural name
	 *
	 * @since    1.0.0
	 *
	 * @return array  print
	 */
	function get_columns() {
		$columns = array(
			'cb'             => '<input type="checkbox" />', // Render a checkbox instead of text.
			'push_title'     => 'Title',
			'push_message'   => 'Message',
			'push_send_date' => 'Date',
			'token_id'       => 'Device Token',
		);

		return $columns;
	}

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the singular and plural name
	 *
	 * @since    1.0.0
	 * @return array  print
	 */
	function get_sortable_columns() {
		$sortable_columns = array(
			'push_sent_date' => array( 'push_send_date', false ),
		);

		return $sortable_columns;
	}

	/**
	 * get bulk action
	 *
	 * Set the singular and plural name
	 *
	 * @since    1.0.0
	 *
	 * @return array  print
	 */
	function get_bulk_actions() {
		$actions = array(
			'delete' => 'Delete',
		);

		return $actions;
	}

	/**
	 * Process bulk action.
	 *
	 * Set the singular and plural name
	 *
	 * @since    1.0.0
	 */
	function process_bulk_action() {
		// Detect when a bulk action is being triggered.
		if ( 'delete' === $this->current_action() ) {
			global $wpdb;

			$array_data             = $_REQUEST['delete_id'];
			$push_notification_logs = $wpdb->prefix . 'push_notification_sender_logs';

			foreach ( $array_data as $key => $id_value ) {
				$wpdb->query( "DELETE FROM $push_notification_logs WHERE log_id = " . $id_value );
			}

			$current_page_url = $_REQUEST['_wp_http_referer'];

			echo "<script>window.location.href='" . $current_page_url . "';</script>";
		}
	}

	/**
	 * Prepare items.
	 *
	 * Prepare the whole items
	 *
	 * @since    1.0.0
	 */
	public function prepare_items() {
		global $wpdb;
		$per_page = 15;

		$push_notification_logs = $wpdb->prefix . 'push_notification_sender_logs';
		$paged                  = isset( $_REQUEST['paged'] ) ? max( 0, intval( $_REQUEST['paged'] ) - 1 ) * $per_page : 0;
		$where                  = '';
		$orderby                = ( isset( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], array_keys( $this->get_sortable_columns() ) ) ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'log_id';
		$order                  = ( isset( $_REQUEST['order'] ) && in_array( $_REQUEST['order'], array(	'asc', 'desc' ) ) ) ? sanitize_text_field( $_REQUEST['order'] ) : 'desc';
		$data                   = $wpdb->get_results( "SELECT * FROM $push_notification_logs $where ORDER BY $orderby $order LIMIT $per_page OFFSET $paged", ARRAY_A );
		$columns                = $this->get_columns();
		$hidden                 = array();
		$sortable               = $this->get_sortable_columns();
		$this->_column_headers  = array( $columns, $hidden, $sortable );
		$current_page           = $this->get_pagenum();
		$total_items            = $wpdb->get_var( "SELECT COUNT(log_id) FROM $push_notification_logs" );
		$this->items            = $data;

		$this->process_bulk_action();
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,                  // WE have to calculate the total number of items.
				'per_page'    => $per_page,                     // WE have to determine how many items to show on a page.
				'total_pages' => ceil( $total_items / $per_page ),   // WE have to calculate the total number of pages.
			)
		);
	}
}
