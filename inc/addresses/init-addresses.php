<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'ANA_ADDR_VERSION', '2.0.0' );
define( 'ANA_ADDR_PLUGIN_FILE', __FILE__ );
define( 'ANA_ADDR_PLUGIN_DIR', get_stylesheet_directory() . '/inc/addresses/' );
define( 'ANA_ADDR_PLUGIN_URL', get_stylesheet_directory_uri() . '/inc/addresses/' );

/**
 * Main plugin class
 */
class ANA_Addresses_Manager_Plugin {
    
    /**
     * Single instance
     */
    private static $instance = null;
    
    /**
     * Get instance
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Hook into WordPress
     */
    private function init_hooks() {
        // Since RIMA already loaded this on plugins_loaded, we can directly load includes
        $this->on_plugins_loaded();
        
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'admin_init', [ $this, 'maybe_redirect_to_setup' ] );
        
        // WooCommerce hooks
        add_action( 'woocommerce_checkout_create_order', [ $this, 'save_selected_address_to_order' ], 10, 2 );
    }
    

    
    /**
     * Load plugin after all plugins loaded
     */
    public function on_plugins_loaded() {
        // Load includes unconditionally since RIMA Academy requires WooCommerce anyway
        $this->includes();
    }
    
    /**
     * Include required files
     */
    private function includes() {
        require_once ANA_ADDR_PLUGIN_DIR . 'includes/class-database.php';
        require_once ANA_ADDR_PLUGIN_DIR . 'includes/class-companies-integration.php';
        require_once ANA_ADDR_PLUGIN_DIR . 'includes/class-addresses.php';
        require_once ANA_ADDR_PLUGIN_DIR . 'includes/ajax-handlers.php';
        
        // Frontend AJAX handlers (for My Account & Checkout)
        // ALWAYS include - the class itself checks is_user_logged_in() on init hook
        require_once ANA_ADDR_PLUGIN_DIR . 'includes/class-frontend-ajax.php';

        
        // Admin interface
        require_once ANA_ADDR_PLUGIN_DIR . 'includes/ajax-get-single-address.php';
        require_once ANA_ADDR_PLUGIN_DIR . 'includes/woocommerce-integration.php';
        require_once ANA_ADDR_PLUGIN_DIR . 'includes/class-frontend-ui.php';

        
        // Admin page and setup wizard
        require_once ANA_ADDR_PLUGIN_DIR . 'includes/class-setup-wizard.php';
        // Load modern admin interface
        require_once ANA_ADDR_PLUGIN_DIR . 'includes/class-admin-modern.php';
        
        // Initialize modern admin page
        new ANA_Addresses_Admin_Modern();
    }
    
    /**
     * Check if setup wizard needs to be shown
     */
    public function maybe_redirect_to_setup() {
        // Disabled for RIMA Academy integration.
        // Setup is handled by RIMA Installer.
    }
    
    /**
     * Check if WooCommerce is active
     */
    public function check_dependencies() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            add_action( 'admin_notices', [ $this, 'woocommerce_missing_notice' ] );
            return false;
        }
        return true;
    }
    
    /**
     * WooCommerce missing notice
     */
    public function woocommerce_missing_notice() {
        ?>
        <div class="notice notice-error">
            <p><?php esc_html_e( 'Manager Adrese Multiple necesită WooCommerce pentru a funcționa.', 'ana-addresses' ); ?></p>
        </div>
        <?php
    }
    
    /**
     * Enqueue assets
     */
    public function enqueue_assets() {
        if ( is_checkout() || is_account_page() ) {
            wp_enqueue_style(
                'ana-addresses',
                ANA_ADDR_PLUGIN_URL . 'assets/css/addresses.css',
                [],
                ANA_ADDR_VERSION
            );
            
            wp_enqueue_script(
                'ana-addresses',
                ANA_ADDR_PLUGIN_URL . 'assets/js/addresses.js',
                [ 'jquery' ],
                ANA_ADDR_VERSION,
                true
            );
            
            // Frontend address management
            if (is_user_logged_in()) {
                wp_enqueue_script(
                    'ana-addresses-frontend',
                    ANA_ADDR_PLUGIN_URL . 'assets/js/frontend.js',
                    [ 'jquery' ],
                    ANA_ADDR_VERSION,
                    true
                );
                
                wp_localize_script('ana-addresses-frontend', 'anaAddressesFrontend', [
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('ana_frontend_nonce'),
                    'messages' => [
                        'saveSuccess' => __('Adresa a fost salvată', 'ana-addresses'),
                        'saveError' => __('Eroare la salvare', 'ana-addresses'),
                        'deleteConfirm' => __('Sigur vrei să ștergi această adresă?', 'ana-addresses'),
                        'deleteSuccess' => __('Adresa a fost ștearsă', 'ana-addresses'),
                    ]
                ]);
            }
            
            wp_localize_script( 'ana-addresses', 'anaAddresses', [
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'woocommerce-process_checkout' ),
                'i18n' => [
                    'confirmDelete' => __( 'Sigur vrei să ștergi această adresă?', 'ana-addresses' ),
                    'errorLoad' => __( 'Eroare la încărcarea adreselor.', 'ana-addresses' ),
                    'errorSave' => __( 'Eroare la salvarea adresei.', 'ana-addresses' ),
                    'errorDelete' => __( 'Eroare la ștergerea adresei.', 'ana-addresses' ),
                ]
            ]);
        }
    }
    
    /**
     * Save selected address to order meta
     */
    public function save_selected_address_to_order( $order, $data ) {
        if ( ! empty( $_POST['selected_billing_address_id'] ) ) {
            $billing_id = sanitize_text_field( $_POST['selected_billing_address_id'] );
            $order->update_meta_data( '_billing_address_id', $billing_id );
            
            // Also save full address details
            if ( is_numeric( $billing_id ) ) {
                $address = ANA_Addresses_Plugin::get_address( get_current_user_id(), $billing_id, 'billing' );
                if ( $address ) {
                    $order->update_meta_data( '_billing_address_data', $address );
                }
            }
        }
        
        if ( ! empty( $_POST['selected_shipping_address_id'] ) ) {
            $shipping_id = sanitize_text_field( $_POST['selected_shipping_address_id'] );
            $order->update_meta_data( '_shipping_address_id', $shipping_id );
            
            // Also save full address details
            if ( is_numeric( $shipping_id ) ) {
                $address = ANA_Addresses_Plugin::get_address( get_current_user_id(), $shipping_id, 'shipping' );
                if ( $address ) {
                    $order->update_meta_data( '_shipping_address_data', $address );
                }
            }
        }
    }
}

/**
 * Check database version on plugin load
 */
function ana_addresses_check_version() {
    if ( class_exists( 'ANA_Addresses_Database' ) ) {
        ANA_Addresses_Database::maybe_update();
    }
}
// Run this late on plugins_loaded or directly from rima-academy

/**
 * Initialize module
 */
function ANA_Addresses_Manager() {
    return ANA_Addresses_Manager_Plugin::instance();
}

// Start module
ANA_Addresses_Manager();



