<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_openai_Actions_openai_create_edit' ) ) :

	/**
	 * Load the openai_create_edit action
	 *
	 * @since 6.1.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_openai_Actions_openai_create_edit {

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
				'model'		=> array( 
					'required' => true, 
					'type' => 'select', 
					'multiple' => false,
					'choices' => array(
						'text-davinci-edit-001' => 'text-davinci-edit-001',
						'code-davinci-edit-001' => 'code-davinci-edit-001',
					),
					'label' => __( 'Model', 'wp-webhooks' ), 
					'short_description' => __( 'The ID of the model you want to use. E.g.: text-davinci-003', 'wp-webhooks' ),
				),
				'input'		=> array( 
					'required' => false, 
					'label' => __( 'Input', 'wp-webhooks' ), 
					'short_description' => __( 'The input text used for the edit.', 'wp-webhooks' ),
				),
				'instruction'		=> array( 
					'required' => true, 
					'label' => __( 'Instruction', 'wp-webhooks' ), 
					'short_description' => __( 'The instructions that tell the model what to do.', 'wp-webhooks' ),
				),
				'number'		=> array( 
					'required' => false, 
					'label' => __( 'Number', 'wp-webhooks' ), 
					'short_description' => __( 'The number of inputs generated for the edit.', 'wp-webhooks' ),
				),
				'temperature'		=> array( 
					'required' => false, 
					'label' => __( 'Temperature', 'wp-webhooks' ), 
					'short_description' => __( 'This argument accepts a value between 0 and 1. The higher the temperatue (e.g. 0.9), the more creative the answer for the edit.', 'wp-webhooks' ),
				),
				'top_p'		=> array( 
					'required' => false, 
					'label' => __( 'Nucleus sampling', 'wp-webhooks' ), 
					'short_description' => __( 'This is an alternative to the temperature argument where the model considers the results of the tokens with the given probability mass. Its suggested to use temperature instead.', 'wp-webhooks' ),
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
				  'object' => 'edit',
				  'created' => 1671987179,
				  'choices' => 
				  array (
					0 => 
					array (
					  'text' => 'Creating a WordPress plugin',
					  'index' => 0,
					),
				  ),
				  'usage' => 
				  array (
					'prompt_tokens' => 29,
					'completion_tokens' => 17,
					'total_tokens' => 46,
				  ),
				),
			);

			$description = array(
				'tipps' => array(
					__( 'To learn more about this endpoint, please visit the following URL: ', 'wp-webhooks' ) . '<a title="OpenAI" target="_blank" href="https://beta.openai.com/docs/api-reference/edits/create">https://beta.openai.com/docs/api-reference/edits/create</a>',
				),
			);

			return array(
				'action'			=> 'openai_create_edit', //required
				'name'			   	=> __( 'Create edit', 'wp-webhooks' ),
				'sentence'			=> __( 'create an edit', 'wp-webhooks' ),
				'parameter'		 	=> $parameter,
				'returns'		   	=> $returns,
				'returns_code'	  	=> $returns_code,
				'short_description' => __( 'Create an edit within "OpenAI".', 'wp-webhooks' ),
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
			$model = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'model' );
			$input = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'input' );
			$instruction = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'instruction' );
			$number = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'number' );
			$temperature = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'temperature' );
			$top_p = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'top_p' );
			$timeout = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'timeout' );

			if( empty( $auth_template ) ){
                $return_args['msg'] = __( "Please set the auth_template argument.", 'wp-webhooks' );
				return $return_args;
            }

			if( empty( $model ) ){
                $return_args['msg'] = __( "Please set the model argument.", 'wp-webhooks' );
				return $return_args;
            }

			if( empty( $instruction ) ){
                $return_args['msg'] = __( "Please set the instruction argument.", 'wp-webhooks' );
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
				'model' => $model,
				'input' => $input,
				'instruction' => $instruction,
			);
			
			if( is_numeric( $temperature ) ){
				$payload['temperature'] = $temperature;
			}
			
			if( is_numeric( $top_p ) ){
				$payload['top_p'] = $top_p;
			}
			
			if( is_numeric( $number ) && ! empty( $number ) ){
				$payload['n'] = $number;
			}

			$http_args['body'] = $payload;

			$response = WPWHPRO()->http->send_http_request( 'https://api.openai.com/v1/edits', $http_args );

			if( 
				! empty( $response )
				&& is_array( $response )
				&& isset( $response['success'] )
				&& $response['success']
			){

				$content = $response['content'];

				$return_args['success'] = true;
				$return_args['msg'] = __( "The edit was received successfully.", 'wp-webhooks' );
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