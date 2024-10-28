<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_wp_optimize Class
 *
 * This class integrates all WP-Optimize related features and endpoints
 *
 * @since 6.0
 */
class WP_Webhooks_Integrations_wp_optimize {

    public function is_active(){
        return defined( 'WPO_VERSION' );
    }

    public function get_details(){
        return array(
            'name' => 'WP-Optimize',
            'icon' => 'assets/img/icon-wp-optimize.png',
        );
    }

}
