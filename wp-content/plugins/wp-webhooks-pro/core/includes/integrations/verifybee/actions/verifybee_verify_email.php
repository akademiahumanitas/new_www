<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_verifybee_Actions_verifybee_verify_email' ) ) :

	/**
	 * Load the verifybee_verify_email action
	 *
	 * @since 6.1.3
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_verifybee_Actions_verifybee_verify_email {

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
						'auth_methods' => array( 'verifybee_auth' )
					)
				),
				'short_description' => __( 'Use globally defined VerifyBee credentials to authenticate this action.', 'wp-webhooks' ),
				'description' => __( 'This argument accepts the ID of a VerifyBee authentication template of your choice. You can create new templates within the "Authentication" tab.', 'wp-webhooks' ),
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
			'data' => 
			array (
				'status' => 'success',
				'data' => 
				array (
				  'deliverable' => 1,
				  'catchAll' => 0,
				  'validFormat' => 1,
				),
			  ),
		  );

		return array(
			'action'			=> 'verifybee_verify_email', //required
			'name'			   => __( 'Verify email', 'wp-webhooks' ),
			'sentence'			   => __( 'verify the validity of an email', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Verify the validity and functionality of an email within "VerifyBee".', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'verifybee',
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
				$verifybee_auth = WPWHPRO()->integrations->get_auth( 'verifybee', 'verifybee_auth' );
				$credentials = $verifybee_auth->get_credentials( $auth_template );

				if(
					isset( $credentials['wpwhpro_verifybee_api_key'] )
					&& ! empty( $credentials['wpwhpro_verifybee_api_key'] )
				){
					$api_key = $credentials['wpwhpro_verifybee_api_key'];
				}
			}

			if( empty( $api_key ) ){
                $return_args['msg'] = __( "The provided API key is invalid.", 'wp-webhooks' );
				return $return_args;
            }

			$http_args = array(
				'method' => 'POST',
				'blocking' => true,
				'httpversion' => '1.1',
				'timeout' => 20,
				'headers' => array(
					'content-type' => 'application/json',
					'Vb-Token' => $api_key,
				),
				'body' => array(
					'email' => $email
				)
			);
	
			$api_url = 'https://app.verifybee.io/api/v1.3/verify/';

			$response = WPWHPRO()->http->send_http_request( $api_url, $http_args );

			if( $full_response ){
				//Map the response
				$return_args = array_merge( $return_args, $response );
			} else {
				if( isset( $response['code'] ) ){
					$response_code = intval( $response['code'] );
					if( 
						$response_code >= 200 
						&& $response_code >! 299 
						&& is_array( $response['content'] ) 
						&& isset( $response['content']['status'] )
						&& $response['content']['status'] === 'success'
					){
						$return_args['success'] = true;
						$return_args['msg'] = __( "The email was successfully verified.", 'wp-webhooks' );
						$return_args['data'] = $response['content'];
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