<?php
/*
Plugin Name: WooCommercePlus
Plugin URI:
Description: WooCommerce Customization
Version: 1.0.0
Author: Altis Infonet Private Limited
Author URI: https://www.altisinfonet.com

Text Domain: woocommerceplus
*/

if( !defined("ABSPATH") ) die('Direct access not allowed!');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

global $wpdb;
define( 'WOPL_PLUGIN_FILE', __FILE__ );
define( 'WOPL_PLUGIN_DIR', WP_PLUGIN_DIR . "/woocommerceplus/");
include("includes/common.php");
?>