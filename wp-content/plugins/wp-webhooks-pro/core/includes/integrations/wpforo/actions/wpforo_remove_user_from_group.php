<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wpforo_Actions_wpforo_remove_user_from_group' ) ) :
		/**
		 * Load the wpforo_remove_user_from_group action
		 *
		 * @since 6.1.1
		 * @author Ironikus <info@ironikus.com>
		 */
	class WP_Webhooks_Integrations_wpforo_Actions_wpforo_remove_user_from_group {


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
						'(String) The group ID you want to remove a user from.',
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
				'msg'     => 'The user has been removed from the group succesffully.',
			);

			return array(
				'action'            => 'wpforo_remove_user_from_group', // required
				'name'              => __( 'Remove user from group', 'wp-webhooks' ),
				'sentence'          => __( 'remove a user from a group', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Remove a user from a group within wpForo.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'wpforo',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
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

            $selected_group_data =  WPF()->db->get_row(
                WPF()->db->prepare(
                    "SELECT * FROM `" . WPF()->tables->profiles . "` WHERE `userid` = %d",
                    $user_id
                ),
                ARRAY_A
             );
            $user_group_id = ( is_array( $selected_group_data['groupid'] ) && isset( $selected_group_data['groupid'] ) ) ? intval( $selected_group_data['groupid'] ) : 0;

			if( !wpforo_setting( 'authorization', 'role_synch' ) ) {
                if( $group_id && $group_id === $user_group_id ) {
                    $default_group = absint( WPF()->usergroup->default_groupid );
                    $sql           = 'UPDATE `' . WPF()->tables->profiles . '` SET `groupid` = %d WHERE `userid` = %d';
                    if ( false !== WPF()->db->query( WPF()->db->prepare( $sql, $default_group, $user_id ) ) ) {
                        WPF()->member->reset( $user_id );  
                        $return_args['success'] = true;
                        $return_args['msg'] = __( 'The user has been removed from the group succesffully.', 'wp-webhooks' );
                    } else {
                        $return_args['msg'] = __( 'An error has been occurred while updating a DB record.', 'wp-webhooks' );
                    }
                } else {
                    $return_args['msg'] = __( 'The specified user is not a member of the selected group.', 'wp-webhooks' );
                }

			} else {
                $return_args['msg'] = __( 'The role syncing is on, the user role can\'t be setted.', 'wp-webhooks' );
			}

			return $return_args;

		}
	}
endif;
