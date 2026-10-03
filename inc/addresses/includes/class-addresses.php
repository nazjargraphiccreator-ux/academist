<?php
/**
 * ANA Addresses Manager - Hybrid Storage
 * 
 * Manages multiple billing and shipping addresses for users
 * Supports both database storage (new) and user meta (legacy)
 * Auto-migrates from user meta to database on first read
 * 
 * @package ANA_Addresses
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ANA_Addresses_Plugin {
    
    /**
     * Get all addresses for a user (HYBRID + COMPANIES)
     * Checks database first, falls back to user meta, auto-migrates
     * Also includes companies from wpu3_ana_companies table when PJ filter is used
     * 
     * @param int $user_id User ID
     * @param string $type 'billing' or 'shipping'
     * @param string $filter Optional: 'pf' or 'pj' to filter by type
     * @return array Array of addresses
     */
    public static function get_addresses( $user_id, $type = 'billing', $filter = '' ) {
        global $wpdb;
        
        // AUTO-MIGRATE: Trigger migration for this user if not done yet
        // This ensures companies from "Firmele mele" are migrated when addresses are accessed
        ANA_Addresses_Database::auto_migrate_user_saved_addresses( $user_id );
        
        // First, check database for regular addresses
        $db_addresses = self::get_addresses_from_db( $user_id, $type, $filter );
        
        // Get companies from dedicated table if filter is PJ or no filter
        $companies = [];
        if ( $filter === 'pj' || $filter === '' ) {
            $companies = ANA_Companies_Integration::get_user_companies_as_addresses( $user_id, $type );
        }
        
        // Merge addresses from database with companies from dedicated table
        // BUT only if they are not already in the database (to avoid duplicates after migration)
        // Only consider it a duplicate if it's already a PJ address in the DB
        $existing_companies = array_map( function($addr) {
            if ( isset($addr['entity_type']) && $addr['entity_type'] === 'pj' ) {
                return isset($addr['company']) ? $addr['company'] : '';
            }
            return '';
        }, $db_addresses );
        
        $unique_companies = array_filter( $companies, function($company) use ($existing_companies) {
            return ! in_array( $company['company'], $existing_companies );
        });
        
        $all_addresses = array_merge( $db_addresses, $unique_companies );
        
        // If we have addresses (from DB or companies), return them
        if ( ! empty( $all_addresses ) ) {
            return $all_addresses;
        }
        
        // No addresses in database - check user meta (legacy)
        $meta_addresses = self::get_addresses_from_meta( $user_id, $type, $filter );
        
        // If we found addresses in user meta, migrate them to database
        if ( ! empty( $meta_addresses ) ) {
            self::migrate_user_addresses( $user_id, $type, $meta_addresses );
            // Return the migrated addresses from database + companies
            $db_addresses = self::get_addresses_from_db( $user_id, $type, $filter );
            return array_merge( $db_addresses, $companies );
        }
        
        // No addresses in either location - try WooCommerce default
        $wc_addresses = self::migrate_woocommerce_address( $user_id, $type );
        
        if ( ! empty( $wc_addresses ) ) {
            // Save to database
            foreach ( $wc_addresses as $addr ) {
                self::insert_address_to_db( $user_id, $addr, $type );
            }
            $db_addresses = self::get_addresses_from_db( $user_id, $type, $filter );
            return array_merge( $db_addresses, $companies );
        }
        
        return $companies; // Return companies even if no regular addresses
    }
    
    /**
     * Get addresses from database
     */
    private static function get_addresses_from_db( $user_id, $type, $filter = '' ) {
        global $wpdb;
        $table_name = ANA_Addresses_Database::get_table_name();
        
        // Check if table exists
        if ( ! ANA_Addresses_Database::table_exists() ) {
            return [];
        }
        
        $where = $wpdb->prepare( "user_id = %d AND address_type = %s", $user_id, $type );
        
        if ( ! empty( $filter ) && in_array( $filter, [ 'pf', 'pj' ] ) ) {
            $where .= $wpdb->prepare( " AND entity_type = %s", $filter );
        }
        
        $results = $wpdb->get_results( 
            "SELECT * FROM {$table_name} WHERE {$where} ORDER BY is_default DESC, created_at DESC",
            ARRAY_A 
        );
        
        return $results ? $results : [];
    }
    
    /**
     * Get addresses from user meta (legacy)
     */
    private static function get_addresses_from_meta( $user_id, $type, $filter = '' ) {
        $meta_key = '_ana_' . $type . '_addresses';
        $addresses = get_user_meta( $user_id, $meta_key, true );
        
        if ( ! is_array( $addresses ) ) {
            return [];
        }
        
        // Filter by PF/PJ if requested
        if ( ! empty( $filter ) && in_array( $filter, [ 'pf', 'pj' ] ) ) {
            $addresses = array_filter( $addresses, function( $addr ) use ( $filter ) {
                // Check both 'address_type' (old) and 'entity_type' (new)
                $entity = isset( $addr['entity_type'] ) ? $addr['entity_type'] : 
                         ( isset( $addr['address_type'] ) ? $addr['address_type'] : 'pf' );
                return $entity === $filter;
            });
        }
        
        return $addresses;
    }
    
    /**
     * Migrate user addresses from meta to database
     */
    private static function migrate_user_addresses( $user_id, $type, $addresses ) {
        foreach ( $addresses as $addr ) {
            self::insert_address_to_db( $user_id, $addr, $type );
        }
    }
    
    /**
     * Insert address to database
     */
    public static function insert_address_to_db( $user_id, $data, $type ) {
        global $wpdb;
        $table_name = ANA_Addresses_Database::get_table_name();
        
        // Map old 'address_type' field to 'entity_type'
        $entity_type = isset( $data['entity_type'] ) ? $data['entity_type'] : 
                      ( isset( $data['address_type'] ) ? $data['address_type'] : 'pf' );
        
        // Validate Data if Entity Type is PJ
        if ( $entity_type === 'pj' ) {
            require_once ANA_ADDR_PLUGIN_DIR . 'includes/class-validator.php';
            
            // Validate CUI
            if ( ! empty( $data['vat_number'] ) ) {
                $cui_valid = ANA_Address_Validator::validate_cui( $data['vat_number'] );
                if ( is_wp_error( $cui_valid ) ) {
                    // We can't easily return WP_Error from here if the caller expects an ID.
                    // For now, we'll log it or maybe throw an exception? 
                    // Or better, we just save it but maybe mark it?
                    // Ideally, the caller should validate BEFORE calling this.
                    // But to be safe, let's allow saving but maybe add a note?
                    // Actually, let's enforce it if it's a direct save.
                    // If we return 0 or false, it might break things.
                    // Let's assume the frontend does validation, but here we just sanitize.
                    // However, the user requested "Advanced Validation".
                    // Let's return the WP_Error and handle it in the caller (AJAX handler).
                    return $cui_valid;
                }
            }
            
            // Validate IBAN
            if ( ! empty( $data['iban'] ) ) {
                $iban_valid = ANA_Address_Validator::validate_iban( $data['iban'] );
                if ( is_wp_error( $iban_valid ) ) {
                    return $iban_valid;
                }
            }
        }

        $insert_data = [
            'user_id' => $user_id,
            'address_type' => $type,
            'entity_type' => $entity_type,
            'first_name' => isset( $data['first_name'] ) ? $data['first_name'] : '',
            'last_name' => isset( $data['last_name'] ) ? $data['last_name'] : '',
            'company' => isset( $data['company'] ) ? $data['company'] : '',
            'vat_number' => isset( $data['vat_number'] ) ? $data['vat_number'] : '',
            'reg_com' => isset( $data['reg_com'] ) ? $data['reg_com'] : '',
            'bank' => isset( $data['bank'] ) ? $data['bank'] : '',
            'iban' => isset( $data['iban'] ) ? $data['iban'] : '',
            'address_1' => isset( $data['address_1'] ) ? $data['address_1'] : '',
            'address_2' => isset( $data['address_2'] ) ? $data['address_2'] : '',
            'city' => isset( $data['city'] ) ? $data['city'] : '',
            'state' => isset( $data['state'] ) ? $data['state'] : '',
            'postcode' => isset( $data['postcode'] ) ? $data['postcode'] : '',
            'country' => isset( $data['country'] ) ? $data['country'] : 'RO',
            'phone' => isset( $data['phone'] ) ? $data['phone'] : '',
            'email' => isset( $data['email'] ) ? $data['email'] : '',
            'is_default' => isset( $data['is_default'] ) ? (int) $data['is_default'] : 0,
            'created_at' => isset( $data['created_at'] ) ? $data['created_at'] : current_time( 'mysql' ),
        ];
        
        $wpdb->insert( $table_name, $insert_data );
        
        return $wpdb->insert_id;
    }
    
    /**
     * Get single address by ID
     */
    public static function get_address( $user_id, $address_id, $type = 'billing' ) {
        global $wpdb;
        $table_name = ANA_Addresses_Database::get_table_name();
        
        if ( ! ANA_Addresses_Database::table_exists() ) {
            return null;
        }
        
        $result = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE id = %d AND user_id = %d AND address_type = %s",
            $address_id,
            $user_id,
            $type
        ), ARRAY_A );
        
        return $result;
    }
    
    /**
     * Add new address
     */
    public static function add_address( $user_id, $data, $type = 'billing' ) {
        global $wpdb;
        
        // Map old 'address_type' to 'entity_type'
        $entity_type = isset( $data['address_type'] ) ? $data['address_type'] : 
                      ( isset( $data['entity_type'] ) ? $data['entity_type'] : 'pf' );
        
        // Auto-detect PJ if company or VAT number is present
        if ( ( ! empty( $data['company'] ) || ! empty( $data['vat_number'] ) ) && $entity_type !== 'pj' ) {
            $entity_type = 'pj';
        }
        
        // Respect user's explicit choice for default address
        // No longer auto-setting first address as default
        $is_default = isset( $data['is_default'] ) ? (int) $data['is_default'] : 0;
        
        $address_id = self::insert_address_to_db( $user_id, array_merge( $data, [
            'entity_type' => $entity_type,
            'is_default' => $is_default
        ]), $type );
        
        // If set as default, update others
        if ( $is_default ) {
            self::set_default_address( $user_id, $address_id, $type );
        }
        
        return $address_id;
    }
    
    /**
     * Update existing address
     */
    public static function update_address( $user_id, $address_id, $data, $type = 'billing' ) {
        global $wpdb;
        $table_name = ANA_Addresses_Database::get_table_name();
        
        // Prepare update data
        $update_data = [];
        $allowed_fields = [
            'entity_type', 'first_name', 'last_name', 'company', 'vat_number',
            'reg_com', 'bank', 'iban',
            'address_1', 'address_2', 'city', 'state', 'postcode', 'country',
            'phone', 'email', 'is_default'
        ];
        
        // Map old 'address_type' to 'entity_type'
        if ( isset( $data['address_type'] ) && ! isset( $data['entity_type'] ) ) {
            $data['entity_type'] = $data['address_type'];
        }
        
        foreach ( $allowed_fields as $field ) {
            if ( isset( $data[$field] ) ) {
                $update_data[$field] = $data[$field];
            }
        }

        // Validate if updating PJ fields
        if ( isset( $update_data['entity_type'] ) && $update_data['entity_type'] === 'pj' ) {
            require_once ANA_ADDR_PLUGIN_DIR . 'includes/class-validator.php';
            
            if ( ! empty( $update_data['vat_number'] ) ) {
                $cui_valid = ANA_Address_Validator::validate_cui( $update_data['vat_number'] );
                if ( is_wp_error( $cui_valid ) ) return $cui_valid;
            }
            
            if ( ! empty( $update_data['iban'] ) ) {
                $iban_valid = ANA_Address_Validator::validate_iban( $update_data['iban'] );
                if ( is_wp_error( $iban_valid ) ) return $iban_valid;
            }
        }
        
        if ( empty( $update_data ) ) {
            return false;
        }
        
        $update_data['updated_at'] = current_time( 'mysql' );
        
        $result = $wpdb->update(
            $table_name,
            $update_data,
            [
                'id' => $address_id,
                'user_id' => $user_id,
                'address_type' => $type
            ],
            null,
            [ '%d', '%d', '%s' ]
        );
        
        // Update default if needed
        if ( isset( $data['is_default'] ) && $data['is_default'] ) {
            self::set_default_address( $user_id, $address_id, $type );
        }
        
        return $result !== false;
    }
    
    /**
     * Delete address
     */
    public static function delete_address( $user_id, $address_id, $type = 'billing' ) {
        global $wpdb;
        $table_name = ANA_Addresses_Database::get_table_name();
        
        // Can't delete if only one address
        $count = ANA_Addresses_Database::get_addresses_count( $user_id, $type );
        if ( $count <= 1 ) {
            return new WP_Error( 'last_address', 'Nu poți șterge ultima adresă.' );
        }
        
        // Check if this is default
        $address = self::get_address( $user_id, $address_id, $type );
        $was_default = $address && ! empty( $address['is_default'] );
        
        // Delete
        $result = $wpdb->delete(
            $table_name,
            [
                'id' => $address_id,
                'user_id' => $user_id,
                'address_type' => $type
            ],
            [ '%d', '%d', '%s' ]
        );
        
        // If deleted was default, set first remaining address as default
        if ( $was_default && $result ) {
            $remaining = self::get_addresses( $user_id, $type );
            if ( ! empty( $remaining ) ) {
                self::set_default_address( $user_id, $remaining[0]['id'], $type );
            }
        }
        
        return $result ? true : false;
    }
    
    /**
     * Set default address
     */
    public static function set_default_address( $user_id, $address_id, $type = 'billing' ) {
        global $wpdb;
        $table_name = ANA_Addresses_Database::get_table_name();
        
        // Unset all defaults for this user and type
        $wpdb->update(
            $table_name,
            [ 'is_default' => 0 ],
            [
                'user_id' => $user_id,
                'address_type' => $type
            ],
            [ '%d' ],
            [ '%d', '%s' ]
        );
        
        // Set new default
        $wpdb->update(
            $table_name,
            [ 'is_default' => 1 ],
            [
                'id' => $address_id,
                'user_id' => $user_id,
                'address_type' => $type
            ],
            [ '%d' ],
            [ '%d', '%d', '%s' ]
        );
        
        // Sync with WooCommerce default
        $address = self::get_address( $user_id, $address_id, $type );
        if ( $address ) {
            self::sync_with_woocommerce( $user_id, $address, $type );
        }
        
        return true;
    }
    
    /**
     * Get default address
     */
    public static function get_default_address( $user_id, $type = 'billing' ) {
        $addresses = self::get_addresses( $user_id, $type );
        
        foreach ( $addresses as $addr ) {
            if ( ! empty( $addr['is_default'] ) ) {
                return $addr;
            }
        }
        
        // Fallback to first address
        return ! empty( $addresses ) ? $addresses[0] : null;
    }
    
    /**
     * Sync address with WooCommerce default user meta
     */
    private static function sync_with_woocommerce( $user_id, $address, $type ) {
        $prefix = $type; // 'billing' or 'shipping'
        
        $wc_fields = [
            'first_name', 'last_name', 'company', 'address_1', 'address_2',
            'city', 'state', 'postcode', 'country', 'phone', 'email'
        ];
        
        foreach ( $wc_fields as $field ) {
            if ( isset( $address[$field] ) ) {
                update_user_meta( $user_id, $prefix . '_' . $field, $address[$field] );
            }
        }
        
        // Special: VAT number if PJ
        if ( ! empty( $address['vat_number'] ) ) {
            update_user_meta( $user_id, 'billing_vat_number', $address['vat_number'] );
        }
    }
    
    /**
     * Migrate WooCommerce default address to our system
     */
    private static function migrate_woocommerce_address( $user_id, $type ) {
        $prefix = $type;
        
        $address = [
            'first_name' => get_user_meta( $user_id, $prefix . '_first_name', true ),
            'last_name' => get_user_meta( $user_id, $prefix . '_last_name', true ),
            'company' => get_user_meta( $user_id, $prefix . '_company', true ),
            'address_1' => get_user_meta( $user_id, $prefix . '_address_1', true ),
            'address_2' => get_user_meta( $user_id, $prefix . '_address_2', true ),
            'city' => get_user_meta( $user_id, $prefix . '_city', true ),
            'state' => get_user_meta( $user_id, $prefix . '_state', true ),
            'postcode' => get_user_meta( $user_id, $prefix . '_postcode', true ),
            'country' => get_user_meta( $user_id, $prefix . '_country', true ),
            'phone' => get_user_meta( $user_id, $prefix . '_phone', true ),
            'email' => get_user_meta( $user_id, $prefix . '_email', true ),
            'is_default' => true,
            'created_at' => current_time( 'mysql' )
        ];
        
        // Determine entity type
        $entity_type = 'pf';
        if ( ! empty( $address['company'] ) ) {
            $entity_type = 'pj';
            $address['vat_number'] = get_user_meta( $user_id, 'billing_vat_number', true );
        }
        $address['entity_type'] = $entity_type;
        
        // Only migrate if at least some data exists
        if ( ! empty( $address['first_name'] ) || ! empty( $address['address_1'] ) ) {
            return [ $address ];
        }
        
        return [];
    }
}
