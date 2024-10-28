<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_formidable_forms_Triggers_formidable_stripe_payment_completed' ) ) :

 /**
  * Load the formidable_stripe_payment_completed trigger
  *
  * @since 4.2.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_formidable_forms_Triggers_formidable_stripe_payment_completed {

	public function is_active(){
		return function_exists( 'frm_strp_autoloader' );
	}

  /**
   * Register the actual functionality of the webhook
   *
   * @param mixed $response
   * @param string $action
   * @param string $response_ident_value
   * @param string $response_api_key
   * @return mixed The response data for the webhook caller
   */
	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'frm_payment_status_complete',
				'callback' => array( $this, 'frm_payment_status_complete_callback' ),
				'priority' => 20,
				'arguments' => 1,
				'delayed' => true,
			),
		);
	}

	/**
	 * Defines the details of the trigger
	 *
	 * @return array
	 */
	public function get_details(){

		$parameter = array(
			'entry_id' => array( 'short_description' => __( '(Integer) The ID of the entry.', 'wp-webhooks' ) ),
			'payment' => array( 'short_description' => __( '(Array) Further details about the payment.', 'wp-webhooks' ) ),
		);

		$settings = array(
			'load_default_settings' => true,
			'data' => array()
		);

		return array(
			'trigger'		   => 'formidable_stripe_payment_completed',
			'name'			  => __( 'Stripe Payment Completed', 'wp-webhooks' ),
			'sentence'			  => __( 'a Stripe payment was completed', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a Stripe payment was completed within Formidable Forms.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'formidable-forms',
			'premium'		   => false,
		);

	}

	public function frm_payment_status_complete_callback( $atts ){

		if( 
			! isset( $atts['payment'] ) 
			|| ! is_object( $atts['payment'] ) 
			|| ! isset( $atts['payment']->paysys )
			|| $atts['payment']->paysys !== 'stripe'
		){
			return;
		}

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'formidable_stripe_payment_completed' );
		$entry_id = isset( $atts['entry_id'] ) ? $atts['entry_id'] : 0;
		
		$payload = array(
			'entry_id' => $entry_id,
			'payment' => ( isset( $atts['payment'] ) ) ? $atts['payment'] : array(),
		);

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( $is_valid ){
				if( $webhook_url_name !== null ){
					$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				} else {
					$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				}
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_formidable_stripe_payment_completed', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			"entry_id" => "12",
			"payment" => [
				"id" => "5",
				"meta_value" => "",
				"receipt_id" => "pi_3LdI16Ga0zW4zxfk03K0phHx",
				"invoice_id" => "",
				"sub_id" => "",
				"item_id" => "12",
				"action_id" => "20",
				"amount" => "10.00",
				"status" => "complete",
				"begin_date" => "2022-09-01",
				"expire_date" => "0000-00-00",
				"paysys" => "stripe",
				"created_at" => "2022-09-01 18:21:32",
			],
		);

		return $data;
	}

  }

endif; // End if class_exists check.