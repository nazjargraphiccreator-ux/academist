<?php
/**
 * Admin User Addresses Table - Shows both database and user meta addresses
 * 
 * @package ANA_Addresses
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class ANA_Addresses_Users_Table extends WP_List_Table {
    
    public function __construct() {
        parent::__construct( [
            'singular' => 'address',
            'plural'   => 'addresses',
            'ajax'     => true
        ] );
    }
    
    /**
     * Get columns
     */
    public function get_columns() {
        return [
            'cb'           => '<input type="checkbox" />',
            'user'         => 'Utilizator',
            'source'       => 'Sursă',
            'address_type' => 'Tip',
            'entity_type'  => 'Entitate',
            'address'      => 'Adresă',
            'phone'        => 'Telefon',
            'email'        => 'Email',
            'is_default'   => 'Implicit',
            'created_at'   => 'Creat',
            'actions'      => 'Acțiuni'
        ];
    }
    
    /**
     * Get sortable columns
     */
    public function get_sortable_columns() {
        return [
            'user'         => [ 'user_id', false ],
            'address_type' => [ 'address_type', false ],
            'entity_type'  => [ 'entity_type', false ],
            'created_at'   => [ 'created_at', true ]
        ];
    }
    
    /**
     * Get bulk actions
     */
    public function get_bulk_actions() {
        return [
            'delete'        => 'Șterge',
            'migrate'       => 'Migrează în Tabelă',
            'export'        => 'Exportă CSV'
        ];
    }
    
    /**
     * Column checkbox
     */
    public function column_cb( $item ) {
        return sprintf( '<input type="checkbox" name="address_ids[]" value="%s" />', $item['id'] );
    }
    
    /**
     * Column user
     */
    public function column_user( $item ) {
        $user = get_user_by( 'id', $item['user_id'] );
        if ( ! $user ) {
            return 'N/A';
        }
        
        $edit_url = admin_url( 'user-edit.php?user_id=' . $item['user_id'] );
        
        // Show company name prominently for PJ entities
        $display_name = esc_html( $user->display_name );
        if ( $item['entity_type'] === 'pj' && ! empty( $item['company'] ) ) {
            $display_name = '<span style="color: #f39c12; display: block; margin-bottom: 2px;">🏢 ' . esc_html( $item['company'] ) . '</span>' . $display_name;
        }
        
        return sprintf(
            '<strong><a href="%s">%s</a></strong><br><small style="color: #666;">%s</small><br><small style="color: #999;">ID: %d</small>',
            esc_url( $edit_url ),
            $display_name,
            esc_html( $user->user_email ),
            $item['user_id']
        );
    }
    
    /**
     * Column source
     */
    public function column_source( $item ) {
        if ( isset( $item['source'] ) && $item['source'] === 'user_meta' ) {
            return '<span class="ana-badge ana-badge-orange">User Meta</span>';
        }
        return '<span class="ana-badge ana-badge-blue">Database</span>';
    }
    
    /**
     * Column address type
     */
    public function column_address_type( $item ) {
        $types = [
            'billing'  => '<span class="ana-badge ana-badge-blue">Facturare</span>',
            'shipping' => '<span class="ana-badge ana-badge-green">Livrare</span>'
        ];
        return isset( $types[ $item['address_type'] ] ) ? $types[ $item['address_type'] ] : $item['address_type'];
    }
    
    /**
     * Column entity type
     */
    public function column_entity_type( $item ) {
        $types = [
            'pf' => '<span class="ana-badge ana-badge-gray">PF</span>',
            'pj' => '<span class="ana-badge ana-badge-orange">PJ</span>'
        ];
        return isset( $types[ $item['entity_type'] ] ) ? $types[ $item['entity_type'] ] : $item['entity_type'];
    }
    
    /**
     * Column address
     */
    public function column_address( $item ) {
        $name = trim( $item['first_name'] . ' ' . $item['last_name'] );
        
        $parts = [];
        
        // Show name in bold if exists
        if ( $name ) {
            $parts[] = '<strong>' . esc_html( $name ) . '</strong>';
        }
        
        // Show company in orange if PJ
        if ( ! empty( $item['company'] ) ) {
            $parts[] = '<span style="color: #f39c12;">' . esc_html( $item['company'] ) . '</span>';
        }
        
        // Add CUI if exists
        if ( ! empty( $item['vat_number'] ) ) {
            $parts[] = '<small>CUI: ' . esc_html( $item['vat_number'] ) . '</small>';
        }
        
        // Address details
        $address_parts = array_filter([
            $item['address_1'],
            $item['address_2'],
            $item['city'] . ( $item['state'] ? ', ' . $item['state'] : '' ) . ( $item['postcode'] ? ' ' . $item['postcode'] : '' ),
            $item['country'] !== 'RO' ? $item['country'] : ''
        ]);
        
        $parts = array_merge( $parts, $address_parts );
        
        return '<div class="ana-address-preview">' . implode( '<br>', $parts ) . '</div>';
    }
    
    /**
     * Column is_default
     */
    public function column_is_default( $item ) {
        if ( $item['is_default'] ) {
            return '<span class="dashicons dashicons-star-filled" style="color: #f39c12;"></span>';
        }
        return '—';
    }
    
    /**
     * Column created_at
     */
    public function column_created_at( $item ) {
        return $item['created_at'] ? date_i18n( 'd M Y', strtotime( $item['created_at'] ) ) : '—';
    }
    
    /**
     * Column actions
     */
    public function column_actions( $item ) {
        // Check if this is a user meta address
        $is_meta = isset( $item['source'] ) && $item['source'] === 'user_meta';
        
        if ( $is_meta ) {
            return '<span class="button button-small button-disabled">Doar Vizualizare</span>';
        }
        
        $edit_url = admin_url( 'admin.php?page=ana-addresses-manager&action=edit&address_id=' . $item['id'] );
        $delete_url = wp_nonce_url(
            admin_url( 'admin.php?page=ana-addresses-manager&action=delete&address_id=' . $item['id'] ),
            'delete_address_' . $item['id']
        );
        
        return sprintf(
            '<a href="%s" class="button button-small">Editează</a> 
             <a href="%s" class="button button-small ana-delete-address" onclick="return confirm(\'Sigur vrei să ștergi?\')">Șterge</a>',
            esc_url( $edit_url ),
            esc_url( $delete_url )
        );
    }
    
    /**
     * Prepare items - Combines database + user meta addresses
     */
    public function prepare_items() {
        global $wpdb;
        
        $per_page = 20;
        $current_page = $this->get_pagenum();
        
        $table_name = ANA_Addresses_Database::get_table_name();
        
        // Search
        $search = isset( $_REQUEST['s'] ) ? sanitize_text_field( $_REQUEST['s'] ) : '';
        
        // Filters
        $address_type = isset( $_REQUEST['filter_type'] ) ? sanitize_text_field( $_REQUEST['filter_type'] ) : '';
        $entity_type = isset( $_REQUEST['filter_entity'] ) ? sanitize_text_field( $_REQUEST['filter_entity'] ) : '';
        $county = isset( $_REQUEST['filter_county'] ) ? sanitize_text_field( $_REQUEST['filter_county'] ) : '';
        
        // Get addresses from database
        $db_addresses = $this->get_db_addresses( $search, $address_type, $entity_type, $county );
        
        // Get addresses from user meta (legacy)
        $meta_addresses = $this->get_user_meta_addresses( $search, $address_type, $entity_type, $county );
        
        // Combine both sources
        $all_addresses = array_merge( $db_addresses, $meta_addresses );
        
        // Apply intelligent sorting: Billing > Shipping, then PF > PJ, then by user
        $orderby = isset( $_REQUEST['orderby'] ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'address_type';
        $order = isset( $_REQUEST['order'] ) ? sanitize_text_field( $_REQUEST['order'] ) : 'ASC';
        
        usort( $all_addresses, function( $a, $b ) use ( $orderby, $order ) {
            // Primary sort: address_type (billing before shipping)
            $type_order = ['billing' => 1, 'shipping' => 2];
            $type_a = isset( $type_order[$a['address_type']] ) ? $type_order[$a['address_type']] : 99;
            $type_b = isset( $type_order[$b['address_type']] ) ? $type_order[$b['address_type']] : 99;
            
            if ( $type_a !== $type_b ) {
                return $type_a - $type_b;
            }
            
            // Secondary sort: entity_type (pf before pj)
            $entity_order = ['pf' => 1, 'pj' => 2];
            $entity_a = isset( $entity_order[$a['entity_type']] ) ? $entity_order[$a['entity_type']] : 99;
            $entity_b = isset( $entity_order[$b['entity_type']] ) ? $entity_order[$b['entity_type']] : 99;
            
            if ( $entity_a !== $entity_b ) {
                return $entity_a - $entity_b;
            }
            
            // Tertiary sort: by user_id
            if ( $a['user_id'] !== $b['user_id'] ) {
                return $a['user_id'] - $b['user_id'];
            }
            
            // Final sort: by custom orderby if specified
            if ( $orderby !== 'address_type' ) {
                $val_a = isset( $a[$orderby] ) ? $a[$orderby] : '';
                $val_b = isset( $b[$orderby] ) ? $b[$orderby] : '';
                
                $cmp = strcmp( $val_a, $val_b );
                return ( $order === 'ASC' ) ? $cmp : -$cmp;
            }
            
            return 0;
        });
        
        // Pagination
        $total_items = count( $all_addresses );
        $offset = ( $current_page - 1 ) * $per_page;
        $this->items = array_slice( $all_addresses, $offset, $per_page );
        
        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil( $total_items / $per_page )
        ]);
        
        $this->_column_headers = [
            $this->get_columns(),
            [],
            $this->get_sortable_columns()
        ];
    }
    
    /**
     * Get addresses from database table
     */
    private function get_db_addresses( $search, $address_type, $entity_type, $county = '' ) {
        global $wpdb;
        $table_name = ANA_Addresses_Database::get_table_name();
        
        $where = [ '1=1' ];
        $values = [];
        
        if ( $search ) {
            $where[] = '(first_name LIKE %s OR last_name LIKE %s OR company LIKE %s OR city LIKE %s OR email LIKE %s)';
            $search_term = '%' . $wpdb->esc_like( $search ) . '%';
            $values = array_merge( $values, array_fill( 0, 5, $search_term ) );
        }
        
        if ( $address_type ) {
            $where[] = 'address_type = %s';
            $values[] = $address_type;
        }
        
        if ( $entity_type ) {
            $where[] = 'entity_type = %s';
            $values[] = $entity_type;
        }

        if ( $county ) {
            $where[] = 'state = %s';
            $values[] = $county;
        }
        
        $where_sql = implode( ' AND ', $where );
        
        if ( ! empty( $values ) ) {
            $addresses = $wpdb->get_results(
                $wpdb->prepare( "SELECT *, 'db' as source FROM {$table_name} WHERE {$where_sql}", $values ),
                ARRAY_A
            );
        } else {
            $addresses = $wpdb->get_results(
                "SELECT *, 'db' as source FROM {$table_name} WHERE {$where_sql}",
                ARRAY_A
            );
        }
        
        return $addresses ? $addresses : [];
    }
    
    /**
     * Get addresses from user meta (legacy WooCommerce)
     */
    private function get_user_meta_addresses( $search, $address_type, $entity_type, $county = '' ) {
        $users = get_users( [ 'fields' => [ 'ID', 'user_email', 'display_name', 'user_registered' ] ] );
        $addresses = [];
        
        foreach ( $users as $user ) {
            // Get billing address
            if ( ! $address_type || $address_type === 'billing' ) {
                $billing = $this->extract_user_meta_address( $user->ID, 'billing', $user->user_registered );
                if ( $billing && $this->matches_search( $billing, $search ) ) {
                    $addresses[] = $billing;
                }
            }
            
            // Get shipping address
            if ( ! $address_type || $address_type === 'shipping' ) {
                $shipping = $this->extract_user_meta_address( $user->ID, 'shipping', $user->user_registered );
                if ( $shipping && $this->matches_search( $shipping, $search ) ) {
                    $addresses[] = $shipping;
                }
            }
        }
        
        // Apply entity type filter
        if ( $entity_type ) {
            $addresses = array_filter( $addresses, function( $addr ) use ( $entity_type ) {
                return $addr['entity_type'] === $entity_type;
            });
        }

        // Apply county filter
        if ( $county ) {
            $addresses = array_filter( $addresses, function( $addr ) use ( $county ) {
                return isset( $addr['state'] ) && $addr['state'] === $county;
            });
        }
        
        return $addresses;
    }
    
    /**
     * Extract address from user meta
     */
    private function extract_user_meta_address( $user_id, $type, $user_registered ) {
        $first_name = get_user_meta( $user_id, $type . '_first_name', true );
        $last_name = get_user_meta( $user_id, $type . '_last_name', true );
        $address_1 = get_user_meta( $user_id, $type . '_address_1', true );
        
        // Only return if at least some address data exists
        if ( ! $first_name && ! $last_name && ! $address_1 ) {
            return null;
        }
        
        return [
            'id' => 'meta_' . $user_id . '_' . $type,
            'user_id' => $user_id,
            'address_type' => $type,
            'entity_type' => get_user_meta( $user_id, $type . '_company', true ) ? 'pj' : 'pf',
            'first_name' => $first_name,
            'last_name' => $last_name,
            'company' => get_user_meta( $user_id, $type . '_company', true ),
            'vat_number' => '',
            'address_1' => $address_1,
            'address_2' => get_user_meta( $user_id, $type . '_address_2', true ),
            'city' => get_user_meta( $user_id, $type . '_city', true ),
            'state' => get_user_meta( $user_id, $type . '_state', true ),
            'postcode' => get_user_meta( $user_id, $type . '_postcode', true ),
            'country' => get_user_meta( $user_id, $type . '_country', true ) ?: 'RO',
            'phone' => get_user_meta( $user_id, $type . '_phone', true ),
            'email' => get_user_meta( $user_id, $type . '_email', true ),
            'is_default' => 1,
            'created_at' => $user_registered,
            'updated_at' => null,
            'source' => 'user_meta'
        ];
    }
    
    /**
     * Check if address matches search query
     */
    private function matches_search( $address, $search ) {
        if ( ! $search ) {
            return true;
        }
        
        $search = strtolower( $search );
        $fields = [ 'first_name', 'last_name', 'company', 'city', 'email' ];
        
        foreach ( $fields as $field ) {
            if ( isset( $address[$field] ) && stripos( $address[$field], $search ) !== false ) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get available counties from DB for filter
     */
    private function get_available_counties() {
        global $wpdb;
        $table_name = ANA_Addresses_Database::get_table_name();
        
        $results = $wpdb->get_col( "SELECT DISTINCT state FROM {$table_name} WHERE state != '' ORDER BY state ASC" );
        return $results ? $results : [];
    }

    /**
     * Display table navigation with filters
     */
    // extra_tablenav removed - filters moved to main page GET form
}
