<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_lemon_squeezy_Triggers_lemon_subscription_paused' ) ) :

 /**
  * Load the lemon_subscription_paused trigger
  *
  * @since 6.1.5
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_lemon_squeezy_Triggers_lemon_subscription_paused {

	public function get_details(){

		$parameter = array(
			'custom_construct' => array( 'short_description' => __( '(Mixed) The data that was sent along with the HTTP call that was made to the receivable URL from within Lemon Squeezy.', 'wp-webhooks' ) ),
		);

		$description = array(
			'steps' => array(
				__( 'Within the settings of this webhook trigger, copy the receivable URL.', 'wp-webhooks' ),
				__( 'Head into Lemon Squeezy and go to Settings > Webhooks', 'wp-webhooks' ),
				__( 'Place the receivable URL there and select the subscription_paused webhook event.', 'wp-webhooks' ),
				__( 'Save the webhook and give it a try.', 'wp-webhooks' ),
			),
			'tipps' => array(
				__( 'To receive data on the receivable URL, you must add the receivable URL within the Lemon Squeezy webhooks.', 'wp-webhooks' ),
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
			'trigger'		   => 'lemon_subscription_paused',
			'name'			  => __( 'Subscription paused', 'wp-webhooks' ),
			'sentence'			  => __( 'a subscription is paused', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => sprintf( __( 'This webhook fires as soon as a subscription is paused within Lemon Squeezy.', 'wp-webhooks' ), WPWHPRO()->settings->get_page_title() ),
			'description'	   => $description,
			'integration'	   => 'lemon-squeezy',
			'receivable_url'	=> true,
			'premium'		   => true,
		);

	}

	public function execute( $return_data, $response_body, $trigger_url_name ){

		if( $trigger_url_name !== null ){
			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'lemon_subscription_paused', $trigger_url_name );
			if( ! empty( $webhooks ) ){
				$webhooks = array( $webhooks );
			} else {
				$return_data['msg'] = __( 'We could not locate a callable trigger URL.', 'wp-webhooks' );
				return $return_data;
			}
		} else {
			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'lemon_subscription_paused' );
		}

		$payload = $response_body['content'];

		$meta = WPWHPRO()->helpers->validate_request_value( $payload, 'meta' );

		$event_name = '';
		if( WPWHPRO()->helpers->is_json( $meta ) ){
			$meta_array = json_decode( $meta, true );
			if( is_array( $meta_array ) && isset( $meta_array['event_name'] ) ){
				$event_name = $meta_array['event_name'];
			}
		}

		if( $event_name !== 'subscription_paused' ){
			$return_data['msg'] = __( 'The incoming webhook did not call the correct event.', 'wp-webhooks' );
			return $return_data;
		}

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

		do_action( 'wpwhpro/webhooks/trigger_lemon_subscription_paused', $return_data, $response_body, $trigger_url_name, $response_data_array );

		return $return_data;
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'meta' => 
			array (
			  'test_mode' => true,
			  'event_name' => 'subscription_paused',
			),
			'data' => 
			array (
			  'type' => 'subscriptions',
			  'id' => '123456',
			  'attributes' => 
			  array (
				'store_id' => 123456,
				'customer_id' => 1234567,
				'order_id' => 123456,
				'order_item_id' => 123456,
				'product_id' => 123456,
				'variant_id' => 123456,
				'product_name' => 'Demo Product',
				'variant_name' => 'Default',
				'user_name' => 'Jon Doe',
				'user_email' => 'jondoe@demodomain.test',
				'status' => 'active',
				'status_formatted' => 'Active',
				'card_brand' => 'visa',
				'card_last_four' => '4242',
				'pause' => NULL,
				'paused' => false,
				'trial_ends_at' => NULL,
				'billing_anchor' => 2,
				'urls' => 
				array (
				  'update_payment_method' => 'https://ironikus.lemonsqueezy.com/subscription/123456/payment-details?expires=1683126151&signature=xxxxx',
				),
				'renews_at' => '2023-06-02T15:02:25.000000Z',
				'ends_at' => NULL,
				'paused_at' => '2023-05-02T15:02:27.000000Z',
				'paused_at' => '2023-05-02T15:02:30.000000Z',
				'test_mode' => true,
			  ),
			  'relationships' => 
			  array (
				'store' => 
				array (
				  'links' => 
				  array (
					'related' => 'https://api.lemonsqueezy.com/v1/subscriptions/123456/store',
					'self' => 'https://api.lemonsqueezy.com/v1/subscriptions/123456/relationships/store',
				  ),
				),
				'customer' => 
				array (
				  'links' => 
				  array (
					'related' => 'https://api.lemonsqueezy.com/v1/subscriptions/123456/customer',
					'self' => 'https://api.lemonsqueezy.com/v1/subscriptions/123456/relationships/customer',
				  ),
				),
				'order' => 
				array (
				  'links' => 
				  array (
					'related' => 'https://api.lemonsqueezy.com/v1/subscriptions/123456/order',
					'self' => 'https://api.lemonsqueezy.com/v1/subscriptions/123456/relationships/order',
				  ),
				),
				'order-item' => 
				array (
				  'links' => 
				  array (
					'related' => 'https://api.lemonsqueezy.com/v1/subscriptions/123456/order-item',
					'self' => 'https://api.lemonsqueezy.com/v1/subscriptions/123456/relationships/order-item',
				  ),
				),
				'product' => 
				array (
				  'links' => 
				  array (
					'related' => 'https://api.lemonsqueezy.com/v1/subscriptions/123456/product',
					'self' => 'https://api.lemonsqueezy.com/v1/subscriptions/123456/relationships/product',
				  ),
				),
				'variant' => 
				array (
				  'links' => 
				  array (
					'related' => 'https://api.lemonsqueezy.com/v1/subscriptions/123456/variant',
					'self' => 'https://api.lemonsqueezy.com/v1/subscriptions/123456/relationships/variant',
				  ),
				),
				'subscription-invoices' => 
				array (
				  'links' => 
				  array (
					'related' => 'https://api.lemonsqueezy.com/v1/subscriptions/123456/subscription-invoices',
					'self' => 'https://api.lemonsqueezy.com/v1/subscriptions/123456/relationships/subscription-invoices',
				  ),
				),
			  ),
			  'links' => 
			  array (
				'self' => 'https://api.lemonsqueezy.com/v1/subscriptions/123456',
			  ),
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.