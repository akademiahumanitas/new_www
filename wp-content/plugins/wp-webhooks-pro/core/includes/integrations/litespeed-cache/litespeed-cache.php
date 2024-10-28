<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_litespeed_cache Class
 *
 * This class integrates all litespeed_cache related features and endpoints
 *
 * @since 6.0
 */
class WP_Webhooks_Integrations_litespeed_cache {

    public function is_active(){
        return defined( 'LSCWP_V' );
    }

    public function get_details(){
        return array(
            'name' => 'LiteSpeed Cache',
            'icon' => 'assets/img/icon-litespeed-cache.svg',
        );
    }

}
