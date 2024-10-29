<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_filemaker Class
 *
 * This class integrates all FileMaker related features and endpoints
 *
 * @since 6.1.4
 */
class WP_Webhooks_Integrations_filemaker {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'FileMaker by Claris',
            'icon' => 'assets/img/icon-filemaker.png',
        );
    }

}
