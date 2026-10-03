<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * RIMA Academy — Secure PDF Server
 * Serves PDFs only to authenticated users who purchased the course.
 */

// AJAX handler — serve PDF Viewer HTML (the new iframe app)
add_action( 'wp_ajax_rima_view_pdf', 'rima_handle_pdf_viewer_request' );
function rima_handle_pdf_viewer_request() {
    check_ajax_referer( 'rima_nonce', 'nonce' );

    if ( ! is_user_logged_in() ) {
        wp_die( __( 'Nu ești autentificat.', 'rima-academy' ), 403 );
    }

    $attachment_id = absint( $_GET['doc'] ?? 0 );
    $course_id     = absint( $_GET['course'] ?? 0 );

    if ( ! $attachment_id || ! $course_id ) {
        wp_die( __( 'Parametri lipsă.', 'rima-academy' ), 400 );
    }

    // Verify access
    if ( ! rima_user_has_course( $course_id ) ) {
        wp_die( __( 'Nu ai acces la acest document. Cumpără cursul.', 'rima-academy' ), 403 );
    }

    // Include the HTML viewer app
    require get_stylesheet_directory() . '/templates/pdf-viewer-app.php';
    exit;
}

// AJAX handler — serve actual PDF binary to the PDF.js viewer
add_action( 'wp_ajax_rima_view_pdf_data', 'rima_handle_pdf_data_request' );
function rima_handle_pdf_data_request() {
    check_ajax_referer( 'rima_nonce', 'nonce' );

    if ( ! is_user_logged_in() ) {
        wp_die( __( 'Nu ești autentificat.', 'rima-academy' ), 403 );
    }

    $attachment_id = absint( $_GET['doc'] ?? 0 );
    $course_id     = absint( $_GET['course'] ?? 0 );

    // Quick verify
    if ( ! rima_user_has_course( $course_id ) ) {
        wp_die( __( 'Acces interzis.', 'rima-academy' ), 403 );
    }

    $file_path = get_attached_file( $attachment_id );
    if ( ! $file_path || ! file_exists( $file_path ) ) {
        wp_die( __( 'Fișierul nu a fost găsit.', 'rima-academy' ), 404 );
    }

    // Serve binary
    header( 'Content-Type: application/pdf' );
    header( 'Content-Length: ' . filesize( $file_path ) );
    header( 'Cache-Control: private, no-store' );
    header( 'Pragma: no-cache' );
    header( 'X-Content-Type-Options: nosniff' );

    readfile( $file_path );
    exit;
}

