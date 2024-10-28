<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_tomba_Actions_tomba_find_email' ) ) :

	/**
	 * Load the tomba_find_email action
	 *
	 * @since 6.1.3
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_tomba_Actions_tomba_find_email {

	public function get_details(){

		$parameter = array(
			'auth_template'		=> array( 
				'required' => true, 
				'type' => 'select', 
				'multiple' => false, 
				'label' => __( 'Authentication template', 'wp-webhooks' ), 
				'query'			=> array(
					'filter'	=> 'authentications',
					'args'		=> array(
						'auth_methods' => array( 'tomba_auth' )
					)
				),
				'short_description' => __( 'Use globally defined Tomba.io credentials to authenticate this action via an authentication template.', 'wp-webhooks' ),
				'description' => __( 'This argument accepts the ID of a Tomba.io authentication template of your choice. You can create new templates within the "Authentication" tab.', 'wp-webhooks' ),
			),
			'domain'		=> array( 
				'required' => true, 
				'label' => __( 'Domain', 'wp-webhooks' ), 
				'short_description' => __( 'The domain of the email.', 'wp-webhooks' ),
			),
			'full_name'		=> array( 
				'required' => false, 
				'label' => __( 'Full name', 'wp-webhooks' ), 
				'short_description' => __( 'The full name of the person.', 'wp-webhooks' ),
			),
			'first_name'		=> array( 
				'required' => false, 
				'label' => __( 'First name', 'wp-webhooks' ), 
				'short_description' => __( 'The first name of the person. Not case-sensitive.', 'wp-webhooks' ),
			),
			'last_name'		=> array( 
				'required' => false, 
				'label' => __( 'Last name', 'wp-webhooks' ), 
				'short_description' => __( 'The last name of the person. Not case-sensitive.', 'wp-webhooks' ),
			),
			'full_response' => array( 
				'label' => __( 'Return full response', 'wp-webhooks' ), 
				'type' => 'select',
				'multiple' => false,
				'choices' => array(
					'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
				),
				'default_value' => 'no',
				'short_description' => __( 'Return the full HTTP response instead of our simplified version. This gives you access to cookies, headers, and much more. Default: "no"', 'wp-webhooks' ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			'data'		=> array( 'short_description' => __( '(Array) Further data about the request.', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The email was successfully returned.',
			'data' => 
			array (
			  'website_url' => 'domain.com',
			  'accept_all' => false,
			  'email' => 'jondoe@domain.com',
			  'first_name' => 'Jon',
			  'last_name' => 'Doe',
			  'country' => NULL,
			  'gender' => 'female',
			  'phone_number' => false,
			  'position' => NULL,
			  'twitter' => 'https://twitter.com/xxx',
			  'linkedin' => NULL,
			  'full_name' => 'Jon Doe',
			  'company' => NULL,
			  'score' => 88,
			  'verification' => 
			  array (
				'date' => '2023-03-07',
				'status' => 'valid',
			  ),
			  'sources' => 
			  array (
				0 => 
				array (
				  'uri' => 'https://domainbigdata.com/something.com',
				  'website_url' => 'domainbigdata.com',
				  'extracted_on' => '2022-02-03T12:13:56+04:00',
				  'last_seen_on' => '2022-02-06T16:12:29+04:00',
				  'still_on_page' => true,
				),
			  ),
			),
		);

		return array(
			'action'			=> 'tomba_find_email', //required
			'name'			   => __( 'Find email', 'wp-webhooks' ),
			'sentence'			   => __( 'find an email', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Find an email within "Tomba.io".', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'tomba',
			'premium'	   	=> true
		);


		}

		public function execute( $return_data, $response_body ){
			
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(),
			);

			$auth_template = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'auth_template' );
			$domain = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'domain' );
			$full_name = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'full_name' );
			$first_name = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'first_name' );
			$last_name = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'last_name' );
			$full_response = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'full_response' ) === 'yes' ) ? true : false;
			$api_key = '';
			$api_secret_key = '';

			if( empty( $domain ) ){
                $return_args['msg'] = __( "Please define the domain argument.", 'wp-webhooks' );
				return $return_args;
            }

			if( ! empty( $auth_template ) ){
				$tomba_auth = WPWHPRO()->integrations->get_auth( 'tomba', 'tomba_auth' );
				$credentials = $tomba_auth->get_credentials( $auth_template );

				if(
					isset( $credentials['wpwhpro_tomba_api_key'] )
					&& ! empty( $credentials['wpwhpro_tomba_api_key'] )
				){
					$api_key = $credentials['wpwhpro_tomba_api_key'];
				}

				if(
					isset( $credentials['wpwhpro_tomba_api_key_secret'] )
					&& ! empty( $credentials['wpwhpro_tomba_api_key_secret'] )
				){
					$api_secret_key = $credentials['wpwhpro_tomba_api_key_secret'];
				}
			}

			if( empty( $api_key ) || empty( $api_secret_key ) ){
                $return_args['msg'] = __( "The provided API key is invalid.", 'wp-webhooks' );
				return $return_args;
            }

			$http_args = array(
				'method' => 'GET',
				'blocking' => true,
				'httpversion' => '1.1',
				'timeout' => 20,
				'headers' => array(
					'X-Tomba-Key' => $api_key,
					'X-Tomba-Secret' => $api_secret_key,
				),
				'body' => array(
					'domain' => $domain,
					'full_name' => $full_name,
					'first_name' => $first_name,
					'last_name' => $last_name,
				),
			);
	
			$api_url = 'https://api.tomba.io/v1/email-finder/';

			$response = WPWHPRO()->http->send_http_request( $api_url, $http_args );

			if( $full_response ){
				//Map the response
				$return_args = array_merge( $return_args, $response );
			} else {
				if( isset( $response['success'] ) ){
					if( $response['success'] ){

						if( isset( $response['content']['data'] ) && ! empty( $response['content']['data'] ) ){
							$return_args['success'] = true;
							$return_args['msg'] = __( "The email was successfully returned.", 'wp-webhooks' );
							$return_args['data'] = isset( $response['content']['data'] ) ? $response['content']['data'] : $response['content'];
						} else {
							$return_args['msg'] = __( "No email found.", 'wp-webhooks' );
						}
						
					} else {
						$return_args['msg'] = __( "An error occured finding the email.", 'wp-webhooks' );
					}
				} else {
					$return_args['msg'] = __( "We did not get a valid response code.", 'wp-webhooks' );
				}
			}
			
			return $return_args;
	
		}

	}

endif; // End if class_exists check.