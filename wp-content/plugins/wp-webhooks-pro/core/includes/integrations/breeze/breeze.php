<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_breeze Class
 *
 * This class integrates all Breeze related features and endpoints
 *
 * @since 6.0
 */
class WP_Webhooks_Integrations_breeze {

    public function is_active(){
        return defined( 'BREEZE_VERSION' );
    }

    public function get_details(){
        return array(
            'name' => 'Breeze',
            'icon' => 'assets/img/icon-breeze.png',
        );
    }

}
