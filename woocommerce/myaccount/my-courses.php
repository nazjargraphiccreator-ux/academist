<?php
/**
 * My Courses / Learning Hub – RIMA Academy Premium (2026 SaaS Design)
 */
if ( ! defined('ABSPATH') ) exit;

$courses = function_exists('rima_get_user_courses') ? rima_get_user_courses() : array();
$nonce   = wp_create_nonce('rima_nonce');
?>

<div class="rima-learning-hub" style="font-family: 'Inter', sans-serif;">
    <!-- Hub Header -->
    <div class="hub-header shadow-lg rounded-4 mb-5 overflow-hidden position-relative" style="background: #0f172a;">
        <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden" style="pointer-events: none;">
            <div class="position-absolute rounded-circle" style="width: 400px; height: 400px; background: rgba(59,130,246,0.15); filter: blur(80px); top: -150px; right: -100px;"></div>
            <div class="position-absolute rounded-circle" style="width: 300px; height: 300px; background: rgba(139,92,246,0.15); filter: blur(60px); bottom: -100px; left: -100px;"></div>
        </div>
        
        <div class="p-4 p-md-5 position-relative z-1 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
            <div>
                <span class="badge bg-primary bg-opacity-25 text-info mb-3 px-3 py-2 rounded-pill fw-semibold shadow-sm" style="font-size: 12px; letter-spacing: 0.5px;">
                    <i class="fa fa-sparkles me-1"></i> RIMA Learning Hub
                </span>
                <h2 class="display-6 fw-bold text-white mb-2" style="letter-spacing: -0.5px;">
                    <span class="rima-en">Your Learning Journey</span>
                    <span class="rima-ro">Parcursul tău de învățare</span>
                </h2>
                <p class="fs-6 text-white-50 m-0" style="max-width: 500px; line-height: 1.6;">
                    <span class="rima-en">Access video lessons, study materials, live Zoom sessions, and track your progress in real-time.</span>
                    <span class="rima-ro">Accesează lecții video, materiale de studiu, sesiuni live Zoom și urmărește-ți progresul în timp real.</span>
                </p>
            </div>
            <div class="text-md-end shrink-0">
                <div class="d-flex flex-row flex-md-column gap-3 justify-content-md-end">
                    <div class="text-center p-3 rounded-4" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                        <h3 class="fw-bold text-white mb-0"><?php echo count($courses); ?></h3>
                        <span class="small text-white-50 text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 1px;">Enrolled</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ( empty($courses) ) : ?>
        <div class="text-center py-5 px-3 rounded-4" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
            <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4 shadow-sm"
                 style="width:80px;height:80px;">
                <i class="fa fa-book text-muted opacity-50" style="font-size:32px;"></i>
            </div>
            <h4 class="fw-bold mb-2 text-dark">
                <span class="rima-en">No active enrollments</span>
                <span class="rima-ro">Nu ești înscris la niciun curs</span>
            </h4>
            <p class="text-muted mb-4 mx-auto" style="max-width:400px; font-size: 15px;">
                <span class="rima-en">Explore our collection and start your learning journey today.</span>
                <span class="rima-ro">Explorează colecția noastră și începe-ți călătoria de învățare astăzi.</span>
            </p>
            <a href="<?php echo esc_url(site_url('/our-courses/')); ?>"
               class="btn text-white border-0 px-4 py-2 fw-bold shadow-sm rounded-pill"
               style="background: linear-gradient(135deg,#3b82f6,#2563eb);">
                <span class="rima-en">Discover Courses</span>
                <span class="rima-ro">Descoperă Cursuri</span>
                <i class="fa fa-arrow-right ms-2"></i>
            </a>
        </div>
    <?php else : ?>
        <div class="hub-grid">
        <?php foreach ($courses as $course) :
            $is_active  = ($course['order_status'] === 'completed');
            $is_pending = in_array($course['order_status'], array('pending','on-hold','processing'));
            $is_bank    = ($course['payment_method'] === 'bacs');
            $has_proof  = ! empty($course['payment_proof_url']);
            $has_docs   = ! empty($course['docs']);
            $has_zoom   = ! empty($course['zoom']);
            $order_id   = $course['order_id'];
            $img        = $course['image'] ?: 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'300\' height=\'200\'%3E%3Crect fill=\'%230f172a\' width=\'300\' height=\'200\'/%3E%3C/svg%3E';
        ?>
            <div class="hub-course-card">
                <!-- Image Header -->
                <div class="course-thumb">
                    <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($course['title']); ?>">
                    <div class="course-overlay"></div>
                    
                    <!-- Badges Top Right -->
                    <div class="status-badges">
                        <?php if ($is_active) : ?>
                            <span class="hub-badge active">
                                <span class="pulse-dot"></span>
                                <span class="rima-en">Active Learning</span><span class="rima-ro">Învățare Activă</span>
                            </span>
                        <?php elseif ($is_pending && $is_bank && $has_proof) : ?>
                            <span class="hub-badge warning">
                                <i class="fa fa-clock-o me-1"></i> Verifying Proof
                            </span>
                        <?php elseif ($is_pending && $is_bank) : ?>
                            <span class="hub-badge danger">
                                <i class="fa fa-exclamation-circle me-1"></i> Payment Req.
                            </span>
                        <?php else : ?>
                            <span class="hub-badge secondary">
                                <?php echo esc_html(ucfirst($course['order_status'])); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Course Info -->
                <div class="course-body">
                    <div class="mb-3">
                        <span class="small text-muted fw-semibold d-block mb-1" style="font-size: 12px; letter-spacing: 0.5px;">
                            <i class="fa fa-hashtag text-primary opacity-50"></i> ORDER <?php echo esc_html($order_id); ?>
                        </span>
                        <h4 class="course-title"><?php echo esc_html($course['title']); ?></h4>
                    </div>

                    <div class="course-actions mt-auto border-top pt-4">
                        <?php if ($is_active) : ?>
                            <div class="d-flex flex-column gap-2">
                                <!-- Enter Course Button -->
                                <?php
                                $rima_pdf = get_post_meta( intval($course['product_id']), 'rima_pdf_url', true );
                                if ( $rima_pdf ) :
                                    $viewer_url = add_query_arg('rima_viewer', intval($course['product_id']), wc_get_account_endpoint_url('my-courses'));
                                ?>
                                <a href="<?php echo esc_url($viewer_url); ?>" class="btn-hub primary w-100">
                                    <i class="fa fa-play-circle me-2"></i>
                                    <span class="rima-en">Resume Course (PDF/Video)</span>
                                    <span class="rima-ro">Continuă Cursul (Lecții)</span>
                                </a>
                                <?php endif; ?>

                                <!-- Quick Actions Row -->
                                <div class="d-flex gap-2">
                                    <?php if ($has_docs) : 
                                        $doc_link = isset($course['docs'][0]['url']) ? $course['docs'][0]['url'] : wc_get_account_endpoint_url('course-documents') . $course['product_id'] . '/';
                                    ?>
                                    <a href="<?php echo esc_url($doc_link); ?>" target="_blank" class="btn-hub secondary flex-fill text-center">
                                        <i class="fa fa-folder-open"></i> Docs
                                    </a>
                                    <?php endif; ?>

                                    <?php if ($has_zoom) : ?>
                                    <button class="btn-hub secondary flex-fill text-center" data-bs-toggle="collapse" data-bs-target="#zoom-<?php echo esc_attr($course['product_id']); ?>">
                                        <i class="fa fa-video-camera text-primary"></i> Zoom (<?php echo count($course['zoom']); ?>)
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Zoom Sessions Collapse -->
                            <?php if ($has_zoom) : ?>
                            <div class="collapse" id="zoom-<?php echo esc_attr($course['product_id']); ?>">
                                <div class="zoom-panel mt-3">
                                    <?php foreach ($course['zoom'] as $session) : ?>
                                    <div class="zoom-session">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="fw-bold text-dark" style="font-size:14px;">
                                                <?php echo esc_html($session['title'] ?? 'Zoom Session'); ?>
                                            </div>
                                            <?php if (!empty($session['join_url'])) : ?>
                                            <a href="<?php echo esc_url($session['join_url']); ?>" target="_blank" class="btn-zoom-join">
                                                Join
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($session['date'])) : ?>
                                        <div class="small text-muted mb-1">
                                            <i class="fa fa-clock-o me-1"></i> <?php echo esc_html($session['date']); ?> <?php if (!empty($session['time'])) echo esc_html($session['time']); ?>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($session['meeting_id'])) : ?>
                                        <div class="small text-muted" style="font-size: 11px;">
                                            ID: <strong><?php echo esc_html($session['meeting_id']); ?></strong>
                                            <?php if (!empty($session['password'])) : ?> | Pass: <strong><?php echo esc_html($session['password']); ?></strong><?php endif; ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                        <?php elseif ($is_pending && $is_bank) : ?>
                            <!-- Bank Transfer Logic -->
                            <?php if ($has_proof) : ?>
                            <div class="alert-hub warning">
                                <i class="fa fa-clock-o"></i>
                                <div>
                                    <strong><span class="rima-en">Verifying payment</span><span class="rima-ro">Verificare plată</span></strong>
                                    <span class="d-block mt-1">Accesul se activează după confirmarea băncii (max 24h).</span>
                                </div>
                            </div>
                            <button class="btn-hub outline mt-3 rima-proof-toggle" data-order="<?php echo esc_attr($order_id); ?>"><i class="fa fa-refresh"></i> Replace proof</button>
                            <?php else : ?>
                            <div class="alert-hub danger">
                                <i class="fa fa-exclamation-triangle"></i>
                                <div>
                                    <strong><span class="rima-en">Payment Required</span><span class="rima-ro">Plată Necesară</span></strong>
                                    <span class="d-block mt-1">Vă rugăm să încărcați OP-ul pentru activare.</span>
                                </div>
                            </div>
                            <button class="btn-hub primary w-100 mt-3 rima-proof-toggle" data-order="<?php echo esc_attr($order_id); ?>">
                                <i class="fa fa-upload me-1"></i> Upload Payment Proof
                            </button>
                            <?php endif; ?>

                            <!-- Upload form (hidden) -->
                            <div class="rima-proof-form mt-3" id="proof-form-<?php echo esc_attr($order_id); ?>" style="display:none;">
                                <div class="p-3 rounded-4" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                                    <input type="file" class="form-control form-control-sm mb-2 rima-proof-file shadow-none" accept="image/*,application/pdf" data-order="<?php echo esc_attr($order_id); ?>">
                                    <button class="btn-hub success w-100 rima-proof-submit" data-order="<?php echo esc_attr($order_id); ?>" data-nonce="<?php echo esc_attr($nonce); ?>">
                                        <i class="fa fa-check me-1"></i> Submit
                                    </button>
                                    <p class="rima-proof-status small mt-2 mb-0 text-center fw-semibold" style="display:none;"></p>
                                </div>
                            </div>

                        <?php else : ?>
                            <div class="alert-hub secondary">
                                <i class="fa fa-cog fa-spin"></i>
                                <div>
                                    <strong><span class="rima-en">Processing</span><span class="rima-ro">Se procesează</span></strong>
                                    <span class="d-block mt-1">Comanda este în curs de validare.</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
