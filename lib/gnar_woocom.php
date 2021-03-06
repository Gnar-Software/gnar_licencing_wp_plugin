<?php

class gnar_woocom {

    public function __construct() {

        $this->addProductFields();
        $this->addPurchaseHooks();

        // todo display licence key and download link on order recieved page
        add_action('woocommerce_thankyou', [$this, 'showKeyAndDownloadLink'], 10, 1);

        // my account page
        $this->setupMyAccountPage();

        // ajax actions
        add_action( 'wp_ajax_updatedomain', [$this, 'ajaxUpdateDomain'] );
        add_action( 'wp_ajax_nopriv_updatedomain', [$this, 'ajaxUpdateDomain'] );

        // todo add licence key and download link to woocom email

        // add subscription status change hook
        add_action( 'woocommerce_subscription_status_updated', [$this, 'subscriptionStatusUpdated'], 10, 3 );

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

        add_action( 'woocommerce_checkout_order_processed', [$this, 'createLicence'] );

    }


    /**
     * WC create Gnar licence
     */
    public function createLicence($order_id) {

        $order = wc_get_order($order_id);
        $items = $order->get_items();

        // customer email
        $customerEmail = $order->get_billing_email();

        foreach ($items as $itemID => $item) {

            $product = $item->get_product();
            $productID = $product->get_id();

            // check if item is gnar licensing enabled and get software ID
            $licensingEnabled = get_post_meta($productID, 'gnar_licencing_enable_prod', true);
            $softwareID = get_post_meta($productID, 'gnar_licencing_software_id', true);

            if ($licensingEnabled !== 'yes' || empty($softwareID)) {
                break;
            }

            // assume wc subscription status is active straight away
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

            $order->add_order_note('Gnar licence ID: ' . $licence->licenceID);
            add_post_meta($order_id, 'licence_id_' . $licence->softwareID, $licence->licenceID);

        }

        // save licence to subscription (assumes only one subscription per order, and one licence per subscription)
        $subscriptions = wcs_get_subscriptions_for_order($order_id);

        if (!empty($subscriptions)) {

            foreach ($subscriptions as $subscriptionID => $subscription) {
                update_post_meta($subscriptionID, 'gnar_licence_id', $licenceID);
                break;
            }
        }

    }


    /**
     * Display licence key and download link on WC thank you page
     */
    public function showKeyAndDownloadLink($order_id) {

        $order = wc_get_order($order_id);
        $items = $order->get_items();

        foreach ($items as $item) {

            $product = $item->get_product();
            $productID = $product->get_id();

            // check if item is gnar licensing enabled
            $licensingEnabled = get_post_meta($productID, 'gnar_licencing_enable_prod', true);

            if ($licensingEnabled !== 'yes') {
                break;
            }

            $softwareID = get_post_meta($productID, 'gnar_licencing_software_id', true);

            if (empty($softwareID)) {
                break;
            }

            $licenceKey = get_post_meta($order_id, 'licence_key_' . $softwareID, true);

            if (empty($licenceKey)) {
                echo 'There was problem generating your licence key.';
                break;
            }

            $downloadLink = gnar_download::downloadLink($softwareID);

            $this->keyAndDownloadMarkUp($licenceKey, $downloadLink, $softwareID);
        }

    }


    /**
     * Mark Up: Display licence key and download link on WC thank you page
     */
    public function keyAndDownloadMarkUp($licenceKey, $downloadLink, $softwareID) {
        ?>
        <div class="gnar_purchased_cont">
            <div>
                <label>Licence key: </label>
                <span><?= $licenceKey ?></span>
            </div>

            <p>Your licence key has been emailed to you. Please click the link below to download your purchased software.</p>

            <div class="gnar_download_link_cont">
                <a href="<?= $downloadLink ?>">Click here to download <?= $softwareID ?></a>
            </div>

            <p>You will be able to re-download your purchased software at any time from your <a href="/my-account/">account page</a>.</p>

        </div>
        <?php
    }


    /**
     * Get available downloads/licences for the users my account page
     * 
     * @param int $user_id
     * @return array $downloads (array of objects)
     */
    public static function getDownloads($user_id) {
        $downloads = [];

        $userData = get_userdata($user_id);
        $email = $userData->user_email;

        // get all licences associated with this email address
        $licences = gnar_licence::getUserLicence($email);

        // get product data and generate response object
        foreach ($licences as $licence) {

            // get product from softwareID
            $args = array(
                'post_type' => 'product',
                'meta_key' => 'gnar_licencing_software_id',
                'meta_value' => $licence->softwareID
            );

            $products = wc_get_products($args);

            if (!empty($products)) {
                $product = $products[0];

                // form download obj
                $downloadObj = (object) [
                    'imageUrl'     =>  wp_get_attachment_url(get_post_thumbnail_id($product->get_id())),
                    'productName'  =>  $product->get_title(),
                    'productDesc'  =>  $product->get_description(),
                    'licenceID'    =>  $licence->licenceID,
                    'licenceKey'   =>  $licence->licenceKey,
                    'domain'       =>  $licence->domain,
                    'downloadLink' =>  gnar_download::downloadLink($licence->softwareID)
                ];
    
                // add to array
                array_push($downloads, $downloadObj);
            }

        }

        return $downloads;
    }


