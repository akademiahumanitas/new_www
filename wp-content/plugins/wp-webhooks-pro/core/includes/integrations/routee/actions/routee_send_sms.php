<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_routee_Actions_routee_send_sms' ) ) :

	/**
	 * Load the routee_send_sms action
	 *
	 * @since 6.1.4
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_routee_Actions_routee_send_sms {

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
							'auth_methods' => array( 'routee_auth' )
						)
					),
					'short_description' => __( 'Use globally defined Routee credentials to authenticate this action.', 'wp-webhooks' ),
					'description' => __( 'This argument accepts the ID of a Routee authentication template of your choice. You can create new templates within the "Authentication" tab.', 'wp-webhooks' ),
				),
				'recipient'		=> array( 
					'required' => true,
					'label' => __( 'Recipient (To)', 'wp-webhooks' ), 
					'short_description' => __( 'The recipient of the message. E.g. +30697XXXXXX', 'wp-webhooks' ),
				),
				'message'		=> array( 
					'required' => true, 
					'label' => __( 'Message', 'wp-webhooks' ), 
					'short_description' => __( 'The message you want to send.', 'wp-webhooks' ),
				),
				'sender'		=> array(
					'required' => false, 
					'label' => __( 'Sender (From)', 'wp-webhooks' ), 
					'short_description' => __( 'The sender name of the message. E.g. senderName', 'wp-webhooks' ),
				),
				'completion_callback'		=> array(
					'required' => false, 
					'label' => __( 'Completion Callback URL', 'wp-webhooks' ), 
					'short_description' => __( 'Add a URL that is called once the SMS reaches is final message delivery status. This field also accepts receivable URLs from our plugin.', 'wp-webhooks' ),
					'description' => __( 'You can also add any of the receivable URLs from within our other webhook triggers. E.g. you can use the receivable URL of the Webhooks integration trigger to fire another flow based on the feedback from Routee.net', 'wp-webhooks' ),
				),
				'timeout'		=> array( 
					'required' => false, 
					'default_value' => 30, 
					'label' => __( 'Timeout', 'wp-webhooks' ), 
					'short_description' => __( 'Set the number of seconds you want to wait before the request to Routee runs into a timeout.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(Array) Further data about the request.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The SMS was sent successfully.',
				'data' => 
				array (
				  'trackingId' => '646bxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx',
				  'status' => 'Queued',
				  'createdAt' => '2023-03-29T18:35:20.281Z',
				  'from' => 'WP Webhooks',
				  'to' => '+123456789',
				  'body' => 'This is a test message.',
				  'bodyAnalysis' => 
				  array (
					'parts' => 1,
					'unicode' => false,
					'characters' => 45,
				  ),
				  'flash' => false,
				),
			);

			$description = array(
				'tipps' => array(
					__( 'To learn more about this endpoint, please visit the following URL: ', 'wp-webhooks' ) . '<a title="Routee" target="_blank" href="https://docs.routee.net/docs/send-a-simple-sms">https://docs.routee.net/docs/send-a-simple-sms</a>',
				),
			);

			return array(
				'action'			=> 'routee_send_sms', //required
				'name'			   	=> __( 'Send SMS', 'wp-webhooks' ),
				'sentence'			=> __( 'send an SMS', 'wp-webhooks' ),
				'parameter'		 	=> $parameter,
				'returns'		   	=> $returns,
				'returns_code'	  	=> $returns_code,
				'short_description' => __( 'Send an SMS within Routee.net.', 'wp-webhooks' ),
				'description'	   	=> $description,
				'integration'	   	=> 'routee',
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
			$recipient = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'recipient' );
			$message = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'message' );
			$sender = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'sender' );
			$timeout = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'timeout' );
			$completion_callback = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'completion_callback' );

			if( empty( $auth_template ) ){
                $return_args['msg'] = __( "Please set the auth_template argument.", 'wp-webhooks' );
				return $return_args;
            }

			$http_args = array(
				'timeout' => ( ! empty( $timeout ) && is_numeric( $timeout ) ) ? $timeout : 30,
				'headers' => array(
					'content-type' => 'application/json',
					'expect' => '',
				),
			);

			$routee_auth = WPWHPRO()->integrations->get_auth( 'routee', 'routee_auth' );
			$http_args = $routee_auth->apply_auth( $auth_template, $http_args );

			if( empty( $http_args ) ){
                $return_args['msg'] = __( "An error occured applying the auth template.", 'wp-webhooks' );
				return $return_args;
            }

			$payload = array(
				'to' => $recipient,
				'body' => $message,
			);
			
			if( ! empty( $sender ) ){
				$payload['from'] = $sender;
			}
			
			if( ! empty( $completion_callback ) ){
				$payload['callback'] = array(
					'strategy' => 'OnCompletion',
					'url' => $completion_callback,
				);
			}

			$http_args['body'] = $payload;

			$response = WPWHPRO()->http->send_http_request( 'https://connect.routee.net/sms', $http_args );

			if( 
				! empty( $response )
				&& is_array( $response )
				&& isset( $response['success'] )
				&& $response['success']
			){

				$content = $response['content'];

				$return_args['success'] = true;
				$return_args['msg'] = __( "The SMS was sent successfully.", 'wp-webhooks' );
				$return_args['data'] = $content;

			} else {
				$return_args['msg'] = __( "An error occured while sending the SMS via Routee.", 'wp-webhooks' );

				if( isset( $response['content'] ) ){
					$return_args['data'] = $response['content'];
				}
			}
			
			return $return_args;
	
		}

	}

endif; // End if class_exists check.