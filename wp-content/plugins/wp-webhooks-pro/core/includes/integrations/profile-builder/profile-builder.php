<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_profile_builder Class
 *
 * This class integrates all Profile Builder by Cozmoslabs related features and endpoints
 *
 * @since 6.1.5
 */
class WP_Webhooks_Integrations_profile_builder {

    public function is_active(){
        return defined( 'PROFILE_BUILDER_VERSION' );
    }

    public function get_details(){
        return array(
            'name' => 'Profile Builder by Cozmoslabs',
            'icon' => 'assets/img/icon-profile-builder.png',
        );
    }

}
