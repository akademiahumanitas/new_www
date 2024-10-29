<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_ninjaforms Class
 *
 * This class integrates all Ninja Forms related features and endpoints
 *
 * @since 4.2.1
 */
class WP_Webhooks_Integrations_ninjaforms {

    public function is_active(){
        return function_exists( 'Ninja_Forms' );
    }

    public function get_details(){
        return array(
            'name' => 'Ninja Forms',
            'icon' => 'assets/img/icon-ninjaforms.png',
        );
    }

}
