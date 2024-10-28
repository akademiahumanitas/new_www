<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_openai_Actions_openai_create_completion' ) ) :

	/**
	 * Load the openai_create_completion action
	 *
	 * @since 6.1.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_openai_Actions_openai_create_completion {

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
						'text-davinci-003' => 'text-davinci-003',
						'text-davinci-002' => 'text-davinci-002',
						'text-davinci-001' => 'text-davinci-001',
						'text-ada-001' => 'text-ada-001',
						'text-curie-001' => 'text-curie-001',
						'text-babbage-001' => 'text-babbage-001',
						'code-davinci-002' => 'code-davinci-002',
						'code-cushman-001' => 'code-cushman-001',
					),
					'label' => __( 'Model', 'wp-webhooks' ), 
					'short_description' => __( 'The ID of the model you want to use. E.g.: text-davinci-003', 'wp-webhooks' ),
				),
				'prompt'		=> array( 
					'required' => false, 
					'label' => __( 'Prompt', 'wp-webhooks' ), 
					'short_description' => __( 'The prompt(s) to generate completions for. This argument supports a string, a JSON formatted array of strings, array of tokens, or array of token arrays.', 'wp-webhooks' ),
				),
				'suffix'		=> array( 
					'required' => false, 
					'label' => __( 'Suffix', 'wp-webhooks' ), 
					'short_description' => __( 'The suffix that appears after the completion of the inserted text.', 'wp-webhooks' ),
				),
				'max_tokens'		=> array( 
					'required' => false, 
					'label' => __( 'Max amount of tokens', 'wp-webhooks' ), 
					'short_description' => __( 'The maximum number of tokens used to generate the completion.', 'wp-webhooks' ),
				),
				'temperature'		=> array( 
					'required' => false, 
					'label' => __( 'Temperature', 'wp-webhooks' ), 
					'short_description' => __( 'This argument accepts a value between 0 and 1. The higher the temperatue (e.g. 0.9), the more creative the answer.', 'wp-webhooks' ),
				),
				'top_p'		=> array( 
					'required' => false, 
					'label' => __( 'Nucleus sampling', 'wp-webhooks' ), 
					'short_description' => __( 'This is an alternative to the temperature argument where the model considers the results of the tokens with the given probability mass. Its suggested to use temperature instead.', 'wp-webhooks' ),
				),
				'number'		=> array( 
					'required' => false, 
					'label' => __( 'Number', 'wp-webhooks' ), 
					'short_description' => __( 'The number of completions generated for each prompt. Use wisely as multiple can consume more tokens.', 'wp-webhooks' ),
				),
				'logprobs'		=> array( 
					'required' => false, 
					'label' => __( 'Log probabilities', 'wp-webhooks' ), 
					'short_description' => __( 'The log probabilities on the most likely tokens, as well the chosen tokens.', 'wp-webhooks' ),
				),
				'echo'		=> array( 
					'required' => false, 
					'label' => __( 'Echo prompt', 'wp-webhooks' ),
					'type' => 'select',
					'multiple' => false,
					'default_value' => 'no', 
					'choices' => array( 
						'yes' => __( 'Yes', 'wp-webhooks' ),
						'no' => __( 'No', 'wp-webhooks' ),
					), 
					'short_description' => __( 'Echo back the promt in addition to the completion.', 'wp-webhooks' ),
				),
				'stop'		=> array( 
					'required' => false, 
					'label' => __( 'Stop sequences', 'wp-webhooks' ), 
					'short_description' => __( 'Add up to four stop sequences to prevent the API from generating further tokens.', 'wp-webhooks' ),
				),
				'presence_penalty'		=> array( 
					'required' => false, 
					'label' => __( 'Presence penalty', 'wp-webhooks' ), 
					'short_description' => __( 'A number between -2.0 and 2.0. Positive values penalize new tokens based on whether they appear in the text so far, increasing the models likelihood to talk about new topics.', 'wp-webhooks' ),
				),
				'frequency_penalty'		=> array( 
					'required' => false, 
					'label' => __( 'Frequency penalty', 'wp-webhooks' ), 
					'short_description' => __( 'A number between -2.0 and 2.0. Positive values penalize new tokens based on their existing frequency in the text so far, decreasing the models likelihood to repeat the same line verbatim.', 'wp-webhooks' ),
				),
				'best_of'		=> array( 
					'required' => false, 
					'label' => __( 'Best of completions', 'wp-webhooks' ), 
					'short_description' => __( 'Generates the best of the completions on server-side and returns the "best" (the one with the highest log probability per token).', 'wp-webhooks' ),
				),
				'logit_bias'		=> array( 
					'required' => false, 
					'label' => __( 'Token', 'wp-webhooks' ), 
					'short_description' => __( 'Modify the likelihood of specified tokens appearing in the completion.', 'wp-webhooks' ),
					'description' => __( 'As an example, you can pass <code>{"50256": -100}</code> to prevent the <|endoftext|> token from being generated.', 'wp-webhooks' ),
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
				'msg' => 'The completion was received successfully.',
				'data' => 
				array (
				'id' => 'cmpl-6RMmq4mkIBArZxxxxxxxxxxxxxxxx',
				'object' => 'text_completion',
				'created' => 1671980468,
				'model' => 'text-davinci-003',
				'choices' => 
				array (
					0 => 
					array (
					'text' => ' Create a WordPress Website in 5 Easy Steps | A Step-By-Step Guide on Building a Website with WordPress',
					'index' => 0,
					'logprobs' => NULL,
					'finish_reason' => 'stop',
					),
				),
				'usage' => 
				array (
					'prompt_tokens' => 28,
					'completion_tokens' => 22,
					'total_tokens' => 50,
				),
				),
			);

			$description = array(
				'tipps' => array(
					__( 'To learn more about this endpoint, please visit the following URL: ', 'wp-webhooks' ) . '<a title="OpenAI" target="_blank" href="https://beta.openai.com/docs/api-reference/completions/create">https://beta.openai.com/docs/api-reference/completions/create</a>',
				),
			);

			return array(
				'action'			=> 'openai_create_completion', //required
				'name'			   	=> __( 'Create completion', 'wp-webhooks' ),
				'sentence'			=> __( 'create a completion', 'wp-webhooks' ),
				'parameter'		 	=> $parameter,
				'returns'		   	=> $returns,
				'returns_code'	  	=> $returns_code,
				'short_description' => __( 'Create a completion within "OpenAI".', 'wp-webhooks' ),
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
			$prompt = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'prompt' );
			$suffix = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'suffix' );
			$max_tokens = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'max_tokens' );
			$temperature = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'temperature' );
			$top_p = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'top_p' );
			$number = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'number' );
			$logprobs = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'logprobs' );
			$echo = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'echo' ) === 'yes' ) ? true : false;
			$stop = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'stop' );
			$presence_penalty = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'presence_penalty' );
			$frequency_penalty = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'frequency_penalty' );
			$best_of = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'best_of' );
			$user = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$timeout = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'timeout' );

			if( empty( $auth_template ) ){
                $return_args['msg'] = __( "Please set the auth_template argument.", 'wp-webhooks' );
				return $return_args;
            }

			if( empty( $model ) ){
                $return_args['msg'] = __( "Please set the model argument.", 'wp-webhooks' );
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
				'prompt' => $prompt,
			);
			
			if( ! empty( $suffix ) ){
				$payload['suffix'] = $suffix;
			}
			
			if( is_numeric( $max_tokens ) ){
				$payload['max_tokens'] = $max_tokens;
			}
			
			if( is_numeric( $temperature ) ){
				$payload['temperature'] = $temperature;
			}
			
			if( is_numeric( $top_p ) ){
				$payload['top_p'] = $top_p;
			}
			
			if( is_numeric( $number ) && ! empty( $number ) ){
				$payload['n'] = $number;
			}
			
			if( is_numeric( $logprobs ) ){
				$payload['logprobs'] = $logprobs;
			}
			
			if( $echo ){
				$payload['echo'] = $echo;
			}
			
			if( $stop ){
				$payload['stop'] = ( WPWHPRO()->helpers->is_json( $stop ) ) ? json_decode( $stop, true ) : $stop;
			}

			if( is_numeric( $presence_penalty ) ){
				$payload['presence_penalty'] = $presence_penalty;
			}

			if( is_numeric( $frequency_penalty ) ){
				$payload['frequency_penalty'] = $frequency_penalty;
			}

			if( $best_of ){
				$payload['best_of'] = ( WPWHPRO()->helpers->is_json( $best_of ) ) ? json_decode( $best_of, true ) : $best_of;
			}

			if( $user ){
				$payload['user'] = $user;
			}

			$http_args['body'] = $payload;

			$response = WPWHPRO()->http->send_http_request( 'https://api.openai.com/v1/completions', $http_args );
	
			if( 
				! empty( $response )
				&& is_array( $response )
				&& isset( $response['success'] )
				&& $response['success']
			){

				$content = $response['content'];

				$return_args['success'] = true;
				$return_args['msg'] = __( "The completion was received successfully.", 'wp-webhooks' );
				$return_args['data'] = $content;

			} else {
				$return_args['msg'] = __( "An error occured while sending data to OpenAI.", 'wp-webhooks' );

				if( isset( $response['content'] ) ){
					$return_args['data'] = $response['content'];
				}
			}
			
			return $return_args;
	
		}

	}

endif; // End if class_exists check.