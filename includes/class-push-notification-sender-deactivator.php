<?php
/**
 * Fired during plugin deactivation
 *
 * @link       http://gentryx.com/bishalsaha
 * @since      1.0.0
 *
 * @package    Push_Notification_Sender
 * @subpackage Push_Notification_Sender/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Push_Notification_Sender
 * @subpackage Push_Notification_Sender/includes
 * @author     Bishal Saha <bishal.saha@gmail.com>
 */
class Push_Notification_Sender_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {


		update_option( 'pns_on_new_post_publish', '' );
		update_option( 'pns_on_new_page_save', '' );
		update_option( 'pns_on_new_user_register', '' );
		update_option( 'pns_on_new_comment_post', '' );

		update_option( 'pns_send_to_android', '' );
		update_option( 'pns_send_to_ios', '' );
		update_option( 'pns_send_via_production', '' );
		update_option( 'pns_send_via_sandbox', '' );

		update_option( 'pns_ios_certificate_path', '' );
		update_option( 'pns_ios_certificate_name', '' );
		update_option( 'pns_google_api_key', '' );
		update_option( 'pns_send_to_android_via', '' );

	}

}
