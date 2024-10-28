<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_shortcoder_Triggers_sc_shortcode_updated' ) ) :

	/**
	 * Load the sc_shortcode_updated trigger
	 *
	 * @since 6.0.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_shortcoder_Triggers_sc_shortcode_updated {
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
					'hook'      => 'pre_post_update',
					'callback'  => array( $this, 'ironikus_prepare_pre_shortcode_update' ),
					'priority'  => 20,
					'arguments' => 2,
					'delayed'   => false,
				),
				array(
					'type'      => 'action',
					'hook'      => 'post_updated',
					'callback'  => array( $this, 'ironikus_prepare_post_update' ),
					'priority'  => 10,
					'arguments' => 3,
					'delayed'   => false,
				),
				array(
					'type'      => 'action',
					'hook'      => 'wp_insert_post',
					'callback'  => array( $this, 'ironikus_trigger_shortcode_update' ),
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
					'short_description' => __( '(Array) The shortcode data. Puts only updated values.', 'wp-webhooks' ),
				),
			);

			$settings = array(
				'load_default_settings' => true,
				'data'                  => array(),
			);

			return array(
				'trigger'           => 'sc_shortcode_updated',
				'name'              => __( 'Shortcode updated', 'wp-webhooks' ),
				'sentence'          => __( 'a shortcode has been updated', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires after a shortcode has been updated within Shortcoder.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'shortcoder',
			);

		}

		/*
		* Preserve the post_before on update_post
		*
		* @since 2.0.5
		*/
		public function ironikus_prepare_post_update( $post_ID, $post_after, $post_before ) {
			$this->pre_action_values['update_post_post_before'] = $post_before;
		}


		/**
		 * Triggers once an shortcode is created
		 *
		 * @param integer $shortcode_id Shortcode id
		 * @param object  $post_after Shortcode after update
		 * @param object  $post_before Shortcode before update
		 */
		public function ironikus_trigger_shortcode_update( $shortcode_id, $shortcode, $update ) {

			if ( ! defined( 'SC_POST_TYPE' ) || $shortcode->post_type != SC_POST_TYPE ) {
				return;
			}

			if ( $shortcode->post_status != 'publish' ) {
				return;
			}

			$webhooks            = WPWHPRO()->webhook->get_hooks( 'trigger', 'sc_shortcode_updated' );
			$response_data_array = array();

			$shortcode_after  = (array) $shortcode;
			$shortcode_before = (array) $this->pre_action_values['update_post_post_before'];
			$shortcode_data   = array();

			if ( $shortcode_after['post_author'] != $shortcode_before['post_author'] ) {
				$shortcode_data['post_author'] = $shortcode_after['post_author'];
			}

			if ( $shortcode_after['post_title'] != $shortcode_before['post_title'] ) {
				$shortcode_data['post_title'] = $shortcode_after['post_title'];
			}

			if ( $shortcode_after['post_name'] != $shortcode_before['post_name'] ) {
				$shortcode_data['post_name'] = $shortcode_after['post_name'];
			}

			if ( $shortcode_after['post_content'] != $shortcode_before['post_content'] ) {
				$shortcode_data['post_content'] = $shortcode_after['post_content'];
			}

			$terms      = get_the_term_list( $shortcode_id, 'sc_tag', '', ',' );
			$tags_after = strip_tags( $terms );

			if ( $tags_after != $this->pre_action_values['update_shortcode_tags_before'] ) {
				$shortcode_data['tax_input']['sc_tag'] = $tags_after;
			}

			$settings_after  = get_post_meta( $shortcode_id );
			$settings_before = $this->pre_action_values['update_shortcode_meta_before'];

			if ( $settings_after['_sc_editor'][0] != $settings_before['_sc_editor'][0] ) {
				$shortcode_data['meta_input']['_sc_editor'] = $settings_after['_sc_editor'][0];
			}

			if ( $settings_after['_sc_description'][0] != $settings_before['_sc_description'][0] ) {
				$shortcode_data['meta_input']['_sc_description'] = $settings_after['_sc_description'][0];
			}

			if ( $settings_after['_sc_disable_sc'][0] != $settings_before['_sc_disable_sc'][0] ) {
				$shortcode_data['meta_input']['_sc_disable_sc'] = $settings_after['_sc_disable_sc'][0];
			}

			if ( $settings_after['_sc_disable_admin'][0] != $settings_before['_sc_disable_admin'][0] ) {
				$shortcode_data['meta_input']['_sc_disable_admin'] = $settings_after['_sc_disable_admin'][0];
			}

			if ( $settings_after['_sc_allowed_devices'][0] != $settings_before['_sc_allowed_devices'][0] ) {
				$shortcode_data['meta_input']['_sc_allowed_devices'] = $settings_after['_sc_allowed_devices'][0];
			}

			$payload = array(
				'success'        => true,
				'msg'            => __( 'The shortcode has been updated.', 'wp-webhooks' ),
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

			do_action( 'wpwhpro/webhooks/trigger_sc_shortcode_updated', $payload, $response_data_array );
		}


		/*
		* Preserve the shortcode meta on update_post
		*
		* @since 6.0.0
		*/
		public function ironikus_prepare_pre_shortcode_update( $shortcode_id, $data ) {

			$this->pre_action_values['update_shortcode_meta_before'] = get_post_meta( $shortcode_id );

			$terms = get_the_term_list( $shortcode_id, 'sc_tag', '', ',' );
			$tags  = strip_tags( $terms );

			$this->pre_action_values['update_shortcode_tags_before'] = $tags;

		}

		public function get_demo( $options = array() ) {

			$data = array(
				'success'        => true,
				'msg'            => 'The shortcode has been updated.',
				'shortcode_id'   => 182,
				'shortcode_data' =>
				array(
					'post_author' => 1,
					'post_title'  => 'demo',
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
						'_sc_allowed_devices' => 'all',
					),
				),
			);

			return $data;
		}

	}

endif; // End if class_exists check.
