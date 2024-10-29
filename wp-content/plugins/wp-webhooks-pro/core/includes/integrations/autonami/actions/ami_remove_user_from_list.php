<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_autonami_Actions_ami_remove_user_from_list' ) ) :
	/**
	 * Load the ami_remove_user_from_list action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_autonami_Actions_ami_remove_user_from_list {

		public function get_details() {
				$parameter = array(
				'user' => array(
					'required'          => true,
					'label'             => __( 'User', 'wp-webhooks' ),
					'short_description' => __( '(Mixed) The user ID or user email of the user you want to remove from the lists.', 'wp-webhooks' )
				),
				'lists'   => array(
					'required'          => true,
					'label'             => __( 'List IDs', 'wp-webhooks' ),
					'short_description' => __( '(Array) The list IDs you want to remove from user. This argument expects a comma-separated string of the form IDs. ', 'wp-webhooks' )
				)
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further details about the sent data.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'All lists have been successfully removed.',
				'data'    => [
					'user_id'       => 26,
					'removed_lists' => [ 63, 24 ]
				]
			);

			return array(
				'action'            => 'ami_remove_user_from_list',
				'name'              => __( 'Remove user from lists', 'wp-webhooks' ),
				'sentence'          => __( 'remove a user from one or multiple lists', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'This webhook action allows you to remove a user from one or multiple lists in FunnelKit Automations.', 'wp-webhooks' ),
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
					'removed_lists' => array()
				)
			);

			$user = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$lists   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'lists' );

			//Force user id
			$user_id = WPWHPRO()->helpers->serve_user_id( $user );

			if ( empty( $user_id ) ) {
				$return_args['msg'] = __( "Please provide valid user data.", 'action-ami_remove_user_from_list-failure' );

				return $return_args;
			}

			$user = get_user_by( 'id', $user_id );

			if ( empty( $user ) ) {
				$return_args['msg'] = __( "We could not retrieve the user for your given data.", 'action-ami_remove_user_from_list-failure' );

				return $return_args;
			}

			$email = $user->user_email;

			if ( empty( $lists ) ) {
				$return_args['msg'] = __( "No lists provided.", 'action-ami_remove_user_from_list-failure' );

				return $return_args;
			}

			if ( WPWHPRO()->helpers->is_json( $lists ) ) {
				$lists = json_decode( $lists );
			} else {
				$lists_array = explode( ',', $lists );
				if( is_array( $lists_array ) ){
					$lists = array();

					foreach( $lists_array as $list ){
						$lists[] = trim( $list );
					}
				}
			}

			if( ! empty( $lists ) ){
				$lists = array();
			}

			$contact = new BWFCRM_Contact( $email );

			if ( ! $contact->is_contact_exists() ) {
				$return_args['msg'] = sprintf( __( 'No user found with given ID #%d', 'wp-webhooks' ), $user_id );

				return $return_args;
			}

			$lists = array_map( 'absint', $lists );
			$lists = array_filter( $lists );

			if ( empty( $lists ) ) {
				$return_args['msg']   = __( 'No lists found.', 'wp-webhooks' );
				return $return_args;
			}

			$removed_lists = $contact->remove_lists( $lists );
			$contact->save();
			$lists_not_removed = array_diff( $lists, $removed_lists );

			if ( count( $lists_not_removed ) === count( $lists ) ) {
				$return_args['msg'] = __( 'Unable to remove the lists.', 'wp-webhooks' );
				return $return_args;
			}

			$return_args['msg'] = __( 'All lists have been successfully removed.', 'wp-webhooks' );

			if ( ! empty( $lists_not_removed ) ) {
				$removed_lists_text = implode( ', ', $removed_lists );
				$return_args['msg'] = sprintf( __( 'Some lists have been removed: %s', 'wp-webhooks' ), $removed_lists_text );
			}

			$return_args['success']               = true;
			$return_args['data']['user_id']       = $user_id;
			$return_args['data']['removed_lists'] = $removed_lists;

			return $return_args;

		}
	}
endif;
