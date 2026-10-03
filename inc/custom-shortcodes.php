<?php
/* ============================================================
   RIMA DYNAMIC GLOBE MARKERS
   Generates JSON marker data based on active 'course-category' terms
   ============================================================ */
function rima_get_globe_markers_json() {
    $globe_dict = array(
        'english'    => array('aliases' => array('english', 'engleza'), 'lat' => 51.5072,  'lng' => -0.1276,  'flag' => 'https://flagcdn.com/w40/gb.png', 'isos' => array('GBR', 'USA', 'CAN', 'AUS'), 'label' => 'English'),
        'romanian'   => array('aliases' => array('romanian', 'romana'), 'lat' => 45.9432,  'lng' => 24.9668,  'flag' => 'https://flagcdn.com/w40/ro.png', 'isos' => array('ROU', 'MDA'), 'label' => 'Romanian'),
        'japanese'   => array('aliases' => array('japanese', 'japoneza'), 'lat' => 36.2048,  'lng' => 138.2529, 'flag' => 'https://flagcdn.com/w40/jp.png', 'isos' => array('JPN'), 'label' => 'Japanese'),
        'spanish'    => array('aliases' => array('spanish', 'spaniola'), 'lat' => 40.4637,  'lng' => -3.7492,  'flag' => 'https://flagcdn.com/w40/es.png', 'isos' => array('ESP', 'MEX', 'ARG', 'COL', 'PER', 'CHL'), 'label' => 'Spanish'),
        'french'     => array('aliases' => array('french', 'franceza'), 'lat' => 46.2276,  'lng' => 2.2137,   'flag' => 'https://flagcdn.com/w40/fr.png', 'isos' => array('FRA', 'CAN', 'BEL', 'CHE', 'CMR'), 'label' => 'French'),
        'german'     => array('aliases' => array('german', 'germana'), 'lat' => 51.1657,  'lng' => 10.4515,  'flag' => 'https://flagcdn.com/w40/de.png', 'isos' => array('DEU', 'AUT', 'CHE'), 'label' => 'German'),
        'italian'    => array('aliases' => array('italian', 'italiana'), 'lat' => 41.8719,  'lng' => 12.5674,  'flag' => 'https://flagcdn.com/w40/it.png', 'isos' => array('ITA', 'CHE'), 'label' => 'Italian'),
        'chinese'    => array('aliases' => array('chinese', 'chineza'), 'lat' => 35.8617,  'lng' => 104.1954, 'flag' => 'https://flagcdn.com/w40/cn.png', 'isos' => array('CHN', 'TWN', 'SGP'), 'label' => 'Chinese'),
        'arabic'     => array('aliases' => array('arabic', 'araba'), 'lat' => 23.8859,  'lng' => 45.0792,  'flag' => 'https://flagcdn.com/w40/sa.png', 'isos' => array('SAU', 'EGY', 'ARE', 'MAR', 'DZA'), 'label' => 'Arabic'),
        'portuguese' => array('aliases' => array('portuguese', 'portugheza'), 'lat' => -14.235,  'lng' => -51.9253, 'flag' => 'https://flagcdn.com/w40/pt.png', 'isos' => array('PRT', 'BRA', 'AGO'), 'label' => 'Portuguese'),
        'russian'    => array('aliases' => array('russian', 'rusa'), 'lat' => 61.524,   'lng' => 105.3188, 'flag' => 'https://flagcdn.com/w40/ru.png', 'isos' => array('RUS', 'BLR', 'KAZ'), 'label' => 'Russian'),
        'korean'     => array('aliases' => array('korean', 'coreeana'), 'lat' => 35.9078,  'lng' => 127.7669, 'flag' => 'https://flagcdn.com/w40/kr.png', 'isos' => array('KOR'), 'label' => 'Korean'),
        'dutch'      => array('aliases' => array('dutch', 'olandeza'), 'lat' => 52.1326,  'lng' => 5.2913,   'flag' => 'https://flagcdn.com/w40/nl.png', 'isos' => array('NLD', 'BEL', 'SUR'), 'label' => 'Dutch'),
        'turkish'    => array('aliases' => array('turkish', 'turca'), 'lat' => 38.9637,  'lng' => 35.2433,  'flag' => 'https://flagcdn.com/w40/tr.png', 'isos' => array('TUR'), 'label' => 'Turkish'),
        'hindi'      => array('aliases' => array('hindi'), 'lat' => 20.5937,  'lng' => 78.9629,  'flag' => 'https://flagcdn.com/w40/in.png', 'isos' => array('IND'), 'label' => 'Hindi')
    );

    $found_langs = array();
    
    if ( taxonomy_exists('course-category') ) {
        $course_categories = get_terms(array(
            'taxonomy' => 'course-category',
            'hide_empty' => false
        ));

        if ( ! is_wp_error( $course_categories ) && ! empty( $course_categories ) ) {
            foreach( $course_categories as $term ) {
                $slug = strtolower($term->slug);
                $name = strtolower($term->name);
                
                foreach( $globe_dict as $key => $data ) {
                    $matched = false;
                    foreach ( $data['aliases'] as $alias ) {
                        if( strpos($slug, $alias) !== false || strpos($name, $alias) !== false ) {
                            $matched = true;
                            break;
                        }
                    }
                    if ( $matched ) {
                        if ( ! isset($found_langs[$key]) ) {
                            $found_langs[$key] = array(
                                'lat'   => $data['lat'],
                                'lng'   => $data['lng'],
                                'flag'  => $data['flag'],
                                'label' => $data['label'],
                                'isos'  => $data['isos']
                            );
                        }
                    }
                }
            }
        }
    }

    if ( empty($found_langs) ) {
        foreach( array('english', 'romanian', 'japanese', 'spanish', 'french', 'german') as $def_key ) {
            $data = $globe_dict[$def_key];
            $found_langs[$def_key] = array(
                'lat'   => $data['lat'],
                'lng'   => $data['lng'],
                'flag'  => $data['flag'],
                'label' => $data['label'],
                'isos'  => $data['isos']
            );
        }
    }

    return json_encode(array_values($found_langs));
}


