<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Triggers_edd_subscriptions' ) ) :

 /**
  * Load the edd_subscriptions trigger
  *
  * @since 4.2.0
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_edd_Triggers_edd_subscriptions {

	public function is_active(){

		$is_active = defined( 'EDD_RECURRING_PRODUCT_NAME' );

		//Backwards compatibility for the "Easy Digital Downloads" integration
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
				'hook' => 'edd_subscription_post_create',
				'callback' => array( $this, 'wpwh_trigger_edd_subscriptions_map_create' ),
				'priority' => 10,
				'arguments' => 2,
				'delayed' => false,
			),
			array(
				'type' => 'action',
				'hook' => 'edd_subscription_post_renew',
				'callback' => array( $this, 'wpwh_trigger_edd_subscriptions_map_renew' ),
				'priority' => 10,
				'arguments' => 3,
				'delayed' => false,
			),
			array(
				'type' => 'action',
				'hook' => 'edd_subscription_completed',
				'callback' => array( $this, 'wpwh_trigger_edd_subscriptions_map_completed' ),
				'priority' => 10,
				'arguments' => 2,
				'delayed' => false,
			),
			array(
				'type' => 'action',
				'hook' => 'edd_subscription_expired',
				'callback' => array( $this, 'wpwh_trigger_edd_subscriptions_map_expired' ),
				'priority' => 10,
				'arguments' => 2,
				'delayed' => false,
			),
			array(
				'type' => 'action',
				'hook' => 'edd_subscription_failing',
				'callback' => array( $this, 'wpwh_trigger_edd_subscriptions_map_failing' ),
				'priority' => 10,
				'arguments' => 2,
				'delayed' => false,
			),
			array(
				'type' => 'action',
				'hook' => 'edd_subscription_cancelled',
				'callback' => array( $this, 'wpwh_trigger_edd_subscriptions_map_cancelled' ),
				'priority' => 10,
				'arguments' => 2,
				'delayed' => false,
			),
		);
	}

	public function get_details(){

		$choices = apply_filters( 'wpwhpro/settings/edd_subscription_statuses', array(
			'create' => __( 'Created', 'wp-webhooks' ),
			'renew' => __( 'Renewed', 'wp-webhooks' ),
			'completed' => __( 'Completed', 'wp-webhooks' ),
			'expired' => __( 'Expired', 'wp-webhooks' ),
			'failing' => __( 'Failed', 'wp-webhooks' ),
			'cancelled' => __( 'Cancelled', 'wp-webhooks' ),
		) );

		$parameter = array(
			'id' => array( 'short_description' => __( '(String) The subscription id.', 'wp-webhooks' ) ),
			'customer_id' => array( 'short_description' => __( '(String) The id of the related customer.', 'wp-webhooks' ) ),
			'period' => array( 'short_description' => __( '(String) The subcription period.', 'wp-webhooks' ) ),
			'initial_amount' => array( 'short_description' => __( '(String) The initial price amount.', 'wp-webhooks' ) ),
			'initial_tax_rate' => array( 'short_description' => __( '(String) The initial tax rate.', 'wp-webhooks' ) ),
			'initial_tax' => array( 'short_description' => __( '(String) The initial tax amount.', 'wp-webhooks' ) ),
			'recurring_amount' => array( 'short_description' => __( '(String) The recurring price amount.', 'wp-webhooks' ) ),
			'recurring_tax_rate' => array( 'short_description' => __( '(String) The recurring tax rate.', 'wp-webhooks' ) ),
			'recurring_tax' => array( 'short_description' => __( '(String) The recurring tax amount.', 'wp-webhooks' ) ),
			'bill_times' => array( 'short_description' => __( '(String) The times the customer gets billed.', 'wp-webhooks' ) ),
			'transaction_id' => array( 'short_description' => __( '(String) The transaction id.', 'wp-webhooks' ) ),
			'parent_payment_id' => array( 'short_description' => __( '(String) The parent payment id in case the payment is recurring.', 'wp-webhooks' ) ),
			'product_id' => array( 'short_description' => __( '(String) The related product id for this subscription.', 'wp-webhooks' ) ),
			'price_id' => array( 'short_description' => __( '(String) The price id in case it is a variation.', 'wp-webhooks' ) ),
			'created' => array( 'short_description' => __( '(String) The date and time of creation (in SQL format).', 'wp-webhooks' ) ),
			'expiration' => array( 'short_description' => __( '(String) The date and time of expiration (in SQL format).', 'wp-webhooks' ) ),
			'trial_period' => array( 'short_description' => __( '(String) The trial period.', 'wp-webhooks' ) ),
			'status' => array( 'short_description' => __( '(String) The current subscription status.', 'wp-webhooks' ) ),
			'profile_id' => array( 'short_description' => __( '(String) The unique profile id.', 'wp-webhooks' ) ),
			'gateway' => array( 'short_description' => __( '(String) The chosen gateway for this subscription.', 'wp-webhooks' ) ),
			'customer' => array( 'short_description' => __( '(Array) An array with all of the customer information. Please see the example down below for further details.', 'wp-webhooks' ) ),
			'notes' => array( 'short_description' => __( '(Array) An array with all the subscription notes.', 'wp-webhooks' ) ),
		);

		$settings = array(
			'data' => array(
				'wpwhpro_trigger_edd_subscriptions_whitelist_status' => array(
					'id'		  => 'wpwhpro_trigger_edd_subscriptions_whitelist_status',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => $choices,
					'label'	   => __( 'Trigger on selected subscription status changes', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'Select only the subscription statuses you want to fire the trigger on. You can choose multiple ones. If none is selected, all are triggered.', 'wp-webhooks' )
				),
			)
		);

		return array(
			'trigger'		   => 'edd_subscriptions',
			'name'			  => __( 'Subscription status changed', 'wp-webhooks' ),
			'sentence'			  => __( 'a subscription status has changed', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires on certain status changes of subscriptions within Easy Digital Downloads.', 'wp-webhooks' ),
			'description'	   => array(),
			'callback'		  => 'test_edd_subscriptions',
			'integration'	   => 'edd',
		);

	}

	public function wpwh_trigger_edd_subscriptions_map_create( $subscription_id = 0, $args = array() ) {

		if( ! class_exists( 'EDD_Subscription' ) ) {
			return;
		}
		$subscription = new EDD_Subscription( $subscription_id );

		$this->wpwh_trigger_edd_subscriptions_init( $subscription, 'create' );
	}

	public function wpwh_trigger_edd_subscriptions_map_renew( $sub_id = 0, $expiration = '', EDD_Subscription $subscription ) {
		if( ! class_exists( 'EDD_Subscription' ) ) {
			return;
		}
		$this->wpwh_trigger_edd_subscriptions_init( $subscription, 'renew' );
	}

	public function wpwh_trigger_edd_subscriptions_map_completed( $sub_id = 0, EDD_Subscription $subscription ) {
		if( ! class_exists( 'EDD_Subscription' ) ) {
			return;
		}
		$this->wpwh_trigger_edd_subscriptions_init( $subscription, 'completed' );
	}

	public function wpwh_trigger_edd_subscriptions_map_expired( $sub_id = 0, EDD_Subscription $subscription ) {
		if( ! class_exists( 'EDD_Subscription' ) ) {
			return;
		}
		$this->wpwh_trigger_edd_subscriptions_init( $subscription, 'expired' );
	}

	public function wpwh_trigger_edd_subscriptions_map_failing( $sub_id = 0, EDD_Subscription $subscription ) {
		if( ! class_exists( 'EDD_Subscription' ) ) {
			return;
		}
		$this->wpwh_trigger_edd_subscriptions_init( $subscription, 'failing' );
	}

	public function wpwh_trigger_edd_subscriptions_map_cancelled( $sub_id = 0, EDD_Subscription $subscription ) {
		if( ! class_exists( 'EDD_Subscription' ) ) {
			return;
		}
		$this->wpwh_trigger_edd_subscriptions_init( $subscription, 'cancelled' );
	}

	/*
	* Register the edd payments post delay trigger logic
	*/
	public function wpwh_trigger_edd_subscriptions_init(){
		WPWHPRO()->delay->add_post_delayed_trigger( array( $this, 'wpwh_trigger_edd_subscriptions' ), func_get_args() );
	}

	/**
	 * Triggers once a new EDD payment was changed
	 *
	 * @param  integer $customer_id   Customer ID.
	 * @param  array   $args		  Customer data.
	 */
	public function wpwh_trigger_edd_subscriptions( $subscription, $status ){
		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'edd_subscriptions' );
		$response_data_array = array();

		

		foreach( $webhooks as $webhook ){

			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){

					if( $settings_name === 'wpwhpro_trigger_edd_subscriptions_whitelist_status' && ! empty( $settings_data ) ){
						if( ! in_array( $status, $settings_data ) ){
							$is_valid = false;
						}
					}

				}
			}

			if( $is_valid ) {

				$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

				if( $webhook_url_name !== null ){
					$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $subscription );
				} else {
					$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $subscription );
				}

				do_action( 'wpwhpro/webhooks/trigger_edd_subscriptions', $subscription, $status, $response_data_array );
			}
			
		}
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'id' => '1',
			'customer_id' => '1',
			'period' => 'year',
			'initial_amount' => '9.97',
			'initial_tax_rate' => '',
			'initial_tax' => '',
			'recurring_amount' => '9.97',
			'recurring_tax_rate' => '',
			'recurring_tax' => '',
			'bill_times' => '2',
			'transaction_id' => '',
			'parent_payment_id' => '706',
			'product_id' => '285',
			'price_id' => '0',
			'created' => '2020-04-23 16:29:36',
			'expiration' => '2020-04-22 23:59:59',
			'trial_period' => '',
			'status' => 'completed',
			'profile_id' => 'xxxxxxxx',
			'gateway' => 'manual',
			'customer' => 
			array (
			  'id' => '1',
			  'purchase_count' => 2,
			  'purchase_value' => 87.97,
			  'email' => 'johndoe123@test.com',
			  'emails' => 
			  array (
				0 => 'johndoe123more@test.com',
			  ),
			  'name' => 'John Doe',
			  'date_created' => '2019-02-26 07:32:56',
			  'payment_ids' => '695,706',
			  'user_id' => '1',
			),
			'notes' => 
			array (
			  'April 23, 2020 16:32:05 - Status changed from completed to failing by admin',
			  'April 23, 2020 16:30:59 - Status changed from active to completed by admin',
			  'April 23, 2020 16:30:45 - Status changed from expired to active by admin',
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.