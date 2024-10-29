<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_ifttt Class
 *
 * This class integrates all IFTTT related features and endpoints
 *
 * @since 5.0
 */
class WP_Webhooks_Integrations_ifttt {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'IFTTT',
            'icon' => 'assets/img/icon-ifttt.png',
        );
    }

}
