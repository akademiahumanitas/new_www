<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_n8n Class
 *
 * This class integrates all n8n related features and endpoints
 *
 * @since 5.0
 */
class WP_Webhooks_Integrations_n8n {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'n8n',
            'icon' => 'assets/img/icon-n8n.png',
        );
    }

}
