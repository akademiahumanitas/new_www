<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_freshdesk Class
 *
 * This class integrates all Freshdesk related features and endpoints
 *
 * @since 6.1.3
 */
class WP_Webhooks_Integrations_freshdesk {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'Freshdesk',
            'icon' => 'assets/img/icon-freshdesk.svg',
        );
    }

}
