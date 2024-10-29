<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_tutor_lms Class
 *
 * This class integrates all Tutor LMS related features and endpoints
 *
 * @since 5.1
 */
class WP_Webhooks_Integrations_tutor_lms {

    public function is_active(){
        return function_exists( 'tutor' );
    }

    public function get_details(){
        return array(
            'name' => 'Tutor LMS',
            'icon' => 'assets/img/icon-tutor-lms.svg',
        );
    }

}