// Remove Addresses from Woo Menu
add_filter( 'woocommerce_account_menu_items', 'rima_remove_addresses_tab', 999 );
function rima_remove_addresses_tab( $items ) {
    if ( isset( $items['edit-address'] ) ) {
        unset( $items['edit-address'] );
    }
    return $items;
}

// -----------------------------------------------------------------------------
// SAVE RIMA CUSTOM REGISTRATION FIELDS (PF / PJ)
// -----------------------------------------------------------------------------
add_action( 'woocommerce_created_customer', 'rima_save_custom_registration_fields', 10, 3 );
function rima_save_custom_registration_fields( $customer_id, $new_customer_data, $password_generated ) {
    
    // Nume / Prenume (comun pentru PF și PJ ca persoană de contact)
    if ( isset( $_POST['billing_first_name'] ) ) {
        update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
        update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
    }
    if ( isset( $_POST['billing_last_name'] ) ) {
        update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
        update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
    }

    // Tip Cont: 'pf' (Persoană Fizică) sau 'pj' (Persoană Juridică)
    $account_type = isset( $_POST['rima_account_type'] ) ? sanitize_text_field( $_POST['rima_account_type'] ) : 'pf';
    update_user_meta( $customer_id, 'rima_account_type', $account_type );

    // Persoană Juridică Fields
    if ( $account_type === 'pj' ) {
        if ( isset( $_POST['billing_company'] ) ) {
            update_user_meta( $customer_id, 'billing_company', sanitize_text_field( $_POST['billing_company'] ) );
        }
        if ( isset( $_POST['billing_cui'] ) ) {
            update_user_meta( $customer_id, 'billing_cui', sanitize_text_field( $_POST['billing_cui'] ) );
        }
        if ( isset( $_POST['billing_reg_com'] ) ) {
            update_user_meta( $customer_id, 'billing_reg_com', sanitize_text_field( $_POST['billing_reg_com'] ) );
        }
        if ( isset( $_POST['billing_iban'] ) ) {
            update_user_meta( $customer_id, 'billing_iban', sanitize_text_field( $_POST['billing_iban'] ) );
        }
        if ( isset( $_POST['billing_bank'] ) ) {
            update_user_meta( $customer_id, 'billing_bank', sanitize_text_field( $_POST['billing_bank'] ) );
        }
    } else {
        // Dacă este PF, ștergem eventualele date rămase
        delete_user_meta( $customer_id, 'billing_company' );
        delete_user_meta( $customer_id, 'billing_cui' );
        delete_user_meta( $customer_id, 'billing_reg_com' );
        delete_user_meta( $customer_id, 'billing_iban' );
        delete_user_meta( $customer_id, 'billing_bank' );
    }

    // Trigger Custom RIMA Email for New Account
    $user = get_userdata( $customer_id );
    $first = get_user_meta( $customer_id, 'billing_first_name', true );
    $last  = get_user_meta( $customer_id, 'billing_last_name', true );
    $name = trim($first . ' ' . $last);
    if(empty($name)) $name = $user->display_name;

    $pass = 'Setată de tine la înregistrare.';
    if ( isset($_POST['account_password']) ) {
        $pass = $_POST['account_password']; // If plain text is available in request
    } elseif ( $password_generated ) {
        $pass = 'A fost generată automat și a fost trimisă în email-ul de sistem (sau folosește opțiunea "Am uitat parola").';
    }

    do_action('rima_student_account_created', $user->user_email, $name, $user->user_login, $pass);
}

