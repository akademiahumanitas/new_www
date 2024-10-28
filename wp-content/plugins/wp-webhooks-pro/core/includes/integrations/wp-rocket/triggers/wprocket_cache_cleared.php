<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_wp_rocket_Triggers_wprocket_cache_cleared' ) ) :

	/**
	 * Load the wprocket_cache_cleared trigger
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_rocket_Triggers_wprocket_cache_cleared {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'rocket_purge_cache',
					'callback'  => array( $this, 'wprocket_cache_cleared_callback' ),
					'priority'  => 20,
					'arguments' => 4,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {


			$parameter = array(
				'success'  => array( 'short_description' => __( '(Bool) True if the trigger was successful.', 'wp-webhooks' ) ),
				'msg'      => array( 'short_description' => __( '(String) Some informative text about the action.', 'wp-webhooks' ) ),
				'type'     => array( 'short_description' => __( '(String) The type of the cleared cache. E.g. all, post, term, user, url.', 'wp-webhooks' ) ),
				'id'       => array( 'short_description' => __( '(Integer) The post ID, term ID, or user ID being cleared. 0 when $type is not post, term, or user.', 'wp-webhooks' ) ),
				'taxonomy' => array( 'short_description' => __( '(String) The taxonomy the term being cleared. Empty when $type is not term', 'wp-webhooks' ) ),
				'url'      => array( 'short_description' => __( '(String) The URL being cleared. Empty when $type is not url.', 'wp-webhooks' ) ),
			);

			$settings = array(
				'load_default_settings' => false,
				'data'                  => array(
					'wpwhpro_wp_rocket_trigger_on_selected_types' => array(
						'id'          => 'wpwhpro_wp_rocket_trigger_on_selected_types',
						'type'        => 'select',
						'multiple'    => true,
						'label'       => __( 'Trigger on selected cache types', 'wp-webhooks' ),
						'choices'     => array(
							'all'  => array( 'label' => __( 'All', 'wp-webhooks' ) ),
							'post' => array( 'label' => __( 'Post', 'wp-webhooks' ) ),
							'term' => array( 'label' => __( 'Term', 'wp-webhooks' ) ),
							'user' => array( 'label' => __( 'User', 'wp-webhooks' ) ),
							'url'  => array( 'label' => __( 'URL', 'wp-webhooks' ) ),
						),
						'required'    => false,
						'description' => __( 'Trigger this webhook only on specific cache types. You can also choose multiple ones. If none are selected, all are triggered.', 'wp-webhooks' ),
					),
				),
			);

			return array(
				'trigger'           => 'wprocket_cache_cleared',
				'name'              => __( 'Cache cleared', 'wp-webhooks' ),
				'sentence'          => __( 'the cache was cleared', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires as soon as the cache was cleared within WP Rocket.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'wp-rocket',
				'premium'           => true,
			);

		}

		public function wprocket_cache_cleared_callback( $type, $id, $taxonomy, $url ) {

			$webhooks            = WPWHPRO()->webhook->get_hooks( 'trigger', 'wprocket_cache_cleared' );
			$response_data_array = array();

			$payload = array(
				'success'  => true,
				'msg'  	   => __( 'The cache has been cleared successfully.', 'wp-webhooks' ),
				'type'     => $type,
				'id'       => $id,
				'taxonomy' => $taxonomy,
				'url'      => $url,
			);

			foreach ( $webhooks as $webhook ) {

				$webhook_url_name = ( is_array( $webhook ) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
				$is_valid         = true;

				if ( isset( $webhook['settings'] ) ) {

					if ( isset( $webhook['settings']['wpwhpro_wp_rocket_trigger_on_selected_types'] ) && ! empty( $webhook['settings']['wpwhpro_wp_rocket_trigger_on_selected_types'] ) ) {
						$is_valid = false;

						if ( in_array( $type, $webhook['settings']['wpwhpro_wp_rocket_trigger_on_selected_types'] ) ) {
							$is_valid = true;
						}
					}
				}

				if ( $is_valid ) {
					if ( $webhook_url_name !== null ) {
						$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					} else {
						$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					}
				}
			}

			do_action( 'wpwhpro/webhooks/trigger_wprocket_cache_cleared', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array(
				'success'  => true,
				'msg'      => 'The cache has been cleared successfully.',
				'type'     => 'post',
				'id'       => 9083,
				'taxonomy' => '',
				'url'      => '',
			);

			return $data;
		}

	}

endif; // End if class_exists check.
