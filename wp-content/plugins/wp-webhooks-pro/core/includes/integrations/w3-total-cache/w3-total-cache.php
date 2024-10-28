<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_w3_total_cache Class
 *
 * This class integrates all W3 Total Cache related features and endpoints
 *
 * @since 6.0
 */
class WP_Webhooks_Integrations_w3_total_cache {

    public function is_active(){
        return defined( 'W3TC' );
	}

    public function get_details(){
        return array(
            'name' => 'W3 Total Cache',
            'icon' => 'assets/img/icon-w3-total-cache.png',
        );
    }

}