/* Learning Hub 2026 SaaS Styles */
.hub-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 24px;
}
.hub-course-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03);
}
.hub-course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.05);
    border-color: #cbd5e1;
}
.course-thumb {
    position: relative;
    height: 190px;
    background: #0f172a;
    overflow: hidden;
}
.course-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}
.hub-course-card:hover .course-thumb img {
    transform: scale(1.05);
}
.course-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(15,23,42,0.8) 0%, rgba(15,23,42,0) 100%);
}
.status-badges {
    position: absolute;
    top: 16px;
    right: 16px;
    z-index: 2;
}
.hub-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 30px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    backdrop-filter: blur(8px);
}
.hub-badge.active { background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(52, 211, 153, 0.3); }
.hub-badge.warning { background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(251, 191, 36, 0.3); }
.hub-badge.danger { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(248, 113, 113, 0.3); }
.hub-badge.secondary { background: rgba(255, 255, 255, 0.2); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.3); }

.pulse-dot {
    width: 6px;
    height: 6px;
    background-color: #34d399;
    border-radius: 50%;
    box-shadow: 0 0 0 rgba(52, 211, 153, 0.4);
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.4); }
    70% { box-shadow: 0 0 0 6px rgba(52, 211, 153, 0); }
    100% { box-shadow: 0 0 0 0 rgba(52, 211, 153, 0); }
}

