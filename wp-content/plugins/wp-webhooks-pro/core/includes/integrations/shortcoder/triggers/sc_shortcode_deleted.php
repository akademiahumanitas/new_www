<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_shortcoder_Triggers_sc_shortcode_deleted' ) ) :

	/**
	 * Load the sc_shortcode_deleted trigger
	 *
	 * @since 6.0.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_shortcoder_Triggers_sc_shortcode_deleted {
		/**
		 * Preserver certain values
		 *
		 * @var array
		 * @since 6.0.
		 */
		private $pre_action_values = array();


		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'before_delete_post',
					'callback'  => array( $this, 'ironikus_prepare_post_delete' ),
					'priority'  => 10,
					'arguments' => 1,
					'delayed'   => false,
				),
				array(
					'type'      => 'action',
					'hook'      => 'delete_post',
					'callback'  => array( $this, 'ironikus_trigger_shortcode_deleted' ),
					'priority'  => 10,
					'arguments' => 1,
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
				'trigger'           => 'sc_shortcode_deleted',
				'name'              => __( 'Shortcode deleted', 'wp-webhooks' ),
				'sentence'          => __( 'Shortcode has been deleted successfully.', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires after new shortcode has been deleted.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'shortcoder',
			);

		}

		/*
		* Preserve the post_before on update_post
		*
		* @since 2.0.5
		*/
		public function ironikus_prepare_post_delete( $shortcode_id ) {

			$this->pre_action_values['delete_post_post_data'][ $shortcode_id ] = get_post( $shortcode_id );
			$this->pre_action_values['delete_post_post_meta'][ $shortcode_id ] = get_post_meta( $shortcode_id );

			$terms = get_the_term_list( $shortcode_id, 'sc_tag', '', ',' );
			$tags  = strip_tags( $terms );
			$this->pre_action_values['delete_post_post_tags'][ $shortcode_id ] = $tags;
		}


		/**
		 * Triggers once an shortcode is deleted
		 *
		 * @param integer $shortcode_id Shortcode id
		 */
		public function ironikus_trigger_shortcode_deleted( $shortcode_id ) {

			$shortcode = isset( $this->pre_action_values['delete_post_post_data'][ $shortcode_id ] ) ? $this->pre_action_values['delete_post_post_data'][ $shortcode_id ] : null;
			if ( empty( $shortcode ) || ! defined( 'SC_POST_TYPE' ) || $shortcode->post_type != SC_POST_TYPE ) {
				return;
			}

			$webhooks            = WPWHPRO()->webhook->get_hooks( 'trigger', 'sc_shortcode_deleted' );
			$response_data_array = array();
			$shortcode_data      = array();

			$tags     = $this->pre_action_values['delete_post_post_tags'][ $shortcode_id ];
			$shortcode_settings = $this->pre_action_values['delete_post_post_meta'][ $shortcode_id ];

			if ( ! empty( $shortcode ) ) {
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
				'success'       => true,
				'msg'           => __( 'The shortcode has been deleted successfully.', 'wp-webhooks' ),
				'shortcode_id'  => $shortcode_id,
				'shorcode_data' => $shortcode_data,
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

			do_action( 'wpwhpro/webhooks/trigger_sc_shortcode_deleted', $payload, $response_data_array );
		}


		public function get_demo( $options = array() ) {

			$data = array(
				'success'        => true,
				'msg'            => 'The shortcode has been deleted successfully.',
				'shortcode_id'   => 182,
				'shortcode_data' =>
				array(
					'post_author' => 1,
					'post_title'  => 'demo',
					'post_name'   => 'demoshortcode__trashed',
					'post_type'   => 'shortcoder',
					'post_status' => 'publish',
					'tax_input'   =>
					array(
						'sc_tag' => 'demo, tag, test',
					),
					'meta_input'  =>
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
