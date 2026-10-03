<?php
/**
 * RIMA Academy - My Addresses Page
 * Grid cu carduri PF/PJ + modal adăugare/editare
 * Design identic cu Ana Cleaning, adaptat la RIMA navy/blue
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$customer_id = get_current_user_id();

// Load addresses via ANA_Addresses_Plugin
$billing_addresses  = class_exists('ANA_Addresses_Plugin') ? ANA_Addresses_Plugin::get_addresses($customer_id, 'billing')  : [];
$shipping_addresses = class_exists('ANA_Addresses_Plugin') ? ANA_Addresses_Plugin::get_addresses($customer_id, 'shipping') : [];

// Default addresses
$default_billing_id  = null;
$default_shipping_id = null;
foreach ($billing_addresses as $a) {
    if (!empty($a['is_default'])) { $default_billing_id = $a['id']; break; }
}
foreach ($shipping_addresses as $a) {
    if (!empty($a['is_default'])) { $default_shipping_id = $a['id']; break; }
}

// Helper: format address for display
function rima_format_address_card($addr, $is_pj) {
    $out = '';
    if ($is_pj) {
        $out .= '<strong>' . esc_html($addr['company'] ?? '') . '</strong>';
        if (!empty($addr['vat_number'])) $out .= '<div class="rima-addr-line">CUI: ' . esc_html($addr['vat_number']) . '</div>';
        if (!empty($addr['reg_com']))    $out .= '<div class="rima-addr-line">Reg: ' . esc_html($addr['reg_com']) . '</div>';
    } else {
        $out .= '<strong>' . esc_html(trim(($addr['first_name'] ?? '') . ' ' . ($addr['last_name'] ?? ''))) . '</strong>';
    }
    if (!empty($addr['address_1'])) $out .= '<div class="rima-addr-line">' . esc_html($addr['address_1']) . '</div>';
    if (!empty($addr['address_2'])) $out .= '<div class="rima-addr-line">' . esc_html($addr['address_2']) . '</div>';
    $city_line = implode(', ', array_filter([$addr['city'] ?? '', $addr['state'] ?? '', $addr['postcode'] ?? '']));
    if ($city_line) $out .= '<div class="rima-addr-line">' . esc_html($city_line) . '</div>';
    if (!empty($addr['phone'])) $out .= '<div class="rima-addr-line">📞 ' . esc_html($addr['phone']) . '</div>';
    return $out;
}
?>

<div class="rima-myaddresses-page">

    <!-- ============= BILLING SECTION ============= -->
    <div class="rima-addr-section">
        <div class="rima-addr-section-header">
            <h3 class="rima-addr-section-title">
                <span>📋</span> Adrese de Facturare
            </h3>
            <button type="button"
                    class="rima-btn-add-addr"
                    data-rima-modal-open
                    data-address-type="billing"
                    data-entity-type="pf">
                <span>+</span> Adaugă Adresă
            </button>
        </div>

        <div class="rima-addr-note">
            <span>ℹ️</span>
            <span>Adresa de facturare predefinită va fi folosită pentru emiterea facturilor.</span>
        </div>

        <div class="rima-addr-grid">
            <?php if (!empty($billing_addresses)) : ?>
                <?php foreach ($billing_addresses as $addr) :
                    $is_pj = isset($addr['entity_type']) && $addr['entity_type'] === 'pj';
                    $is_default = ($addr['id'] == $default_billing_id);
                    $addr_json = esc_attr(wp_json_encode($addr));
                ?>
                    <div class="rima-addr-card <?php echo $is_default ? 'is-default' : ''; ?>"
                         data-rima-address-id="<?php echo esc_attr($addr['id'] ?? ''); ?>"
                         data-address-json="<?php echo $addr_json; ?>">

                        <div class="rima-addr-card-badges">
                            <span class="rima-badge <?php echo $is_pj ? 'badge-pj' : 'badge-pf'; ?>">
                                <?php echo $is_pj ? 'PJ' : 'PF'; ?>
                            </span>
                            <?php if ($is_default) : ?>
                                <span class="rima-badge badge-default">PREDEFINITĂ</span>
                            <?php endif; ?>
                        </div>

                        <div class="rima-addr-card-body">
                            <?php echo rima_format_address_card($addr, $is_pj); ?>
                        </div>

                        <div class="rima-addr-card-actions">
                            <button type="button"
                                    class="rima-addr-edit-btn"
                                    data-address-id="<?php echo esc_attr($addr['id'] ?? ''); ?>"
                                    data-address-type="billing"
                                    data-entity-type="<?php echo $is_pj ? 'pj' : 'pf'; ?>">
                                ✏️ Editează
                            </button>
                            <?php if (!$is_default) : ?>
                                <button type="button"
                                        class="rima-addr-default-btn"
                                        data-address-id="<?php echo esc_attr($addr['id'] ?? ''); ?>"
                                        data-address-type="billing">
                                    ⭐ Predefinită
                                </button>
                            <?php endif; ?>
                            <button type="button"
                                    class="rima-addr-delete-btn"
                                    data-address-id="<?php echo esc_attr($addr['id'] ?? ''); ?>"
                                    data-address-type="billing">
                                🗑️
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Add new card -->
                <div class="rima-addr-add-card"
                     data-rima-modal-open
                     data-address-type="billing"
                     data-entity-type="pf">
                    <div class="rima-add-icon">+</div>
                    <div class="rima-add-text">Adaugă adresă nouă</div>
                </div>

            <?php else : ?>
                <div class="rima-addr-empty">
                    <div class="rima-addr-empty-icon">📭</div>
                    <p>Nu ai încă nicio adresă de facturare.</p>
                    <button type="button"
                            class="rima-btn-add-addr"
                            data-rima-modal-open
                            data-address-type="billing"
                            data-entity-type="pf">
                        Adaugă prima adresă
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ============= SHIPPING SECTION ============= -->
    <div class="rima-addr-section">
        <div class="rima-addr-section-header">
            <h3 class="rima-addr-section-title">
                <span>🚚</span> Adrese de Livrare
            </h3>
            <button type="button"
                    class="rima-btn-add-addr rima-btn-secondary"
                    data-rima-modal-open
                    data-address-type="shipping"
                    data-entity-type="pf">
                <span>+</span> Adaugă Adresă
            </button>
        </div>

        <div class="rima-addr-grid">
            <?php if (!empty($shipping_addresses)) : ?>
                <?php foreach ($shipping_addresses as $addr) :
                    $is_pj = isset($addr['entity_type']) && $addr['entity_type'] === 'pj';
                    $is_default = ($addr['id'] == $default_shipping_id);
                    $addr_json = esc_attr(wp_json_encode($addr));
                ?>
                    <div class="rima-addr-card <?php echo $is_default ? 'is-default' : ''; ?>"
                         data-rima-address-id="<?php echo esc_attr($addr['id'] ?? ''); ?>"
                         data-address-json="<?php echo $addr_json; ?>">

                        <div class="rima-addr-card-badges">
                            <span class="rima-badge <?php echo $is_pj ? 'badge-pj' : 'badge-pf'; ?>">
                                <?php echo $is_pj ? 'PJ' : 'PF'; ?>
                            </span>
                            <?php if ($is_default) : ?>
                                <span class="rima-badge badge-default">PREDEFINITĂ</span>
                            <?php endif; ?>
                        </div>

                        <div class="rima-addr-card-body">
                            <?php echo rima_format_address_card($addr, $is_pj); ?>
                        </div>

                        <div class="rima-addr-card-actions">
                            <button type="button"
                                    class="rima-addr-edit-btn"
                                    data-address-id="<?php echo esc_attr($addr['id'] ?? ''); ?>"
                                    data-address-type="shipping"
                                    data-entity-type="<?php echo $is_pj ? 'pj' : 'pf'; ?>">
                                ✏️ Editează
                            </button>
                            <?php if (!$is_default) : ?>
                                <button type="button"
                                        class="rima-addr-default-btn"
                                        data-address-id="<?php echo esc_attr($addr['id'] ?? ''); ?>"
                                        data-address-type="shipping">
                                    ⭐ Predefinită
                                </button>
                            <?php endif; ?>
                            <button type="button"
                                    class="rima-addr-delete-btn"
                                    data-address-id="<?php echo esc_attr($addr['id'] ?? ''); ?>"
                                    data-address-type="shipping">
                                🗑️
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Add new card -->
                <div class="rima-addr-add-card"
                     data-rima-modal-open
                     data-address-type="shipping"
                     data-entity-type="pf">
                    <div class="rima-add-icon">+</div>
                    <div class="rima-add-text">Adaugă adresă nouă</div>
                </div>

            <?php else : ?>
                <div class="rima-addr-empty">
                    <div class="rima-addr-empty-icon">📦</div>
                    <p>Nu ai încă nicio adresă de livrare.</p>
                    <button type="button"
                            class="rima-btn-add-addr rima-btn-secondary"
                            data-rima-modal-open
                            data-address-type="shipping"
                            data-entity-type="pf">
                        Adaugă prima adresă
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- Include address modal -->
<?php include get_stylesheet_directory() . '/woocommerce/myaccount/parts/rima-address-modal.php'; ?>

<!-- Pass AJAX URL to JS -->
<script>
var rimaAjaxData = { ajaxUrl: '<?php echo esc_url(admin_url("admin-ajax.php")); ?>' };
</script>

<!-- Toast Notification CSS -->
<style>
/* =========================================================
   RIMA MY ADDRESSES PAGE - Complete Design
   ========================================================= */

