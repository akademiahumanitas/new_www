<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_typebot Class
 *
 * This class integrates all Typebot related features and endpoints
 *
 * @since 6.1.3
 */
class WP_Webhooks_Integrations_typebot {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'Typebot',
            'icon' => 'assets/img/icon-typebot.svg',
        );
    }

}
