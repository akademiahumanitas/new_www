<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_shortcoder_Triggers_sc_shortcode_created' ) ) :

	/**
	 * Load the sc_shortcode_created trigger
	 *
	 * @since 6.0.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_shortcoder_Triggers_sc_shortcode_created {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'wp_insert_post',
					'callback'  => array( $this, 'wpwh_trigger_sc_shortcode_created' ),
					'priority'  => 10,
					'arguments' => 3,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {


			$parameter = array(
				'success'        => array( 'short_description' => __( '(Bool) True if the cache was successfully cleared.', 'wp-webhooks' ) ),
				'msg'            => array( 'short_description' => __( '(String) Further details about the clearing of the cache.', 'wp-webhooks' ) ),
				'shortcode_id'   => array(
					'label'             => __( 'Shortcode ID', 'wp-webhooks' ),
					'short_description' => __( '(Integer) The shortcode id.', 'wp-webhooks' ),
				),
				'shortcode_data' => array(
					'label'             => __( 'Shortcode data', 'wp-webhooks' ),
					'short_description' => __( '(Array) The shortcode data.', 'wp-webhooks' ),
				),
			);

			$settings = array(
				'load_default_settings' => true,
				'data'                  => array(),
			);

			return array(
				'trigger'           => 'sc_shortcode_created',
				'name'              => __( 'Shortcode created', 'wp-webhooks' ),
				'sentence'          => __( 'a shortcode has been created', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires after a shortcode has been created within Shortcoder.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'shortcoder',
			);

		}

		/**
		 * Triggers once an shortcode is created
		 *
		 * @param integer $shortcode_id Shortcode id
		 * @param object  $shortcode Shortcode data
		 */
		public function wpwh_trigger_sc_shortcode_created( $shortcode_id, $shortcode ) {

			if ( ! defined( 'SC_POST_TYPE' ) || $shortcode->post_type != SC_POST_TYPE ) {
				return;
			}

			if ( $shortcode->post_status != 'publish' ) {
				return;
			}

			$webhooks            = WPWHPRO()->webhook->get_hooks( 'trigger', 'sc_shortcode_created' );
			$response_data_array = array();

			$terms    = get_the_term_list( $shortcode_id, 'sc_tag', '', ',' );
			$tags     = strip_tags( $terms );
			$shortcode_settings = get_post_meta( $shortcode_id );

			$shortcode_data = array();

			if ( ! empty( $settings ) ) {
				$shortcode_data = array(
					'post_author'  => $shortcode->post_author,
					'post_title'   => $shortcode->post_title,
					'post_name'    => $shortcode->post_name,
					'post_content' => $shortcode->post_content,
					'post_type'    => $shortcode->post_type,
					'post_status'  => $shortcode->post_status,
					'tax_input'    =>
					array(
						'sc_tag' => $tags,
					),
					'meta_input'   =>
					array(
						'_sc_editor'          => $shortcode_settings['_sc_editor'][0],
						'_sc_description'     => $shortcode_settings['_sc_description'][0],
						'_sc_disable_sc'      => $shortcode_settings['_sc_disable_sc'][0],
						'_sc_disable_admin'   => $shortcode_settings['_sc_disable_admin'][0],
						'_sc_allowed_devices' => $shortcode_settings['_sc_allowed_devices'][0],
					),
				);
			}

			$payload = array(
				'success'        => true,
				'msg'            => __( 'The shortcode has been created.', 'wp-webhooks' ),
				'shortcode_id'   => $shortcode_id,
				'shortcode_data' => $shortcode_data,
			);

			foreach ( $webhooks as $webhook ) {

				$webhook_url_name = ( is_array( $webhook ) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
				$is_valid         = true;

				if ( $is_valid ) {
					if ( $webhook_url_name !== null ) {
						$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					} else {
						$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					}
				}
			}

			do_action( 'wpwhpro/webhooks/trigger_sc_shortcode_created', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array(
				'success'        => true,
				'msg'            => 'The shortcode has been created.',
				'shortcode_id'   => 182,
				'shortcode_data' =>
				array(
					'post_author'  => 1,
					'post_title'   => 'demo',
					'post_name'    => 'demo_shortcode',
					'post_content' => '',
					'post_type'    => 'shortcoder',
					'post_status'  => 'publish',
					'tax_input'    =>
					array(
						'sc_tag' => 'demo, tag, test',
					),
					'meta_input'   =>
					array(
						'_sc_editor'          => 'code',
						'_sc_description'     => 'demo description',
						'_sc_disable_sc'      => 'no',
						'_sc_disable_admin'   => 'no',
						'_sc_allowed_devices' => 'all',
					),
				),
			);
			return $data;
		}

	}

endif; // End if class_exists check.
