<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_ultimate_member Class
 *
 * This class integrates all Ultimate Member related features and endpoints
 *
 * @since 5.2.2
 */
class WP_Webhooks_Integrations_ultimate_member {

    public function is_active(){
        return defined( 'ultimatemember_version' );
    }

    public function get_details(){
        return array(
            'name' => 'Ultimate Member',
            'icon' => 'assets/img/icon-ultimate-member.png',
        );
    }

}
