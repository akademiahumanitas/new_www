<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_restrict_content_pro Class
 *
 * This class integrates all Restrict Content Pro related features and endpoints
 *
 * @since 4.3.6
 */
class WP_Webhooks_Integrations_restrict_content_pro {

    public function is_active(){
        return class_exists( 'RCP_Requirements_Check' );
    }

    public function get_details(){
        return array(
            'name' => 'Restrict Content Pro',
            'icon' => 'assets/img/icon-restrict-content-pro.svg',
        );
    }

}
