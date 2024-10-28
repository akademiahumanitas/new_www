<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_woocommerce_memberships Class
 *
 * This class integrates all WooCommerce Memberships related features and endpoints
 *
 * @since 4.3.7
 */
class WP_Webhooks_Integrations_woocommerce_memberships {

    public function is_active(){
        return class_exists( 'WC_Memberships_Loader' );
    }

    public function get_details(){
        return array(
            'name' => 'WooCommerce Memberships',
            'icon' => 'assets/img/icon-woocommerce-memberships.svg',
        );
    }

}
