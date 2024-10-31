(function () {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
	 *
	 * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
	 *
	 * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */

})(jQuery);


jQuery(document).ready(function () {
    // validate signup form on keyup and submit
    jQuery("#pns_os_setting").validate({
        rules: {
            pns_upload_ios_certi: "required",
            pns_push_to_ios: "required",
            pns_send_to_ios: "required"
        },
        messages: {
            pns_upload_ios_certi: "Please Upload Valid file",
            pns_push_to_ios: "Please Select Option",
            pns_send_to_ios: "Please Select option for send notifications to ios devices"
        },
        errorPlacement: function (error, element) {
            jQuery(element).closest('tr').next().find('.error_label').html(error);
        }
    });

    // Uploading pem file
    var file_frame;
    jQuery('#pns_upload_image_button').on('click', function (event) {
        event.preventDefault();

        // If the media frame already exists, reopen it.
        if (file_frame) {
            file_frame.open();
            return;
        }

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: jQuery(this).data('uploader_title'),
            button: {
                text: jQuery(this).data('uploader_button_text')
            },
            multiple: false  // Set to true to allow multiple files to be selected
        });

        // When an image is selected, run a callback.
        file_frame.on('select', function () {
            var attachment = file_frame.state().get('selection').first().toJSON();
            jQuery('#pns_upload_ios_certi').val(attachment.url);
            jQuery('#pns_upload_ios_certi_name').val(attachment.filename);

        });

        // Finally, open the modal
        file_frame.open();
    });

});