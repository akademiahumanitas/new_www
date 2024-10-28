<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * WP_Webhooks_Integrations_sql Class
 *
 * This class integrates all SQL related features and endpoints
 *
 * @since 6.1.0
 */
class WP_Webhooks_Integrations_sql {

	public function is_active() {
		return true;
	}

	public function get_details() {
		return array(
			'name' => 'SQL',
			'icon' => 'assets/img/icon-sql.svg',
		);
	}

}

