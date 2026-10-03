<?php
/**
 * RIMA Academy – Checkout Form Template (Bilingual EN/RO)
 *
 * Premium checkout for digital products (online courses).
 * No custom AnaCleaning functions – 100% standard WooCommerce.
 *
 * @package RIMA_Academy
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_checkout_form', $checkout);

// Check cart has contents.
if (WC()->cart->is_empty()) {
    return;
}
?>

<script type="text/javascript">
// Quantity button helper – available immediately
if (typeof window.rimaChangeQty === 'undefined') {
    window.rimaChangeQty = function(btn, action) {
        var $ = jQuery;
        var $input = $(btn).closest('.rima-order-product-qty-wrapper').find('input.qty');
        if (!$input.length) return;
        var val = parseInt($input.val()) || 1;
        var min = parseInt($input.attr('min')) || 1;
        var max = parseInt($input.attr('max')) || 999;
        if (action === 'plus'  && val < max) $input.val(val + 1).trigger('change');
        if (action === 'minus' && val > min) $input.val(val - 1).trigger('change');
        jQuery(document.body).trigger('update_checkout');
    };
}
</script>



<form name="checkout" method="post"
      class="checkout woocommerce-checkout"
      action="<?php echo esc_url(wc_get_checkout_url()); ?>"
      enctype="multipart/form-data">

    <div class="rima-checkout-wrapper">

        <!-- ── HEADER ───────────────────────────────────── -->
        <div class="rima-checkout-header">
            <h1 class="rima-checkout-title">
                <span class="rima-en">Checkout</span>
                <span class="rima-ro">Finalizează Comanda</span>
            </h1>
            <p class="rima-checkout-subtitle">
                <span class="rima-en">Fill in the details below to place your order</span>
                <span class="rima-ro">Completează datele de mai jos pentru a plasa comanda</span>
            </p>
        </div>

        <!-- ── MAIN COLUMN ──────────────────────────────── -->
        <div class="rima-checkout-main">

            <!-- Order Summary -->
            <div class="rima-order-summary-panel">
                <div class="rima-order-summary-header">
                    <span class="rima-order-summary-icon">🛒</span>
                    <h3 class="rima-order-summary-title">
                        <span class="rima-en">Order Summary</span>
                        <span class="rima-ro">Sumar Comandă</span>
                    </h3>
                </div>
                <?php wc_get_template('checkout/review-order.php', array('checkout' => WC()->checkout())); ?>
            </div>

            <!-- Step 1: Billing -->
            <div class="rima-checkout-step" id="rima-billing-step">
                <div class="rima-checkout-step-header">
                    <span class="rima-step-number">1</span>
                    <div>
                        <h3 class="rima-step-title">
                            <span class="rima-en">Billing Details</span>
                            <span class="rima-ro">Date de Facturare</span>
                        </h3>
                        <p class="rima-step-subtitle">
                            <span class="rima-en">Fill in your billing information</span>
                            <span class="rima-ro">Completează datele pentru factură</span>
                        </p>
                    </div>
                </div>
                <div class="rima-checkout-step-body">
                    <?php do_action('woocommerce_checkout_billing'); ?>
                </div>
            </div>

            <!-- Step 2: Additional Info / Notes -->
            <div class="rima-checkout-step" id="rima-notes-step">
                <div class="rima-checkout-step-header">
                    <span class="rima-step-number">2</span>
                    <div>
                        <h3 class="rima-step-title">
                            <span class="rima-en">Additional Information</span>
                            <span class="rima-ro">Informații Suplimentare</span>
                        </h3>
                        <p class="rima-step-subtitle">
                            <span class="rima-en">Notes or special requests (optional)</span>
                            <span class="rima-ro">Observații sau note (opțional)</span>
                        </p>
                    </div>
                </div>
                <div class="rima-checkout-step-body">
                    <?php do_action('woocommerce_checkout_order_review_before'); ?>
                    <?php if (apply_filters('woocommerce_enable_order_notes_field', get_option('woocommerce_enable_order_comments', 'yes') === 'yes')) : ?>
                        <?php if (WC()->cart->needs_shipping() || apply_filters('woocommerce_checkout_show_order_notes_shipping', true)) : ?>
                            <?php woocommerce_form_field('order_comments', array(
                                'type'        => 'textarea',
                                'class'       => array('notes'),
                                'label'       => '',
                                'placeholder' => __('Special notes for your order / Note speciale pentru comandă...', 'rima-academy'),
                            ), ''); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Step 3: Payment -->
            <div class="rima-checkout-step" id="rima-payment-step">
                <div class="rima-checkout-step-header">
                    <span class="rima-step-number">3</span>
                    <div>
                        <h3 class="rima-step-title">
                            <span class="rima-en">Payment Method</span>
                            <span class="rima-ro">Metodă de Plată</span>
                        </h3>
                        <p class="rima-step-subtitle">
                            <span class="rima-en">Choose how you want to pay</span>
                            <span class="rima-ro">Alege cum vrei să plătești</span>
                        </p>
                    </div>
                </div>
                <div class="rima-checkout-step-body">
                    <?php woocommerce_checkout_payment(); ?>
                </div>
            </div>

        </div><!-- /.rima-checkout-main -->

    </div><!-- /.rima-checkout-wrapper -->

    <!-- Mobile Sticky Footer -->
    <div class="rima-mobile-sticky-footer">
        <button type="submit"
                class="rima-btn rima-btn-primary rima-btn-lg rima-place-order-btn"
                name="woocommerce_checkout_place_order">
            🛒 
            <span class="rima-en">Place Order</span>
            <span class="rima-ro">Plasează Comanda</span>
            <span class="rima-place-order-total"><?php echo WC()->cart->get_total(); ?></span>
        </button>
    </div>

</form>

<script>
(function($) {
    $(document).ready(function() {
        // Language switch logic for checkout
        $('#rima-lang-switch-checkout').on('click', function() {
            var isRo = document.body.classList.contains('rima-lang-ro');
            var newLang = isRo ? 'en' : 'ro';
            
            if (newLang === 'ro') {
                document.body.classList.add('rima-lang-ro');
            } else {
                document.body.classList.remove('rima-lang-ro');
            }
            
            localStorage.setItem('rima_lang', newLang);
            
            var domain = window.location.hostname;
            document.cookie = "googtrans=/en/" + newLang + "; path=/; domain=" + domain;
            document.cookie = "googtrans=/en/" + newLang + "; path=/";

            var select = document.querySelector('select.goog-te-combo');
            if (select) {
                select.value = newLang;
                select.dispatchEvent(new Event('change'));
            }

            // Save to server if logged in
            if (typeof rima_ajax_obj !== 'undefined') {
                $.post(rima_ajax_obj.ajax_url, {
                    action: 'rima_save_lang_pref',
                    lang:   newLang,
                    nonce:  rima_ajax_obj.nonce
                });
            }
        });
    });
})(jQuery);
</script>

<style>
/* ── CHECKOUT LAYOUT ──────────────────────────────────── */
.rima-checkout-wrapper {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
    max-width: 760px;
    margin: 0 auto;
}

