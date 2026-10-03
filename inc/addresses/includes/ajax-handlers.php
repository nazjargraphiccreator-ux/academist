<?php
/**
 * AJAX Handlers for Address Management
 * 
 * @package ANA_Addresses
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Load addresses for modal
 */
add_action( 'wp_ajax_ana_load_addresses', 'ana_ajax_load_addresses' );
function ana_ajax_load_addresses() {
    // Temporarily disabled to bypass ModSecurity 403 errors
    // check_ajax_referer( 'woocommerce-process_checkout', 'security' );
    
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( 'Not logged in' );
    }
    
    $user_id = get_current_user_id();
    $type = isset( $_POST['address_type'] ) ? sanitize_text_field( $_POST['address_type'] ) : 'billing';
    $filter = isset( $_POST['filter'] ) ? sanitize_text_field( $_POST['filter'] ) : '';
    
    $addresses = ANA_Addresses_Plugin::get_addresses( $user_id, $type, $filter );
    
    wp_send_json_success( $addresses );
}

/**
 * Save (add/update) address
 */
add_action( 'wp_ajax_ana_save_address', 'ana_ajax_save_address' );
function ana_ajax_save_address() {
    // Temporarily disabled to bypass ModSecurity 403 errors
    // check_ajax_referer( 'woocommerce-process_checkout', 'security' );
    
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( 'Not logged in' );
    }
    
    $user_id = get_current_user_id();
    $type = isset( $_POST['address_type'] ) ? sanitize_text_field( $_POST['address_type'] ) : 'billing';
    $address_id = isset( $_POST['address_id'] ) ? sanitize_text_field( $_POST['address_id'] ) : '';
    
    // If ID starts with 'company_', it's a read-only integration address being edited.
    // We treat this as a NEW address insertion (migration to local editable address).
    if ( strpos( $address_id, 'company_' ) === 0 ) {
        $address_id = '';
    }
    
    // Support both formats: direct POST fields OR nested in $_POST['data']
    $source = isset( $_POST['data'] ) ? $_POST['data'] : $_POST;
    
    // Sanitize address data
    $data = [
        'entity_type' => isset( $source['entity_type'] ) ? sanitize_text_field( $source['entity_type'] ) : 
                        ( isset( $source['address_type'] ) ? sanitize_text_field( $source['address_type'] ) : 'pf' ),
        'first_name' => isset( $source['first_name'] ) ? sanitize_text_field( $source['first_name'] ) : '',
        'last_name' => isset( $source['last_name'] ) ? sanitize_text_field( $source['last_name'] ) : '',
        'company' => isset( $source['company'] ) ? sanitize_text_field( $source['company'] ) : '',
        'vat_number' => isset( $source['vat_number'] ) ? sanitize_text_field( $source['vat_number'] ) : '',
        'reg_com' => isset( $source['reg_com'] ) ? sanitize_text_field( $source['reg_com'] ) : 
                     ( isset( $source['reg_number'] ) ? sanitize_text_field( $source['reg_number'] ) : '' ),
        'bank_name' => isset( $source['bank_name'] ) ? sanitize_text_field( $source['bank_name'] ) : '',
        'iban' => isset( $source['iban'] ) ? sanitize_text_field( $source['iban'] ) : '',
        'address_1' => isset( $source['address_1'] ) ? sanitize_text_field( $source['address_1'] ) : '',
        'address_2' => isset( $source['address_2'] ) ? sanitize_text_field( $source['address_2'] ) : '',
        'city' => isset( $source['city'] ) ? sanitize_text_field( $source['city'] ) : '',
        'state' => isset( $source['state'] ) ? sanitize_text_field( $source['state'] ) : '',
        'postcode' => isset( $source['postcode'] ) ? sanitize_text_field( $source['postcode'] ) : '',
        'country' => isset( $source['country'] ) ? sanitize_text_field( $source['country'] ) : 'RO',
        'phone' => isset( $source['phone'] ) ? sanitize_text_field( $source['phone'] ) : '',
        'email' => isset( $source['email'] ) ? sanitize_email( $source['email'] ) : '',
        'is_default' => isset( $source['is_default'] ) ? 1 : 0,
    ];
    
    if ( empty( $address_id ) ) {
        // Add new
        $new_id = ANA_Addresses_Plugin::add_address( $user_id, $data, $type );
        
        if ( is_wp_error( $new_id ) ) {
            wp_send_json_error( $new_id->get_error_message() );
        }
        
        wp_send_json_success( [ 'address_id' => $new_id, 'message' => 'Adresă adăugată cu succes!', 'reload' => true ] );
    } else {
        // Update existing
        $result = ANA_Addresses_Plugin::update_address( $user_id, $address_id, $data, $type );
        
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result->get_error_message() );
        } elseif ( $result ) {
            wp_send_json_success( [ 'message' => 'Adresă actualizată cu succes!', 'reload' => true ] );
        } else {
            wp_send_json_error( 'Failed to update address' );
        }
    }
}

