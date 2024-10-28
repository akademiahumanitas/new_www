<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_wp_polls Class
 *
 * This class integrates all WP-Polls related features and endpoints
 *
 * @since 5.1.1
 */
class WP_Webhooks_Integrations_wp_polls {

    public function is_active(){
        return defined('WP_POLLS_VERSION');
    }

    public function get_details(){
        return array(
            'name' => 'WP-Polls',
            'icon' => 'assets/img/icon-wp-polls.svg',
        );
    }

}
