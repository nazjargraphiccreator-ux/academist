<?php
/**
 * RIMA Academy - Address Modal
 * Modal pentru adăugare/editare adrese cu toggle PF/PJ
 * Identic cu Ana Cleaning, adaptat la design RIMA dark-navy
 */
defined('ABSPATH') || exit;

$countries = WC()->countries->get_countries();
?>

<!-- Modal Backdrop -->
<div class="rima-modal-backdrop" id="rimaModalBackdrop"></div>

<!-- Address Modal -->
<div class="rima-addr-modal" id="rimaAddressModal">

    <div class="rima-addr-modal-header">
        <h3 class="rima-addr-modal-title" id="rimaModalTitle"><?php esc_html_e('Adaugă Adresă Facturare', 'rima-academy'); ?></h3>
        <button type="button" class="rima-addr-modal-close" id="rimaModalClose">&times;</button>
    </div>

    <form id="rimaAddressForm" class="rima-addr-form">
        <div class="rima-addr-modal-body">

            <!-- Hidden Fields -->
            <input type="hidden" name="address_id" id="rimaAddrId" value="">
            <input type="hidden" name="address_type" id="rimaAddrType" value="billing">
            <input type="hidden" name="entity_type" id="rimaEntityType" value="pf">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('rima_address_nonce'); ?>">

            <!-- PF / PJ Toggle -->
            <div class="rima-entity-toggle">
                <button type="button" class="rima-entity-btn is-active" data-type="pf">
                    👤 <span>PERSOANĂ FIZICĂ</span>
                </button>
                <button type="button" class="rima-entity-btn" data-type="pj">
                    🏢 <span>PERSOANĂ JURIDICĂ</span>
                </button>
            </div>

            <!-- ANAF Lookup (PJ Only) -->
            <div class="rima-anaf-lookup" id="rimaAnafLookup" style="display:none;">
                <div class="rima-anaf-row">
                    <input type="text" id="rimaAnafCui" class="rima-addr-input" placeholder="Introdu CUI (ex: 42467528)">
                    <button type="button" id="rimaAnafBtn" class="rima-btn-anaf">
                        <span class="rima-anaf-text">🔍 Caută</span>
                        <span class="rima-anaf-loading" style="display:none;">⏳ Se caută...</span>
                    </button>
                </div>
                <div id="rimaAnafStatus" class="rima-anaf-status"></div>
            </div>

            <!-- PJ Specific Fields -->
            <div class="rima-pj-fields" id="rimaPjFields" style="display:none;">
                <div class="rima-form-row-2">
                    <div class="rima-form-group">
                        <label class="rima-form-label required">CUI / CIF:</label>
                        <input type="text" name="vat_number" id="rimaVatNumber" class="rima-addr-input" placeholder="RO42467528">
                    </div>
                    <div class="rima-form-group">
                        <label class="rima-form-label">Nr. Reg. Comerțului:</label>
                        <input type="text" name="reg_number" id="rimaRegNumber" class="rima-addr-input" placeholder="J40/1234/2020">
                    </div>
                </div>
                <div class="rima-form-row-2">
                    <div class="rima-form-group">
                        <label class="rima-form-label required">Nume Firmă:</label>
                        <input type="text" name="company" id="rimaCompany" class="rima-addr-input" placeholder="S.C. Exemplu S.R.L.">
                    </div>
                    <div class="rima-form-group">
                        <label class="rima-form-label">IBAN:</label>
                        <input type="text" name="iban" id="rimaIban" class="rima-addr-input" placeholder="RO49AAAA1B31007593840000">
                    </div>
                </div>
            </div>

            <!-- PF Fields -->
            <div class="rima-pf-fields" id="rimaPfFields">
                <div class="rima-form-row-2">
                    <div class="rima-form-group">
                        <label class="rima-form-label required">Prenume:</label>
                        <input type="text" name="first_name" id="rimaFirstName" class="rima-addr-input" placeholder="Prenumele tău">
                    </div>
                    <div class="rima-form-group">
                        <label class="rima-form-label required">Nume:</label>
                        <input type="text" name="last_name" id="rimaLastName" class="rima-addr-input" placeholder="Numele de familie">
                    </div>
                </div>
            </div>

            <!-- Common Address Fields -->
            <div class="rima-form-group">
                <label class="rima-form-label required">Adresa:</label>
                <input type="text" name="address_1" id="rimaAddr1" class="rima-addr-input" placeholder="Strada (ex: Str. Tudor Vladimirescu)">
            </div>

            <div class="rima-form-group">
                <label class="rima-form-label">Număr, Bloc, Apartament:</label>
                <input type="text" name="address_2" id="rimaAddr2" class="rima-addr-input" placeholder="Nr. 23, Bl. A, Sc. 2, Ap. 15">
            </div>

            <!-- Location: Country + Region + City + Postcode -->
            <div class="rima-form-row-2">
                <div class="rima-form-group">
                    <label class="rima-form-label required">Țară:</label>
                    <select name="country" id="rimaCountry" class="rima-addr-select">
                        <option value="">Selectează țara...</option>
                        <?php foreach ($countries as $code => $name) : ?>
                            <option value="<?php echo esc_attr($code); ?>" <?php selected($code, 'RO'); ?>>
                                <?php echo esc_html($name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="rima-form-group" id="rimaStateWrap">
                    <label class="rima-form-label">Județ / Regiune:</label>
                    <input type="text" name="state" id="rimaState" class="rima-addr-input" placeholder="ex: Argeș, Ilfov, Catalonia...">
                </div>
            </div>
            
            <div class="rima-form-row-2">
                <div class="rima-form-group" id="rimaCityWrap" style="position:relative;">
                    <label class="rima-form-label required">Oraș:</label>
                    <input type="text" name="city" id="rimaCity" class="rima-addr-input" placeholder="Caută orașul..." autocomplete="off">
                    <div id="rimaCityDropdown" class="rima-autocomplete-dropdown" style="display:none;"></div>
                </div>
                <div class="rima-form-group">
                    <label class="rima-form-label">Cod Poștal:</label>
                    <input type="text" name="postcode" id="rimaPostcode" class="rima-addr-input" placeholder="123456">
                </div>
            </div>

            <!-- Phone & Email -->
            <div class="rima-form-row-2">
                <div class="rima-form-group">
                    <label class="rima-form-label required">Telefon:</label>
                    <input type="tel" name="phone" id="rimaPhone" class="rima-addr-input" placeholder="0712 345 678">
                </div>
                <div class="rima-form-group">
                    <label class="rima-form-label">Email:</label>
                    <input type="email" name="email" id="rimaEmail" class="rima-addr-input" placeholder="contact@exemplu.com">
                </div>
            </div>



            <!-- Default Checkbox -->
            <div class="rima-checkbox-wrapper">
                <input type="checkbox" name="is_default" id="rimaIsDefault" value="1" class="rima-checkbox">
                <label for="rimaIsDefault" class="rima-checkbox-label">
                    Setează ca adresă predefinită
                </label>
            </div>

        </div><!-- /.rima-addr-modal-body -->

        <div class="rima-addr-modal-footer">
            <button type="button" class="rima-btn-cancel" id="rimaModalCancel">ANULEAZĂ</button>
            <button type="submit" class="rima-btn-save" id="rimaModalSave">
                <span class="rima-save-text">SALVEAZĂ ADRESA</span>
                <span class="rima-save-loading" style="display:none;">⏳ Se salvează...</span>
            </button>
        </div>
    </form>
</div>

<style>
/* ================================================================
   RIMA ADDRESS MODAL - Dark Navy Design System
   Identic cu Ana Cleaning modal, adaptat RIMA colors
   ================================================================ */

.rima-modal-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    z-index: 99998;
    backdrop-filter: blur(3px);
    animation: rimaFadeIn 0.2s ease;
}
.rima-modal-backdrop.is-open { display: block; }

