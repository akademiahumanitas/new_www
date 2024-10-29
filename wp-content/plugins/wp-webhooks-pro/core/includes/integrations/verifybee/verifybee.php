<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_verifybee Class
 *
 * This class integrates all VerifyBee related features and endpoints
 *
 * @since 6.1.3
 */
class WP_Webhooks_Integrations_verifybee {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'VerifyBee',
            'icon' => 'assets/img/icon-verifybee.png',
        );
    }

}
