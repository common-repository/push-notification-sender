<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://gentryx.com/bishalsaha
 * @since      1.0.0
 *
 * @package    Push_Notification_Sender
 * @subpackage Push_Notification_Sender/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Push_Notification_Sender
 * @subpackage Push_Notification_Sender/admin
 * @author     Bishal Saha <bishal.saha@gmail.com>
 */
class Push_Notification_Sender_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;



	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;


	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Push_Notification_Sender_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Push_Notification_Sender_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( 'jquery-ui', plugin_dir_url( __FILE__ ) . 'css/jquery-ui-min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/push-notification-sender-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'pqselect.dev', plugin_dir_url( __FILE__ ) . 'css/pqselect.dev.css' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Push_Notification_Sender_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Push_Notification_Sender_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-selectmenu' );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/push-notification-sender-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'jquery-validate', plugin_dir_url( __FILE__ ) . 'js/jquery.validate.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'pqselect.dev', plugin_dir_url( __FILE__ ) . 'js/pqselect.dev.js', array(), '1.0.0', true );
	}

	/**
	 * Create pages for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function push_notification_sender_admin_menu() {
		if ( current_user_can( 'administrator' ) ) {
			$icon_svg_url = 'data:image/svg+xml;base64,' . base64_encode( '<svg width="20" height="20" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M912 1696q0-16-16-16-59 0-101.5-42.5t-42.5-101.5q0-16-16-16t-16 16q0 73 51.5 124.5t124.5 51.5q16 0 16-16zm816-288q0 52-38 90t-90 38h-448q0 106-75 181t-181 75-181-75-75-181h-448q-52 0-90-38t-38-90q50-42 91-88t85-119.5 74.5-158.5 50-206 19.5-260q0-152 117-282.5t307-158.5q-8-19-8-39 0-40 28-68t68-28 68 28 28 68q0 20-8 39 190 28 307 158.5t117 282.5q0 139 19.5 260t50 206 74.5 158.5 85 119.5 91 88z" fill="#fff"/></svg>' );
			add_menu_page(
				'Push Notification Sender',
				'Push Notification Sender',
				'administrator',
				'push-notification-sender',
				array( $this, 'push_notification_sender_list_html' ),
				$icon_svg_url,
				25
			);

			add_submenu_page(
				'push-notification-sender',
				'All Push Notifications',
				'All Push Notifications',
				'administrator',
				'push-notification-sender',
				array( $this, 'push_notification_sender_list_html' )
			);

			add_submenu_page(
				'push-notification-sender',
				'Custom Notification',
				'Custom Notification',
				'administrator',
				'custom-push-notification-sender',
				array( $this, 'custom_push_notification_sender_html' )
			);

			add_submenu_page(
				'push-notification-sender',
				'Settings',
				'Settings',
				'administrator',
				'push-notification-sender-settings',
				array( $this, 'push_notification_sender_settings_html' )
			);
		}
	}

	/**
	 * Create pages for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function push_notification_sender_list_html() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/push-notification-sender-list-display.php';
	}

	/**
	 * Create pages for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function push_notification_sender_settings_html() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/push-notification-sender-settings-display.php';
	}

	/**
	 * Create pages for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function custom_push_notification_sender_html() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/custom-push-notification-sender-display.php';
	}

	public function push_notification_sender_certificate_dir( $param ) {

		if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
			$request_uri_referrer       = $_SERVER['HTTP_REFERER'];
			$request_uri_referrer_parts = explode( 'page=', $request_uri_referrer );

			if ( isset( $request_uri_referrer_parts[1] ) ) {
				$current_page_url = $request_uri_referrer_parts[1];

				if ( 'push-notification-sender-settings&tab=ios_tab' === $current_page_url ) {
					$mydir         = '/pns-ioscert';
					$param['path'] = $param['basedir'] . $mydir;
					$param['url']  = $param['baseurl'] . $mydir;
				}
			}
		}

		return $param;
	}

	/**
	 * Upload Certificate Extension:
	 *
	 * @since    1.0.0
	 * @param array $mime_types mime type.
	 * @return string  $mime_types mime type
	 */
	public function push_notification_sender_mime_types( $mime_types ) {
		$mime_types['pem'] = 'application/x-pem-file'; // Adding .pem extension.
		$mime_types['p12'] = 'application/x-pkcs12';  // Adding photoshop files.

		return $mime_types;
	}

	/**
	 * Get all system users with is device token and device type.
	 *
	 * @since    1.0.0
	 */
	public function push_notification_sender_get_all_system_users() {

		global $wpdb;
		$only_ios     = get_option( 'pns_send_to_ios' );
		$only_android = get_option( 'pns_send_to_android' );

		$table_push_notification_token = $wpdb->prefix . 'push_notification_sender_token';

		$select_all_users = $wpdb->get_results(
			" SELECT device_token, os_type 
 					FROM   $table_push_notification_token",ARRAY_A
		);

		$all_user_devices = array();

		foreach ( $select_all_users as $select_sql_data ) {
			if ( ! empty( $select_sql_data['os_type'] ) ) {
				$device_type = $select_sql_data['os_type'];
			} else {
				$device_type = '';
			}
			if ( ! empty( $select_sql_data['device_token'] ) ) {
				$device_token = $select_sql_data['device_token'];
			} else {
				$device_token = '';
			}

			if ( 'android' === $device_type && 'yes' === $only_android ) {
				array_push(
					$all_user_devices,
					array(
						'token' => $device_token,
						'is_Android' => true,
					)
				);
			} elseif ( 'ios' === $device_type && 'yes' === $only_ios ) {
				array_push(
					$all_user_devices,
					array(
						'token' => $device_token,
						'is_Android' => false,
					)
				);
			}
		}
		return $all_user_devices;
	}

	/**
	 * Send notifications on post update/insert/edit:
	 *
	 * @since    1.0.0
	 */
	public function push_notification_sender_save_post() {
		$error = false;
		$only_ios     = get_option( 'pns_send_to_ios' );
		$only_android = get_option( 'pns_send_to_android' );
		$post_title   = sanitize_text_field( $_POST['post_title'] );
		$post_content = sanitize_text_field( $_POST['post_content'] );

		$message = array(
			'title' => $post_title,
			'message' => $post_content,
		);

		$all_user_devices = $this->push_notification_sender_get_all_system_users();

		if ( 'yes' === $only_ios ) {
			$ios_certi_name = get_option( 'ios_certi_name' );
			if ( ( empty( $ios_certi_name )) || ( strlen( $ios_certi_name ) <= 0) ) {
				$error = 'true';
			}
		}

		if ( 'yes' === $only_android ) {
			$pns_google_api_key = get_option( 'pns_google_api_key' );
			if ( ( empty( $pns_google_api_key ) ) || ( strlen( $pns_google_api_key ) <= 0 ) ) {
				$error = 'true';
			}
		}

		if ( 'false' === $error ) {
			$push_notification_sender_api_obj = new Push_Notification_Sender_API();
			$push_notification_sender_api_obj->send_notification( $all_user_devices, $message );
		} else {
			return false;
		}
	}

	/**
	 * Display error when notifications not send.
	 *
	 * @since    1.0.0
	 * @param array $messages set the message.
	 * @return array  $message returns the message.
	 */
	public function push_notification_sender_post_published( $messages = array() ) {
		global $post_ID , $error;

		if ( ! empty( $error ) ) {
			$messages['post'][1] = sprintf( __( 'Post updated. <a href="%s">View post</a>, notification not send Please check Setting page.' ),  esc_url( get_permalink( $post_ID ) ) );
			$messages['post'][6] = sprintf( __( 'Post published. <a href="%s">View post</a>, notification not send Please check Setting page.' ), esc_url( get_permalink( $post_ID ) ) );

			$messages['page'][1] = sprintf( __( 'Page updated. <a href="%s">View page</a>, notification not send Please check Setting page.' ), esc_url( get_permalink( $post_ID ) ) );
			$messages['page'][6] = sprintf( __( 'Page published. <a href="%s">View page</a>, notification not send Please check Setting page.' ), esc_url( get_permalink( $post_ID ) ) );
		} else {
			$messages['post'][1] = sprintf( __( 'Post updated. <a href="%s">View post</a>, Notification Sent Successfully.' ), esc_url( get_permalink( $post_ID ) ) );
			$messages['post'][6] = sprintf( __( 'Post published. <a href="%s">View post</a>, Notification Sent Successfully.' ), esc_url( get_permalink( $post_ID ) ) );

			$messages['page'][1] = sprintf( __( 'Page updated. <a href="%s">View page</a>  , Notification Sent Successfully.' ), esc_url( get_permalink( $post_ID ) ) );
			$messages['page'][6] = sprintf( __( 'Page published. <a href="%s">View page</a> , Notification Sent Successfully.' ), esc_url( get_permalink( $post_ID ) ) );
		}
		return $messages;
	}
}
