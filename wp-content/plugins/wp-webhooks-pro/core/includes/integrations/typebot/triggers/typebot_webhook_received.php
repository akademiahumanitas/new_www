<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_typebot_Triggers_typebot_webhook_received' ) ) :

 /**
  * Load the typebot_webhook_received trigger
  *
  * @since 6.1.3
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_typebot_Triggers_typebot_webhook_received {

	public function get_details(){

		$parameter = array(
			'custom_data' => array( 'short_description' => __( '(Mixed) The data that was sent along with the HTTP call that was made to the receivable URL from within Typebot.', 'wp-webhooks' ) ),
		);

		$description = array(
			'steps' => array(
				__( 'Within your Typebot chat of choice, add the "Webhooks" widget.', 'wp-webhooks' ),
				__( 'Within the Widget, add the dynamically created URL from within the settings of this trigger.', 'wp-webhooks' ),
				__( 'Select the "POST" method and test the step using real data.', 'wp-webhooks' ),
			),
			'tipps' => array(
				__( 'To receive data on the receivable URL, please use the "Webhooks" widget within your Typebot chat.', 'wp-webhooks' ),
			)
		);

		$settings = array(
			'load_default_settings' => false,
			'data' => array(
				'wpwhpro_typebot_return_full_request' => array(
					'id'		  => 'wpwhpro_typebot_return_full_request',
					'type'		=> 'checkbox',
					'label'	   => __( 'Send full request', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'Send the full, validated request instead of the payload (body) data only. This gives you access to header, cookies, response type and much more.', 'wp-webhooks' )
				),
			)
		);

		return array(
			'trigger'		   => 'typebot_webhook_received',
			'name'			  => __( 'Typebot webhook request received', 'wp-webhooks' ),
			'sentence'			  => __( 'a Typebot webhook request was received', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a request was received from one of your Typebot chats.', 'wp-webhooks' ),
			'description'	   => $description,
			'integration'	   => 'typebot',
			'receivable_url'	=> true,
			'premium'		   => true,
		);

	}

	public function execute( $return_data, $response_body, $trigger_url_name ){

		if( $trigger_url_name !== null ){
			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'typebot_webhook_received', $trigger_url_name );
			if( ! empty( $webhooks ) ){
				$webhooks = array( $webhooks );
			} else {
				$return_data['msg'] = __( 'We could not locate a callable trigger URL.', 'wp-webhooks' );
				return $return_data;
			}
		} else {
			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'typebot_webhook_received' );
		}
		

		$payload = $response_body['content'];

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){
	  
				  if( $settings_name === 'wpwhpro_typebot_return_full_request' && ! empty( $settings_data ) ){
					$payload = $response_body;
				  }
	  
				}
			}

			if( $is_valid ){

				$webhook_response = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload, array( 'blocking' => true ) );

				if( $webhook_url_name !== null ){
					$response_data_array[ $webhook_url_name ] = $webhook_response;
				} else {
					$response_data_array[] = $webhook_response;
				}
			}

		}

		$return_data['success'] = true;
		$return_data['data'] = ( count( $response_data_array ) > 1 ) ? $response_data_array : reset( $response_data_array );

		do_action( 'wpwhpro/webhooks/trigger_typebot_webhook_received', $return_data, $response_body, $trigger_url_name, $response_data_array );

		return $return_data;
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'custom_data' => 'The data that was sent to the receivable data URL. Or the full request array.',
		);

		return $data;
	}

  }

endif; // End if class_exists check.