<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_autonami_Actions_ami_remove_user_tags' ) ) :
	/**
	 * Load the ami_remove_user_tags action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_autonami_Actions_ami_remove_user_tags {

		public function get_details() {
			$parameter         = array(
				'user' => array(
					'required'          => true,
					'label'             => __( 'User', 'wp-webhooks' ),
					'short_description' => __( '(Mixed) The user ID or user email of the user you want to remove the tags from.', 'wp-webhooks' )
				),
				'tags'    => array(
					'required'          => true,
					'label'             => __( 'Tag IDs', 'wp-webhooks' ),
					'short_description' => __( '(Array) The tag IDs you want to remove from user. This argument expects a comma-separated string of the tag IDs.', 'wp-webhooks' )
				)
			);
			//This is a more detailed view of how the data you sent will be returned.
			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further details about the sent data.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'Tags have been succesfully removed.',
				'data'    => [
					'user_id'      => 1,
					'removed_tags' => [ 63, 24 ]
				]
			);

			return array(
				'action'            => 'ami_remove_user_tags',
				'name'              => __( 'Remove user tags', 'wp-webhooks' ),
				'sentence'          => __( 'remove one or multiple tags tags from a user', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'This webhook action allows you to remove one or multiple tags tags from a user wihtin FunnelKit Automations.', 'wp-webhooks' ),
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
					'removed_tags' => array()
				)
			);

			$user = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$tags    = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tags' );
			
			//Force user id
			$user_id = WPWHPRO()->helpers->serve_user_id( $user );

			if ( empty( $user_id ) ) {
				$return_args['msg'] = __( "Please provide valid user data.", 'action-ami_remove_user_tags-failure' );

				return $return_args;
			}

			$user = get_user_by( 'id', $user_id );

			if ( empty( $user ) ) {
				$return_args['msg'] = __( "We could not retrieve the user for your given data.", 'action-ami_remove_user_tags-failure' );

				return $return_args;
			}

			$email = $user->user_email;

			if ( empty( $tags ) ) {
				$return_args['msg'] = __( "No tags provided", 'action-ami_remove_user_tags-failure' );

				return $return_args;
			}
			if ( WPWHPRO()->helpers->is_json( $tags ) ) {
				$tags = json_decode( $tags );
			}

			$contact = new BWFCRM_Contact( $email );

			if ( ! $contact->is_contact_exists() ) {
				$return_args['msg'] = sprintf( __( 'No user found with given ID #%d', 'wp-webhooks' ), $user_id );

				return $return_args;
			}

			if ( ! is_array( $tags ) ) {
				if ( is_string( $tags ) && false !== strpos( $tags, ',' ) ) {
					$tags = explode( ',', $tags );
				} else if ( empty( absint( $tags ) ) ) {
					$return_args['msg'] = __( 'No tags found', 'wp-webhooks' );

					return $return_args;
				} else {
					$tags = array( absint( $tags ) );
				}
			}

			if( ! is_array( $tags ) ){
				$tags = array();
			}

			$tags = array_map( 'absint', $tags );
			$tags = array_filter( $tags );

			if ( empty( $tags ) ) {
				$return_args['msg'] = __( 'Invalid tags provided.', 'wp-webhooks' );

				return $return_args;
			}

			$removed_tags = $contact->remove_tags( $tags );
			$contact->save();
			$tags_not_removed = array_diff( $tags, $removed_tags );

			if ( count( $tags_not_removed ) === count( $tags ) ) {
				$return_args['msg'] = __( 'Unable to remove any tag.', 'wp-webhooks' );

				return $return_args['msg'];
			}

			$return_args['msg'] = __( 'Tags have been succesfully removed.', 'wp-webhooks' );

			if ( ! empty( $tags_not_removed ) ) {
				$removed_tags_text  = implode( ', ', $removed_tags );
				$return_args['msg'] = sprintf( __( 'Some tags have been removed: %s', 'wp-webhooks' ), $removed_tags_text );
			}

			$return_args['success']              = true;
			$return_args['data']['user_id']      = $user_id;
			$return_args['data']['removed_tags'] = $removed_tags;

			return $return_args;
		}
	}
endif;
