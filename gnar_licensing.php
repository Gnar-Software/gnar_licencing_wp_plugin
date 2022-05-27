<?php

/*
 * Plugin Name: Gnar Licensing
 * Description: Create & manage gnar licences
 * Version: 1.0.0
 * Author: gnar software
 * Author URI: https://www.gnar.co.uk/
 * License: GPLv2 or later
 * Text Domain: gnar_software
*/


if ( ! defined( 'ABSPATH' )) {
    exit;
}

define( 'GNRL_PLUGIN_DIR',                       plugin_dir_path( __FILE__ ) );
define( 'GNRL_LIB_DIR',                          plugin_dir_path( __FILE__ ) . 'lib' );
define( 'GNRL_JS_DIR',                           plugin_dir_url( __FILE__ ) . 'js' );
define( 'GNRL_ADMIN_DIR',                        plugin_dir_path( __FILE__ ) . 'admin' );
define( 'GNRL_GNAR_API_URL',                     'https://api.gnar.co.uk/api' );
define( 'GNRL_CSS_DIR',                          plugin_dir_url( __FILE__ ) . 'css' );
define( 'GNRL_ASSET_DIR',                          plugin_dir_url( __FILE__ ) . 'assets' );

include_once( GNRL_LIB_DIR   . '/gnar_api.php' );
include_once( GNRL_LIB_DIR   . '/gnar_woocom.php' );
include_once( GNRL_LIB_DIR   . '/gnar_licence.php' );
include_once( GNRL_ADMIN_DIR . '/gnar_licensing_options_view.php' );
include_once( GNRL_ADMIN_DIR . '/gnar_licensing_licences_view.php' );


class gnar_licensing {

    public function __construct() {

        // admin pages
        add_action( 'admin_menu', [$this, 'adminPages'] );
        add_action( 'admin_enqueue_scripts', [$this, 'adminScripts'] );

        // classes
        new gnar_woocom();

    }


    /**
     * Register admin pages
     */
    public function adminPages() {

        $gnarLicencingAdminOptions = add_menu_page( 'Gnar Licensing', 'Gnar Licensing', 'manage_options', 'gnar_licensing_options', ['gnar_licensing_options_view', 'gnarLicensingOptionsView'], GNRL_ASSET_DIR . '/gnar.png', 99 );
        $gnarLicencingAdminOptions = add_submenu_page( 'gnar_licensing_options', 'Settings', 'Settings', 'manage_options', 'gnar_licensing_options', ['gnar_licensing_options_view', 'gnarLicensingOptionsView'], 1);
        $gnarLicencingAdminManage  = add_submenu_page( 'gnar_licensing_options', 'Manage licences', 'Manage licences', 'manage_options', 'gnar_licensing_manage', ['gnar_licensing_licences_view', 'gnarLicensingLicencesView'], 1);

        add_action( 'admin_print_styles-' . $gnarLicencingAdminOptions, [$this, 'adminStyles'], 10 );
        add_action( 'admin_print_styles-' . $gnarLicencingAdminManage, [$this, 'adminStyles'], 10 );

    }


    /**
     * Register admin styles
     */
    public function adminStyles() {
        
        wp_register_style( 'gnar_licensing_admin_style', GNRL_CSS_DIR . '/gnar_licensing_admin.css', false, '' );
        wp_enqueue_style( 'gnar_licensing_admin_style' );

    }


    /**
     * Register admin scripts
     */
    public function adminScripts() {

        wp_enqueue_script( 'gnar_licensing_admin', GNRL_JS_DIR . '/gnar_licensing_admin.js', array(), '' );

    }

}

new gnar_licensing();

?>