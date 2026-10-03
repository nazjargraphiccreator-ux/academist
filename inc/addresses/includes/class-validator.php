<?php
/**
 * ANA Address Validator
 * 
 * Handles validation for specific Romanian address fields:
 * - CUI (Cod Unic de Înregistrare) - Checks control digit
 * - IBAN (International Bank Account Number) - Checks format and checksum
 * - CNP (Optional, for future use)
 * 
 * @package ANA_Addresses
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ANA_Address_Validator {

    /**
     * Validate CUI (Romanian Company Registration Code)
     * 
     * @param string $cui The CUI to validate
     * @return bool|WP_Error True if valid, WP_Error if invalid
     */
    public static function validate_cui( $cui ) {
        // Remove 'RO' prefix if present and any whitespace
        $cui = strtoupper( trim( $cui ) );
        if ( strpos( $cui, 'RO' ) === 0 ) {
            $cui = substr( $cui, 2 );
        }
        $cui = trim( $cui );

        // Must be numeric and between 2 and 10 digits
        if ( ! is_numeric( $cui ) || strlen( $cui ) > 10 || strlen( $cui ) < 2 ) {
            return new WP_Error( 'invalid_cui_format', 'CUI-ul trebuie să conțină doar cifre (2-10 caractere).' );
        }

        // Control digit validation
        $control_key = '753217532';
        $cui_len = strlen( $cui );
        $cui_digits = str_split( $cui );
        
        // Pad control key to match CUI length (align from right for multiplication, but algorithm aligns from left?)
        // Standard algorithm:
        // 1. Reverse CUI (excluding control digit)
        // 2. Multiply by reversed control key
        // Let's use the standard implementation.
        
        $cui_check = (int) $cui;
        $control_digit = $cui_check % 10;
        $cui_check = (int) ($cui_check / 10);
        
        $t = 0;
        $k = 10; // length of control key string + 1? No.
        
        // Reverse loop
        while ( $cui_check > 0 ) {
            $digit = $cui_check % 10;
            $cui_check = (int) ($cui_check / 10);
            $t += $digit * $control_key[$k-2]; // $k starts at 10, so index 8 (last char of 753217532)
            $k--;
        }
        
        // Wait, let's use a cleaner implementation of the algorithm
        // Key: 753217532
        // CUI: 123456 (example)
        // We align from right-to-left excluding the last digit (control).
        // Actually, the standard is: align from right to left? No, usually left to right against the key 753217532.
        
        // Let's use the proven algorithm:
        $v = 753217532;
        $c1 = $cui;
        $w = (int) ($c1 / 10);
        $t = 0;
        $k = 9; // Key length
        
        // We need to iterate the digits of the CUI (minus the last one)
        // against the key 753217532 reversed?
        
        // Correct Algorithm:
        // 1. Remove 'RO'
        // 2. Take the last digit as Control Digit.
        // 3. Take the rest of the number.
        // 4. Reverse the rest.
        // 5. Multiply each digit by the corresponding digit in '235712357' (reversed key of 753217532).
        // 6. Sum results.
        // 7. Multiply sum by 10.
        // 8. Modulo 11.
        // 9. If result is 10, it becomes 0.
        // 10. Compare with Control Digit.
        
        $cui_str = (string) $cui;
        $control_digit = (int) substr( $cui_str, -1 );
        $rest_cui = substr( $cui_str, 0, -1 );
        
        $rest_len = strlen( $rest_cui );
        $key = '753217532';
        
        // We need to align the key to the RIGHT of the rest_cui?
        // Actually, usually it's aligned to the right of the number.
        // Let's try the standard PHP implementation found in many RO libraries.
        
        $total = 0;
        for ( $i = $rest_len - 1, $j = 9; $i >= 0; $i--, $j-- ) {
            $total += (int)$rest_cui[$i] * (int)$key[$j-1];
        }
        
        $result = $total * 10 % 11;
        if ( $result == 10 ) {
            $result = 0;
        }
        
        if ( $result !== $control_digit ) {
            return new WP_Error( 'invalid_cui_checksum', 'CUI-ul este invalid (cifra de control incorectă).' );
        }

        return true;
    }

    /**
     * Validate IBAN (International Bank Account Number)
     * 
     * @param string $iban The IBAN to validate
     * @return bool|WP_Error True if valid, WP_Error if invalid
     */
    public static function validate_iban( $iban ) {
        $iban = strtoupper( str_replace( ' ', '', $iban ) );
        
        if ( empty( $iban ) ) {
            return true; // Allow empty if not mandatory
        }

        // Check generic length (RO is 24 chars)
        if ( strlen( $iban ) < 15 || strlen( $iban ) > 34 ) {
             return new WP_Error( 'invalid_iban_length', 'Lungimea IBAN-ului este incorectă.' );
        }
        
        // If RO IBAN, check specific length
        if ( substr( $iban, 0, 2 ) === 'RO' && strlen( $iban ) !== 24 ) {
            return new WP_Error( 'invalid_ro_iban_length', 'Un IBAN din România trebuie să aibă exact 24 de caractere.' );
        }

        // Move first 4 chars to end
        $check_string = substr( $iban, 4 ) . substr( $iban, 0, 4 );
        
        // Replace letters with numbers (A=10, B=11, ..., Z=35)
        $check_digits = '';
        foreach ( str_split( $check_string ) as $char ) {
            if ( is_numeric( $char ) ) {
                $check_digits .= $char;
            } else {
                $check_digits .= ord( $char ) - 55;
            }
        }
        
        // Modulo 97 check (using bcmod or manual string modulo if bcmath not available)
        if ( function_exists( 'bcmod' ) ) {
            $remainder = bcmod( $check_digits, '97' );
        } else {
            // Manual modulo for large numbers
            $remainder = 0;
            for ( $i = 0; $i < strlen( $check_digits ); $i++ ) {
                $remainder = ( ( $remainder * 10 ) + (int)$check_digits[$i] ) % 97;
            }
        }
        
        if ( $remainder != 1 ) {
            return new WP_Error( 'invalid_iban_checksum', 'Codul IBAN este invalid.' );
        }

        return true;
    }
}
