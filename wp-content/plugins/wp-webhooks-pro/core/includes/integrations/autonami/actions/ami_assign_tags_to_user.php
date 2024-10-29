<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_autonami_Actions_ami_assign_tags_to_user' ) ) :
	/**
	 * Load the ami_assign_tags_to_user action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_autonami_Actions_ami_assign_tags_to_user {

		public function get_details() {

				$parameter = array(
				'user' => array(
					'required'          => true,
					'label'             => __( 'User', 'wp-webhooks' ),
					'short_description' => __( '(Mixed) The user id or user email of the user you want to assign tags to.', 'wp-webhooks' )
				),
				'tags'    => array(
					'required'          => true,
					'label'             => __( 'Tag slugs', 'wp-webhooks' ),
					'short_description' => __( '(Array) The tags slugs you want to add to. This argument expects a JSON formatted string with the tag slugs as a value.', 'wp-webhooks' )
				)
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(array) Further details about the sent data.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'Tag(s) assigned',
				'data'    => array(
					'user_id'      => '',
					'tags_added'   =>
						[
							[
								'ID'         => 72,
								'name'       => 'work',
								'type'       => '1',
								'created_at' => '2022-08-19 03:27:56',
								'updated_at' => null,
								'data'       => null,
							],
							[
								'ID'         => 71,
								'name'       => 'website',
								'type'       => '1',
								'created_at' => '2022-08-19 03:27:56',
								'updated_at' => null,
								'data'       => null,
							]
						],
					'tag_modified' => '2022-08-18 19:06:19',
				)
			);

			return array(
				'action'            => 'ami_assign_tags_to_user',
				'name'              => __( 'Assign tags to user', 'wp-webhooks' ),
				'sentence'          => __( 'assign one or multiple tags to a user', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'This webhook action allows you to assign one or multiple tags to a user in FunnelKit Automations.', 'wp-webhooks' ),
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
					'user_id'      => '',
					'tags_added'   => '',
					'tag_modified' => ''
				)
			);

			$user = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$tag_names = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tags' );

			//Force user id
			$user_id = WPWHPRO()->helpers->serve_user_id( $user );

			if ( empty( $user_id ) ) {
				$return_args['msg'] = __( "User ID is mandatory.", 'action-ami_assign_tags_to_user-failure' );

				return $return_args;
			}

			$user    = get_user_by( 'id', $user_id );
			if ( empty( $user ) ) {
				$return_args['msg'] = __( "We could not find a user for your given data.", 'action-ami_assign_tags_to_user-failure' );

				return $return_args;
			}

			$email   = $user->user_email;

			if ( WPWHPRO()->helpers->is_json( $tag_names ) ) {
				$tag_names = json_decode( $tag_names );
			}

			$tags = array();

			if ( isset( $tag_names ) ) {
				foreach ( $tag_names as $key => $values ) {
					foreach ( $values as $value ) {
						$tags[] = array( 'id' => "0", 'value' => $value );
					}
				}
			}

			$contact = new BWFCRM_Contact( $email );

			if ( ! $contact->is_contact_exists() ) {
				$return_args['msg'] = sprintf( __( 'No user found with given id #%d.', 'action-ami_assign_tags_to_user-failure', $user_id ) );

				return $return_args;
			}

			$tags = array_filter( array_values( $tags ) );

			if ( empty( $tags ) ) {
				$return_args['msg'] = __( 'Required tags missing.', 'wp-webhooks' );

				return $return_args;
			}

			$added_tags = $contact->add_tags( $tags );

			if ( is_wp_error( $added_tags ) ) {
				$return_args['msg'] = sprintf( __( '500 %s.', 'wp-webhooks' ), $added_tags );

				return $return_args;
			}

			if ( empty( $added_tags ) ) {
				$return_args['msg'] = __( 'Provided tags are applied already.', 'wp-webhooks' );

				return $return_args;
			}

			$tags_added         = array_map( function ( $tag ) {
				return $tag->get_array();
			}, $added_tags );
			$result             = [];
			$return_args['msg'] = __( 'Tag(s) assigned.', 'wp-webhooks' );
			if ( count( $tags ) !== count( $added_tags ) ) {
				$applied_tags_names = array_map( function ( $tag ) {
					return $tag->get_name();
				}, $added_tags );
				$applied_tags_names = implode( ', ', $applied_tags_names );
				$return_args['msg'] = sprintf( __( 'Some tags are applied already. Applied tags are: %s', 'wp-webhooks' ), $applied_tags_names );
			}

			$result['tags_added']   = is_array( $tags_added ) ? array_values( $tags_added ) : $tags_added;
			$result['tag_modified'] = $contact->contact->get_last_modified();

			$return_args['success']              = true;
			$return_args['data']['user_id']      = $user_id;
			$return_args['data']['tags_added']   = $result['tags_added'];
			$return_args['data']['tag_modified'] = $result['tag_modified'];

			return $return_args;

		}
	}
endif;