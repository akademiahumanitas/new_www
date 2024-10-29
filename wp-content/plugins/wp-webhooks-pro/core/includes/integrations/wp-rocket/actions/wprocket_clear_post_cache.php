<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_wp_rocket_Actions_wprocket_clear_post_cache' ) ) :

	/**
	 * Load the wprocket_clear_post_cache action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_rocket_Actions_wprocket_clear_post_cache {

		public function get_details() {


			$parameter = array(
				'post_ids' => array( 
					'required'		=> true, 
					'label' => __( 'Post IDs', 'wp-webhooks' ),
					'short_description' => __( '(String) Clear specific post IDs only. To add multiple ones, please comma-separate them. If none are given, all are flushed.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'    => array( 'short_description' => __( '(String) An informative message about the action.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The given post IDs have been cleared.',
			);

			return array(
				'action'            => 'wprocket_clear_post_cache',
				'name'              => __( 'Clear post cache', 'wp-webhooks' ),
				'sentence'          => __( 'flush the cache for one or multiple posts', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Clear the cache for one or multiple posts within W3 Total Cache.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'wp-rocket',
				'premium'           => true,
			);

		}

		/**
		 * Execute function
		 *
		 * @param array $return_data Returning data.
		 * @param  array $response_body Response body.
		 * @return array $return_args
		 */
		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
			);

			$post_ids = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_ids' );

			if( empty( $post_ids ) ){
				$return_args['msg'] = __( 'Please set the post_ids argument.', 'wp-webhooks' );
				return $return_args;
			}
			
			$validated_post_ids = array();
				
			if( WPWHPRO()->helpers->is_json( $post_ids ) ){
				$post_ids_data = json_decode( $post_ids, true );
			} else {
				$post_ids_data = explode( ',', $post_ids );
			}

			if( ! empty( $post_ids_data ) && is_array( $post_ids_data ) ){
				foreach( $post_ids_data as $post_id ){
					$validated_post_ids[] = absint( trim( $post_id ) );
				}
			}

			if( empty( $validated_post_ids ) ){
				$return_args['msg'] = __( 'We could not validate the given post IDs.', 'wp-webhooks' );
				return $return_args;
			}

			if( ! empty( $validated_post_ids ) ){
				foreach( $validated_post_ids as $post_id ){
					rocket_clean_post( $post_id );
				}
			}

			$return_args['msg']     = __( 'The given post IDs have been cleared.', 'wp-webhooks' );
			$return_args['success'] = true;

			return $return_args;

		}

	}

endif; // End if class_exists check.
