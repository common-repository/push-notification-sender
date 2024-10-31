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
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $wp, $wpdb;

require_once plugin_dir_path( dirname( __FILE__ ) ) . '/api/class-push-notification-sender-api.php';

$error         = false;
$error_ios     = false;
$error_android = false;
$is_submitted  = false; // checks whether form submitted or not.

if ( isset( $_POST['send_now_button'] ) ) {
	if ( ! wp_verify_nonce( $_POST['apn_custom_message'], 'custom_message' ) ) {
		print 'Sorry, your nonce did not verify.';
		//exit;
	} else {
		$is_submitted = true;

		if ( isset( $_POST['selected_user'] ) ) {
			$selected_users_id = wp_unslash( $_POST['selected_user'] );  // Do not sanitize array data.
		} else {
			$selected_users_id = array();
		}

		if ( isset( $_POST['only_ios'] ) ) {
			$only_ios = sanitize_text_field( $_POST['only_ios'] );
		} else {
			$only_ios = '';
		}

		if ( isset( $_POST['only_android'] ) ) {
			$only_android = sanitize_text_field( $_POST['only_android'] );
		} else {
			$only_android = '';
		}

		$table_push_notification_sender_token = $wpdb->prefix . 'push_notification_sender_tokens';
		$all_device_tokens                    = $wpdb->get_results( "SELECT device_token FROM $table_push_notification_sender_token" );


		if ( ( ! empty( $selected_users_id ) ) && ( ! empty( $all_device_tokens ) ) ) {
			$all_user_devices = array();
			$message          = array(
				"message" => sanitize_textarea_field( $_POST['message_text'] ),
				"title"   => sanitize_text_field( $_POST['msg_title'] )
			);

			foreach ( $selected_users_id as $selected_user_id ) {
				$user_data_array = $wpdb->get_results( "SELECT device_token, os_type FROM `$table_push_notification_sender_token` where user_id=" . $selected_user_id );

				foreach ( $user_data_array as $user_data ) {
					if ( ! empty( $user_data->os_type ) ) {
						$device_type = $user_data->os_type;
					} else {
						$device_type = '';
					}
					if ( ! empty( $user_data->device_token ) ) {
						$device_token = $user_data->device_token;
					} else {
						$device_token = '';
					}

					if ( 'android' === $device_type && '' !== $only_android ) {
						array_push(
							$all_user_devices,
							array(
								'token'      => $device_token,
								'is_Android' => true
							)
						);
					} elseif ( $device_type == 'ios' && $only_ios != '' ) {
						array_push( $all_user_devices, array( 'token' => $device_token, 'is_IOS' => true ) );
					}
				}
			}

			if ( ! empty( $only_ios ) ) {

				$pns_ios_certi_name = get_option( 'pns_ios_certi_name' );
				if ( empty( $pns_ios_certi_name ) || strlen( $pns_ios_certi_name ) <= 0 ) {
					$error_ios = true;
				}
			}

			if ( ! empty( $only_android ) ) {
				$pns_google_api_key = get_option( 'pns_google_api_key' );

				if ( empty( $pns_google_api_key ) || strlen( $pns_google_api_key ) <= 0 ) {
					$error_android = true;
				}
			}

			if ( $error == false ) {
				$push_notification_sender_api_obj = new Push_Notification_Sender_API();
				$reg_id_chunk                     = array_chunk( $all_user_devices, 100 );

				foreach ( $reg_id_chunk as $reg_id ) {
					$push_notification_sender_api_obj->send_notification( $reg_id, $message );
				}
			}
		} else {

			$message = __( 'There was an error sending push notification message, There is no device tokens in table.' );
			printf( "<p class='error'>%s</p>", $message );
		}
	}
}
$pns_users = $wpdb->prefix . 'users';
$all_users = $wpdb->get_results( "SELECT ID, user_login, user_nicename FROM $pns_users" );
?>
<script>
    jQuery(document).ready(function () { // initialize the pqSelect widget.
        jQuery("#selected_user").pqSelect({
            multiplePlaceholder: 'Select User',
            checkbox: true // adds checkbox to options.
        }).on("change", function (evt) {
            var val = jQuery(this).val();
        }).pqSelect('close');

        // validate signup form on keyup and submit
        jQuery("#custom_push_notification_form").validate({
            ignore: '',
            rules: {
                'selected_user[]': {
                    required: true
                },
                message_text: {
                    required: true,
                    maxlength: 235
                },
                msg_title: "required",
                only_ios: {
                    required: function () {
                        if (jQuery("#only_android").prop('checked')) {
                            return false;
                        }
                        return true;
                    }
                },
                only_android: {
                    required: function () {
                        if (jQuery("#only_ios").prop('checked')) {
                            return false;
                        }
                        return true;
                    }
                }
            },
            messages: {
                'selected_user[]': "Please Select Users",
                msg_title: "Please enter your Message title",
                message_text: {
                    required: "Please enter a Message",
                    minlength: "Your Message Must not be more than 235 characters"
                }
            },
            errorPlacement: function (error, element) {
                jQuery(element).closest('tr').next().find('.error_label').html(error);
            }
        });

    });
