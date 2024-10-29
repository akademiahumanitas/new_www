<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_cache_enabler Class
 *
 * This class integrates all Cache Enabler related features and endpoints
 *
 * @since 6.0
 */
class WP_Webhooks_Integrations_cache_enabler {

    public function is_active(){
        return defined( 'CACHE_ENABLER_VERSION' );
    }

    public function get_details(){
        return array(
            'name' => 'Cache Enabler',
            'icon' => 'assets/img/icon-cache-enabler.svg',
        );
    }

}
