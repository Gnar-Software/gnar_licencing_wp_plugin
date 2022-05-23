<?php

class gnar_api {

    /**
     * Authorize
     */
    private function authenticate() {

        // check if token already exists

        // otherwise get new token

        // store token as session var

        // return jwt

    }


    /**
     * Get request
     */
    public static function getRequest() {
        
    }


    /**
     * Post request
     * 
     * @param object body
     * @return string json response
     */
    public static function postRequest($body) {
        $response = '';

        $token = $this->authenticate();

        $URI = GNRL_GNAR_API_URL . GNRL_GNAR_API_LICENCE_ROUTE;

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
            'GoCardless-Version: ' . GC_API_VERSION
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