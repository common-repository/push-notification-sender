<?php

/**
 * Fired during plugin activation
 *
 * @link       http://gentryx.com/bishalsaha
 * @since      1.0.0
 *
 * @package    Push_Notification_Sender
 * @subpackage Push_Notification_Sender/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Push_Notification_Sender
 * @subpackage Push_Notification_Sender/includes
 * @author     Bishal Saha <bishal.saha@gmail.com>
 */
class Push_Notification_Sender_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$table_logs      = $wpdb->prefix . 'push_notification_sender_logs';
		$table_tokens    = $wpdb->prefix . 'push_notification_sender_tokens';
		$charset_collate = $wpdb->get_charset_collate();

		$table_logs_sql = "CREATE TABLE $table_logs (
			`log_id` int(11) NOT NULL AUTO_INCREMENT,
			`push_title` text NOT NULL,
			`push_message` text NOT NULL,
			`push_sent` tinyint(4) NOT NULL,
			`push_send_date` datetime NOT NULL,
			`token_id` text NOT NULL,
			PRIMARY KEY (`log_id`)
		) $charset_collate;";
		dbDelta( $table_logs_sql );

		$table_tokens_sql = "CREATE TABLE $table_tokens (
			`token_id` int(11) NOT NULL AUTO_INCREMENT,
			`device_token` text NOT NULL,
			`os_type` varchar(10) NOT NULL,
			`user_email_id` varchar(100) NOT NULL,
			`user_id` int(11) NOT NULL,
			`last_updatedate` datetime NOT NULL,
			PRIMARY KEY (`token_id`)
		) $charset_collate;";
		dbDelta( $table_tokens_sql );

		// Add Custom Upload folder in wp-upload folder to upload the ios certificate.
		$upload_dir   = wp_upload_dir();
		$userdir      = 'pns-ioscert';
		$user_dirname = $upload_dir['basedir'] . '/' . $userdir;

		if ( ! file_exists( $user_dirname ) ) {
			wp_mkdir_p( $user_dirname );
		}

	}

}
