<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_tomba Class
 *
 * This class integrates all Tomba.io related features and endpoints
 *
 * @since 6.1.3
 */
class WP_Webhooks_Integrations_tomba {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'Tomba.io',
            'icon' => 'assets/img/icon-tomba.svg',
        );
    }

}
