<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://gentryx.com/bishalsaha
 * @since      1.0.0
 *
 * @package    Push_Notification_Sender
 * @subpackage Push_Notification_Sender/admin/partials
 */

/**
 * Plugin class that is used to render the list table
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'class-push-notification-sender-list-table.php';

// Create an instance of our package class...
$push_notification_sender_list_table = new Push_Notification_Sender_List_Table();

$push_notification_sender_list_table->prepare_items();

?>
<div class="wrap">
    <h2><?php esc_html_e( 'All Push Notifications' ); ?></h2>
    <p>
        <?php
            if ( 'send' === isset( $_GET['notifications'] ) ) {
			    echo _e('Notifications Sent.');
            }
        ?>
    </p>
    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
    <form id="push_notification_sender_list_table" action="" method="get">
        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <!-- Now we can render the completed list table -->
		<?php $push_notification_sender_list_table->display(); ?>
    </form>
</div>
