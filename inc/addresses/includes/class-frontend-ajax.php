<?php
/**
 * Ana Addresses Manager - Frontend AJAX Handler
 * 
 * Handles AJAX requests from frontend (My Account, Checkout)
 * for address management operations
 * 
 * @package AnaAddressesManager
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class ANA_Addresses_Frontend_AJAX {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Only for logged-in users
        if (!is_user_logged_in()) {
            return;
        }
        
        // Register AJAX handlers
        add_action('wp_ajax_ana_frontend_get_address', [$this, 'get_address']);
        add_action('wp_ajax_ana_frontend_save_address', [$this, 'save_address']);
        add_action('wp_ajax_ana_frontend_delete_address', [$this, 'delete_address']);
        add_action('wp_ajax_ana_frontend_set_default', [$this, 'set_default']);
        add_action('wp_ajax_ana_get_user_addresses', [$this, 'get_user_addresses_list']);
    }
    
    /**
     * Get single address data for editing
     */
    public function get_address() {
        // TEMPORARY DEBUG LOGGING
        error_log('=== ANA FRONTEND GET ADDRESS CALLED ===');
        error_log('POST data: ' . print_r($_POST, true));
        error_log('Expected nonce action: ana_frontend_nonce');
        error_log('Received nonce: ' . ($_POST['nonce'] ?? 'NOT SET'));
        
        // Verify nonce
        $nonce_check = check_ajax_referer('ana_frontend_nonce', 'nonce', false);
        error_log('Nonce check result: ' . ($nonce_check ? 'VALID' : 'INVALID'));
        
        if (!$nonce_check) {
            error_log('NONCE FAILED - Sending error response');
            wp_send_json_error(['message' => 'Nonce invalid']);
            return;
        }
        
        $address_id_raw = isset($_POST['address_id']) ? sanitize_text_field($_POST['address_id']) : '';

        if (strpos($address_id_raw, 'company_') === 0 && class_exists('ANA_Companies_Integration')) {
            // It's a read-only company address
             // Extract ID
            $company_id = intval(str_replace('company_', '', $address_id_raw));
            
            // Get company data
            $company = ANA_Companies_Integration::get_company_by_id($company_id);
            
            if ($company) {
                // Convert to address format
                $address = ANA_Companies_Integration::company_to_address_format($company, 'billing');
                wp_send_json_success($address);
                return;
            }
        }

        $address_id = intval($address_id_raw);
        $user_id = get_current_user_id();
        
        if (!$address_id) {
            wp_send_json_error(['message' => 'ID adresă invalid']);
        }
        
        global $wpdb;
        $table_name = ANA_Addresses_Database::get_table_name();
        
        $address = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE id = %d AND user_id = %d",
            $address_id,
            $user_id
        ), ARRAY_A);
        
        if (!$address) {
            wp_send_json_error(['message' => 'Adresa nu a fost găsită']);
        }
        
        wp_send_json_success($address);
    }
    
    /**
     * Save address (add or update)
     */
    public function save_address() {
        // DEBUG LOG
        error_log('=== ANA FRONTEND SAVE ADDRESS ===');
        error_log('POST payload: ' . print_r($_POST, true));

        check_ajax_referer('ana_frontend_nonce', 'nonce');
        
        $user_id = get_current_user_id();
        
        $address_id_raw = isset($_POST['address_id']) ? sanitize_text_field($_POST['address_id']) : '';
        $address_id = 0;
        
        // Check if it's a numeric ID (existing local address)
        // If it starts with 'company_', it's a read-only address being migrated -> ID stays 0 to Insert New
        if (is_numeric($address_id_raw) && $address_id_raw > 0) {
            $address_id = intval($address_id_raw);
        }
        
        global $wpdb;
        $table_name = ANA_Addresses_Database::get_table_name();
        
        // If editing, verify ownership
        if ($address_id > 0) {
            $existing = $wpdb->get_row($wpdb->prepare(
                "SELECT user_id FROM {$table_name} WHERE id = %d",
                $address_id
            ));
            
            if (!$existing || $existing->user_id != $user_id) {
                error_log('Access denied for address ID: ' . $address_id);
                wp_send_json_error(['message' => 'Acces interzis']);
            }
        }
        
        // Prepare address data
        $address_data = [
            'user_id' => $user_id,
            'address_type' => sanitize_text_field($_POST['address_type'] ?? 'billing'),
            'entity_type' => sanitize_text_field($_POST['entity_type'] ?? 'pf'),
            'first_name' => sanitize_text_field($_POST['first_name'] ?? ''),
            'last_name' => sanitize_text_field($_POST['last_name'] ?? ''),
            'company' => sanitize_text_field($_POST['company'] ?? ''),
            'vat_number' => sanitize_text_field($_POST['vat_number'] ?? ''),
            'reg_com' => sanitize_text_field($_POST['reg_number'] ?? ''), // Map frontend reg_number to db reg_com
            'address_1' => sanitize_text_field($_POST['address_1'] ?? ''),
            'address_2' => sanitize_text_field($_POST['address_2'] ?? ''),
            'city' => sanitize_text_field($_POST['city'] ?? ''),
            'state' => sanitize_text_field($_POST['state'] ?? ''),
            'postcode' => sanitize_text_field($_POST['postcode'] ?? ''),
            'country' => sanitize_text_field($_POST['country'] ?? 'RO'),
            'phone' => sanitize_text_field($_POST['phone'] ?? ''),
            'email' => sanitize_email($_POST['email'] ?? ''),
            'is_default' => isset($_POST['is_default']) ? 1 : 0,
        ];
        
        // TEMPORARY DEBUG LOGGING
        error_log('=== ANA SAVE ADDRESS DEBUG ===');
        error_log('is_default in POST: ' . (isset($_POST['is_default']) ? 'YES (1)' : 'NO (0)'));
        error_log('is_default value: ' . $address_data['is_default']);
        error_log('Address ID: ' . $address_id);
        error_log('===============================');
        
        // If setting as default, unset other defaults
        if ($address_data['is_default']) {
            $wpdb->update(
                $table_name,
                ['is_default' => 0],
                [
                    'user_id' => $user_id,
                    'address_type' => $address_data['address_type']
                ]
            );
        }
        
        if ($address_id > 0) {
            // Update existing address
            $address_data['updated_at'] = current_time('mysql');
            $result = $wpdb->update($table_name, $address_data, ['id' => $address_id]);
            
            if ($result === false) {
                wp_send_json_error(['message' => 'Eroare la actualizare']);
            }
            
            wp_send_json_success([
                'message' => 'Adresa a fost actualizată',
                'address_id' => $address_id
            ]);
        } else {
            // Insert new address
            $address_data['created_at'] = current_time('mysql');
            $result = $wpdb->insert($table_name, $address_data);
            
            if (!$result) {
                wp_send_json_error(['message' => 'Eroare la salvare']);
            }
            
            wp_send_json_success([
                'message' => 'Adresa a fost adăugată',
                'address_id' => $wpdb->insert_id
            ]);
        }
    }
    
    /**
     * Delete address
     */
    public function delete_address() {
        check_ajax_referer('ana_frontend_nonce', 'nonce');
        
        $address_id_raw = isset($_POST['address_id']) ? sanitize_text_field($_POST['address_id']) : '';

        // Handle read-only company addresses
        if (strpos($address_id_raw, 'company_') === 0 && class_exists('ANA_Companies_Integration')) {
            // Extract ID
            $company_id = intval(str_replace('company_', '', $address_id_raw));
            
            // Get company data
            $company = ANA_Companies_Integration::get_company_by_id($company_id);
            
            if ($company) {
                // Convert to address format
                $address = ANA_Companies_Integration::company_to_address_format($company, 'billing');
                wp_send_json_success($address);
                return;
            }
        }

        $address_id = intval($address_id_raw);
        $user_id = get_current_user_id();
        
        if (!$address_id) {
            wp_send_json_error(['message' => 'ID adresă invalid']);
        }
        
        global $wpdb;
        $table_name = ANA_Addresses_Database::get_table_name();
        
        // Verify ownership
        $address = $wpdb->get_row($wpdb->prepare(
            "SELECT user_id FROM {$table_name} WHERE id = %d",
            $address_id
        ));
        
        if (!$address || $address->user_id != $user_id) {
            wp_send_json_error(['message' => 'Acces interzis']);
        }
        
        $result = $wpdb->delete($table_name, ['id' => $address_id]);
        
        if (!$result) {
            wp_send_json_error(['message' => 'Eroare la ștergere']);
        }
        
        wp_send_json_success(['message' => 'Adresa a fost ștearsă']);
    }
    
    /**
     * Set address as default
     */
    public function set_default() {
        check_ajax_referer('ana_frontend_nonce', 'nonce');
        
        $address_id = isset($_POST['address_id']) ? intval($_POST['address_id']) : 0;
        $address_type = isset($_POST['address_type']) ? sanitize_text_field($_POST['address_type']) : 'billing';
        $user_id = get_current_user_id();
        
        if (!$address_id) {
            wp_send_json_error(['message' => 'ID adresă invalid']);
        }
        
        global $wpdb;
        $table_name = ANA_Addresses_Database::get_table_name();
        
        // Verify ownership
        $address = $wpdb->get_row($wpdb->prepare(
            "SELECT user_id FROM {$table_name} WHERE id = %d",
            $address_id
        ));
        
        if (!$address || $address->user_id != $user_id) {
            wp_send_json_error(['message' => 'Acces interzis']);
        }
        
        // Unset all defaults for this address type
        $wpdb->update(
            $table_name,
            ['is_default' => 0],
            [
                'user_id' => $user_id,
                'address_type' => $address_type
            ]
        );
        
        // Set this as default
        $result = $wpdb->update(
            $table_name,
            ['is_default' => 1],
            ['id' => $address_id]
        );
        
        if ($result === false) {
            wp_send_json_error(['message' => 'Eroare la setare predefinită']);
        }
        
        wp_send_json_success(['message' => 'Adresa predefinită a fost setată']);
    }
    
    /**
     * Get user addresses list for selector modal
     */
    public function get_user_addresses_list() {
        check_ajax_referer('ana_frontend_nonce', 'nonce');
        
        $user_id = get_current_user_id();
        $address_type = sanitize_text_field($_POST['address_type'] ?? 'billing');
        
        if (!$user_id) {
            wp_send_json_error(['message' => 'User not logged in']);
        }
        
        // Get addresses using helper function
        if (function_exists('ana_get_user_addresses')) {
            $addresses = ana_get_user_addresses($user_id, $address_type);
            wp_send_json_success($addresses);
        } else {
            wp_send_json_error(['message' => 'Address function not found']);
        }
    }
}

// Initialize on 'init' hook to ensure user session is established
add_action('init', function() {
    if (is_user_logged_in()) {
        new ANA_Addresses_Frontend_AJAX();
    }
}, 10);
