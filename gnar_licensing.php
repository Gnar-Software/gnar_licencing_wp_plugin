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

define( 'PLUGIN_DIR',                       plugin_dir_path( __FILE__ ) );
define( 'LIB_DIR',                          plugin_dir_path( __FILE__ ) . '/lib' );
define( 'JS_DIR',                           plugin_dir_url( __FILE__ ) . '/js' );
define( 'ADMIN_DIR',                        plugin_dir_path( __FILE__ ) . '/admin' );
define( 'GNAR_API_URL',                     'https://api.gnar.co.uk/api' );
define( 'GNAR_API_LICENCE_ROUTE',           '/licence'  );
define( 'CSS_DIR',                          plugin_dir_url( __FILE__ ) . '/css' );


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
        
        wp_register_style( 'gnar_licensing_admin_style', CSS_DIR . '/gnar_licensing_admin.css', false, '1.0.0' );
        wp_enqueue_style( 'gnar_licensing_admin_style' );

    }


    /**
     * Register admin scripts
     */
    public function adminScripts() {

        wp_enqueue_script( 'gnar_licensing_admin', JS_DIR . '/gnar_licensing_admin.js', array(), '1.0.0' );

    }

}

?>