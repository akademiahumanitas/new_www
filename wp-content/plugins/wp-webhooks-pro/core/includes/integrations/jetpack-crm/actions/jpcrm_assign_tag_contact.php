<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_jetpack_crm_Actions_jpcrm_assign_tag_contact' ) ) :
	/**
	 * Load the jpcrm_assign_tag_contact action
	 *
	 * @since 6.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_jetpack_crm_Actions_jpcrm_assign_tag_contact {
		public function get_details() {

			$parameter = array(
				'contact' => array(
					'label'             => __( 'Contact', 'wp-webhooks' ),
					'required'          => true,
					'short_description' => __( '(String) The contact id or email.', 'wp-webhooks' ),
				),
				'tags'    => array(
					'label'             => __( 'Tags', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(),
					'multiple'          => true,
					'query'             => array(
						'filter' => 'helpers',
						'args'   => array(
							'integration' => 'jetpack-crm',
							'helper'      => 'jpcrm_helpers',
							'function'    => 'get_query_tags',
							'type'        => 'ZBS_TYPE_CONTACT',
						),
					),
					'short_description' => __( '(String) The tags to assign to the contact.', 'wp-webhooks' ),
				),
				'tag_mode'    => array(
					'label'             => __( 'Tag mode', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(
						'append' => array(
							'label' => __( 'Append', 'wp-webhooks' ),
						),
						'replace' => array(
							'label' => __( 'Replace', 'wp-webhooks' ),
						),
					),
					'multiple'          => false,
					'default_value'     => 'append',
					'short_description' => __( '(String) Choose whether you want to replace the other tags or if you want to append the new ones.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further details about the action.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The tags have been assigned to the contact.',
				'data'    =>
				array(
					'contact_id' => '23',
				),
			);

			return array(
				'action'            => 'jpcrm_assign_tag_contact', // required
				'name'              => __( 'Assign tag to contact', 'wp-webhooks' ),
				'sentence'          => __( 'assign one or multiple tags to a contact', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Assign one or multiple tags to a contact within Jetpack CRM.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'jetpack-crm',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
				'data'    => array(),
			);

			$contact            = array();
			$contact['contact'] = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'contact' );
			$contact['tags']    = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tags' );
			$contact['tag_mode'] = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tag_mode' );

			if ( isset( $contact['tags'] ) ) {
				if ( WPWHPRO()->helpers->is_json( $contact['tags'] ) ) {
					$contact['tags'] = json_decode( $contact['tags'], true );
				} else {
					$contact['tags'] = preg_split( '/(\s*,*\s*)*,+(\s*,*\s*)*/', $contact['tags'] );
				}
			}

			if( ! is_array( $contact['tags'] ) && is_numeric( $contact['tags'] ) ){
				$contact['tags'] = array( $contact['tags'] );
			}

			$contact_id = 0;

			global $zbs;
			if ( is_email( $contact['contact'] ) ) {
				$contact_id = zeroBS_getCustomerIDWithEmail( $contact['contact'] );
			} else {
				$contact_id = $zbs->DAL->contacts->getContact(
					intval( $contact['contact'] ),
					array(
						'ignoreOwner' => 1,
						'onlyID'      => 1,
					)
				);
			}

			if ( $contact_id <= 0 ) {
				$return_args['msg'] = __( 'The given contact doesn\'t exist.', 'wp-webhooks' );
				return $return_args;
			}

			if( empty( $contact['tag_mode'] ) ){
				$contact['tag_mode'] = 'append';
			}

			$result = $zbs->DAL->contacts->addUpdateContactTags(
				array(
					'id'        => $contact_id,
					'tag_input' => $contact['tags'],
					'mode'      => $contact['tag_mode'],
				)
			);

			if ( $result == true ) {
				$return_args['success']            = true;
				$return_args['data']['contact_id'] = $contact_id;
				$return_args['msg']                = __( 'The tags have been assigned to the contact.', 'wp-webhooks' );
			} else {
				$return_args['msg'] = __( 'An error occurred while adding the tags.', 'wp-webhooks' );
			}

			return $return_args;
		}

	}
endif;
