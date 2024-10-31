<?php
/**
 * The file that defines the class to send push notification to iOS devices.
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

/**
 * The core Android class.
 *
 * This is used to define function to send push notification to iOS devices.
 *
 * @since      1.0.0
 * @package    Push_Notification_Sender
 * @subpackage Push_Notification_Sender/includes
 * @author     Bishal Saha <bishal.saha@gmail.com>
 */
class Push_Notification_Sender_IOS {
	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 *
	 * @param  array  $devices registration tokens array.
	 * @param  array $message text message to send push notification.
	 *
	 * @return string $error return the error message if any.
	 */
	public static function send_to_ios( $devices, $message ) {

		ini_set( 'max_execution_time', 600 ); //600 seconds = 10 minutes
		ini_set( "memory_limit", "512M" );
		set_time_limit( 0 );

		$upload_dir         = wp_upload_dir();
		$pns_ios_certi_name = get_option( 'pns_ios_certi_name' );

		$user_iosdir        = '/pns-ioscerti/';
		$ios_certificate_custom_path = $upload_dir['basedir'] . $user_iosdir . $pns_ios_certi_name; /*for custom dir url*/

		$error = false;

		// post Option.
		$message_title = $message['title'];
		$message_text  = $message['message'];

		if ( empty( $pns_ios_certi_name ) || strlen( $pns_ios_certi_name ) <= 0 ) {
			$error = true;
			return $error;
		}

		$pns_send_via_production = get_option( 'pns_send_via_production' );
		$passphrase              = '';

		$ctx = stream_context_create();
		stream_context_set_option( $ctx, 'ssl', 'local_cert', $ios_certificate_custom_path );
		stream_context_set_option( $ctx, 'ssl', 'passphrase', $passphrase );

		if ( 'yes' === $pns_send_via_production ) {
			$fp = stream_socket_client( 'ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx );
		} else {
			$fp = stream_socket_client( 'ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx );
		}

		if ( ! $fp ) {
			exit( 'Failed to connect apple gateway:' . $err . $errstr . PHP_EOL );
		}

		$results = array();

		if ( empty( $msg_title ) ) {
			// Create the payload body.
			$body['aps'] = array(
				'badge' => + 1,
				'alert' => array(
					'title' => 'PushNotifications title to ios.',
					'body'  => 'PushNotifications send to ios.',
				),
				'sound' => 'default',
			);
		} else {
			// Create the payload body.
			$body['aps'] = array(
				'badge' => + 1,
				'alert' => array(
					'title' => $message_title,
					'body'  => $message_text,
				),
				'sound' => 'default',
			);
		}

		$payload = json_encode( $body );

		// Build the binary notification.
		foreach ( $devices as $device ) {
			// Build the binary notification.
			$msg = chr( 0 ) . pack( 'n', 32 ) . pack( 'H*', $device ) . pack( 'n', strlen( $payload ) ) . $payload;

			// Send it to the server.
			$results[] = fwrite( $fp, $msg, strlen( $msg ) );

			// Insert message into push notification log table.
			global $wpdb;
			$current_time  = current_time( 'mysql' );
			$table_push_notification_sender_logs = $wpdb->prefix . 'push_notification_sender_logs';

			$wpdb->insert(
				$table_push_notification_sender_logs,
				array(
					'push_title'     => $message_title,
					'push_message'   => $message_text,
					'push_sent'      => 1,
					'push_send_date' => $current_time,
					'token_id'       => $device,
				),
				array(
					'%s',
					'%s',
					'%d',
					'%s',
					'%s',
				)
			);
		}

		if ( ! empty( $results ) ) {
			return $results;
		}

		// Close the connection to the server.
		fclose( $fp );

		return $error;
	}
}