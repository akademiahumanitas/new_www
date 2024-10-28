<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_jetpack_crm_Actions_jpcrm_delete_contact' ) ) :
	/**
	 * Load the jpcrm_delete_contact action
	 *
	 * @since 6.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_jetpack_crm_Actions_jpcrm_delete_contact {

		public function get_details() {

			$parameter = array(
				'contact' => array(
					'label'             => __( 'Contact', 'wp-webhooks' ),
					'required'          => true,
					'short_description' => __( '(String) The contact id or email.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further information about the shortcode.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success'    => true,
				'msg'        => 'The contact has been deleted successfully.',
				'contact_id' => 31,
			);

			return array(
				'action'            => 'jpcrm_delete_contact', // required
				'name'              => __( 'Delete contact', 'wp-webhooks' ),
				'sentence'          => __( 'Delete a contact', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Delete a contact within Jetpack CRM.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'jetpack-crm',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
			);

			$contact            = array();
			$contact['contact'] = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'contact' );
			$exists             = 0;

			global $zbs;
			if ( is_email( $contact['contact'] ) ) {
				$exists = zeroBS_getCustomerIDWithEmail( $contact['contact'] );
			} else {
				$exists = $zbs->DAL->contacts->getContact(
					intval( $contact['contact'] ),
					array(
						'ignoreOwner' => 1,
						'onlyID'      => 1,
					)
				);
			}

			if ( $exists <= 0 ) {
				$return_args['msg'] = __( 'The given contact doesn\'t exist.', 'wp-webhooks' );
				return $return_args;
			}

			$contact_id = $exists;
			$result     = zeroBS_deleteCustomer( $contact_id, false );

			if ( $result == true ) {
				$return_args['success']    = true;
				$return_args['contact_id'] = intval( $contact_id );
				$return_args['msg']        = __( 'The contact has been deleted successfully.', 'wp-webhooks' );
			} else {
				$return_args['msg'] = __( 'An error occurred while deleting the contact.', 'wp-webhooks' );
			}

			return $return_args;
		}

	}
endif;
