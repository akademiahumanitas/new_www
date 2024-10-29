<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_metabox Class
 *
 * This class integrates all Meta Box related features and endpoints
 *
 * @since 5.2.4
 */
class WP_Webhooks_Integrations_meta_box {

    public function is_active(){
        return class_exists( 'RWMB_Loader' );
    }

    public function get_details(){
        return array(
            'name' => 'Meta Box',
            'icon' => 'assets/img/icon-meta-box.svg',
        );
    }

}
