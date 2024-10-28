<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_hummingbird Class
 *
 * This class integrates all Hummingbird related features and endpoints
 *
 * @since 6.0
 */
class WP_Webhooks_Integrations_hummingbird {

    public function is_active(){
        return defined( 'WPHB_VERSION' );
    }

    public function get_details(){
        return array(
            'name' => 'Hummingbird',
            'icon' => 'assets/img/icon-hummingbird.png',
        );
    }

}
