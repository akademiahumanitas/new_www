<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_autonami Class
 *
 * This class integrates all FunnelKit Automations related features and endpoints
 *
 * @since 5.2.5
 */
class WP_Webhooks_Integrations_autonami {

    public function is_active(){
        return class_exists( 'BWFCRM_Contact' );
    }

    public function get_details(){
        return array(
            'name' => 'FunnelKit Automations',
            'icon' => 'assets/img/icon-autonami.svg',
        );
    }

}
