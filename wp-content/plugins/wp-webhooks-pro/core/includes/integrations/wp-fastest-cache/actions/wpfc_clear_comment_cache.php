<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_fastest_cache_Actions_wpfc_clear_comment_cache' ) ) :

	/**
	 * Load the wpfc_clear_comment_cache action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_fastest_cache_Actions_wpfc_clear_comment_cache {

		public function get_details() {


			$parameter = array(
				'comment_ids' => array( 
					'required'		=> true, 
					'label' => __( 'Comment IDs', 'wp-webhooks' ),
					'short_description' => __( '(String) Clear specific comment IDs only. To add multiple ones, please comma-separate them. If none are given, all are flushed.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'    => array( 'short_description' => __( '(String) An informative message about the action.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The cached comment IDs have been cleared.',
			);

			return array(
				'action'            => 'wpfc_clear_comment_cache', // required
				'name'              => __( 'Clear comment cache', 'wp-webhooks' ),
				'sentence'          => __( 'clear the cache of one or multiple comments', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Clear the cache of one or multiple comments within WP Fastest Cache.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'wp-fastest-cache',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
			);

			$comment_ids = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_ids' );

			if( empty( $comment_ids ) ){
				$return_args['msg']     = __( 'Please set the comment_ids argument.', 'wp-webhooks' );
				return $return_args;
			}

			$validated_comment_ids = array();
				
			if( WPWHPRO()->helpers->is_json( $comment_ids ) ){
				$comment_ids_data = json_decode( $comment_ids, true );
			} else {
				$comment_ids_data = explode( ',', $comment_ids );
			}

			if( ! empty( $comment_ids_data ) && is_array( $comment_ids_data ) ){
				foreach( $comment_ids_data as $comment_id ){
					$validated_comment_ids[] = absint( trim( $comment_id ) );
				}
			}

			if( empty( $validated_comment_ids ) ){
				$return_args['msg'] = __( 'We could not validate the given comment IDs.', 'wp-webhooks' );
				return $return_args;
			}

			if( ! empty( $validated_comment_ids ) ){
				foreach( $validated_comment_ids as $comment_id ){
					do_action( 'wpfc_clear_post_cache_by_id', $comment_id );
				}
			}

			$return_args['success'] = true;
			$return_args['msg']     = __( 'The cached comment IDs have been cleared.', 'wp-webhooks' );

			return $return_args;

		}

	}

endif; // End if class_exists check.
