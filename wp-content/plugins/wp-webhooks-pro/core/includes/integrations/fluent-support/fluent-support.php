<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_fluent_support Class
 *
 * This class integrates all Fluent Support related features and endpoints
 *
 * @since 4.3.4
 */
class WP_Webhooks_Integrations_fluent_support {

    public function is_active(){
        return defined( 'FLUENT_SUPPORT_VERSION' );
    }

    public function get_details(){
        return array(
            'name' => 'Fluent Support',
            'icon' => 'assets/img/icon-fluent-support.png',
        );
    }

}