    /**
     * Setup My account page
     */
    public function setupMyAccountPage() {

        // add link to menu items & remove wc downloads item
        add_filter ( 'woocommerce_account_menu_items', [$this, 'addMyAccountMenuItem'], 10 );

        // register permalink
        add_action( 'init', [$this, 'addMyAccountEndpoint'] );

        // page content
        add_action( 'woocommerce_account_my-licences_endpoint', [$this, 'myLicencesPage'] );

    }


    /**
     * Add my account menu item & remove wc downloads item
     */
    public function addMyAccountMenuItem($menuItems) {

        $menuItems['my-licences'] = 'Licences / Downloads';
        unset($menuItems['downloads']);

        return $menuItems;
    }


    /**
     * Add my account page endpoint
     */
    public function addMyAccountEndpoint() {

        add_rewrite_endpoint( 'my-licences', EP_PAGES );

    }


    /**
     * My licences page mark up
     */
    public function myLicencesPage() {

        // wc subscriptions are active
        $wcSubscriptionsExists = false;
        // if( !is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) ) {
        //     $wcSubscriptionsExists = true;
        // }

        // get users available downloads
        $user = wp_get_current_user();
        $downloads = gnar_woocom::getDownloads($user->ID);

        $has_downloads = false;
        if (!empty($downloads)) {
            $has_downloads = true;
        }

        if (!empty($downloads)) : ?> 

        <?php foreach ($downloads as $downloadObj) {
        ?>
            <div class="account_gnar_download_outer">
                <div class="gnar_col left">
                    <img src="<?= $downloadObj->imageUrl ?>" />
                </div>
                <div class="gnar_col right">
                    <h3><?= $downloadObj->productName ?></h3>
                    <p><?= $downloadObj->productDesc ?></p>
                    <div class="gnar_col_right_inner">
                        <form id="gnar_download_form" data-licenceID="<?= $downloadObj->licenceID ?>">
                            <p>
                                <label>Licence key:</label>
                                <span><?= $downloadObj->licenceKey ?></span>
                            </p>
                            <p>
                                <label>Registered domain:</label>
                                <input type="text" name="registered_domain" value="<?= $downloadObj->domain ?>" />
                                <button class="update_domain" data-licenceid="<?= $downloadObj->licenceID ?>" id="domain_update_<?= $downloadObj->licenceKey ?>">Update</button>
                            </p>
                            <p>
                                <label>Download link:</label>
                                <a href="<?= $downloadObj->downloadLink ?>" class="gnar_download_btn">Download Latest Version</a>
                            </p>

                        </form>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>

        <?php else : ?> 
        <div class="woocommerce-Message woocommerce-Message--info woocommerce-info"> 
            <a class="woocommerce-Button button" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>"> 
                <?php esc_html_e( 'Go shop', 'woocommerce' ) ?> 
            </a> 
            <?php esc_html_e( 'No downloads available yet.', 'woocommerce' ); ?> 
        </div> 
        <?php endif;
    }


    /**
     * Ajax update domain
     */
    public static function ajaxUpdateDomain() {

        if (!isset($_POST['security']) || !wp_verify_nonce( $_POST['security'], 'gnar_security_nonce' )) {
            echo json_encode( (object) [
                'status' => 'unauthorised'
            ]);
            die();
        }

        if (empty($_POST['domain']) || empty($_POST['licenceID'])) {
            echo json_encode( (object) [
                'status' => 'invalid request',
                'post'   => $_POST 
            ]);
            die();
        }

        $licenceID = strip_tags($_POST['licenceID']);
        $domain = strip_tags($_POST['domain']);

        $args = (object) [
            'domain' => $domain
        ];

        $success = gnar_licence::updateLicence($licenceID, $args);

        if (!$success) {
            echo json_encode( (object) [
                'status' => 'error'
            ]);
            die();
        }

        echo json_encode( (object) [
            'status' => 'success'
        ]);

        die();

    }


    /**
     * Subscription status updated - update licence status
     * 
     * @param object $subscription
     * @param string $newStatus
     * @param string $oldStatus
     * @param bool $licenceStatusChanged
     */
    public function subscriptionStatusUpdated($subscription, $newStatus, $oldStatus) {

        echo 'sub status was updated ';

        // new status can be ignored
        if ($newStatus !== 'active' && $newStatus !== 'cancelled' && $newStatus !== 'expired') {
            return false;
        }

        // new status
        if ($newStatus !== 'active') {
            $licenceStatus = 'active';
        }
        else if ($newStatus == 'cancelled' || $newStatus == 'expired') {
            $licenceStatus = 'inactive';
        }

        echo 'new status is ' . $licenceStatus;

        // get licence id from subscription meta
        $subID = $subscription->get_id();
        $licenceID = get_post_meta($subID, 'gnar_licence_id', true);

        if (empty($licenceID)) {
            return;
        }

        // update licence
        $args = (object) [
            'status' => $licenceStatus
        ];

        return gnar_licence::updateLicence($licenceID, $args);

    }
}



?>