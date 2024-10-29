<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_givewp_Triggers_give_donation_created' ) ) :

 /**
  * Load the give_donation_created trigger
  *
  * @since 6.1.1
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_givewp_Triggers_give_donation_created {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'give_insert_payment',
				'callback' => array( $this, 'give_donation_created_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'payment_id' => array( 'short_description' => __( '(Integer) The ID of the current donation.', 'wp-webhooks' ) ),
			'form_id' => array( 'short_description' => __( '(Integer) The ID of the form that was used to make the donation.', 'wp-webhooks' ) ),
			'payment_data' => array( 'short_description' => __( '(Array) All related data to the donation itself.', 'wp-webhooks' ) ),
		);

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_givewp_trigger_on_selected_forms' => array(
					'id'		  => 'wpwhpro_givewp_trigger_on_selected_forms',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'posts',
						'args'		=> array(
							'post_type' => 'give_forms'
						)
					),
					'label'	   => __( 'Trigger on selected forms', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'Select only the GiveWP forms you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', 'wp-webhooks' )
				),
			)
		);

		return array(
			'trigger'		   => 'give_donation_created',
			'name'			  => __( 'Donation created', 'wp-webhooks' ),
			'sentence'			  => __( 'a donation was created', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a donation was created within GiveWP.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'givewp',
			'premium'		   => true,
		);

	}

	public function give_donation_created_callback( $payment_id, $payment_data ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'give_donation_created' );
		$response_data_array = array();
		$payment = new Give_Payment( $payment_id );

		$form_id = isset( $payment_data['give_form_id'] ) ? $payment_data['give_form_id'] : 0;

		$payload = array(
			'payment_id' => $payment_id,
			'form_id' => $form_id,
			'payment_data' => $payment_data,
		);

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){

				if( isset( $webhook['settings']['wpwhpro_givewp_trigger_on_selected_forms'] ) && ! empty( $webhook['settings']['wpwhpro_givewp_trigger_on_selected_forms'] ) ){
					if( ! in_array( $form_id, $webhook['settings']['wpwhpro_givewp_trigger_on_selected_forms'] ) ){
						$is_valid = false;
					}
				}

			}

			if( $is_valid ){
				if( $webhook_url_name !== null ){
					$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				} else {
					$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				}
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_give_donation_created', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'payment_id' => 9592,
			'payment_data' => 
			array (
			  'price' => 100,
			  'give_form_title' => 'Donation Form',
			  'give_form_id' => 9063,
			  'give_price_id' => '3',
			  'date' => '2022-12-15 08:48:31',
			  'user_email' => 'admin@zipfme.dev',
			  'purchase_key' => 'b21d4bd53e390712548ee7afbbbcf42d',
			  'currency' => 'USD',
			  'user_info' => 
			  array (
				'id' => 1,
				'title' => '',
				'email' => 'admin@zipfme.dev',
				'first_name' => 'Jannis',
				'last_name' => 'Thümmig',
				'address' => false,
				'donor_id' => '1',
			  ),
			  'status' => 'pending',
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.