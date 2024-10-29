<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_gravity_pdf Class
 *
 * This class integrates all Gravity PDF related features and endpoints
 *
 * @since 6.0
 */
class WP_Webhooks_Integrations_gravity_pdf {

    public function is_active(){
        return class_exists( 'GFPDF_Major_Compatibility_Checks' );
    }

    public function get_details(){
        return array(
            'name' => 'Gravity PDF',
            'icon' => 'assets/img/icon-gravity-pdf.svg',
        );
    }

}
