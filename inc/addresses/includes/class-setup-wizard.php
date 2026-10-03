<?php
/**
 * Setup Wizard for ANA Addresses Manager
 * 
 * @package ANA_Addresses
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ANA_Addresses_Setup_Wizard {
    
    private $step = '';
    private $steps = [];
    
    public function __construct() {
        $this->steps = [
            'welcome' => __( 'Bun venit', 'ana-addresses' ),
            'database' => __( 'Bază de Date', 'ana-addresses' ),
            'migration' => __( 'Import Adrese', 'ana-addresses' ),
            'integrations' => __( 'Integrări', 'ana-addresses' ),
            'complete' => __( 'Finalizare', 'ana-addresses' ),
        ];
        
        if ( isset( $_GET['page'] ) && $_GET['page'] === 'ana-addresses-setup' ) {
            add_action( 'admin_menu', [ $this, 'admin_menu' ] );
            add_action( 'admin_init', [ $this, 'setup_wizard' ] );
        }
        
        // AJAX handlers
        add_action( 'wp_ajax_ana_setup_create_table', [ $this, 'ajax_create_table' ] );
        add_action( 'wp_ajax_ana_setup_scan_migration', [ $this, 'ajax_scan_migration' ] );
        add_action( 'wp_ajax_ana_setup_run_migration', [ $this, 'ajax_run_migration' ] );
    }
    
    public function admin_menu() {
        add_dashboard_page( '', '', 'manage_options', 'ana-addresses-setup', '' );
    }
    
    public function setup_wizard() {
        $this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : 'welcome';
        
        ob_start();
        $this->setup_wizard_header();
        $this->setup_wizard_steps();
        $this->setup_wizard_content();
        $this->setup_wizard_footer();
        exit;
    }
    
    public function setup_wizard_header() {
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta name="viewport" content="width=device-width" />
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title><?php esc_html_e( 'Manager Adrese Multiple - Setup', 'ana-addresses' ); ?></title>
            <?php do_action( 'admin_print_styles' ); ?>
            <?php do_action( 'admin_print_scripts' ); ?>
            <?php do_action( 'admin_head' ); ?>
            <style>
                body { margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; background: #f0f0f1; }
                .ana-setup-wizard { max-width: 800px; margin: 50px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 20px rgba(0,0,0,0.1); }
                .ana-setup-header { padding: 30px; text-align: center; border-bottom: 1px solid #e5e5e5; }
                .ana-setup-header h1 { margin: 0 0 10px; font-size: 28px; color: #1d2327; }
                .ana-setup-header p { margin: 0; color: #646970; }
                .ana-setup-steps { display: flex; justify-content: space-between; padding: 0 30px; margin: 20px 0; }
                .ana-setup-step { flex: 1; text-align: center; position: relative; }
                .ana-setup-step:not(:last-child)::after { content: ''; position: absolute; top: 15px; left: 50%; width: 100%; height: 2px; background: #dcdcde; z-index: 0; }
                .ana-setup-step-number { width: 30px; height: 30px; border-radius: 50%; background: #dcdcde; color: #50575e; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; position: relative; z-index: 1; margin-bottom: 5px; }
                .ana-setup-step.active .ana-setup-step-number { background: #2271b1; color: #fff; }
                .ana-setup-step.completed .ana-setup-step-number { background: #00a32a; color: #fff; }
                .ana-setup-step-label { font-size: 12px; color: #50575e; }
                .ana-setup-content { padding: 40px; min-height: 400px; }
                .ana-setup-actions { padding: 20px 40px; border-top: 1px solid #e5e5e5; text-align: right; background: #f6f7f7; }
                .button { display: inline-block; padding: 10px 20px; text-decoration: none; border-radius: 3px; cursor: pointer; border: none; font-size: 14px; }
                .button-primary { background: #2271b1; color: #fff; }
                .button-secondary { background: #f0f0f1; color: #2c3338; }
                .button-primary:hover { background: #135e96; }
                .ana-progress-bar { width: 100%; height: 30px; background: #f0f0f1; border-radius: 15px; overflow: hidden; margin: 20px 0; }
                .ana-progress-fill { height: 100%; background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); transition: width 0.3s ease; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 600; font-size: 12px; }
                .ana-form-table { width: 100%; }
                .ana-form-table th { text-align: left; padding: 15px 0; width: 200px; vertical-align: top; }
                .ana-form-table td { padding: 15px 0; }
                .ana-form-table input[type="text"] { width: 100%; max-width: 400px; padding: 8px 12px; }
                .ana-info-box { background: #e7f3ff; border-left: 4px solid #2271b1; padding: 15px; margin: 20px 0; }
                .ana-success-box { background: #d5f5e3; border-left: 4px solid #00a32a; padding: 15px; margin: 20px 0; }
                .ana-warning-box { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; }
            </style>
        </head>
        <body class="ana-setup">
            <div class="ana-setup-wizard">
                <div class="ana-setup-header">
                    <h1><?php esc_html_e( 'Manager de adrese multiple', 'ana-addresses' ); ?></h1>
                    <p><?php esc_html_e( 'Configurare inițială a plugin-ului', 'ana-addresses' ); ?></p>
                </div>
        <?php
    }
    
    public function setup_wizard_steps() {
        $current_step_index = array_search( $this->step, array_keys( $this->steps ) );
        ?>
        <div class="ana-setup-steps">
            <?php foreach ( $this->steps as $step_key => $step_label ) : 
                $step_index = array_search( $step_key, array_keys( $this->steps ) );
                $class = '';
                if ( $step_index < $current_step_index ) {
                    $class = 'completed';
                } elseif ( $step_key === $this->step ) {
                    $class = 'active';
                }
            ?>
                <div class="ana-setup-step <?php echo esc_attr( $class ); ?>">
                    <div class="ana-setup-step-number"><?php echo $step_index + 1; ?></div>
                    <div class="ana-setup-step-label"><?php echo esc_html( $step_label ); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
    
    public function setup_wizard_content() {
        echo '<div class="ana-setup-content">';
        
        switch ( $this->step ) {
            case 'welcome':
                $this->step_welcome();
                break;
            case 'database':
                $this->step_database();
                break;
            case 'migration':
                $this->step_migration();
                break;
            case 'integrations':
                $this->step_integrations();
                break;
            case 'complete':
                $this->step_complete();
                break;
        }
        
        echo '</div>';
    }
    
    public function setup_wizard_footer() {
        echo '</div>'; // .ana-setup-wizard
        ?>
        <script>
            jQuery(document).ready(function($) {
                // AJAX handlers will be added here
            });
        </script>
        </body>
        </html>
        <?php
    }
    
    private function step_welcome() {
        ?>
        <h2>Bun venit la Manager de adrese multiple!</h2>
        <p>Acest wizard te va ghida prin configurarea inițială a plugin-ului pentru gestionarea adreselor multiple.</p>
        
        <div class="ana-info-box">
            <h3>Ce vei configura:</h3>
            <ul>
                <li>✓ Baza de date pentru stocarea adreselor</li>
                <li>✓ Import automat al adreselor existente</li>
                <li>✓ Integrări cu WooCommerce, Cargus și Banca Transilvania</li>
            </ul>
        </div>
        
        <h3>Verificare Cerințe</h3>
        <table class="ana-form-table">
            <tr>
                <th>WordPress:</th>
                <td><?php echo get_bloginfo( 'version' ); ?> <?php echo version_compare( get_bloginfo( 'version' ), '5.8', '>=' ) ? '✓' : '✗'; ?></td>
            </tr>
            <tr>
                <th>PHP:</th>
                <td><?php echo PHP_VERSION; ?> <?php echo version_compare( PHP_VERSION, '7.4', '>=' ) ? '✓' : '✗'; ?></td>
            </tr>
            <tr>
                <th>WooCommerce:</th>
                <td><?php echo class_exists( 'WooCommerce' ) ? '✓ Instalat' : '✗ Nu este instalat'; ?></td>
            </tr>
        </table>
        
        <div class="ana-setup-actions">
            <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-primary">Începe Configurarea →</a>
        </div>
        <?php
    }
    
    private function step_database() {
        global $wpdb;
        $default_prefix = $wpdb->prefix;
        $saved_prefix = get_option( 'ana_addresses_db_prefix', $default_prefix );
        ?>
        <h2>Configurare Bază de Date</h2>
        <p>Alege prefix-ul pentru tabelul de adrese. Poți folosește prefix-ul WordPress standard sau unul personalizat.</p>
        
        <form method="post" id="ana-database-form">
            <?php wp_nonce_field( 'ana_setup_database' ); ?>
            <table class="ana-form-table">
                <tr>
                    <th><label for="db_prefix">Prefix Tabel:</label></th>
                    <td>
                        <input type="text" id="db_prefix" name="db_prefix" value="<?php echo esc_attr( $saved_prefix ); ?>" placeholder="<?php echo esc_attr( $default_prefix ); ?>">
                        <p class="description">Tabelul va fi: <code><span id="table-preview"><?php echo esc_html( $saved_prefix ); ?>ana_user_addresses</span></code></p>
                    </td>
                </tr>
            </table>
            
            <div class="ana-info-box">
                <strong>Recomandare:</strong> Folosește prefix-ul standard WordPress (<code><?php echo esc_html( $default_prefix ); ?></code>) dacă nu ai motive specifice să-l schimbi.
            </div>
            
            <div id="table-creation-status"></div>
            
            <div class="ana-setup-actions">
                <a href="<?php echo esc_url( $this->get_prev_step_link() ); ?>" class="button button-secondary">← Înapoi</a>
                <button type="submit" class="button button-primary" id="create-table-btn">Creează Tabelul →</button>
            </div>
        </form>
        
        <script>
            jQuery(document).ready(function($) {
                $('#db_prefix').on('input', function() {
                    $('#table-preview').text($(this).val() + 'ana_user_addresses');
                });
                
                $('#ana-database-form').on('submit', function(e) {
                    e.preventDefault();
                    const prefix = $('#db_prefix').val();
                    const $btn = $('#create-table-btn');
                    const $status = $('#table-creation-status');
                    
                    $btn.prop('disabled', true).text('Se creează...');
                    $status.html('<div class="ana-progress-bar"><div class="ana-progress-fill" style="width: 50%">Se creează tabelul...</div></div>');
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'ana_setup_create_table',
                            prefix: prefix,
                            _wpnonce: '<?php echo wp_create_nonce( 'ana_setup_create_table' ); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                $status.html('<div class="ana-success-box">✓ Tabelul a fost creat cu succes!</div>');
                                setTimeout(function() {
                                    window.location.href = '<?php echo esc_js( $this->get_next_step_link() ); ?>';
                                }, 1000);
                            } else {
                                $status.html('<div class="ana-warning-box">✗ Eroare: ' + response.data + '</div>');
                                $btn.prop('disabled', false).text('Încearcă din nou');
                            }
                        }
                    });
                });
            });
        </script>
        <?php
    }
    
    private function step_migration() {
        ?>
        <h2>Import Adrese Existente</h2>
        <p>Scanează și importă adresele existente din user meta în noul sistem de bază de date.</p>
        
        <div id="migration-scanner">
            <button type="button" class="button button-primary" id="scan-addresses-btn">Scanează Adresele</button>
        </div>
        
        <div id="migration-results" style="display:none;">
            <div class="ana-info-box">
                <h3>Rezultate Scanare:</h3>
                <p>Utilizatori găsiți: <strong id="users-count">0</strong></p>
                <p>Adrese găsite: <strong id="addresses-count">0</strong></p>
            </div>
            
            <div class="ana-form-table">
                <label>
                    <input type="checkbox" id="keep-meta-backup" checked>
                    Păstrează adresele originale din user meta ca backup
                </label>
            </div>
            
            <button type="button" class="button button-primary" id="start-migration-btn">Începe Importul</button>
        </div>
        
        <div id="migration-progress" style="display:none;">
            <div class="ana-progress-bar">
                <div class="ana-progress-fill" id="migration-progress-bar" style="width: 0%">0%</div>
            </div>
            <p id="migration-status">Procesare...</p>
        </div>
        
        <div class="ana-setup-actions">
            <a href="<?php echo esc_url( $this->get_prev_step_link() ); ?>" class="button button-secondary">← Înapoi</a>
            <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-primary" id="skip-migration-btn">Sari peste →</a>
        </div>
        
        <script>
            jQuery(document).ready(function($) {
                $('#scan-addresses-btn').on('click', function() {
                    const $btn = $(this);
                    $btn.prop('disabled', true).text('Se scanează...');
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'ana_setup_scan_migration',
                            _wpnonce: '<?php echo wp_create_nonce( 'ana_setup_scan' ); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#users-count').text(response.data.users);
                                $('#addresses-count').text(response.data.addresses);
                                $('#migration-scanner').hide();
                                $('#migration-results').show();
                            }
                        }
                    });
                });
                
                $('#start-migration-btn').on('click', function() {
                    $('#migration-results').hide();
                    $('#migration-progress').show();
                    $('#skip-migration-btn').hide();
                    
                    runMigration(0);
                });
                
                function runMigration(offset) {
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'ana_setup_run_migration',
                            offset: offset,
                            keep_backup: $('#keep-meta-backup').is(':checked') ? 1 : 0,
                            _wpnonce: '<?php echo wp_create_nonce( 'ana_setup_migrate' ); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                const progress = response.data.progress;
                                $('#migration-progress-bar').css('width', progress + '%').text(progress + '%');
                                $('#migration-status').text('Procesat: ' + response.data.processed + ' utilizatori');
                                
                                if (!response.data.complete) {
                                    runMigration(response.data.offset);
                                } else {
                                    setTimeout(function() {
                                        window.location.href = '<?php echo esc_js( $this->get_next_step_link() ); ?>';
                                    }, 1000);
                                }
                            }
                        }
                    });
                }
            });
        </script>
        <?php
    }
    
    private function step_integrations() {
        ?>
        <h2>Configurare Integrări</h2>
        <p>Activează integrările cu sistemele externe.</p>
        
        <form method="post" action="<?php echo esc_url( $this->get_next_step_link() ); ?>">
            <?php wp_nonce_field( 'ana_setup_integrations' ); ?>
            
            <h3>WooCommerce</h3>
            <table class="ana-form-table">
                <tr>
                    <th>Status:</th>
                    <td><?php echo class_exists( 'WooCommerce' ) ? '<span style="color: green;">✓ Detectat automat</span>' : '<span style="color: red;">✗ Nu este instalat</span>'; ?></td>
                </tr>
            </table>
            
            <h3>Cargus Shipping</h3>
            <div class="ana-info-box">
                ✓ API Cargus este deja configurat în sistemul tău WooCommerce. Adresele din plugin vor fi folosite automat pentru generarea AWB-urilor.
            </div>
            
            <h3>Banca Transilvania</h3>
            <div class="ana-info-box">
                ✓ Integrare automată cu plățile online Banca Transilvania. Adresele de facturare vor fi transmise automat la procesarea plăților.
            </div>
            
            <div class="ana-setup-actions">
                <a href="<?php echo esc_url( $this->get_prev_step_link() ); ?>" class="button button-secondary">← Înapoi</a>
                <button type="submit" class="button button-primary">Finalizează Setup →</button>
            </div>
        </form>
        <?php
    }
    
    private function step_complete() {
        update_option( 'ana_addresses_setup_completed', true );
        ?>
        <h2>✓ Configurare Completă!</h2>
        <p>Plugin-ul Manager de adrese multiple a fost configurat cu succes.</p>
        
        <div class="ana-success-box">
            <h3>Ce urmează:</h3>
            <ul>
                <li>Accesează dashboard-ul pentru a vedea statistici</li>
                <li>Gestionează adresele utilizatorilor din panoul admin</li>
                <li>Utilizatorii pot adăuga adrese multiple din My Account</li>
                <li>La checkout, utilizatorii pot selecta din adresele salvate</li>
            </ul>
        </div>
        
        <div class="ana-setup-actions">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=ana-addresses-manager' ) ); ?>" class="button button-primary">Mergi la Dashboard →</a>
        </div>
        <?php
    }
    
    // AJAX Handlers
    public function ajax_create_table() {
        check_ajax_referer( 'ana_setup_create_table' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Permisiuni insuficiente' );
        }
        
        $prefix = isset( $_POST['prefix'] ) ? sanitize_text_field( $_POST['prefix'] ) : '';
        
        if ( empty( $prefix ) ) {
            global $wpdb;
            $prefix = $wpdb->prefix;
        }
        
        update_option( 'ana_addresses_db_prefix', $prefix );
        
        ANA_Addresses_Database::create_table();
        
        wp_send_json_success();
    }
    
    public function ajax_scan_migration() {
        check_ajax_referer( 'ana_setup_scan' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error();
        }
        
        global $wpdb;
        
        $users = get_users( [ 'fields' => 'ID' ] );
        $total_addresses = 0;
        $users_with_addresses = 0;
        
        foreach ( $users as $user_id ) {
            $billing = get_user_meta( $user_id, '_ana_billing_addresses', true );
            $shipping = get_user_meta( $user_id, '_ana_shipping_addresses', true );
            
            if ( is_array( $billing ) ) {
                $total_addresses += count( $billing );
                $users_with_addresses++;
            }
            if ( is_array( $shipping ) ) {
                $total_addresses += count( $shipping );
            }
        }
        
        wp_send_json_success( [
            'users' => $users_with_addresses,
            'addresses' => $total_addresses
        ] );
    }
    
    public function ajax_run_migration() {
        check_ajax_referer( 'ana_setup_migrate' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error();
        }
        
        $offset = isset( $_POST['offset'] ) ? intval( $_POST['offset'] ) : 0;
        $batch_size = 20;
        
        $users = get_users( [
            'number' => $batch_size,
            'offset' => $offset,
            'fields' => 'ID'
        ] );
        
        $migrated = 0;
        foreach ( $users as $user_id ) {
            // Migration logic will use ANA_Addresses_Plugin::migrate_user_addresses()
            $result = ANA_Addresses_Plugin::get_addresses( $user_id, 'billing' );
            if ( ! empty( $result ) ) {
                $migrated++;
            }
        }
        
        $total_users = count( get_users( [ 'fields' => 'ID' ] ) );
        $processed = $offset + count( $users );
        $progress = min( 100, round( ( $processed / $total_users ) * 100 ) );
        
        wp_send_json_success( [
            'complete' => $processed >= $total_users,
            'progress' => $progress,
            'processed' => $processed,
            'offset' => $processed
        ] );
    }
    
    private function get_next_step_link() {
        $keys = array_keys( $this->steps );
        $current_index = array_search( $this->step, $keys );
        $next_step = isset( $keys[ $current_index + 1 ] ) ? $keys[ $current_index + 1 ] : '';
        
        if ( $next_step ) {
            return admin_url( 'index.php?page=ana-addresses-setup&step=' . $next_step );
        }
        
        return admin_url( 'admin.php?page=ana-addresses-manager' );
    }
    
    private function get_prev_step_link() {
        $keys = array_keys( $this->steps );
        $current_index = array_search( $this->step, $keys );
        $prev_step = isset( $keys[ $current_index - 1 ] ) ? $keys[ $current_index - 1 ] : '';
        
        if ( $prev_step ) {
            return admin_url( 'index.php?page=ana-addresses-setup&step=' . $prev_step );
        }
        
        return admin_url( 'admin.php?page=ana-addresses-manager' );
    }
}

// Initialize setup wizard
new ANA_Addresses_Setup_Wizard();