// Disable default WooCommerce New Account email (since we send our own 2026 design)
add_filter( 'woocommerce_email_classes', 'rima_disable_wc_new_account_email' );
function rima_disable_wc_new_account_email( $email_classes ) {
    // Dacă vrei să dezactivezi complet mailul WC, decomentează linia de mai jos:
    // unset( $email_classes['WC_Email_Customer_New_Account'] );
    // Dar îl lăsăm momentan activ în cazul în care WC trebuie să trimită parola generată.
    return $email_classes;
}

// -----------------------------------------------------------------------------
// WOOCOMMERCE BILLING / CHECKOUT FIELDS (PF / PJ, IBAN, BANCA)
// -----------------------------------------------------------------------------
add_filter( 'woocommerce_billing_fields', 'rima_custom_billing_fields' );
function rima_custom_billing_fields( $fields ) {
    $fields['rima_account_type'] = array(
        'type'        => 'radio',
        'label'       => __('Tip Cont', 'woocommerce'),
        'required'    => true,
        'class'       => array('form-row-wide', 'rima-checkout-account-type'),
        'options'     => array(
            'pf' => 'Persoană Fizică',
            'pj' => 'Persoană Juridică (Firmă)'
        ),
        'priority'    => 25,
        'default'     => 'pf'
    );

    $fields['billing_cui'] = array(
        'type'        => 'text',
        'label'       => __('CUI / CIF', 'woocommerce'),
        'required'    => false, // JS will make it required if PJ
        'class'       => array('form-row-first', 'rima-pj-field'),
        'priority'    => 35,
    );

    $fields['billing_reg_com'] = array(
        'type'        => 'text',
        'label'       => __('Reg. Comerțului', 'woocommerce'),
        'placeholder' => 'J.../...',
        'required'    => false,
        'class'       => array('form-row-last', 'rima-pj-field'),
        'priority'    => 36,
    );

    $fields['billing_iban'] = array(
        'type'        => 'text',
        'label'       => __('IBAN', 'woocommerce'),
        'placeholder' => 'RO...',
        'required'    => false,
        'class'       => array('form-row-first', 'rima-pj-field', 'rima-checkout-iban'),
        'priority'    => 37,
    );

    $fields['billing_bank'] = array(
        'type'        => 'text',
        'label'       => __('Banca', 'woocommerce'),
        'required'    => false,
        'class'       => array('form-row-last', 'rima-pj-field', 'rima-checkout-bank'),
        'custom_attributes' => array('readonly' => 'readonly'),
        'priority'    => 38,
    );

    if(isset($fields['billing_company'])) {
        $fields['billing_company']['class'] = array('form-row-wide', 'rima-pj-field');
        $fields['billing_company']['priority'] = 30;
    }

    return $fields;
}

