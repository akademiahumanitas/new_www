<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_subscriptions_Actions_wcs_get_subscription' ) ) :

	/**
	 * Load the wcs_get_subscription action
	 *
	 * @since 5.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_woocommerce_subscriptions_Actions_wcs_get_subscription {

		public function get_details(){

				$parameter = array(
				'subscription_id'		=> array( 
					'required' => true, 
					'label' => __( 'Subscription ID', 'wp-webhooks' ), 
					'short_description' => __( 'The ID of the subscription you want to get.', 'wp-webhooks' )
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired actions.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The subscription has been successfully returned.',
				'data' => 
				array (
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
				),
			);

			return array(
				'action'			=> 'wcs_get_subscription', //required
				'name'			   => __( 'Get subscription', 'wp-webhooks' ),
				'sentence'			   => __( 'get a subscribtion', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Get a subscriptions within WooCommerce Subscriptions.', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'woocommerce-subscriptions',
				'premium'	   	=> true,
			);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'subscription' => array(),
				)
			);

			$subscription_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'subscription_id' ) );

			if( empty( $subscription_id ) ){
				$return_args['msg'] = __( "Please set the subscription_id argument.", 'action-wcs_get_subscription-error' );
				return $return_args;
			}

			$wcs_helpers = WPWHPRO()->integrations->get_helper( 'woocommerce-subscriptions', 'wcs_helpers' );
			$subscription = $wcs_helpers->get_subscription_array( $subscription_id );
			
			if( ! empty( $subscription ) ){
				$return_args['success'] = true;
				$return_args['msg'] = __( "The subscription has been successfully returned.", 'action-wcs_get_subscription-success' );
				$return_args['data']['subscription'] = $subscription;
			} else {
				$return_args['msg'] = __( "An error occured while retrieving the subscription.", 'action-wcs_get_subscription-success' );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.