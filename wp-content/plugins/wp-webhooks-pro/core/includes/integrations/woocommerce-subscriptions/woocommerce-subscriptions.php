<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_woocommerce_subscriptions Class
 *
 * This class integrates all WooCommerce Subscriptions related features and endpoints
 *
 * @since 5.2
 */
class WP_Webhooks_Integrations_woocommerce_subscriptions {

    public function is_active(){
        return class_exists( 'WC_Subscriptions' );
    }

    public function get_details(){
        return array(
            'name' => 'WooCommerce Subscriptions',
            'icon' => 'assets/img/icon-woocommerce-subscriptions.svg',
        );
    }

}
