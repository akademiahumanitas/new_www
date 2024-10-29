<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_autoptimize Class
 *
 * This class integrates all Autoptimize related features and endpoints
 *
 * @since 6.0
 */
class WP_Webhooks_Integrations_autoptimize {

    public function is_active(){
        return defined( 'AUTOPTIMIZE_PLUGIN_VERSION' );
    }

    public function get_details(){
        return array(
            'name' => 'Autoptimize',
            'icon' => 'assets/img/icon-autoptimize.png',
        );
    }

}
