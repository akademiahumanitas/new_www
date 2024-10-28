<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_forminator Class
 *
 * This class integrates all Forminator related features and endpoints
 *
 * @since 4.2.2
 */
class WP_Webhooks_Integrations_forminator {

    public function is_active(){
        return class_exists( 'Forminator' );
    }

    public function get_details(){
        return array(
            'name' => 'Forminator',
            'icon' => 'assets/img/icon-forminator.png',
        );
    }

}
