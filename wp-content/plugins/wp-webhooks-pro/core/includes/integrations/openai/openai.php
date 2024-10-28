<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_openai Class
 *
 * This class integrates all OpenAI related features and endpoints
 *
 * @since 6.1.1
 */
class WP_Webhooks_Integrations_openai {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'OpenAI',
            'icon' => 'assets/img/icon-openai.png',
        );
    }

}
