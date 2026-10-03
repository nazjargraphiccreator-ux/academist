<?php
/**
 * Modern View Order Template for RIMA Academy (Bilingual EN/RO)
 * Override WooCommerce default view order page.
 */

defined( 'ABSPATH' ) || exit;

$order = wc_get_order( $order_id );
if ( ! $order ) return;

$status = $order->get_status();
$date = $order->get_date_created() ? $order->get_date_created()->date_i18n('d F Y, H:i') : '';
$payment_method = $order->get_payment_method();
$proof_id = get_post_meta( $order_id, '_rima_payment_proof', true );
$proof_url = $proof_id ? wp_get_attachment_url( $proof_id ) : '';

// Status styles
$status_colors = array(
    'pending'    => array( 'bg' => '#fef3c7', 'text' => '#92400e', 'icon' => 'fa-clock-o' ),
    'processing' => array( 'bg' => '#eff6ff', 'text' => '#102d56', 'icon' => 'fa-cog fa-spin' ),
    'on-hold'    => array( 'bg' => '#fee2e2', 'text' => '#991b1b', 'icon' => 'fa-pause-circle' ),
    'completed'  => array( 'bg' => '#dcfce7', 'text' => '#15803d', 'icon' => 'fa-check-circle' ),
    'cancelled'  => array( 'bg' => '#f3f4f6', 'text' => '#374151', 'icon' => 'fa-times-circle' ),
    'failed'     => array( 'bg' => '#fee2e2', 'text' => '#991b1b', 'icon' => 'fa-exclamation-circle' ),
);
$cls = $status_colors[$status] ?? array( 'bg' => '#f3f4f6', 'text' => '#374151', 'icon' => 'fa-file-text-o' );
?>