// AJAX handler - Save PDF Progress and Time
add_action( 'wp_ajax_rima_save_pdf_progress', 'rima_save_pdf_progress' );
function rima_save_pdf_progress() {
    check_ajax_referer( 'rima_pdf_track', 'nonce' );

    if ( ! is_user_logged_in() ) {
        wp_send_json_error();
    }

    $user_id      = get_current_user_id();
    $course_id    = absint( $_POST['course_id'] ?? 0 );
    $doc_id       = absint( $_POST['doc_id'] ?? 0 );
    $current_page = absint( $_POST['current_page'] ?? 1 );
    $total_pages  = absint( $_POST['total_pages'] ?? 1 );
    $time_added   = absint( $_POST['session_time_added'] ?? 0 ); // in seconds

    if ( ! $course_id || ! $doc_id ) {
        wp_send_json_error( 'Invalid data' );
    }

    $meta_key = '_rima_pdf_progress_' . $course_id . '_' . $doc_id;
    $progress = get_user_meta( $user_id, $meta_key, true );

    $product_id = get_post_meta( $course_id, 'eltdf_course_woo_product_meta', true );
    $min_days = get_post_meta( $product_id, '_rima_min_study_days', true );
    $daily_time_limit_mins = get_post_meta( $product_id, '_rima_daily_time_limit', true );
    $today = date('Y-m-d');
    
    if ( ! is_array( $progress ) ) {
        $progress = array(
            'highest_page' => 1,
            'total_pages'  => $total_pages,
            'time_spent'   => 0,
            'completed'    => false,
            'last_updated' => time(),
            'last_study_date' => $today,
            'daily_time_spent' => 0,
            'start_page_today' => 1,
            'daily_pages_read' => 0
        );
    }
    
    // Initialize or reset daily stats
    if ( empty($progress['last_study_date']) || $progress['last_study_date'] !== $today ) {
        $progress['last_study_date'] = $today;
        $progress['daily_time_spent'] = 0;
        $progress['start_page_today'] = $progress['highest_page'];
        $progress['daily_pages_read'] = 0;
    }

    // Update highest page reached
    if ( $current_page > $progress['highest_page'] ) {
        $progress['highest_page'] = $current_page;
    }
    $progress['total_pages'] = $total_pages;

    // Accumulate time
    if ( $time_added > 0 ) {
        $progress['time_spent'] += $time_added;
        $progress['daily_time_spent'] += $time_added;
    }
    
    // Accumulate pages read today
    $pages_read_today = $progress['highest_page'] - $progress['start_page_today'];
    $progress['daily_pages_read'] = max(0, $pages_read_today);
    
    $limit_reached = false;
    $limit_reason = '';
    
    // 1. Check Daily Time Limit
    if ( $daily_time_limit_mins > 0 && ($progress['daily_time_spent'] >= ($daily_time_limit_mins * 60)) ) {
        $limit_reached = true;
        $limit_reason = 'Ai atins limita zilnică de timp (' . $daily_time_limit_mins . ' minute). Profesorul RIMA te așteaptă mâine!';
    }
    
    // 2. Check Dynamic Page Limit
    if ( ! $limit_reached && $min_days > 0 && $total_pages > 0 ) {
        $max_pages_per_day = ceil( $total_pages / $min_days );
        if ( $progress['daily_pages_read'] >= $max_pages_per_day ) {
            // They reached the limit. If they try to go to a page higher than what they are allowed today:
            if ( $current_page > ($progress['start_page_today'] + $max_pages_per_day - 1) ) {
                $limit_reached = true;
                $limit_reason = 'Ai atins limita de pagini pentru astăzi (' . $max_pages_per_day . ' pagini). Învățatul eficient se face în ritm constant, pe parcursul a minim ' . $min_days . ' zile!';
            }
        }
    }

    // Check if completed
    $course_just_completed = false;
    if ( $progress['highest_page'] >= $total_pages && $total_pages > 0 && ! $progress['completed'] ) {
        $progress['completed'] = true;
        
        // --- HOOK FOR LMS COURSE COMPLETION ---
        do_action('rima_pdf_course_completed', $user_id, $course_id, $doc_id);
        $course_just_completed = true;
    }
    
    $progress['last_updated'] = time();

    // Do not save the new page if limit is reached to prevent cheating
    if ( $limit_reached ) {
        $progress['highest_page'] = max(1, $current_page - 1);
    }
    
    update_user_meta( $user_id, $meta_key, $progress );

    wp_send_json_success( array(
        'saved' => true,
        'course_completed' => $course_just_completed,
        'limit_reached' => $limit_reached,
        'limit_reason' => $limit_reason,
        'course_url' => get_permalink($course_id)
    ) );
}

/**
 * Generate secure PDF view URL (Used by iframe)
 */
function rima_get_pdf_url( $attachment_id, $course_id ) {
    return add_query_arg( array(
        'action' => 'rima_view_pdf',
        'doc'    => $attachment_id,
        'course' => $course_id,
        'nonce'  => wp_create_nonce('rima_nonce'),
    ), admin_url('admin-ajax.php') );
}

/**
 * Check if a user has completed all PDFs for a specific course
 */
