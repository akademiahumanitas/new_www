<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_klicktipp_Actions_klickt_add_subscriber' ) ) :

	/**
	 * Load the klickt_add_subscriber action
	 *
	 * @since 5.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_klicktipp_Actions_klickt_add_subscriber {

	public function get_details(){

		$parameter = array(
			'auth_template'		=> array( 
				'required' => false, 
				'type' => 'select', 
				'multiple' => false, 
				'label' => __( 'Authentication template', 'wp-webhooks' ), 
				'query'			=> array(
					'filter'	=> 'authentications',
					'args'		=> array(
						'auth_methods' => array( 'klickt_auth' )
					)
				),
				'short_description' => __( 'Use globally defined KlickTipp credentials to authenticate this action.', 'wp-webhooks' ),
				'description' => __( 'This argument accepts the ID of a KlickTipp authentication template of your choice. You can create new templates within the "Authentication" tab.', 'wp-webhooks' ),
			),
			'username'		=> array( 
				'required' => false, 
				'label' => __( 'Username', 'wp-webhooks' ), 
				'short_description' => __( 'The username of your account to authenticate to KlickTipp. This will be prioritized over the authentication template.', 'wp-webhooks' ),
			),
			'password'		=> array( 
				'required' => false, 
				'label' => __( 'Password', 'wp-webhooks' ), 
				'short_description' => __( 'The password of your account to authenticate to KlickTipp. This will be prioritized over the authentication template.', 'wp-webhooks' ),
			),
			'email_address'		=> array( 
				'required' => true, 
				'label' => __( 'Email Address', 'wp-webhooks' ), 
				'short_description' => __( 'The email address of the subscriber you want to add within KlickTipp.', 'wp-webhooks' ),
			),
			'double_optin_process_id' => array(
				'label' => __( 'Double Opt-In Process ID', 'wp-webhooks' ), 
				'short_description' => __( 'In the Automation menu, select the Double-Opt-in Processes option. You can find the ID at the beginning of the table.', 'wp-webhooks' ),
			),
			'tag_id' => array( 
				'label' => __( 'Tag ID', 'wp-webhooks' ), 
				'short_description' => __( 'In the "Automation" menu, select the "Tags" option. You can find the ID at the beginning of the table.', 'wp-webhooks' ),
			),
			'fields' => array(
				'type' => 'repeater',
				'label' => __( 'Additional fields', 'wp-webhooks' ), 
				'short_description' => __( 'Additional data of the recipient, e.g., affiliate ID, address, or customer number.', 'wp-webhooks' ),
			),
			'smsnumber' => array(
				'label' => __( 'SMS Number', 'wp-webhooks' ), 
				'short_description' => __( 'The mobile phone number of the subscriber.', 'wp-webhooks' ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			'data'		=> array( 'short_description' => __( '(Array) Further data about the request.', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The subscriber was successfully added.',
			'data' => 
			array (
			  'subscriber' => 
			  array (
				'id' => '101880000',
				'listid' => '259000',
				'optin' => '20.05.2022 15:01:32',
				'optin_ip' => '0.0.0.0 - By API Request',
				'email' => 'jon@doe.test',
				'status' => 'Opt-In Pending',
				'bounce' => 'Not Bounced',
				'date' => '',
				'ip' => '0.0.0.0 - By API Request',
				'unsubscription' => '',
				'unsubscription_ip' => '0.0.0.0',
				'referrer' => '',
				'sms_phone' => NULL,
				'sms_status' => NULL,
				'sms_bounce' => NULL,
				'sms_date' => '',
				'sms_unsubscription' => '',
				'sms_referrer' => NULL,
				'fieldFirstName' => '',
				'fieldLastName' => '',
				'fieldCompanyName' => '',
				'fieldStreet1' => '',
				'fieldStreet2' => '',
				'fieldCity' => '',
				'fieldState' => '',
				'fieldZip' => '',
				'fieldCountry' => '',
				'fieldPrivatePhone' => '',
				'fieldMobilePhone' => '',
				'fieldPhone' => '',
				'fieldFax' => '',
				'fieldWebsite' => '',
				'fieldBirthday' => '',
				'fieldLeadValue' => '',
				'tags' => 
				array (
				  0 => '8713617',
				),
				'manual_tags' => 
				array (
				  8713617 => '1653051692',
				),
			  ),
			),
		);

		return array(
			'action'			=> 'klickt_add_subscriber', //required
			'name'			   => __( 'Add subscriber', 'wp-webhooks' ),
			'sentence'			   => __( 'add a subscriber', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Add a subscriber within "KlickTipp".', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'klicktipp',
			'premium'	   	=> true
		);


		}

		public function execute( $return_data, $response_body ){
			
			$return_args = array(
				'success' => false,
				'msg' => '',
			);

			$auth_template = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'auth_template' );
			$username = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'username' );
			$password = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'password' );
			$email_address = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'email_address' );
			$double_optin_process_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'double_optin_process_id' ) );
			$tag_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tag_id' ) );
			$fields = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'fields' );
			$smsnumber = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'smsnumber' );

			if( ! empty( $auth_template ) ){
				$klickt_auth = WPWHPRO()->integrations->get_auth( 'klicktipp', 'klickt_auth' );
				$credentials = $klickt_auth->get_credentials( $auth_template );

				if( 
					empty( $username ) 
					&& isset( $credentials['wpwhpro_klickt_username'] )
					&& ! empty( $credentials['wpwhpro_klickt_username'] )
				){
					$username = $credentials['wpwhpro_klickt_username'];
				}

				if( 
					empty( $password ) 
					&& isset( $credentials['wpwhpro_klickt_password'] )
					&& ! empty( $credentials['wpwhpro_klickt_password'] )
				){
					$password = $credentials['wpwhpro_klickt_password'];
				}
			}

			if( empty( $username ) || empty( $password ) ){
                $return_args['msg'] = __( "The provided credentials are invalid.", 'wp-webhooks' );
				return $return_args;
            }

			if( empty( $email_address ) || ! is_email( $email_address ) ){
                $return_args['msg'] = __( "Please define a valid email address within the email_address argument.", 'wp-webhooks' );
				return $return_args;
            }

			$double_optin_process_id_validated = '';
			if( ! empty( $double_optin_process_id ) ){
				$double_optin_process_id_validated = $double_optin_process_id;
			}

			$tag_id_validated = '';
			if( ! empty( $tag_id ) ){
				$tag_id_validated = $tag_id;
			}

			$fields_validated = '';
			if( ! empty( $fields ) && WPWHPRO()->helpers->is_json( $fields ) ){
				$fields_validated = json_decode( $fields, true );
			}

			$smsnumber_validated = '';
			if( ! empty( $smsnumber ) ){
				$smsnumber_validated = $smsnumber;
			}

			$klickt_helpers = WPWHPRO()->integrations->get_helper( 'klicktipp', 'klickt_helpers' );

			$connector = $klickt_helpers->get_klicktipp();

			if( ! empty( $connector ) ){

				$connector->login( $username, $password );

				$subscriber = $connector->subscribe( $email_address, $double_optin_process_id_validated, $tag_id_validated, $fields_validated, $smsnumber_validated );

				if( ! empty( $subscriber ) ){
					$return_args['success'] = true;
					$return_args['msg'] = __( "The subscriber was successfully added.", 'wp-webhooks' );
					$return_args['data']['subscriber'] = $subscriber;
				} else {
					$return_args['msg'] = __( "An error occured while adding the subscriber.", 'wp-webhooks' );
					$return_args['data']['error'] = $connector->get_last_error();
				}
				
				$connector->logout();

			} else {
				$return_args['msg'] = __( "An error occured while loading the KlickTipp helper.", 'wp-webhooks' );
			}
			
			return $return_args;
	
		}

	}

endif; // End if class_exists check.