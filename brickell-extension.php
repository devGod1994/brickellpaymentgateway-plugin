<?php
/*
Plugin Name: Brickell Payment Gateway Extension
Description: Integrates Brickell Payment Gateway API
Version: 1.0
Author: Linus
*/

// Basic security check.
if (!defined('ABSPATH')) {
    exit;
}

// Include the main class.
require_once 'includes/brickell-payment-gateway-extension.php';

// Initialize the plugin.
add_action('plugins_loaded', array('Brickell_Payment_Gateway_Extension', 'init'));