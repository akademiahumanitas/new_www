<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_typeform Class
 *
 * This class integrates all Typeform related features and endpoints
 *
 * @since 6.1.4
 */
class WP_Webhooks_Integrations_typeform {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'Typeform',
            'icon' => 'assets/img/icon-typeform.svg',
        );
    }

}
