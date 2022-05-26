<?php

class gnar_woocom {

    public function __construct() {

        $this->addProductFields();
        $this->addPurchaseHooks();

        // todo display licence key and download link on order recieved page
        add_action('woocommerce_thankyou', [$this, 'showKeyAndDownloadLink'], 10, 1);

        // todo add licence key and download link to woocom email

        // todo add subscription status change hook

    }


    /**
     * Add product fields
     */
    public function addProductFields() {

        add_action( 'woocommerce_product_options_general_product_data', [$this, 'addEnableGnarLicensingField'], 10 );
        add_action( 'woocommerce_process_product_meta', [$this, 'saveGnarProductFields'], 10, 1 );

    }


    /**
     * Add enable Gnar Licensing Field
     */
    public function addEnableGnarLicensingField() {

        $post_id = get_the_ID();

        echo '<div class="options_group">';

        woocommerce_wp_checkbox( array(
            'id'    => 'gnar_licencing_enable_prod',
            'label' => 'Enable gnar licensing',
            'value' => get_post_meta( $post_id, 'gnar_licencing_enable_prod', true )
        ));

        woocommerce_wp_text_input( array(
            'id'          => 'gnar_licencing_software_id',
            'label'       => 'Software ID',
            'placeholder' => 'e.g. my_example_wp_plugin',
            'value'       => get_post_meta( $post_id, 'gnar_licencing_software_id', true )
        ));

        echo '</div>';
    
    }


    /**
     * Save gnar product fields
     */
    public function saveGnarProductFields($post_id) {

        if (isset($_POST['gnar_licencing_enable_prod'])) {
            update_post_meta( $post_id, 'gnar_licencing_enable_prod', $_POST['gnar_licencing_enable_prod'] );
        }
        if (isset($_POST['gnar_licencing_software_id'])) {
            update_post_meta( $post_id, 'gnar_licencing_software_id', $_POST['gnar_licencing_software_id'] );
        }

    }


    /**
     * Add woocom hooks
     */
    public function addPurchaseHooks() {

        add_action( 'woocommerce_payment_complete', [$this, 'createLicence'], 10, 1 );

    }


    /**
     * WC create Gnar licence
     */
    public function createLicence($order_id) {

        $order = wc_get_order($order_id);
        $items = $order->get_items();

        // customer email
        $customerEmail = $orde->get_billing_email();

        foreach ($items as $item) {

            // check if item is gnar licensing enabled
            $licensingEnabled = get_post_meta($item->ID, 'gnar_licencing_enable_prod', true);

            if ($licensingEnabled !== 'yes') {
                break;
            }

            // get software id
            $softwareID = get_post_meta($item->ID, 'gnar_licencing_software_id', true);

            if (empty($softwareID)) {
                break;
            }

            // woocom subscription status / todo
            $status = 'active';

            // generate licence
            $licence = gnar_licence::createLicence($customerEmail, $softwareID, $status);

            // problem creating licence
            if (empty($licence)) {
                $order->add_order_note('Problem creating gnar licence');

                return;
            }

            // save licence to order
            $order->add_order_note('Gnar licence key: ' . $licence->licenceKey);
            add_post_meta($order_id, 'licence_key_' . $licence->softwareID, $licence->licenceKey);

        }

    }


    /**
     * Display licence key and download link on WC thank you page
     */
    public function showKeyAndDownloadLink($order_id) {

        $order = wc_get_order($order_id);
        $items = $order->get_items();

        foreach ($items as $item) {
            // check if item is gnar licensing enabled
            $licensingEnabled = get_post_meta($item->ID, 'gnar_licencing_enable_prod', true);

            if ($licensingEnabled !== 'yes') {
                break;
            }

            $softwareID = get_post_meta($item->ID, 'gnar_licencing_software_id', true);

            if (empty($softwareID)) {
                break;
            }

            $licenceKey = get_post_meta($order_id, 'licence_key_' . $softwareID, true);

            if (empty($licenceKey)) {
                break;
            }

            $downloadLink = gnar_download::downloadLink($softwareID);

            $this->keyAndDownloadMarkUp($licenceKey, $downloadLink);
        }

    }


    /**
     * Mark Up: Display licence key and download link on WC thank you page
     */
    public function keyAndDownloadMarkUp($licenceKey, $downloadLink) {
        ?>
        <div class="gnar_purchased_cont">
            

        </div>
        <?php
    }

}



?>