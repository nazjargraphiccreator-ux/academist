<?php
/* ============================================================
   5. PLUGIN TEMPLATE OVERRIDE PATH
   ============================================================ */
// Allow the membership plugin's template to be overridden in child theme
add_filter( 'academist_membership_filter_templates_dir', function( $dir ) {
	$child_dir = get_stylesheet_directory() . '/academist-membership/';
	return file_exists( $child_dir ) ? $child_dir : $dir;
});add_action('wp_head', function() {
    if ( class_exists('WooCommerce') && is_user_logged_in() ) {
        echo '<script type="text/javascript">
        var rima_ajax_obj = {
            "ajax_url": "' . esc_url(admin_url('admin-ajax.php')) . '",
            "nonce": "' . wp_create_nonce('rima_nonce') . '"
        };
        </script>';
    }
});
add_action('wp_footer', function() {
    if ( class_exists('WooCommerce') && is_user_logged_in() ) {
        ?>
        <script>
        jQuery(document).ready(function($) {
            // Bind to both avatar upload inputs (sidebar and edit account form)
            $('#rima-avatar-upload, #avatar-upload-input').on('change', function() {
                var file = this.files[0];
                if (!file) return;

                var formData = new FormData();
                formData.append('avatar', file);
                formData.append('action', 'academist_child_upload_avatar');
                formData.append('nonce', rima_ajax_obj.nonce);

                var statusDiv = $('#avatar-upload-status');
                if(statusDiv.length) {
                    statusDiv.show().text('Uploading...').removeClass('text-danger text-success').addClass('text-info');
                }

                $.ajax({
                    url: rima_ajax_obj.ajax_url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if(response.success) {
                            $('#edit-avatar-preview').css('background-image', 'url(' + response.data.url + ')');
                            $('#user-avatar-display').css('background-image', 'url(' + response.data.url + ')');
                            if(statusDiv.length) {
                                statusDiv.text('Avatar updated!').removeClass('text-info text-danger').addClass('text-success');
                                setTimeout(() => statusDiv.fadeOut(), 3000);
                            }
                        } else {
                            if(statusDiv.length) {
                                statusDiv.text('Error: ' + response.data).removeClass('text-info').addClass('text-danger');
                            } else {
                                alert('Error uploading avatar: ' + response.data);
                            }
                        }
                    },
                    error: function() {
                        if(statusDiv.length) {
                            statusDiv.text('Network error.').removeClass('text-info').addClass('text-danger');
                        } else {
                            alert('Network error while uploading avatar.');
                        }
                    }
                });
            });
        });
        </script>
        <?php
    }
}, 100);

