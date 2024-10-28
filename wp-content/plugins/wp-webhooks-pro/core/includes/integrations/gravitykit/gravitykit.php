<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_gravitykit Class
 *
 * This class integrates all GravityKit related features and endpoints
 *
 * @since 5.2.4
 */
class WP_Webhooks_Integrations_gravitykit {

    public function is_active(){
        return class_exists( 'GravityView_Plugin' );
    }

    public function get_details(){
        return array(
            'name' => 'GravityKit',
            'icon' => 'assets/img/icon-gravitykit.svg',
        );
    }

}
