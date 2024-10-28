<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_pabbly_connect Class
 *
 * This class integrates all Pabbly Connect related features and endpoints
 *
 * @since 5.0
 */
class WP_Webhooks_Integrations_pabbly_connect {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'Pabbly Connect',
            'icon' => 'assets/img/icon-pabbly-connect.png',
        );
    }

}
