<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_make Class
 *
 * This class integrates all Make related features and endpoints
 *
 * @since 5.0
 */
class WP_Webhooks_Integrations_make {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'Make (Integromat)',
            'icon' => 'assets/img/icon-make.png',
        );
    }

}