<div class="rima-view-order-page rima-dashboard-content card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4 p-md-5">

        <!-- Top Navigation -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="btn btn-sm btn-outline-secondary rounded-3">
                <i class="fa fa-arrow-left me-1"></i> <span class="rima-en">Back to Orders</span><span class="rima-ro">Înapoi la Comenzi</span>
            </a>
            <span class="badge px-3 py-2 rounded-pill fw-semibold d-flex align-items-center gap-1"
                  style="background:<?php echo $cls['bg']; ?>;color:<?php echo $cls['text']; ?>;font-size:13px;">
                <i class="fa <?php echo $cls['icon']; ?>"></i>
                <?php echo esc_html( wc_get_order_status_name( $status ) ); ?>
            </span>
        </div>

        <!-- Header Title -->
        <div class="mb-5 pb-3 border-bottom">
            <h1 class="h3 fw-bold mb-1 text-dark">
                <span class="rima-en">Order Details</span><span class="rima-ro">Detaliile Comenzii</span>
                <span class="text-primary">#<?php echo $order->get_order_number(); ?></span>
            </h1>
            <p class="small text-muted mb-0">
                <span class="rima-ro">Plasată pe <?php echo $date; ?></span>
                <span class="rima-en">Placed on <?php echo $date; ?></span>
            </p>
        </div>

        <!-- Bank Details and Payment Instruction for BACS -->
        <?php if ( $payment_method === 'bacs' && in_array( $status, array('pending', 'on-hold') ) ) : ?>
            <div class="p-4 mb-4 rounded-4 border bg-light">
                <h5 class="fw-bold text-dark mb-2" style="font-size: 14px;">
                    <i class="fa fa-university me-1 text-primary"></i> 
                    <span class="rima-en">Bank Transfer Payment Details</span>
                    <span class="rima-ro">Detalii Plată Transfer Bancar</span>
                </h5>
                <p class="small text-muted mb-3">
                    <span class="rima-en">Please make your payment directly into our bank account below. Please use your Order ID <strong>#<?php echo $order->get_order_number(); ?></strong> as the payment reference.</span>
                    <span class="rima-ro">Vă rugăm să efectuați plata în contul bancar de mai jos utilizând numărul comenzii <strong>#<?php echo $order->get_order_number(); ?></strong> ca referință de plată.</span>
                </p>
                <div class="row g-3">
                    <?php 
                    $bacs_accounts = get_option( 'woocommerce_bacs_accounts', array() );
                    if ( ! empty( $bacs_accounts ) ) : 
                        foreach ( $bacs_accounts as $account ) : 
                    ?>
                        <div class="col-md-6">
                            <div class="bg-white p-3 rounded-3 border h-100" style="font-size: 13px;">
                                <p class="mb-1"><strong>IBAN:</strong> <code><?php echo esc_html( $account['iban'] ); ?></code></p>
                                <p class="mb-1">
                                    <strong><span class="rima-en">Bank:</span><span class="rima-ro">Bancă:</span></strong> 
                                    <?php echo esc_html( $account['bank_name'] ); ?>
                                </p>
                                <p class="mb-0">
                                    <strong><span class="rima-en">Beneficiary:</span><span class="rima-ro">Beneficiar:</span></strong> 
                                    <?php echo esc_html( $account['account_name'] ); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Secure Proof of Payment Upload box (called directly from our custom function) -->
        <?php 
        if ( function_exists('rima_add_payment_proof_upload_section') ) {
            rima_add_payment_proof_upload_section( $order );
        }
        ?>

        <!-- Order Items List (Modern) -->
        <div class="mt-5 mb-5">
            <h4 class="h5 fw-bold mb-3 text-dark">
                <span class="rima-en">Order Items</span><span class="rima-ro">Produsele Comandate</span>
            </h4>
            <div class="d-flex flex-column gap-3">
                <?php foreach ( $order->get_items() as $item ) : 
                    $product = $item->get_product();
                    $img = $product ? $product->get_image(array(80, 80), array('class' => 'rounded-3 shadow-sm', 'style' => 'width: 80px; height: 80px; object-fit: cover;')) : '';
                    $product_url = $product ? $product->get_permalink() : '#';
                ?>
                    <div class="p-3 rounded-4 border bg-white shadow-sm d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 rima-order-item-card transition-all" style="transition: all 0.2s ease;">
                        <div class="d-flex align-items-center gap-3 flex-grow-1">
                            <?php if ($img) : ?>
                                <a href="<?php echo esc_url($product_url); ?>" class="flex-shrink-0 text-decoration-none">
                                    <?php echo $img; ?>
                                </a>
                            <?php endif; ?>
                            
                            <div>
                                <a href="<?php echo esc_url($product_url); ?>" class="text-decoration-none">
                                    <strong class="text-dark d-block fs-6 mb-1 rima-product-title-hover" style="transition: color 0.2s;"><?php echo esc_html($item->get_name()); ?></strong>
                                </a>
                                <span class="badge bg-light text-secondary border fw-normal mb-2" style="font-size:12px;">x<?php echo esc_html($item->get_quantity()); ?></span>
                                
                                <!-- Completed order extra files/zoom access -->
                                <?php if ( $status === 'completed' && $product ) : 
                                    $pid = $product->get_id();
                                    $prod_zoom = get_post_meta( $pid, '_rima_zoom_sessions', true ) ?: array();
                                    $prod_docs = get_post_meta( $pid, '_rima_course_documents', true ) ?: array();
                                    
                                    if ( ! empty($prod_docs) || ! empty($prod_zoom) ) :
                                ?>
                                    <div class="d-flex flex-wrap gap-2 mt-1">
                                        <?php if ( ! empty($prod_docs) ) : ?>
                                            <a href="<?php echo esc_url( wc_get_account_endpoint_url('course-documents') . $pid . '/' ); ?>" 
                                               class="btn btn-sm btn-outline-danger fw-semibold rounded-pill px-3 py-1" style="font-size: 11px;">
                                                <i class="fa fa-file-pdf-o me-1"></i> Materiale curs
                                            </a>
                                        <?php endif; ?>
                                        <?php if ( ! empty($prod_zoom) ) : ?>
                                            <a href="<?php echo esc_url( wc_get_account_endpoint_url('course-documents') . $pid . '/' ); ?>" 
                                               class="btn btn-sm btn-outline-primary fw-semibold rounded-pill px-3 py-1" style="font-size: 11px;">
                                                <i class="fa fa-video-camera me-1"></i> Zoom
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; endif; ?>
                            </div>
                        </div>
                        
                        <div class="text-md-end border-top border-md-top-0 pt-3 pt-md-0 mt-2 mt-md-0 text-end">
                            <span class="small text-muted d-block mb-1"><span class="rima-en">Total</span><span class="rima-ro">Preț</span></span>
                            <span class="fw-bold text-dark fs-5"><?php echo $order->get_formatted_line_subtotal($item); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <style>
                .rima-order-item-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
                    border-color: #cbd5e1 !important;
                }
                .rima-product-title-hover:hover {
                    color: #2563eb !important;
                }
            </style>
        </div>

        <!-- Order Totals -->
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <!-- Customer Order Notes / Updates -->
                <?php 
                $notes = $order->get_customer_order_notes();
                if ( $notes ) : 
                ?>
                    <div class="p-4 rounded-4 border bg-white shadow-sm h-100">
                        <h4 class="h6 fw-bold mb-3 text-dark">
                            <span class="rima-en">Order Updates</span><span class="rima-ro">Actualizări Comandă</span>
                        </h4>
                        <div class="rima-notes-timeline" style="font-size:13px; line-height:1.4;">
                            <?php foreach ( $notes as $note ) : ?>
                                <div class="mb-3 pb-3 border-bottom last-border-0">
                                    <span class="text-muted d-block small mb-1"><?php echo esc_html(date_i18n('d M Y, H:i', strtotime($note->comment_date))); ?></span>
                                    <span class="text-dark"><?php echo wp_kses_post(wpautop(wptexturize($note->comment_content))); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <!-- Summary Card -->
                <div class="p-4 rounded-4 border bg-light h-100">
                    <h4 class="h6 fw-bold mb-3 text-dark">
                        <span class="rima-en">Order Summary</span><span class="rima-ro">Sumar Comandă</span>
                    </h4>
                    <div style="font-size:14px;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span class="text-dark fw-semibold"><?php echo $order->get_subtotal_to_display(); ?></span>
                        </div>
                        <?php if ( $order->get_total_discount() > 0 ) : ?>
                            <div class="d-flex justify-content-between mb-2 text-danger">
                                <span>Discount</span>
                                <span>-<?php echo wc_price($order->get_total_discount()); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between border-top pt-2 mt-2" style="font-size:16px;">
                            <strong class="text-dark">Total</strong>
                            <strong class="text-primary"><?php echo $order->get_formatted_order_total(); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Billing Address -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="p-4 rounded-4 border bg-white shadow-sm h-100">
                    <h4 class="h6 fw-bold mb-3 text-dark"><i class="fa fa-id-card-o text-muted me-1"></i> <span class="rima-en">Billing Address</span><span class="rima-ro">Adresă Facturare</span></h4>
                    <address class="small text-muted mb-0" style="font-style:normal; line-height:1.5;">
                        <?php echo wp_kses_post($order->get_formatted_billing_address() ?: 'N/A'); ?>
                        <?php if ( $order->get_billing_email() ) : ?>
                            <p class="mt-2 mb-0 text-dark">📧 <?php echo esc_html($order->get_billing_email()); ?></p>
                        <?php endif; ?>
                        <?php if ( $order->get_billing_phone() ) : ?>
                            <p class="mb-0 text-dark">📞 <?php echo esc_html($order->get_billing_phone()); ?></p>
                        <?php endif; ?>
                    </address>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="p-4 rounded-4 border bg-white shadow-sm h-100">
                    <h4 class="h6 fw-bold mb-3 text-dark"><i class="fa fa-truck text-muted me-1"></i> <span class="rima-en">Shipping Method</span><span class="rima-ro">Metodă Livrare</span></h4>
                    <span class="small text-muted d-block">
                        <?php echo esc_html( $order->get_shipping_method() ?: 'N/A' ); ?>
                    </span>
                    <span class="small text-muted d-block mt-2">
                        <strong>Payment Method:</strong> <?php echo esc_html( $order->get_payment_method_title() ); ?>
                    </span>
                </div>
            </div>
        </div>

    </div>
</div>
