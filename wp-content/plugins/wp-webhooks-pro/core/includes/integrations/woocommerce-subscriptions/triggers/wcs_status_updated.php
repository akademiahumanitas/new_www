<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_subscriptions_Triggers_wcs_status_updated' ) ) :

 /**
  * Load the wcs_status_updated trigger
  *
  * @since 5.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_woocommerce_subscriptions_Triggers_wcs_status_updated {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'woocommerce_subscription_status_updated',
				'callback' => array( $this, 'wcs_status_updated_callback' ),
				'priority' => 20,
				'arguments' => 3,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'subscription_id' => array( 'short_description' => __( '(Integer) The ID of the updated subscription.', 'wp-webhooks' ) ),
			'user_id' => array( 'short_description' => __( '(Integer)The ID of the user who belongs to the subscription. ', 'wp-webhooks' ) ),
			'new_status' => array( 'short_description' => __( '(String) The new subscripiton status.', 'wp-webhooks' ) ),
			'old_status' => array( 'short_description' => __( '(String) The old subscripiton status.', 'wp-webhooks' ) ),
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
				'wpwhpro_woocommerce_trigger_on_statuses' => array(
					'id'		  => 'wpwhpro_woocommerce_trigger_on_statuses',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'helpers',
						'args'		=> array(
							'integration' => 'woocommerce-subscriptions',
							'helper' => 'wcs_helpers',
							'function' => 'get_query_statuses',
						)
					),
					'label'	   => __( 'Trigger on selected subscription statuses', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'Select only the subscription statuses you want to fire the trigger on. If none is selected, all are triggered.', 'wp-webhooks' )
				),
			)
		);

		return array(
			'trigger'		   => 'wcs_status_updated',
			'name'			  => __( 'Subscription status updated', 'wp-webhooks' ),
			'sentence'			  => __( 'a subscription status was updated', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a subscription status was updated within WooCommerce Subscriptions.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'woocommerce-subscriptions',
			'premium'		   => true,
		);

	}

	public function wcs_status_updated_callback( $subscription, $new_status, $old_status ){	

		$subscription_id = $subscription->get_id();
		$user_id = $subscription->get_user_id();
		$new_status_validated = ( strpos( $new_status, 'wc-' ) === false ) ? 'wc-' . $new_status : $new_status;
		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wcs_status_updated' );
		$wcs_helpers = WPWHPRO()->integrations->get_helper( 'woocommerce-subscriptions', 'wcs_helpers' );
		$payload = array(
			'subscription_id' => $subscription_id,
			'user_id' => $user_id,
			'new_status' => $new_status,
			'old_status' => $old_status,
			'subscription' => $wcs_helpers->get_subscription_array( $subscription ),
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

				if( $is_valid && isset( $webhook['settings']['wpwhpro_woocommerce_trigger_on_statuses'] ) && is_array( $webhook['settings']['wpwhpro_woocommerce_trigger_on_statuses'] ) ){
					if( ! in_array( $new_status_validated, $webhook['settings']['wpwhpro_woocommerce_trigger_on_statuses'] ) ){
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

		do_action( 'wpwhpro/webhooks/trigger_wcs_status_updated', $payload, $response_data_array );
	}
	
	public function get_demo( $options = array() ) {

		$data = array (
			'subscription_id' => 9298,
			'user_id' => 1,
			'new_status' => 'on-hold',
			'old_status' => 'active',
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