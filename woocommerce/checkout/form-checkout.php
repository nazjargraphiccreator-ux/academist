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
// Language toggle is handled globally by RIMA_LANG controller (see functions.php wp_footer)
// Selectors covered: #rima-lang-switch-checkout, .rima-lang-toggle-card, #rima-lang-switch
// No local handler needed.
</script>


<?php
do_action('woocommerce_after_checkout_form', $checkout);

