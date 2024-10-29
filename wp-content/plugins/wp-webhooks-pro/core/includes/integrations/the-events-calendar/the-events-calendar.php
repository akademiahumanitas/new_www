<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_the_events_calendar Class
 *
 * This class integrates all The Events Calendar related features and endpoints
 *
 * @since 6.1.0
 */
class WP_Webhooks_Integrations_the_events_calendar {

    public function is_active(){
        return defined( 'TRIBE_EVENTS_FILE' );
    }

    public function get_details(){
        return array(
            'name' => 'The Events Calendar',
            'icon' => 'assets/img/icon-the-events-calendar.svg',
        );
    }

}
