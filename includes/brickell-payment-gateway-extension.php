<?php
class Brickell_Payment_Gateway_Extension {
    const API_URL = 'https://api.fictionalshipping.com/v1/';
    const API_KEY = 'your_api_key_here';

    public static function init() {
        add_filter('woocommerce_shipping_methods', array(__CLASS__, 'add_shipping_method'));
        add_action('woocommerce_shipping_init', array(__CLASS__, 'shipping_method_init'));
    }

    public static function add_shipping_method($methods) {
        $methods['my_shipping_method'] = 'WC_My_Shipping_Method';
        return $methods;
    }

    public static function shipping_method_init() {
        require_once 'brickell-payment-gateway-method.php';
    }
}