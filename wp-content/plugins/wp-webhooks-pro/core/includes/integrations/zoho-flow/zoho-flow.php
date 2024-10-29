<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_zoho_flow Class
 *
 * This class integrates all Zoho Flow related features and endpoints
 *
 * @since 5.0
 */
class WP_Webhooks_Integrations_zoho_flow {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'Zoho Flow',
            'icon' => 'assets/img/icon-zoho-flow.png',
        );
    }

}
