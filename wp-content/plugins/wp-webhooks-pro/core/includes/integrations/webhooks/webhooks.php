<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_webhooks Class
 *
 * This class integrates all Webhooks related features and endpoints
 *
 * @since 4.3.6
 */
class WP_Webhooks_Integrations_webhooks {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'Webhooks',
            'icon' => 'assets/img/icon-webhooks.svg',
        );
    }

}
