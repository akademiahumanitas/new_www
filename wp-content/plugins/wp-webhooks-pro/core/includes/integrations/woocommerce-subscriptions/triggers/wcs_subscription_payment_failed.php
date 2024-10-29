<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_subscriptions_Triggers_wcs_subscription_payment_failed' ) ) :

 /**
  * Load the wcs_subscription_payment_failed trigger
  *
  * @since 5.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_woocommerce_subscriptions_Triggers_wcs_subscription_payment_failed {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'woocommerce_order_status_failed',
				'callback' => array( $this, 'wcs_subscription_payment_failed_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'subscription_id' => array( 'short_description' => __( '(Integer) The ID of the subscription.', 'wp-webhooks' ) ),
			'user_id' => array( 'short_description' => __( '(Integer)The ID of the user who belongs to the subscription. ', 'wp-webhooks' ) ),
			'user' => array( 'short_description' => __( '(Array) Further details about the subsciption user. ', 'wp-webhooks' ) ),
			'subscription' => array( 'short_description' => __( '(Array) Further details about the subscripiton.', 'wp-webhooks' ) ),
			'order_id' => array( 'short_description' => __( '(Integer) The ID of the failed order.', 'wp-webhooks' ) ),
			'order' => array( 'short_description' => __( '(Array) Further data about the related order.', 'wp-webhooks' ) ),
			'parent_order_id' => array( 'short_description' => __( '(Integer) The parent order ID of the subscription.', 'wp-webhooks' ) ),
			'checkout_payment_url' => array( 'short_description' => __( '(String) The checkout payment URL.', 'wp-webhooks' ) ),
		);

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_woocommerce_trigger_on_sub_products' => array(
					'id'		  => 'wpwhpro_woocommerce_trigger_on_sub_products',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'posts',
						'args'		=> array(
							'post_type' => 'product',
							'tax_query' => array(
								array(
									'taxonomy' => 'product_type',
									'terms'    => array( 'subscription', 'variable-subscription' ),
									'field'    => 'slug',
									'operator' => 'IN',
								),
							)
						)
					),
					'label'	   => __( 'Trigger on selected subscription products', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'Select only the subscription products you want to fire the trigger on. If none is selected, all are triggered.', 'wp-webhooks' )
				),
			)
		);

		return array(
			'trigger'		   => 'wcs_subscription_payment_failed',
			'name'			  => __( 'Subscription payment failed', 'wp-webhooks' ),
			'sentence'			  => __( 'a subscription payment failed', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a subscription payment failed within WooCommerce Subscriptions.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'woocommerce-subscriptions',
			'premium'		   => true,
		);

	}

	public function wcs_subscription_payment_failed_callback( $order_id, $order ){	

		$subscriptions = wcs_get_subscriptions_for_order( $order, array( 'order_type' => 'any' ) );
	
		//bail if no subscriptions are given
		if ( empty( $subscriptions ) ) {
			return;
		}

		foreach ( $subscriptions as $ssub ) {
			$subscription = $ssub;
			break;
		}

		$order_array = null;

		if( class_exists( 'WP_REST_Request' ) ){
			$request = new WP_REST_Request( 'GET', '/wc/v3/orders/' . $order_id );

			$response = rest_do_request( $request );
			$server = rest_get_server();
			$order_array = $server->response_to_data( $response, false );
		}

		$subscription_id = $subscription->get_id();
		$user_id = $subscription->get_user_id();
		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wcs_subscription_payment_failed' );
		$last_order = $subscription->get_last_order( 'all', 'any' );
		$wcs_helpers = WPWHPRO()->integrations->get_helper( 'woocommerce-subscriptions', 'wcs_helpers' );
		$parent_order_id = $subscription->get_parent_id();
		$payload = array(
			'subscription_id' => $subscription_id,
			'user_id' => $user_id,
			'user' => get_user_by( 'id', $user_id ),
			'subscription' => $wcs_helpers->get_subscription_array( $subscription ),
			'order_id' => $order_id,
			'order' => $order_array,
			'parent_order_id' => $parent_order_id,
			'checkout_payment_url' => ( ! empty( $order ) ) ? $order->get_checkout_payment_url() : '',
		);

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){	

				if( $is_valid && isset( $webhook['settings']['wpwhpro_woocommerce_trigger_on_sub_products'] ) && ! empty( $webhook['settings']['wpwhpro_woocommerce_trigger_on_sub_products'] ) && is_array( $webhook['settings']['wpwhpro_woocommerce_trigger_on_sub_products'] ) ){
					$is_valid = false;

					foreach( $payload['subscription']['products'] as $product ){
						if( in_array( $product['product_id'], $webhook['settings']['wpwhpro_woocommerce_trigger_on_sub_products'] ) ){
							$is_valid = true;
							break;
						}
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

		do_action( 'wpwhpro/webhooks/trigger_wcs_subscription_payment_failed', $payload, $response_data_array );
	}
	
	public function get_demo( $options = array() ) {

		$data = array (
			'subscription_id' => 9298,
			'user_id' => 1,
			'user' => 
			array (
				'data' => 
				array (
				'ID' => '1',
				'user_login' => 'jondoe',
				'user_pass' => '$P$B4B1t8fCUMz4mAsSvdwFN8GWC7EbzY1',
				'user_nicename' => 'Jon Doe',
				'user_email' => 'jon@doe.test',
				'user_url' => '',
				'user_registered' => '2021-07-27 23:58:11',
				'user_activation_key' => '',
				'user_status' => '0',
				'display_name' => 'admin',
				'spam' => '0',
				'deleted' => '0',
				array (
				),
				),
				'ID' => 1,
				'caps' => 
				array (
				'subscriber' => true,
				),
				'cap_key' => 'wp_capabilities',
				'roles' => 
				array (
				0 => 'subscriber',
				),
				'allcaps' => 
				array (
				0 => 'read',
				),
				'filter' => NULL,
			),
			'subscription' => 
			array (
			  'subscription_id' => 9298,
			  'user_id' => 1,
			  'products' => 
			  array (
				0 => 
				array (
				  'id' => 59,
				  'name' => 'Demo Subscription',
				  'sku' => '',
				  'product_id' => 9285,
				  'variation_id' => 0,
				  'quantity' => 2,
				  'tax_class' => '',
				  'price' => '1',
				  'subtotal' => '2',
				  'subtotal_tax' => '0',
				  'total' => '2',
				  'total_tax' => '0',
				  'taxes' => 
				  array (
				  ),
				  'meta' => 
				  array (
				  ),
				),
			  ),
			  'billing_period' => 'month',
			  'billing_interval' => '1',
			  'trial_period' => 'month',
			  'date_created' => '2022-05-17 11:27:41',
			  'date_modified' => '2022-09-20 03:21:26',
			  'view_order_url' => 'https://yourdomain.test/view-subscription/9298/',
			  'is_download_permitted' => false,
			  'sign_up_fee' => 0,
			  'start_date' => '2022-05-17T11:27:41',
			  'trial_end' => '1970-01-01T00:00:00',
			  'next_payment' => '1970-01-01T00:00:00',
			  'end_date' => '2022-10-17T11:27:41',
			  'date_completed_gmt' => NULL,
			  'date_paid_gmt' => '2022-05-17T11:27:41',
			  'last_order_id' => 9297,
			  'renewal_order_ids' => 9324,
			),
			'order_id' => 9297,
			'order' => 
			array (
				'id' => 9324,
				'parent_id' => 0,
				'status' => 'failed',
				'currency' => 'EUR',
				'version' => '6.0.1',
				'prices_include_tax' => false,
				'date_created' => '2022-06-18T06:38:39',
				'date_modified' => '2022-10-18T13:31:41',
				'discount_total' => '0.00',
				'discount_tax' => '0.00',
				'shipping_total' => '0.00',
				'shipping_tax' => '0.00',
				'cart_tax' => '0.00',
				'total' => '2.00',
				'total_tax' => '0.00',
				'customer_id' => 1,
				'order_key' => 'wc_order_LOrDDBcQ0X769',
				'billing' => 
				array (
				'first_name' => 'test',
				'last_name' => 'test',
				'company' => '',
				'address_1' => 'test 123',
				'address_2' => '',
				'city' => 'test city',
				'state' => '',
				'postcode' => '12345',
				'country' => 'DE',
				'email' => 'test@test.test',
				'phone' => '1234567',
				),
				'shipping' => 
				array (
				'first_name' => '',
				'last_name' => '',
				'company' => '',
				'address_1' => '',
				'address_2' => '',
				'city' => '',
				'state' => '',
				'postcode' => '',
				'country' => '',
				'phone' => '',
				),
				'payment_method' => '',
				'payment_method_title' => '',
				'transaction_id' => '',
				'customer_ip_address' => '127.0.0.1',
				'customer_user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.83 Safari/537.36',
				'created_via' => 'subscription',
				'customer_note' => '',
				'date_completed' => NULL,
				'date_paid' => NULL,
				'cart_hash' => '',
				'number' => '9324',
				'meta_data' => 
				array (
				0 => 
				array (
					'id' => 77039,
					'key' => 'is_vat_exempt',
					'value' => 'no',
				),
				1 => 
				array (
					'id' => 77040,
					'key' => 'demo_field',
					'value' => '',
				),
				2 => 
				array (
					'id' => 77041,
					'key' => '_demo_field',
					'value' => 'field_6225afd2de64a',
				),
				3 => 
				array (
					'id' => 77049,
					'key' => '_subscription_renewal',
					'value' => '9298',
				),
				),
				'line_items' => 
				array (
				0 => 
				array (
					'id' => 64,
					'name' => 'Demo Subscription',
					'product_id' => 9285,
					'variation_id' => 0,
					'quantity' => 2,
					'tax_class' => '',
					'subtotal' => '2.00',
					'subtotal_tax' => '0.00',
					'total' => '2.00',
					'total_tax' => '0.00',
					'taxes' => 
					array (
					),
					'meta_data' => 
					array (
					0 => 
					array (
						'id' => 572,
						'key' => '_has_trial',
						'value' => 'true',
						'display_key' => '_has_trial',
						'display_value' => 'true',
					),
					),
					'sku' => '',
					'price' => 1,
					'parent_name' => NULL,
				),
				),
				'tax_lines' => 
				array (
				),
				'shipping_lines' => 
				array (
				),
				'fee_lines' => 
				array (
				),
				'coupon_lines' => 
				array (
				),
				'refunds' => 
				array (
				),
				'date_created_gmt' => '2022-06-18T06:38:39',
				'date_modified_gmt' => '2022-10-18T13:31:41',
				'date_completed_gmt' => NULL,
				'date_paid_gmt' => NULL,
				'currency_symbol' => '€',
				'_links' => 
				array (
				'self' => 
				array (
					0 => 
					array (
					'href' => 'https://demodomain.test/wp-json/wc/v3/orders/9324',
					),
				),
				'collection' => 
				array (
					0 => 
					array (
					'href' => 'https://demodomain.test/wp-json/wc/v3/orders',
					),
				),
				'customer' => 
				array (
					0 => 
					array (
					'href' => 'https://demodomain.test/wp-json/wc/v3/customers/1',
					),
				),
				),
			),
			'parent_order_id' => 85364,
			'checkout_payment_url' => 'https://yourdomain.test/order-pay/9297/?pay_for_order=true&key=wc_order_dchD22UsQGFVb',
		);

		return $data;
	}

  }

endif; // End if class_exists check.