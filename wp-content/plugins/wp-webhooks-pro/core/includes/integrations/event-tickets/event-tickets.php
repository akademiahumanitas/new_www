<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_event_tickets Class
 *
 * This class integrates all Event Tickets related features and endpoints
 *
 * @since 6.1.1
 */
class WP_Webhooks_Integrations_event_tickets {

    public function is_active(){
        return defined( 'EVENT_TICKETS_DIR' );
    }

    public function get_details(){
        return array(
            'name' => 'Event Tickets',
            'icon' => 'assets/img/icon-the-events-tickets.svg',
        );
    }

}
