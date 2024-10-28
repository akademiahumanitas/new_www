<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_wocommerce_shipstation Class
 *
 * This class integrates all WooCommerce ShipStation related features and endpoints
 *
 * @since 6.1.1
 */
class WP_Webhooks_Integrations_woocommerce_shipstation {

    public function is_active(){    
        return defined( 'WC_SHIPSTATION_VERSION' );
    }

    public function get_details(){
        return array(
            'name' => 'WooCommerce ShipStation',
            'icon' => 'assets/img/icon-woocommerce-shipstation.svg',
        );
    }

}
