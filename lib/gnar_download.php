<?php

class gnar_download {

    /**
     * Get download link
     * 
     * @param string $softwareID
     * @return string $downloadLink
     */
    public static function downloadLink($softwareID) {

        $token = gnar_download::getToken();
        $downloadLink = GNRL_GNAR_API_URL . '/download/' . $softwareID . '?download_token=' . $token;
        
        return $downloadLink;
    }

    /**
     * Get single use download token
     * 
     * @return string $downloadToken
     */
    public static function getToken() {

        $tokenResponse = gnar_api::getRequest('', '/download_token');

        if (empty($tokenResponse) || empty($tokenResponse->download_token)) {
            error_log('error recieving download token');
            return;
        }

        return $tokenResponse->download_token;
    }

}

?>