.rima-myaddresses-page {
    padding: 0;
}

/* Section */
.rima-addr-section {
    margin-bottom: 36px;
}

.rima-addr-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 14px;
}

.rima-addr-section-title {
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Info Note */
.rima-addr-note {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 12px 16px;
    background: linear-gradient(135deg, rgba(29,78,216,0.06), rgba(16,45,86,0.04));
    border: 1px solid rgba(29,78,216,0.15);
    border-radius: 10px;
    margin-bottom: 16px;
    font-size: 13px;
    color: #475569;
    line-height: 1.5;
}

/* Grid */
.rima-addr-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
}

/* Address Card */
.rima-addr-card {
    background: #fff;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    transition: all 0.2s ease;
    cursor: default;
}
.rima-addr-card.is-default {
    border-color: #1d4ed8;
    box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
}
.rima-addr-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

/* Badges */
.rima-addr-card-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 10px;
}
.rima-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.badge-pf { background: rgba(29,78,216,0.1); color: #1d4ed8; }
.badge-pj { background: rgba(245,158,11,0.12); color: #b45309; }
.badge-default { background: linear-gradient(135deg, #102d56, #1d4ed8); color: #fff; }

/* Card Body */
.rima-addr-card-body {
    font-size: 13px;
    line-height: 1.65;
    color: #475569;
    margin-bottom: 14px;
}
.rima-addr-card-body strong {
    display: block;
    font-size: 15px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 4px;
}
.rima-addr-line { margin: 0; }

/* Card Actions */
.rima-addr-card-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    padding-top: 12px;
    border-top: 1px solid #f1f5f9;
}
.rima-addr-card-actions button {
    padding: 7px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    border: 1.5px solid transparent;
    background: transparent;
}
.rima-addr-edit-btn {
    background: rgba(29,78,216,0.06) !important;
    border-color: rgba(29,78,216,0.2) !important;
    color: #1d4ed8 !important;
}
.rima-addr-edit-btn:hover { background: rgba(29,78,216,0.12) !important; }
.rima-addr-default-btn {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: #64748b !important;
}
.rima-addr-delete-btn {
    background: rgba(239,68,68,0.06) !important;
    border-color: rgba(239,68,68,0.2) !important;
    color: #ef4444 !important;
}

/* Add new card */
.rima-addr-add-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 120px;
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s;
    background: #fafbfc;
    gap: 8px;
}
.rima-addr-add-card:hover {
    border-color: #1d4ed8;
    background: rgba(29,78,216,0.03);
}
.rima-add-icon {
    font-size: 28px;
    color: #1d4ed8;
    font-weight: 300;
    line-height: 1;
}
.rima-add-text { font-size: 13px; color: #64748b; }

/* Empty State */
.rima-addr-empty {
    text-align: center;
    padding: 36px 20px;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}
.rima-addr-empty-icon { font-size: 40px; margin-bottom: 12px; }
.rima-addr-empty p { font-size: 14px; color: #64748b; margin: 0 0 16px; }

/* Add buttons */
.rima-btn-add-addr {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 20px;
    background: linear-gradient(135deg, #102d56, #1d4ed8);
    color: #fff !important;
    border: none;
    border-radius: 8px;
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    transition: opacity 0.2s;
    text-decoration: none !important;
}
.rima-btn-add-addr:hover { opacity: 0.9; }
.rima-btn-add-addr.rima-btn-secondary {
    background: linear-gradient(135deg, #0f766e, #0d9488);
}

/* Toast notification */
.rima-addr-notice {
    position: fixed;
    bottom: 24px;
    right: 24px;
    padding: 14px 22px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    z-index: 999999;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.3s ease;
    max-width: 320px;
}
.rima-addr-notice.is-visible { opacity: 1; transform: translateY(0); }
.rima-addr-notice--success { background: #059669; color: #fff; }
.rima-addr-notice--error { background: #dc2626; color: #fff; }

/* Responsive */
@media (max-width: 768px) {
    .rima-addr-grid { grid-template-columns: 1fr; gap: 12px; }
    .rima-addr-section-header { flex-direction: column; align-items: stretch; }
    .rima-btn-add-addr { justify-content: center; }
}
</style>
