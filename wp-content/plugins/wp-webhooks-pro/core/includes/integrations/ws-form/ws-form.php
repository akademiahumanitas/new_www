<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_ws_form Class
 *
 * This class integrates all WS Form related features and endpoints
 *
 * @since 4.3.5
 */
class WP_Webhooks_Integrations_ws_form {

    public function is_active(){
        return class_exists( 'WS_Form' );
    }

    public function get_details(){
        return array(
            'name' => 'WS Form',
            'icon' => 'assets/img/icon-ws-form.svg',
        );
    }

}
