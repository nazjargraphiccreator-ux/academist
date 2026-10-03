<?php
/**
 * WooCommerce Integration
 * 
 * @package ANA_Addresses
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Disable guest checkout - Force login
 */
add_action( 'template_redirect', 'ana_force_checkout_login' );
function ana_force_checkout_login() {
    if ( is_checkout() && ! is_user_logged_in() && ! is_wc_endpoint_url( 'order-received' ) ) {
        wc_add_notice( __( 'Trebuie să fi autentificat pentru a finaliza comanda.', 'ana-addresses' ), 'error' );
        wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
        exit;
    }
}

// Disable guest checkout options
add_filter( 'woocommerce_checkout_registration_enabled', '__return_false' );
add_filter( 'pre_option_woocommerce_enable_guest_checkout', '__return_false' );
add_filter( 'woocommerce_enable_guest_checkout', '__return_false' );

/**
 * Use selected address on checkout submit
 */
add_action( 'woocommerce_checkout_create_order', 'ana_use_selected_address_on_order', 10, 2 );
function ana_use_selected_address_on_order( $order, $data ) {
    if ( ! is_user_logged_in() ) {
        return;
    }
    
    $user_id = get_current_user_id();
    
    // Use selected billing address or default
    $billing_id = isset( $_POST['selected_billing_address_id'] ) ? sanitize_text_field( $_POST['selected_billing_address_id'] ) : '';
    if ( ! empty( $billing_id ) ) {
        $address = ANA_Addresses_Plugin::get_address( $user_id, $billing_id, 'billing' );
        if ( $address ) {
            $order->set_billing_first_name( $address['first_name'] );
            $order->set_billing_last_name( $address['last_name'] );
            $order->set_billing_company( $address['company'] );
            $order->set_billing_address_1( $address['address_1'] );
            $order->set_billing_address_2( $address['address_2'] );
            $order->set_billing_city( $address['city'] );
            $order->set_billing_state( $address['state'] );
            $order->set_billing_postcode( $address['postcode'] );
            $order->set_billing_phone( $address['phone'] );
            $order->set_billing_email( $address['email'] );
            
            // Save address ID and type to order meta
            $order->update_meta_data( '_billing_address_id', $billing_id );
            $order->update_meta_data( '_billing_address_type', $address['address_type'] );
            
            if ( ! empty( $address['vat_number'] ) ) {
                $order->update_meta_data( '_billing_vat_number', $address['vat_number'] );
            }
            if ( ! empty( $address['reg_com'] ) ) {
                $order->update_meta_data( '_billing_reg_number', $address['reg_com'] );
            }
        }
    }
    
    // Use selected shipping address or default
    $shipping_id = isset( $_POST['selected_shipping_address_id'] ) ? sanitize_text_field( $_POST['selected_shipping_address_id'] ) : '';
    if ( ! empty( $shipping_id ) ) {
        $address = ANA_Addresses_Plugin::get_address( $user_id, $shipping_id, 'shipping' );
        if ( $address ) {
            $order->set_shipping_first_name( $address['first_name'] );
            $order->set_shipping_last_name( $address['last_name'] );
            $order->set_shipping_address_1( $address['address_1'] );
            $order->set_shipping_address_2( $address['address_2'] );
            $order->set_shipping_city( $address['city'] );
            $order->set_shipping_state( $address['state'] );
            $order->set_shipping_postcode( $address['postcode'] );
            
            $order->update_meta_data( '_shipping_address_id', $shipping_id );
        }
    }
}

/**
 * Display address type in order admin
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'ana_display_order_address_type' );
function ana_display_order_address_type( $order ) {
    $address_type = $order->get_meta( '_billing_address_type' );
    $vat_number = $order->get_meta( '_billing_vat_number' );
    $reg_number = $order->get_meta( '_billing_reg_number' );
    
    if ( $address_type === 'pj' ) {
        echo '<p><strong>' . __( 'Tip factură:', 'ana-addresses' ) . '</strong> Persoană Juridică</p>';
        if ( $vat_number ) {
            echo '<p><strong>' . __( 'CUI:', 'ana-addresses' ) . '</strong> ' . esc_html( $vat_number ) . '</p>';
        }
        if ( $reg_number ) {
            echo '<p><strong>' . __( 'Nr. Reg. Com.:', 'ana-addresses' ) . '</strong> ' . esc_html( $reg_number ) . '</p>';
        }
    } else {
        echo '<p><strong>' . __( 'Tip factură:', 'ana-addresses' ) . '</strong> Persoană Fizică</p>';
    }
}

/**
 * Populate WooCommerce customer session with selected address
 * This ensures shipping plugins (Cargus, etc.) can access the address
 */
add_action( 'wp_ajax_ana_load_addresses', 'ana_sync_address_to_session_on_load', 20 );
add_action( 'woocommerce_checkout_update_customer_data', 'ana_sync_selected_address_to_customer', 10, 2 );

