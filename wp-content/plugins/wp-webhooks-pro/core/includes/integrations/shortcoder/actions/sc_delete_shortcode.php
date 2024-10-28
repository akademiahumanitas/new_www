<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_shortcoder_Actions_sc_delete_shortcode' ) ) :

	/**
	 * Load the sc_delete_shortcode action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_shortcoder_Actions_sc_delete_shortcode {

		public function get_details() {


			$parameter = array(
				'shortcode_id' => array(
					'required'          => true,
					'label'             => __( 'Shortcode id', 'wp-webhooks' ),
					'short_description' => __( '(Integer) "The shorcode id to delete.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further information about the shortcode.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The shortcode has been successfully deleted',
				'data'    =>
				array(
					'shortcode_id' => 170,
				),
			);

			return array(
				'action'            => 'sc_delete_shortcode', // required
				'name'              => __( 'Delete shortcode', 'wp-webhooks' ),
				'sentence'          => __( 'delete a shortcode', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Edit a shortcode within Shortcoder.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'shortcoder',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
				'data'    => array(),
			);

			$shortcode_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'shortcode_id' ) );

			if ( ! empty( $shortcode_id ) ) {
				$shortcode = get_post( $shortcode_id );
			}

			if ( empty( $shortcode ) ) {
				$return_args['msg'] = __( 'No shortcode found', 'wp-webhooks' );
				return $return_args;
			}

			if ( $shortcode->post_type != SC_POST_TYPE ) {
				$return_args['msg'] = __( 'Error getting a wrong post type.', 'wp-webhooks' );
				return $return_args;
			}

			$check = wp_delete_post( $shortcode->ID, true );

			if ( $check ) {
				$return_args['success']      = true;
				$return_args['msg']          = __( 'The shortcode has been deleted successfully.', 'wp-webhooks' );
				$return_args['data']['shortcode_id'] = $shortcode->ID;
			} else {
				$return_args['msg']                  = __( 'Error creating a shortcode.', 'wp-webhooks' );
				$return_args['data']['shortcode_id'] = $shortcode_id;
			}

			return $return_args;

		}

	}

endif; // End if class_exists check.
