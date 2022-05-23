<?php

class gnar_woocom {

    public function __construct() {

        $this->addProductFields();
        $this->addPurchaseHooks();

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

        foreach ($items as $item) {

            // check if item is gnar licensing enabled
            $licensingEnabled = get_post_meta($item->ID, 'gnar_licencing_enable_prod', true);

            if ($licensingEnabled !== 'yes') {
                break;
            }

            // get software id
            $softwareID = get_post_meta($item->ID, 'gnar_licencing_software_id', true);

            if (isempty($softwareID)) {
                break;
            }

            // generate licence
            $gnar_licence = new gnar_licence();
            $createSuccess = $gnar_licence->createLicence();

            // save licence to order notes
            if ($createSuccess) {

            }

            // handle errors
            if (!$createSuccess) {

            }


        }

    }

}



?>