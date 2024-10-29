<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_autonami_Actions_ami_remove_contact_from_list' ) ) :
	/**
	 * Load the ami_remove_contact_from_list action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_autonami_Actions_ami_remove_contact_from_list {

		public function get_details() {
			$parameter         = array(
				'contact_id' => array(
					'required'          => true,
					'label'             => __( 'Contact ID', 'wp-webhooks' ),
					'short_description' => __( '(Integer) The contact ID you want to remove from the lists.', 'wp-webhooks' )
				),
				'lists'      => array(
					'required'          => true,
					'label'             => __( 'List IDs', 'wp-webhooks' ),
					'short_description' => __( '(String) The list IDs you want to remove from the contact. This argument expects a comma-separated string of the list IDs.', 'wp-webhooks' )
				)
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further details about the sent data.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'Lists removed',
				'data'    => [
					'contact_id'    => 26,
					'removed_lists' => [ 63, 24 ]
				]
			);

			return array(
				'action'            => 'ami_remove_contact_from_list',
				'name'              => __( 'Remove contact from lists', 'wp-webhooks' ),
				'sentence'          => __( 'remove a contact from one or multiple lists', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'This webhook action allows you to remove a contact from one or multiple lists within FunnelKit Automations.', 'wp-webhooks' ),
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
					'contact_id'    => '',
					'removed_lists' => array(),
				)
			);

			$contact_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'contact_id' ) );
			$lists      = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'lists' );

			if ( empty( $lists ) ) {
				$return_args['msg'] = __( "No Lists provided.", 'action-ami_remove_contact_from_list-failure' );

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

			if ( empty( $contact_id ) ) {
				$return_args['msg'] = __( "Contact ID is mandatory.", 'action-ami_remove_contact_from_list-failure' );

				return $return_args;
			}

			$contact = new BWFCRM_Contact( $contact_id );

			if ( ! $contact->is_contact_exists() ) {
				$return_args['msg'] = sprintf( __( 'No contact found with given ID #%d', 'wp-webhooks' ), $contact_id );

				return $return_args;
			}

			$lists = array_map( 'absint', $lists );
			$lists = array_filter( $lists );

			if ( empty( $lists ) ) {
				$return_args['msg'] = __( 'No lists found.', 'wp-webhooks' );

				return $return_args;
			}

			$removed_lists = $contact->remove_lists( $lists );
			$contact->save();
			$lists_not_removed = array_diff( $lists, $removed_lists );

			if ( count( $lists_not_removed ) === count( $lists ) ) {
				$return_args['msg'] = __( 'Unable to remove the lists.', 'wp-webhooks' );
			}

			$return_args['msg'] = __( 'Lists removed.', 'wp-webhooks' );

			if ( ! empty( $lists_not_removed ) ) {
				$removed_lists_text = implode( ', ', $removed_lists );
				$return_args['msg'] = sprintf( __( 'Some lists have been removed: %s', 'wp-webhooks' ), $removed_lists_text );
			}

			$return_args['success']               = true;
			$return_args['data']['contact_id']    = $contact_id;
			$return_args['data']['removed_lists'] = $removed_lists;

			return $return_args;

		}
	}
endif;