function rima_has_completed_course_pdfs( $course_id, $user_id = null ) {
    if ( ! $user_id ) $user_id = get_current_user_id();
    
    $product_id = get_post_meta( $course_id, 'eltdf_course_woo_product_meta', true );
    if ( ! $product_id ) return true; // No product linked, so no PDFs
    
    $docs = get_post_meta( $product_id, '_rima_course_documents', true );
    if ( ! is_array($docs) || empty($docs) ) {
        return true; // No PDFs, consider completed
    }

    foreach ( $docs as $doc ) {
        $attachment_id = $doc['attachment_id'];
        $meta_key = '_rima_pdf_progress_' . $course_id . '_' . $attachment_id;
        $progress = get_user_meta( $user_id, $meta_key, true );
        
        if ( ! is_array( $progress ) || empty( $progress['completed'] ) ) {
            return false; // At least one PDF is not completed
        }
    }
    
    return true;
}

// =========================================================================
// PDF REPORTS ADMIN PAGE
// =========================================================================

add_action('admin_menu', 'rima_pdf_reports_admin_menu', 60);

function rima_pdf_reports_admin_menu() {
    // Add it as a submenu to the rima-academy-settings if it exists, otherwise to general settings
    add_submenu_page(
        'rima-academy-settings',
        __('Rapoarte PDF-uri', 'rima-academy'),
        __('Rapoarte PDF-uri', 'rima-academy'),
        'manage_options',
        'rima-pdf-reports',
        'rima_pdf_reports_page_html'
    );
}

function rima_pdf_reports_page_html() {
    if (!current_user_can('manage_options')) return;
    global $wpdb;
    
    // Fetch latest PDF progress
    $results = $wpdb->get_results("SELECT user_id, meta_key, meta_value FROM {$wpdb->usermeta} WHERE meta_key LIKE '_rima_pdf_progress_%' ORDER BY umeta_id DESC LIMIT 200");
    ?>
    <div class="wrap rima-crm-hub">
        <h1 style="color: #17325c; margin-bottom: 20px;">Rapoarte Parcurgere PDF-uri</h1>
        <p>Aici poți vedea progresul cursanților la citirea materialelor PDF din cursuri.</p>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Cursant</th>
                    <th>Curs</th>
                    <th>Document PDF</th>
                    <th>Status Parcurgere</th>
                    <th>Pagina Curentă</th>
                    <th>Timp Petrecut</th>
                    <th>Acțiuni</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty($results) ) : ?>
                    <tr><td colspan="7">Nu există date despre progresul studenților.</td></tr>
                <?php else : ?>
                    <?php 
                    foreach ($results as $row) {
                        $user = get_userdata($row->user_id);
                        $parts = explode('_', str_replace('_rima_pdf_progress_', '', $row->meta_key));
                        $course_id = isset($parts[0]) ? intval($parts[0]) : 0;
                        $doc_id = isset($parts[1]) ? intval($parts[1]) : 0;
                        
                        $data = maybe_unserialize($row->meta_value);
                        if (!is_array($data)) continue;
                        
                        $course_title = get_the_title($course_id);
                        $doc_title = get_the_title($doc_id);
                        $time_spent = isset($data['time_spent']) ? intval($data['time_spent']) : 0;
                        $minutes = floor($time_spent / 60);
                        $seconds = $time_spent % 60;
                        
                        $completed = !empty($data['completed']) ? '<span style="color:green;font-weight:bold;">Finalizat</span>' : '<span style="color:orange;font-weight:bold;">În Progres</span>';
                        $page = isset($data['page']) ? intval($data['page']) : 1;
                        $total_pages = isset($data['total_pages']) ? intval($data['total_pages']) : '?';
                        
                        echo '<tr>';
                        echo '<td>' . esc_html($user ? $user->display_name : 'Unknown') . '</td>';
                        echo '<td><a href="'.get_edit_post_link($course_id).'">' . esc_html($course_title) . '</a></td>';
                        echo '<td><a href="'.wp_get_attachment_url($doc_id).'" target="_blank">' . esc_html($doc_title) . '</a></td>';
                        echo '<td id="status-'.$row->user_id.'-'.$course_id.'-'.$doc_id.'">' . $completed . '</td>';
                        echo '<td>' . $page . ' / ' . $total_pages . '</td>';
                        echo '<td>' . sprintf("%02d:%02d", $minutes, $seconds) . ' (Min:Sec)</td>';
                        
                        // Actions
                        echo '<td>';
                        echo '<button type="button" class="button button-small" onclick="rima_update_pdf_status('.$row->user_id.', '.$course_id.', '.$doc_id.', \'complete\')">M. Finalizat</button> ';
                        echo '<button type="button" class="button button-small" onclick="rima_update_pdf_status('.$row->user_id.', '.$course_id.', '.$doc_id.', \'reset\')">Reset</button>';
                        echo '</td>';
                        
                        echo '</tr>';
                    }
                    ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script>
    function rima_update_pdf_status(user_id, course_id, doc_id, action_type) {
        if (!confirm('Ești sigur că vrei să modifici manual progresul?')) return;
        jQuery.post(ajaxurl, {
            action: 'rima_admin_update_pdf_progress',
            user_id: user_id,
            course_id: course_id,
            doc_id: doc_id,
            action_type: action_type,
            nonce: '<?php echo wp_create_nonce("rima_admin_pdf_nonce"); ?>'
        }, function(res) {
            if (res.success) {
                alert('Status modificat cu succes!');
                location.reload();
            } else {
                alert('Eroare: ' + res.data);
            }
        });
    }
    </script>
    <?php
}

