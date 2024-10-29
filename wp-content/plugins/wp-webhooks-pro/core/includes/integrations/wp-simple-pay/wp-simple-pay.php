<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_wp_simple_pay Class
 *
 * This class integrates all Contact Form 7 related features and endpoints
 *
 * @since 4.2.0
 */
class WP_Webhooks_Integrations_wp_simple_pay {

    public function is_active(){
        return defined( 'SIMPLE_PAY_VERSION' );
    }

    public function get_details(){
        return array(
            'name' => 'WP Simple Pay',
            'icon' => 'assets/img/icon-wp-simple-pay.png',
        );
    }

}
