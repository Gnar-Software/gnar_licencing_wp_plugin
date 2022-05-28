<?php 
/** 
 * Downloads 
 * 
 * Shows downloads on the account page. 
 * 
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/downloads.php. 
 * 
 * HOWEVER, on occasion WooCommerce will need to update template files and you 
 * (the theme developer) will need to copy the new files to your theme to 
 * maintain compatibility. We try to do this as little as possible, but it does 
 * happen. When this occurs the version of the template file will be bumped and 
 * the readme will list any important changes. 
 * 
 * @see     https://docs.woocommerce.com/document/template-structure/ 
 * @author  WooThemes 
 * @package WooCommerce/Templates 
 * @version 3.0.0 
 */ 
 
if ( ! defined( 'ABSPATH' ) ) { 
  exit; 
}

// wc subscriptions are active
$wcSubscriptionsExists = false;
if( !is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) ) {
	$wcSubscriptionsExists = true;
}

// get users available downloads
$user = wp_get_current_user();
$downloads = gnar_woocom::getDownloads($user->ID);

$has_downloads = false;
if (!empty($downloads)) {
    $has_downloads = true;
}

 
do_action( 'woocommerce_before_account_downloads', $has_downloads ); ?> 
 
<?php if (!empty($downloads)) : ?> 
 
  <?php do_action( 'woocommerce_before_available_downloads' ); ?> 

    <?php foreach ($downloads as $downloadObj) {
    ?>
        <div class="account_gnar_download_outer">
            <div class="gnar_col left">
                <img src="<?= $downloadsObj->imageUrl ?>" />
            </div>
            <div class="gnar_col right">
                <h3><?= $downloadsObj->productName ?></h3>
                <p><?= $downloadsObj->productDesc ?></p>
                <div class="gnar_col_right_inner">
                    <form id="gnar_download_form" data-key="<?= $downloadsObj->licenceKey ?>">
                        <p>
                            <label>Licence key:</label>
                            <span><?= $downloadsObj->licenceKey ?></span>
                        </p>
                        <p>
                            <label>Registered domain:</label>
                            <input type="text" name="registered_domain" value="<?= $downloadsObj->domain ?>" /> <br/>
                            <span class="gnar_tip">You can change the domain your plugin/theme is registered to here.</span>
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


  <?php do_action( 'woocommerce_after_available_downloads' ); ?> 
<?php else : ?> 
  <div class="woocommerce-Message woocommerce-Message--info woocommerce-info"> 
      <a class="woocommerce-Button button" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>"> 
          <?php esc_html_e( 'Go shop', 'woocommerce' ) ?> 
      </a> 
      <?php esc_html_e( 'No downloads available yet.', 'woocommerce' ); ?> 
  </div> 
<?php endif; ?> 
 
<?php do_action( 'woocommerce_after_account_downloads', $has_downloads ); ?> 