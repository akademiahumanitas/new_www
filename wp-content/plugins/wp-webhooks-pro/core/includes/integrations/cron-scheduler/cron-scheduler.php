<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_cron_scheduler Class
 *
 * This class integrates all Zapier related features and endpoints
 *
 * @since 6.1.4
 */
class WP_Webhooks_Integrations_cron_scheduler {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'Cron Scheduler',
            'icon' => 'assets/img/icon-cron-scheduler.svg',
        );
    }

}
