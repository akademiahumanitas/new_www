<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_indexnow Class
 *
 * This class integrates all IndexNow related features and endpoints
 *
 * @since 6.0
 */
class WP_Webhooks_Integrations_indexnow {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'IndexNow',
            'icon' => 'assets/img/icon-indexnow.svg',
        );
    }

}
