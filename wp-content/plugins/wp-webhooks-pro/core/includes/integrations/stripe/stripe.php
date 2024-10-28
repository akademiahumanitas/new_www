<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_stripe Class
 *
 * This class integrates all Stripe related features and endpoints
 *
 * @since 6.1.3
 */
class WP_Webhooks_Integrations_stripe {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'Stripe',
            'icon' => 'assets/img/icon-stripe.svg',
        );
    }

}