.rima-addr-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.96);
    width: 90%;
    max-width: 680px;
    max-height: 90vh;
    overflow-y: auto;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25);
    z-index: 99999;
    transition: transform 0.25s ease, opacity 0.25s ease;
    opacity: 0;
}
.rima-addr-modal.is-open {
    display: block;
    transform: translate(-50%, -50%) scale(1);
    opacity: 1;
    animation: rimaSlideUp 0.3s ease forwards;
}

@keyframes rimaFadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes rimaSlideUp {
    from { transform: translate(-50%, calc(-50% + 20px)) scale(0.97); opacity: 0; }
    to   { transform: translate(-50%, -50%) scale(1); opacity: 1; }
}

/* Modal Header */
.rima-addr-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    background: linear-gradient(135deg, #102d56, #1d4ed8);
    border-radius: 16px 16px 0 0;
}
.rima-addr-modal-title {
    color: #fff;
    font-size: 17px;
    font-weight: 700;
    margin: 0;
}
.rima-addr-modal-close {
    background: rgba(255,255,255,0.15);
    border: none;
    color: #fff;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}
.rima-addr-modal-close:hover { background: rgba(255,255,255,0.25); }

/* Modal Body */
.rima-addr-modal-body {
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

/* PF/PJ Toggle */
.rima-entity-toggle {
    display: flex;
    gap: 0;
    background: #f1f5f9;
    border-radius: 50px;
    padding: 4px;
    border: 1px solid #e2e8f0;
}
.rima-entity-btn {
    flex: 1;
    padding: 10px 16px;
    border: none;
    background: transparent;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 700;
    color: #64748b;
    cursor: pointer;
    transition: all 0.25s ease;
    letter-spacing: 0.3px;
}
.rima-entity-btn.is-active {
    background: linear-gradient(135deg, #102d56, #1d4ed8);
    color: #fff;
    box-shadow: 0 2px 10px rgba(29, 78, 216, 0.35);
}

/* ANAF Lookup */
.rima-anaf-lookup {
    background: linear-gradient(135deg, rgba(16, 45, 86, 0.04), rgba(29, 78, 216, 0.06));
    border: 1px solid rgba(29, 78, 216, 0.15);
    border-radius: 10px;
    padding: 14px;
}
.rima-anaf-row {
    display: flex;
    gap: 10px;
}
.rima-btn-anaf {
    white-space: nowrap;
    padding: 0 18px;
    background: linear-gradient(135deg, #102d56, #1d4ed8);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    transition: opacity 0.2s;
    min-height: 44px;
}
.rima-btn-anaf:hover { opacity: 0.9; }
.rima-anaf-status {
    margin-top: 8px;
    font-size: 13px;
    font-weight: 500;
}
.rima-anaf-ok { color: #059669; }
.rima-anaf-err { color: #dc2626; }
.rima-anaf-loading-msg { color: #d97706; }

/* Form Groups */
.rima-form-group { display: flex; flex-direction: column; gap: 5px; }
.rima-form-label {
    font-size: 13px;
    font-weight: 600;
    color: #374151;
}
.rima-form-label.required::after { content: ' *'; color: #ef4444; }
.rima-addr-input, .rima-addr-select {
    width: 100%;
    padding: 11px 14px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    color: #1e293b;
    background: #fff;
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
    box-sizing: border-box;
}
.rima-addr-input:focus, .rima-addr-select:focus {
    border-color: #1d4ed8;
    box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
}

/* Row Layouts */
.rima-form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.rima-form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }

/* Checkbox */
.rima-checkbox-wrapper { display: flex; align-items: center; gap: 10px; }
.rima-checkbox {
    width: 18px; height: 18px;
    accent-color: #1d4ed8;
    cursor: pointer;
    flex-shrink: 0;
}
.rima-checkbox-label { font-size: 13px; color: #475569; cursor: pointer; }

/* Modal Footer */
.rima-addr-modal-footer {
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 0 24px 24px;
}
.rima-btn-save {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #102d56, #1d4ed8);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 700;
    letter-spacing: 0.5px;
    cursor: pointer;
    transition: opacity 0.2s, transform 0.1s;
}
.rima-btn-save:hover { opacity: 0.9; }
.rima-btn-save:active { transform: scale(0.99); }
.rima-btn-cancel {
    width: 100%;
    padding: 10px;
    background: transparent;
    color: #64748b;
    border: none;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.5px;
    cursor: pointer;
    text-decoration: underline;
}

/* Mobile */
@media (max-width: 576px) {
    .rima-addr-modal { width: 94%; max-height: 95vh; }
    .rima-form-row-2 { grid-template-columns: 1fr; }
    .rima-form-row-3 { grid-template-columns: 1fr 1fr; }
    .rima-form-row-3 .rima-form-group:last-child { grid-column: 1 / -1; }
    .rima-addr-modal-body { padding: 16px; gap: 12px; }
    .rima-entity-btn { font-size: 11px; padding: 9px 10px; }
}

/* Autocomplete Dropdown */
.rima-autocomplete-dropdown {
    position: absolute;
    top: calc(100% + 2px);
    left: 0;
    right: 0;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    z-index: 9999;
    max-height: 220px;
    overflow-y: auto;
    animation: rimaFadeIn 0.15s ease;
}
.rima-autocomplete-item {
    padding: 10px 14px;
    cursor: pointer;
    font-size: 13px;
    color: #1e293b;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s;
}
.rima-autocomplete-item:last-child { border-bottom: none; }
.rima-autocomplete-item:hover { background: #f0f7ff; }
.rima-autocomplete-item strong { font-weight: 700; color: #102d56; }
.rima-autocomplete-item span { color: #94a3b8; font-size: 12px; margin-left: 6px; }
</style>

<script>
(function($) {
    'use strict';

    /* ============================
       RIMA Address Modal System
       ============================ */

    var modal = {
        $modal: $('#rimaAddressModal'),
        $backdrop: $('#rimaModalBackdrop'),
        $form: $('#rimaAddressForm'),

        open: function(opts) {
            opts = opts || {};
            modal.$modal.addClass('is-open');
            modal.$backdrop.addClass('is-open');
            document.body.style.overflow = 'hidden';

            // Set modal title
            $('#rimaModalTitle').text(opts.editMode
                ? 'Editează Adresă'
                : 'Adaugă Adresă ' + (opts.addressType === 'billing' ? 'Facturare' : 'Livrare'));

            // Set hidden fields
            $('#rimaAddrId').val(opts.addressId || '');
            $('#rimaAddrType').val(opts.addressType || 'billing');

            // Reset form
            modal.$form[0].reset();

            // Trigger country-based location rendering (default RO)
            var defaultCountry = (opts.data && opts.data.country) ? opts.data.country : 'RO';
            $('#rimaCountry').val(defaultCountry);
            renderLocationForCountry(defaultCountry);

            modal.setEntityType(opts.entityType || 'pf');
            $('#rimaAnafStatus').html('').removeClass();

            // Fill data if editing
            if (opts.data) { modal.fillForm(opts.data); }
        },

        close: function() {
            modal.$modal.removeClass('is-open');
            modal.$backdrop.removeClass('is-open');
            document.body.style.overflow = '';
        },

        setEntityType: function(type) {
            $('#rimaEntityType').val(type);
            $('.rima-entity-btn').removeClass('is-active');
            $('.rima-entity-btn[data-type="' + type + '"]').addClass('is-active');

            if (type === 'pj') {
                $('#rimaPjFields').slideDown(200);
                $('#rimaPfFields').slideUp(200);
                $('#rimaAnafLookup').slideDown(200);
            } else {
                $('#rimaPjFields').slideUp(200);
                $('#rimaPfFields').slideDown(200);
                $('#rimaAnafLookup').slideUp(200);
            }
        },

        fillForm: function(data) {
            // First set country and rebuild location fields
            var country = data.country || 'RO';
            $('#rimaCountry').val(country);
            renderLocationForCountry(country);

            $.each(data, function(key, val) {
                if (key === 'country') return; // already set
                var $el = modal.$form.find('[name="' + key + '"]');
                if ($el.length) {
                    if ($el.attr('type') === 'checkbox') {
                        $el.prop('checked', !!val);
                    } else {
                        $el.val(val);
                    }
                }
            });

            // Handle Romania county → city cascade on edit
            if (country === 'RO' && data.state) {
                var $stateSelect = $('#rimaState');
                if ($stateSelect.is('select')) {
                    $stateSelect.val(data.state).trigger('change.ro');
                    setTimeout(function() {
                        if (data.city) { $('#rimaCity').val(data.city); }
                    }, 100);
                }
            } else if (data.state) {
                setTimeout(function() {
                    if (data.city) { $('#rimaCity').val(data.city); }
                }, 200);
            }

            // entity type from data
            if (data.entity_type) { modal.setEntityType(data.entity_type); }
        },

        save: function() {
            var $btn = $('#rimaModalSave');
            var $text = $btn.find('.rima-save-text');
            var $loading = $btn.find('.rima-save-loading');

            $text.hide(); $loading.show();
            $btn.prop('disabled', true);

            // Build data
            var formData = {};
            modal.$form.serializeArray().forEach(function(f) { formData[f.name] = f.value; });

            // Handle manual city input ("Altul" option)
            var $cityManual = $('#rimaCityManual');
            if ($cityManual.length && $cityManual.val()) {
                formData.city = $cityManual.val();
            }
            // city select with __other__
            if (formData.city === '__other__') { formData.city = ''; }

            $.post(rimaAjaxData.ajaxUrl, $.extend({action: 'ana_save_address'}, formData), function(res) {
                if (res.success) {
                    modal.close();
                    rimaAddresses.showNotice(res.data.message || 'Adresă salvată!', 'success');
                    if (res.data.reload) { setTimeout(function() { location.reload(); }, 900); }
                } else {
                    rimaAddresses.showNotice(res.data || 'Eroare la salvare.', 'error');
                }
            }).fail(function() {
                rimaAddresses.showNotice('Eroare de conexiune. Încearcă din nou.', 'error');
            }).always(function() {
                $text.show(); $loading.hide();
                $btn.prop('disabled', false);
            });
        }
    };

    /* ============================
       RIMA Addresses Main Logic
       ============================ */
    var rimaAddresses = window.rimaAddresses = {

        showNotice: function(msg, type) {
            var $n = $('<div class="rima-addr-notice rima-addr-notice--' + type + '">' + msg + '</div>');
            $('body').append($n);
            setTimeout(function() { $n.addClass('is-visible'); }, 50);
            setTimeout(function() { $n.removeClass('is-visible'); setTimeout(function() { $n.remove(); }, 300); }, 3000);
        },

        deleteAddress: function(addressId, addressType) {
            if (!confirm('Ești sigur că vrei să ștergi această adresă?')) return;
            $.post(rimaAjaxData.ajaxUrl, {
                action: 'ana_delete_address',
                address_id: addressId,
                address_type: addressType
            }, function(res) {
                if (res.success) {
                    rimaAddresses.showNotice(res.data.message || 'Adresă ștearsă!', 'success');
                    $('[data-rima-address-id="' + addressId + '"]').closest('.rima-addr-card').fadeOut(400, function() {
                        $(this).remove();
                    });
                } else {
                    rimaAddresses.showNotice(res.data || 'Eroare la ștergere.', 'error');
                }
            });
        },

        setDefault: function(addressId, addressType) {
            $.post(rimaAjaxData.ajaxUrl, {
                action: 'ana_set_default_address',
                address_id: addressId,
                address_type: addressType
            }, function(res) {
                if (res.success) {
                    rimaAddresses.showNotice('Adresă predefinită setată!', 'success');
                    setTimeout(function() { location.reload(); }, 800);
                }
            });
        }
    };

    /* ============================
       Event Bindings
       ============================ */
    $(document).ready(function() {

        // Open modal - Add button / card
        $(document).on('click', '[data-rima-modal-open]', function() {
            var $t = $(this);
            modal.open({
                addressType: $t.data('address-type') || 'billing',
                entityType:  $t.data('entity-type') || 'pf',
                addressId:   '',
                editMode:    false
            });
        });

        // Open modal - Edit button
        $(document).on('click', '.rima-addr-edit-btn', function() {
            var $t = $(this);
            var data = $t.closest('[data-rima-address-id]').data('address-json') || {};
            modal.open({
                addressType: $t.data('address-type') || 'billing',
                entityType:  $t.data('entity-type') || 'pf',
                addressId:   $t.data('address-id'),
                editMode:    true,
                data:        data
            });
        });

        // Close modal
        $(document).on('click', '#rimaModalClose, #rimaModalCancel, .rima-modal-backdrop', function() {
            modal.close();
        });
        $(document).on('keyup', function(e) {
            if (e.key === 'Escape') modal.close();
        });

        // PF/PJ toggle
        $(document).on('click', '.rima-entity-btn', function() {
            modal.setEntityType($(this).data('type'));
        });

        // Form submit
        $('#rimaAddressForm').on('submit', function(e) {
            e.preventDefault();
            modal.save();
        });

        // Delete
        $(document).on('click', '.rima-addr-delete-btn', function() {
            rimaAddresses.deleteAddress($(this).data('address-id'), $(this).data('address-type'));
        });

        // Set default
        $(document).on('click', '.rima-addr-default-btn', function() {
            rimaAddresses.setDefault($(this).data('address-id'), $(this).data('address-type'));
        });

        /* ============================
           Smart Location: RO = dropdown, altele = autocomplete
           ============================ */

        // Romania județe → date complete
        var RO_JUDETE = {
            'AB':'Alba','AR':'Arad','AG':'Argeș','BC':'Bacău','BH':'Bihor','BN':'Bistrița-Năsăud',
            'BT':'Botoșani','BV':'Brașov','BR':'Brăila','B':'București','BZ':'Buzău','CS':'Caraș-Severin',
            'CL':'Călărași','CJ':'Cluj','CT':'Constanța','CV':'Covasna','DB':'Dâmbovița','DJ':'Dolj',
            'GL':'Galați','GR':'Giurgiu','GJ':'Gorj','HR':'Harghita','HD':'Hunedoara','IL':'Ialomița',
            'IS':'Iași','IF':'Ilfov','MM':'Maramureș','MH':'Mehedinți','MS':'Mureș','NT':'Neamț',
            'OT':'Olt','PH':'Prahova','SM':'Satu Mare','SJ':'Sălaj','SB':'Sibiu','SV':'Suceava',
            'TR':'Teleorman','TM':'Timiș','TL':'Tulcea','VS':'Vaslui','VL':'Vâlcea','VN':'Vrancea'
        };

        // Orașe complete per județ RO
        var RO_ORASE = {
            'AB':['Alba Iulia','Aiud','Blaj','Cugir','Sebeș','Câmpeni','Abrud','Ocna Mureș','Teiuș','Zlatna'],
            'AR':['Arad','Curtici','Ineu','Lipova','Nădlac','Pecica','Pâncota','Sântana','Sebiș','Chișineu-Criș'],
            'AG':['Pitești','Câmpulung','Curtea de Argeș','Mioveni','Costești','Topoloveni','Ștefănești','Zăvoi'],
            'BC':['Bacău','Onești','Moinești','Buhuși','Comănești','Dărmănești','Slănic-Moldova','Tg. Ocna'],
            'BH':['Oradea','Salonta','Marghita','Beiuș','Aleșd','Nucet','Ștei','Valea lui Mihai'],
            'BN':['Bistrița','Beclean','Năsăud','Sângeorz-Băi'],
            'BT':['Botoșani','Dorohoi','Darabani','Flămânzi','Săveni','Ștefănești'],
            'BV':['Brașov','Codlea','Făgăraș','Ghimbav','Predeal','Râșnov','Rupea','Săcele','Victoria','Zărnești'],
            'BR':['Brăila','Ianca','Făurei'],
            'B':['București','Sector 1','Sector 2','Sector 3','Sector 4','Sector 5','Sector 6'],
            'BZ':['Buzău','Râmnicu Sărat','Nehoiu','Pătârlagele','Pogoanele'],
            'CS':['Reșița','Caransebeș','Băile Herculane','Moldova Nouă','Oravița','Oțelu Roșu'],
            'CL':['Călărași','Oltenița','Lehliu Gară','Budești'],
            'CJ':['Cluj-Napoca','Turda','Dej','Câmpia Turzii','Gherla','Huedin'],
            'CT':['Constanța','Mangalia','Medgidia','Cernavodă','Eforie','Năvodari','Ovidiu','Techirghiol'],
            'CV':['Sfântu Gheorghe','Covasna','Întorsura Buzăului','Tg. Secuiesc','Bixad'],
            'DB':['Târgoviște','Moreni','Pucioasa','Fieni','Răcari','Titu'],
            'DJ':['Craiova','Băilești','Calafat','Segarcea','Bechet','Dăbuleni','Filiaș'],
            'GL':['Galați','Tecuci','Tg. Bujor','Berești'],
            'GR':['Giurgiu','Bolintin-Vale','Mihăilești'],
            'GJ':['Tg. Jiu','Motru','Rovinari','Tismana','Tg. Cărbuneşti','Bumbeşti-Jiu'],
            'HR':['Miercurea Ciuc','Odorheiu Secuiesc','Gheorgheni','Toplița','Vlăhița','Borsec'],
            'HD':['Deva','Hunedoara','Petroșani','Lupeni','Orăștie','Simeria','Uricani','Vulcan','Aninoasa','Brad'],
            'IL':['Slobozia','Fetești','Urziceni','Amara','Căzănești'],
            'IS':['Iași','Pașcani','Hârlău','Târgu Frumos','Podu Iloaiei'],
            'IF':['Buftea','Bragadiru','Chitila','Măgurele','Otopeni','Pantelimon','Popești-Leordeni','Voluntari'],
            'MM':['Baia Mare','Sighetu Marmației','Borșa','Câmpulung la Tisa','Seini','Șomcuta Mare','Tg. Lăpuș','Vișeu de Sus'],
            'MH':['Drobeta-Turnu Severin','Orșova','Strehaia','Vânju Mare'],
            'MS':['Tg. Mureș','Sighișoara','Reghin','Tg. Secuiesc','Iernut','Luduș','Sovata','Ungheni'],
            'NT':['Piatra Neamț','Roman','Tg. Neamț','Bicaz','Roznov','Ștefan cel Mare'],
            'OT':['Slatina','Caracal','Balș','Corabia','Drăgănești-Olt','Piatra Olt','Potcoava','Scornicești'],
            'PH':['Ploiești','Câmpina','Sinaia','Băicoi','Breaza','Bușteni','Comarnic','Mizil','Plopeni','Urlați','Vălenii de Munte'],
            'SM':['Satu Mare','Carei','Negrești-Oaș','Tășnad'],
            'SJ':['Zalău','Jibou','Șimleu Silvaniei'],
            'SB':['Sibiu','Mediaș','Cisnădie','Agnita','Avrig','Copșa Mică','Ocna Sibiului','Dumbrăveni'],
            'SV':['Suceava','Câmpulung Moldovenesc','Fălticeni','Gura Humorului','Rădăuți','Siret','Vatra Dornei'],
            'TR':['Alexandria','Roșiorii de Vede','Turnu Măgurele','Videle','Zimnicea'],
            'TM':['Timișoara','Lugoj','Deta','Fget','Jimbolia','Sânnicolau Mare'],
            'TL':['Tulcea','Babadag','Isaccea','Măcin','Sulina'],
            'VS':['Vaslui','Bârlad','Huși','Negrești'],
            'VL':['Râmnicu Vâlcea','Drăgășani','Băbeni','Băile Govora','Băile Olănești','Berești-Mădiei','Brezoi','Călimănești','Horezu'],
            'VN':['Focșani','Adjud','Mărășești','Panciu']
        };

        function buildCountySelect() {
            var opts = '<option value="">Selectează județul...</option>';
            Object.keys(RO_JUDETE).sort(function(a,b){ return RO_JUDETE[a].localeCompare(RO_JUDETE[b],'ro'); }).forEach(function(code) {
                opts += '<option value="' + code + '">' + RO_JUDETE[code] + '</option>';
            });
            return opts;
        }

        function buildCitySelect(countyCode) {
            var cities = RO_ORASE[countyCode] || [];
            var opts = '<option value="">Selectează orașul...</option>';
            cities.sort(function(a,b){return a.localeCompare(b,'ro');}).forEach(function(c) {
                opts += '<option value="' + c + '">' + c + '</option>';
            });
            opts += '<option value="__other__">Altul (introdu manual)</option>';
            return opts;
        }

        /* Location field rendering based on country */
        function renderLocationForCountry(countryCode) {
            var $stateWrap = $('#rimaStateWrap');
            var $cityWrap  = $('#rimaCityWrap');
            var $cityDrop  = $('#rimaCityDropdown');

            if (countryCode === 'RO') {
                // ROMANIA: dropdown județ + dropdown orașe complete
                $stateWrap.find('label').text('Județ:').addClass('required');
                var $stateField = $('<select name="state" id="rimaState" class="rima-addr-select"></select>');
                $stateField.html(buildCountySelect());
                $stateWrap.find('input, select').replaceWith($stateField);

                $cityWrap.find('label').text('Oraș:').addClass('required');
                var $citySelect = $('<select name="city" id="rimaCity" class="rima-addr-select" disabled><option value="">Selectează mai întâi județul...</option></select>');
                $cityWrap.find('input, select').replaceWith($citySelect);
                $cityDrop.hide();

                // County → City cascade
                $stateField.off('change.ro').on('change.ro', function() {
                    var code = $(this).val();
                    if (code && RO_ORASE[code]) {
                        $citySelect.html(buildCitySelect(code)).prop('disabled', false);
                    } else {
                        $citySelect.html('<option value="">Selectează mai întâi județul...</option>').prop('disabled', true);
                    }
                });

                $citySelect.off('change.ro').on('change.ro', function() {
                    if ($(this).val() === '__other__') {
                        var $manual = $('<input type="text" id="rimaCityManual" class="rima-addr-input" style="margin-top:6px;" placeholder="Introdu localitatea...">');
                        $cityWrap.append($manual);
                        $manual.focus();
                        $manual.on('input', function() {
                            // Override city value on save
                        });
                    } else {
                        $('#rimaCityManual').remove();
                    }
                });

            } else {
                // ALT TIP: text input județ/regiune + autocomplete Nominatim pentru oraș
                $stateWrap.find('label').text('Regiune / Stat:').removeClass('required');
                var $stateText = $('<input type="text" name="state" id="rimaState" class="rima-addr-input" placeholder="ex: Catalonia, Bavaria, Île-de-France...">');
                $stateWrap.find('input, select').replaceWith($stateText);

                $cityWrap.find('label').text('Oraș:').addClass('required');
                var $cityInput = $('<input type="text" name="city" id="rimaCity" class="rima-addr-input" placeholder="Caută orașul..." autocomplete="off">');
                $cityWrap.find('input, select').replaceWith($cityInput);
                $('#rimaCityManual, #rimaCityDropdown').remove();
                $cityWrap.append('<div id="rimaCityDropdown" class="rima-autocomplete-dropdown" style="display:none;"></div>');

                // Nominatim autocomplete
                var cityTimer;
                $cityInput.on('input', function() {
                    clearTimeout(cityTimer);
                    var q = $(this).val().trim();
                    var $drop = $('#rimaCityDropdown');
                    if (q.length < 2) { $drop.hide(); return; }
                    var country = $('#rimaCountry').val();
                    var countryParam = country ? '&countrycodes=' + country.toLowerCase() : '';

                    cityTimer = setTimeout(function() {
                        fetch('https://nominatim.openstreetmap.org/search?format=json&limit=8&addressdetails=1' + countryParam + '&q=' + encodeURIComponent(q) + '&featuretype=city', {
                            headers: {'User-Agent': 'RIMAAcademy/1.0'}
                        })
                        .then(function(r){ return r.json(); })
                        .then(function(data) {
                            if (!data.length) { $drop.hide(); return; }
                            var html = '';
                            var seen = {};
                            data.forEach(function(item) {
                                var city = item.address.city || item.address.town || item.address.village || item.address.municipality || item.name;
                                var state = item.address.state || item.address.county || '';
                                var key = city + state;
                                if (!city || seen[key]) return;
                                seen[key] = 1;
                                html += '<div class="rima-autocomplete-item" data-city="' + city + '" data-state="' + state + '" data-postcode="' + (item.address.postcode || '') + '">' +
                                    '<strong>' + city + '</strong>' + (state ? ' <span>' + state + '</span>' : '') +
                                '</div>';
                            });
                            $drop.html(html).show();
                        })
                        .catch(function(){ $drop.hide(); });
                    }, 350);
                });

                // Click on autocomplete suggestion
                $(document).on('click', '.rima-autocomplete-item', function() {
                    var city = $(this).data('city');
                    var state = $(this).data('state');
                    var postcode = $(this).data('postcode');
                    $('#rimaCity').val(city);
                    if (state) $('#rimaState').val(state);
                    if (postcode) $('#rimaPostcode').val(postcode);
                    $('#rimaCityDropdown').hide();
                });

                // Close dropdown on outside click
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('#rimaCityWrap').length) {
                        $('#rimaCityDropdown').hide();
                    }
                });
            }
        }

        // Trigger on country change
        $('#rimaCountry').on('change', function() {
            renderLocationForCountry($(this).val());
        });
        // Init with RO on modal open
        renderLocationForCountry('RO');

        /* ============================
           ANAF Lookup
           ============================ */
        $('#rimaAnafBtn').on('click', function() {
            var cui = $('#rimaAnafCui').val().trim();
            if (!cui) return;

            var $text = $(this).find('.rima-anaf-text');
            var $loading = $(this).find('.rima-anaf-loading');
            $text.hide(); $loading.show();
            $(this).prop('disabled', true);
            $('#rimaAnafStatus').html('<span class="rima-anaf-loading-msg">⏳ Se comunică cu ANAF...</span>');

            $.post(rimaAjaxData.ajaxUrl, {
                action: 'rima_anaf_lookup',
                cui: cui,
                nonce: $('[name="nonce"]').val()
            }, function(res) {
                if (res.success && res.data) {
                    var d = res.data;
                    // Map ANAF response fields to form fields
                    if (d.name)    $('#rimaCompany').val(d.name);
                    if (cui)       $('#rimaVatNumber').val(cui);
                    if (d.reg_com) $('#rimaRegNumber').val(d.reg_com);
                    if (d.address) $('#rimaAddr1').val(d.address);
                    if (d.phone)   $('#rimaPhone').val(d.phone);
                    
                    // Match county to dropdown
                    if (d.county) {
                        var $stateOpts = $('#rimaState option');
                        var matched = false;
                        $stateOpts.each(function() {
                            if ($(this).text().toLowerCase().indexOf(d.county.toLowerCase()) !== -1 ||
                                d.county.toLowerCase().indexOf($(this).text().toLowerCase()) !== -1) {
                                $('#rimaState').val($(this).val()).trigger('change');
                                matched = true;
                                return false;
                            }
                        });
                    }
                    
                    // Set city (after county loads via timeout)
                    if (d.city) {
                        setTimeout(function() {
                            // Try select first
                            var $citySelect = $('#rimaCity');
                            var citySet = false;
                            $citySelect.find('option').each(function() {
                                if ($(this).text().toLowerCase().indexOf(d.city.toLowerCase()) !== -1) {
                                    $citySelect.val($(this).val());
                                    citySet = true;
                                    return false;
                                }
                            });
                            if (!citySet) {
                                // Fall back to custom input
                                $('#rimaCityCustom').val(d.city).show();
                            }
                        }, 1200);
                    }
                    
                    $('#rimaAnafStatus').html('<span class="rima-anaf-ok">✅ Companie găsită și completată cu succes!</span>');
                } else {
                    $('#rimaAnafStatus').html('<span class="rima-anaf-err">❌ ' + (res.data || 'CUI-ul nu a fost găsit în ANAF.') + '</span>');
                }
            }).fail(function() {
                $('#rimaAnafStatus').html('<span class="rima-anaf-err">❌ Eroare de conexiune ANAF.</span>');
            }).always(function() {
                $text.show(); $loading.hide();
                $('#rimaAnafBtn').prop('disabled', false);
            });
        });

    }); // end document ready

})(jQuery);
</script>
