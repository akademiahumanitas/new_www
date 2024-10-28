<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * WP_Webhooks_Integrations_jetpack_crm Class
 *
 * This class integrates all Jetpack CRM related features and endpoints
 *
 * @since 6.0.1
 */
class WP_Webhooks_Integrations_jetpack_crm {

	public function is_active() {
		return defined( 'ZEROBSCRM_PATH' );
	}

	public function get_details() {
		return array(
			'name' => 'Jetpack CRM',
			'icon' => 'assets/img/icon-jetpack-crm.svg',
		);
	}

}
