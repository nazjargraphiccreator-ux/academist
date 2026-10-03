<?php
/**
 * Modern Admin Page - Complete Rewrite
 *
 * @package ANA_Addresses
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ANA_Addresses_Admin_Modern {

    /**
     * Current view
     */
    private $current_view = 'dashboard';

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        
        // Add AJAX handlers for forms (user search, save, delete)
        add_action( 'wp_ajax_ana_search_users', [ $this, 'ajax_search_users' ] );
        add_action( 'admin_post_ana_save_address', [ $this, 'handle_save_address' ] );
        add_action( 'admin_post_ana_delete_address', [ $this, 'handle_delete_address' ] );
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'Adrese Multiple', 'ana-addresses' ),
            __( 'Adrese', 'ana-addresses' ),
            'manage_options',
            'ana-addresses',
            [ $this, 'render_page' ],
            'dashicons-location',
            56
        );

        add_submenu_page(
            'ana-addresses',
            __( 'Dashboard', 'ana-addresses' ),
            __( 'Dashboard', 'ana-addresses' ),
            'manage_options',
            'ana-addresses',
            [ $this, 'render_page' ]
        );

        add_submenu_page(
            'ana-addresses',
            __( 'Toate Adresele', 'ana-addresses' ),
            __( 'Toate Adresele', 'ana-addresses' ),
            'manage_options',
            'ana-addresses-list',
            [ $this, 'render_addresses_page' ]
        );

        add_submenu_page(
            'ana-addresses',
            __( 'Companii (PJ)', 'ana-addresses' ),
            __( 'Companii (PJ)', 'ana-addresses' ),
            'manage_options',
            'ana-addresses-companies',
            [ $this, 'render_companies_page' ]
        );

        add_submenu_page(
            'ana-addresses',
            __( 'Setări', 'ana-addresses' ),
            __( 'Setări', 'ana-addresses' ),
            'manage_options',
            'ana-addresses-settings',
            [ $this, 'render_settings_page' ]
        );
    }

    /**
     * Enqueue assets
     */
    public function enqueue_assets( $hook ) {
        $is_rima_addresses = ( strpos( $hook, 'rima-admin-panel' ) !== false && isset( $_GET['rima_tab'] ) && $_GET['rima_tab'] === 'addresses' );
        if ( strpos( $hook, 'ana-addresses' ) === false && ! $is_rima_addresses ) {
            return;
        }

        wp_enqueue_style(
            'ana-admin-modern',
            ANA_ADDR_PLUGIN_URL . 'assets/css/admin-modern.css',
            [],
            ANA_ADDR_VERSION
        );

        wp_enqueue_script(
            'ana-admin-modern',
            ANA_ADDR_PLUGIN_URL . 'assets/js/admin-modern.js',
            [ 'jquery' ],
            ANA_ADDR_VERSION,
            true
        );
        
        // Enqueue forms JS for user search autocomplete
        wp_enqueue_script(
            'ana-admin-forms',
            ANA_ADDR_PLUGIN_URL . 'assets/js/admin-forms.js',
            [ 'jquery' ],
            ANA_ADDR_VERSION,
            true
        );

        wp_localize_script( 'ana-admin-modern', 'anaAdmin', [
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'ana_admin_nonce' ),
        ]);
        
        wp_localize_script( 'ana-admin-forms', 'anaAddressesAdmin', [
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'ana_addresses_admin' ),
        ]);
    }

    /**
     * Get stats
     */
    private function get_stats() {
        global $wpdb;
        $table = $wpdb->prefix . 'ana_user_addresses';

        return [
            'total'   => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" ),
            'pf'      => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE entity_type = 'pf'" ),
            'pj'      => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE entity_type = 'pj'" ),
            'users'   => (int) $wpdb->get_var( "SELECT COUNT(DISTINCT user_id) FROM {$table}" ),
            'billing' => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE address_type = 'billing'" ),
            'shipping'=> (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE address_type = 'shipping'" ),
        ];
    }

    /**
     * Get top counties
     */
    private function get_top_counties( $limit = 5 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'ana_user_addresses';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT state, COUNT(*) as count FROM {$table} WHERE state != '' GROUP BY state ORDER BY count DESC LIMIT %d",
            $limit
        ) );
    }

    /**
     * Render sidebar
     */
    private function render_sidebar( $active = 'dashboard' ) {
        $stats = $this->get_stats();
        ?>
        <aside class="ana-sidebar">
            <div class="ana-sidebar-header">
                <div class="ana-sidebar-logo">
                    <div class="logo-icon">📍</div>
                    <div>
                        <div class="logo-text">Manager de adrese multiple</div>
                    </div>
                    <span class="version">v1.0</span>
                </div>
            </div>

            <nav class="ana-sidebar-nav">
                <div class="ana-nav-section">
                    <div class="ana-nav-section-title">Principal</div>
                    <a href="<?php echo admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses' ); ?>" 
                       class="ana-nav-item <?php echo $active === 'dashboard' ? 'active' : ''; ?>">
                        <span class="dashicons dashicons-dashboard"></span>
                        Dashboard
                    </a>
                    <a href="<?php echo admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses&subtab=list' ); ?>" 
                       class="ana-nav-item <?php echo $active === 'addresses' ? 'active' : ''; ?>">
                        <span class="dashicons dashicons-location-alt"></span>
                        Toate Adresele
                        <span class="badge"><?php echo $stats['total']; ?></span>
                    </a>
                </div>

                <div class="ana-nav-section">
                    <div class="ana-nav-section-title">Tipuri</div>
                    <a href="<?php echo admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses&subtab=list&filter=pf' ); ?>" 
                       class="ana-nav-item <?php echo $active === 'pf' ? 'active' : ''; ?>">
                        <span class="dashicons dashicons-admin-users"></span>
                        Persoane Fizice
                        <span class="badge"><?php echo $stats['pf']; ?></span>
                    </a>
                    <a href="<?php echo admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses&subtab=companies' ); ?>" 
                       class="ana-nav-item <?php echo $active === 'companies' ? 'active' : ''; ?>">
                        <span class="dashicons dashicons-building"></span>
                        Companii (PJ)
                        <span class="badge"><?php echo $stats['pj']; ?></span>
                    </a>
                </div>

                <div class="ana-nav-section">
                    <div class="ana-nav-section-title">Instrumente</div>
                    <a href="<?php echo admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses&subtab=settings' ); ?>" 
                       class="ana-nav-item <?php echo $active === 'settings' ? 'active' : ''; ?>">
                        <span class="dashicons dashicons-admin-settings"></span>
                        Setări
                    </a>
                </div>
            </nav>

            <div class="ana-sidebar-footer">
                <a href="https://webservicesp.anaf.ro" target="_blank">
                    <span class="dashicons dashicons-external"></span>
                    ANAF API
                </a>
                <a href="#" class="ana-export-btn">
                    <span class="dashicons dashicons-download"></span>
                    Export CSV
                </a>
            </div>
        </aside>
        <?php
    }

    /**
     * Render Dashboard Page
     */
    public function render_page() {
        $stats = $this->get_stats();
        $top_counties = $this->get_top_counties();
        $recent = $this->get_recent_addresses( 5 );
        ?>
        <div class="ana-admin-app">
            <?php $this->render_sidebar( 'dashboard' ); ?>

            <main class="ana-main">
                <div class="ana-page-header">
                    <h1><span class="dashicons dashicons-dashboard"></span> Dashboard</h1>
                    <div class="ana-header-actions">
                        <a href="<?php echo admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses&subtab=list&action=add' ); ?>" class="ana-btn ana-btn-primary">
                            <span class="dashicons dashicons-plus-alt2"></span>
                            Adaugă Adresă
                        </a>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="ana-stats-grid">
                    <div class="ana-stat-card total">
                        <div class="ana-stat-icon"><span class="dashicons dashicons-location-alt"></span></div>
                        <div class="ana-stat-value"><?php echo number_format( $stats['total'] ); ?></div>
                        <div class="ana-stat-label">Total Adrese</div>
                    </div>
                    <div class="ana-stat-card pf">
                        <div class="ana-stat-icon"><span class="dashicons dashicons-admin-users"></span></div>
                        <div class="ana-stat-value"><?php echo number_format( $stats['pf'] ); ?></div>
                        <div class="ana-stat-label">Persoane Fizice</div>
                    </div>
                    <div class="ana-stat-card pj">
                        <div class="ana-stat-icon"><span class="dashicons dashicons-building"></span></div>
                        <div class="ana-stat-value"><?php echo number_format( $stats['pj'] ); ?></div>
                        <div class="ana-stat-label">Companii (PJ)</div>
                    </div>
                    <div class="ana-stat-card users">
                        <div class="ana-stat-icon"><span class="dashicons dashicons-groups"></span></div>
                        <div class="ana-stat-value"><?php echo number_format( $stats['users'] ); ?></div>
                        <div class="ana-stat-label">Utilizatori</div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
                    <!-- Recent Addresses -->
                    <div class="ana-card">
                        <div class="ana-card-header">
                            <h2><span class="dashicons dashicons-clock"></span> Adrese Recente</h2>
                            <a href="<?php echo admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses&subtab=list' ); ?>" class="ana-btn ana-btn-secondary ana-btn-sm">
                                Vezi toate
                            </a>
                        </div>
                        <div class="ana-card-body">
                            <?php if ( ! empty( $recent ) ) : ?>
                                <div class="ana-table-wrapper">
                                    <table class="ana-table">
                                        <thead>
                                            <tr>
                                                <th>Utilizator</th>
                                                <th>Tip</th>
                                                <th>Adresă</th>
                                                <th>Data</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ( $recent as $addr ) : 
                                                $user = get_user_by( 'id', $addr->user_id );
                                                $initials = $user ? strtoupper( substr( $user->display_name, 0, 2 ) ) : '??';
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="ana-user-cell">
                                                        <div class="ana-avatar"><?php echo $initials; ?></div>
                                                        <div class="ana-user-info">
                                                            <div class="name"><?php echo $user ? esc_html( $user->display_name ) : 'Șters'; ?></div>
                                                            <div class="email"><?php echo $user ? esc_html( $user->user_email ) : ''; ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="ana-badge ana-badge-<?php echo $addr->entity_type; ?>">
                                                        <?php echo $addr->entity_type === 'pf' ? 'PF' : 'PJ'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="ana-address-cell">
                                                        <?php if ( ! empty( $addr->company ) ) : ?>
                                                            <div class="company">
                                                                <span class="dashicons dashicons-building"></span>
                                                                <?php echo esc_html( $addr->company ); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="address">
                                                            <?php echo esc_html( $addr->address_1 . ', ' . $addr->city ); ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="color: #6c757d; font-size: 12px;">
                                                    <?php echo date_i18n( 'd M Y', strtotime( $addr->created_at ) ); ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else : ?>
                                <div class="ana-empty-state">
                                    <div class="icon"><span class="dashicons dashicons-location"></span></div>
                                    <h3>Nicio adresă încă</h3>
                                    <p>Adresele vor apărea aici când utilizatorii le adaugă.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Top Counties -->
                    <div class="ana-card">
                        <div class="ana-card-header">
                            <h2><span class="dashicons dashicons-location"></span> Top Județe</h2>
                        </div>
                        <div class="ana-card-body">
                            <?php if ( ! empty( $top_counties ) ) : ?>
                                <ul class="ana-top-list">
                                    <?php foreach ( $top_counties as $i => $county ) : ?>
                                        <li>
                                            <span class="rank"><?php echo $i + 1; ?></span>
                                            <span class="name"><?php echo esc_html( $county->state ); ?></span>
                                            <span class="count"><?php echo $county->count; ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else : ?>
                                <p style="color: #6c757d;">Încă nu sunt date.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; margin-top: 24px;">
                    <div class="ana-card">
                        <div class="ana-card-header">
                            <h2><span class="dashicons dashicons-chart-pie"></span> Distribuție Tip</h2>
                        </div>
                        <div class="ana-card-body">
                            <div style="display: flex; gap: 20px;">
                                <div style="flex: 1;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span>Facturare</span>
                                        <strong><?php echo $stats['billing']; ?></strong>
                                    </div>
                                    <div style="height: 8px; background: #e9ecef; border-radius: 4px; overflow: hidden;">
                                        <div style="height: 100%; background: linear-gradient(90deg, #3498db, #5dade2); width: <?php echo $stats['total'] > 0 ? round($stats['billing']/$stats['total']*100) : 0; ?>%;"></div>
                                    </div>
                                </div>
                                <div style="flex: 1;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span>Livrare</span>
                                        <strong><?php echo $stats['shipping']; ?></strong>
                                    </div>
                                    <div style="height: 8px; background: #e9ecef; border-radius: 4px; overflow: hidden;">
                                        <div style="height: 100%; background: linear-gradient(90deg, #00b894, #55efc4); width: <?php echo $stats['total'] > 0 ? round($stats['shipping']/$stats['total']*100) : 0; ?>%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ana-card">
                        <div class="ana-card-header">
                            <h2><span class="dashicons dashicons-megaphone"></span> Acțiuni Rapide</h2>
                        </div>
                        <div class="ana-card-body">
                            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                                <a href="<?php echo admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses&subtab=list&action=add' ); ?>" class="ana-btn ana-btn-primary">
                                    <span class="dashicons dashicons-plus"></span> Adaugă Adresă
                                </a>
                                <a href="<?php echo admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses&subtab=companies' ); ?>" class="ana-btn ana-btn-secondary">
                                    <span class="dashicons dashicons-building"></span> Companii
                                </a>
                                <button class="ana-btn ana-btn-secondary ana-export-btn">
                                    <span class="dashicons dashicons-download"></span> Export CSV
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <?php
    }

    /**
     * Get recent addresses
     */
    private function get_recent_addresses( $limit = 5 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'ana_user_addresses';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table} ORDER BY created_at DESC LIMIT %d",
            $limit
        ) );
    }

    /**
     * Render Addresses List Page
     */
    public function render_addresses_page() {
        // Check for actions (add/edit)
        $action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
        
        if ( $action === 'add' || $action === 'edit' ) {
            // Delegate to parent class for forms
            require_once ANA_ADDR_PLUGIN_DIR . 'includes/class-admin-page.php';
            $parent = new ANA_Addresses_Admin_Page();
            
            if ( $action === 'add' ) {
                return $parent->render_add_page();
            } else {
                return $parent->render_edit_page();
            }
        }
        
        // Default: show list
        $filter = isset( $_GET['filter'] ) ? sanitize_text_field( $_GET['filter'] ) : '';
        $search = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
        $paged = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
        $per_page = 20;

        $addresses = $this->get_addresses( $filter, $search, $paged, $per_page );
        $total = $this->count_addresses( $filter, $search );
        $total_pages = ceil( $total / $per_page );

        $active = $filter === 'pf' ? 'pf' : 'addresses';
        ?>
        <div class="ana-admin-app">
            <?php $this->render_sidebar( $active ); ?>

            <main class="ana-main">
                <div class="ana-page-header">
                    <h1>
                        <span class="dashicons dashicons-location-alt"></span>
                        <?php echo $filter === 'pf' ? 'Persoane Fizice' : 'Toate Adresele'; ?>
                    </h1>
                    <div class="ana-header-actions">
                        <a href="<?php echo admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses&subtab=list&action=add' ); ?>" class="ana-btn ana-btn-primary">
                            <span class="dashicons dashicons-plus-alt2"></span>
                            Adaugă Adresă
                        </a>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="ana-tabs">
                    <a href="<?php echo admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses&subtab=list' ); ?>" 
                       class="ana-tab <?php echo empty( $filter ) ? 'active' : ''; ?>">
                        Toate
                        <span class="count"><?php echo $this->count_addresses(); ?></span>
                    </a>
                    <a href="<?php echo admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses&subtab=list&filter=pf' ); ?>" 
                       class="ana-tab <?php echo $filter === 'pf' ? 'active' : ''; ?>">
                        <span class="dashicons dashicons-admin-users"></span>
                        Persoane Fizice
                        <span class="count"><?php echo $this->count_addresses( 'pf' ); ?></span>
                    </a>
                    <a href="<?php echo admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses&subtab=list&filter=pj' ); ?>" 
                       class="ana-tab <?php echo $filter === 'pj' ? 'active' : ''; ?>">
                        <span class="dashicons dashicons-building"></span>
                        Companii (PJ)
                        <span class="count"><?php echo $this->count_addresses( 'pj' ); ?></span>
                    </a>
                    <a href="<?php echo admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses&subtab=list&filter=billing' ); ?>" 
                       class="ana-tab <?php echo $filter === 'billing' ? 'active' : ''; ?>">
                        Facturare
                    </a>
                    <a href="<?php echo admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses&subtab=list&filter=shipping' ); ?>" 
                       class="ana-tab <?php echo $filter === 'shipping' ? 'active' : ''; ?>">
                        Livrare
                    </a>
                </div>

                <!-- Filters -->
                <div class="ana-filters">
                    <div class="ana-search">
                        <span class="dashicons dashicons-search"></span>
                        <form method="get">
                            <input type="hidden" name="page" value="ana-addresses-list">
                            <?php if ( $filter ) : ?>
                                <input type="hidden" name="filter" value="<?php echo esc_attr( $filter ); ?>">
                            <?php endif; ?>
                            <input type="text" name="s" value="<?php echo esc_attr( $search ); ?>" 
                                   placeholder="Caută după nume, email, adresă...">
                        </form>
                    </div>
                    <div class="ana-view-toggle">
                        <button class="ana-view-btn active" data-view="table">
                            <span class="dashicons dashicons-list-view"></span>
                        </button>
                        <button class="ana-view-btn" data-view="grid">
                            <span class="dashicons dashicons-grid-view"></span>
                        </button>
                    </div>
                </div>

                <!-- Table View -->
                <div class="ana-card" id="ana-table-view">
                    <div class="ana-card-body" style="padding: 0;">
                        <?php if ( ! empty( $addresses ) ) : ?>
                            <div class="ana-table-wrapper">
                                <table class="ana-table">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="ana-select-all"></th>
                                            <th>Utilizator</th>
                                            <th>Tip</th>
                                            <th>Adresă</th>
                                            <th>Oraș / Județ</th>
                                            <th>Categorie</th>
                                            <th>Acțiuni</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ( $addresses as $addr ) : 
                                            $user = get_user_by( 'id', $addr->user_id );
                                            $initials = $user ? strtoupper( substr( $user->display_name, 0, 2 ) ) : '??';
                                        ?>
                                        <tr>
                                            <td><input type="checkbox" name="address_ids[]" value="<?php echo $addr->id; ?>"></td>
                                            <td>
                                                <div class="ana-user-cell">
                                                    <div class="ana-avatar"><?php echo $initials; ?></div>
                                                    <div class="ana-user-info">
                                                        <div class="name"><?php echo $user ? esc_html( $user->display_name ) : 'Șters'; ?></div>
                                                        <div class="email"><?php echo $user ? esc_html( $user->user_email ) : ''; ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="ana-badge ana-badge-<?php echo $addr->entity_type; ?>">
                                                    <?php echo $addr->entity_type === 'pf' ? 'PF' : 'PJ'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="ana-address-cell">
                                                    <?php if ( ! empty( $addr->company ) ) : ?>
                                                        <div class="company">
                                                            <span class="dashicons dashicons-building"></span>
                                                            <?php echo esc_html( $addr->company ); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="address"><?php echo esc_html( $addr->address_1 ); ?></div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong><?php echo esc_html( $addr->city ); ?></strong><br>
                                                <span style="color: #6c757d; font-size: 12px;"><?php echo esc_html( $addr->state ); ?></span>
                                            </td>
                                            <td>
                                                <span class="ana-badge ana-badge-<?php echo $addr->address_type; ?>">
                                                    <?php echo $addr->address_type === 'billing' ? 'Facturare' : 'Livrare'; ?>
                                                </span>
                                                <?php if ( $addr->is_default ) : ?>
                                                    <span class="ana-badge ana-badge-default">Default</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="ana-actions">
                                                    <a href="<?php echo admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses&subtab=list&action=edit&id=' . $addr->id ); ?>" 
                                                       class="ana-btn ana-btn-secondary ana-btn-icon" title="Editează">
                                                        <span class="dashicons dashicons-edit"></span>
                                                    </a>
                                                    <button class="ana-btn ana-btn-danger ana-btn-icon ana-delete-btn" 
                                                            data-id="<?php echo $addr->id; ?>" title="Șterge">
                                                        <span class="dashicons dashicons-trash"></span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else : ?>
                            <div class="ana-empty-state">
                                <div class="icon"><span class="dashicons dashicons-location"></span></div>
                                <h3>Nicio adresă găsită</h3>
                                <p>Nu există adrese care să corespundă criteriilor.</p>
                                <a href="<?php echo admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses&subtab=list&action=add' ); ?>" class="ana-btn ana-btn-primary">
                                    <span class="dashicons dashicons-plus"></span> Adaugă Prima Adresă
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if ( $total_pages > 1 ) : ?>
                    <div class="ana-pagination">
                        <?php for ( $i = 1; $i <= $total_pages; $i++ ) : ?>
                            <?php if ( $i === $paged ) : ?>
                                <span class="current"><?php echo $i; ?></span>
                            <?php else : ?>
                                <a href="<?php echo add_query_arg( 'paged', $i ); ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </main>
        </div>
        <?php
    }

    /**
     * Get addresses with filters
     */
    private function get_addresses( $filter = '', $search = '', $page = 1, $per_page = 20 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'ana_user_addresses';
        $offset = ( $page - 1 ) * $per_page;

        $where = '1=1';
        if ( $filter === 'pf' ) $where .= " AND entity_type = 'pf'";
        if ( $filter === 'pj' ) $where .= " AND entity_type = 'pj'";
        if ( $filter === 'billing' ) $where .= " AND address_type = 'billing'";
        if ( $filter === 'shipping' ) $where .= " AND address_type = 'shipping'";

        if ( $search ) {
            $search_like = '%' . $wpdb->esc_like( $search ) . '%';
            $where .= $wpdb->prepare( " AND (first_name LIKE %s OR last_name LIKE %s OR company LIKE %s OR city LIKE %s OR address_1 LIKE %s)",
                $search_like, $search_like, $search_like, $search_like, $search_like );
        }

        return $wpdb->get_results( "SELECT * FROM {$table} WHERE {$where} ORDER BY created_at DESC LIMIT {$per_page} OFFSET {$offset}" );
    }

    /**
     * Count addresses
     */
    private function count_addresses( $filter = '', $search = '' ) {
        global $wpdb;
        $table = $wpdb->prefix . 'ana_user_addresses';

        $where = '1=1';
        if ( $filter === 'pf' ) $where .= " AND entity_type = 'pf'";
        if ( $filter === 'pj' ) $where .= " AND entity_type = 'pj'";
        if ( $filter === 'billing' ) $where .= " AND address_type = 'billing'";
        if ( $filter === 'shipping' ) $where .= " AND address_type = 'shipping'";

        if ( $search ) {
            $search_like = '%' . $wpdb->esc_like( $search ) . '%';
            $where .= $wpdb->prepare( " AND (first_name LIKE %s OR last_name LIKE %s OR company LIKE %s OR city LIKE %s)",
                $search_like, $search_like, $search_like, $search_like );
        }

        return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE {$where}" );
    }

    /**
     * Render Companies Page
     */
    public function render_companies_page() {
        $companies = $this->get_companies();
        ?>
        <div class="ana-admin-app">
            <?php $this->render_sidebar( 'companies' ); ?>

            <main class="ana-main">
                <div class="ana-page-header">
                    <h1><span class="dashicons dashicons-building"></span> Companii (PJ)</h1>
                    <div class="ana-header-actions">
                        <a href="<?php echo admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses&subtab=list&action=add&type=pj' ); ?>" class="ana-btn ana-btn-primary">
                            <span class="dashicons dashicons-plus-alt2"></span>
                            Adaugă Companie
                        </a>
                    </div>
                </div>

                <!-- Grid View for Companies -->
                <div class="ana-grid">
                    <?php if ( ! empty( $companies ) ) : ?>
                        <?php foreach ( $companies as $company ) : 
                            $user = get_user_by( 'id', $company->user_id );
                        ?>
                        <div class="ana-grid-card pj">
                            <div class="ana-grid-header">
                                <div>
                                    <span class="ana-badge ana-badge-pj">PJ</span>
                                    <?php if ( $company->is_default ) : ?>
                                        <span class="ana-badge ana-badge-default">Default</span>
                                    <?php endif; ?>
                                </div>
                                <span class="ana-badge ana-badge-<?php echo $company->address_type; ?>">
                                    <?php echo $company->address_type === 'billing' ? 'Facturare' : 'Livrare'; ?>
                                </span>
                            </div>
                            <div class="ana-grid-body">
                                <div class="ana-grid-company"><?php echo esc_html( $company->company ); ?></div>
                                <?php if ( ! empty( $company->vat_number ) ) : ?>
                                    <div class="ana-grid-cui">CUI: <?php echo esc_html( $company->vat_number ); ?></div>
                                <?php endif; ?>
                                <div class="ana-grid-address">
                                    <?php echo esc_html( $company->address_1 ); ?><br>
                                    <?php echo esc_html( $company->city . ', ' . $company->state ); ?>
                                </div>
                            </div>
                            <div class="ana-grid-footer">
                                <div class="ana-grid-date">
                                    <?php if ( $user ) : ?>
                                        <span class="dashicons dashicons-admin-users"></span>
                                        <?php echo esc_html( $user->display_name ); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="ana-actions">
                                    <a href="<?php echo admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses&subtab=list&action=edit&id=' . $company->id ); ?>" 
                                       class="ana-btn ana-btn-secondary ana-btn-sm">
                                        <span class="dashicons dashicons-edit"></span>
                                    </a>
                                    <button class="ana-btn ana-btn-danger ana-btn-sm ana-delete-btn" data-id="<?php echo $company->id; ?>">
                                        <span class="dashicons dashicons-trash"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="ana-empty-state" style="grid-column: 1/-1;">
                            <div class="icon"><span class="dashicons dashicons-building"></span></div>
                            <h3>Nicio companie</h3>
                            <p>Adaugă prima companie (PJ) pentru clienții tăi.</p>
                            <a href="<?php echo admin_url( 'admin.php?page=rima-admin-panel&rima_tab=addresses&subtab=list&action=add&type=pj' ); ?>" class="ana-btn ana-btn-primary">
                                <span class="dashicons dashicons-plus"></span> Adaugă Companie
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
        <?php
    }

    /**
     * Get companies
     */
    private function get_companies( $limit = 50 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'ana_user_addresses';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE entity_type = 'pj' ORDER BY created_at DESC LIMIT %d",
            $limit
        ) );
    }

    /**
     * Render Settings Page
     */
    public function render_settings_page() {
        ?>
        <div class="ana-admin-app">
            <?php $this->render_sidebar( 'settings' ); ?>

            <main class="ana-main">
                <div class="ana-page-header">
                    <h1><span class="dashicons dashicons-admin-settings"></span> Setări</h1>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
                    <!-- Settings Form -->
                    <div class="ana-card">
                        <div class="ana-card-header">
                            <h2>Setări Generale</h2>
                        </div>
                        <div class="ana-card-body">
                            <form method="post" action="options.php">
                                <?php settings_fields( 'ana_addresses_settings' ); ?>
                                
                                <div style="margin-bottom: 20px;">
                                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">
                                        Validare CUI cu ANAF
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 10px;">
                                        <input type="checkbox" name="ana_validate_cui" value="1" 
                                               <?php checked( get_option( 'ana_validate_cui', 1 ) ); ?>>
                                        Verifică automat CUI-ul companiilor cu API-ul ANAF
                                    </label>
                                </div>

                                <div style="margin-bottom: 20px;">
                                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">
                                        Cache ANAF (ore)
                                    </label>
                                    <input type="number" name="ana_anaf_cache_hours" 
                                           value="<?php echo esc_attr( get_option( 'ana_anaf_cache_hours', 24 ) ); ?>"
                                           min="1" max="168" class="ana-filter-select" style="width: 100px;">
                                    <p style="color: #6c757d; font-size: 12px; margin-top: 5px;">
                                        Câte ore să păstrăm în cache datele de la ANAF (1-168 ore).
                                    </p>
                                </div>

                                <div style="margin-bottom: 20px;">
                                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">
                                        Permite CNP pentru PF
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 10px;">
                                        <input type="checkbox" name="ana_allow_cnp" value="1" 
                                               <?php checked( get_option( 'ana_allow_cnp', 0 ) ); ?>>
                                        Afișează câmpul CNP pentru persoane fizice
                                    </label>
                                </div>

                                <button type="submit" class="ana-btn ana-btn-primary">
                                    <span class="dashicons dashicons-saved"></span>
                                    Salvează Setările
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Info Card -->
                    <div>
                        <div class="ana-card">
                            <div class="ana-card-header">
                                <h2><span class="dashicons dashicons-cloud"></span> ANAF API</h2>
                            </div>
                            <div class="ana-card-body">
                                <p style="font-size: 13px; color: #3c434a; margin-bottom: 16px;">
                                    Plugin-ul folosește API-ul oficial ANAF pentru verificarea companiilor românești.
                                </p>
                                <div style="background: rgba(0,184,148,0.1); padding: 12px; border-radius: 8px; margin-bottom: 16px;">
                                    <strong style="color: #00b894;">✓ API Activ</strong>
                                    <div style="font-size: 12px; color: #6c757d; margin-top: 4px;">
                                        webservicesp.anaf.ro/v8
                                    </div>
                                </div>
                                <a href="https://webservicesp.anaf.ro" target="_blank" class="ana-btn ana-btn-secondary" style="width: 100%;">
                                    <span class="dashicons dashicons-external"></span>
                                    Documentație ANAF
                                </a>
                            </div>
                        </div>

                        <div class="ana-card" style="margin-top: 24px;">
                            <div class="ana-card-header">
                                <h2><span class="dashicons dashicons-database"></span> Bază de Date</h2>
                            </div>
                            <div class="ana-card-body">
                                <?php
                                global $wpdb;
                                $table = $wpdb->prefix . 'ana_user_addresses';
                                $exists = $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" );
                                ?>
                                <?php if ( $exists ) : ?>
                                    <div style="background: rgba(0,184,148,0.1); padding: 12px; border-radius: 8px;">
                                        <strong style="color: #00b894;">✓ Tabel Creat</strong>
                                        <div style="font-size: 12px; color: #6c757d; margin-top: 4px;">
                                            <?php echo esc_html( $table ); ?>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <div style="background: rgba(231,76,60,0.1); padding: 12px; border-radius: 8px;">
                                        <strong style="color: #e74c3c;">✗ Tabel Lipsă</strong>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <?php
    }
    
    /**
     * AJAX: Search users for autocomplete
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
     * Handle save address - delegate to parent class
     */
    public function handle_save_address() {
        require_once ANA_ADDR_PLUGIN_DIR . 'includes/class-admin-page.php';
        $parent = new ANA_Addresses_Admin_Page();
        return $parent->handle_save_address();
    }
    
    /**
     * Handle delete address - delegate to parent class
     */
    public function handle_delete_address() {
        require_once ANA_ADDR_PLUGIN_DIR . 'includes/class-admin-page.php';
        $parent = new ANA_Addresses_Admin_Page();
        return $parent->handle_delete_address();
    }
}
