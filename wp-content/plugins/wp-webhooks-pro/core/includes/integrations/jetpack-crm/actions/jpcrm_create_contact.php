<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_jetpack_crm_Actions_jpcrm_create_contact' ) ) :
	/**
	 * Load the jpcrm_create_contact action
	 *
	 * @since 6.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_jetpack_crm_Actions_jpcrm_create_contact {

		public function get_details() {

			$parameter = array(
				'status'           => array(
					'label'             => __( 'Status', 'wp-webhooks' ),
					'type'              => 'select',
					'default_value'     => 'Lead',
					'choices'           => array(
						'Lead'                         => array( 'label' => __( 'Lead', 'wp-webhooks' ) ),
						'Customer'                     => array( 'label' => __( 'Customer', 'wp-webhooks' ) ),
						'Refused'                      => array( 'label' => __( 'Refused', 'wp-webhooks' ) ),
						'Blacklisted'                  => array( 'label' => __( 'Blacklisted', 'wp-webhooks' ) ),
						'Cancelled by Customer'        => array( 'label' => __( 'Cancelled by Customer', 'wp-webhooks' ) ),
						'Cancelled by Us (Pre-Quote)'  => array( 'label' => __( 'Cancelled by Us (Pre-Quote)', 'wp-webhooks' ) ),
						'Cancelled by Us (Post-Quote)' => array( 'label' => __( 'Cancelled by Us (Post-Quote)', 'wp-webhooks' ) ),
					),
					'short_description' => __( '(String) The contact status.', 'wp-webhooks' ),
				),
				'prefix'           => array(
					'label'             => __( 'Prefix', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(
						'Mr'       => array( 'label' => __( 'Mr', 'wp-webhooks' ) ),
						'Mrs'      => array( 'label' => __( 'Mrs', 'wp-webhooks' ) ),
						'Ms'       => array( 'label' => __( 'Ms', 'wp-webhooks' ) ),
						'Miss'     => array( 'label' => __( 'Miss', 'wp-webhooks' ) ),
						'Mx'       => array( 'label' => __( 'Mx', 'wp-webhooks' ) ),
						'Dr'       => array( 'label' => __( 'Dr', 'wp-webhooks' ) ),
						'Prof'     => array( 'label' => __( 'Prof', 'wp-webhooks' ) ),
						'Mr & Mrs' => array( 'label' => __( 'Mr & Mrs', 'wp-webhooks' ) ),
					),
					'short_description' => __( '(String) The contact prefix.', 'wp-webhooks' ),
				),
				'fname'            => array(
					'label'             => __( 'First Name', 'wp-webhooks' ),
					'short_description' => __( '(String) The first name.', 'wp-webhooks' ),
				),
				'lname'            => array(
					'label'             => __( 'Last name', 'wp-webhooks' ),
					'short_description' => __( '(String) The last name.', 'wp-webhooks' ),
				),
				'email'            => array(
					'label'             => __( 'Email', 'wp-webhooks' ),
					'short_description' => __( '(String) The contact email.', 'wp-webhooks' ),
				),
				'hometel'          => array(
					'label'             => __( 'Home telephone', 'wp-webhooks' ),
					'short_description' => __( '(String) The home telephone number.', 'wp-webhooks' ),
				),
				'worktel'          => array(
					'label'             => __( 'Work telephone', 'wp-webhooks' ),
					'short_description' => __( '(String) The work telephone number.', 'wp-webhooks' ),
				),
				'mobtel'           => array(
					'label'             => __( 'Mobile telephone', 'wp-webhooks' ),
					'short_description' => __( '(String) The mobile telephone number.', 'wp-webhooks' ),
				),
				'addr1'            => array(
					'label'             => __( 'Address line 1', 'wp-webhooks' ),
					'short_description' => __( '(String) The address line 1.', 'wp-webhooks' ),
				),
				'addr2'            => array(
					'label'             => __( 'Address line 2', 'wp-webhooks' ),
					'short_description' => __( '(String) The address line 2.', 'wp-webhooks' ),
				),
				'city'             => array(
					'label'             => __( 'City', 'wp-webhooks' ),
					'short_description' => __( '(String) The city.', 'wp-webhooks' ),
				),
				'county'           => array(
					'label'             => __( 'County', 'wp-webhooks' ),
					'short_description' => __( '(String) The county.', 'wp-webhooks' ),
				),
				'postcode'         => array(
					'label'             => __( 'Post code', 'wp-webhooks' ),
					'short_description' => __( '(String) The post code.', 'wp-webhooks' ),
				),
				'country'          => array(
					'label'             => __( 'Country', 'wp-webhooks' ),
					'short_description' => __( '(String) The country of the contact using the country code.', 'wp-webhooks' ),
				),
				'companies'        => array(
					'label'             => __( 'Companies', 'wp-webhooks' ),
					'short_description' => __( '(String) The companies where the contact works. This can be a company IDs. You can separate companies with commas.', 'wp-webhooks' ),
				),
				'tags'             => array(
					'label'             => __( 'Tags', 'wp-webhooks' ),
					'short_description' => __( '(String) The tag IDs of the tags you want to assign to the contact. You can separate tags with commas.', 'wp-webhooks' ),
				),
				'social_tw'               => array(
					'label'             => __( 'Social Twitter handle', 'wp-webhooks' ),
					'short_description' => __( '(String) Social media profiles handle for Twitter.', 'wp-webhooks' ),
				),
				'social_li'               => array(
					'label'             => __( 'Social LinkedIn handle', 'wp-webhooks' ),
					'short_description' => __( '(String) Social media profiles handle for LinkedIn.', 'wp-webhooks' ),
				),
				'social_fb'               => array(
					'label'             => __( 'Social Facebook handle', 'wp-webhooks' ),
					'short_description' => __( '(String) Social media profiles handle for Facebook.', 'wp-webhooks' ),
				),
				'update_if_exists'    => array(
					'label'             => __( 'Update contact if exists', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(
						'yes' => array(
							'label' => __( 'Yes', 'wp-webhooks' ),
						),
						'no' => array(
							'label' => __( 'No', 'wp-webhooks' ),
						),
					),
					'multiple'          => false,
					'default_value'     => 'no',
					'short_description' => __( '(String) If a user already exists with the given email address, you can decide if you want to update the user, or cancel the webhook.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further information about the shortcode.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The contact has been created.',
				'data' => 
				array (
				  'id' => 13,
				  'owner' => '-1',
				  'status' => 'Lead',
				  'email' => 'demouser@demodomain.test',
				  'prefix' => 'Mr',
				  'fname' => 'Jon',
				  'lname' => 'Doe',
				  'addr1' => 'Address Line 1',
				  'addr2' => 'Address line 2',
				  'city' => 'Demo City',
				  'county' => 'Demo County',
				  'country' => 'DE',
				  'postcode' => '12345',
				  'hometel' => '1234567',
				  'worktel' => '12345678',
				  'mobtel' => '12345679',
				  'wpid' => '-1',
				  'avatar' => '',
				  'tw' => 'https://social.test/twitter',
				  'li' => 'https://social.test/linkedin',
				  'fb' => 'https://social.test/facebook',
				  'created' => '2022-11-22 08:51:19',
				  'lastcontacted' => -1,
				  'createduts' => '1669107079',
				  'created_date' => '11/22/2022',
				  'lastupdated' => '1669107079',
				  'lastupdated_date' => '11/22/2022',
				  'lastcontacteduts' => '-1',
				  'lastcontacted_date' => false,
				  'fullname' => 'Mx Jonny Doey',
				  'name' => 'Mx Jonny Doey',
				),
			);

			return array(
				'action'            => 'jpcrm_create_contact', // required
				'name'              => __( 'Create contact', 'wp-webhooks' ),
				'sentence'          => __( 'create a contact', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Create a contact within Jetpack CRM.', 'wp-webhooks' ),
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

			$new_customer                             = array();
			$new_customer['data']['prefix']           = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'prefix' );
			$new_customer['data']['fname']            = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'fname' );
			$new_customer['data']['lname']            = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'lname' );
			$new_customer['data']['email']            = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'email' );
			$new_customer['data']['hometel']          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'hometel' );
			$new_customer['data']['worktel']          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'worktel' );
			$new_customer['data']['mobtel']           = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'mobtel' );
			$new_customer['data']['addr1']            = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'addr1' );
			$new_customer['data']['addr2']            = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'addr2' );
			$new_customer['data']['city']             = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'city' );
			$new_customer['data']['county']           = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'county' );
			$new_customer['data']['postcode']         = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'postcode' );
			$new_customer['data']['country']          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'country' );
			$new_customer['data']['secaddr_addr1']    = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'secaddr_addr1' );
			$new_customer['data']['secaddr_addr2']    = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'secaddr_addr2' );
			$new_customer['data']['secaddr_city']     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'secaddr_city' );
			$new_customer['data']['secaddr_county']   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'secaddr_county' );
			$new_customer['data']['secaddr_postcode'] = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'secaddr_postcode' );
			$new_customer['data']['secaddr_country']  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'secaddr_country' );
			$new_customer['data']['companies']        = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'companies' );
			$new_customer['data']['tags']             = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tags' );
			$new_customer['data']['status']           = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'status' );
			$new_customer['data']['tw']               = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'social_tw' );
			$new_customer['data']['li']               = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'social_li' );
			$new_customer['data']['fb']               = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'social_fb' );
			
			$update_if_exists                         = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'update_if_exists' ) === 'yes' ) ? true : false;

			if ( isset( $new_customer['data']['tags'] ) && ! empty( $new_customer['data']['tags'] ) ) {

				if ( WPWHPRO()->helpers->is_json( $new_customer['data']['tags'] ) ) {
					$new_customer['data']['tags'] = json_decode( $new_customer['data']['tags'], true );
				} else {
					$new_customer['data']['tags'] = preg_split( '/(\s*,*\s*)*,+(\s*,*\s*)*/', $new_customer['data']['tags'] );
				}
			} else {
				unset( $new_customer['data']['tags'] );
			}

			$exists = 0;

			if ( isset( $new_customer['data']['email'] ) ) {
				$exists = zeroBS_getCustomerIDWithEmail( $new_customer['data']['email'] );
			}

			if( $exists && ! $update_if_exists ){
				$return_args['msg'] = __( 'The user already exists with the given email.', 'wp-webhooks' );
				$return_args['data']['id'] = intval( $exists );
				return $return_args;
			}

			$new_customer['id'] = intval( $exists );

			global $zbs;
			$newCust             = $zbs->DAL->contacts->addUpdateContact( $new_customer );
			$return_args['data'] = $zbs->DAL->contacts->getContact( $newCust );

			if ( ! $exists ) {
				$return_args['success']    = true;
				$return_args['data']['id'] = intval( $newCust );
				$return_args['msg']        = __( 'The contact has been created.', 'wp-webhooks' );
			} elseif ( $newCust > 0 ) {
				$return_args['success']    = true;
				$return_args['data']['id'] = intval( $newCust );
				$return_args['msg']        = __( 'The contact has been updated.', 'wp-webhooks' );
			} else {
				$return_args['msg'] = __( 'An error occurred while creating the contact.', 'wp-webhooks' );
			}

			return $return_args;
		}

	}
endif;
