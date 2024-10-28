<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_subscriptions_Triggers_wcs_sub_payment_completed' ) ) :

 /**
  * Load the wcs_sub_payment_completed trigger
  *
  * @since 5.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_woocommerce_subscriptions_Triggers_wcs_sub_payment_completed {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'woocommerce_subscription_payment_complete',
				'callback' => array( $this, 'wcs_sub_payment_completed_callback' ),
				'priority' => 20,
				'arguments' => 1,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'subscription_id' => array( 'short_description' => __( '(Integer) The ID of the subscription.', 'wp-webhooks' ) ),
			'user_id' => array( 'short_description' => __( '(Integer)The ID of the user who belongs to the subscription. ', 'wp-webhooks' ) ),
			'subscription' => array( 'short_description' => __( '(Array) Further details about the subscripiton.', 'wp-webhooks' ) ),
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
				'wpwhpro_woocommerce_trigger_on_renewal' => array(
					'id'		  => 'wpwhpro_woocommerce_trigger_on_renewal',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(
						'first' => array( 'label' => __( 'First order', 'wp-webhooks' ) ),
						'renewal' => array( 'label' => __( 'Renewal orders', 'wp-webhooks' ) ),
					),
					'label'	   => __( 'Trigger on first or renewal order ', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'Select only the type of completion you want to fire the trigger on. If none is selected, all are triggered.', 'wp-webhooks' )
				),
			)
		);

		return array(
			'trigger'		   => 'wcs_sub_payment_completed',
			'name'			  => __( 'Subscription payment completed', 'wp-webhooks' ),
			'sentence'			  => __( 'a subscription payment was completed', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a subscription payment was completed within WooCommerce Subscriptions.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'woocommerce-subscriptions',
			'premium'		   => true,
		);

	}

	public function wcs_sub_payment_completed_callback( $subscription ){	

		$subscription_id = $subscription->get_id();
		$user_id = $subscription->get_user_id();
		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wcs_sub_payment_completed' );
		$last_order = $subscription->get_last_order( 'all', 'any' );
		$is_renewal = ( false !== $last_order && wcs_order_contains_renewal( $last_order ) ) ? true : false;
		$wcs_helpers = WPWHPRO()->integrations->get_helper( 'woocommerce-subscriptions', 'wcs_helpers' );
		$payload = array(
			'subscription_id' => $subscription_id,
			'user_id' => $user_id,
			'subscription' => $wcs_helpers->get_subscription_array( $subscription ),
		);

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){

				if( $is_valid && isset( $webhook['settings']['wpwhpro_woocommerce_trigger_on_renewal'] ) && is_array( $webhook['settings']['wpwhpro_woocommerce_trigger_on_renewal'] ) ){
					$is_valid = false;

					foreach( $webhook['settings']['wpwhpro_woocommerce_trigger_on_renewal'] as $otype ){

						if( $otype === 'first' && ! $is_renewal ){
							$is_valid = true;
						} elseif( $otype === 'renewal' && $is_renewal ){
							$is_valid = true;
						}

					}
					
				}	

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

		do_action( 'wpwhpro/webhooks/trigger_wcs_sub_payment_completed', $payload, $response_data_array );
	}
	
	public function get_demo( $options = array() ) {

		$data = array (
			'subscription_id' => 9298,
			'user_id' => 1,
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
			  'date_modified' => '2022-05-23 06:17:43',
			  'view_order_url' => 'https://yourdomain.test/view-subscription/9298/',
			  'is_download_permitted' => false,
			  'sign_up_fee' => 0,
			  'start_date' => '2022-05-17T11:27:41',
			  'trial_end' => '2022-06-17T11:27:41',
			  'next_payment' => '2022-06-17T11:27:41',
			  'end_date' => '1970-01-01T00:00:00',
			  'date_completed_gmt' => NULL,
			  'date_paid_gmt' => '2022-05-17T11:27:41',
			  'last_order_id' => 9297,
			  'renewal_order_ids' => false,
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.