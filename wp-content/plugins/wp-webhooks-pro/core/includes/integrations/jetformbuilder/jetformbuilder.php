<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * WP_Webhooks_Integrations_jetformbuilder Class
 *
 * This class integrates all JetFormBuilder related features and endpoints
 *
 * @since 6.0.1
 */
class WP_Webhooks_Integrations_jetformbuilder {

	public function is_active() {
		return function_exists( 'jet_form_builder_init' );
	}

	public function get_details() {
		return array(
			'name' => 'JetFormBuilder',
			'icon' => 'assets/img/icon-jetformbuilder.png',
		);
	}

}
