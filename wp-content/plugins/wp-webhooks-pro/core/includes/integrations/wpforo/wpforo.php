<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_wpforo Class
 *
 * This class integrates all wpForo related features and endpoints
 *
 * @since 6.1.1
 */
class WP_Webhooks_Integrations_wpforo {

    public function is_active(){
        return defined( 'WPFORO_VERSION' );
    }

    public function get_details(){
        return array(
            'name' => 'wpForo',
            'icon' => 'assets/img/icon-wpforo.svg',
        );
    }

}
