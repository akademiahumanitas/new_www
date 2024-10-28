<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrati
 * ons_wp_fastest_cache Class
 *
 * This class integrates all Wp Fastest Cache related features and endpoints
 *
 * @since 6.0
 */
class WP_Webhooks_Integrations_wp_fastest_cache {

	public function is_active() {
		return class_exists( 'WpFastestCache' );
	}

	public function get_details() {
		return array(
			'name' => 'WP Fastest Cache',
			'icon' => 'assets/img/icon-wp-fastest-cache.png',
		);
	}

}
