<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_yoast_seo Class
 *
 * This class integrates all Yoast SEO related features and endpoints
 *
 * @since 6.1.1
 */
class WP_Webhooks_Integrations_yoast_seo {

    public function is_active(){
        return defined( 'WPSEO_FILE' );
    }

    public function get_details(){
        return array(
            'name' => 'Yoast SEO',
            'icon' => 'assets/img/icon-yoast-seo.svg',
        );
    }

}