function ana_sync_selected_address_to_customer( $customer, $data ) {
    if (!$customer || !is_object($customer)) {
        return;
    }

    if ( ! is_user_logged_in() ) {
        return;
    }
    
    $user_id = get_current_user_id();
    
    // Sync shipping address if selected
    if ( ! empty( $_POST['selected_shipping_address_id'] ) ) {
        $shipping_id = sanitize_text_field( $_POST['selected_shipping_address_id'] );
        $address = ANA_Addresses_Plugin::get_address( $user_id, $shipping_id, 'shipping' );
        
        if ( $address ) {
            // Update WooCommerce customer session
            $customer->set_shipping_first_name( $address['first_name'] );
            $customer->set_shipping_last_name( $address['last_name'] );
            $customer->set_shipping_address_1( $address['address_1'] );
            $customer->set_shipping_address_2( $address['address_2'] );
            $customer->set_shipping_city( $address['city'] );
            $customer->set_shipping_state( $address['state'] );
            $customer->set_shipping_postcode( $address['postcode'] );
            $customer->set_shipping_country( $address['country'] );
        }
    }
    
    // Sync billing address if selected
    if ( ! empty( $_POST['selected_billing_address_id'] ) ) {
        $billing_id = sanitize_text_field( $_POST['selected_billing_address_id'] );
        $address = ANA_Addresses_Plugin::get_address( $user_id, $billing_id, 'billing' );
        
        if ( $address ) {
            $customer->set_billing_first_name( $address['first_name'] );
            $customer->set_billing_last_name( $address['last_name'] );
            $customer->set_billing_company( $address['company'] );
            $customer->set_billing_address_1( $address['address_1'] );
            $customer->set_billing_address_2( $address['address_2'] );
            $customer->set_billing_city( $address['city'] );
            $customer->set_billing_state( $address['state'] );
            $customer->set_billing_postcode( $address['postcode'] );
            $customer->set_billing_country( $address['country'] );
            $customer->set_billing_phone( $address['phone'] );
            $customer->set_billing_email( $address['email'] );
        }
    }
}

/**
 * Ensure shipping packages have the correct address
 * Critical for external shipping plugins (Cargus)
 */
add_filter( 'woocommerce_cart_shipping_packages', 'ana_populate_shipping_packages', 10 );
function ana_populate_shipping_packages( $packages ) {
    if ( ! is_user_logged_in() ) {
        return $packages;
    }
    
    // Get selected shipping address ID from session or POST
    $shipping_id = '';
    if ( isset( $_POST['selected_shipping_address_id'] ) ) {
        $shipping_id = sanitize_text_field( $_POST['selected_shipping_address_id'] );
        WC()->session->set( 'ana_selected_shipping_id', $shipping_id );
    } else {
        $shipping_id = WC()->session->get( 'ana_selected_shipping_id', '' );
    }
    
    if ( ! empty( $shipping_id ) ) {
        $user_id = get_current_user_id();
        $address = ANA_Addresses_Plugin::get_address( $user_id, $shipping_id, 'shipping' );
        
        if ( $address && ! empty( $packages ) ) {
            // Update destination in all packages
            foreach ( $packages as $key => $package ) {
                $packages[ $key ]['destination'] = [
                    'country'   => $address['country'],
                    'state'     => $address['state'],
                    'postcode'  => $address['postcode'],
                    'city'      => $address['city'],
                    'address'   => $address['address_1'],
                    'address_1' => $address['address_1'],
                    'address_2' => $address['address_2'],
                ];
            }
        }
    }
    
    return $packages;
}

/**
 * Store selected address in WooCommerce session when customer data is posted
 */
add_action( 'woocommerce_checkout_update_order_review', 'ana_store_selected_addresses_in_session' );
function ana_store_selected_addresses_in_session( $post_data ) {
    parse_str( $post_data, $data );
    
    if ( isset( $data['selected_billing_address_id'] ) ) {
        WC()->session->set( 'ana_selected_billing_id', sanitize_text_field( $data['selected_billing_address_id'] ) );
    }
    
    if ( isset( $data['selected_shipping_address_id'] ) ) {
        WC()->session->set( 'ana_selected_shipping_id', sanitize_text_field( $data['selected_shipping_address_id'] ) );
        
        // Trigger shipping calculation refresh
        WC()->cart->calculate_shipping();
    }
}

/**
 * Clear selected addresses from session after order completion
 */
add_action( 'woocommerce_thankyou', 'ana_clear_session_addresses' );
function ana_clear_session_addresses( $order_id ) {
    if ( WC()->session ) {
        WC()->session->set( 'ana_selected_billing_id', null );
        WC()->session->set( 'ana_selected_shipping_id', null );
    }
}

/**
 * Apply filter for external plugins to modify address format
 */
function ana_get_formatted_address_for_shipping( $address ) {
    return apply_filters( 'ana_addresses_shipping_format', $address );
}

/**
 * Add Reg Number to formatted billing address
 */
add_filter( 'woocommerce_order_get_formatted_billing_address', 'ana_add_reg_number_to_formatted_address', 10, 3 );
function ana_add_reg_number_to_formatted_address( $address_string, $raw_address, $order ) {
    $reg_number = $order->get_meta( '_billing_reg_number' );
    
    if ( $reg_number ) {
        // Look for company to place it after, or just append
        $company = $order->get_billing_company();
        $formatted_reg = '<br/>' . __( 'Nr. Reg. Com.:', 'ana-addresses' ) . ' ' . esc_html( $reg_number );
        
        if ( $company && strpos( $address_string, $company ) !== false ) {
             // Try to insert after the line containing company if possible, or just append
             $address_string .= $formatted_reg;
        } else {
             $address_string .= $formatted_reg;
        }
    }
    
    return $address_string;
}

/**
 * Add replacements for address formats
 */
add_filter( 'woocommerce_formatted_address_replacements', 'ana_address_replacements', 10, 2 );
function ana_address_replacements( $replacements, $args ) {
    $replacements['{reg_number}'] = isset( $args['reg_number'] ) ? $args['reg_number'] : '';
    return $replacements;
}

/**
 * Add Reg Number to localization formats for RO
 */
add_filter( 'woocommerce_localisation_address_formats', 'ana_localisation_address_formats' );
function ana_localisation_address_formats( $formats ) {
    if ( isset( $formats['RO'] ) ) {
        $formats['RO'] .= "\n{reg_number}";
    }
    return $formats;
}

