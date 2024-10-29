<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_klicktipp_Actions_klickt_find_subscriber' ) ) :

	/**
	 * Load the klickt_find_subscriber action
	 *
	 * @since 5.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_klicktipp_Actions_klickt_find_subscriber {

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
			'subscriber'		=> array( 
				'required' => true, 
				'label' => __( 'Subscriber', 'wp-webhooks' ), 
				'short_description' => __( 'The email or the id of the subscriber you want to find within KlickTipp.', 'wp-webhooks' ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			'data'		=> array( 'short_description' => __( '(Array) Further data about the request.', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The subscriber was successfully returned.',
			'data' => 
			array (
			  'subscriber' => 
			  array (
				'id' => '101880000',
				'listid' => '0',
				'optin' => '20.05.2022 14:25:27',
				'optin_ip' => '0.0.0.0',
				'email' => 'jon@doe.test',
				'status' => 'Subscribed',
				'bounce' => 'Not Bounced',
				'date' => '20.05.2022 14:25:27',
				'ip' => '0.0.0.0',
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
				  8713617 => '1653049872',
				),
			  ),
			),
		  );

		return array(
			'action'			=> 'klickt_find_subscriber', //required
			'name'			   => __( 'Find subscriber', 'wp-webhooks' ),
			'sentence'			   => __( 'find a subscriber', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Find a subscriber within "KlickTipp".', 'wp-webhooks' ),
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
			$subscriber = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'subscriber' );

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

			if( empty( $subscriber ) ){
                $return_args['msg'] = __( "Please define a valid email address within the user argument.", 'wp-webhooks' );
				return $return_args;
            }

			if( ! is_numeric( $subscriber ) && ! is_email( $subscriber ) ){
				$return_args['msg'] = __( "Please define either a valid email or subscriber id within the user argument.", 'wp-webhooks' );
				return $return_args;
			}

			$klickt_helpers = WPWHPRO()->integrations->get_helper( 'klicktipp', 'klickt_helpers' );

			$connector = $klickt_helpers->get_klicktipp();

			if( ! empty( $connector ) ){

				$connector->login( $username, $password );

				if( is_numeric( $subscriber ) ){
					$subscriber_id = intval( $subscriber );
				} else {
					$subscriber_id = $connector->subscriber_search( $subscriber );
				}

				if( ! empty( $subscriber_id ) ){
					$subscriber = $connector->subscriber_get($subscriber_id);

					if( ! empty( $subscriber ) ){
						$return_args['success'] = true;
						$return_args['msg'] = __( "The subscriber was successfully returned.", 'wp-webhooks' );
						$return_args['data']['subscriber'] = $subscriber;
					} else {
						$return_args['msg'] = __( "We could not fetch the subscriber for the found ID.", 'wp-webhooks' );
						$return_args['data']['error'] = $connector->get_last_error();
					}

				} else {
					$return_args['msg'] = __( "We could not find a subscriber for your given data.", 'wp-webhooks' );
					$return_args['data']['error'] = $connector->get_last_error();
				}
				
				$connector->logout();

			} else {
				$return_args['msg'] = __( "An error occured while loading the helper.", 'wp-webhooks' );
			}
			
			return $return_args;
	
		}

	}

endif; // End if class_exists check.