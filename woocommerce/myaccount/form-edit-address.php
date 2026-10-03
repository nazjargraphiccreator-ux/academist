<?php
/**
 * Edit address form - Bootstrap 5 Modernized (Bilingual EN/RO)
 * academist-child/woocommerce/myaccount/form-edit-address.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$page_title = ( 'billing' === $load_address ) ? __( 'Billing address', 'woocommerce' ) : __( 'Shipping address', 'woocommerce' );

do_action( 'woocommerce_before_edit_account_address_form' ); ?>

<?php if ( ! $load_address ) : ?>
	<?php wc_get_template( 'myaccount/my-address.php' ); ?>
<?php else : ?>

	<div class="card border-0 shadow-sm rounded-4 mb-4 rima-dashboard-content">
		<div class="card-body p-4 p-md-5">
			<form method="post" class="woocommerce-EditAddressForm edit-address">

				<div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
					<h3 class="h4 fw-bold m-0 text-dark">
                        <?php if ( 'billing' === $load_address ) : ?>
                            <span class="rima-en">Billing address</span>
                            <span class="rima-ro">Adresă de facturare</span>
                        <?php else : ?>
                            <span class="rima-en">Shipping address</span>
                            <span class="rima-ro">Adresă de livrare</span>
                        <?php endif; ?>
					</h3>
					<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i class="fa fa-arrow-left me-1"></i>
                        <span class="rima-en">Back</span>
                        <span class="rima-ro">Înapoi</span>
                    </a>
				</div>

				<div class="woocommerce-address-fields">
					<?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>

					<div class="woocommerce-address-fields__field-wrapper rima-custom-checkout-fields mb-4">
						<?php
						foreach ( $address as $key => $field ) {
							woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $field['value'] ) );
						}
						?>
					</div>

					<?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>

					<div class="mt-4 pt-3 border-top">
                        <?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>
						<button type="submit" class="btn btn-primary px-5 py-3 fw-bold rounded-3 shadow-sm text-uppercase" style="letter-spacing:1px; background-color:#991b1b !important; border:none; color:white !important;" name="save_address" value="Save address">
                            <i class="fa fa-save me-2"></i>
                            <span class="rima-en">Save address</span>
                            <span class="rima-ro">Salvează adresa</span>
                        </button>
						<input type="hidden" name="action" value="edit_address" />
					</div>
				</div>

			</form>
		</div>
	</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_edit_account_address_form' ); ?>

<style>
/* Style the WooCommerce generated fields to match our Bootstrap 5 theme */
.rima-custom-checkout-fields .form-row {
    margin-bottom: 1.25rem;
}
.rima-custom-checkout-fields label {
    font-weight: 600;
    color: #374151;
    font-size: 14px;
    margin-bottom: 0.5rem;
    display: block;
}
.rima-custom-checkout-fields input.input-text,
.rima-custom-checkout-fields textarea,
.rima-custom-checkout-fields select {
    width: 100%;
    border: 1px solid #d1d5db !important;
    border-radius: 10px !important;
    padding: 12px 16px !important;
    font-size: 15px !important;
    transition: border-color .2s, box-shadow .2s;
    background: #fafafa !important;
}
.rima-custom-checkout-fields input.input-text:focus,
.rima-custom-checkout-fields select:focus,
.rima-custom-checkout-fields textarea:focus {
    border-color: #102d56 !important;
    box-shadow: 0 0 0 4px rgba(16, 45, 86,.15) !important;
    outline: none !important;
    background: #fff !important;
}
.rima-custom-checkout-fields .select2-container--default .select2-selection--single {
    height: 48px;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    background: #fafafa;
}
.rima-custom-checkout-fields .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 48px;
    padding-left: 16px;
    color: #374151;
}
.rima-custom-checkout-fields .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 46px;
    right: 10px;
}
</style>
