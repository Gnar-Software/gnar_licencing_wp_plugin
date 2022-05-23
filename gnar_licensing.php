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
define( 'GNRL_GNAR_API_LICENCE_ROUTE',           '/licence'  );
define( 'GNRL_CSS_DIR',                          plugin_dir_url( __FILE__ ) . '/css' );

include_once( GNRL_ADMIN_DIR . '/gnar_licensing_options_view.php' );


class gnar_licensing {

    public function __construct() {

        // admin pages
        add_action( 'admin_menu', [$this, 'adminPages'] );
        add_action( 'admin_enqueue_scripts', [$this, 'adminScripts'] );
        
    }


    /**
     * Register admin pages
     */
    public function adminPages() {

        $gnarLicencingAdminOptions = add_menu_page( 'Gnar Licensing', 'Gnar Licensing', 'manage_options', 'gnar_licensing_options', ['gnar_licensing_options_view', 'gnarLicensingOptionsView'], '', 99 );

        add_action( 'admin_print_styles-' . $gnarLicencingAdminOptions, [$this, 'adminStyles'] );

    }


    /**
     * Register admin styles
     */
    public function adminStyles() {
        
        wp_register_style( 'gnar_licensing_admin_style', GNRL_CSS_DIR . '/gnar_licensing_admin.css', false, '1.0.0' );
        wp_enqueue_style( 'gnar_licensing_admin_style' );

    }


    /**
     * Register admin scripts
     */
    public function adminScripts() {

        wp_enqueue_script( 'gnar_licensing_admin', GNRL_JS_DIR . '/gnar_licensing_admin.js', array(), '1.0.0' );

    }

}

new gnar_licensing();

?>