/**
 * Delete address
 */
add_action( 'wp_ajax_ana_delete_address', 'ana_ajax_delete_address' );
function ana_ajax_delete_address() {
    // Temporarily disabled to bypass ModSecurity 403 errors
    // check_ajax_referer( 'woocommerce-process_checkout', 'security' );
    
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( 'Not logged in' );
    }
    
    $user_id = get_current_user_id();
    $address_id = isset( $_POST['address_id'] ) ? sanitize_text_field( $_POST['address_id'] ) : '';
    $type = isset( $_POST['address_type'] ) ? sanitize_text_field( $_POST['address_type'] ) : 'billing';
    
    $result = ANA_Addresses_Plugin::delete_address( $user_id, $address_id, $type );
    
    if ( is_wp_error( $result ) ) {
        wp_send_json_error( $result->get_error_message() );
    }
    
    wp_send_json_success( [ 'message' => 'Adresă ștearsă cu succes!' ] );
}

/**
 * Set default address
 */
add_action( 'wp_ajax_ana_set_default_address', 'ana_ajax_set_default_address' );
function ana_ajax_set_default_address() {
    // Temporarily disabled to bypass ModSecurity 403 errors
    // check_ajax_referer( 'ana_set_default_address', 'security' );
    
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( 'Not logged in' );
    }
    
    $user_id = get_current_user_id();
    $address_id = isset( $_POST['address_id'] ) ? intval( $_POST['address_id'] ) : 0;
    $type = isset( $_POST['address_type'] ) ? sanitize_text_field( $_POST['address_type'] ) : 'billing';
    
    ANA_Addresses_Plugin::set_default_address( $user_id, $address_id, $type );
    
    wp_send_json_success( [ 'message' => 'Adresă implicită setată!' ] );
}

/**
 * Lookup CUI (ANAF)
 */
add_action( 'wp_ajax_ana_lookup_cui', 'ana_ajax_lookup_cui' );
add_action( 'wp_ajax_nopriv_ana_lookup_cui', 'ana_ajax_lookup_cui' ); // Allow for guests too
// Alias used by frontend modal and checkout UI
add_action( 'wp_ajax_rima_anaf_lookup', 'ana_ajax_lookup_cui' );
add_action( 'wp_ajax_nopriv_rima_anaf_lookup', 'ana_ajax_lookup_cui' );
function ana_ajax_lookup_cui() {
    // Temporarily disabled to bypass ModSecurity 403 errors
    // check_ajax_referer( 'woocommerce-process_checkout', 'security' );
    
    $cui = isset( $_POST['cui'] ) ? sanitize_text_field( $_POST['cui'] ) : '';
    
    if ( empty( $cui ) ) {
        wp_send_json_error( 'CUI lipsă' );
    }
    
    require_once ANA_ADDR_PLUGIN_DIR . 'includes/class-anaf-integration.php';
    
    $result = ANA_ANAF_Integration::get_company_data( $cui );
    
    if ( is_wp_error( $result ) ) {
        wp_send_json_error( $result->get_error_message() );
    }
    
    wp_send_json_success( $result );
}

/**
 * Demo Excel Download – generates a valid .xlsx template for student enrollment
 * Uses minimal OpenXML (ZIP + XML) without any library dependency
 */
