<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_wp_all_import Class
 *
 * This class integrates all WP All Import related features and endpoints
 *
 * @since 5.2
 */
class WP_Webhooks_Integrations_wp_all_import {

    public function is_active(){
        return defined( 'PMXI_VERSION' );
    }

    public function get_details(){
        return array(
            'name' => 'WP All Import',
            'icon' => 'assets/img/icon-wp-all-import.svg',
        );
    }

}
