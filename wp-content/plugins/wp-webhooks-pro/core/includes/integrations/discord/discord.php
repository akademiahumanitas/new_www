<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_discord Class
 *
 * This class integrates all Discord related features and endpoints
 *
 * @since 6.1.4
 */
class WP_Webhooks_Integrations_discord {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'Discord',
            'icon' => 'assets/img/icon-discord.svg',
        );
    }

}