add_action('wp_ajax_rima_admin_update_pdf_progress', 'rima_ajax_admin_update_pdf_progress');
function rima_ajax_admin_update_pdf_progress() {
    check_ajax_referer('rima_admin_pdf_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('No permission');
    
    $user_id = intval($_POST['user_id']);
    $course_id = intval($_POST['course_id']);
    $doc_id = intval($_POST['doc_id']);
    $action_type = sanitize_text_field($_POST['action_type']);
    
    $meta_key = '_rima_pdf_progress_' . $course_id . '_' . $doc_id;
    
    if ($action_type === 'reset') {
        delete_user_meta($user_id, $meta_key);
    } else if ($action_type === 'complete') {
        $progress = get_user_meta($user_id, $meta_key, true);
        if (!is_array($progress)) $progress = array();
        $progress['completed'] = true;
        update_user_meta($user_id, $meta_key, $progress);
    }
    
    wp_send_json_success();
}

/**
 * Hook to hide the Academist LMS Complete button if PDFs are not read.
 * This runs on the frontend lesson/course pages.
 */
add_action( 'wp_footer', 'rima_block_lms_completion_if_pdf_unread', 99 );
function rima_block_lms_completion_if_pdf_unread() {
    // Only run on Academist LMS single items (lesson, quiz, course)
    if ( ! is_singular( array('course', 'lesson', 'quiz') ) ) {
        return;
    }

    // Get the course ID associated with this lesson/quiz
    $course_id = 0;
    if ( is_singular('course') ) {
        $course_id = get_the_ID();
    } else {
        $course_id = get_post_meta( get_the_ID(), 'eltdf_course_id_meta', true );
    }

    if ( ! $course_id || rima_has_completed_course_pdfs( $course_id ) ) {
        return; // All good, do nothing
    }

    // If we reach here, PDFs are NOT completed. Inject JS/CSS to hide the complete form.
    ?>
    <style>
        .eltdf-lms-complete-item-form { display: none !important; }
        .rima-pdf-warning-msg {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-left: 4px solid #ffeeba;
            margin: 20px 0;
            font-weight: 600;
            border-radius: 4px;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var forms = document.querySelectorAll('.eltdf-lms-complete-item-form');
            forms.forEach(function(form) {
                var warning = document.createElement('div');
                warning.className = 'rima-pdf-warning-msg';
                warning.innerHTML = '<i class="fa fa-exclamation-triangle"></i> Trebuie să citești integral materialele PDF ale acestui curs din Dashboard pentru a putea finaliza lecțiile.';
                form.parentNode.insertBefore(warning, form);
            });
        });
    </script>
    <?php
}
