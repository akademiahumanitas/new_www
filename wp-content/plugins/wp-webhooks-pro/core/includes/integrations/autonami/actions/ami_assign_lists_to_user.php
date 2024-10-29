<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_autonami_Actions_ami_assign_lists_to_user' ) ) :
	/**
	 * Load the ami_assign_lists_to_user action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_autonami_Actions_ami_assign_lists_to_user {

		public function get_details() {
			$parameter         = array(
				'user'  => array(
					'required'          => true,
					'label'             => __( 'User', 'wp-webhooks' ),
					'short_description' => __( '(Mixed) The user ID or user email of the user you want to assign lists to.', 'wp-webhooks' )
				),
				'lists' => array(
					'required'          => true,
					'label'             => __( 'List slugs', 'wp-webhooks' ),
					'short_description' => __( '(Array) The list slugs you want to add to the user. You only have to choose existing lists. This argument expects a JSON formatted string with the list slugs as a value.', 'wp-webhooks' )
				)
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(array) Further details about the sent data.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'List(s) assigned',
				'data'    => [
					'user_id'       => 22,
					'added_lists'   => [
						[
							"ID"         => 68,
							"name"       => "youtube",
							"type"       => "2",
							"created_at" => "2022-08-18 18:55:23",
							"updated_at" => null,
							"data"       => null
						],
						[
							"ID"         => 67,
							"name"       => "blog",
							"type"       => "2",
							"created_at" => "2022-08-18 18:55:23",
							"updated_at" => null,
							"data"       => null
						],
					],
					'last_modified' => '2022-08-18 18:55:23',
				]
			);

			return array(
				'action'            => 'ami_assign_lists_to_user',
				'name'              => __( 'Assign lists to user', 'wp-webhooks' ),
				'sentence'          => __( 'assign one or multiple lists to a user', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'This webhook action allows you to assign one or multiple lists to a user in FunnelKit Automations.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'autonami',
				'premium'           => true,
			);
		}

		public function execute( $return_data, $response_body ) {
			$return_args = array(
				'success' => false,
				'msg'     => '',
				'data'    => array(
					'user_id'       => '',
					'added_lists'   => '',
					'last_modified' => ''
				)
			);

			$user = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );

			//Force user id
			$user_id = WPWHPRO()->helpers->serve_user_id( $user );

			if ( empty( $user_id ) ) {
				$return_args['msg'] = __( "The user ID is mandatory.", 'action-ami_assign_list_to_contact-failure' );

				return $return_args;
			}

			$user = get_user_by( 'id', $user_id );

			if ( empty( $user ) ) {
				$return_args['msg'] = __( "We could not locate a user for your given value.", 'action-ami_assign_list_to_contact-failure' );

				return $return_args;
			}

			$email      = $user->user_email;
			$list_names = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'lists' );
			$lists      = array();
			if ( WPWHPRO()->helpers->is_json( $list_names ) ) {
				$list_names = json_decode( $list_names );
				if ( isset( $list_names ) ) {
					foreach ( $list_names as $key => $values ) {
						foreach ( $values as $value ) {
							$lists[] = array( 'id' => $key, 'value' => $value );
						}
					}
				}
			}

			$contact = new BWFCRM_Contact( $email );

			if ( ! $contact->is_contact_exists() ) {
				$return_args['msg'] = sprintf( __( 'No user found with given id #%d', 'wp-webhooks' ), $user_id );

				return $return_args;
			}

			$lists = array_filter( array_values( $lists ) );

			if ( empty( $lists ) ) {
				$return_args['msg'] = __( 'Required Lists missing.', 'wp-webhooks' );

				return $return_args;
			}

			$added_lists = $contact->add_lists( $lists );

			if ( is_wp_error( $added_lists ) ) {
				$return_args['msg'] = sprintf( __( '500 %s.', 'wp-webhooks' ), $added_lists );

				return $return_args;
			}

			if ( empty( $added_lists ) ) {
				$return_args['msg'] = __( 'Provided lists are applied already.', 'wp-webhooks' );

				return $return_args;
			}

			$result      = [];
			$lists_added = array_map( function ( $list ) {
				return $list->get_array();
			}, $added_lists );

			$return_args['msg'] = __( 'List(s) assigned.', 'wp-webhooks' );

			if ( count( $lists ) !== count( $added_lists ) ) {
				$added_lists_names  = array_map( function ( $list ) {
					return $list->get_name();
				}, $added_lists );
				$added_lists_names  = implode( ', ', $added_lists_names );
				$return_args['msg'] = sprintf( __( 'Some Lists are applied already. Applied Lists are: %s.', 'wp-webhooks' ), $added_lists_names );
			}
			$result['list_added']    = is_array( $lists_added ) ? array_values( $lists_added ) : $lists_added;
			$result['last_modified'] = $contact->contact->get_last_modified();

			$return_args['success']               = true;
			$return_args['data']['user_id']       = $user_id;
			$return_args['data']['added_lists']   = $result['list_added'];
			$return_args['data']['last_modified'] = $result['last_modified'];

			return $return_args;

		}
	}
endif;