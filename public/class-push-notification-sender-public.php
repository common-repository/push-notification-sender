<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://gentryx.com/bishalsaha
 * @since      1.0.0
 *
 * @package    Push_Notification_Sender
 * @subpackage Push_Notification_Sender/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Push_Notification_Sender
 * @subpackage Push_Notification_Sender/public
 * @author     Bishal Saha <bishal.saha@gmail.com>
 */
class Push_Notification_Sender_Public {

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
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/push-notification-sender-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/push-notification-sender-public.js', array( 'jquery' ), $this->version, false );

	}
	/**
	* REST API to register apn
	*
	* @since    1.0.0
	*/
	public function push_notification_sender_apn_register_api() {
		//http://staging.gentryx.com/wordpress-4.8.1/wp-json/push-notification-sender/register?os_type=android&user_email_id=bishal.saha@gmail.com&device_token=aaa
		register_rest_route( 'push-notification-sender', '/register', array(
			'methods' => 'GET',
			'callback' => array( $this, 'push_notification_sender_add_device_token' ),
		));
	}

	function push_notification_sender_add_device_token( $request_data ) {

		$parameters = $request_data->get_params();

		$device_token  = sanitize_text_field( $parameters['device_token'] );
		$os_type       = sanitize_text_field( $parameters['os_type'] );
		$user_email_id = sanitize_text_field( $parameters['user_email_id'] );

		if ( ! empty( $parameters ) ) {
			$user_email = $user_email_id;
			$user_name_array = explode( '@',$user_email );

			if ( isset( $user_name_array[0] ) ) {
				$user_name = $user_name_array[0];
			} else {
				$user_name = '' ;
			}

			$email_exists = email_exists( $user_email_id );

			if ( false === $email_exists ) {
				$length = 12;
				$include_standard_special_chars = false;
				$random_password = wp_generate_password( $length, $include_standard_special_chars );
				$user_name       = $user_name . mt_rand( 10, 100 );
				$user_id         = wp_create_user( $user_name, $random_password, $user_email_id );

				if ( $user_id ) {
					$token_add_status = $this->update_table_tokens( $device_token, $os_type, $user_email_id );
				} else {
					$token_add_status = false ;
				}
			} else {
				$token_add_status = $this->update_table_tokens( $device_token, $os_type, $user_email_id );
			}

			if ( $token_add_status ) {
				$json_status     = 'Success';
				$json_error_code = '200';
				$json_message    = 'Token updated successfully.';
			} else {
				$json_status     = 'Failed';
				$json_error_code = '500';
				$json_message    = 'Unable to update the token.';
			}
		} else {
			$json_status     = 'Failed';
			$json_error_code = '302';
			$json_message    = 'Please provide Proper Parameters';
		}

		$json_data = array(
			'Status'     => $json_status,
			'Code'       => $json_error_code,
			'Message'    => $json_message,
		);

		echo wp_json_encode( $json_data );
		exit;
	}

	public function update_table_tokens( $user_device_token, $user_os_type, $user_email_id ) {
		global $wpdb;
		$table_push_notification_tokens = $wpdb->prefix . 'push_notification_sender_tokens';

		$check_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM  $table_push_notification_tokens  WHERE user_email_id = %s and os_type = %s", $user_email_id, $user_os_type ), ARRAY_A );

		$last_updatedate = current_time( 'mysql' );

		if ( count( $check_row ) ) {
			foreach ( $check_row as $key => $val ) {
				$wpdb->update(
					$table_push_notification_tokens,
					array(
						'device_token'    => $user_device_token,
						'last_updatedate' => $last_updatedate,
					),
					array(
						'token_id' => $check_row['token_id'],
					),
					array(
						'%s',
						'%s',
						'%s',
					),
					array(
						'%d',
					)
				);
				return true;
			}
		} else {
			$user = get_user_by( 'email', $user_email_id );
			if ( $user->ID ) {
				$wpdb->insert(
					$table_push_notification_tokens,
					array(
						'token_id' => null,
						'device_token' => $user_device_token,
						'os_type' => $user_os_type,
						'user_email_id' => $user_email_id,
						'user_id' => $user->ID,
						'last_updatedate' => $last_updatedate,
					),
					array(
						'%d',
						'%s',
						'%s',
						'%s',
						'%d',
						'%s',
					)
				);
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * Display error when notifications not send.
	 *
	 * @since    1.0.0
	 * @param integer $comment_id set the message.
	 * @param object  $comment_object comment object.
	 */
	public function push_notification_sender_comment_inserted( $comment_id, $comment_object ) {
		global $wpdb, $post;

		$post_author_id = get_post_field (
			'post_author',
			sanitize_text_field( $_POST['comment_post_ID'] )
		);
		$post_title = 'Comment Inserted';
		$push_notification_sender_api_obj = new Push_Notification_Sender_API();
		$message = array(
			'title' => $post_title,
			'message' => sanitize_textarea_field( $_POST['comment'] )
		);
		$push_notification_sender_token = $wpdb->prefix . 'push_notification_sender_tokens';
		$post_author_device = $wpdb->get_row( " SELECT device_token, os_type FROM $push_notification_sender_token where user_id = ".$post_author_id,ARRAY_A);

		if ( ! empty( $post_author_device['os_type'] ) ) {
			$device_type = $post_author_device['os_type'];
		} else {
			$device_type = '';
		}
		if ( ! empty( $post_author_Device['device_token'] ) ) {
			$device_token = $post_author_Device['device_token'];
		} else {
			$device_token = '';
		}

		$all_user_devices = array();

		if ( 'android' === $device_type ) {
			array_push(
				$all_user_devices,
				array(
					'token' => $device_token,
					'is_Android' => true,
				)
			);
		} elseif ( 'ios' === $device_type ) {
			array_push(
				$all_user_devices,
				array(
					'token' => $device_token,
					'is_Android' => false,
				)
			);
		}
		$push_notification_sender_api_obj->send_notification( $all_user_devices, $message );
	}
}
