<?php
/**
 * Modern Orders Template for RIMA Academy (Bilingual EN/RO)
 * Override WooCommerce default orders page.
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders ); ?>

<div class="rima-orders-page rima-dashboard-content card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4 p-md-5">

        <!-- Header -->
        <div class="d-flex align-items-center gap-3 mb-5 pb-3 border-bottom">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:50px;height:50px;background:linear-gradient(135deg,#102d56,#102d56);">
                <i class="fa fa-list-alt text-white fs-4"></i>
            </div>
            <div>
                <h3 class="h4 fw-bold mb-1 text-dark">
                    <span class="rima-en">My Orders</span>
                    <span class="rima-ro">Comenzile Mele</span>
                </h3>
                <p class="small text-muted mb-0">
                    <span class="rima-en">Track, view details, and manage payment proofs for your orders.</span>
                    <span class="rima-ro">Urmărește, vizualizează detalii și încarcă dovezi de plată pentru comenzi.</span>
                </p>
            </div>
        </div>

        <?php if ( $has_orders ) : ?>

            <!-- Search and Filter Bar -->
            <style>
            .rima-orders-filter-bar input.form-control,
            .rima-orders-filter-bar select.form-select {
                height: 54px !important;
                padding-left: 48px !important;
                font-size: 14px !important;
                font-weight: 500 !important;
                color: #1e293b !important;
                background-color: #f8fafc !important;
                border: 1px solid #e2e8f0 !important;
                border-radius: 12px !important;
                transition: all 0.3s ease !important;
                box-shadow: 0 2px 4px rgba(0,0,0,0.02) !important;
                line-height: normal !important;
            }
            .rima-orders-filter-bar input.form-control:focus,
            .rima-orders-filter-bar select.form-select:focus {
                background-color: #ffffff !important;
                border-color: #7c3aed !important;
                box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1) !important;
                outline: none !important;
            }
            .rima-orders-filter-icon {
                position: absolute !important;
                top: 50% !important;
                left: 18px !important;
                transform: translateY(-50%) !important;
                color: #94a3b8 !important;
                font-size: 16px !important;
                z-index: 4 !important;
                pointer-events: none !important;
            }
            .rima-orders-chevron {
                position: absolute !important;
                top: 50% !important;
                right: 18px !important;
                transform: translateY(-50%) !important;
                color: #94a3b8 !important;
                font-size: 14px !important;
                z-index: 4 !important;
                pointer-events: none !important;
            }
            .rima-orders-filter-bar select.form-select {
                appearance: none !important;
                -webkit-appearance: none !important;
                cursor: pointer !important;
                padding-right: 48px !important;
            }
            </style>
            <div class="rima-orders-filter-bar d-flex flex-column flex-md-row gap-3 mb-5">
                <!-- Search Input -->
                <div class="position-relative flex-grow-1">
                    <i class="fa fa-search rima-orders-filter-icon"></i>
                    <input type="text" id="rima-order-search" class="form-control" placeholder="Număr comandă, curs...">
                </div>
                <!-- Status Filter -->
                <div class="position-relative" style="min-width: 260px;">
                    <i class="fa fa-filter rima-orders-filter-icon"></i>
                    <select id="rima-order-status-filter" class="form-select">
                        <option value="all">Toate statusurile / All statuses</option>
                        <option value="pending">În așteptare plată / Pending payment</option>
                        <option value="processing">În procesare / Processing</option>
                        <option value="on-hold">În așteptare / On hold</option>
                        <option value="completed">Finalizate / Completed</option>
                        <option value="cancelled">Anulate / Cancelled</option>
                    </select>
                    <i class="fa fa-chevron-down rima-orders-chevron"></i>
                </div>
            </div>

            <!-- Orders Grid -->
            <div class="row g-4" id="rima-orders-list">
                <?php
                foreach ( $customer_orders->orders as $customer_order ) {
                    $order      = wc_get_order( $customer_order );
                    $order_id   = $order->get_id();
                    $status     = $order->get_status();
                    $item_count = $order->get_item_count();
                    $date       = $order->get_date_created() ? $order->get_date_created()->date_i18n('d M Y') : '';
                    $total      = $order->get_formatted_order_total();
                    $payment_method = $order->get_payment_method();
                    $proof_id   = get_post_meta( $order_id, '_rima_payment_proof', true );
                    
                    // Status styling
                    $status_colors = array(
                        'pending'    => array( 'bg' => '#fef3c7', 'text' => '#92400e', 'icon' => 'fa-clock-o' ),
                        'processing' => array( 'bg' => '#eff6ff', 'text' => '#102d56', 'icon' => 'fa-cog fa-spin' ),
                        'on-hold'    => array( 'bg' => '#fee2e2', 'text' => '#991b1b', 'icon' => 'fa-pause-circle' ),
                        'completed'  => array( 'bg' => '#dcfce7', 'text' => '#15803d', 'icon' => 'fa-check-circle' ),
                        'cancelled'  => array( 'bg' => '#f3f4f6', 'text' => '#374151', 'icon' => 'fa-times-circle' ),
                        'failed'     => array( 'bg' => '#fee2e2', 'text' => '#991b1b', 'icon' => 'fa-exclamation-circle' ),
                    );
                    $cls = $status_colors[$status] ?? array( 'bg' => '#f3f4f6', 'text' => '#374151', 'icon' => 'fa-receipt' );
                    ?>
                    
                    <div class="col-md-6 rima-order-card-col" data-order-id="<?php echo esc_attr($order_id); ?>" data-status="<?php echo esc_attr($status); ?>">
                        <div class="card h-100 border rounded-4 shadow-sm overflow-hidden bg-white">
                            <!-- Card Header -->
                            <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="h5 fw-bold mb-0 text-dark">#<?php echo $order->get_order_number(); ?></h4>
                                    <span class="text-muted small"><?php echo esc_html($date); ?></span>
                                </div>
                                <span class="badge px-3 py-2 rounded-pill fw-semibold d-flex align-items-center gap-1"
                                      style="background:<?php echo $cls['bg']; ?>;color:<?php echo $cls['text']; ?>;font-size:12px;">
                                    <i class="fa <?php echo $cls['icon']; ?>"></i>
                                    <?php echo esc_html( wc_get_order_status_name( $status ) ); ?>
                                </span>
                            </div>

                            <!-- Card Body -->
                            <div class="card-body px-4 py-3 d-flex flex-column">
                                <!-- Products list summary -->
                                <div class="mb-3">
                                    <h5 class="small fw-semibold text-muted uppercase tracking-wider mb-2" style="font-size: 11px; letter-spacing: 0.5px;">
                                        <span class="rima-en">Items</span><span class="rima-ro">Produse</span>
                                    </h5>
                                    <ul class="list-unstyled mb-0">
                                        <?php foreach ( $order->get_items() as $item ) : ?>
                                            <li class="small text-dark mb-1 d-flex align-items-center gap-2">
                                                <i class="fa fa-book text-muted"></i>
                                                <span class="text-truncate" style="max-width:250px;"><?php echo esc_html($item->get_name()); ?></span>
                                                <span class="fw-bold text-muted">x<?php echo esc_html($item->get_quantity()); ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>

                                <div class="mt-auto">
                                    <!-- Payment Info -->
                                    <div class="d-flex justify-content-between align-items-center border-top pt-3">
                                        <div>
                                            <span class="small text-muted d-block" style="font-size: 11px;">
                                                <span class="rima-en">Total</span><span class="rima-ro">Total</span>
                                            </span>
                                            <span class="fw-bold text-dark fs-6"><?php echo $total; ?></span>
                                        </div>
                                        <div class="text-end">
                                            <span class="small text-muted d-block" style="font-size: 11px;">
                                                <span class="rima-en">Payment</span><span class="rima-ro">Plată</span>
                                            </span>
                                            <span class="small fw-semibold text-dark">
                                                <?php 
                                                $pm_title = $order->get_payment_method_title();
                                                if ( $payment_method === 'bacs' ) {
                                                    echo '<span class="rima-en">Bank Transfer</span><span class="rima-ro">Transfer Bancar</span>';
                                                } else {
                                                    echo esc_html( $pm_title );
                                                }
                                                ?>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Payment Proof Alert for BACS -->
                                    <?php if ( $payment_method === 'bacs' && in_array( $status, array('pending', 'on-hold') ) ) : ?>
                                        <div class="mt-3 p-2 rounded-3 text-center d-flex align-items-center justify-content-center gap-2"
                                             style="<?php echo $proof_id ? 'background:#e6f4ea;color:#137333;' : 'background:#fce8e6;color:#c5221f;'; ?>font-size:12px;">
                                            <i class="fa <?php echo $proof_id ? 'fa-check-circle' : 'fa-info-circle'; ?>"></i>
                                            <span>
                                                <?php if ($proof_id) : ?>
                                                    <span class="rima-en">Proof uploaded</span><span class="rima-ro">Dovada a fost încărcată</span>
                                                <?php else : ?>
                                                    <span class="rima-en">Proof required</span><span class="rima-ro">Dovada este necesară</span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Card Footer -->
                            <div class="card-footer bg-light border-top-0 px-4 py-3 d-flex gap-2">
                                <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" class="btn btn-sm btn-outline-primary fw-semibold rounded-3 flex-grow-1 py-2" style="font-size: 13px;">
                                    <i class="fa fa-eye me-1"></i> <span class="rima-en">Details</span><span class="rima-ro">Detalii</span>
                                </a>

                                <?php if ( $payment_method === 'bacs' && in_array( $status, array('pending', 'on-hold') ) && !$proof_id ) : ?>
                                    <div class="position-relative rima-quick-upload-wrapper" style="flex-grow: 1;">
                                        <input type="file" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 rima-quick-proof-input" 
                                               style="cursor: pointer; z-index: 2;" 
                                               data-order-id="<?php echo esc_attr($order_id); ?>" 
                                               data-nonce="<?php echo esc_attr(wp_create_nonce('rima_proof_nonce_' . $order_id)); ?>" 
                                               accept="application/pdf,image/png,image/jpeg,image/jpg" 
                                               title="Upload Proof">
                                        <button type="button" class="btn btn-sm btn-danger fw-semibold rounded-3 py-2 w-100 rima-quick-proof-btn" style="font-size: 13px; pointer-events: none;">
                                            <span class="rima-upload-text">
                                                <i class="fa fa-upload me-1"></i> <span class="rima-en">Upload</span><span class="rima-ro">Încarcă Dovada</span>
                                            </span>
                                            <span class="rima-upload-loading" style="display:none;">
                                                <i class="fa fa-spinner fa-spin"></i>
                                            </span>
                                        </button>
                                    </div>
                                <?php endif; ?>

                                <?php if ( $order->needs_payment() ) : ?>
                                    <a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="btn btn-sm btn-success fw-semibold rounded-3 py-2 px-3" style="font-size: 13px;">
                                        <i class="fa fa-credit-card me-1"></i> <span class="rima-en">Pay</span><span class="rima-ro">Plătește</span>
                                    </a>
                                <?php endif; ?>

                                <?php if ( in_array( $status, array('pending', 'on-hold') ) ) : ?>
                                    <a href="<?php echo esc_url( $order->get_cancel_order_url() ); ?>"
                                       class="btn btn-sm btn-outline-danger fw-semibold rounded-3 py-2 px-3" style="font-size: 13px;"
                                       onclick="return confirm('<?php _e('Are you sure you want to cancel this order?', 'woocommerce'); ?>');">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- Pagination -->
            <?php if ( 1 < $customer_orders->max_num_pages ) : ?>
                <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
                    <?php if ( 1 !== $current_page ) : ?>
                        <a class="btn btn-sm btn-outline-secondary rounded-3" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>">
                            <i class="fa fa-arrow-left me-1"></i> <?php _e( 'Previous', 'woocommerce' ); ?>
                        </a>
                    <?php else : ?>
                        <div></div>
                    <?php endif; ?>

                    <span class="small text-muted">Pagina <?php echo $current_page; ?> / <?php echo $customer_orders->max_num_pages; ?></span>

                    <?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
                        <a class="btn btn-sm btn-outline-secondary rounded-3" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>">
                            <?php _e( 'Next', 'woocommerce' ); ?> <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    <?php else : ?>
                        <div></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else : ?>
            <div class="text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width:80px;height:80px;">
                    <i class="fa fa-receipt text-muted opacity-50" style="font-size:32px;"></i>
                </div>
                <h4 class="fw-bold mb-2">
                    <span class="rima-en">No orders yet</span><span class="rima-ro">Nicio comandă momentan</span>
                </h4>
                <p class="text-muted small mb-4">
                    <span class="rima-en">You haven't placed any orders yet.</span><span class="rima-ro">Nu ai plasat nicio comandă până acum.</span>
                </p>
                <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="btn btn-primary rounded-3 btn-sm px-4 py-2">
                    <?php _e( 'Go shop', 'woocommerce' ); ?>
                </a>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Search filter
    $('#rima-order-search').on('input', function() {
        filterOrders();
    });

    // Status filter
    $('#rima-order-status-filter').on('change', function() {
        filterOrders();
    });

    function filterOrders() {
        var searchTerm = $('#rima-order-search').val().toLowerCase();
        var statusFilter = $('#rima-order-status-filter').val();

        $('.rima-order-card-col').each(function() {
            var $card = $(this);
            var text = $card.text().toLowerCase();
            var status = $card.data('status');

            var matchSearch = !searchTerm || text.indexOf(searchTerm) > -1;
            var matchStatus = statusFilter === 'all' || status === statusFilter;

            if (matchSearch && matchStatus) {
                $card.show();
            } else {
                $card.hide();
            }
        });
    }

    function updateBilingualControls(lang) {
        var isRo = (lang === 'ro');
        $('#rima-order-search').attr('placeholder', isRo ? 'Număr comandă, curs...' : 'Order number, course...');
        
        var dropdown = $('#rima-order-status-filter');
        if (dropdown.length) {
            dropdown.find('option[value="all"]').text(isRo ? 'Toate statusurile' : 'All statuses');
            dropdown.find('option[value="pending"]').text(isRo ? 'În așteptare plată' : 'Pending payment');
            dropdown.find('option[value="processing"]').text(isRo ? 'În procesare' : 'Processing');
            dropdown.find('option[value="on-hold"]').text(isRo ? 'În așteptare' : 'On hold');
            dropdown.find('option[value="completed"]').text(isRo ? 'Finalizate' : 'Completed');
            dropdown.find('option[value="cancelled"]').text(isRo ? 'Anulate' : 'Cancelled');
        }
    }

    // Run on load
    var currentLang = localStorage.getItem('rima_lang') || 'en';
    updateBilingualControls(currentLang);

    // Listen to custom change event
    $(document).on('rima_lang_changed', function(e, lang) {
        updateBilingualControls(lang);
    });

    // Handle Quick Upload from Orders Card
    $('.rima-quick-proof-input').on('change', function() {
        var $input = $(this);
        var $wrapper = $input.closest('.rima-quick-upload-wrapper');
        var $btn = $wrapper.find('.rima-quick-proof-btn');
        var $btnText = $btn.find('.rima-upload-text');
        var $btnLoading = $btn.find('.rima-upload-loading');
        
        var file = this.files[0];
        if (!file) return;

        var orderId = $input.data('order-id');
        var nonce = $input.data('nonce');

        // Basic validation
        var allowedTypes = ['application/pdf', 'image/png', 'image/jpeg', 'image/jpg'];
        if (!allowedTypes.includes(file.type)) {
            alert('Format invalid. Sunt permise doar fișiere PDF, PNG și JPG.');
            $input.val('');
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            alert('Fișierul este prea mare. Lăsați dimensiunea maximă de 5MB.');
            $input.val('');
            return;
        }

        // UI state
        $btnText.hide();
        $btnLoading.show();
        $btn.removeClass('btn-danger').addClass('btn-secondary');

        var formData = new FormData();
        formData.append('proof_file', file);
        formData.append('order_id', orderId);
        formData.append('action', 'rima_upload_payment_proof');
        formData.append('nonce', nonce);

        $.ajax({
            url: '<?php echo admin_url("admin-ajax.php"); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $wrapper.html('<button class="btn btn-sm btn-success fw-semibold w-100 rounded-3 py-2" style="font-size: 13px;" disabled><i class="fa fa-check"></i> ' + (currentLang === 'ro' ? 'Dovada a fost încărcată' : 'Proof Uploaded') + '</button>');
                    // Refresh page after 1.5s to update statuses
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    alert(response.data || 'A apărut o eroare la încărcare.');
                    resetQuickBtn();
                }
            },
            error: function() {
                alert('Eroare de rețea. Te rugăm să încerci din nou.');
                resetQuickBtn();
            }
        });

        function resetQuickBtn() {
            $input.val('');
            $btnLoading.hide();
            $btnText.show();
            $btn.removeClass('btn-secondary').addClass('btn-danger');
        }
    });
});
</script>
<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
