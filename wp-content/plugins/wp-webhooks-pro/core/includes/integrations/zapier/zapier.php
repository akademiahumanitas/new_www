<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_zapier Class
 *
 * This class integrates all Zapier related features and endpoints
 *
 * @since 5.0
 */
class WP_Webhooks_Integrations_zapier {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'Zapier',
            'icon' => 'assets/img/icon-zapier.png',
        );
    }

}
