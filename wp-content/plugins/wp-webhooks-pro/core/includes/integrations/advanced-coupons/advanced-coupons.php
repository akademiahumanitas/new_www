<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_advanced_coupons Class
 *
 * This class integrates all Event Tickets related features and endpoints
 *
 * @since 6.1.1
 */
class WP_Webhooks_Integrations_advanced_coupons {

    public function is_active(){
        return class_exists( 'ACFWF' );
    }

    public function get_details(){
        return array(
            'name' => 'Advanced Coupons',
            'icon' => 'assets/img/icon-advanced-coupons.svg',
        );
    }

}
