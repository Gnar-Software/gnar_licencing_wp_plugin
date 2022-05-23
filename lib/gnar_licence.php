<?php

class gnar_licence {

    // single licence properties
    public string $licenceKey;
    public string $licenceDomain;
    public string $customerEmail;
    public string $orderID;
    public string $purchaseDate;
    public string $lastActivationDate;

    // this is used to hold array of single gnar_licence objects
    public array $licences;

    // this is used to hold licence api errors
    public string $error;


    /**
     * Create new licence
     */
    public function createLicence() {

        gnar_api::postRequest();

        // handle errors
    }


    /**
     * Get all licences
     */
    public function getAllLicences() {

        // // get all licenses

        // // foreach licence
        // $licence = new gnar_licence();
        // $licence->licenceKey = '';
        // // ...
        // array_push($this->licences, $licence);


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