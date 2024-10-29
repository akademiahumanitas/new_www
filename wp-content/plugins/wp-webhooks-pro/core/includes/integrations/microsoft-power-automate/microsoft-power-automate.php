<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_microsoft_power_automate Class
 *
 * This class integrates all Microsoft Power Automate related features and endpoints
 *
 * @since 5.2.4
 */
class WP_Webhooks_Integrations_microsoft_power_automate {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'Microsoft Power Automate',
            'icon' => 'assets/img/icon-microsoft-power-automate.svg',
        );
    }

}
