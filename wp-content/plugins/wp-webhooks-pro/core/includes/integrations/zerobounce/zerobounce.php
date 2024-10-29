<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_zerobounce Class
 *
 * This class integrates all ZeroBounce related features and endpoints
 *
 * @since 6.1.3
 */
class WP_Webhooks_Integrations_zerobounce {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'ZeroBounce',
            'icon' => 'assets/img/icon-zerobounce.svg',
        );
    }

}
