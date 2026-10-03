<?php
/**
 * ANAF Integration Class
 * 
 * Handles communication with ANAF Web Services to retrieve company details.
 * API Endpoint: https://webservicesp.anaf.ro/api/PlatitorTvaRest/v9/tva
 * 
 * @package ANA_Addresses
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ANA_ANAF_Integration {

    /**
     * API Endpoint URL
     */
    const API_URL = 'https://webservicesp.anaf.ro/api/PlatitorTvaRest/v9/tva';

    /**
     * Get company data by CUI
     * 
     * @param string $cui The CUI to lookup
     * @return array|WP_Error Company data or error
     */
    public static function get_company_data( $cui ) {
        // Clean CUI
        $cui = preg_replace( '/[^0-9]/', '', $cui );
        
        if ( empty( $cui ) ) {
            return new WP_Error( 'invalid_cui', 'CUI invalid.' );
        }

        // Prepare request body
        $body = [
            [
                "cui" => $cui,
                "data" => date( "Y-m-d" )
            ]
        ];

        // Make request
        $response = wp_remote_post( self::API_URL, [
            'body'    => json_encode( $body ),
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'timeout' => 10
        ] );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code( $response );
        if ( $code !== 200 ) {
            return new WP_Error( 'api_error', 'Eroare comunicare ANAF (' . $code . ')' );
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( empty( $data ) || ! isset( $data['found'] ) || empty( $data['found'] ) ) {
            return new WP_Error( 'not_found', 'Firma nu a fost găsită la ANAF.' );
        }

        $company = $data['found'][0];
        
        // Extract relevant data
        $result = [
            'name' => isset( $company['date_generale']['denumire'] ) ? $company['date_generale']['denumire'] : '',
            'reg_com' => isset( $company['date_generale']['nrRegCom'] ) ? $company['date_generale']['nrRegCom'] : '',
            'phone' => isset( $company['date_generale']['telefon'] ) ? $company['date_generale']['telefon'] : '',
            'address' => self::format_address( $company ),
            'city' => isset( $company['adresa_sediu_social']['sdenumire_Localitate'] ) ? $company['adresa_sediu_social']['sdenumire_Localitate'] : '',
            'county' => isset( $company['adresa_sediu_social']['sdenumire_Judet'] ) ? $company['adresa_sediu_social']['sdenumire_Judet'] : '',
            'address_components' => [
                'street' => isset( $company['adresa_sediu_social']['sdenumire_Strada'] ) ? $company['adresa_sediu_social']['sdenumire_Strada'] : '',
                'number' => isset( $company['adresa_sediu_social']['snumar_Strada'] ) ? $company['adresa_sediu_social']['snumar_Strada'] : '',
            ]
        ];

        return $result;
    }

    /**
     * Format address from components
     */
    private static function format_address( $company ) {
        $addr = $company['adresa_sediu_social'];
        $parts = [];
        
        if ( ! empty( $addr['sdenumire_Strada'] ) ) $parts[] = 'Str. ' . $addr['sdenumire_Strada'];
        if ( ! empty( $addr['snumar_Strada'] ) ) $parts[] = 'Nr. ' . $addr['snumar_Strada'];
        if ( ! empty( $addr['sdenumire_Bloc'] ) ) $parts[] = 'Bl. ' . $addr['sdenumire_Bloc'];
        if ( ! empty( $addr['sdenumire_Scara'] ) ) $parts[] = 'Sc. ' . $addr['sdenumire_Scara'];
        if ( ! empty( $addr['sdenumire_Etaj'] ) ) $parts[] = 'Et. ' . $addr['sdenumire_Etaj'];
        if ( ! empty( $addr['sdenumire_Ap'] ) ) $parts[] = 'Ap. ' . $addr['sdenumire_Ap'];
        
        return implode( ', ', $parts );
    }
}
