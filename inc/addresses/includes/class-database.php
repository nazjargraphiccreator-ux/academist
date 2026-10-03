<?php
/**
 * Database Management for ANA Addresses
 * 
 * Handles table creation, schema updates, and database utilities
 * 
 * @package ANA_Addresses
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ANA_Addresses_Database {
    
    /**
     * Database version
     */
    const DB_VERSION = '1.1.0';
    
    /**
     * Get table name with custom prefix support
     */
    public static function get_table_name() {
        global $wpdb;
        
        // Get custom prefix from options, fallback to WordPress prefix
        $custom_prefix = get_option( 'ana_addresses_db_prefix', $wpdb->prefix );
        
        return $custom_prefix . 'ana_user_addresses';
    }
    
    /**
     * Set custom table prefix
     */
    public static function set_custom_prefix( $prefix ) {
        return update_option( 'ana_addresses_db_prefix', $prefix );
    }
    
    /**
     * Get custom table prefix
     */
    public static function get_custom_prefix() {
        global $wpdb;
        return get_option( 'ana_addresses_db_prefix', $wpdb->prefix );
    }
    
    /**
     * Check if table exists
     */
    public static function table_exists() {
        global $wpdb;
        $table_name = self::get_table_name();
        $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name );
        return $wpdb->get_var( $query ) === $table_name;
    }
    
    /**
     * Create addresses table
     */
    public static function create_table() {
        global $wpdb;
        
        $table_name = self::get_table_name();
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            address_type ENUM('billing', 'shipping') NOT NULL,
            entity_type ENUM('pf', 'pj') NOT NULL DEFAULT 'pf',
            
            first_name VARCHAR(100) DEFAULT '',
            last_name VARCHAR(100) DEFAULT '',
            company VARCHAR(200) DEFAULT '',
            vat_number VARCHAR(50) DEFAULT '',
            reg_com VARCHAR(50) DEFAULT '',
            bank VARCHAR(100) DEFAULT '',
            iban VARCHAR(50) DEFAULT '',
            
            address_1 VARCHAR(255) DEFAULT '',
            address_2 VARCHAR(255) DEFAULT '',
            city VARCHAR(100) DEFAULT '',
            state VARCHAR(100) DEFAULT '',
            postcode VARCHAR(20) DEFAULT '',
            country CHAR(2) DEFAULT 'RO',
            
            phone VARCHAR(30) DEFAULT '',
            email VARCHAR(100) DEFAULT '',
            
            is_default TINYINT(1) DEFAULT 0,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY address_type (address_type),
            KEY entity_type (entity_type),
            KEY is_default (is_default),
            KEY user_type (user_id, address_type)
        ) $charset_collate ENGINE=InnoDB;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        
        // Store database version
        update_option( 'ana_addresses_db_version', self::DB_VERSION );
        
        return true;
    }
    
    /**
     * Check and update database if needed
     */
    public static function maybe_update() {
        $current_version = get_option( 'ana_addresses_db_version', '0' );
        
        if ( version_compare( $current_version, self::DB_VERSION, '<' ) ) {
            self::create_table();
        }
    }
    
    /**
     * Get addresses count for a user
     */
    public static function get_addresses_count( $user_id, $type = null ) {
        global $wpdb;
        $table_name = self::get_table_name();
        
        if ( $type ) {
            $count = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name} WHERE user_id = %d AND address_type = %s",
                $user_id,
                $type
            ) );
        } else {
            $count = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name} WHERE user_id = %d",
                $user_id
            ) );
        }
        
        return (int) $count;
    }
    
    /**
     * Get total addresses count (all users) + COMPANIES
     * Includes companies from wpu3_ana_companies when entity='pj'
     * 
     * @param string $type Optional: 'billing' or 'shipping'
     * @param string $entity Optional: 'pf' or 'pj'
     * @return int
     */
    public static function get_total_count( $type = null, $entity = null ) {
        global $wpdb;
        $table_name = self::get_table_name();
        
        $where = [];
        $params = [];
        
        if ( $type ) {
            $where[] = "address_type = %s";
            $params[] = $type;
        }
        
        if ( $entity ) {
            $where[] = "entity_type = %s";
            $params[] = $entity;
        }
        
        $where_clause = ! empty( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : '';
        $query = "SELECT COUNT(*) FROM {$table_name} {$where_clause}";
        
        if ( ! empty( $params ) ) {
            $query = $wpdb->prepare( $query, $params );
        }
        
        $addresses_count = (int) $wpdb->get_var( $query );
        
        // If counting PJ entities, add companies from dedicated table
        if ( $entity === 'pj' ) {
            $companies_count = ANA_Companies_Integration::get_total_companies_count();
            return $addresses_count + $companies_count;
        }
        
        return $addresses_count;
    }
    
    /**
     * Get statistics by entity type (HYBRID - combines DB + user meta + COMPANIES)
     * 
     * @return array
     */
    public static function get_entity_stats() {
        global $wpdb;
        $table_name = self::get_table_name();
        
        // Initialize stats
        $stats = [
            'pf' => [ 'billing' => 0, 'shipping' => 0, 'total' => 0 ],
            'pj' => [ 'billing' => 0, 'shipping' => 0, 'total' => 0 ],
            'total' => [ 'billing' => 0, 'shipping' => 0, 'total' => 0 ]
        ];
        
        // Get stats from database table if it exists
        if ( self::table_exists() ) {
            $results = $wpdb->get_results(
                "SELECT 
                    entity_type,
                    address_type,
                    COUNT(*) as count
                FROM {$table_name}
                GROUP BY entity_type, address_type",
                ARRAY_A
            );
            
            foreach ( $results as $row ) {
                $entity = $row['entity_type'];
                $type = $row['address_type'];
                $count = (int) $row['count'];
                
                $stats[ $entity ][ $type ] += $count;
                $stats[ $entity ]['total'] += $count;
                $stats['total'][ $type ] += $count;
                $stats['total']['total'] += $count;
            }
        }
        
        // HYBRID: Also count from user meta (WooCommerce legacy addresses)
        $all_users = get_users( [ 'fields' => 'ID' ] );
        
        foreach ( $all_users as $user_id ) {
            // Check billing address from user meta
            $billing_address_1 = get_user_meta( $user_id, 'billing_address_1', true );
            if ( ! empty( $billing_address_1 ) ) {
                $billing_company = get_user_meta( $user_id, 'billing_company', true );
                
                // Determine entity type (PF or PJ based on company field)
                $entity = ! empty( $billing_company ) ? 'pj' : 'pf';
                
                $stats[ $entity ]['billing']++;
                $stats[ $entity ]['total']++;
                $stats['total']['billing']++;
                $stats['total']['total']++;
            }
            
            // Check shipping address from user meta
            $shipping_address_1 = get_user_meta( $user_id, 'shipping_address_1', true );
            if ( ! empty( $shipping_address_1 ) ) {
                $shipping_company = get_user_meta( $user_id, 'shipping_company', true );
                
                // Determine entity type
                $entity = ! empty( $shipping_company ) ? 'pj' : 'pf';
                
                $stats[ $entity ]['shipping']++;
                $stats[ $entity ]['total']++;
                $stats['total']['shipping']++;
                $stats['total']['total']++;
            }
        }
        
        // ALSO: Add companies from dedicated wpu3_ana_companies table
        $companies_count = ANA_Companies_Integration::get_total_companies_count();
        if ( $companies_count > 0 ) {
            // Companies are always billing addresses and PJ
            $stats['pj']['billing'] += $companies_count;
            $stats['pj']['total'] += $companies_count;
            $stats['total']['billing'] += $companies_count;
            $stats['total']['total'] += $companies_count;
        }
        
        
        return $stats;
    }
    
    /**
     * Auto-migrate addresses from ana_saved_addresses user meta to database table
     * This runs transparently when addresses are read
     * 
     * @param int $user_id User ID to migrate addresses for
     * @return array Migration results ['migrated' => int, 'skipped' => int, 'errors' => int]
     */
    public static function auto_migrate_user_saved_addresses( $user_id ) {
        global $wpdb;
        $table_name = self::get_table_name();
        
        // Check if already migrated for this user - DO THIS FIRST!
        $migrated_flag = get_user_meta( $user_id, '_ana_addresses_migrated', true );
        $companies_migrated_flag = get_user_meta( $user_id, '_ana_user_companies_migrated', true );
        
        // Get saved addresses from user meta (PF addresses)
        $saved_addresses = [];
        if ( $migrated_flag !== 'yes' ) {
            $saved_addresses = get_user_meta( $user_id, 'ana_saved_addresses', true );
            if ( ! is_array( $saved_addresses ) ) {
                $saved_addresses = [];
            }
        }
        
        // ALSO get companies from _ana_companies meta (PJ addresses)
        $saved_companies = [];
        if ( $migrated_flag !== 'yes' ) {
            $saved_companies = get_user_meta( $user_id, '_ana_companies', true );
            if ( ! is_array( $saved_companies ) ) {
                $saved_companies = [];
            }
        }
        
        // ALSO get companies from ana_user_companies meta (PJ addresses from "Firmele mele" My Account)
        $user_companies = [];
        if ( $companies_migrated_flag !== 'yes' ) {
            $user_companies = get_user_meta( $user_id, 'ana_user_companies', true );
            if ( ! is_array( $user_companies ) ) {
                $user_companies = [];
            }
        }
        
        // Convert ana_user_companies format to standard address format
        $converted_companies = [];
        foreach ( $user_companies as $company ) {
            // Map company fields to address format
            $converted_companies[] = [
                'type' => 'billing', // Companies are always billing addresses
                'company' => $company['name'] ?? '',
                'cui' => $company['cui'] ?? '',
                'address_1' => trim( ( $company['street'] ?? '' ) . ' ' . ( $company['number'] ?? '' ) ),
                'city' => $company['city'] ?? '',
                'state' => $company['state'] ?? '',
                'postcode' => $company['postcode'] ?? '',
                'phone' => $company['phone'] ?? '',
                'email' => $company['email'] ?? '',
                'first_name' => '', // Will be extracted from rep_name below
                'last_name' => '', // Will be extracted from rep_name below
                'rep_name' => $company['rep_name'] ?? '', // Keep for processing
                'entity_type' => 'pj' // Always PJ
            ];
        }
        
        // Combine all arrays for migration
        $all_addresses = array_merge( $saved_addresses, $saved_companies, $converted_companies );
        
        if ( empty( $all_addresses ) ) {
            // No addresses to migrate, mark as done anyway to prevent re-checking
            if ( $migrated_flag !== 'yes' ) {
                update_user_meta( $user_id, '_ana_addresses_migrated', 'yes' );
            }
            if ( $companies_migrated_flag !== 'yes' ) {
                update_user_meta( $user_id, '_ana_user_companies_migrated', 'yes' );
            }
            return [ 'migrated' => 0, 'skipped' => 0, 'errors' => 0 ];
        }
        
        $results = [
            'migrated' => 0,
            'skipped' => 0,
            'errors' => 0
        ];
        
        foreach ( $all_addresses as $address ) {
            // Skip if missing required fields
            if ( empty( $address['type'] ) || empty( $address['address_1'] ) ) {
                $results['skipped']++;
                continue;
            }
            
            // Map fields from ana_saved_addresses format to database table format
            $address_type = $address['type']; // 'billing' or 'shipping'
            
            // Determine entity type (PF or PJ based on company field)
            // Companies from _ana_companies always have company name
            $entity_type = ! empty( $address['company'] ) ? 'pj' : 'pf';
            
            // Extract VAT number if it exists (companies have 'vat' or 'cui' field)
            $vat_number = '';
            if ( isset( $address['vat'] ) ) {
                $vat_number = $address['vat'];
            } elseif ( isset( $address['cui'] ) ) {
                $vat_number = $address['cui'];
            }
            
            // Check if similar address already exists in database
            // For PJ (companies): check by company name + type
            // For PF (persons): check by address_1 + city + type
            if ( $entity_type === 'pj' && ! empty( $address['company'] ) ) {
                // For companies, check by company name and type to prevent duplicates
                $exists = $wpdb->get_var( $wpdb->prepare(
                    "SELECT id FROM {$table_name} 
                    WHERE user_id = %d 
                    AND address_type = %s 
                    AND company = %s
                    AND entity_type = 'pj'
                    LIMIT 1",
                    $user_id,
                    $address_type,
                    $address['company']
                ) );
            } else {
                // For persons, check by address + city + type
                $exists = $wpdb->get_var( $wpdb->prepare(
                    "SELECT id FROM {$table_name} 
                    WHERE user_id = %d 
                    AND address_type = %s 
                    AND address_1 = %s 
                    AND city = %s
                    LIMIT 1",
                    $user_id,
                    $address_type,
                    $address['address_1'] ?? '',
                    $address['city'] ?? ''
                ) );
            }
            
            if ( $exists ) {
                $results['skipped']++;
                continue;
            }
            
            // Prepare data for insertion
            $insert_data = [
                'user_id' => $user_id,
                'address_type' => $address_type,
                'entity_type' => $entity_type,
                'first_name' => $address['first_name'] ?? '',
                'last_name' => $address['last_name'] ?? '',
                'company' => $address['company'] ?? '',
                'vat_number' => $vat_number,
                'address_1' => $address['address_1'] ?? '',
                'address_2' => $address['address_2'] ?? '',
                'city' => $address['city'] ?? '',
                'state' => $address['state'] ?? '',
                'postcode' => $address['postcode'] ?? '',
                'country' => $address['country'] ?? 'RO',
                'phone' => $address['phone'] ?? '',
                'email' => $address['email'] ?? '',
                'is_default' => ! empty( $address['is_default'] ) ? 1 : 0,
                'created_at' => $address['created_at'] ?? current_time( 'mysql' ),
                'updated_at' => $address['updated_at'] ?? null
            ];
            
            // Special handling for rep_name from ana_user_companies
            if ( ! empty( $address['rep_name'] ) && empty( $insert_data['first_name'] ) && empty( $insert_data['last_name'] ) ) {
                // Split rep_name into first and last name
                $name_parts = explode( ' ', trim( $address['rep_name'] ), 2 );
                $insert_data['first_name'] = $name_parts[0] ?? '';
                $insert_data['last_name'] = $name_parts[1] ?? '';
            }
            
            $insert_types = [
                '%d', // user_id
                '%s', // address_type
                '%s', // entity_type
                '%s', // first_name
                '%s', // last_name
                '%s', // company
                '%s', // vat_number
                '%s', // address_1
                '%s', // address_2
                '%s', // city
                '%s', // state
                '%s', // postcode
                '%s', // country
                '%s', // phone
                '%s', // email
                '%d', // is_default
                '%s', // created_at
                '%s'  // updated_at
            ];
            
            // Insert into database
            $inserted = $wpdb->insert( $table_name, $insert_data, $insert_types );
            
            if ( $inserted ) {
                $results['migrated']++;
            } else {
                $results['errors']++;
            }
        }
        
        // Mark this user as migrated
        if ( $migrated_flag !== 'yes' && ( ! empty( $saved_addresses ) || ! empty( $saved_companies ) ) ) {
            update_user_meta( $user_id, '_ana_addresses_migrated', 'yes' );
        }
        
        if ( $companies_migrated_flag !== 'yes' && ! empty( $user_companies ) ) {
            update_user_meta( $user_id, '_ana_user_companies_migrated', 'yes' );
        }
        
        return $results;
    }
    
    /**
     * Get user statistics (HYBRID - combines DB + user meta)
     * 
     * @return array
     */
    public static function get_user_stats() {
        global $wpdb;
        $table_name = self::get_table_name();
        
        $users_with_addresses = [];
        
        // Get users from database table if it exists
        if ( self::table_exists() ) {
            $db_users = $wpdb->get_col( "SELECT DISTINCT user_id FROM {$table_name}" );
            foreach ( $db_users as $user_id ) {
                $users_with_addresses[ $user_id ] = true;
            }
        }
        
        // HYBRID: Also get users from user meta (WooCommerce legacy)
        $all_users = get_users( [ 'fields' => 'ID' ] );
        foreach ( $all_users as $user_id ) {
            $has_billing = get_user_meta( $user_id, 'billing_address_1', true );
            $has_shipping = get_user_meta( $user_id, 'shipping_address_1', true );
            
            if ( ! empty( $has_billing ) || ! empty( $has_shipping ) ) {
                $users_with_addresses[ $user_id ] = true;
            }
        }
        
        // Total WordPress users
        $total_users = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->users}"
        );
        
        // Calculate average (approximate)
        $total_address_count = 0;
        $user_count = count( $users_with_addresses );
        
        if ( $user_count > 0 ) {
            // Count from DB
            if ( self::table_exists() ) {
                $db_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name}" );
                $total_address_count += $db_count;
            }
            
            // Count from user meta  
            foreach ( $all_users as $user_id ) {
                if ( get_user_meta( $user_id, 'billing_address_1', true ) ) {
                    $total_address_count++;
                }
                if ( get_user_meta( $user_id, 'shipping_address_1', true ) ) {
                    $total_address_count++;
                }
            }
            
            $avg = $total_address_count / $user_count;
        } else {
            $avg = 0;
        }
        
        return [
            'users_with_addresses' => $user_count,
            'total_users' => (int) $total_users,
            'avg_addresses_per_user' => round( $avg, 2 )
        ];
    }
    
    /**
     * Get recent addresses
     * 
     * @param int $limit
     * @return array
     */
    public static function get_recent_addresses( $limit = 10 ) {
        global $wpdb;
        $table_name = self::get_table_name();
        
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT a.*, u.display_name, u.user_email
                FROM {$table_name} a
                LEFT JOIN {$wpdb->users} u ON a.user_id = u.ID
                ORDER BY a.created_at DESC
                LIMIT %d",
                $limit
            ),
            ARRAY_A
        );
        
        return $results;
    }
    
    /**
     * Get top users by address count
     * 
     * @param int $limit
     * @return array
     */
    public static function get_top_users( $limit = 10 ) {
        global $wpdb;
        $table_name = self::get_table_name();
        
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT 
                    a.user_id,
                    u.display_name,
                    u.user_email,
                    COUNT(*) as address_count,
                    SUM(CASE WHEN a.entity_type = 'pf' THEN 1 ELSE 0 END) as pf_count,
                    SUM(CASE WHEN a.entity_type = 'pj' THEN 1 ELSE 0 END) as pj_count
                FROM {$table_name} a
                LEFT JOIN {$wpdb->users} u ON a.user_id = u.ID
                GROUP BY a.user_id
                ORDER BY address_count DESC
                LIMIT %d",
                $limit
            ),
            ARRAY_A
        );
        
        return $results;
    }
}
