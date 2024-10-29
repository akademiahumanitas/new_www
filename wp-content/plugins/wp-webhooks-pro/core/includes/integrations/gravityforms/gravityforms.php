<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_gravityforms Class
 *
 * This class integrates all Gravity Forms related features and endpoints
 *
 * @since 4.2.0
 */
class WP_Webhooks_Integrations_gravityforms {

    public function is_active(){
        return class_exists( 'GFForms' );
    }

    public function get_details(){
        return array(
            'name' => 'Gravity Forms',
            'icon' => 'assets/img/icon-gravityforms.svg',
        );
    }

}
