<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_wpdiscuz Class
 *
 * This class integrates all wpDiscuz related features and endpoints
 *
 * @since 5.1.1
 */
class WP_Webhooks_Integrations_wpdiscuz {

    public function is_active(){
        return class_exists('WpdiscuzCore');
    }

    public function get_details(){
        return array(
            'name' => 'wpDiscuz',
            'icon' => 'assets/img/icon-wpdiscuz.svg',
        );
    }

}
