<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_affiliatewp Class
 *
 * This class integrates all AffiliateWP related features and endpoints
 *
 * @since 4.2.0
 */
class WP_Webhooks_Integrations_affiliatewp {

    public function is_active(){
        return class_exists( 'Affiliate_WP' );
    }

    public function get_details(){
        return array(
            'name' => 'AffiliateWP',
            'icon' => 'assets/img/icon-affiliatewp.svg',
        );
    }

}
