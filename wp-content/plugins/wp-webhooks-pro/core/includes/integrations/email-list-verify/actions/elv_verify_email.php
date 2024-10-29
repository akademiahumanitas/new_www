<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_email_list_verify_Actions_elv_verify_email' ) ) :

	/**
	 * Load the elv_verify_email action
	 *
	 * @since 6.1.3
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_email_list_verify_Actions_elv_verify_email {

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
						'auth_methods' => array( 'elv_auth' )
					)
				),
				'short_description' => __( 'Use globally defined Email List Verify credentials to authenticate this action.', 'wp-webhooks' ),
				'description' => __( 'This argument accepts the ID of a Email List Verify authentication template of your choice. You can create new templates within the "Authentication" tab.', 'wp-webhooks' ),
			),
			'email'		=> array( 
				'required' => true, 
				'label' => __( 'Email', 'wp-webhooks' ), 
				'short_description' => __( 'The email you would like to verify.', 'wp-webhooks' ),
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
		);

		$description = array (
			'tipps' => array(
				__( 'If you would like to learn more about the Email List Verify API, please follow <a title="Go to bitly.com" target="_blank" href="https://www.emaillistverify.com/docs/#tag/Email-Validation-API">this link</a>.', 'wp-webhooks' ),
			),
		);

		return array(
			'action'			=> 'elv_verify_email', //required
			'name'			   => __( 'Verify email', 'wp-webhooks' ),
			'sentence'			   => __( 'verify the validity of an email', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Verify the validity and functionality of an email within "Email List Verify".', 'wp-webhooks' ),
			'description'	   => $description,
			'integration'	   => 'email-list-verify',
			'premium'	   	=> true
		);


		}

		public function execute( $return_data, $response_body ){
			
			$return_args = array(
				'success' => false,
				'msg' => '',
			);

			$auth_template = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'auth_template' );
			$email = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'email' );
			$full_response = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'full_response' ) === 'yes' ) ? true : false;
			$api_key = '';

			if( empty( $email ) ){
                $return_args['msg'] = __( "Please define the email argument.", 'wp-webhooks' );
				return $return_args;
            }

			if( ! is_email( $email ) ){
                $return_args['msg'] = __( "No given email is not a valid email.", 'wp-webhooks' );
				return $return_args;
            }

			$email = sanitize_email( $email );

			if( ! empty( $auth_template ) ){
				$elv_auth = WPWHPRO()->integrations->get_auth( 'email-list-verify', 'elv_auth' );
				$credentials = $elv_auth->get_credentials( $auth_template );

				if( 
					isset( $credentials['wpwhpro_email_list_verify_api_key'] )
					&& ! empty( $credentials['wpwhpro_email_list_verify_api_key'] )
				){
					$api_key = $credentials['wpwhpro_email_list_verify_api_key'];
				}
			}

			if( empty( $api_key ) ){
                $return_args['msg'] = __( "The provided API key is invalid.", 'wp-webhooks' );
				return $return_args;
            }

			$http_args = array(
				'method' => 'GET',
				'blocking' => true,
				'httpversion' => '1.1',
				'timeout' => 20,
			);
	
			$api_url = 'https://apps.emaillistverify.com/api/verifyEmail?secret=' . $api_key . '&email=' . rawurlencode( $email ) . '&timeout=20';

			$response = WPWHPRO()->http->send_http_request( $api_url, $http_args );

			if( $full_response ){
				//Map the response
				$return_args = array_merge( $return_args, $response );
			} else {
				if( isset( $response['success'] ) ){
					if( 
						$response['success']
						&& is_array( $response['content'] )
						&& in_array( 'ok', $response['content'] )
					){
						$return_args['success'] = true;
						$return_args['msg'] = __( "The email was successfully verified as a valid email.", 'wp-webhooks' );
					} else {
						$return_args['msg'] = __( "An error occured verifying the email.", 'wp-webhooks' );
						$return_args['data'] = $response['content'];
					}
				} else {
					$return_args['msg'] = __( "We did not get a valid response code.", 'wp-webhooks' );
				}
			}
			
			return $return_args;
	
		}

	}

endif; // End if class_exists check.