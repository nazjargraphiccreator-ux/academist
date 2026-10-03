<?php
/**
 * My Courses – RIMA Academy (Bilingual EN/RO)
 */
if ( ! defined('ABSPATH') ) exit;

$courses = function_exists('rima_get_user_courses') ? rima_get_user_courses() : array();
$nonce   = wp_create_nonce('rima_nonce');
?>

<div class="card border-0 shadow-sm rounded-4 mb-4 rima-dashboard-content">
    <div class="card-body p-4 p-md-5">

        <!-- Header -->
        <div class="d-flex align-items-center gap-3 mb-5 pb-3 border-bottom">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:50px;height:50px;background:linear-gradient(135deg,#102d56,#102d56);">
                <i class="fa fa-graduation-cap text-white fs-4"></i>
            </div>
            <div>
                <h3 class="h4 fw-bold mb-1 text-dark">
                    <span class="rima-en">My Courses</span>
                    <span class="rima-ro">Cursurile Mele</span>
                </h3>
                <p class="small text-muted mb-0">
                    <span class="rima-en">Your purchased courses. Instant access to materials and Zoom sessions.</span>
                    <span class="rima-ro">Cursurile achiziționate. Acces instant la materiale și sesiuni Zoom.</span>
                </p>
            </div>
        </div>

        <?php if ( empty($courses) ) : ?>
            <div class="text-center py-5 px-3">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                     style="width:100px;height:100px;">
                    <i class="fa fa-book text-muted opacity-50" style="font-size:40px;"></i>
                </div>
                <h4 class="fw-bold mb-3 text-dark">
                    <span class="rima-en">You are not enrolled in any courses</span>
                    <span class="rima-ro">Nu ești înscris la niciun curs</span>
                </h4>
                <p class="text-muted mb-4 mx-auto" style="max-width:400px;">
                    <span class="rima-en">You have no active course orders at the moment. Explore our collection and start your learning journey today.</span>
                    <span class="rima-ro">Nu ai nicio comandă activă pentru cursuri. Explorează colecția noastră și începe-ți călătoria de învățare astăzi.</span>
                </p>
                <a href="<?php echo esc_url(site_url('/our-courses/')); ?>"
                   class="btn text-white border-0 px-4 py-3 fw-bold shadow-sm rounded-3 text-uppercase"
                   style="background:linear-gradient(135deg,#102d56,#102d56);letter-spacing:1px;">
                    <i class="fa fa-search me-2"></i>
                    <span class="rima-en">Discover Courses</span>
                    <span class="rima-ro">Descoperă Cursuri</span>
                </a>
            </div>

        <?php else : ?>
        <div class="row g-4">
        <?php foreach ($courses as $course) :
            $is_active  = ($course['order_status'] === 'completed');
            $is_pending = in_array($course['order_status'], array('pending','on-hold','processing'));
            $is_bank    = ($course['payment_method'] === 'bacs');
            $has_proof  = ! empty($course['payment_proof_url']);
            $has_docs   = ! empty($course['docs']);
            $has_zoom   = ! empty($course['zoom']);
            $order_id   = $course['order_id'];
            $img        = $course['image'] ?: 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'300\' height=\'200\'%3E%3Crect fill=\'%23f8f9fa\' width=\'300\' height=\'200\'/%3E%3C/svg%3E';
        ?>
        <div class="col-md-6 col-xl-4 d-flex">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden w-100">

                <!-- Image -->
                <div class="position-relative bg-light" style="height:180px;">
                    <img src="<?php echo esc_url($img); ?>"
                         alt="<?php echo esc_attr($course['title']); ?>"
                         class="w-100 h-100" style="object-fit:cover;">
                    <!-- Status Badge -->
                    <div class="position-absolute top-0 end-0 p-2">
                        <?php if ($is_active) : ?>
                            <span class="badge px-2 py-1" style="background:#dcfce7;color:#15803d;font-size:11px;">
                                <i class="fa fa-check-circle me-1"></i>
                                <span class="rima-en">Active</span>
                                <span class="rima-ro">Activ</span>
                            </span>
                        <?php elseif ($is_pending && $is_bank && $has_proof) : ?>
                            <span class="badge px-2 py-1" style="background:#fef3c7;color:#92400e;font-size:11px;">
                                <i class="fa fa-clock-o me-1"></i>
                                <span class="rima-en">Proof Submitted</span>
                                <span class="rima-ro">Dovadă trimisă</span>
                            </span>
                        <?php elseif ($is_pending && $is_bank) : ?>
                            <span class="badge px-2 py-1" style="background:#fee2e2;color:#991b1b;font-size:11px;">
                                <i class="fa fa-exclamation-circle me-1"></i>
                                <span class="rima-en">Payment Required</span>
                                <span class="rima-ro">Plată necesară</span>
                            </span>
                        <?php else : ?>
                            <span class="badge bg-secondary bg-opacity-75 px-2 py-1" style="font-size:11px;">
                                <?php echo esc_html(ucfirst($course['order_status'])); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <!-- Docs/Zoom indicators -->
                    <?php if ($is_active) : ?>
                    <div class="position-absolute top-0 start-0 p-2 d-flex flex-column gap-1">
                        <?php if ($has_docs) : ?>
                            <span class="badge bg-white text-dark shadow-sm border px-2 py-1" style="font-size:11px;">
                                <i class="fa fa-file-pdf-o text-danger"></i> Docs
                            </span>
                        <?php endif; ?>
                        <?php if ($has_zoom) : ?>
                            <span class="badge bg-primary shadow-sm px-2 py-1" style="font-size:11px;">
                                <i class="fa fa-video-camera"></i> Zoom
                            </span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Body -->
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="fw-bold mb-2 text-dark" style="line-height:1.4;font-size:15px;">
                        <?php echo esc_html($course['title']); ?>
                    </h5>
                    <p class="small text-muted mb-3">
                        <i class="fa fa-calendar me-1"></i> <?php echo esc_html($course['order_date']); ?>
                        &nbsp;|&nbsp;
                        <span class="rima-en">Order</span>
                        <span class="rima-ro">Comanda</span>
                        #<?php echo esc_html($order_id); ?>
                    </p>

                    <div class="mt-auto pt-3 border-top d-flex flex-column gap-2">

                        <?php if ($is_active) : ?>
                            <?php if ($has_docs) : 
                                $doc_link = isset($course['docs'][0]['url']) ? $course['docs'][0]['url'] : wc_get_account_endpoint_url('course-documents') . $course['product_id'] . '/';
                            ?>
                            <a href="<?php echo esc_url($doc_link); ?>"
                               target="_blank"
                               class="btn btn-sm fw-semibold text-white"
                               style="background:linear-gradient(135deg,#102d56,#102d56);">
                                <i class="fa fa-folder-open me-1"></i>
                                <span class="rima-en">Course Documents</span>
                                <span class="rima-ro">Documente Curs</span>
                            </a>
                            <?php endif; ?>

                            <?php
                            // RIMA PDF Viewer — show button if PDF is configured
                            $rima_pdf = get_post_meta( intval($course['product_id']), 'rima_pdf_url', true );
                            if ( $rima_pdf ) :
                                $viewer_url = add_query_arg('rima_viewer', intval($course['product_id']), wc_get_account_endpoint_url('my-courses'));
                            ?>
                            <a href="<?php echo esc_url($viewer_url); ?>"
                               class="btn btn-sm fw-semibold text-white"
                               style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                                <i class="fa fa-file-pdf-o me-1"></i>
                                <span class="rima-en">Read Course PDF</span>
                                <span class="rima-ro">Citește PDF-ul Cursului</span>
                            </a>
                            <?php endif; ?>

                            <?php if ($has_zoom) : ?>
                            <button class="btn btn-sm fw-semibold text-white"
                                    style="background:linear-gradient(135deg,#102d56,#102d56);"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#zoom-<?php echo esc_attr($course['product_id']); ?>">
                                <i class="fa fa-video-camera me-1"></i>
                                <span class="rima-en">Zoom Sessions</span>
                                <span class="rima-ro">Sesiuni Zoom</span>
                                (<?php echo count($course['zoom']); ?>)
                            </button>
                            <div class="collapse" id="zoom-<?php echo esc_attr($course['product_id']); ?>">
                                <div class="mt-2 rounded-3 p-3" style="background:#eff6ff;border:1px solid #102d56;">
                                    <?php foreach ($course['zoom'] as $session) : ?>
                                    <div class="mb-3 pb-3 border-bottom border-light-subtle">
                                        <div class="fw-bold text-dark mb-1" style="font-size:14px;">
                                            📅 <?php echo esc_html($session['title'] ?? 'Zoom Session'); ?>
                                        </div>
                                        <?php if (!empty($session['date'])) : ?>
                                        <div class="small text-muted mb-1">
                                            <i class="fa fa-calendar me-1"></i>
                                            <?php echo esc_html($session['date']); ?>
                                            <?php if (!empty($session['time'])) echo ' at ' . esc_html($session['time']); ?>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($session['meeting_id'])) : ?>
                                        <div class="small text-muted mb-2">
                                            <i class="fa fa-hashtag me-1"></i>
                                            <span class="rima-en">Meeting ID:</span>
                                            <span class="rima-ro">ID Ședință:</span>
                                            <strong><?php echo esc_html($session['meeting_id']); ?></strong>
                                            <?php if (!empty($session['password'])) : ?>
                                            &nbsp;|&nbsp;
                                            <span class="rima-en">Password:</span>
                                            <span class="rima-ro">Parolă:</span>
                                            <strong><?php echo esc_html($session['password']); ?></strong>
                                            <?php endif; ?>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($session['join_url'])) : ?>
                                        <a href="<?php echo esc_url($session['join_url']); ?>"
                                           target="_blank"
                                           class="btn btn-sm fw-semibold text-white"
                                           style="background:#102d56;font-size:12px;">
                                            <i class="fa fa-video-camera me-1"></i>
                                            <span class="rima-en">Join Zoom</span>
                                            <span class="rima-ro">Intră în Zoom</span>
                                        </a>
                                        <?php else : ?>
                                        <span class="badge" style="background:#fef3c7;color:#92400e;font-size:11px;">
                                            <span class="rima-en">Zoom link coming soon</span>
                                            <span class="rima-ro">Link Zoom în curând</span>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php else : ?>
                            <p class="small text-muted fst-italic mb-0">
                                <i class="fa fa-clock-o me-1"></i>
                                <span class="rima-en">Zoom sessions will be added when groups are formed.</span>
                                <span class="rima-ro">Sesiunile Zoom vor fi adăugate când se formează grupele.</span>
                            </p>
                            <?php endif; ?>

                        <?php elseif ($is_pending && $is_bank) : ?>
                            <!-- Bank Transfer Pending -->
                            <?php if ($has_proof) : ?>
                            <div class="rounded-3 p-3" style="background:#fef3c7;border:1px solid #fcd34d;">
                                <p class="small fw-semibold mb-1" style="color:#92400e;">
                                    <i class="fa fa-check-circle me-1"></i>
                                    <span class="rima-en">Payment proof submitted!</span>
                                    <span class="rima-ro">Dovada de plată a fost trimisă!</span>
                                </p>
                                <p class="small mb-2" style="color:#78350f;">
                                    <span class="rima-en">We are verifying your payment. You will receive course access within 24h.</span>
                                    <span class="rima-ro">Verificăm plata ta. Vei primi acces la curs în maxim 24h.</span>
                                </p>
                                <a href="<?php echo esc_url($course['payment_proof_url']); ?>"
                                   target="_blank" class="btn btn-sm btn-outline-secondary" style="font-size:11px;">
                                    <i class="fa fa-eye me-1"></i>
                                    <span class="rima-en">View proof</span>
                                    <span class="rima-ro">Vizualizează dovada</span>
                                </a>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary rima-proof-toggle"
                                    data-order="<?php echo esc_attr($order_id); ?>" style="font-size:12px;">
                                <i class="fa fa-refresh me-1"></i>
                                <span class="rima-en">Replace proof</span>
                                <span class="rima-ro">Înlocuiește dovada</span>
                            </button>
                            <?php else : ?>
                            <div class="rounded-3 p-3" style="background:#fee2e2;border:1px solid #fca5a5;">
                                <p class="small fw-semibold mb-1" style="color:#991b1b;">
                                    <i class="fa fa-exclamation-circle me-1"></i>
                                    <span class="rima-en">Bank Transfer Payment Required</span>
                                    <span class="rima-ro">Plată prin Ordin Bancar necesară</span>
                                </p>
                                <p class="small mb-0" style="color:#7f1d1d;">
                                    <span class="rima-en">Transfer the amount and upload the payment proof (bank statement or screenshot) to activate your course.</span>
                                    <span class="rima-ro">Transferă suma și încarcă dovada de plată (extras de cont sau captură) pentru a activa cursul.</span>
                                </p>
                            </div>
                            <button class="btn btn-sm fw-semibold text-white rima-proof-toggle"
                                    data-order="<?php echo esc_attr($order_id); ?>"
                                    style="background:linear-gradient(135deg,#102d56,#102d56);">
                                <i class="fa fa-upload me-1"></i>
                                <span class="rima-en">Upload Payment Proof</span>
                                <span class="rima-ro">Încarcă Dovada de Plată</span>
                            </button>
                            <?php endif; ?>

                            <!-- Upload form (hidden) -->
                            <div class="rima-proof-form" id="proof-form-<?php echo esc_attr($order_id); ?>" style="display:none;">
                                <div class="rounded-3 p-3" style="background:#f0f9ff;border:1px solid #102d56;">
                                    <p class="small fw-semibold text-dark mb-2">
                                        <i class="fa fa-upload me-1"></i>
                                        <span class="rima-en">Select file (JPG, PNG, PDF – max 5MB):</span>
                                        <span class="rima-ro">Selectează fișierul (JPG, PNG, PDF – max 5MB):</span>
                                    </p>
                                    <input type="file"
                                           class="form-control form-control-sm mb-2 rima-proof-file"
                                           accept="image/*,application/pdf"
                                           data-order="<?php echo esc_attr($order_id); ?>">
                                    <button class="btn btn-sm fw-semibold text-white rima-proof-submit"
                                            data-order="<?php echo esc_attr($order_id); ?>"
                                            data-nonce="<?php echo esc_attr($nonce); ?>"
                                            style="background:#15803d;">
                                        <i class="fa fa-check me-1"></i>
                                        <span class="rima-en">Submit</span>
                                        <span class="rima-ro">Trimite</span>
                                    </button>
                                    <p class="rima-proof-status small mt-2 mb-0" style="display:none;"></p>
                                </div>
                            </div>

                        <?php else : ?>
                            <p class="small text-muted fst-italic mb-0">
                                <span class="rima-en">Order is being processed…</span>
                                <span class="rima-ro">Comanda se procesează…</span>
                            </p>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Toggle upload form
    $(document).on('click', '.rima-proof-toggle', function() {
        var orderId = $(this).data('order');
        $('#proof-form-' + orderId).slideToggle(200);
    });

    // Submit payment proof
    $(document).on('click', '.rima-proof-submit', function() {
        var $btn    = $(this);
        var orderId = $btn.data('order');
        var nonce   = $btn.data('nonce');
        var $file   = $('.rima-proof-file[data-order="' + orderId + '"]');
        var $status = $btn.closest('.rima-proof-form').find('.rima-proof-status');
        var isRo    = document.body.classList.contains('rima-lang-ro');

        if (!$file[0].files.length) {
            $status.show().css('color','#dc2626').text(isRo ? 'Te rugăm să selectezi un fișier.' : 'Please select a file.');
            return;
        }

        var formData = new FormData();
        formData.append('action',        'rima_upload_payment_proof');
        formData.append('nonce',         nonce);
        formData.append('order_id',      orderId);
        formData.append('payment_proof', $file[0].files[0]);

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> ' + (isRo ? 'Se încarcă…' : 'Uploading…'));
        $status.show().css('color','#6b7280').text(isRo ? 'Se procesează…' : 'Processing…');

        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST', data: formData, processData: false, contentType: false,
            success: function(resp) {
                if (resp.success) {
                    $status.css('color','#15803d').text(resp.data.message);
                    $btn.prop('disabled', true).html('<i class="fa fa-check me-1"></i> ' + (isRo ? 'Trimis!' : 'Submitted!'));
                    setTimeout(function() { location.reload(); }, 2500);
                } else {
                    $status.css('color','#dc2626').text((isRo ? 'Eroare: ' : 'Error: ') + resp.data);
                    $btn.prop('disabled', false);
                }
            },
            error: function() {
                $status.css('color','#dc2626').text(isRo ? 'Eroare de rețea.' : 'Network error. Please try again.');
                $btn.prop('disabled', false);
            }
        });
    });
});
</script>
