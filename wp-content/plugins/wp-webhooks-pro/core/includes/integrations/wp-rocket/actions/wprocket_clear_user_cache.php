<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_wp_rocket_Actions_wprocket_clear_user_cache' ) ) :

	/**
	 * Load the wprocket_clear_user_cache action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_rocket_Actions_wprocket_clear_user_cache {

		public function get_details() {


			$parameter = array(
				'users' => array( 
					'required'		=> true, 
					'label' => __( 'User IDs or Email', 'wp-webhooks' ),
					'short_description' => __( '(String) Clear specific user IDs or Emails. To add multiple ones, please comma-separate them. If none are given, all are flushed.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'    => array( 'short_description' => __( '(String) An informative message about the action.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The given users have been cleared.',
			);

			return array(
				'action'            => 'wprocket_clear_user_cache',
				'name'              => __( 'Clear user cache', 'wp-webhooks' ),
				'sentence'          => __( 'flush the cache for one or multiple users', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Clear the cache for one or multiple users within WP Rocket.', 'wp-webhooks' ),
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

			$users = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'users' );

			if( empty( $users ) ){
				$return_args['msg'] = __( 'Please set the users argument.', 'wp-webhooks' );
				return $return_args;
			}
			
			$validated_user_ids = array();
				
			if( WPWHPRO()->helpers->is_json( $users ) ){
				$users_data = json_decode( $users, true );
			} else {
				$users_data = explode( ',', $users );
			}

			if( ! empty( $users_data ) && is_array( $users_data ) ){
				foreach( $users_data as $user ){
					$validated_user_ids[] = WPWHPRO()->helpers->serve_user_id( $user );
				}
			}

			if( empty( $validated_user_ids ) ){
				$return_args['msg'] = __( 'We could not validate the given users.', 'wp-webhooks' );
				return $return_args;
			}

			if( ! empty( $validated_user_ids ) ){
				foreach( $validated_user_ids as $user_id ){
					rocket_clean_user( $user_id );
				}
			}

			$return_args['msg']     = __( 'The given users have been cleared.', 'wp-webhooks' );
			$return_args['success'] = true;

			return $return_args;

		}

	}

endif; // End if class_exists check.