// Validare server-side pentru Checkout
add_action( 'woocommerce_checkout_process', 'rima_checkout_fields_validation' );
function rima_checkout_fields_validation() {
    if ( isset($_POST['rima_account_type']) && $_POST['rima_account_type'] === 'pj' ) {
        if ( empty( $_POST['billing_company'] ) ) {
            wc_add_notice( __( 'Vă rugăm introduceți Numele Companiei.', 'woocommerce' ), 'error' );
        }
        if ( empty( $_POST['billing_cui'] ) ) {
            wc_add_notice( __( 'Vă rugăm introduceți CUI / CIF.', 'woocommerce' ), 'error' );
        }
    }
}

// Injectează scriptul de toggling și detecție IBAN în footer-ul paginii Checkout / My Account
add_action( 'wp_footer', 'rima_checkout_fields_js' );
function rima_checkout_fields_js() {
    if ( is_checkout() || is_account_page() ) {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var accountTypeRadios = document.querySelectorAll('input[name="rima_account_type"]');
            var pjFields = document.querySelectorAll('.rima-pj-field');
            var companyInput = document.querySelector('input[name="billing_company"]');
            var cuiInput = document.querySelector('input[name="billing_cui"]');
            
            function togglePjFields() {
                var checkedRadio = document.querySelector('input[name="rima_account_type"]:checked');
                if(!checkedRadio) return;
                var isPj = checkedRadio.value === 'pj';
                
                pjFields.forEach(function(el) {
                    if(isPj) {
                        el.style.display = 'block';
                        el.classList.remove('rima-hidden');
                    } else {
                        el.style.display = 'none';
                        el.classList.add('rima-hidden');
                    }
                });

                if(companyInput && cuiInput) {
                    if(isPj) {
                        companyInput.closest('.form-row').classList.add('validate-required');
                        cuiInput.closest('.form-row').classList.add('validate-required');
                    } else {
                        companyInput.closest('.form-row').classList.remove('validate-required');
                        cuiInput.closest('.form-row').classList.remove('validate-required');
                    }
                }
            }

            if(accountTypeField) {
                // Initialize pe jQuery change (pt Select2 de la Woo) și native change
                if (typeof jQuery !== 'undefined') {
                    jQuery('select[name="rima_account_type"]').on('change', togglePjFields);
                }
                accountTypeField.addEventListener('change', togglePjFields);
                togglePjFields();
            }

            var ibanField = document.querySelector('input[name="billing_iban"]');
            var bankField = document.querySelector('input[name="billing_bank"]');
            
            function detectBankCheckout(iban) {
                if(!bankField) return;
                let cleanIban = iban.replace(/\s+/g, '').toUpperCase();
                if (cleanIban.length >= 8 && cleanIban.startsWith('RO')) {
                    const bankCode = cleanIban.substring(4, 8);
                    const banks = {
                        'BTRL': 'Banca Transilvania',
                        'INGB': 'ING Bank',
                        'RZBR': 'Raiffeisen Bank',
                        'BREL': 'Libra Internet Bank',
                        'BRDE': 'BRD Groupe Societe Generale',
                        'RNCB': 'Banca Comerciala Romana (BCR)',
                        'UGBI': 'Garanti Bank',
                        'TREZ': 'Trezoreria Statului',
                        'OTPV': 'OTP Bank Romania',
                        'BACX': 'UniCredit Bank',
                        'CECE': 'CEC Bank',
                        'BACA': 'Alpha Bank',
                        'CRDZ': 'Credito Emiliano',
                        'BROM': 'Banca Romaneasca',
                        'MIRO': 'ProCredit Bank',
                        'BNPA': 'BNP Paribas',
                        'CITI': 'Citibank Europe',
                        'EXIM': 'EximBank',
                        'CARP': 'Banca Comerciala Intesa Sanpaolo',
                        'PORL': 'Porsche Bank',
                        'FNNB': 'First Bank',
                        'VIST': 'Vista Bank'
                    };
                    bankField.value = banks[bankCode] ? banks[bankCode] : 'Bancă Necunoscută (' + bankCode + ')';
                } else {
                    bankField.value = '';
                }
            }

            if(ibanField) {
                ibanField.addEventListener('input', function() { detectBankCheckout(this.value); });
                detectBankCheckout(ibanField.value); // Run on load
            }
        });
        </script>
        <style>
            #billing_bank { background: rgba(0,0,0,0.05); cursor: not-allowed; opacity: 0.8; }
            .rima-hidden { display: none !important; }
            
            /* Modern Payment Methods Styling */
            .woocommerce-checkout #payment ul.payment_methods {
                padding: 0 !important;
                border: none !important;
                background: transparent !important;
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
            .woocommerce-checkout #payment ul.payment_methods li.wc_payment_method {
                background: rgba(255, 255, 255, 0.05) !important;
                border: 1px solid rgba(255, 255, 255, 0.1) !important;
                border-radius: 8px;
                padding: 15px !important;
                transition: all 0.3s ease;
                display: flex;
                flex-direction: column;
            }
            .woocommerce-checkout #payment ul.payment_methods li.wc_payment_method > label {
                font-weight: 600 !important;
                color: var(--rima-lp-text, #fff) !important;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 10px;
                margin: 0 !important;
            }
            .woocommerce-checkout #payment ul.payment_methods li.wc_payment_method input[type="radio"] {
                appearance: none;
                width: 18px;
                height: 18px;
                border: 2px solid rgba(255,255,255,0.3);
                border-radius: 50%;
                margin: 0;
                position: relative;
                outline: none;
                transition: 0.2s;
            }
            .woocommerce-checkout #payment ul.payment_methods li.wc_payment_method input[type="radio"]:checked {
                border-color: #991b1b;
            }
            .woocommerce-checkout #payment ul.payment_methods li.wc_payment_method input[type="radio"]:checked::after {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 8px;
                height: 8px;
                background: #991b1b;
                border-radius: 50%;
            }
            .woocommerce-checkout #payment ul.payment_methods li.wc_payment_method:focus-within,
            .woocommerce-checkout #payment ul.payment_methods li.wc_payment_method:hover {
                border-color: rgba(255,255,255,0.3) !important;
                background: rgba(255, 255, 255, 0.08) !important;
            }
            .woocommerce-checkout #payment div.payment_box {
                background: transparent !important;
                padding: 10px 0 0 28px !important;
                margin: 0 !important;
                color: rgba(255,255,255,0.7) !important;
                font-size: 13px;
                border: none !important;
            }
            .woocommerce-checkout #payment div.payment_box::before { display: none !important; }
            .woocommerce-checkout #payment {
                background: transparent !important;
                border: none !important;
                padding: 0 !important;
            }
            .woocommerce-checkout #payment .place-order {
                background: transparent !important;
                padding: 20px 0 0 0 !important;
            }
        </style>
        <?php
    }
}

