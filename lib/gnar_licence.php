<?php

class gnar_licence {

    // single licence properties
    public int $licenceID;
    public string $licenceKey;
    public string $domain;
    public string $customerEmail;
    public string $softwareID;
    public string $status;
    public string $orderID;
    public string $createdDate;
    public string $lastVerificationDate;

    // this is used to hold licence api errors
    public string $error;


    /**
     * Create new licence
     * 
     * @param string $customerEmail
     * @param string $softwareID
     * @param string $status
     * @return gnar_licence
     */
    public static function createLicence($customerEmail, $softwareID, $status) {

        $body = (object) [
            'customer_email' => $customerEmail,
            'status' => $status,
            'software_id' => $softwareID
        ];

        $route = '/licence/create';

        $responseObj = gnar_api::postRequest($body, $route);

        if (empty($responseObj->licence)) {
            return;
        }

        $respLicence = $responseObj->licence;

        $licence = new gnar_licence();

        (isset($respLicence->id))             ? $licence->licenceID = $respLicence->id : $licence->licenceID = '';
        (isset($respLicence->licence_key))    ? $licence->licenceKey = $respLicence->licence_key : $licence->licenceKey = '';
        (isset($respLicence->customer_email)) ? $licence->customerEmail = $respLicence->customer_email : $licence->customerEmail = '';
        (isset($respLicence->software_id))    ? $licence->softwareID = $respLicence->software_id : $licence->softwareID = '';
        (isset($respLicence->status))         ? $licence->status = $respLicence->status : $licence->status = '';
        (isset($respLicence->domain))         ? $licence->domain = $respLicence->domain : $licence->domain = '';
        (isset($respLicence->created_at))     ? $licence->createdDate = $respLicence->created_at : $licence->createdDate = '';
        (isset($respLicence->last_verified))  ? $licence->lastVerificationDate = $respLicence->last_verified : $licence->lastVerificationDate = '';

        // todo - get order id from woocom
        $licence->orderID = '';

        return $licence;
    }


    /**
     * Get all licences
     * 
     * @return array of gnar_licence
     */
    public static function getAllLicences() {

        $licences = [];

        // get all licenses
        $response = gnar_api::getRequest(
            $params = null,
            $route = '/licence/all'
        );

        if (empty($response)) {
            return (object) [
                'error' => 'an unknown error has occured'
            ];
        }

        if (!empty($response->error)) {
            return $response;
        }

        foreach ($response->licences as $respLicence) {
            $licence = new gnar_licence();

            (isset($respLicence->id))             ? $licence->licenceID = $respLicence->id : $licence->licenceID = '';
            (isset($respLicence->licence_key))    ? $licence->licenceKey = $respLicence->licence_key : $licence->licenceKey = '';
            (isset($respLicence->customer_email)) ? $licence->customerEmail = $respLicence->customer_email : $licence->customerEmail = '';
            (isset($respLicence->software_id))    ? $licence->softwareID = $respLicence->software_id : $licence->softwareID = '';
            (isset($respLicence->status))         ? $licence->status = $respLicence->status : $licence->status = '';
            (isset($respLicence->domain))         ? $licence->domain = $respLicence->domain : $licence->domain = '';
            (isset($respLicence->created_at))     ? $licence->createdDate = $respLicence->created_at : $licence->createdDate = '';
            (isset($respLicence->last_verified))  ? $licence->lastVerificationDate = $respLicence->last_verified : $licence->lastVerificationDate = '';

            // todo - get order id from woocom
            $licence->orderID = '';

            array_push($licences, $licence);
        }

        return $licences;

    }


    /**
     * Get users licences
     */
    public function getUserLicence($email) {

    }


    /**
     * Get licence by licence key
     */

    public function getLicence($licenceKey) {

    }


}

?>