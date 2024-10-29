<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Triggers_edd_new_customer' ) ) :

 /**
  * Load the edd_new_customer trigger
  *
  * @since 4.2.0
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_edd_Triggers_edd_new_customer {

	public function is_active(){

		$is_active = true;

		//Backwards compatibility
		if( defined( 'WPWH_EDD_NAME' ) ){
			$is_active = false;
		}

		return $is_active;
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
				'hook' => 'edd_customer_post_create',
				'callback' => array( $this, 'wpwh_trigger_edd_new_customer' ),
				'priority' => 10,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'first_name' => array( 'short_description' => __( '(String) The first name of the customer.', 'wp-webhooks' ) ),
			'last_name' => array( 'short_description' => __( '(String) The last name of the customer.', 'wp-webhooks' ) ),
			'id' => array( 'short_description' => __( '(String) The unique id of the customer. (This is not the user id)', 'wp-webhooks' ) ),
			'purchase_count' => array( 'short_description' => __( '(Integer) The number of purchases of the customer.', 'wp-webhooks' ) ),
			'purchase_value' => array( 'short_description' => __( '(Float) The value of all purchases of the customer.', 'wp-webhooks' ) ),
			'email' => array( 'short_description' => __( '(String) The main email of the customer.', 'wp-webhooks' ) ),
			'emails' => array( 'short_description' => __( '(Array) Additional emails of the customer.', 'wp-webhooks' ) ),
			'name' => array( 'short_description' => __( '(String) The full name of the customer.', 'wp-webhooks' ) ),
			'date_created' => array( 'short_description' => __( '(String) The date and time of the user creation in SQL format.', 'wp-webhooks' ) ),
			'payment_ids' => array( 'short_description' => __( '(String) A comme-separated list of payment ids.', 'wp-webhooks' ) ),
			'user_id' => array( 'short_description' => __( '(String) The user id of the customer.', 'wp-webhooks' ) ),
			'notes' => array( 'short_description' => __( '(String) Additional ntes given by the customer.', 'wp-webhooks' ) ),
		);

		$settings = array();

		return array(
			'trigger'		   => 'edd_new_customer',
			'name'			  => __( 'Customer created', 'wp-webhooks' ),
			'sentence'			  => __( 'a new customer was created', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a new customer is created within Easy Digital Downloads.', 'wp-webhooks' ),
			'description'	   => array(),
			'callback'		  => 'test_edd_new_customer',
			'integration'	   => 'edd',
		);

	}

	/**
	 * Triggers once a new EDD customer was created
	 *
	 * @param  integer $customer_id   Customer ID.
	 * @param  array   $args		  Customer data.
	 */
	public function wpwh_trigger_edd_new_customer( $customer_id = 0, $args = array() ){
		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'edd_new_customer' );

		if( ! class_exists( 'EDD_Customer' ) ){
			return;
		}
		
		$customer = new EDD_Customer( $customer_id );

		//Properly calculate names as given by the Zapier extension
		$first_name = '';
		$last_name = '';
		if( isset( $customer->name ) ){
			$separated_names = explode( ' ', $customer->name );

			$first_name = ( ! empty( $separated_names[0] ) ) ? $separated_names[0] : '';

			if( ! empty( $separated_names[1] ) ) {
				unset( $separated_names[0] );
				$last_name = implode( ' ', $separated_names );
			}
		}
		$customer->first_name = $first_name;
		$customer->last_name  = $last_name;

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

			if( $webhook_url_name !== null ){
				$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $customer );
			} else {
				$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $customer );
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_edd_new_customer', $customer_id, $customer, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array(
			'user_id'		=> 1234,
			'name'		   => 'John Doe',
			'first_name'	 => 'John',
			'last_name'	  => 'Doe',
			'email'		  => 'johndoe123@test.com',
			'payment_ids'	=> 2345,
			'purchase_value' => '23.5',
			'date_created'   => date( 'Y-m-d h:i:s' ),
			'purchase_count' => 1,
			'notes'		  => null,
		);

		return $data;
	}

  }

endif; // End if class_exists check.