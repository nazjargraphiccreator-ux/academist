<?php
defined('ABSPATH') || exit;

// ── Robust order retrieval ─────────────────────────────────────────────────
// WooCommerce passes $order_id and $order to this template via extract().
// However if session/cookie mismatch, $order can be false even with a valid URL.
// Fallback: read order ID from URL and validate against the ?key= parameter.

$order = false;

// 1) Use the variable WC passed in (standard path)
if ( ! empty( $order_id ) ) {
    $order = wc_get_order( $order_id );
}

// 2) Fallback: read from URL query var (handles session mismatch)
if ( ! $order ) {
    // URL pattern: /order-received/{id}/?key=wc_order_...
    $url_order_id = absint( get_query_var( 'order-received' ) );
    if ( ! $url_order_id ) {
        // Try to extract from current URL path
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        if ( preg_match( '/order-received\/(\d+)/', $request_uri, $matches ) ) {
            $url_order_id = intval( $matches[1] );
        }
    }

    if ( $url_order_id ) {
        $candidate = wc_get_order( $url_order_id );

        if ( $candidate ) {
            // Validate the order key from the URL for security
            $url_key = sanitize_text_field( $_GET['key'] ?? '' );
            // Allow if: key matches OR user is logged in and owns the order
            $key_matches  = ( $url_key && $candidate->get_order_key() === $url_key );
            $user_owns    = is_user_logged_in() && ( get_current_user_id() === $candidate->get_customer_id() );

            if ( $key_matches || $user_owns ) {
                $order    = $candidate;
                $order_id = $url_order_id;
            }
        }
    }
}
?>

