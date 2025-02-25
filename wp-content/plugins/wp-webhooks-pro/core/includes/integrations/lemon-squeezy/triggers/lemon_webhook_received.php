<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_lemon_squeezy_Triggers_lemon_webhook_received' ) ) :

 /**
  * Load the lemon_webhook_received trigger
  *
  * @since 6.1.5
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_lemon_squeezy_Triggers_lemon_webhook_received {

	public function get_details(){

		$parameter = array(
			'custom_construct' => array( 'short_description' => __( '(Mixed) The data that was sent along with the HTTP call that was made to the receivable URL from within Lemon Squeezy.', 'wp-webhooks' ) ),
		);

		$description = array(
			'steps' => array(
				__( 'Within the settings of this webhook trigger, copy the receivable URL.', 'wp-webhooks' ),
				__( 'Head into Lemon Squeezy and go to Settings > Webhooks', 'wp-webhooks' ),
				__( 'Place the receivable URL there and send data based on your requirements.', 'wp-webhooks' ),
			),
			'tipps' => array(
				__( 'To receive data on the receivable URL, please use the "Webhooks by Lemon Squeezy" app within Lemon Squeezy.', 'wp-webhooks' ),
				__( 'The receivable URL accepts content types such as JSON, form data, or XML.', 'wp-webhooks' ),
			)
		);

		$settings = array(
			'load_default_settings' => false,
			'data' => array(
				'wpwhpro_lemon_squeezy_return_full_request' => array(
					'id'		  => 'wpwhpro_lemon_squeezy_return_full_request',
					'type'		=> 'checkbox',
					'label'	   => __( 'Send full request', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'Send the full, validated request instead of the payload (body) data only. This gives you access to header, cookies, response type and much more.', 'wp-webhooks' )
				),
			)
		);

		return array(
			'trigger'		   => 'lemon_webhook_received',
			'name'			  => __( 'Webhook request received', 'wp-webhooks' ),
			'sentence'			  => __( 'a webhook request was received', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => sprintf( __( 'This webhook fires as soon as a webhook request request was received from Lemon Squeezy.', 'wp-webhooks' ), WPWHPRO()->settings->get_page_title() ),
			'description'	   => $description,
			'integration'	   => 'lemon-squeezy',
			'receivable_url'	=> true,
			'premium'		   => true,
		);

	}

	public function execute( $return_data, $response_body, $trigger_url_name ){

		if( $trigger_url_name !== null ){
			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'lemon_webhook_received', $trigger_url_name );
			if( ! empty( $webhooks ) ){
				$webhooks = array( $webhooks );
			} else {
				$return_data['msg'] = __( 'We could not locate a callable trigger URL.', 'wp-webhooks' );
				return $return_data;
			}
		} else {
			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'lemon_webhook_received' );
		}
		

		$payload = $response_body['content'];

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){
	  
				  if( $settings_name === 'wpwhpro_lemon_squeezy_return_full_request' && ! empty( $settings_data ) ){
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

		do_action( 'wpwhpro/webhooks/trigger_lemon_webhook_received', $return_data, $response_body, $trigger_url_name, $response_data_array );

		return $return_data;
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'custom_construct' => 'The data that was sent to the receivable data URL. Or the full request array.',
		);

		return $data;
	}

  }

endif; // End if class_exists check.