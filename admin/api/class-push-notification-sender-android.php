<?php
/**
 * The file that defines the class to send push notification to Android devices.
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
 * This is used to define function to send push notification to Android devices.
 *
 * @since      1.0.0
 * @package    Push_Notification_Sender
 * @subpackage Push_Notification_Sender/includes
 * @author     Bishal Saha <bishal.saha@gmail.com>
 */
class Push_Notification_Sender_Android {
	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 *
	 * @param  array  $registration_ids registration tokens array.
	 * @param  string $message text message to send push notification.
	 *
	 * @return string $error return the error message if any.
	 */
	function send_to_android( $registration_ids, $message ) {
		ini_set( 'max_execution_time', 600 );
		ini_set( 'memory_limit', '512M' );
		set_time_limit( 0 );

		$error = false;

		// post Option.
		$msg_title    = $message['title'];
		$message_text = $message['message'];

		// Get Option.
		$pns_google_api_key = get_option( 'pns_google_api_key' );

		if ( empty( $pns_google_api_key ) || strlen( $pns_google_api_key ) <= 0 ) {
			$error = true;

			return $error;
		}

		// Get Option.
		$pns_send_to_android_via = get_option( 'pns_send_to_android_via' );

		// include config.
		define( 'GOOGLE_API_KEY', $pns_google_api_key );

		// Set POST variables.
		if ( 'gcm' === $pns_send_to_android_via ) {
			$url = 'https://android.googleapis.com/gcm/send';
		} else {
			$url = 'https://fcm.googleapis.com/fcm/send';
		}

		if ( empty( $msg_title ) ) {
			// prep the bundle.
			$message = array(
				'message'    => 'Push Notification send to android.',
				'title'      => 'Push Notification title to android.',
				'subtitle'   => 'Push Notification subtitle to android.',
				'tickerText' => 'Push Notification text here...',
				'vibrate'    => 1,
				'sound'      => 1,
				'largeIcon'  => 'large_icon',
				'smallIcon'  => 'small_icon',
			);
		} else {
			$message = array(
				'message'   => $message_text,
				'title'     => $msg_title,
				'vibrate'   => 1,
				'sound'     => 1,
				'largeIcon' => 'large_icon',
				'smallIcon' => 'small_icon',
			);
		}

		$fields = array(
			'registration_ids' => $registration_ids,
			'data'             => $message,
		);

		$headers = array(
			'Authorization: key=' . GOOGLE_API_KEY,
			'Content-Type: application/json',
		);

		// TODO: use wp_remote_get instead of CURL
		// Open connection.
		$ch = curl_init();

		// Set the url, number of POST vars, POST data.
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

		// Disabling SSL Certificate support temporarly.
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, wp_json_encode( $fields ) );

		// Execute post.
		$result = curl_exec( $ch );

		if ( false === $result ) {
			die( 'Curl failed: ' . curl_error( $ch ) );
		}

		global $wpdb;
		$current_time = current_time( 'mysql' );
		$table_push_notification_sender_logs = $wpdb->prefix . 'push_notification_sender_logs';

		foreach ( $registration_ids as $registration_id ) {
			$wpdb->insert(
				$table_push_notification_sender_logs,
				array(
					'push_title'     => $msg_title,
					'push_message'   => $message_text,
					'push_sent'      => 1,
					'push_send_date' => $current_time,
					'token_id'       => $registration_id,
				),
				array( '%s', '%s', '%d', '%s', '%s' )
			);
			$wpdb->print_error();
		}

		// Close connection.
		curl_close( $ch );

		return $error;

	}
}
