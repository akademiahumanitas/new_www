<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_openai_Actions_openai_create_image' ) ) :

	/**
	 * Load the openai_create_image action
	 *
	 * @since 6.1.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_openai_Actions_openai_create_image {

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
							'auth_methods' => array( 'openai_auth' )
						)
					),
					'short_description' => __( 'Use globally defined OpenAI credentials to authenticate this action.', 'wp-webhooks' ),
					'description' => __( 'This argument accepts the ID of a OpenAI authentication template of your choice. You can create new templates within the "Authentication" tab.', 'wp-webhooks' ),
				),
				'prompt'		=> array( 
					'required' => false, 
					'label' => __( 'Prompt', 'wp-webhooks' ), 
					'short_description' => __( 'Describe the desired image(s) here.', 'wp-webhooks' ),
				),
				'number'		=> array( 
					'required' => false, 
					'label' => __( 'Number', 'wp-webhooks' ), 
					'short_description' => __( 'The number of images you want to create.', 'wp-webhooks' ),
				),
				'size'		=> array( 
					'required' => false, 
					'label' => __( 'Size', 'wp-webhooks' ),
					'type' => 'select',
					'multiple' => false,
					'default_value' => '1024x1024', 
					'choices' => array( 
						'1024x1024' => __( '1024x1024', 'wp-webhooks' ),
						'512x512' => __( '512x512', 'wp-webhooks' ),
						'256x256' => __( '256x256', 'wp-webhooks' ),
					), 
					'short_description' => __( 'The size of the generated image(s).', 'wp-webhooks' ),
				),
				'response_format'		=> array( 
					'required' => false, 
					'label' => __( 'Response format', 'wp-webhooks' ),
					'type' => 'select',
					'multiple' => false,
					'default_value' => 'url', 
					'choices' => array( 
						'url' => __( 'URL', 'wp-webhooks' ),
						'b64_json' => __( 'Base64 JSON', 'wp-webhooks' ),
					), 
					'short_description' => __( 'The format the image will be returned in.', 'wp-webhooks' ),
				),
				'user'		=> array( 
					'required' => false, 
					'label' => __( 'User', 'wp-webhooks' ), 
					'short_description' => __( 'A unique identifier representing your end-user.', 'wp-webhooks' ),
				),
				'timeout'		=> array( 
					'required' => false, 
					'default_value' => 30, 
					'label' => __( 'Timeout', 'wp-webhooks' ), 
					'short_description' => __( 'Set the number of seconds you want to wait before the request to OpenAI runs into a timeout.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(Array) Further data about the request.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The edit was received successfully.',
				'data' => 
				array (
				  'created' => 1671988375,
				  'data' => 
				  array (
					0 => 
					array (
					  'url' => 'https://domain.test/image_url.png',
					),
				  ),
				),
			);

			$description = array(
				'tipps' => array(
					__( 'To learn more about this endpoint, please visit the following URL: ', 'wp-webhooks' ) . '<a title="OpenAI" target="_blank" href="https://beta.openai.com/docs/api-reference/images/create">https://beta.openai.com/docs/api-reference/images/create</a>',
				),
			);

			return array(
				'action'			=> 'openai_create_image', //required
				'name'			   	=> __( 'Create image', 'wp-webhooks' ),
				'sentence'			=> __( 'create an image', 'wp-webhooks' ),
				'parameter'		 	=> $parameter,
				'returns'		   	=> $returns,
				'returns_code'	  	=> $returns_code,
				'short_description' => __( 'Create an image within "OpenAI".', 'wp-webhooks' ),
				'description'	   	=> $description,
				'integration'	   	=> 'openai',
				'premium'	   		=> true
			);

		}

		public function execute( $return_data, $response_body ){
			
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(),
			);

			$auth_template = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'auth_template' );
			$prompt = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'prompt' );
			$number = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'number' );
			$size = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'size' );
			$response_format = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'response_format' );
			$user = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$timeout = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'timeout' );

			if( empty( $auth_template ) ){
                $return_args['msg'] = __( "Please set the auth_template argument.", 'wp-webhooks' );
				return $return_args;
            }

			$http_args = array(
				'timeout' => ( ! empty( $timeout ) ) ? $timeout : 30,
				'headers' => array(
					'content-type' => 'application/json'
				),
			);

			$openai_auth = WPWHPRO()->integrations->get_auth( 'openai', 'openai_auth' );
			$http_args = $openai_auth->apply_auth( $auth_template, $http_args );

			if( empty( $http_args ) ){
                $return_args['msg'] = __( "An error occured applying the auth template.", 'wp-webhooks' );
				return $return_args;
            }

			$payload = array(
				'prompt' => $prompt,
			);
			
			if( ! empty( $size ) ){
				$payload['size'] = $size;
			}
			
			if( ! empty( $response_format ) ){
				$payload['response_format'] = $response_format;
			}
			
			if( ! empty( $user ) ){
				$payload['user'] = $user;
			}
			
			if( is_numeric( $number ) && ! empty( $number ) ){
				$payload['n'] = $number;
			}

			$http_args['body'] = $payload;

			$response = WPWHPRO()->http->send_http_request( 'https://api.openai.com/v1/images/generations', $http_args );

			if( 
				! empty( $response )
				&& is_array( $response )
				&& isset( $response['success'] )
				&& $response['success']
			){

				$content = $response['content'];

				$return_args['success'] = true;
				$return_args['msg'] = __( "The image was received successfully.", 'wp-webhooks' );
				$return_args['data'] = $content;

			} else {
				$return_args['msg'] = __( "An error occured while sending the data to OpenAI.", 'wp-webhooks' );

				if( isset( $response['content'] ) ){
					$return_args['data'] = $response['content'];
				}
			}
			
			return $return_args;
	
		}

	}

endif; // End if class_exists check.