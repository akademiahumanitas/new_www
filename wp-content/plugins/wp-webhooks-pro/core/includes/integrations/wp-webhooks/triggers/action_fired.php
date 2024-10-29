<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_Triggers_action_fired' ) ) :

 /**
  * Load the action_fired trigger
  *
  * @since 4.1.0
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_wp_webhooks_Triggers_action_fired {

  public function get_callbacks(){

   return array(
	array(
		'type' => 'action',
		'hook' => 'wpwhpro/webhooks/echo_action_data',
		'callback' => array( $this, 'add_webhook_actions_callback' ),
		'priority' => 20,
		'arguments' => 2,
		'delayed' => false,
	  ),
	);

  }

	public function get_details(){

	  $parameter = array(
		'action' => array( 'short_description' => __( 'The technical name (slug) of the action that was currently triggered.', 'wp-webhooks' ) ),
		'webhook_name' => array( 'short_description' => __( 'The webhook name of the webhook action URL that triggered this webhook.', 'wp-webhooks' ) ),
		'response_body' => array( 'short_description' => __( 'The data that was initially sent, within the payload, to the currently called action.', 'wp-webhooks' ) ),
		'webhook_response' => array( 'short_description' => __( 'The webhook response the action returned to the initial caller.', 'wp-webhooks' ) ),
	  );

	  	$settings = array(
		'load_default_settings' => true,
		'data' => array(
		  'wpwhpro_action_fired_trigger_on_actions' => array(
			'id'	 => 'wpwhpro_action_fired_trigger_on_actions',
			'type'	=> 'select',
			'multiple'  => true,
			'query'			=> array(
				'filter'	=> 'actions',
				'args'		=> array()
			),
			'label'	=> __( 'Trigger on selected actions', 'wp-webhooks' ),
			'placeholder' => '',
			'required'  => false,
			'description' => __( 'Select only the actions you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', 'wp-webhooks' )
		  ),
		)
	  );

	  return array(
		'trigger'	  => 'action_fired',
		'name'	   => __( 'Action fired', 'wp-webhooks' ),
		'sentence'	   => __( 'an action was fired', 'wp-webhooks' ),
		'parameter'	 => $parameter,
		'settings'	 => $settings,
		'returns_code'   => $this->get_demo( array() ),
		'short_description' => __( 'This webhook fires after a webhook action has been executed.', 'wp-webhooks' ),
		'description'	=> array(),
		'integration'	=> 'wp-webhooks',
		'premium'	=> false,
	  );

	}

	public function add_webhook_actions_callback( $action, $validated_data ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'action_fired' );
		$response_body = WPWHPRO()->http->get_current_request();
		$webhook = WPWHPRO()->webhook->get_current_webhook_action();

		$webhook_name = '';
		if( is_array( $webhook ) && isset( $webhook['webhook_name'] ) ){
			$webhook_name = sanitize_title( $webhook['webhook_name'] );
		}

		$data_array = array(
		'action' => $action,
		'webhook_name' => $webhook_name,
		'response_body' => $response_body,
		'webhook_response' => $validated_data,
		);
		$response_data = array();

	  foreach( $webhooks as $webhook ){

		$is_valid = true;

		if( isset( $webhook['settings'] ) ){
		  foreach( $webhook['settings'] as $settings_name => $settings_data ){

			if( $settings_name === 'wpwhpro_action_fired_trigger_on_actions' && ! empty( $settings_data ) ){
			  if( ! in_array( $action, $settings_data ) ){
				$is_valid = false;
			  }
			}

		  }
		}

		if( $is_valid ) {
		  $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

		  if( $webhook_url_name !== null ){
			$response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
		  } else {
			$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
		  }
		}
	  }

	  do_action( 'wpwhpro/webhooks/trigger_action_fired', $data_array, $response_data );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'action' => 'ironikus_test',
			'webhook_name' => 'nonceing',
			'response_body' => 
			array (
				'content_type' => 'application/x-www-form-urlencoded',
				'content' => 
				array (
				'action' => 'ironikus_test',
				'test_var' => 'test-value123',
				),
			),
			'webhook_response' => 
			array (
				'arguments' => 
				array (
				'success' => true,
				'msg' => 'Test value successfully filled.',
				'test_var' => 'test-value123',
				),
				'response_type' => 'json',
			),
		);

	  return $data;
	}

  }

endif; // End if class_exists check.