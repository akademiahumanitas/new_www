<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_wp_all_export Class
 *
 * This class integrates all WP All Export related features and endpoints
 *
 * @since 5.2
 */
class WP_Webhooks_Integrations_wp_all_export {

    public function is_active(){
        return defined( 'PMXE_PREFIX' );
    }

    public function get_details(){
        return array(
            'name' => 'WP All Export',
            'icon' => 'assets/img/icon-wp-all-export.svg',
        );
    }

}
