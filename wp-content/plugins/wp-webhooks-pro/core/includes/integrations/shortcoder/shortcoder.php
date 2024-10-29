<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_shortcoder Class
 *
 * This class integrates all Shortcoder related features and endpoints
 *
 * @since 6.0
 */
class WP_Webhooks_Integrations_shortcoder {

    public function is_active(){
        return defined( 'SC_VERSION' );
    }

    public function get_details(){
        return array(
            'name' => 'Shortcoder',
            'icon' => 'assets/img/icon-shortcoder.svg',
        );
    }

}
