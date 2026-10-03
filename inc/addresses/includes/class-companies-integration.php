<?php
/**
 * Companies Integration for ANA Addresses Manager
 * 
 * Bridges wpu3_ana_companies table with addresses manager plugin
 * Converts company records to address format for checkout compatibility
 * 
 * @package ANA_Addresses
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ANA_Companies_Integration {
    
    /**
     * Get user companies from wpu3_ana_companies table formatted as addresses
     * 
     * @param int $user_id User ID
     * @param string $type Address type ('billing' or 'shipping')
     * @return array Array of companies formatted as addresses
     */
    public static function get_user_companies_as_addresses( $user_id, $type = 'billing' ) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ana_companies';
        
        // Check if companies table exists
        $table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) );
        if ( ! $table_exists ) {
            return [];
        }
        
        // Get all companies for this user
        $companies = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE user_id = %d ORDER BY is_default DESC, created_at DESC",
                $user_id
            ),
            ARRAY_A
        );
        
        if ( empty( $companies ) ) {
            return [];
        }
        
        // Convert each company to address format
        $addresses = [];
        foreach ( $companies as $company ) {
            $addresses[] = self::company_to_address_format( $company, $type );
        }
        
        return $addresses;
    }
    
    /**
     * Convert company record to address format
     * Maps company fields to WooCommerce address fields
     * 
     * @param array $company Company data from wpu3_ana_companies
     * @param string $type Address type ('billing' or 'shipping')
     * @return array Address-formatted data
     */
    public static function company_to_address_format( $company, $type = 'billing' ) {
        // Split representative name into first/last name
        $rep_name = isset( $company['rep_name'] ) ? trim( $company['rep_name'] ) : '';
        $name_parts = explode( ' ', $rep_name, 2 );
        $first_name = isset( $name_parts[0] ) ? $name_parts[0] : '';
        $last_name = isset( $name_parts[1] ) ? $name_parts[1] : '';
        
        // Combine street and number for address_1
        $street = isset( $company['legal_street'] ) ? $company['legal_street'] : '';
        $number = isset( $company['legal_number'] ) ? $company['legal_number'] : '';
        $address_1 = trim( $street . ' ' . $number );
        
        return [
            'id'           => 'company_' . $company['id'], // Prefix to distinguish from regular addresses
            'address_type' => $type,
            'entity_type'  => 'pj', // Always PJ (Persoană Juridică)
            
            // Company info
            'company'      => isset( $company['company_name'] ) ? $company['company_name'] : '',
            'vat_number'   => isset( $company['cui'] ) ? $company['cui'] : '',
            
            // Representative (mapped to first/last name for compatibility)
            'first_name'   => $first_name,
            'last_name'    => $last_name,
            
            // Address fields
            'address_1'    => $address_1,
            'address_2'    => isset( $company['reg_com'] ) ? 'Reg. Com: ' . $company['reg_com'] : '',
            'city'         => isset( $company['legal_locality'] ) ? $company['legal_locality'] : '',
            'state'        => isset( $company['legal_county'] ) ? $company['legal_county'] : '',
            'postcode'     => isset( $company['legal_postal_code'] ) ? $company['legal_postal_code'] : '',
            'country'      => 'RO',
            
            // Contact
            'phone'        => isset( $company['phone'] ) ? $company['phone'] : '',
            'email'        => isset( $company['email'] ) ? $company['email'] : '',
            
            // Metadata
            'is_default'   => isset( $company['is_default'] ) ? $company['is_default'] : 0,
            'created_at'   => isset( $company['created_at'] ) ? $company['created_at'] : current_time( 'mysql' ),
            'updated_at'   => isset( $company['updated_at'] ) ? $company['updated_at'] : null,
            
            // Extra company-specific fields (for display/reference)
            'rep_position' => isset( $company['rep_position'] ) ? $company['rep_position'] : '',
            'bank_name'    => isset( $company['bank_name'] ) ? $company['bank_name'] : '',
            'iban'         => isset( $company['iban'] ) ? $company['iban'] : '',
            
            // Source marker
            '_source'      => 'companies_table',
        ];
    }
    
    /**
     * Get single company by ID
     * 
     * @param int $company_id Company ID
     * @return array|null Company data or null if not found
     */
    public static function get_company_by_id( $company_id ) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ana_companies';
        
        $company = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE id = %d",
                $company_id
            ),
            ARRAY_A
        );
        
        return $company ? $company : null;
    }
    
    /**
     * Check if a company exists in the dedicated table
     * 
     * @param int $company_id Company ID
     * @return bool True if exists, false otherwise
     */
    public static function company_exists( $company_id ) {
        return self::get_company_by_id( $company_id ) !== null;
    }
    
    /**
     * Get total companies count for a user
     * 
     * @param int $user_id User ID
     * @return int Number of companies
     */
    public static function get_companies_count( $user_id ) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ana_companies';
        
        $count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name} WHERE user_id = %d",
                $user_id
            )
        );
        
        return intval( $count );
    }
    
    /**
     * Get total companies count (all users)
     * 
     * @return int Total number of companies
     */
    public static function get_total_companies_count() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ana_companies';
        
        // Check if table exists
        $table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) );
        if ( ! $table_exists ) {
            return 0;
        }
        
        $count = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name}" );
        
        return intval( $count );
    }
}