<div class="rima-thankyou-page">

    <?php if ($order) : 
        $order_status = $order->get_status();
        $order_items = $order->get_items();
    ?>

    <!-- Success Animation Header -->
    <div class="rima-thankyou-header">
        <div class="rima-success-animation">
            <div class="rima-success-checkmark">
                <div class="rima-checkmark-circle">
                    <div class="rima-checkmark-stem"></div>
                    <div class="rima-checkmark-kick"></div>
                </div>
            </div>
        </div>
        
        <h1 class="rima-thankyou-title">
            <?php esc_html_e('Mulțumim pentru comandă!', 'rima-academy'); ?>
        </h1>
        <p class="rima-thankyou-subtitle">
            <?php printf(
                esc_html__('Comanda ta #%s a fost plasată cu succes.', 'rima-academy'),
                '<strong>' . $order->get_order_number() . '</strong>'
            ); ?>
        </p>
    </div>

    <!-- Order Status Card -->
    <div class="rima-order-status-card">
        <div class="rima-status-icon">
            <?php 
            switch ($order_status) {
                case 'pending':
                    echo '⏳';
                    break;
                case 'processing':
                    echo '🔄';
                    break;
                case 'on-hold':
                    echo '⏸️';
                    break;
                case 'completed':
                    echo '✅';
                    break;
                default:
                    echo '📦';
            }
            ?>
        </div>
        <div class="rima-status-content">
            <div class="rima-status-label"><?php esc_html_e('Status comandă', 'rima-academy'); ?></div>
            <div class="rima-status-value rima-status-<?php echo esc_attr($order_status); ?>">
                <?php echo esc_html(wc_get_order_status_name($order_status)); ?>
            </div>
        </div>
        
        <?php if ($order_status === 'pending' || $order_status === 'on-hold') : ?>
            <div class="rima-status-action">
                <?php if ($order->get_payment_method() === 'bacs') : ?>
                    <a href="#payment-info" class="rima-btn rima-btn-primary">
                        <?php esc_html_e('Vezi detalii plată', 'rima-academy'); ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Order Details Grid -->
    <div class="rima-thankyou-grid">
        
        <!-- Order Summary -->
        <div class="rima-thankyou-card">
            <div class="rima-card-header">
                <h3 class="rima-card-title">
                    <span class="rima-card-icon">📦</span>
                    <?php esc_html_e('Sumar Comandă', 'rima-academy'); ?>
                </h3>
            </div>
            <div class="rima-card-body">
                <table class="rima-order-items-table">
                    <tbody>
                        <?php foreach ($order_items as $item_id => $item) : 
                            $product = $item->get_product();
                            $qty = $item->get_quantity();
                            $item_name = $item->get_name();
                        ?>
                            <tr>
                                <td class="rima-item-image">
                                    <?php if ($product && $product->get_image_id()) : ?>
                                        <img src="<?php echo esc_url(wp_get_attachment_image_url($product->get_image_id(), 'thumbnail')); ?>" 
                                             alt="<?php echo esc_attr($item_name); ?>">
                                    <?php else : ?>
                                        <div class="rima-item-placeholder">📦</div>
                                    <?php endif; ?>
                                </td>
                                <td class="rima-item-details">
                                    <div class="rima-item-name"><?php echo esc_html($item_name); ?></div>
                                    <div class="rima-item-qty">x<?php echo esc_html($qty); ?></div>
                                </td>
                                <td class="rima-item-total">
                                    <?php echo wp_kses_post($order->get_formatted_line_subtotal($item)); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="rima-order-totals">
                    <div class="rima-total-row">
                        <span class="rima-total-label"><?php esc_html_e('Subtotal', 'rima-academy'); ?></span>
                        <span class="rima-total-value"><?php echo wp_kses_post($order->get_subtotal_to_display()); ?></span>
                    </div>
                    
                    <?php if ($order->get_shipping_total() > 0) : ?>
                        <div class="rima-total-row">
                            <span class="rima-total-label"><?php esc_html_e('Livrare', 'rima-academy'); ?></span>
                            <span class="rima-total-value"><?php echo wp_kses_post(wc_price($order->get_shipping_total())); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($order->get_discount_total() > 0) : ?>
                        <div class="rima-total-row rima-discount">
                            <span class="rima-total-label"><?php esc_html_e('Discount', 'rima-academy'); ?></span>
                            <span class="rima-total-value">-<?php echo wp_kses_post(wc_price($order->get_discount_total())); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="rima-total-row rima-grand-total">
                        <span class="rima-total-label"><?php esc_html_e('Total', 'rima-academy'); ?></span>
                        <span class="rima-total-value"><?php echo wp_kses_post($order->get_formatted_order_total()); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Addresses -->
        <div class="rima-thankyou-card">
            <div class="rima-card-header">
                <h3 class="rima-card-title">
                    <span class="rima-card-icon">📍</span>
                    <?php esc_html_e('Adrese', 'rima-academy'); ?>
                </h3>
            </div>
            <div class="rima-card-body">
                <div class="rima-addresses-row">
                    <div class="rima-address-column">
                        <h4 class="rima-address-type"><?php esc_html_e('Facturare', 'rima-academy'); ?></h4>
                        <address class="rima-address-formatted">
                            <?php echo wp_kses_post($order->get_formatted_billing_address() ?: esc_html__('N/A', 'rima-academy')); ?>
                        </address>
                        <?php if ($order->get_billing_phone()) : ?>
                            <p class="rima-address-phone">📞 <?php echo esc_html($order->get_billing_phone()); ?></p>
                        <?php endif; ?>
                        <?php if ($order->get_billing_email()) : ?>
                            <p class="rima-address-email">📧 <?php echo esc_html($order->get_billing_email()); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($order->needs_shipping_address() && $order->get_formatted_shipping_address()) : ?>
                        <div class="rima-address-column">
                            <h4 class="rima-address-type"><?php esc_html_e('Livrare', 'rima-academy'); ?></h4>
                            <address class="rima-address-formatted">
                                <?php echo wp_kses_post($order->get_formatted_shipping_address()); ?>
                            </address>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
    </div>

    <!-- Payment Details (for BACS) -->
    <?php if ($order->get_payment_method() === 'bacs' && ($order_status === 'pending' || $order_status === 'on-hold')) : ?>
        <div class="rima-payment-info" id="payment-info">
            <div class="rima-card-header">
                <h3 class="rima-card-title">
                    <span class="rima-card-icon">🏦</span>
                    <?php esc_html_e('Detalii Plată - Transfer Bancar', 'rima-academy'); ?>
                </h3>
            </div>
            <div class="rima-card-body">
                <?php do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order_id); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Actions -->
    <div class="rima-thankyou-actions">
        <a href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>" class="rima-btn rima-btn-secondary">
            📦 <?php esc_html_e('Vezi comenzile mele', 'rima-academy'); ?>
        </a>
        <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="rima-btn rima-btn-primary">
            🛒 <?php esc_html_e('Continuă cumpărăturile', 'rima-academy'); ?>
        </a>
    </div>

    <?php else : ?>

    <!-- Order not found -->
    <div class="rima-thankyou-empty">
        <div class="rima-empty-icon">🔍</div>
        <h2><?php esc_html_e('Comanda nu a fost găsită', 'rima-academy'); ?></h2>
        <p><?php esc_html_e('Nu am putut găsi informații despre această comandă.', 'rima-academy'); ?></p>
        <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="rima-btn rima-btn-primary">
            <?php esc_html_e('Mergi la magazin', 'rima-academy'); ?>
        </a>
    </div>

    <?php endif; ?>

</div>

<style>
/* ========================================
   THANK YOU PAGE STYLES
   ======================================== */

.rima-thankyou-page {
    max-width: 900px;
    margin: 0 auto;
    padding: 40px 20px;
}

/* Success Header */
.rima-thankyou-header {
    text-align: center;
    margin-bottom: 40px;
}

.rima-success-animation {
    margin-bottom: 24px;
}

.rima-success-checkmark {
    width: 100px;
    height: 100px;
    margin: 0 auto;
    position: relative;
}

.rima-checkmark-circle {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #00BFA6 0%, #102d56 100%);
    position: relative;
    animation: rima-checkmark-circle 0.6s ease-in-out;
}

.rima-checkmark-stem {
    position: absolute;
    width: 4px;
    height: 35px;
    background: #fff;
    left: 55px;
    top: 35px;
    transform: rotate(45deg);
    animation: rima-checkmark-stem 0.3s ease-in-out 0.3s forwards;
    transform-origin: bottom;
}

