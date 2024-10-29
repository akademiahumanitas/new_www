<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_klicktipp Class
 *
 * This class integrates all KlickTipp related features and endpoints
 *
 * @since 5.2
 */
class WP_Webhooks_Integrations_klicktipp {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'KlickTipp',
            'icon' => 'assets/img/icon-klicktipp.svg',
        );
    }

}
