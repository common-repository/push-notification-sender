<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://gentryx.com/bishalsaha
 * @since             1.0.0
 * @package           Push_Notification_Sender
 *
 * @wordpress-plugin
 * Plugin Name:       Push Notification Sender
 * Plugin URI:        http://gentryx.com/wp-plugin/push-notification-sender
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Bishal Saha
 * Author URI:        http://gentryx.com/bishalsaha
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       push-notification-sender
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-push-notification-sender-activator.php
 */
function activate_push_notification_sender() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-push-notification-sender-activator.php';
	Push_Notification_Sender_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-push-notification-sender-deactivator.php
 */
function deactivate_push_notification_sender() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-push-notification-sender-deactivator.php';
	Push_Notification_Sender_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_push_notification_sender' );
register_deactivation_hook( __FILE__, 'deactivate_push_notification_sender' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-push-notification-sender.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_push_notification_sender() {

	$plugin = new Push_Notification_Sender();
	$plugin->run();

}

run_push_notification_sender();
