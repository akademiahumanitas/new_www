<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_tomba_Actions_tomba_search_domain' ) ) :

	/**
	 * Load the tomba_search_domain action
	 *
	 * @since 6.1.3
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_tomba_Actions_tomba_search_domain {

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
				'short_description' => __( 'The domain name. E.g. domain.com', 'wp-webhooks' ),
			),
			'page'		=> array( 
				'required' => false, 
				'label' => __( 'Page', 'wp-webhooks' ), 
				'short_description' => __( 'The pagination for emails. Default: 1', 'wp-webhooks' ),
			),
			'limit'		=> array( 
				'required' => false, 
				'label' => __( 'Limit', 'wp-webhooks' ), 
				'short_description' => __( 'The max number of emails to return per request.', 'wp-webhooks' ),
			),
			'type' => array( 
				'label' => __( 'Type', 'wp-webhooks' ), 
				'type' => 'select',
				'multiple' => false,
				'choices' => array(
					'personal' => array( 'label' => __( 'Personal', 'wp-webhooks' ) ),
					'generic' => array( 'label' => __( 'Generic', 'wp-webhooks' ) ),
				),
				'short_description' => __( 'This setting allows you to return either personal or generic emails. If you want to return all, leave it empty.', 'wp-webhooks' ),
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
			'msg' => 'The email was successfully verified.',
			'data' => 
			array (
			  'organization' => 
			  array (
				'location' => 
				array (
				  'country' => NULL,
				  'city' => NULL,
				  'state' => NULL,
				  'street_address' => NULL,
				),
				'social_links' => 
				array (
				  'twitter_url' => NULL,
				  'facebook_url' => NULL,
				  'linkedin_url' => NULL,
				),
				'disposable' => false,
				'webmail' => false,
				'website_url' => 'domain.com',
				'phone_number' => NULL,
				'industries' => NULL,
				'postal_code' => NULL,
				'employee_count' => 0,
				'founded' => NULL,
				'company_size' => NULL,
				'last_updated' => '2022-02-06T16:12:29+04:00',
				'revenue' => NULL,
				'accept_all' => false,
				'description' => NULL,
				'pattern' => NULL,
				'domain_score' => 30,
				'organization' => 'Doeorg',
				'whois' => 
				array (
				  'registrar_name' => NULL,
				  'created_date' => NULL,
				  'referral_url' => NULL,
				),
			  ),
			  'emails' => 
			  array (
				0 => 
				array (
				  'email' => 'jondoe@domain.com',
				  'first_name' => 'Jon',
				  'last_name' => 'Doe',
				  'full_name' => 'Jon Doe',
				  'gender' => 'male',
				  'phone_number' => NULL,
				  'type' => 'personal',
				  'country' => NULL,
				  'position' => NULL,
				  'department' => NULL,
				  'seniority' => NULL,
				  'twitter' => 'https://twitter.com/jondoe',
				  'linkedin' => NULL,
				  'accept_all' => false,
				  'pattern' => NULL,
				  'score' => 70,
				  'verification' => 
				  array (
					'date' => '2023-03-07',
					'status' => 'valid',
				  ),
				  'last_updated' => '2023-03-07T15:23:33+04:00',
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
			  ),
			),
		);

		return array(
			'action'			=> 'tomba_search_domain', //required
			'name'			   => __( 'Search domain', 'wp-webhooks' ),
			'sentence'			   => __( 'search one or multiple domains', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Search one or multiple domains within "Tomba.io".', 'wp-webhooks' ),
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
			$page = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'page' );
			$limit = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'limit' );
			$type = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'type' );
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

			$data = array(
				'domain' => $domain,
			);

			if( ! empty( $page ) ){
				$data['page'] = intval( $page );
			}

			if( ! empty( $limit ) ){
				$data['limit'] = intval( $limit );
			}

			if( ! empty( $type ) ){
				$data['type'] = sanitize_title( $type );
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
				'body' => $data,
			);
	
			$api_url = 'https://api.tomba.io/v1/domain-search/';

			$response = WPWHPRO()->http->send_http_request( $api_url, $http_args );

			if( $full_response ){
				//Map the response
				$return_args = array_merge( $return_args, $response );
			} else {
				if( isset( $response['success'] ) ){
					if( $response['success'] ){

						if( isset( $response['content']['data'] ) && ! empty( $response['content']['data'] ) ){
							$return_args['success'] = true;
							$return_args['msg'] = __( "The email was successfully verified.", 'wp-webhooks' );
							$return_args['data'] = isset( $response['content']['data'] ) ? $response['content']['data'] : $response['content'];
						} else {
							$return_args['msg'] = __( "No domains found.", 'wp-webhooks' );
						}

						
					} else {
						$return_args['msg'] = __( "An error occured validating the email.", 'wp-webhooks' );
					}
				} else {
					$return_args['msg'] = __( "We did not get a valid response code.", 'wp-webhooks' );
				}
			}
			
			return $return_args;
	
		}

	}

endif; // End if class_exists check.