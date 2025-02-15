<?php
/**
 * Plugin Name: Via.Delivery
 * Plugin URI: https://via.delivery
 * Description: Deliver to your customer at a fraction of the cost of traditional carriers.
 * Version: 1.0.12
 * Author: Ipol
 * Author URI: https://ipol.ru/
 * Text Domain: viadelivery
 * Domain Path: /languages
 */

if (!defined('WPINC')) {
    die;
}

if (!defined('VIADELIVERY_PLUGIN_FILE')) {
    define('VIADELIVERY_PLUGIN_FILE', __FILE__);
}

if (!defined('VIADELIVERY_PLUGIN_URI')) {
    // define('VIADELIVERY_PLUGIN_URI', plugin_dir_url(VIADELIVERY_PLUGIN_FILE));
    define('VIADELIVERY_PLUGIN_URI', plugin_dir_url(__FILE__));

}

if (!defined('VIADELIVERY_PLUGIN_PATH')) {
    define('VIADELIVERY_PLUGIN_PATH', plugin_dir_path(VIADELIVERY_PLUGIN_FILE));
}


//Проверяем наличие woocommerce
if (in_array(
        'woocommerce/woocommerce.php',
        apply_filters('active_plugins', get_option('active_plugins'))
    )) {

    if (!class_exists('Ipol\\Woo\\ViaDelivery')) {
        include_once VIADELIVERY_PLUGIN_PATH . '/includes/viadelivery.php';
    }

    function viadelivery() {
        return new \Ipol\Woo\ViaDelivery;
    }

    // Global for backwards compatibility.
    $GLOBALS['viadelivery'] = viadelivery();
}
