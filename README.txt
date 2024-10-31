=== Push Notification Sender for WP ===
Contributors: bishalsaha
Donate link: http://gentryx.com/bishalsaha
Tags: push notification sender, send push notification, send to android, send to iphone, send to mobile, push notification for android, push notification for ios, push notification for iphone, push notification for mobile, google cloud nessaging, firebase cloud nessaging, android notification, ios notification, iphone notification , on new post published, on new page published, on new comment post, send custom message to wp User
Requires at least: 3.0.1
Tested up to: 4.7
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easiest way to launch push notification from your WordPress website to iOs and Android devices. Ready to go, no third party any integration required.

== Description ==

Launch push notification to all iOS and Android devices automatically when an Add/Edit a Post/Page and even when a new comment is added to any post. No any third party software integration required. You can also send a custom push notification to any individual registered member.

== Key Features ==
Supports following methods

* Apple Push Notification service (APNs)
* Google Cloud Messaging (GCM)
* Firebase Cloud Messaging (FCM)

This plugin have options to:

1. Launch push notification to WordPress users separately.
2. Launch push notification to users when a new page/post is published or when new comment is added to the post (administrator user)

== Required Settings ==
To launch push notification to android devices, you need to enter the Google GCM API Key

To launch push notification to iOS devices, you need to upload the Apple APNs pem certification file.

This plugin have a separate API to register any devices to receive push notification. You may use this API in your mobile application and send the token to API to register the device.

* WordPress 4.4 or greater
* PHP version 5.2.4 or greater
* MySQL version 5.0 or greater

== Installation ==

1. Download the plugin as zip file from the plugin download link.
2. Extract the zip file and upload the folder push-notification-sender to `/wp-content/plugins/` directory
3. Activate the plugin from admin panel plugin listing screen.
4. You can now configure plugin from setting screen which appears in admin menu.

== Screenshots ==

1. Admin Settings page.
2. Admin Send custom push notification manually.
3. List of Sent notifications in Admin Panel.

== Changelog ==

= 1.0 =
* First public release.

== Upgrade Notice ==
= 1.0 =
* First public release.

== Translations ==

* English - default, always included