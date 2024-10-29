<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_emailable Class
 *
 * This class integrates all Emailable related features and endpoints
 *
 * @since 6.1.3
 */
class WP_Webhooks_Integrations_emailable {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'Emailable',
            'icon' => 'assets/img/icon-emailable.svg',
        );
    }

}
