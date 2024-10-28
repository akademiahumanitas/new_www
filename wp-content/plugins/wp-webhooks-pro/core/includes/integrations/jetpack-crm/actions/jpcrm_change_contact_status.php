<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_jetpack_crm_Actions_jpcrm_change_contact_status' ) ) :
	/**
	 * Load the jpcrm_change_contact_status action
	 *
	 * @since 6.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_jetpack_crm_Actions_jpcrm_change_contact_status {
		public function get_details() {

			$parameter = array(
				'contact' => array(
					'label'             => __( 'Contact', 'wp-webhooks' ),
					'required'          => true,
					'short_description' => __( '(String) The contact id or email.', 'wp-webhooks' ),
				),
				'status'  => array(
					'label'             => __( 'Status', 'wp-webhooks' ),
					'type'              => 'select',
					'default_value'     => 'Lead',
					'choices'           => apply_filters( 'wpwhpro/integrations/jetpack_crm/jpcrm_change_contact_status/status', array(
						'Lead'                         => array( 'label' => __( 'Lead', 'wp-webhooks' ) ),
						'Customer'                     => array( 'label' => __( 'Customer', 'wp-webhooks' ) ),
						'Refused'                      => array( 'label' => __( 'Refused', 'wp-webhooks' ) ),
						'Blacklisted'                  => array( 'label' => __( 'Blacklisted', 'wp-webhooks' ) ),
						'Cancelled by Customer'        => array( 'label' => __( 'Cancelled by Customer', 'wp-webhooks' ) ),
						'Cancelled by Us (Pre-Quote)'  => array( 'label' => __( 'Cancelled by Us (Pre-Quote)', 'wp-webhooks' ) ),
						'Cancelled by Us (Post-Quote)' => array( 'label' => __( 'Cancelled by Us (Post-Quote)', 'wp-webhooks' ) ),
					) ),
					'short_description' => __( '(String) The new contact status.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further details about the action.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The contact\'s status has been changed successfully.',
				'data'    => array(
					'contact_id' => 31,
				),
			);

			return array(
				'action'            => 'jpcrm_change_contact_status', // required
				'name'              => __( 'Change contact status', 'wp-webhooks' ),
				'sentence'          => __( 'change the status of a contact', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Change the status of a contact within Jetpack CRM.', 'wp-webhooks' ),
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

			$contact                   = array();
			$contact['id']             = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'contact' );
			$contact['status']         = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'status' );

			global $zbs;
			if ( is_email( $contact['id'] ) ) {
				$contact['id'] = zeroBS_getCustomerIDWithEmail( $contact['id'] );
			} else {
				$contact['id'] = $zbs->DAL->contacts->getContact(
					intval( $contact['id'] ),
					array(
						'ignoreOwner' => 1,
						'onlyID'      => 1,
					)
				);
			}

			if ( $contact['id'] <= 0 ) {
				$return_args['msg'] = __( 'The given contact doesn\'t exist.', 'wp-webhooks' );
				return $return_args;
			}

			global $zbs;

			$result = $zbs->DAL->contacts->setContactStatus( $contact['id'], $contact['status'] );

			if ( $result > 0 ) {
				$return_args['success']            = true;
				$return_args['data']['contact_id'] = intval( $result );
				$return_args['msg']                = __( 'The contact\'s status has been changed successfully.', 'wp-webhooks' );
			} else {
				$return_args['msg'] = __( 'An error occurred while changing the status.', 'wp-webhooks' );
			}
			return $return_args;
		}

	}
endif;
