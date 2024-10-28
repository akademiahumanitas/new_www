<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_lemon_squeezy Class
 *
 * This class integrates all Lemon Squeezy related features and endpoints
 *
 * @since 6.1.5
 */
class WP_Webhooks_Integrations_lemon_squeezy {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'Lemon Squeezy',
            'icon' => 'assets/img/icon-lemon-squeezy.svg',
        );
    }

}
