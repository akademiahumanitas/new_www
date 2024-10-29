<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wpforo_Actions_wpforo_set_user_reputation' ) ) :
	/**
	 * Load the wpforo_set_user_reputation action
	 *
	 * @since 6.1.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wpforo_Actions_wpforo_set_user_reputation{


		public function get_details() {
			$parameter = array(
				'user'  => array(
					'required'          => true,
					'label'             => __( 'User', 'wp-webhooks' ),
					'type'              => 'select',
					'query'             => array(
						'filter' => 'users',
						'args'   => array(),
					),
					'short_description' => __(
						'(String) The user ID or email. You can provide an existing email address or an ID of a user.',
						'wp-webhooks'
					),
				),
				'reputation' => array(
					'required' => true,
					'label'    => __( 'Reputation', 'wp-webhooks' ),
					'type'     => 'select',
					'query'    => array(
						'filter' => 'helpers',
						'args'   => array(
							'integration' => 'wpforo',
							'helper'      => 'wpforo_helpers',
							'function'    => 'get_query_levels',
						),
					),
					'short_description' => __(
						'(String) The reputation ID you want to assign to the user.',
						'wp-webhooks'
					),
				),

			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action status.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further information about the request.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The user reputation has been succesffully adjusted.',
				'data'    => array(
					'level' => 3,
					'points'  => 4,
				),
			);

			return array(
				'action'            => 'wpforo_set_user_reputation', // required
				'name'              => __( 'Set user reputation', 'wp-webhooks' ),
				'sentence'          => __( 'set the reputation for a user', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Set the reputation for a user within wpForo.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'wpforo',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
				'data'    => array(),
			);

			$user     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$user_id  = WPWHPRO()->helpers->serve_user_id( $user );
			$reputation_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'reputation' ) );

            $points        = WPF()->member->rating( $reputation_id, 'points' );
            $result = false;
            $args = array( 'custom_points' => $points );
            $result = WPF()->member->update_profile_fields( $user_id, $args, false );
            WPF()->member->reset( $user_id );

			if ( $result ) {
				$return_args['success'] = true;
				$return_args['msg']     = __( 'The user reputation has been succesffully adjusted.', 'wp-webhooks' );
                $return_args['data']  = array(
                    'level' => $reputation_id,
                    'points' => $points,
                );
			} else {
				$return_args['msg'] = __( 'An error occurred while adjusting the reputation.', 'wp-webhooks' );
			}

			return $return_args;

		}
	}
endif;
