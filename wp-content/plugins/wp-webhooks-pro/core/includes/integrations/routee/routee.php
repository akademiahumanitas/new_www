<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_routee Class
 *
 * This class integrates all Routee related features and endpoints
 *
 * @since 6.1.4
 */
class WP_Webhooks_Integrations_routee {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'Routee',
            'icon' => 'assets/img/icon-routee.png',
        );
    }

}
