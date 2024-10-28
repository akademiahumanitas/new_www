<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_advanced_custom_fields Class
 *
 * This class integrates all Advanced Custom Fields related features and endpoints
 *
 * @since 4.2.2
 */
class WP_Webhooks_Integrations_advanced_custom_fields {

    public function is_active(){
        return class_exists( 'ACF' );
    }

    public function get_details(){
        return array(
            'name' => 'Advanced Custom Fields',
            'icon' => 'assets/img/icon-advanced-custom-fields.png',
        );
    }

}