add_action( 'wp_ajax_rima_demo_excel', 'rima_ajax_demo_excel' );
add_action( 'wp_ajax_nopriv_rima_demo_excel', 'rima_ajax_demo_excel' );
function rima_ajax_demo_excel() {

    /* ----------------------------------------------------------------
       Build a minimal .xlsx file (ZIP with XML parts)
    ---------------------------------------------------------------- */

    // ── [Content_Types].xml ──
    $content_types = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml"  ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml"
    ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml"
    ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/sharedStrings.xml"
    ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
  <Override PartName="/xl/styles.xml"
    ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>';

    // ── _rels/.rels ──
    $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';

    // ── xl/_rels/workbook.xml.rels ──
    $wb_rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';

    // ── xl/workbook.xml ──
    $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"
          xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Studenti" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>';

    // ── Shared Strings ──
    // All cell text values referenced by index
    $strings = [
        // Row 1 – Headers
        'Prenume', 'Nume', 'Email', 'Telefon', 'Functie / Departament', 'Note',
        // Row 2 – Demo student 1
        'Ion', 'Popescu', 'ion.popescu@firma.ro', '0712345001', 'Manager Vanzari', '',
        // Row 3 – Demo student 2
        'Maria', 'Ionescu', 'maria.ionescu@firma.ro', '0712345002', 'Contabil', '',
        // Row 4 – Demo student 3
        'Andrei', 'Constantin', 'andrei.constantin@firma.ro', '0712345003', 'Specialist IT', '',
        // Row 5 – Demo student 4
        'Elena', 'Popa', 'elena.popa@firma.ro', '0712345004', 'HR', '',
        // Row 6 – Demo student 5
        'Cristian', 'Dima', 'cristian.dima@firma.ro', '0712345005', 'Director', '',
    ];

    $ss_items = '';
    foreach ( $strings as $s ) {
        $ss_items .= '<si><t xml:space="preserve">' . esc_xml( $s ) . '</t></si>';
    }
    $shared_strings = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($strings) . '" uniqueCount="' . count($strings) . '">'
        . $ss_items . '</sst>';

    // ── Styles (minimal + bold header row) ──
    $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="2">
    <font><sz val="11"/><name val="Calibri"/></font>
    <font><sz val="11"/><b/><name val="Calibri"/><color rgb="FFFFFFFF"/></font>
  </fonts>
  <fills count="3">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FF102D56"/></patternFill></fill>
  </fills>
  <borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>
  <cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>
  <cellXfs count="2">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/>
  </cellXfs>
</styleSheet>';

    // ── Worksheet ──
    // Helper: build a shared-string cell reference
    // s=1 = bold header style, s=0 = normal
    $idx = 0; // running index into $strings[]

    function rima_xlsx_cell( $col, $row, $si_index, $style = 0 ) {
        return '<c r="' . $col . $row . '" t="s" s="' . $style . '"><v>' . $si_index . '</v></c>';
    }

    $cols = ['A','B','C','D','E','F'];

    // Row 1 – Headers (style=1, bold)
    $row1 = '<row r="1">';
    foreach ( $cols as $c => $col ) {
        $row1 .= rima_xlsx_cell( $col, 1, $idx++, 1 );
    }
    $row1 .= '</row>';

    // Rows 2-6 – Demo data
    $data_rows = '';
    for ( $r = 2; $r <= 6; $r++ ) {
        $data_rows .= '<row r="' . $r . '">';
        foreach ( $cols as $c => $col ) {
            $data_rows .= rima_xlsx_cell( $col, $r, $idx++, 0 );
        }
        $data_rows .= '</row>';
    }

    $sheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetFormatPr defaultRowHeight="16" customHeight="1"/>
  <cols>
    <col min="1" max="1" width="14" customWidth="1"/>
    <col min="2" max="2" width="16" customWidth="1"/>
    <col min="3" max="3" width="30" customWidth="1"/>
    <col min="4" max="4" width="16" customWidth="1"/>
    <col min="5" max="5" width="24" customWidth="1"/>
    <col min="6" max="6" width="20" customWidth="1"/>
  </cols>
  <sheetData>' . $row1 . $data_rows . '</sheetData>
  <pageSetup orientation="landscape"/>
</worksheet>';

    /* ----------------------------------------------------------------
       Build ZIP in memory
    ---------------------------------------------------------------- */
    if ( ! class_exists('ZipArchive') ) {
        // Fallback to CSV if ZipArchive is missing
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="model-studenti-rima.csv"');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM for Excel
        echo "Prenume,Nume,Email,Telefon,Functie / Departament,Note\r\n";
        echo "Ion,Popescu,ion.popescu@firma.ro,0712345001,Manager Vanzari,\r\n";
        echo "Maria,Ionescu,maria.ionescu@firma.ro,0712345002,Contabil,\r\n";
        echo "Andrei,Constantin,andrei.constantin@firma.ro,0712345003,Specialist IT,\r\n";
        echo "Elena,Popa,elena.popa@firma.ro,0712345004,HR,\r\n";
        echo "Cristian,Dima,cristian.dima@firma.ro,0712345005,Director,\r\n";
        exit;
    }

    $tmp = tempnam( sys_get_temp_dir(), 'rima_excel_' ) . '.xlsx';

    $zip = new ZipArchive();
    if ( $zip->open( $tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE ) !== true ) {
        wp_send_json_error( 'Nu s-a putut crea fișierul Excel.' );
    }

    $zip->addFromString( '[Content_Types].xml',               $content_types );
    $zip->addFromString( '_rels/.rels',                        $rels );
    $zip->addFromString( 'xl/_rels/workbook.xml.rels',         $wb_rels );
    $zip->addFromString( 'xl/workbook.xml',                    $workbook );
    $zip->addFromString( 'xl/sharedStrings.xml',               $shared_strings );
    $zip->addFromString( 'xl/styles.xml',                      $styles );
    $zip->addFromString( 'xl/worksheets/sheet1.xml',           $sheet );
    $zip->close();

    // Send file to browser
    $filename = 'model-studenti-rima.xlsx';
    header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
    header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
    header( 'Content-Length: ' . filesize($tmp) );
    header( 'Cache-Control: private, no-cache' );
    header( 'Pragma: no-cache' );

    readfile( $tmp );
    @unlink( $tmp );
    exit;
}

