<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_comet_cache Class
 *
 * This class integrates all Comet cache related features and endpoints
 *
 * @since 6.0
 */
class WP_Webhooks_Integrations_comet_cache {

    public function is_active(){
        return class_exists( 'WebSharks\CometCache\Classes\Plugin' );
    }

    public function get_details(){
        return array(
            'name' => 'Comet Cache',
            'icon' => 'assets/img/icon-comet-cache.png',
        );
    }

}
