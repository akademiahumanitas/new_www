<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_email_list_verify Class
 *
 * This class integrates all Email List Verify related features and endpoints
 *
 * @since 6.1.3
 */
class WP_Webhooks_Integrations_email_list_verify {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'Email List Verify',
            'icon' => 'assets/img/icon-email-list-verify.svg',
        );
    }

}
