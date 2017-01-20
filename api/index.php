<?php


class Battery_API {


    protected $auth;

    protected $token;

    protected $json;


    function __construct () {
        $this->check_security();

        $this->auth = $this->get_auth_data();
        $this->token = $this->get_token();
        $this->json = $this->get_vehicle_data();

        $this->send_response_json();
    }


    function check_security() {
        if ( empty( $_SERVER['HTTP_REFERER'] ) OR strcmp( parse_url( $_SERVER['HTTP_REFERER'], PHP_URL_HOST ), $_SERVER['SERVER_NAME'] ) !== 0 ) {
            http_response_code( 404 );
            exit;
        }
    }


    function get_auth_data() {
        return json_decode(
            @file_get_contents(
                'auth.json'
            )
        );
    }


    function cache_remote_token( $token_data ) {
        file_put_contents(
            'token.json',
            json_encode( $token_data )
        );
    }


    function get_cached_token() {
        return json_decode(
            @file_get_contents(
                'token.json'
            )
        );
    }


    function get_token() {
        // Get cached token
        if ( $cached_token_data = $this->get_cached_token() ) {
            if ( $cached_token_data->expires > time() ) {
                $token = $cached_token_data->token;
            }
        }

        // Get remote token
        if ( empty( $token ) ) {
            $token_data = $this->get_remote_token();
            $token = $token_data->token;

            $this->cache_remote_token( $token_data );
        }

        return $token;
    }


    function get_remote_token() {
        // Init cURL
        $ch = curl_init();

        // Set cURL options
        curl_setopt( $ch, CURLOPT_URL, 'https://customer.bmwgroup.com/gcdm/oauth/authenticate' );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, false );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_HEADER, true );
        curl_setopt( $ch, CURLOPT_NOBODY, true );
        curl_setopt( $ch, CURLOPT_COOKIESESSION, true );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/x-www-form-urlencoded' ) );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, 'username=' . urlencode( $this->auth->username) . '&password=' . urlencode( $this->auth->password) . '&client_id=dbf0a542-ebd1-4ff0-a9a7-55172fbfce35&redirect_uri=https%3A%2F%2Fwww.bmw-connecteddrive.com%2Fapp%2Fdefault%2Fstatic%2Fexternal-dispatch.html&response_type=token&scope=authenticate_user%20fupo&state=eyJtYXJrZXQiOiJkZSIsImxhbmd1YWdlIjoiZGUiLCJkZXN0aW5hdGlvbiI6ImxhbmRpbmdQYWdlIn0&locale=DE-de' );

        // Exec curl request
        $response = curl_exec( $ch );

        // Close connection
        curl_close( $ch );

        // Extract token
        preg_match( '/access_token=([\w\d]+).*token_type=(\w+).*expires_in=(\d+)/', $response, $matches );

        // Check token type
        if ( empty( $matches[2] ) OR $matches[2] !== 'Bearer' ) {
            http_response_code(503);
            exit;
        }

        return array(
            'token' => $matches[1],
            'expires' => time() + $matches[3]
        );
    }


    function get_vehicle_data() {
        // Init cURL
        $ch = curl_init();

        // Set cURL options
        curl_setopt( $ch, CURLOPT_URL, 'https://www.bmw-connecteddrive.de/api/vehicle/dynamic/v1/' . $this->auth->vehicle . '?offset=-60' );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' , 'Authorization: Bearer ' . $this->token ) );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );

        // Exec curl request
        $response = curl_exec( $ch );

        // Close connection
        curl_close( $ch );

        // Decode response
        $json = json_decode( $response );

        // Exit if error
        if ( json_last_error() ) {
            http_response_code( 503 );
            exit;
        }

        return $json;
    }


    function send_response_json() {
        // Set JSON vars
        $attributes = $this->json->attributesMap;

        $updateTime = $attributes->updateTime_converted;
        $electricRange = intval( $attributes->beRemainingRangeElectricKm );
        $chargingLevel = intval( $attributes->chargingLevelHv );
        $chargingActive = intval( $attributes->chargingSystemStatus === 'CHARGINGACTIVE' );

        $chargingTimeRemaining = intval( $attributes->chargingTimeRemaining );
        $chargingTimeRemaining = ( $chargingTimeRemaining ? ( date( 'H:i', mktime( 0, $chargingTimeRemaining ) ) . ' h' ) : '--:--' );

        // Send Header
        header('Access-Control-Allow-Origin: https://' . $_SERVER['SERVER_NAME'] );
        header('Content-Type: application/json; charset=utf-8');

        // Send JSON
        die(
            json_encode(
                array(
                    'updateTime' => $updateTime,
                    'electricRange' => $electricRange,
                    'chargingLevel' => $chargingLevel,
                    'chargingActive' => $chargingActive,
                    'chargingTimeRemaining' => $chargingTimeRemaining
                )
            )
        );
    }
}


new Battery_API();
