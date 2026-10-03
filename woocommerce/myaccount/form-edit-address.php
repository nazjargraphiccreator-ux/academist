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

	<div class="card shadow-sm rounded-4 mb-5 bg-white rima-form-card" style="border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.03) !important;">
		<div class="card-body p-4 p-md-5">
			<form method="post" class="woocommerce-EditAddressForm edit-address">

				<div class="d-flex align-items-center justify-content-between mb-5">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa fa-map-marker text-primary fs-5"></i>
                        </div>
                        <div>
                            <h3 class="h5 fw-bold mb-0 text-dark">
                                <?php if ( 'billing' === $load_address ) : ?>
                                    <span class="rima-en">Billing address</span>
                                    <span class="rima-ro">Adresă de facturare</span>
                                <?php else : ?>
                                    <span class="rima-en">Shipping address</span>
                                    <span class="rima-ro">Adresă de livrare</span>
                                <?php endif; ?>
                            </h3>
                            <p class="small text-muted mb-0 mt-1">
                                <span class="rima-en">Update your address details.</span>
                                <span class="rima-ro">Actualizează detaliile adresei.</span>
                            </p>
                        </div>
                    </div>
					<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>" class="btn btn-sm btn-outline-dark rounded-pill px-4 fw-semibold border">
                        <i class="fa fa-arrow-left me-2"></i>
                        <span class="rima-en">Back</span>
                        <span class="rima-ro">Înapoi</span>
                    </a>
				</div>

				<div class="woocommerce-address-fields">
					<?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>

                    <?php if ( 'billing' === $load_address ) : ?>
                        <!-- PF/PJ Toggle (Custom) -->
                        <div class="rima-client-type-wrapper mb-4">
                            <div class="d-flex gap-4">
                                <label class="cursor-pointer d-flex align-items-center gap-2 text-secondary">
                                    <input type="radio" name="rima_client_type" value="pf" checked> 
                                    <span class="rima-en">Persoană Fizică (Individual)</span>
                                    <span class="rima-ro">Persoană Fizică (Individual)</span>
                                </label>
                                <label class="cursor-pointer d-flex align-items-center gap-2 text-secondary">
                                    <input type="radio" name="rima_client_type" value="pj"> 
                                    <span class="rima-en">Persoană Juridică (Companie)</span>
                                    <span class="rima-ro">Persoană Juridică (Companie)</span>
                                </label>
                            </div>
                        </div>

                        <!-- ANAF Search (Hidden by default, shown for PJ) -->
                        <div id="rima-b2b-anaf-container" class="card p-4 mb-4 bg-light border-0 rounded-4" style="background-color: #f8fafc !important; display: none;">
                            <label class="fw-bold mb-2 text-dark" style="font-size: 14px;"><i class="fa fa-building text-primary me-2"></i>Date Companie (Căutare ANAF)</label>
                            <div class="d-flex gap-2">
                                <input type="text" id="rima_billing_cui_search" class="form-control rounded-3 border-0 shadow-sm" placeholder="Introdu CUI (ex: RO123456)" style="padding: 12px 16px;">
                                <button type="button" id="rima_billing_anaf_btn" class="btn btn-primary rounded-3 px-4 fw-bold shadow-sm" style="background: #3b82f6; border:none; transition: 0.2s;">Caută</button>
                            </div>
                            <div id="rima_billing_anaf_status" class="mt-2 small fw-medium"></div>
                        </div>
                    <?php endif; ?>

					<div class="woocommerce-address-fields__field-wrapper rima-custom-checkout-fields mb-5">
						<?php
						foreach ( $address as $key => $field ) {
							woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $field['value'] ) );
						}
						?>
					</div>

					<?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>

					<div class="mt-4 d-flex justify-content-end">
                        <?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>
						<button type="submit" class="btn btn-primary px-5 py-3 fw-bold rounded-pill shadow rima-hover-lift text-uppercase text-white" style="letter-spacing:1px; background: linear-gradient(135deg, #102d56, #1d4ed8) !important; border:none;" name="save_address" value="Save address">
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

<?php if ( 'billing' === $load_address ) : ?>
<script>
jQuery(document).ready(function($) {

    // Toggle PF/PJ fields
    function toggleB2BFields() {
        var val = $('input[name="rima_client_type"]:checked').val();
        if (val === 'pj') {
            $('#billing_company_field, .rima-pj-field').slideDown();
            $('#rima-b2b-anaf-container').slideDown();
        } else {
            $('#billing_company_field, .rima-pj-field').slideUp();
            $('#rima-b2b-anaf-container').slideUp();
        }
    }

    $('input[name="rima_client_type"]').on('change', toggleB2BFields);
    
    // Initial check (if company is filled, select PJ)
    if ($('#billing_company').val() || $('#billing_cui').val()) {
        $('input[name="rima_client_type"][value="pj"]').prop('checked', true);
    }
    toggleB2BFields();

    $('#rima_billing_anaf_btn').on('click', function(e) {
        e.preventDefault();
        var cui = $('#rima_billing_cui_search').val();
        if (!cui) return;
        
        var $btn = $(this);
        $btn.text('Se caută...').prop('disabled', true).css('opacity', '0.7');
        $('#rima_billing_anaf_status').html('<span class="text-warning"><i class="fa fa-spinner fa-spin me-1"></i> Se comunică cu ANAF...</span>');
        
        $.post('<?php echo admin_url("admin-ajax.php"); ?>', {
            action: 'rima_anaf_lookup',
            cui: cui,
            nonce: '<?php echo wp_create_nonce("rima_anaf_lookup"); ?>'
        }, function(res) {
            $btn.text('Caută').prop('disabled', false).css('opacity', '1');
            if (res.success) {
                $('#rima_billing_anaf_status').html('<span class="text-success"><i class="fa fa-check-circle me-1"></i> Datele firmei au fost completate mai jos.</span>');
                
                if ($('#billing_company').length) $('#billing_company').val(res.data.name);
                if ($('#billing_cui').length) $('#billing_cui').val(cui);
                if ($('#billing_reg_com').length) $('#billing_reg_com').val(res.data.reg_com);
                if ($('#billing_city').length) $('#billing_city').val(res.data.city);
                if ($('#billing_address_1').length) $('#billing_address_1').val(res.data.address);
                if ($('#billing_postcode').length && res.data.zip) $('#billing_postcode').val(res.data.zip);
                
                $('#billing_company, #billing_cui, #billing_reg_com, #billing_city, #billing_address_1').trigger('change');
            } else {
                $('#rima_billing_anaf_status').html('<span class="text-danger"><i class="fa fa-exclamation-circle me-1"></i> ' + res.data + '</span>');
            }
        }).fail(function() {
            $btn.text('Caută').prop('disabled', false).css('opacity', '1');
            $('#rima_billing_anaf_status').html('<span class="text-danger"><i class="fa fa-exclamation-circle me-1"></i> Eroare de conexiune.</span>');
        });
    });
});
</script>
<?php endif; ?>

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
