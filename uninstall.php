<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       http://gentryx.com/bishalsaha
 * @since      1.0.0
 *
 * @package    Push_Notification_Sender
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$table_logs   = $wpdb->prefix . 'push_notification_sender_logs';
$table_tokens = $wpdb->prefix . 'push_notification_sender_tokens';

//Delete saved tables
$wpdb->query( "DROP TABLE IF EXISTS $table_logs" );
$wpdb->query( "DROP TABLE IF EXISTS $table_tokens" );

$current_url = "//" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$phpself_url = $_SERVER['PHP_SELF'];

// Delete saved options.
delete_option( 'pns_on_new_post_publish' );
delete_option( 'pns_on_new_page_save' );
delete_option( 'pns_on_new_user_register' );
delete_option( 'pns_on_new_comment_post' );

delete_option( 'pns_send_to_android' );
delete_option( 'pns_send_to_ios' );
delete_option( 'pns_send_via_production' );
delete_option( 'pns_send_via_sandbox' );

delete_option( 'pns_ios_certificate_path' );
delete_option( 'pns_ios_certificate_name' );
delete_option( 'pns_google_api_key' );
delete_option( 'pns_send_to_android_via' );


