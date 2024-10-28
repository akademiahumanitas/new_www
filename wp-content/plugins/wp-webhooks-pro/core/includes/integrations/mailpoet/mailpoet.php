<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * WP_Webhooks_Integrations_mailpoet Class
 *
 * This class integrates all MailPoet related features and endpoints
 *
 * @since 6.0.1
 */
class WP_Webhooks_Integrations_mailpoet {

	public function is_active() {
		return class_exists( '\MailPoet\Config\Activator' );
	}

	public function get_details() {
		return array(
			'name' => 'MailPoet',
			'icon' => 'assets/img/icon-mailpoet.svg',
		);
	}

}

