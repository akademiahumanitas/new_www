<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_studiocart Class
 *
 * This class integrates all Studiocart related features and endpoints
 *
 * @since 6.0
 */
class WP_Webhooks_Integrations_studiocart {

    public function is_active(){
        return defined( 'NCS_CART_VERSION' );
    }

    public function get_details(){
        return array(
            'name' => 'Studiocart',
            'icon' => 'assets/img/icon-studiocart.png',
        );
    }

}
