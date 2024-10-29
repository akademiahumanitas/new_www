<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_learndash Class
 *
 * This class integrates all LearnDash related features and endpoints
 *
 * @since 4.3.1
 */
class WP_Webhooks_Integrations_learndash {

    public function is_active(){
        return defined( 'LEARNDASH_VERSION' );
    }

    public function get_details(){
        return array(
            'name' => 'LearnDash',
            'icon' => 'assets/img/icon-learndash.png',
        );
    }

}
