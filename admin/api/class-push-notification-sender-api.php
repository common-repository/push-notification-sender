<?php
/**
 * The file that defines the class to send push notification.
 *
 * A class definition that includes attributes and functions used to send push notification.
 *
 * @link       http://gentryx.com/bishalsaha
 * @since      1.0.0
 *
 * @package    Push_Notification_Sender
 * @subpackage Push_Notification_Sender/includes
 */

/** @noinspection PhpIncludeInspection */
require_once plugin_dir_path( __FILE__ ) . 'class-push-notification-sender-android.php';
/** @noinspection PhpIncludeInspection */
require_once plugin_dir_path( __FILE__ ) . 'class-push-notification-sender-ios.php';

/**
 * The core plugin class to send push notification api.
 *
 * This is used to define api.
 *
 * @since      1.0.0
 * @package    Push_Notification_Sender
 * @subpackage Push_Notification_Sender/includes
 * @author     Bishal Saha <bishal.saha@gmail.com>
 */
class Push_Notification_Sender_API {

	/**
	 * Send notification
	 *
	 * @since  1.0.0
	 * @param  array  $user_devices user device token.
	 * @param  string $message text message to send.
	 */
	function send_notification( $user_devices, $message ) {

		ini_set( 'max_execution_time', 600 ); //600 seconds = 10 minutes
		ini_set( "memory_limit", "512M" );
		set_time_limit( 0 );

		$android_devices = array();
		$ios_devices     = array();

		/*for android push notification start*/
		foreach ( $user_devices as $device ) {
			if ( $device['is_Android'] ) {
				$android_devices[] = $device['token'];
			} elseif ( $device['is_IOS'] ) {
				$ios_devices[] = $device['token'];
			}
		}

		if ( ! empty( $android_devices ) ) {
			$push_notification_sender_android_obj = new Push_Notification_Sender_Android();
			$push_notification_sender_android_obj->send_to_android( $android_devices, $message );
		}

		/*for ios push notification start*/
		if ( ! empty( $ios_devices ) ) {
			$push_notification_sender_ios_obj = new Push_Notification_Sender_IOS();
			$push_notification_sender_ios_obj->send_to_ios( $ios_devices, $message );
		}
	}
}
