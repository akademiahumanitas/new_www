<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_bounceless Class
 *
 * This class integrates all Bounceless related features and endpoints
 *
 * @since 6.1.3
 */
class WP_Webhooks_Integrations_bounceless {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'Bounceless',
            'icon' => 'assets/img/icon-bounceless.svg',
        );
    }

}
