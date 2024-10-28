<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_litespeed_cache_Actions_lsc_purge_cached_post' ) ) :

	/**
	 * Load the lsc_purge_cached_post action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_litespeed_cache_Actions_lsc_purge_cached_post {

		public function get_details() {


			$parameter = array(
				'post_ids' => array( 
					'required'		=> true, 
					'label' => __( 'Post IDs', 'wp-webhooks' ),
					'short_description' => __( '(String) Clear specific post IDs only. To add multiple ones, please comma-separate them.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'    => array( 'short_description' => __( '(String) An informative message about the action.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(array) Further details about the fired actions.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The post(s) have been purged.',
				'data'	  => array(
					'post_ids' => array(
						12,
						33,
						29
					)
				)
			);

			return array(
				'action'            => 'lsc_purge_cached_post', // required
				'name'              => __( 'Purge cached posts', 'wp-webhooks' ),
				'sentence'          => __( 'purge one or multiple cached posts', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Purge one or multiple cached posts within LiteSpeed Cache.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'litespeed-cache',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
				'data'	  => array(
					'post_ids' => array()
				)
			);

			$post_ids = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_ids' );

			if( empty( $post_ids ) ){
				$return_args['msg']     = __( 'Please set the post_ids argument.', 'wp-webhooks' );
				return $return_args;
			}

			$validated_post_ids = array();
			
			if( WPWHPRO()->helpers->is_json( $post_ids ) ){
				$post_ids_data = json_decode( $post_ids, true );
			} else {
				$post_ids_data = explode( ',', $post_ids );
			}

			if( ! empty( $post_ids_data ) && is_array( $post_ids_data ) ){
				foreach( $post_ids_data as $post_type ){
					$validated_post_ids[] = absint( trim( $post_type ) );
				}
			}

			if( empty( $validated_post_ids ) ){
				$return_args['msg']     = __( 'We could not validate the given post IDs.', 'wp-webhooks' );
				return $return_args;
			}

			if( ! empty( $validated_post_ids ) ){
				foreach( $validated_post_ids as $post_id ){
					do_action( 'litespeed_purge_post', $post_id );
				}
			}

			$return_args['success'] = true;
			$return_args['msg']     = __( 'The post(s) have been purged.', 'wp-webhooks' );
			$return_args['data']['post_ids'] = $validated_post_ids;

			return $return_args;

		}

	}

endif; // End if class_exists check.
