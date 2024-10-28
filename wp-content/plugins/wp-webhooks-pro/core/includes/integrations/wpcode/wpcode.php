<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_wpcode Class
 *
 * This class integrates all WPCode related features and endpoints
 *
 * @since 6.1.4
 */
class WP_Webhooks_Integrations_wpcode {

    public function is_active(){
        return class_exists( 'WPCode' );
    }

    public function get_details(){
        return array(
            'name' => 'WPCode',
            'icon' => 'assets/img/icon-wpcode.svg',
        );
    }

}
