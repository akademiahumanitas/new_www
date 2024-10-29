<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wpforo_Actions_wpforo_add_user_to_group' ) ) :
		/**
		 * Load the wpforo_add_user_to_group action
		 *
		 * @since 6.1.1
		 * @author Ironikus <info@ironikus.com>
		 */
	class WP_Webhooks_Integrations_wpforo_Actions_wpforo_add_user_to_group {


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
				'group' => array(
					'required' => true,
					'label'    => __( 'Group', 'wp-webhooks' ),
					'type'     => 'select',
					'query'    => array(
						'filter' => 'helpers',
						'args'   => array(
							'integration' => 'wpforo',
							'helper'      => 'wpforo_helpers',
							'function'    => 'get_query_groups',
						),
					),
					'short_description' => __(
						'(String) The group ID you want to add a user to.',
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
				'msg'     => 'The user has been added to the group succesffully.',
				'data'    => array(
					'group_id' => 3,
					'user_id'  => 4,
				),
			);

			$description = array(
				'tipps'    => array(
					__( 'To successfully use this action, please make sure the usergroup sync is turned off.', 'wp-webhooks' )
				),
			);

			return array(
				'action'            => 'wpforo_add_user_to_group', // required
				'name'              => __( 'Add user to group', 'wp-webhooks' ),
				'sentence'          => __( 'add a user to a group', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Add a user to a group within wpForo.', 'wp-webhooks' ),
				'description'       => $description,
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
			$group_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'group' ) );

			if( empty( $user_id ) ){
				$return_args['msg'] = __( 'We could not locate a user for your given data.', 'wp-webhooks' );
				return $return_args;
			}

			if( empty( $group_id ) ){
				$return_args['msg'] = __( 'Please set the group argument.', 'wp-webhooks' );
				return $return_args;
			}

			$result = false;

			if ( wpforo_setting( 'authorization', 'role_synch' ) ) {
				$result = WPF()->member->set_groupid( $user_id, $group_id );
			} else {
				$status = WPF()->usergroup->set_users_groupid( array( $group_id => array( $user_id ) ) );
				$result = $status['success'];	
			}

			if ( $result ) {
				$return_args['success'] = true;
				$return_args['msg']     = __( 'The user has been added to the group successfully.', 'wp-webhooks' );
				$return_args['data']    = array(
					'group_id' => $group_id,
					'user_id'  => $user_id,
				);
			} else {
				$return_args['msg'] = __( 'An error has been occurred while adding a user to the group.', 'wp-webhooks' );
			}

			return $return_args;

		}
	}
endif;