.rima-checkmark-kick {
    position: absolute;
    width: 4px;
    height: 20px;
    background: #fff;
    left: 35px;
    top: 55px;
    transform: rotate(-45deg);
    animation: rima-checkmark-kick 0.3s ease-in-out 0.3s forwards;
    transform-origin: bottom;
}

@keyframes rima-checkmark-circle {
    0% { transform: scale(0); }
    100% { transform: scale(1); }
}

.rima-thankyou-title {
    font-size: 32px;
    font-weight: 700;
    color: #1e3a5f;
    margin: 0 0 12px;
}

.rima-thankyou-subtitle {
    font-size: 18px;
    color: #64748b;
    margin: 0;
}

/* Order Status Card */
.rima-order-status-card {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 24px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 30px;
}

.rima-status-icon {
    font-size: 40px;
}

.rima-status-content {
    flex: 1;
}

.rima-status-label {
    font-size: 14px;
    color: #64748b;
    margin-bottom: 4px;
}

.rima-status-value {
    font-size: 20px;
    font-weight: 600;
}

.rima-status-pending { color: #FF9800; }
.rima-status-processing { color: #102d56; }
.rima-status-on-hold { color: #9C27B0; }
.rima-status-completed { color: #00BFA6; }

/* Grid */
.rima-thankyou-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 30px;
}

/* Cards */
.rima-thankyou-card,
.rima-payment-info {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    overflow: hidden;
}

.rima-card-header {
    padding: 16px 24px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.rima-card-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
    font-weight: 600;
    color: #1e3a5f;
    margin: 0;
}

.rima-card-icon {
    font-size: 20px;
}

.rima-card-body {
    padding: 24px;
}

/* Order Items Table */
.rima-order-items-table {
    width: 100%;
    border-collapse: collapse;
}

.rima-order-items-table tr {
    border-bottom: 1px solid #f1f5f9;
}

.rima-order-items-table tr:last-child {
    border-bottom: none;
}

.rima-order-items-table td {
    padding: 12px 0;
    vertical-align: middle;
}

.rima-item-image img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 8px;
}

.rima-item-placeholder {
    width: 50px;
    height: 50px;
    background: #f1f5f9;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.rima-item-name {
    font-weight: 500;
    color: #1e3a5f;
}

.rima-item-qty {
    font-size: 13px;
    color: #64748b;
}

.rima-item-total {
    text-align: right;
    font-weight: 600;
    color: #1e3a5f;
}

/* Order Totals */
.rima-order-totals {
    border-top: 2px solid #e2e8f0;
    margin-top: 16px;
    padding-top: 16px;
}

.rima-total-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
}

.rima-total-label {
    color: #64748b;
}

.rima-total-value {
    font-weight: 500;
    color: #1e3a5f;
}

.rima-grand-total {
    border-top: 1px solid #e2e8f0;
    margin-top: 8px;
    padding-top: 12px;
}

.rima-grand-total .rima-total-label,
.rima-grand-total .rima-total-value {
    font-size: 18px;
    font-weight: 700;
}

.rima-grand-total .rima-total-value {
    color: #00BFA6;
}

.rima-discount .rima-total-value {
    color: #00BFA6;
}

/* Addresses */
.rima-addresses-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}

.rima-address-type {
    font-size: 14px;
    font-weight: 600;
    color: #64748b;
    margin: 0 0 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.rima-address-formatted {
    font-style: normal;
    color: #1e3a5f;
    line-height: 1.6;
}

.rima-address-phone,
.rima-address-email {
    margin: 8px 0 0;
    font-size: 14px;
    color: #64748b;
}

/* Payment Info */
.rima-payment-info {
    margin-bottom: 30px;
}

/* Actions */
.rima-thankyou-actions {
    display: flex;
    justify-content: center;
    gap: 16px;
}

/* Buttons */
.rima-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 28px;
    font-size: 16px;
    font-weight: 500;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.2s ease;
    cursor: pointer;
    border: none;
}

.rima-btn-primary {
    background: linear-gradient(135deg, #00BFA6 0%, #102d56 100%);
    color: #fff;
}

.rima-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 191, 166, 0.35);
}

.rima-btn-secondary {
    background: #f8fafc;
    color: #1e3a5f;
    border: 2px solid #e2e8f0;
}

.rima-btn-secondary:hover {
    border-color: #00BFA6;
    background: #fff;
}

/* Empty State */
.rima-thankyou-empty {
    text-align: center;
    padding: 60px 20px;
}

.rima-empty-icon {
    font-size: 64px;
    margin-bottom: 20px;
}

/* Responsive */
@media (max-width: 768px) {
    .rima-thankyou-grid {
        grid-template-columns: 1fr;
    }
    
    .rima-order-status-card {
        flex-wrap: wrap;
    }
    
    .rima-addresses-row {
        grid-template-columns: 1fr;
    }
    
    .rima-thankyou-actions {
        flex-direction: column;
    }
    
    .rima-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
