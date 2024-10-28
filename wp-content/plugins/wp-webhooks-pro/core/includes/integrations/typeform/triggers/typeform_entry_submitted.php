<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_typeform_Triggers_typeform_entry_submitted' ) ) :

 /**
  * Load the typeform_entry_submitted trigger
  *
  * @since 6.1.4
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_typeform_Triggers_typeform_entry_submitted {

	public function get_details(){

		$parameter = array(
			'event_id' => array( 'short_description' => __( '(String) The ID of the current event within Typeform.', 'wp-webhooks' ) ),
			'event_type' => array( 'short_description' => __( '(String) The type of the event. (It is always form_response)', 'wp-webhooks' ) ),
			'form_response' => array( 'short_description' => __( '(Array) The details about the submitted form.', 'wp-webhooks' ) ),
		);

		$description = array(
			'steps' => array(
				__( 'Within the settings of this webhook trigger, copy the receivable URL.', 'wp-webhooks' ),
				__( 'Head into Typeform and go to the Connect panel of your chosen Workspace and click on the Webhooks tab.', 'wp-webhooks' ),
				__( 'Place the receivable URL there and send data based on your requirements.', 'wp-webhooks' ),
				__( 'After you can click on View Deliveries and send a demo request.', 'wp-webhooks' ),
				__( 'By default, the webhook fires on all event types. To limit these types, you can set conditionals against the event_type key within the payload data.', 'wp-webhooks' ),
			),
			'tipps' => array(
				__( 'To learn more about the webhook setup, please follow this manual: <a title="Go to typeform" target="_blank" href="https://www.typeform.com/help/a/webhooks-360029573471/">https://www.typeform.com/help/a/webhooks-360029573471/</a>', 'wp-webhooks' ),
			)
		);

		$settings = array(
			'load_default_settings' => false,
			'data' => array(
				'wpwhpro_typeform_return_full_request' => array(
					'id'		  => 'wpwhpro_typeform_return_full_request',
					'type'		=> 'checkbox',
					'label'	   => __( 'Send full request', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'Send the full, validated request instead of the payload (body) data only. This gives you access to header, cookies, response type and much more.', 'wp-webhooks' )
				),
			)
		);

		return array(
			'trigger'		   => 'typeform_entry_submitted',
			'name'			  => __( 'Entry submitted', 'wp-webhooks' ),
			'sentence'			  => __( 'an entry is submitted', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => sprintf( __( 'This webhook fires as soon as an entry is submitted via Typeform.', 'wp-webhooks' ), WPWHPRO()->settings->get_page_title() ),
			'description'	   => $description,
			'integration'	   => 'typeform',
			'receivable_url'	=> true,
			'premium'		   => true,
		);

	}

	public function execute( $return_data, $response_body, $trigger_url_name ){

		if( $trigger_url_name !== null ){
			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'typeform_entry_submitted', $trigger_url_name );
			if( ! empty( $webhooks ) ){
				$webhooks = array( $webhooks );
			} else {
				$return_data['msg'] = __( 'We could not locate a callable trigger URL.', 'wp-webhooks' );
				return $return_data;
			}
		} else {
			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'typeform_entry_submitted' );
		}
		

		$payload = $response_body['content'];

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){
	  
				  if( $settings_name === 'wpwhpro_typeform_return_full_request' && ! empty( $settings_data ) ){
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

		do_action( 'wpwhpro/webhooks/trigger_typeform_entry_submitted', $return_data, $response_body, $trigger_url_name, $response_data_array );

		return $return_data;
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'event_id' => '01GZ1FANJ6N5HNZ605VYFHM48V',
			'event_type' => 'form_response',
			'form_response' => 
			array (
			  'form_id' => 'vb5VXmjl',
			  'token' => '01GZ1FANJ6N5HNZ605VYFHM48V',
			  'landed_at' => '2023-04-27T13:44:31Z',
			  'submitted_at' => '2023-04-27T13:44:31Z',
			  'definition' => 
			  array (
				'id' => 'vb5VXmjl',
				'title' => 'My typeform',
				'fields' => 
				array (
				  0 => 
				  array (
					'id' => '8fBrdJ5bd6gg',
					'ref' => '01GZ1ER1XVNPH5ZCPJY22ZD11N',
					'type' => 'short_text',
					'title' => 'Hello, what\'s your name?',
					'properties' => 
					array (
					),
				  ),
				  1 => 
				  array (
					'id' => 'WtoQGzRODeEf',
					'ref' => '01GZ1ER1YJX3VDQ72MQ3RW5465',
					'type' => 'multiple_choice',
					'title' => 'Nice to meet you, {{field:01GZ1ER1XVNPH5ZCPJY22ZD11N}}, how is your day going?',
					'properties' => 
					array (
					),
				  ),
				),
			  ),
			  'answers' => 
			  array (
				0 => 
				array (
				  'type' => 'text',
				  'text' => 'Lorem ipsum dolor',
				  'field' => 
				  array (
					'id' => '8fBrdJ5bd6gg',
					'type' => 'short_text',
					'ref' => '01GZ1ER1XVNPH5ZCPJY22ZD11N',
				  ),
				),
				1 => 
				array (
				  'type' => 'choice',
				  'choice' => 
				  array (
					'label' => 'Barcelona',
				  ),
				  'field' => 
				  array (
					'id' => 'WtoQGzRODeEf',
					'type' => 'multiple_choice',
					'ref' => '01GZ1ER1YJX3VDQ72MQ3RW5465',
				  ),
				),
			  ),
			  'ending' => 
			  array (
				'ref' => 'default_tys',
			  ),
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.