<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_wp_super_cache Class
 *
 * This class integrates all WP Super Cache related features and endpoints
 *
 * @since 6.0
 */
class WP_Webhooks_Integrations_wp_super_cache {

    public function is_active(){
        return defined( 'WPCACHECONFIGPATH' );
    }

    public function get_details(){
        return array(
            'name' => 'WP Super Cache',
            'icon' => 'assets/img/icon-wp-super-cache.png',
        );
    }

}
