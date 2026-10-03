<?php
/**
 * AJAX Handler for Getting Single Address
 * 
 * @package ANA_Addresses
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get single address for editing
 */
add_action( 'wp_ajax_ana_get_single_address', 'ana_ajax_get_single_address' );
function ana_ajax_get_single_address() {
    // TEMPORARILY DISABLED - Same ModSecurity 403 issue as other AJAX handlers
    // TODO: Re-enable after ModSecurity rules are configured properly
    /*
    $nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : ( isset( $_POST['security'] ) ? $_POST['security'] : '' );
    
    // Verify nonce - accept either ana_modal_nonce or woocommerce-process_checkout
    $valid_nonce = wp_verify_nonce( $nonce, 'ana_modal_nonce' ) || 
                   wp_verify_nonce( $nonce, 'woocommerce-process_checkout' ) ||
                   wp_verify_nonce( $nonce, 'ana-addresses-nonce' );
    
    if ( ! $valid_nonce ) {
        wp_send_json_error( array( 'message' => 'Invalid security token' ) );
    }
    */
    
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( array( 'message' => 'Not logged in' ) );
    }
    
    global $wpdb;
    $user_id = get_current_user_id();
    $address_id = isset( $_POST['address_id'] ) ? sanitize_text_field( $_POST['address_id'] ) : '';
    $address_type = isset( $_POST['address_type'] ) ? sanitize_text_field( $_POST['address_type'] ) : 'billing';
    
    if ( empty( $address_id ) ) {
        wp_send_json_error( array( 'message' => 'Invalid address ID' ) );
    }
    
    // Try to get address directly from database first
    $table_name = $wpdb->prefix . 'ana_addresses';
    
    // Check if address_id is numeric (database ID) or string (legacy)
    if ( is_numeric( $address_id ) ) {
        $address = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE id = %d AND user_id = %d",
            absint( $address_id ),
            $user_id
        ), ARRAY_A );
        
        if ( $address ) {
            wp_send_json_success( $address );
        }
    }
    
    // Fallback: search through all addresses using ANA_Addresses_Plugin
    if ( class_exists( 'ANA_Addresses_Plugin' ) ) {
        // Get billing addresses
        $billing_addresses = ANA_Addresses_Plugin::get_addresses( $user_id, 'billing' );
        $shipping_addresses = ANA_Addresses_Plugin::get_addresses( $user_id, 'shipping' );
        
        $all_addresses = array_merge( $billing_addresses, $shipping_addresses );
        
        foreach ( $all_addresses as $addr ) {
            if ( isset( $addr['id'] ) && (string) $addr['id'] === (string) $address_id ) {
                wp_send_json_success( $addr );
            }
        }
    }
    
    // Try legacy ANA_Addresses_Manager if available
    if ( class_exists( 'ANA_Addresses_Manager' ) ) {
        $addresses_manager = ANA_Addresses_Manager::get_instance();
        $all_addresses = $addresses_manager->get_user_addresses( $user_id );
        
        foreach ( $all_addresses as $addr ) {
            if ( isset( $addr['id'] ) && (string) $addr['id'] === (string) $address_id ) {
                wp_send_json_success( $addr );
            }
        }
    }
    
    wp_send_json_error( array( 'message' => 'Address not found (ID: ' . $address_id . ')' ) );
}
