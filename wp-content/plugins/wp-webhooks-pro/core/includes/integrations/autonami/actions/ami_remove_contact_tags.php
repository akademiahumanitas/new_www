<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_autonami_Actions_ami_remove_contact_tags' ) ) :
	/**
	 * Load the ami_remove_contact_tags action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_autonami_Actions_ami_remove_contact_tags {

		public function get_details() {
				$parameter = array(
				'contact_id' => array(
					'required'          => true,
					'label'             => __( 'Contact ID', 'wp-webhooks' ),
					'short_description' => __( '(Integer) The contact ID you want to remove tags from.', 'wp-webhooks' )
				),
				'tags'       => array(
					'required'          => true,
					'label'             => __( 'Tag IDs', 'wp-webhooks' ),
					'short_description' => __( '(String) The tag IDs you want to remove from the contact. This argument expects a comma-separated string of the tag IDs.', 'wp-webhooks' )
				)
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further details about the sent data.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'Tags successfully removed.',
				'data'    => [
					'contact_id'   => 26,
					'removed_tags' => [ 63, 24 ]
				]
			);

			return array(
				'action'            => 'ami_remove_contact_tags',
				'name'              => __( 'Remove contact tags', 'wp-webhooks' ),
				'sentence'          => __( 'remove a contact from one or multiple tags', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'This webhook action allows you to remove one or multiple tags from a contact within FunnelKit Automations.', 'wp-webhooks' ),
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
					'contact_id'   => '',
					'removed_tags' => array(),
				)
			);

			$contact_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'contact_id' ) );
			$tags       = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tags' );

			if ( empty( $tags ) ) {
				$return_args['msg'] = __( "No tags provided.", 'action-ami_remove_contact_tags-failure' );

				return $return_args;
			}

			if ( WPWHPRO()->helpers->is_json( $tags ) ) {
				$tags = json_decode( $tags );
			}

			if ( empty( $contact_id ) ) {
				$return_args['msg'] = __( "The contact ID is mandatory.", 'action-ami_remove_contact_tags-failure' );

				return $return_args;
			}

			$contact = new BWFCRM_Contact( $contact_id );

			if ( ! $contact->is_contact_exists() ) {
				$return_args['msg'] = sprintf( __( 'No contact found with the given ID #%d', 'wp-webhooks' ), $contact_id );

				return $return_args;
			}

			if ( ! is_array( $tags ) ) {
				if ( is_string( $tags ) && false !== strpos( $tags, ',' ) ) {
					$tags = explode( ',', $tags );
				} else if ( empty( absint( $tags ) ) ) {
					$return_args['msg'] = __( 'No tags found.', 'wp-webhooks' );

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

			$return_args['msg'] = __( 'Tags successfully removed.', 'wp-webhooks' );

			if ( ! empty( $tags_not_removed ) ) {
				$removed_tags_text  = implode( ', ', $removed_tags );
				$return_args['msg'] = sprintf( __( 'Some tags have been removed: %s', 'wp-webhooks' ), $removed_tags_text );
			}

			$return_args['success']              = true;
			$return_args['data']['contact_id']   = $contact_id;
			$return_args['data']['removed_tags'] = $removed_tags;

			return $return_args;

		}
	}
endif;