.rima-checkout-header {
    text-align: center;
    margin-bottom: 8px;
}
.rima-checkout-title  { font-size: 28px; font-weight: 700; color: #1e293b; margin: 0 0 6px; }
.rima-checkout-subtitle { font-size: 15px; color: #64748b; margin: 0; }

.rima-checkout-main { display: flex; flex-direction: column; gap: 20px; }

/* ── ORDER SUMMARY PANEL ─────────────────────────────── */
.rima-order-summary-panel {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    overflow: hidden;
}
.rima-order-summary-header {
    display: flex; align-items: center; gap: 12px;
    padding: 20px 24px;
    background: #fff;
    border-bottom: 1px solid #f1f5f9;
}
.rima-order-summary-title { font-size: 18px; font-weight: 700; color: #1e293b; margin: 0; }
.rima-order-summary-icon  { font-size: 22px; }

/* ── CHECKOUT STEPS ──────────────────────────────────── */
.rima-checkout-step {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.07);
    overflow: hidden;
}
.rima-checkout-step-header {
    display: flex; align-items: center; gap: 16px;
    padding: 16px 24px;
    background: linear-gradient(135deg, #102d56, #102d56);
    color: #fff;
}
.rima-step-number {
    width: 36px; height: 36px;
    background: #fff; color: #102d56;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; font-weight: 700; flex-shrink: 0;
}
.rima-step-title    { font-size: 17px; font-weight: 600; margin: 0; color: #fff; }
.rima-step-subtitle { font-size: 13px; color: rgba(255,255,255,0.8); margin: 0; }

.rima-checkout-step-body { padding: 24px; }

/* WooCommerce form fields inside our steps */
.rima-checkout-step-body .form-row label { font-weight: 600; color: #374151; font-size: 14px; }
.rima-checkout-step-body .form-row input.input-text,
.rima-checkout-step-body .form-row textarea,
.rima-checkout-step-body .form-row select {
    border: 1px solid #d1d5db !important;
    border-radius: 10px !important;
    padding: 12px 16px !important;
    font-size: 15px !important;
    transition: border-color .2s, box-shadow .2s;
    background: #fafafa !important;
}
.rima-checkout-step-body .form-row input.input-text:focus,
.rima-checkout-step-body .form-row select:focus,
.rima-checkout-step-body .form-row textarea:focus {
    border-color: #102d56 !important;
    box-shadow: 0 0 0 4px rgba(16, 45, 86,.15) !important;
    outline: none !important;
    background: #fff !important;
}

/* Payment box */
#payment { background: #f8fafc; border-radius: 12px; padding: 20px; border: 1px solid #e2e8f0; }
#payment ul.payment_methods { border-bottom: 1px solid #e2e8f0; padding-bottom: 16px; list-style: none; margin: 0; }
#payment ul.payment_methods li { margin-bottom: 10px; }
#payment div.payment_box { background: rgba(16, 45, 86,.06); border: 1px solid rgba(16, 45, 86,.2); border-radius: 10px; padding: 14px; margin-top: 12px; font-size: 14px; color: #475569; }
#payment #place_order {
    width: 100%;
    background: linear-gradient(135deg, #ff4757, #ff6b81) !important;
    color: #fff !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 18px 24px !important;
    font-size: 17px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
    box-shadow: 0 6px 20px rgba(255,71,87,.4) !important;
    margin-top: 16px !important;
    cursor: pointer;
    transition: all .2s;
}
#payment #place_order:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 10px 28px rgba(255,71,87,.5) !important;
}

/* ── MOBILE STICKY ───────────────────────────────────── */
.rima-mobile-sticky-footer {
    display: none;
    position: fixed; bottom: 0; left: 0; right: 0;
    background: #fff;
    padding: 14px 16px max(14px, env(safe-area-inset-bottom));
    box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
    z-index: 9999;
    border-top: 1px solid #e2e8f0;
}
.rima-place-order-btn {
    width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px;
    padding: 16px 24px;
    font-size: 17px; font-weight: 700;
    background: linear-gradient(135deg, #ff4757, #ff6b81) !important;
    color: #fff !important; border: none; border-radius: 12px; cursor: pointer;
    box-shadow: 0 4px 16px rgba(255,71,87,.35);
    text-transform: uppercase; letter-spacing: 0.5px;
}
.rima-place-order-total {
    background: rgba(255,255,255,.25); color: #fff;
    padding: 4px 12px; border-radius: 20px;
    font-size: 14px; font-weight: 800;
    border: 1px solid rgba(255,255,255,.4);
}
@media (max-width: 768px) {
    .rima-mobile-sticky-footer { display: block; }
    body { padding-bottom: 110px !important; }
}
</style>
<?php
do_action('woocommerce_after_checkout_form', $checkout);
