<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_projecthuddle Class
 *
 * This class integrates all ProjectHuddle related features and endpoints
 *
 * @since 6.0
 */
class WP_Webhooks_Integrations_projecthuddle {

    public function is_active(){
        return class_exists( 'Project_Huddle' );
    }

    public function get_details(){
        return array(
            'name' => 'ProjectHuddle',
            'icon' => 'assets/img/icon-projecthuddle.svg',
        );
    }

}