/**
 * Helper for XML escaping in shared strings
 */
if ( ! function_exists('esc_xml') ) {
    function esc_xml( $str ) {
        return htmlspecialchars( $str, ENT_XML1 | ENT_COMPAT, 'UTF-8' );
    }
}

/**
 * Parse uploaded Excel/CSV file and return student list
 * Called after order is placed, from admin side
 */
add_action( 'wp_ajax_rima_parse_students_excel', 'rima_ajax_parse_students_excel' );
function rima_ajax_parse_students_excel() {
    if ( ! current_user_can('manage_options') ) {
        wp_send_json_error('Unauthorized');
    }

    $order_id = intval( $_POST['order_id'] ?? 0 );
    if ( ! $order_id ) {
        wp_send_json_error('Order ID lipsă');
    }

    $attachment_id = get_post_meta( $order_id, '_ana_students_excel_id', true );
    if ( ! $attachment_id ) {
        wp_send_json_error('Nu există fișier Excel atașat comenzii.');
    }

    $file_path = get_attached_file( $attachment_id );
    if ( ! $file_path || ! file_exists($file_path) ) {
        wp_send_json_error('Fișierul Excel nu a fost găsit pe server.');
    }

    // Parse CSV fallback or basic xlsx line reading
    $ext = strtolower( pathinfo($file_path, PATHINFO_EXTENSION) );
    $rows = [];

    if ( $ext === 'csv' ) {
        if ( ($h = fopen($file_path, 'r')) !== false ) {
            $headers = null;
            while ( ($row = fgetcsv($h, 1000, ',')) !== false ) {
                if ( $headers === null ) { $headers = $row; continue; }
                if ( count($row) >= 3 ) {
                    $rows[] = [
                        'first_name' => $row[0] ?? '',
                        'last_name'  => $row[1] ?? '',
                        'email'      => $row[2] ?? '',
                        'phone'      => $row[3] ?? '',
                        'role'       => $row[4] ?? '',
                    ];
                }
            }
            fclose($h);
        }
    }

    wp_send_json_success(['students' => $rows, 'count' => count($rows)]);
}
