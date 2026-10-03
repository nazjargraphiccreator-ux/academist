<?php
/**
 * Admin Page for Multiple Addresses Manager
 * 
 * @package ANA_Addresses
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ANA_Addresses_Admin_Page {
    
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
        add_action( 'admin_post_ana_create_table', [ $this, 'handle_create_table' ] );
        add_action( 'admin_post_ana_bulk_action', [ $this, 'handle_bulk_action' ] );
        add_action( 'admin_post_ana_save_address', [ $this, 'handle_save_address' ] );
        add_action( 'admin_post_ana_delete_address', [ $this, 'handle_delete_address' ] );
        add_action( 'wp_ajax_ana_search_users', [ $this, 'ajax_search_users' ] );
        add_action( 'admin_notices', [ $this, 'show_migration_progress' ] );
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets() {
        wp_enqueue_style( 'ana-admin-style', ANA_ADDR_PLUGIN_URL . 'assets/css/admin-style.css', [], '1.0.0' );
    }

    /**
     * Render Dashboard Widgets
     */
    private function render_dashboard_widgets() {
        global $wpdb;
        $table_name = ANA_Addresses_Database::get_table_name();
        
        // Stats
        $total_addresses = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name}" );
        $total_pf = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name} WHERE entity_type = 'pf'" );
        $total_pj = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name} WHERE entity_type = 'pj'" );
        
        // Top Counties
        $top_counties = $wpdb->get_results( "SELECT state, COUNT(*) as count FROM {$table_name} WHERE state != '' GROUP BY state ORDER BY count DESC LIMIT 5" );
        
        ?>
        <div class="ana-dashboard-grid">
            <!-- Total Addresses -->
            <div class="ana-stat-card total">
                <div class="ana-stat-title">Total Adrese</div>
                <div class="ana-stat-value"><?php echo intval( $total_addresses ); ?></div>
                <div class="ana-stat-meta">În baza de date</div>
            </div>
            
            <!-- PF Stats -->
            <div class="ana-stat-card pf">
                <div class="ana-stat-title">Persoane Fizice</div>
                <div class="ana-stat-value"><?php echo intval( $total_pf ); ?></div>
                <div class="ana-stat-meta">
                    <?php 
                    $pf_percent = $total_addresses > 0 ? round( ($total_pf / $total_addresses) * 100 ) : 0;
                    echo $pf_percent . '% din total';
                    ?>
                </div>
                <div class="ana-chart-bar">
                    <div class="ana-chart-fill" style="width: <?php echo $pf_percent; ?>%"></div>
                </div>
            </div>
            
            <!-- PJ Stats -->
            <div class="ana-stat-card pj">
                <div class="ana-stat-title">Persoane Juridice</div>
                <div class="ana-stat-value"><?php echo intval( $total_pj ); ?></div>
                <div class="ana-stat-meta">
                    <?php 
                    $pj_percent = $total_addresses > 0 ? round( ($total_pj / $total_addresses) * 100 ) : 0;
                    echo $pj_percent . '% din total';
                    ?>
                </div>
                <div class="ana-chart-bar">
                    <div class="ana-chart-fill" style="width: <?php echo $pj_percent; ?>%; background: #f39c12;"></div>
                </div>
            </div>
            
            <!-- Top Counties -->
            <div class="ana-stat-card">
                <div class="ana-stat-title">Top Județe</div>
                <ul style="margin: 0; padding: 0; list-style: none;">
                    <?php foreach ( $top_counties as $county ): ?>
                        <li style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 13px;">
                            <span><?php echo esc_html( $county->state ); ?></span>
                            <span style="font-weight: 600;"><?php echo intval( $county->count ); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php
    }

    /**
     * Render Grid View
     */
    private function render_grid_view( $table ) {
        $items = $table->items;
        
        if ( empty( $items ) ) {
            echo '<div class="notice notice-warning inline"><p>Nu au fost găsite adrese.</p></div>';
            return;
        }
        
        echo '<div class="ana-grid-view">';
        
        foreach ( $items as $item ) {
            $user = get_user_by( 'id', $item['user_id'] );
            $avatar = $user ? strtoupper( substr( $user->display_name, 0, 1 ) ) : '?';
            $is_pj = $item['entity_type'] === 'pj';
            
            ?>
            <div class="ana-grid-card">
                <div class="ana-card-header">
                    <div class="ana-card-user">
                        <div class="ana-card-avatar"><?php echo $avatar; ?></div>
                        <div class="ana-card-user-details">
                            <span class="ana-user-name"><?php echo $user ? esc_html( $user->display_name ) : 'Utilizator Șters'; ?></span>
                            <span class="ana-user-email"><?php echo $user ? esc_html( $user->user_email ) : ''; ?></span>
                        </div>
                    </div>
                    <div class="ana-card-badges">
                        <?php if ( $item['address_type'] === 'billing' ): ?>
                            <span class="ana-badge ana-badge-blue">Facturare</span>
                        <?php else: ?>
                            <span class="ana-badge ana-badge-green">Livrare</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="ana-card-body">
                    <?php if ( $is_pj && ! empty( $item['company'] ) ): ?>
                        <div class="ana-card-company">
                            <span class="dashicons dashicons-building"></span>
                            <?php echo esc_html( $item['company'] ); ?>
                        </div>
                        <?php if ( ! empty( $item['vat_number'] ) ): ?>
                            <div style="font-size: 12px; color: #666; margin-bottom: 5px;">CUI: <?php echo esc_html( $item['vat_number'] ); ?></div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="ana-card-user-name" style="margin-bottom: 5px; font-weight: 600;">
                        <span class="dashicons dashicons-admin-users" style="font-size: 16px; width: 16px; height: 16px; vertical-align: middle;"></span>
                        <?php echo esc_html( trim( ( $item['first_name'] ?? '' ) . ' ' . ( $item['last_name'] ?? '' ) ) ); ?>
                    </div>
                    
                    <div class="ana-card-address">
                        <?php 
                        echo esc_html( $item['address_1'] ); 
                        if ( ! empty( $item['address_2'] ) ) echo ', ' . esc_html( $item['address_2'] );
                        echo '<br>';
                        echo esc_html( $item['city'] ) . ', ' . esc_html( $item['state'] ) . ' ' . esc_html( $item['postcode'] );
                        ?>
                    </div>
                </div>
                
                <div class="ana-card-footer">
                    <span class="ana-card-date">
                        <?php echo date_i18n( 'd M Y', strtotime( $item['created_at'] ) ); ?>
                    </span>
                    <div class="ana-card-actions">
                        <a href="<?php echo admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses&action=edit&address_id=' . $item['id'] ); ?>" class="button button-small">Editează</a>
                    </div>
                </div>
            </div>
            <?php
        }
        
        echo '</div>';
        
        // Pagination for Grid
        $table->pagination( 'top' );
    }

    /**
     * Render table page (Modified)
     */
    public function render_table_page() {
        require_once ANA_ADDR_PLUGIN_DIR . 'includes/class-admin-table.php';
        
        $view = isset( $_GET['view'] ) && $_GET['view'] === 'grid' ? 'grid' : 'list';
        $base_url = remove_query_arg( 'view' );
        ?>
        <div class="wrap ana-wrap">
            <h1 class="wp-heading-inline">Adrese Utilizatori</h1>
            
            <!-- View Toggle -->
            <div class="ana-view-toggle">
                <a href="<?php echo esc_url( add_query_arg( 'view', 'list', $base_url ) ); ?>" class="ana-view-btn <?php echo $view === 'list' ? 'active' : ''; ?>" title="List View">
                    <span class="dashicons dashicons-list-view"></span>
                </a>
                <a href="<?php echo esc_url( add_query_arg( 'view', 'grid', $base_url ) ); ?>" class="ana-view-btn <?php echo $view === 'grid' ? 'active' : ''; ?>" title="Grid View">
                    <span class="dashicons dashicons-grid-view"></span>
                </a>
            </div>
            
            <hr class="wp-header-end">
            
            <?php
            // Render Dashboard Widgets
            $this->render_dashboard_widgets();
            
            if ( isset( $_GET['deleted'] ) ) {
                echo '<div class="notice notice-success is-dismissible"><p>' . sprintf( '%d adrese șterse cu succes.', intval( $_GET['deleted'] ) ) . '</p></div>';
            }
            ?>
            
            <?php
            $table = new ANA_Addresses_Users_Table();
            $table->prepare_items();
            ?>
            
            <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
                <input type="hidden" name="page" value="ana-addresses-manager">
                <?php 
                if ( isset( $_GET['view'] ) ) {
                    echo '<input type="hidden" name="view" value="' . esc_attr( $_GET['view'] ) . '">';
                }
                
                // Get counties for filter
                global $wpdb;
                $table_name = ANA_Addresses_Database::get_table_name();
                $counties = $wpdb->get_col( "SELECT DISTINCT state FROM {$table_name} WHERE state != '' ORDER BY state ASC" );
                ?>
                
                <div class="alignleft actions" style="margin-bottom: 10px;">
                    <select name="filter_type">
                        <option value="">Toate tipurile</option>
                        <option value="billing" <?php selected( isset( $_GET['filter_type'] ) && $_GET['filter_type'] === 'billing' ); ?>>Facturare</option>
                        <option value="shipping" <?php selected( isset( $_GET['filter_type'] ) && $_GET['filter_type'] === 'shipping' ); ?>>Livrare</option>
                    </select>
                    
                    <select name="filter_entity">
                        <option value="">Toate entitățile</option>
                        <option value="pf" <?php selected( isset( $_GET['filter_entity'] ) && $_GET['filter_entity'] === 'pf' ); ?>>Persoană Fizică</option>
                        <option value="pj" <?php selected( isset( $_GET['filter_entity'] ) && $_GET['filter_entity'] === 'pj' ); ?>>Persoană Juridică</option>
                    </select>

                    <select name="filter_county">
                        <option value="">Toate județele</option>
                        <?php if ( $counties ): foreach ( $counties as $county ): ?>
                            <option value="<?php echo esc_attr( $county ); ?>" <?php selected( isset( $_GET['filter_county'] ) && $_GET['filter_county'] === $county ); ?>>
                                <?php echo esc_html( $county ); ?>
                            </option>
                        <?php endforeach; endif; ?>
                    </select>
                    
                    <input type="submit" class="button" value="Filtrează">
                </div>
                
                <?php $table->search_box( 'Caută adrese', 'ana-addresses' ); ?>
            </form>
            
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="ana_bulk_action">
                <?php
                wp_nonce_field( 'bulk-addresses' );
                
                // Render based on view
                if ( $view === 'grid' ) {
                    $table->display_tablenav( 'top' ); // Show filters
                    $this->render_grid_view( $table );
                    $table->display_tablenav( 'bottom' );
                } else {
                    $table->display();
                }
                ?>
            </form>
        </div>
        <?php
    }
    /**
     * Show migration progress bar
     */
    public function show_migration_progress() {
        $screen = get_current_screen();
        
        // Only show on plugin admin pages
        if ( ! $screen || strpos( $screen->id, 'ana-addresses' ) === false ) {
            return;
        }
        
        $users = get_users( [ 'fields' => 'ID' ] );
        $total_users = count( $users );
        $migrated_addresses = 0;
        $migrated_companies = 0;
        
        foreach ( $users as $user_id ) {
            if ( get_user_meta( $user_id, '_ana_addresses_migrated', true ) === 'yes' ) {
                $migrated_addresses++;
            }
            if ( get_user_meta( $user_id, '_ana_user_companies_migrated', true ) === 'yes' ) {
                $migrated_companies++;
            }
        }
        
        $addresses_percentage = ( $total_users > 0 ) ? round( ( $migrated_addresses / $total_users ) * 100 ) : 0;
        $companies_percentage = ( $total_users > 0 ) ? round( ( $migrated_companies / $total_users ) * 100 ) : 0;
        $all_migrated = ( $migrated_addresses >= $total_users && $migrated_companies >= $total_users );
        
        if ( ! $all_migrated ) {
            ?>
            <div class="notice notice-info" style="padding: 15px; border-left: 4px solid #2271b1;">
                <h3 style="margin: 0 0 15px 0;">🔄 Migrare Adrese în Curs</h3>
                
                <!-- General Addresses Migration -->
                <div style="margin-bottom: 20px;">
                    <p style="margin: 0 0 8px 0; font-weight: 600;">📍 Adrese Generale (PF/PJ vechi)</p>
                    <div style="background: #f0f0f1; border-radius: 4px; height: 24px; overflow: hidden; margin-bottom: 8px; position: relative;">
                        <div style="background: linear-gradient(90deg, #2271b1, #72aee6); height: 100%; width: <?php echo $addresses_percentage; ?>%; transition: width 0.3s ease;"></div>
                        <span style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: 600; color: #1d2327; font-size: 12px;">
                            <?php echo $addresses_percentage; ?>%
                        </span>
                    </div>
                    <p style="margin: 0; font-size: 13px; color: #646970;">
                        <strong><?php echo $migrated_addresses; ?> / <?php echo $total_users; ?></strong> utilizatori
                        <?php if ( $addresses_percentage < 100 ): ?>
                            <span style="color: #f0b849;">● În curs...</span>
                        <?php else: ?>
                            <span style="color: #00a32a;">✓ Complet</span>
                        <?php endif; ?>
                    </p>
                </div>
                
                <!-- Companies Migration -->
                <div style="margin-bottom: 15px;">
                    <p style="margin: 0 0 8px 0; font-weight: 600;">🏢 Firme (Firmele mele)</p>
                    <div style="background: #f0f0f1; border-radius: 4px; height: 24px; overflow: hidden; margin-bottom: 8px; position: relative;">
                        <div style="background: linear-gradient(90deg, #f39c12, #f1c40f); height: 100%; width: <?php echo $companies_percentage; ?>%; transition: width 0.3s ease;"></div>
                        <span style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: 600; color: #1d2327; font-size: 12px;">
                            <?php echo $companies_percentage; ?>%
                        </span>
                    </div>
                    <p style="margin: 0; font-size: 13px; color: #646970;">
                        <strong><?php echo $migrated_companies; ?> / <?php echo $total_users; ?></strong> utilizatori
                        <?php if ( $companies_percentage < 100 ): ?>
                            <span style="color: #f0b849;">● În curs...</span>
                        <?php else: ?>
                            <span style="color: #00a32a;">✓ Complet</span>
                        <?php endif; ?>
                    </p>
                </div>
                
                <?php if ( $addresses_percentage < 100 || $companies_percentage < 100 ): ?>
                    <p style="margin: 10px 0 0 0; font-size: 12px; color: #646970; font-style: italic;">
                        ⏱️ Pagina se va actualiza automat în 3 secunde...
                    </p>
                <?php endif; ?>
            </div>
            <?php if ( $addresses_percentage < 100 || $companies_percentage < 100 ): ?>
                <script>
                    setTimeout(function() { location.reload(); }, 3000);
                </script>
            <?php endif; ?>
            <?php
        } elseif ( ( $migrated_addresses > 0 || $migrated_companies > 0 ) && ! get_transient( 'ana_migration_complete_shown' ) ) {
            set_transient( 'ana_migration_complete_shown', true, 10 );
            ?>
            <div class="notice notice-success is-dismissible" style="padding: 15px;">
                <h3 style="margin: 0 0 10px 0;">✅ Migrare Completată!</h3>
                <p style="margin: 0;">Toate adresele suplimentare și firmele (CUI) au fost migrate cu succes.</p>
                <ul style="margin: 10px 0 0 20px;">
                    <li>📍 Adrese generale: <strong><?php echo $migrated_addresses; ?></strong> utilizatori</li>
                    <li>🏢 Firme: <strong><?php echo $migrated_companies; ?></strong> utilizatori</li>
                </ul>
            </div>
            <?php
        }
    }
    
    /**
     * Handle manual table creation
     */
    public function handle_create_table() {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_die( 'Acces interzis' );
        }
        
        check_admin_referer( 'ana_create_table' );
        
        ANA_Addresses_Database::create_table();
        
        wp_redirect( add_query_arg( 'table_created', '1', admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses' ) ) );
        exit;
    }
    
    /**
     * Handle bulk actions
     */
    public function handle_bulk_action() {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_die( 'Acces interzis' );
        }
        
        check_admin_referer( 'bulk-addresses' );
        
        $action = isset( $_POST['action'] ) ? sanitize_text_field( $_POST['action'] ) : '';
        $address_ids = isset( $_POST['address_ids'] ) ? array_map( 'intval', $_POST['address_ids'] ) : [];
        
        if ( empty( $address_ids ) ) {
            wp_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses' ) );
            exit;
        }
        
        global $wpdb;
        $table_name = ANA_Addresses_Database::get_table_name();
        
        switch ( $action ) {
            case 'delete':
                $placeholders = implode( ',', array_fill( 0, count( $address_ids ), '%d' ) );
                $wpdb->query( $wpdb->prepare(
                    "DELETE FROM {$table_name} WHERE id IN ({$placeholders})",
                    $address_ids
                ) );
                break;
                
            case 'export':
                $this->export_addresses( $address_ids );
                exit;
        }
        
        wp_redirect( add_query_arg( 'deleted', count( $address_ids ), wp_get_referer() ? wp_get_referer() : admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses' ) ) );
        exit;
    }
    
    /**
     * Export addresses to CSV
     */
    private function export_addresses( $address_ids ) {
        global $wpdb;
        $table_name = ANA_Addresses_Database::get_table_name();
        
        $placeholders = implode( ',', array_fill( 0, count( $address_ids ), '%d' ) );
        $addresses = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE id IN ({$placeholders})",
            $address_ids
        ), ARRAY_A );
        
        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment; filename="addresses-export-' . date( 'Y-m-d' ) . '.csv"' );
        
        $output = fopen( 'php://output', 'w' );
        
        // Headers
        if ( ! empty( $addresses ) ) {
            fputcsv( $output, array_keys( $addresses[0] ) );
        }
        
        // Data
        foreach ( $addresses as $address ) {
            fputcsv( $output, $address );
        }
        
        fclose( $output );
    }
    
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            'Manager de adrese multiple',
            'Manager de adrese multiple',
            'manage_options',
            'ana-addresses-manager',
            [ $this, 'render_admin_page' ],
            'dashicons-location-alt',
            56
        );
        
        // Submenu: All Addresses
        add_submenu_page(
            'ana-addresses-manager',
            'Toate Adresele',
            'Toate Adresele',
            'manage_options',
            'ana-addresses-users',
            [ $this, 'render_users_addresses_page' ]
        );
        
        // Submenu: Natural Persons (PF)
        add_submenu_page(
            'ana-addresses-manager',
            'Adrese Persoane Fizice',
            '→ Persoane Fizice',
            'manage_options',
            'ana-addresses-pf',
            [ $this, 'render_pf_addresses_page' ]
        );
        
        // Submenu: Legal Entities (PJ)
        add_submenu_page(
            'ana-addresses-manager',
            'Adrese Persoane Juridice',
            '→ Persoane Juridice',
            'manage_options',
            'ana-addresses-pj',
            [ $this, 'render_pj_addresses_page' ]
        );
        
        // Submenu: Add New Address
        add_submenu_page(
            'ana-addresses-manager',
            'Adaugă Adresă',
            'Adaugă Nouă',
            'manage_options',
            'ana-addresses-add',
            [ $this, 'render_add_page' ]
        );
    }
    

    
    /**
     * Render all addresses page
     */
    public function render_users_addresses_page() {
        require_once ANA_ADDR_PLUGIN_DIR . 'includes/class-admin-table.php';
        
        ?>
        <div class="wrap">
            <h1>📋 Toate Adresele</h1>
            <p class="description">Vizualizează toate adresele utilizatorilor (atât din baza de date, cât și din user meta).</p>
            
            <?php
            if ( isset( $_GET['deleted'] ) ) {
                echo '<div class="notice notice-success is-dismissible"><p>' . sprintf( '%d adrese șterse cu succes.', intval( $_GET['deleted'] ) ) . '</p></div>';
            }
            ?>
            
            <?php
            $table = new ANA_Addresses_Users_Table();
            $table->prepare_items();
            ?>
            
            <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
                <input type="hidden" name="page" value="ana-addresses-users">
                <?php $table->search_box( 'Caută adrese', 'ana-addresses' ); ?>
            </form>
            
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="ana_bulk_action">
                <?php
                wp_nonce_field( 'bulk-addresses' );
                $table->display();
                ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Render PF addresses page
     */
    public function render_pf_addresses_page() {
        require_once ANA_ADDR_PLUGIN_DIR . 'includes/class-admin-table.php';
        
        ?>
        <div class="wrap">
            <h1>👤 Adrese Persoane Fizice</h1>
            <p class="description">Vizualizează doar adresele persoanelor fizice (PF).</p>
            
            <?php
            $_GET['filter_entity'] = 'pf'; // Set filter
            $table = new ANA_Addresses_Users_Table();
            $table->prepare_items();
            ?>
            
            <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
                <input type="hidden" name="page" value="ana-addresses-pf">
                <input type="hidden" name="filter_entity" value="pf">
                <?php $table->search_box( 'Caută adrese PF', 'ana-addresses' ); ?>
            </form>
            
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="ana_bulk_action">
                <?php
                wp_nonce_field( 'bulk-addresses' );
                $table->display();
                ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Render PJ addresses page
     */
    public function render_pj_addresses_page() {
        require_once ANA_ADDR_PLUGIN_DIR . 'includes/class-admin-table.php';
        
        ?>
        <div class="wrap">
            <h1>🏢 Adrese Persoane Juridice</h1>
            <p class="description">Vizualizează doar adresele persoanelor juridice (PJ) - firme.</p>
            
            <?php
            $_GET['filter_entity'] = 'pj'; // Set filter
            $table = new ANA_Addresses_Users_Table();
            $table->prepare_items();
            ?>
            
            <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
                <input type="hidden" name="page" value="ana-addresses-pj">
                <input type="hidden" name="filter_entity" value="pj">
                <?php $table->search_box( 'Caută adrese PJ', 'ana-addresses' ); ?>
            </form>
            
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="ana_bulk_action">
                <?php
                wp_nonce_field( 'bulk-addresses' );
                $table->display();
                ?>
            </form>
        </div>
        <?php
    }
    

    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        // Check if we're editing an address
        if ( isset( $_GET['action'] ) && $_GET['action'] === 'edit' && isset( $_GET['address_id'] ) ) {
            return $this->render_edit_page();
        }
        
        // Render the main table page
        $this->render_table_page();
    }

    /**
     * Render Edit Address Page
     */
    public function render_edit_page() {
        global $wpdb;
        
        if ( ! isset( $_GET['address_id'] ) ) {
            wp_die( 'ID adresă lipsește' );
        }
        
        $address_id = intval( $_GET['address_id'] );
        $table_name = ANA_Addresses_Database::get_table_name();
        
        $address = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE id = %d",
            $address_id
        ), ARRAY_A );
        
        if ( ! $address ) {
            wp_die( 'Adresa nu a fost găsită' );
        }
        
        $user = get_user_by( 'id', $address['user_id'] );
        
        ?>
        <div class="wrap">
            <div class="ana-form-container">
                <div class="ana-form-header">
                    <h1><span class="dashicons dashicons-edit"></span> Editează Adresa</h1>
                    <p>Modifică detaliile adresei pentru <?php echo esc_html( $user->display_name ); ?></p>
                </div>
                
                <div class="ana-form-body">
                    <?php if ( isset( $_GET['updated'] ) ) : ?>
                        <div class="ana-success-message">
                            <span class="dashicons dashicons-yes-alt"></span>
                            <span>Adresa a fost actualizată cu succes!</span>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="ana-address-form">
                        <input type="hidden" name="action" value="ana_save_address">
                        <input type="hidden" name="address_id" value="<?php echo esc_attr( $address_id ); ?>">
                        <?php wp_nonce_field( 'ana_save_address' ); ?>
                        
                        <!-- User Info Card -->
                        <div class="ana-info-card">
                            <h3><span class="dashicons dashicons-admin-users"></span> Utilizator</h3>
                            <p><strong><?php echo esc_html( $user->display_name ); ?></strong> (<?php echo esc_html( $user->user_email ); ?>)</p>
                        </div>
                        
                        <!-- Type & Entity Section -->
                        <div class="ana-section-header">
                            <span class="dashicons dashicons-tag"></span>
                            <h2>Tip Adresă</h2>
                        </div>
                        
                        <div class="ana-form-grid">
                            <div class="ana-form-field">
                                <label>Tip Adresă <span class="required">*</span></label>
                                <div class="ana-radio-group">
                                    <label class="ana-radio-item">
                                        <input type="radio" name="address_type" value="billing" <?php checked( $address['address_type'], 'billing' ); ?> required>
                                        <span class="ana-badge-preview billing">Facturare</span>
                                    </label>
                                    <label class="ana-radio-item">
                                        <input type="radio" name="address_type" value="shipping" <?php checked( $address['address_type'], 'shipping' ); ?>>
                                        <span class="ana-badge-preview shipping">Livrare</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="ana-form-field">
                                <label>Entitate <span class="required">*</span></label>
                                <div class="ana-radio-group">
                                   <label class="ana-radio-item">
                                        <input type="radio" name="entity_type" value="pf" <?php checked( $address['entity_type'], 'pf' ); ?> required>
                                        <span class="ana-badge-preview pf">Persoană Fizică</span>
                                    </label>
                                    <label class="ana-radio-item">
                                        <input type="radio" name="entity_type" value="pj" <?php checked( $address['entity_type'], 'pj' ); ?>>
                                        <span class="ana-badge-preview pj">Persoană Juridică</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Personal Info Section -->
                        <div class="ana-section-header">
                            <span class="dashicons dashicons-admin-users"></span>
                            <h2>Informații Personale</h2>
                        </div>
                        
                        <div class="ana-form-grid">
                            <div class="ana-form-field">
                                <label><span class="dashicons dashicons-admin-users"></span> Prenume <span class="required">*</span></label>
                                <input type="text" name="first_name" value="<?php echo esc_attr( $address['first_name'] ); ?>" required>
                            </div>
                            
                            <div class="ana-form-field">
                                <label><span class="dashicons dashicons-admin-users"></span> Nume <span class="required">*</span></label>
                                <input type="text" name="last_name" value="<?php echo esc_attr( $address['last_name'] ); ?>" required>
                            </div>
                        </div>
                        
                        <!-- Company Fields (shown only for PJ) -->
                        <div class="ana-form-grid company-fields" style="display: <?php echo $address['entity_type'] === 'pj' ? 'grid' : 'none'; ?>;">
                            <div class="ana-form-field">
                                <label><span class="dashicons dashicons-building"></span> Companie</label>
                                <input type="text" name="company" id="company" value="<?php echo esc_attr( $address['company'] ); ?>">
                            </div>
                            
                            <div class="ana-form-field">
                                <label><span class="dashicons dashicons-businessman"></span> CUI / CIF</label>
                                <input type="text" name="vat_number" value="<?php echo esc_attr( $address['vat_number'] ); ?>">
                                <span class="description">Cod Unic de Înregistrare</span>
                            </div>
                        </div>
                        
                        <!-- Address Section -->
                        <div class="ana-section-header">
                            <span class="dashicons dashicons-location-alt"></span>
                            <h2>Detalii Adresă</h2>
                        </div>
                        
                        <div class="ana-form-grid full-width">
                            <div class="ana-form-field">
                                <label><span class="dashicons dashicons-location"></span> Adresă Linia 1 <span class="required">*</span></label>
                                <input type="text" name="address_1" value="<?php echo esc_attr( $address['address_1'] ); ?>" placeholder="Stradă, număr" required>
                            </div>
                            
                            <div class="ana-form-field">
                                <label><span class="dashicons dashicons-location"></span> Adresă Linia 2</label>
                                <input type="text" name="address_2" value="<?php echo esc_attr( $address['address_2'] ); ?>" placeholder="Bloc, scară, apartament">
                            </div>
                        </div>
                        
                        <div class="ana-form-grid">
                            <div class="ana-form-field">
                                <label><span class="dashicons dashicons-location-alt"></span> Oraș <span class="required">*</span></label>
                                <input type="text" name="city" value="<?php echo esc_attr( $address['city'] ); ?>" required>
                            </div>
                            
                            <div class="ana-form-field">
                                <label><span class="dashicons dashicons-admin-site-alt3"></span> Județ <span class="required">*</span></label>
                                <?php 
                                $states = class_exists('WooCommerce') ? WC()->countries->get_states('RO') : []; 
                                if ( ! empty( $states ) ) :
                                ?>
                                    <select name="state" required class="ana-select2">
                                        <option value="">-- Selectează Județul --</option>
                                        <?php foreach ( $states as $state_key => $state_name ) : ?>
                                            <option value="<?php echo esc_attr( $state_key ); ?>" <?php selected( $address['state'], $state_key ); ?>><?php echo esc_html( $state_name ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else : ?>
                                    <input type="text" name="state" value="<?php echo esc_attr( $address['state'] ); ?>" required>
                                <?php endif; ?>
                            </div>
                            
                            <div class="ana-form-field">
                                <label><span class="dashicons dashicons-pressthis"></span> Cod Poștal <span class="required">*</span></label>
                                <input type="text" name="postcode" value="<?php echo esc_attr( $address['postcode'] ); ?>" required>
                            </div>
                            
                            <div class="ana-form-field">
                                <label><span class="dashicons dashicons-admin-site"></span> Țară</label>
                                <select name="country">
                                    <option value="RO" <?php selected( $address['country'], 'RO' ); ?>>România</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Contact Section -->
                        <div class="ana-section-header">
                            <span class="dashicons dashicons-phone"></span>
                            <h2>Informații Contact</h2>
                        </div>
                        
                        <div class="ana-form-grid">
                            <div class="ana-form-field">
                                <label><span class="dashicons dashicons-phone"></span> Telefon <span class="required">*</span></label>
                                <input type="tel" name="phone" value="<?php echo esc_attr( $address['phone'] ); ?>" placeholder="07XXXXXXXX" required>
                                <span class="description">Format: 07XXXXXXXX sau +407XXXXXXXX</span>
                            </div>
                            
                            <div class="ana-form-field">
                                <label><span class="dashicons dashicons-email"></span> Email</label>
                                <input type="email" name="email" value="<?php echo esc_attr( $address['email'] ); ?>" placeholder="exemplu@email.ro">
                            </div>
                        </div>
                        
                        <!-- Default Address Toggle -->
                        <div class="ana-form-field">
                            <label>
                                <span class="dashicons dashicons-star-filled"></span> 
                                Adresă Implicită
                            </label>
                            <label class="ana-toggle">
                                <input type="checkbox" name="is_default" value="1" <?php checked( $address['is_default'], 1 ); ?>>
                                <span class="ana-toggle-slider"></span>
                            </label>
                            <span class="description">Această adresă va fi folosită implicit pentru checkout</span>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="ana-form-actions">
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses-table' ) ); ?>" class="ana-btn ana-btn-secondary">
                                <span class="dashicons dashicons-arrow-left-alt"></span>
                                Anulează
                            </a>
                            
                            <button type="submit" class="ana-btn ana-btn-primary">
                                <span class="dashicons dashicons-saved"></span>
                                Salvează Modificările
                            </button>
                        </div>
                    </form>
                    
                    <!-- Delete Section -->
                    <div class="ana-section-header" style="margin-top: 40px;">
                        <span class="dashicons dashicons-trash"></span>
                        <h2>Zona Periculoasă</h2>
                    </div>
                    
                    <div class="ana-warning-card">
                        <h3>⚠️ Șterge Adresa</h3>
                        <p>Odată ștearsă, această adresă nu va mai putea fi recuperată.</p>
                        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top: 15px;">
                            <input type="hidden" name="action" value="ana_delete_address">
                            <input type="hidden" name="address_id" value="<?php echo esc_attr( $address_id ); ?>">
                            <?php wp_nonce_field( 'ana_delete_address_' . $address_id ); ?>
                            <button type="submit" class="ana-btn ana-btn-danger">
                                <span class="dashicons dashicons-trash"></span>
                                Șterge Permanent Adresa
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render Add Address Page
     */
    public function render_add_page() {
        ?>
        <div class="wrap">
            <div class="ana-form-container">
                <div class="ana-form-header">
                    <h1><span class="dashicons dashicons-plus-alt"></span> Adaugă Adresă Nouă</h1>
                    <p>Creează o adresă nouă pentru un utilizator</p>
                </div>
                
                <div class="ana-form-body">
                    <?php if ( isset( $_GET['added'] ) ) : ?>
                        <div class="ana-success-message">
                            <span class="dashicons dashicons-yes-alt"></span>
                            <span>Adresa a fost adăugată cu succes!</span>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="ana-address-form">
                        <input type="hidden" name="action" value="ana_save_address">
                        <input type="hidden" id="selected-user-id" name="user_id" value="">
                        <?php wp_nonce_field( 'ana_save_address' ); ?>
                        
                        <!-- User Selection -->
                        <div class="ana-section-header">
                            <span class="dashicons dashicons-admin-users"></span>
                            <h2>Selectează Utilizatorul</h2>
                        </div>
                        
                        <div class="ana-form-field">
                            <label><span class="dashicons dashicons-search"></span> Caută Utilizator <span class="required">*</span></label>
                            <div class="ana-user-selector">
                                <input type="text" id="user-search" placeholder="Introdu nume sau email..." autocomplete="off" required>
                                <span class="dashicons dashicons-search search-icon"></span>
                                <div class="ana-user-dropdown" style="display: none;"></div>
                            </div>
                            <span class="description">Tastează minim 2 caractere pentru a căuta</span>
                        </div>
                        
                        <div class="ana-info-card hidden">
                            <!-- Will be filled by JS -->
                        </div>
                        
                        <!-- Type & Entity Section -->
                        <div class="ana-section-header">
                            <span class="dashicons dashicons-tag"></span>
                            <h2>Tip Adresă</h2>
                        </div>
                        
                        <div class="ana-form-grid">
                            <div class="ana-form-field">
                                <label>Tip Adresă <span class="required">*</span></label>
                                <div class="ana-radio-group">
                                    <label class="ana-radio-item">
                                        <input type="radio" name="address_type" value="billing" checked required>
                                        <span class="ana-badge-preview billing">Facturare</span>
                                    </label>
                                    <label class="ana-radio-item">
                                        <input type="radio" name="address_type" value="shipping">
                                        <span class="ana-badge-preview shipping">Livrare</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="ana-form-field">
                                <label>Entitate <span class="required">*</span></label>
                                <div class="ana-radio-group">
                                    <label class="ana-radio-item">
                                        <input type="radio" name="entity_type" value="pf" checked required>
                                        <span class="ana-badge-preview pf">Persoană Fizică</span>
                                    </label>
                                    <label class="ana-radio-item">
                                        <input type="radio" name="entity_type" value="pj">
                                        <span class="ana-badge-preview pj">Persoană Juridică</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Personal Info Section -->
                        <div class="ana-section-header">
                            <span class="dashicons dashicons-admin-users"></span>
                            <h2>Informații Personale</h2>
                        </div>
                        
                        <div class="ana-form-grid">
                            <div class="ana-form-field">
                                <label><span class="dashicons dashicons-admin-users"></span> Prenume <span class="required">*</span></label>
                                <input type="text" name="first_name" required>
                            </div>
                            
                            <div class="ana-form-field">
                                <label><span class="dashicons dashicons-admin-users"></span> Nume <span class="required">*</span></label>
                                <input type="text" name="last_name" required>
                            </div>
                        </div>
                        
                        <!-- Company Fields (hidden by default) -->
                        <div class="ana-form-grid company-fields" style="display: none;">
                            <div class="ana-form-field">
                                <label><span class="dashicons dashicons-building"></span> Companie</label>
                                <input type="text" name="company" id="company">
                            </div>
                            
                            <div class="ana-form-field">
                                <label><span class="dashicons dashicons-businessman"></span> CUI / CIF</label>
                                <input type="text" name="vat_number">
                                <span class="description">Cod Unic de Înregistrare</span>
                            </div>
                        </div>
                        
                        <!-- Address Section -->
                        <div class="ana-section-header">
                            <span class="dashicons dashicons-location-alt"></span>
                            <h2>Detalii Adresă</h2>
                        </div>
                        
                        <div class="ana-form-grid full-width">
                            <div class="ana-form-field">
                                <label><span class="dashicons dashicons-location"></span> Adresă Linia 1 <span class="required">*</span></label>
                                <input type="text" name="address_1" placeholder="Stradă, număr" required>
                            </div>
                            
                            <div class="ana-form-field">
                                <label><span class="dashicons dashicons-location"></span> Adresă Linia 2</label>
                                <input type="text" name="address_2" placeholder="Bloc, scară, apartament">
                            </div>
                        </div>
                        
                        <div class="ana-form-grid">
                            <div class="ana-form-field">
                                <label><span class="dashicons dashicons-location-alt"></span> Oraș <span class="required">*</span></label>
                                <input type="text" name="city" required>
                            </div>
                            
                            <div class="ana-form-field">
                                <label><span class="dashicons dashicons-admin-site-alt3"></span> Județ <span class="required">*</span></label>
                                <?php 
                                $states = class_exists('WooCommerce') ? WC()->countries->get_states('RO') : []; 
                                if ( ! empty( $states ) ) :
                                ?>
                                    <select name="state" required class="ana-select2">
                                        <option value="">-- Selectează Județul --</option>
                                        <?php foreach ( $states as $state_key => $state_name ) : ?>
                                            <option value="<?php echo esc_attr( $state_key ); ?>"><?php echo esc_html( $state_name ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else : ?>
                                    <input type="text" name="state" required>
                                <?php endif; ?>
                            </div>
                            
                            <div class="ana-form-field">
                                <label><span class="dashicons dashicons-pressthis"></span> Cod Poștal <span class="required">*</span></label>
                                <input type="text" name="postcode" required>
                            </div>
                            
                            <div class="ana-form-field">
                                <label><span class="dashicons dashicons-admin-site"></span> Țară</label>
                                <select name="country">
                                    <option value="RO" selected>România</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Contact Section -->
                        <div class="ana-section-header">
                            <span class="dashicons dashicons-phone"></span>
                            <h2>Informații Contact</h2>
                        </div>
                        
                        <div class="ana-form-grid">
                            <div class="ana-form-field">
                                <label><span class="dashicons dashicons-phone"></span> Telefon <span class="required">*</span></label>
                                <input type="tel" name="phone" placeholder="07XXXXXXXX" required>
                                <span class="description">Format: 07XXXXXXXX sau +407XXXXXXXX</span>
                            </div>
                            
                            <div class="ana-form-field">
                                <label><span class="dashicons dashicons-email"></span> Email</label>
                                <input type="email" name="email" placeholder="exemplu@email.ro">
                            </div>
                        </div>
                        
                        <!-- Default Address Toggle -->
                        <div class="ana-form-field">
                            <label>
                                <span class="dashicons dashicons-star-filled"></span> 
                                Adresă Implicită
                            </label>
                            <label class="ana-toggle">
                                <input type="checkbox" name="is_default" value="1">
                                <span class="ana-toggle-slider"></span>
                            </label>
                            <span class="description">Această adresă va fi folosită implicit pentru checkout</span>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="ana-form-actions">
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses-table' ) ); ?>" class="ana-btn ana-btn-secondary">
                                <span class="dashicons dashicons-arrow-left-alt"></span>
                                Anulează
                            </a>
                            
                            <button type="submit" class="ana-btn ana-btn-primary">
                                <span class="dashicons dashicons-plus-alt"></span>
                                Creează Adresa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Handle save address (both add and edit)
     */
    public function handle_save_address() {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_die( 'Acces interzis' );
        }
        
        check_admin_referer( 'ana_save_address' );
        
        global $wpdb;
        $table_name = ANA_Addresses_Database::get_table_name();
        
        $address_id = isset( $_POST['address_id'] ) ? intval( $_POST['address_id'] ) : 0;
        $user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;
        
        // If editing, get user_id from existing address
        if ( $address_id > 0 ) {
            $existing = $wpdb->get_row( $wpdb->prepare(
                "SELECT user_id FROM {$table_name} WHERE id = %d",
                $address_id
            ) );
            $user_id = $existing->user_id;
        }
        
        if ( ! $user_id ) {
            wp_die( 'ID utilizator lipsește' );
        }
        
        $address_data = [
            'user_id' => $user_id,
            'address_type' => sanitize_text_field( $_POST['address_type'] ),
            'entity_type' => sanitize_text_field( $_POST['entity_type'] ),
            'first_name' => sanitize_text_field( $_POST['first_name'] ),
            'last_name' => sanitize_text_field( $_POST['last_name'] ),
            'company' => sanitize_text_field( $_POST['company'] ?? '' ),
            'vat_number' => sanitize_text_field( $_POST['vat_number'] ?? '' ),
            'address_1' => sanitize_text_field( $_POST['address_1'] ),
            'address_2' => sanitize_text_field( $_POST['address_2'] ?? '' ),
            'city' => sanitize_text_field( $_POST['city'] ),
            'state' => sanitize_text_field( $_POST['state'] ),
            'postcode' => sanitize_text_field( $_POST['postcode'] ),
            'country' => sanitize_text_field( $_POST['country'] ?? 'RO' ),
            'phone' => sanitize_text_field( $_POST['phone'] ),
            'email' => sanitize_email( $_POST['email'] ?? '' ),
            'is_default' => isset( $_POST['is_default'] ) ? 1 : 0,
        ];
        
        if ( $address_id > 0 ) {
            // Update existing
            $address_data['updated_at'] = current_time( 'mysql' );
            $wpdb->update( $table_name, $address_data, [ 'id' => $address_id ] );
            
            wp_redirect( add_query_arg( [
                'page' => 'ana-addresses-manager',
                'action' => 'edit',
                'address_id' => $address_id,
                'updated' => '1'
            ], admin_url( 'admin.php' ) ) );
        } else {
            // Insert new
            $address_data['created_at'] = current_time('mysql' );
            $wpdb->insert( $table_name, $address_data );
            
            wp_redirect( add_query_arg( [
                'page' => 'ana-addresses-list',
                'added' => '1'
            ], admin_url( 'admin.php' ) ) );
        }
        
        exit;
    }
    
    /**
     * Handle delete address
     */
    public function handle_delete_address() {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_die( 'Acces interzis' );
        }
        
        $address_id = isset( $_POST['address_id'] ) ? intval( $_POST['address_id'] ) : 0;
        check_admin_referer( 'ana_delete_address_' . $address_id );
        
        global $wpdb;
        $table_name = ANA_Addresses_Database::get_table_name();
        
        $wpdb->delete( $table_name, [ 'id' => $address_id ] );
        
        wp_redirect( add_query_arg( [
            'page' => 'ana-addresses-table',
            'deleted' => '1'
        ], admin_url( 'admin.php' ) ) );
        exit;
    }
    
    /**
     * AJAX: Search users
     */
    public function ajax_search_users() {
        check_ajax_referer( 'ana_addresses_admin', 'nonce' );
        
        $query = sanitize_text_field( $_POST['query'] );
        
        $users = get_users( [
            'search' => '*' . $query . '*',
            'search_columns' => [ 'user_login', 'user_email', 'display_name' ],
            'number' => 10
        ] );
        
        $results = [];
        foreach ( $users as $user ) {
            $results[] = [
                'id' => $user->ID,
                'display_name' => $user->display_name,
                'user_email' => $user->user_email
            ];
        }
        
        wp_send_json_success( $results );
    }
    
    /**
     * Count users with addresses in user meta
     */
    private function count_users_with_meta_addresses() {
        $users = get_users( [ 'fields' => 'ID' ] );
        $count = [
            'total' => 0,
            'billing' => 0,
            'shipping' => 0
        ];
        
        foreach ( $users as $user_id ) {
            $has_billing = get_user_meta( $user_id, 'billing_address_1', true );
            $has_shipping = get_user_meta( $user_id, 'shipping_address_1', true );
            
            if ( $has_billing ) {
                $count['billing']++;
                $count['total']++;
            }
            if ( $has_shipping ) {
                $count['shipping']++;
                $count['total']++;
            }
        }
        
        return $count;
    }
}

//Initialize admin page
new ANA_Addresses_Admin_Page();
