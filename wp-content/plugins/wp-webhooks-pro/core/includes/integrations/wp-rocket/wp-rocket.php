<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_wp_rocket Class
 *
 * This class integrates all WP Rocket related features and endpoints
 *
 * @since 6.0
 */
class WP_Webhooks_Integrations_wp_rocket {

    public function is_active(){
        return defined( 'WP_ROCKET_VERSION' );
    }

    public function get_details(){
        return array(
            'name' => 'WP Rocket',
            'icon' => 'assets/img/icon-wp-rocket.svg',
        );
    }

}