.course-body {
    padding: 24px;
    display: flex;
    flex-direction: column;
    flex: 1;
}
.course-title {
    font-size: 17px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.4;
    margin: 0;
}
.btn-hub {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 16px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none !important;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
}
.btn-hub.primary {
    background: #3b82f6;
    color: #fff;
    box-shadow: 0 4px 6px -1px rgba(59,130,246,0.2);
}
.btn-hub.primary:hover { background: #2563eb; transform: translateY(-1px); }
.btn-hub.secondary {
    background: #f1f5f9;
    color: #475569;
}
.btn-hub.secondary:hover { background: #e2e8f0; color: #0f172a; }
.btn-hub.success { background: #10b981; color: #fff; }
.btn-hub.success:hover { background: #059669; }
.btn-hub.outline {
    background: transparent;
    border: 1px solid #cbd5e1;
    color: #475569;
    width: 100%;
}

.zoom-panel {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
}
.zoom-session {
    padding-bottom: 12px;
    margin-bottom: 12px;
    border-bottom: 1px dashed #cbd5e1;
}
.zoom-session:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
.btn-zoom-join {
    background: #ef4444;
    color: #fff !important;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    text-decoration: none;
    text-transform: uppercase;
}

.alert-hub {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 12px;
    font-size: 13px;
    line-height: 1.4;
}
.alert-hub i { margin-top: 3px; font-size: 16px; }
.alert-hub.warning { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
.alert-hub.danger { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
.alert-hub.secondary { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }
</style>

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
