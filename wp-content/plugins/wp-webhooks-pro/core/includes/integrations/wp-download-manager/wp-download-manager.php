<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * WP_Webhooks_Integrations_wp_download_manager Class
 *
 * This class integrates all JetFormBuilder related features and endpoints
 *
 * @since 6.1.0
 */
class WP_Webhooks_Integrations_wp_download_manager {

	public function is_active() {
		return class_exists( 'WPDM\WordPressDownloadManager' );
	}

	public function get_details() {
		return array(
			'name' => 'WordPress Download Manager',
			'icon' => 'assets/img/icon-wp-download-manager.svg',
		);
	}

}
