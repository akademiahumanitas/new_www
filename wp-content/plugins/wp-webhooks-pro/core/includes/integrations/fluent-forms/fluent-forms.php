<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_fluent_forms Class
 *
 * This class integrates all Fluent Forms related features and endpoints
 *
 * @since 4.2.2
 */
class WP_Webhooks_Integrations_fluent_forms {

    public function is_active(){
        return defined( 'FLUENTFORM' );
    }

    public function get_details(){
        return array(
            'name' => 'Fluent Forms',
            'icon' => 'assets/img/icon-fluent-forms.png',
        );
    }

}