</script>
<div class="wrap">
    <h2><?php _e( 'Send custom push notification to an user.', 'push-notifications-sender' ); ?></h2>
    <div id="poststuff" class="metabox-holder">
        <div class="sm-padded">
            <div id="post-body-content">
                <div class="meta-box-sortabless">
                    <form name="custom_push_notification_form" action="" id="custom_push_notification_form"
                          method="post">
						<?php wp_nonce_field( 'custom_message', 'apn_custom_message' ); ?>
						<?php if ( $error_ios == true && $error_android == false ) { ?>
                            <div id="message" class="notice notice-error is-dismissible">
                                <p>
									<?php
									$message = __( 'There was an error sending push notification message to ios mobile devices, please check Settings page and verify PEM certificate file for APNs.' );
									printf( "<p class='error'>%s</p>", $message );
									?>
                                </p>
                            </div>
						<?php } else if ( $error_android == true && $error_ios == false ) { ?>
                            <div id="message" class="notice notice-error is-dismissible">
                                <p>
									<?php
									$message = __( 'There was an error sending push notification message to android mobile devices, please check Settings page and verify GCM / FCM Key.' );
									printf( "<p class='error'>%s</p>", $message );
									?>
                                </p>
                            </div>
						<?php } else if ( $error_android == true && $error_ios == true ) { ?>
                            <div id="message" class="notice notice-error is-dismissible">
                                <p>
									<?php
									$message = __( 'There was an error sending push notification message to mobile devices, please check Settings page and verify PEM certificate file for APNs and also verify GCM / FCM Key.' );
									printf( "<p class='error'>%s</p>", $message );
									?>
                                </p>
                            </div>
						<?php } else if ( $error_ios == false && $error_android == false && $is_submitted == true ) { ?>
                            <div id="message" class="notice notice-success is-dismissible">
                                <p>
									<?php
									$message = __( 'The message send successfully. please check the push notification in device.' );
									printf( "<p class='suceess' style='color:green'>%s</p>", $message );
									?>
                                </p>
                            </div>
						<?php } ?>
                        <div class="postbox">

                            <h3 class="hndle">
                                <span><?php _e( 'Custom Message', 'push-notifications-sender' ); ?></span>
                            </h3>
                            <div class="inside">
                                <table>
                                    <tr>
                                        <td><?php _e( 'Select Users' ) ?>:</td>
                                        <td>
                                            <select title="Select User" id="selected_user" name="selected_user[]"
                                                    multiple=multiple style="margin: 20px;width:300px;" required>
												<?php
												foreach ( $all_users as $user ) {
													echo '<option value=' . $user->ID . '>' . $user->user_nicename . '</option>';
												}
												?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td><span class="error_label"></span></td>
                                    </tr>
                                    <tr>
                                        <td><?php _e( 'Message Title' ) ?>:</td>
                                        <td><input title="Message Title" type="text" name="msg_title" required></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td><span class="error_label"></span></td>
                                    </tr>
                                    <tr>
                                        <td><?php _e( 'Message Text' ) ?>:</td>
                                        <td><textarea title="Message" rows="5" cols="25" name="message_text"
                                                      required></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td><span class="error_label"></span></td>
                                    </tr>
                                    <tr>
                                        <td><?php _e( 'Send Push Notifications To' ) ?>:</td>
                                        <td>
                                            <input type="checkbox" name="only_ios" value="yes" id="only_ios"
                                                   class="checkBox_class">
                                            <label for="only_ios"><?php _e( 'iOS devices' ) ?></label>
                                            <br>
                                            <input type="checkbox" name="only_android" value="yes" id="only_android"
                                                   class="checkBox_class">
                                            <label for="only_android"><?php _e( 'Android devices' ) ?></label><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td><span class="error_label"></span></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>
                                            <span><?php _e( 'Note: Please make sure you have done valid setting under "All Settings" menu.' ) ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="submit" value="<?php _e( 'Send Now' ) ?>"
                                                   name="send_now_button" id="send_now_button"
                                                   class="button button-primary">
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
