<?php
class WC_My_Shipping_Method extends WC_Shipping_Method {
    const API_URL = 'https://api.fictionalshipping.com/';

    public function __construct() {
        $this->id = 'my_shipping_method';
        $this->method_title = __('Fictional Shipping', 'my-shipping-extension');
        $this->method_description = __('Real-time shipping rates from Fictional Shipping.', 'my-shipping-extension');

        $this->init();
    }

    public function init() {
        $this->init_form_fields();
        $this->init_settings();

        $this->title = $this->get_option('title');
        $this->api_key = $this->get_option('api_key');

        add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
    }

    public function calculate_shipping($package = array()) {
        $rates = $this->get_shipping_rates($package);
        if (!empty($rates)) {
            foreach ($rates as $rate) {
                $this->add_rate($rate);
            }
        }
    }

    private function get_shipping_rates($package) {
        $destination = $package['destination'];
        $weight = 0;

        foreach ($package['contents'] as $item) {
            $product = $item['data'];
            $weight += $product->get_weight() * $item['quantity'];
        }

        $response = wp_remote_get(self::API_URL . 'rates', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_key,
            ),
            'body' => array(
                'destination' => $destination['postcode'],
                'weight' => $weight,
            ),
        ));

        if (is_wp_error($response)) {
            return array();
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (empty($data) || !isset($data['rates'])) {
            return array();
        }

        $rates = array();
        foreach ($data['rates'] as $rate) {
            $rates[] = array(
                'id' => $this->id . ':' . $rate['service'],
                'label' => $rate['service_name'],
                'cost' => $rate['cost'],
                'calc_tax' => 'per_item',
            );
        }

        return $rates;
    }

    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'my-shipping-extension'),
                'type' => 'checkbox',
                'label' => __('Enable Fictional Shipping', 'my-shipping-extension'),
                'default' => 'yes',
            ),
            'title' => array(
                'title' => __('Method Title', 'my-shipping-extension'),
                'type' => 'text',
                'description' => __('This determines the title that appears to the user when they check out.', 'my-shipping-extension'),
                'default' => __('Fictional Shipping', 'my-shipping-extension'),
            ),
            'api_key' => array(
                'title' => __('API Key', 'my-shipping-extension'),
                'type' => 'text',
                'description' => __('Enter your Fictional Shipping API key.', 'my-shipping-extension'),
            ),
        );
    }

}