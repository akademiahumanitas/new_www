<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_wp_webhooks Class
 *
 * This class integrates all Contact Form 7 related features and endpoints
 *
 * @since 4.2.0
 */
class WP_Webhooks_Integrations_wp_webhooks {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'WP Webhooks',
            'icon' => 'assets/img/icon-wp-webhooks.svg',
        );
    }

}
