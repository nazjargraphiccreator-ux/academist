<?php
/**
 * Modern Address Viewer - Visual Cards Display
 * 
 * Displays all user addresses (database + user meta) in modern card layout
 * 
 * @package ANA_Addresses
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ANA_Modern_Address_Viewer {
    
    /**
     * Render all addresses grouped by user
     */
    public static function render() {
        global $wpdb;
        $table_name = ANA_Addresses_Database::get_table_name();
        
        // Get all users
        $users = get_users( [ 'fields' => 'all', 'orderby' => 'display_name' ] );
        
        // Collect all addresses per user
        $users_addresses = [];
        
        foreach ( $users as $user ) {
            $user_id = $user->ID;
            $user_addresses = [];
            
            // AUTO-MIGRATE: Check and migrate Ana Saved Addresses transparently
            // This runs ONCE per user (checked by _ana_addresses_migrated flag)
            ANA_Addresses_Database::auto_migrate_user_saved_addresses( $user_id );
            
            // 1. FIRST: Get addresses from USER META (WooCommerce default billing/shipping)
            // These are the primary addresses set in My Account - ALWAYS PF
            $billing_meta = self::get_meta_address( $user_id, 'billing' );
            if ( $billing_meta ) {
                $user_addresses[] = $billing_meta;
            }
            
            $shipping_meta = self::get_meta_address( $user_id, 'shipping' );
            if ( $shipping_meta ) {
                $user_addresses[] = $shipping_meta;
            }
            
            // 2. THEN: Get supplementary addresses from DATABASE (migrated from ana_saved_addresses + _ana_companies)
            // These are additional addresses created by user
            if ( ANA_Addresses_Database::table_exists() ) {
                $db_addresses = $wpdb->get_results( $wpdb->prepare(
                    "SELECT * FROM {$table_name} WHERE user_id = %d ORDER BY is_default DESC, created_at DESC",
                    $user_id
                ), ARRAY_A );
                
                foreach ( $db_addresses as $addr ) {
                    $addr['source'] = 'db';
                    $user_addresses[] = $addr;
                }
            }
            
            // 3. ALSO: Get companies from dedicated wpu3_ana_companies table
            $companies = ANA_Companies_Integration::get_user_companies_as_addresses( $user_id, 'billing' );
            foreach ( $companies as $company ) {
                $company['source'] = 'companies_table';
                $user_addresses[] = $company;
            }
            
            // Only add users who have addresses
            if ( ! empty( $user_addresses ) ) {
                $users_addresses[] = [
                    'user' => $user,
                    'addresses' => $user_addresses
                ];
            }
        }
        
        // If no addresses found
        if ( empty( $users_addresses ) ) {
            return self::render_empty_state();
        }
        
        
        // Render addresses grouped by user
        ob_start();
        ?>
        
        <!-- Filter Tabs -->
        <div class="ana-filter-tabs">
            <button class="ana-filter-tab active" data-filter="all">
                <span class="dashicons dashicons-admin-site"></span> Toate
            </button>
            <button class="ana-filter-tab" data-filter="pf">
                <span class="dashicons dashicons-admin-users"></span> Persoane Fizice
            </button>
            <button class="ana-filter-tab" data-filter="pj">
                <span class="dashicons dashicons-building"></span> Persoane Juridice
            </button>
            <button class="ana-filter-tab" data-filter="billing">
                <span class="dashicons dashicons-money-alt"></span> Facturare
            </button>
            <button class="ana-filter-tab" data-filter="shipping">
                <span class="dashicons dashicons-cart"></span> Livrare
            </button>
        </div>
        
        <div class="ana-addresses-container">
            <?php foreach ( $users_addresses as $user_data ) : 
                echo self::render_user_section( $user_data );
            endforeach; ?>
        </div>
        
        <?php
        return ob_get_clean();
    }
    
    /**
     * Get address from user meta (WooCommerce default)
     * IMPORTANT: WooCommerce default addresses are ALWAYS PF (Persoane Fizice)
     * Only addresses from _ana_companies meta are PJ
     */
    private static function get_meta_address( $user_id, $type ) {
        $first_name = get_user_meta( $user_id, "{$type}_first_name", true );
        $last_name = get_user_meta( $user_id, "{$type}_last_name", true );
        $company = get_user_meta( $user_id, "{$type}_company", true );
        $address_1 = get_user_meta( $user_id, "{$type}_address_1", true );
        $address_2 = get_user_meta( $user_id, "{$type}_address_2", true );
        $city = get_user_meta( $user_id, "{$type}_city", true );
        $state = get_user_meta( $user_id, "{$type}_state", true );
        $postcode = get_user_meta( $user_id, "{$type}_postcode", true );
        $country = get_user_meta( $user_id, "{$type}_country", true );
        $phone = get_user_meta( $user_id, "{$type}_phone", true );
        $email = get_user_meta( $user_id, "{$type}_email", true );
        
        // Skip if no address data
        if ( empty( $address_1 ) && empty( $city ) ) {
            return null;
        }
        
        return [
            'address_type' => $type,
            'entity_type' => 'pf', // WooCommerce default addresses are ALWAYS PF!
            'first_name' => $first_name,
            'last_name' => $last_name,
            'company' => $company,
            'vat_number' => '',
            'address_1' => $address_1,
            'address_2' => $address_2,
            'city' => $city,
            'state' => $state,
            'postcode' => $postcode,
            'country' => $country,
            'phone' => $phone,
            'email' => $email,
            'source' => 'meta',
            'is_default' => 1
        ];
    }
    
    /**
     * Render empty state
     */
    private static function render_empty_state() {
        return '<div class="ana-empty-state">
            <span class="dashicons dashicons-location"></span>
            <h3>Nicio adresă găsită</h3>
            <p>Nu există adrese salvate în sistem. Adresele vor apărea aici când utilizatorii le adaugă.</p>
        </div>';
    }
    
    /**
     * Render user section with addresses
     */
    private static function render_user_section( $user_data ) {
        $user = $user_data['user'];
        $addresses = $user_data['addresses'];
        
        ob_start();
        ?>
        <div class="ana-user-section">
            <div class="ana-user-header">
                <div class="ana-user-info">
                    <?php echo get_avatar( $user->ID, 48, '', '', [ 'class' => 'ana-user-avatar' ] ); ?>
                    <div class="ana-user-details">
                        <h3><?php echo esc_html( $user->display_name ); ?></h3>
                        <span class="user-email"><?php echo esc_html( $user->user_email ); ?></span>
                    </div>
                </div>
                <div class="ana-user-stats">
                    <div class="ana-user-stat">
                        <div class="ana-user-stat-number"><?php echo count( $addresses ); ?></div>
                        <div class="ana-user-stat-label">Adrese</div>
                    </div>
                </div>
            </div>
            
            <div class="ana-addresses-grid">
                <?php foreach ( $addresses as $addr ) : 
                    echo self::render_address_card( $addr );
                endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render individual address card
     */
    private static function render_address_card( $addr ) {
        $type = isset( $addr['address_type'] ) ? $addr['address_type'] : 'billing';
        $entity = isset( $addr['entity_type'] ) ? $addr['entity_type'] : 'pf';
        $source = isset( $addr['source'] ) ? $addr['source'] : 'db';
        $is_company = $entity === 'pj';
        
        // Determine card class
        $card_class = $is_company ? 'company' : $type;
        if ( $source === 'meta' ) {
            $card_class .= ' meta';
        }
        
        
        ob_start();
        ?>
        <div class="ana-address-card <?php echo esc_attr( $card_class ); ?>" 
             data-entity="<?php echo esc_attr( $entity ); ?>" 
             data-type="<?php echo esc_attr( $type ); ?>">
            <div class="ana-source-indicator <?php echo esc_attr( $source ); ?>" 
                 title="<?php echo $source === 'db' ? 'Database' : 'User Meta'; ?>"></div>
            
            <div class="ana-card-header">
                <div class="ana-card-type">
                    <span class="dashicons dashicons-<?php echo $type === 'billing' ? 'money-alt' : 'cart'; ?>"></span>
                    <?php echo $type === 'billing' ? 'FACTURARE' : 'LIVRARE'; ?>
                </div>
                <div class="ana-card-badges">
                    <?php if ( ! empty( $addr['is_default'] ) ) : ?>
                        <span class="ana-badge default">DEFAULT</span>
                    <?php endif; ?>
                    <span class="ana-badge <?php echo esc_attr( $entity ); ?>">
                        <?php echo $entity === 'pf' ? 'PF' : 'PJ'; ?>
                    </span>
                    <span class="ana-badge <?php echo esc_attr( $source ); ?>">
                        <?php echo $source === 'db' ? 'DB' : 'META'; ?>
                    </span>
                </div>
            </div>
            
            <div class="ana-card-content">
                <?php if ( $is_company && ! empty( $addr['company'] ) ) : ?>
                    <div class="ana-company-name">
                        <span class="dashicons dashicons-building"></span>
                        <?php echo esc_html( $addr['company'] ); ?>
                    </div>
                    <?php if ( ! empty( $addr['vat_number'] ) ) : ?>
                        <div class="ana-vat-number">
                            CIF: <?php echo esc_html( $addr['vat_number'] ); ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="ana-address-line">
                    <span class="dashicons dashicons-admin-users"></span>
                    <strong><?php echo esc_html( trim( ( $addr['first_name'] ?? '' ) . ' ' . ( $addr['last_name'] ?? '' ) ) ); ?></strong>
                </div>
                
                <?php if ( ! empty( $addr['address_1'] ) ) : ?>
                    <div class="ana-address-line">
                        <span class="dashicons dashicons-location"></span>
                        <span><?php echo esc_html( $addr['address_1'] ); ?>
                            <?php if ( ! empty( $addr['address_2'] ) ) : ?>
                                <strong><?php echo esc_html( $addr['address_2'] ); ?></strong>
                            <?php endif; ?>
                        </span>
                    </div>
                <?php endif; ?>
                
                <?php if ( ! empty( $addr['city'] ) || ! empty( $addr['state'] ) || ! empty( $addr['postcode'] ) ) : 
                    $state_name = '';
                    if ( ! empty( $addr['state'] ) && function_exists( 'WC' ) ) {
                        $states = WC()->countries->get_states( 'RO' );
                        $state_name = isset( $states[ $addr['state'] ] ) ? $states[ $addr['state'] ] : $addr['state'];
                    }
                ?>
                    <div class="ana-address-line">
                        <span class="dashicons dashicons-admin-site-alt3"></span>
                        <span>
                            <?php 
                            $location_parts = [];
                            if ( ! empty( $addr['city'] ) ) {
                                $location_parts[] = esc_html( $addr['city'] );
                            }
                            if ( ! empty( $state_name ) ) {
                                $location_parts[] = '<strong>jud. ' . esc_html( $state_name ) . '</strong>';
                            }
                            if ( ! empty( $addr['postcode'] ) ) {
                                $location_parts[] = 'CP ' . esc_html( $addr['postcode'] );
                            }
                            echo implode( ', ', $location_parts );
                            ?>
                        </span>
                    </div>
                <?php endif; ?>
                
                <?php if ( ! empty( $addr['phone'] ) ) : ?>
                    <div class="ana-address-line">
                        <span class="dashicons dashicons-phone"></span>
                        <span><?php echo esc_html( $addr['phone'] ); ?></span>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if ( $source === 'db' && isset( $addr['id'] ) ) : ?>
                <div class="ana-card-footer">
                    <div class="ana-card-meta">
                        <?php if ( ! empty( $addr['created_at'] ) ) : ?>
                            <small>Adăugată: <?php echo esc_html( date( 'd.m.Y', strtotime( $addr['created_at'] ) ) ); ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="ana-card-actions">
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=ana-addresses-manager&action=edit&address_id=' . $addr['id'] ) ); ?>" 
                           class="ana-card-action-btn">
                            <span class="dashicons dashicons-edit"></span> Edit
                        </a>
                    </div>
                </div>
            <?php else : ?>
                <div class="ana-card-footer">
                    <small>📦 WooCommerce Default (User Meta)</small>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
