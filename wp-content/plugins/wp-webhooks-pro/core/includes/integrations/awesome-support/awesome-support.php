<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * WP_Webhooks_Integrations_awesome_support Class
 *
 * This class integrates all Awesome Support related features and endpoints
 *
 * @since 6.1.0
 */
class WP_Webhooks_Integrations_awesome_support {

	public function is_active() {
		return class_exists( 'Awesome_Support' );
	}

	public function get_details() {
		return array(
			'name' => 'Awesome Support',
			'icon' => 'assets/img/icon-awesome-support.png',
		);
	}

}
