<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_newsletter Class
 *
 * This class integrates all Newsletter related features and endpoints
 *
 * @since 4.2.2
 */
class WP_Webhooks_Integrations_newsletter {

    public function is_active(){
        return defined('NEWSLETTER_VERSION');
    }

    public function get_details(){
        return array(
            'name' => 'Newsletter',
            'icon' => 'assets/img/icon-newsletter.png',
        );
    }

}