// -----------------------------------------------------------------------------
// SAVE CHECKOUT CUSTOM FIELDS TO ORDER META
// -----------------------------------------------------------------------------
add_action( 'woocommerce_checkout_update_order_meta', 'rima_custom_checkout_field_update_order_meta' );
function rima_custom_checkout_field_update_order_meta( $order_id ) {
    if ( ! empty( $_POST['rima_account_type'] ) ) {
        update_post_meta( $order_id, 'rima_account_type', sanitize_text_field( $_POST['rima_account_type'] ) );
    }
    if ( ! empty( $_POST['billing_cui'] ) ) {
        update_post_meta( $order_id, '_billing_cui', sanitize_text_field( $_POST['billing_cui'] ) );
    }
    if ( ! empty( $_POST['billing_reg_com'] ) ) {
        update_post_meta( $order_id, '_billing_reg_com', sanitize_text_field( $_POST['billing_reg_com'] ) );
    }
    if ( ! empty( $_POST['billing_iban'] ) ) {
        update_post_meta( $order_id, '_billing_iban', sanitize_text_field( $_POST['billing_iban'] ) );
    }
    if ( ! empty( $_POST['billing_bank'] ) ) {
        update_post_meta( $order_id, '_billing_bank', sanitize_text_field( $_POST['billing_bank'] ) );
    }
}

