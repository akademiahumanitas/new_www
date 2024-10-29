<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * WP_Webhooks_Integrations_surecart Class
 *
 * This class integrates all JetFormBuilder related features and endpoints
 *
 * @since 6.0.1
 */
class WP_Webhooks_Integrations_surecart {

	public function is_active() {
		return defined( 'SURECART_PLUGIN_FILE' );
	}

	public function get_details() {
		return array(
			'name' => 'SureCart',
			'icon' => 'assets/img/icon-surecart.svg',
		);
	}

}
