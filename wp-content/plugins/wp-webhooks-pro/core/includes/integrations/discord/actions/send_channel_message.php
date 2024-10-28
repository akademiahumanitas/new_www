<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_discord_Actions_send_channel_message' ) ) :

	/**
	 * Load the send_channel_message action
	 *
	 * @since 6.1.4
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_discord_Actions_send_channel_message {

		public function get_details(){

			$parameter = array(
				'webhook_url'		=> array( 
					'required' => true,
					'label' => __( 'Webhook URL', 'wp-webhooks' ), 
					'short_description' => __( 'The webhook URL of the channel you want to send the message to.', 'wp-webhooks' ),
					'description' => __( 'To learn more about where to find the webhook URL and how to customize the avatar, please refer to this url: <a title="Visit Discord helpfile" target="_blank" href="https://support.discord.com/hc/en-us/articles/228383668-Intro-to-Webhooks">https://support.discord.com/hc/en-us/articles/228383668-Intro-to-Webhooks</a>', 'wp-webhooks' ),
				),
				'message'		=> array( 
					'required' => true,
					'label' => __( 'Message', 'wp-webhooks' ), 
					'short_description' => __( 'The message you want to send to the channel.', 'wp-webhooks' ),
				),
				'timeout'		=> array( 
					'required' => false, 
					'default_value' => 30, 
					'label' => __( 'Timeout', 'wp-webhooks' ), 
					'short_description' => __( 'Set the number of seconds you want to wait before the request to Discord runs into a timeout.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(Array) Further data about the request.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The message was sent successfully.',
				'data' => 
				array (),
			);

			$description = array(
				'tipps' => array(
					__( 'To learn more about this endpoint, please visit the following URL: ', 'wp-webhooks' ) . '<a title="Discord" target="_blank" href="https://docs.discord.net/docs/send-a-simple-sms">https://docs.discord.net/docs/send-a-simple-sms</a>',
				),
			);

			return array(
				'action'			=> 'send_channel_message', //required
				'name'			   	=> __( 'Send channel message', 'wp-webhooks' ),
				'sentence'			=> __( 'send a channel message', 'wp-webhooks' ),
				'parameter'		 	=> $parameter,
				'returns'		   	=> $returns,
				'returns_code'	  	=> $returns_code,
				'short_description' => __( 'Send a channel message within Discord.', 'wp-webhooks' ),
				'description'	   	=> $description,
				'integration'	   	=> 'discord',
				'premium'	   		=> true
			);

		}

		public function execute( $return_data, $response_body ){
			
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(),
			);

			$webhook_url = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'webhook_url' );
			$message = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'message' );
			$timeout = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'timeout' );

			if( empty( $webhook_url ) ){
                $return_args['msg'] = __( "Please set the webhook_url argument.", 'wp-webhooks' );
				return $return_args;
            }

			$http_args = array(
				'timeout' => ( ! empty( $timeout ) && is_numeric( $timeout ) ) ? $timeout : 30,
				'headers' => array(
					'content-type' => 'application/json',
				),
			);

			$payload = array(
				'content' => $message,
			);

			$http_args['body'] = $payload;

			$response = WPWHPRO()->http->send_http_request( $webhook_url, $http_args );

			if( 
				! empty( $response )
				&& is_array( $response )
				&& isset( $response['success'] )
				&& $response['success']
			){

				$content = $response['content'];

				$return_args['success'] = true;
				$return_args['msg'] = __( "The message was sent successfully.", 'wp-webhooks' );
				$return_args['data'] = $content;

			} else {
				$return_args['msg'] = __( "An error occured while sending the message to Discord.", 'wp-webhooks' );

				if( isset( $response['content'] ) ){
					$return_args['data'] = $response['content'];
				}
			}
			
			return $return_args;
	
		}

	}

endif; // End if class_exists check.