// -----------------------------------------------------------------------------
// PERMITE ACHIZIȚIA MULTIPLĂ (PENTRU FIRME / GRUPURI)
// -----------------------------------------------------------------------------
add_filter( 'woocommerce_is_sold_individually', 'rima_allow_multiple_courses', 10, 2 );
function rima_allow_multiple_courses( $sold_individually, $product ) {
    // Permitem cantitate > 1 pentru a putea fi achiziționate de firme (mai multe licențe)
    return false;
}

// -----------------------------------------------------------------------------
// FORCE DISABLE WOOCOMMERCE COMING SOON
// -----------------------------------------------------------------------------
add_action('init', function() {
    if (get_option('woocommerce_coming_soon') === 'yes') {
        update_option('woocommerce_coming_soon', 'no');
    }
});

// -----------------------------------------------------------------------------
// FLOATING CART WIDGET
// -----------------------------------------------------------------------------
add_action('wp_footer', function() {
    if ( function_exists('WC') && !is_cart() && !is_checkout() ) {
        $count = WC()->cart->get_cart_contents_count();
        $url = wc_get_checkout_url();
        echo '<a href="' . esc_url($url) . '" class="rima-floating-cart" style="'.($count == 0 ? 'display:none;' : '').'">';
        echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>';
        echo '<span class="count">' . esc_html($count) . '</span>';
        echo '</a>';
    }
});

add_filter('woocommerce_add_to_cart_fragments', function($fragments) {
    if ( !function_exists('WC') ) return $fragments;
    $count = WC()->cart->get_cart_contents_count();
    $url = wc_get_checkout_url();
    ob_start();
    ?>
    <a href="<?php echo esc_url($url); ?>" class="rima-floating-cart" style="<?php echo $count == 0 ? 'display:none;' : ''; ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
        <span class="count"><?php echo esc_html($count); ?></span>
    </a>
    <?php
    $fragments['a.rima-floating-cart'] = ob_get_clean();
    return $fragments;
});

add_action('wp_head', function() {
    echo '<style>
    .rima-floating-cart {
        position: fixed; bottom: 30px; right: 30px; z-index: 999999;
        width: 65px; height: 65px; border-radius: 50%;
        background: linear-gradient(135deg, #E62243, #D61B38);
        box-shadow: 0 10px 30px rgba(230,34,67,0.4);
        display: flex; align-items: center; justify-content: center;
        color: white !important; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none;
    }
    .rima-floating-cart:hover, .rima-floating-cart:focus { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(230,34,67,0.5); color: white;}
    .rima-floating-cart svg { width: 28px; height: 28px; }
    .rima-floating-cart .count {
        position: absolute; top: -5px; right: -5px; background: #0f172a; color: white;
        font-size: 13px; font-weight: 800; width: 24px; height: 24px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; border: 2px solid white;
    }
    @media (max-width: 768px) {
        .rima-floating-cart { bottom: 20px; right: 20px; width: 55px; height: 55px; }
        .rima-floating-cart svg { width: 24px; height: 24px; }
    }
    </style>';
});






// ── RIMA AUTO-PURGE CACHE V16 ──
add_action( 'init', 'rima_auto_purge_cache_v16' );
function rima_auto_purge_cache_v16() {
    if ( ! get_option( 'rima_cache_purged_v16' ) ) {
        // LiteSpeed Cache
        if ( has_action( 'litespeed_purge_all' ) ) {
            do_action( 'litespeed_purge_all' );
        }
        // Fallback for LSCWP API
        if ( class_exists('LiteSpeed\Purge') ) {
            \LiteSpeed\Purge::purge_all();
        }
        // Mark as purged
        update_option( 'rima_cache_purged_v16', true );
    }
}



add_action('wp_loaded', function() {
    if (isset($_GET['rima_nuke_cache'])) {
        if (class_exists('LiteSpeed\Purge')) {
            \LiteSpeed\Purge::purge_all();
            echo 'LSCWP CACHE NUKED SUCCESS';
        } else {
            echo 'LSCWP CLASS NOT FOUND';
        }
        exit;
    }
});

// RIMA UI Customizer
require_once get_stylesheet_directory() . '/inc/rima-customizer.php';

