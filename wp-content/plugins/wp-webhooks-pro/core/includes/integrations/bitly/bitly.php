<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_bitly Class
 *
 * This class integrates all Bitly related features and endpoints
 *
 * @since 6.1.2
 */
class WP_Webhooks_Integrations_bitly {

    public function is_active(){
        return true;
    }

    public function get_details(){
        return array(
            'name' => 'Bitly',
            'icon' => 'assets/img/icon-bitly.svg',
        );
    }

}
