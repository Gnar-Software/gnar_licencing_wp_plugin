<?php

class gnar_api {

    /**
     * Authenticate
     * 
     * @return string token
     */
    private static function authenticate() {

        if (!session_id()) {
            session_start();
        }

        // check if token already exists
        if (!empty($_SESSION['gnar_licensing_api_token'])) {
            $token = $_SESSION['gnar_licensing_api_token'];

            return $token;
        }

        // otherwise get new token
        $apiKey = get_option('gnar_licensing_api_key');

        if (empty($apiKey)) {
            return;
        }

        $response = '';

        $URI = GNRL_GNAR_API_URL . '/authenticate';

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];

        $body = (object) [
            'api_key' => $apiKey
        ];
        
        $ch = curl_init($URI);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $response = curl_exec($ch);
        curl_close($ch);

        $responseObj = json_decode($response);

        $token = $responseObj->token;
        $_SESSION['gnar_licensing_api_token'] = $token;

        return $token;

    }


    /**
     * Get request
     * 
     * @param array $params
     * @param string $route
     * @return object $response
     */
    public static function getRequest($params, $route) {
        $response = '';

        $token = gnar_api::authenticate();

        if (empty($token)) {
            return (object) [
                'error' => 'could not authenticate with the gnar api, please ensure you have entered your API key in the settings page'
            ];
        }

        $URI = GNRL_GNAR_API_URL . $route;

        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $token
        ];
        
        $ch = curl_init($URI);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }


    /**
     * Post request
     * 
     * @param object body
     * @param string route
     * @return object response
     */
    public static function postRequest($body, $route) {
        $response = '';

        $token = gnar_api::authenticate();

        if (empty($token)) {
            return (object) [
                'error' => 'could not authenticate with the gnar api'
            ];
        }

        $URI = GNRL_GNAR_API_URL . $route;

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ];
        
        $ch = curl_init($URI);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }

}

?>