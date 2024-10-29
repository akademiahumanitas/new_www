<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_studiocart_Triggers_sc_subscription_cancelled' ) ) :

	/**
	 * Load the sc_subscription_cancelled trigger
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_studiocart_Triggers_sc_subscription_cancelled {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'sc_subscription_canceled',
					'callback'  => array( $this, 'wpwh_trigger_sc_subscription_cancelled' ),
					'priority'  => 10,
					'arguments' => 3,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {

				$parameter = array(
				'status'     => array( 'short_description' => __( '(String) The status of the related order.', 'wp-webhooks' ) ),
				'order_type' => array( 'short_description' => __( '(String) The type of the subscription.', 'wp-webhooks' ) ),
				'order_info' => array( 'short_description' => __( '(Array) Further information about the order and the subscription.', 'wp-webhooks' ) ),
			);

			$settings = array(
				'load_default_settings' => true,
				'data'                  => array(
					'wpwhpro_sc_order_completed_trigger_on_products' => array(
						'id'          => 'wpwhpro_sc_order_completed_trigger_on_products',
						'type'        => 'select',
						'multiple'    => true,
						'choices'     => array(),
						'query'			=> array(
							'filter'	=> 'posts',
							'args'		=> array(
								'post_type' => 'sc_product'
							)
						),
						'label'       => __( 'Trigger on products', 'wp-webhooks' ),
						'placeholder' => '',
						'required'    => false,
						'description' => __( 'Select only the products you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', 'wp-webhooks' )
					),
				)
			);

			return array(
				'trigger'           => 'sc_subscription_cancelled',
				'name'              => __( 'Subscriptions cancelled', 'wp-webhooks' ),
				'sentence'          => __( 'a subscription was cancelled', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires after a subscription was cancelled within Studiocart.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'studiocart',
			);

		}

		/**
		 * Triggers once User's subscription is cancelled
		 *
		 * @param bool $canceled Status of cancelation
		 * @param array $sub Object type
		 * @param string $sub_id Subscription id
		 * @param bool $now
		 */
		public function wpwh_trigger_sc_subscription_cancelled( $status, $order_info, $order_type ) {
			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'sc_subscription_cancelled' );

			$payload = array(
				'status'     => $status,
				'order_type' => $order_type,
				'order_info' => $order_info,
			);

			foreach ( $webhooks as $webhook ) {

				$webhook_url_name = ( is_array( $webhook ) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
				$is_valid         = true;

				if ( isset( $webhook['settings'] ) ) {
					foreach ( $webhook['settings'] as $settings_name => $settings_data ) {

						if ( $settings_name === 'wpwhpro_sc_order_completed_trigger_on_products' && ! empty( $settings_data ) ) {
							$is_valid = false;

							if ( isset( $order_info['product_id'] ) && in_array( $order_info['product_id'], $settings_data ) ) {
								$is_valid = true;
							}
						}

					}
				}
				if ( $is_valid ) {
					if ( $webhook_url_name !== null ) {
						$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					} else {
						$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					}
				}
			}

			do_action( 'wpwhpro/webhooks/trigger_sc_subscription_cancelled', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array (
				'status' => 'canceled',
				'order_type' => 'main',
				'order_info' => 
				array (
				  'ID' => 7,
				  'id' => 7,
				  'subscription_id' => 'sub_1LhiEfGa0zW4zxfk5p3XUYsg',
				  'status' => 'canceled',
				  'sub_status' => 'canceled',
				  'first_order' => '8',
				  'order_log' => 
				  array (
					'1663110590 - sc1308981656' => 'Subscription status updated to pending-payment',
					'1663110593 - sc812493240' => 'Subscription status updated to active',
					'1663110924 - sc518950512' => 'Subscription status updated to canceled',
				  ),
				  'amount' => '10',
				  'tax_amount' => 0,
				  'sub_amount' => '10',
				  'sub_discount' => 0,
				  'sub_discount_duration' => NULL,
				  'sub_item_name' => '10 monthly',
				  'sub_installments' => '-1',
				  'sub_interval' => 'month',
				  'sub_frequency' => '1',
				  'sub_next_bill_date' => '1665702585',
				  'cancel_at' => NULL,
				  'free_trial_days' => 0,
				  'sign_up_fee' => 0,
				  'order_bump_subs' => NULL,
				  'main_product_sub' => NULL,
				  'cancel_date' => '2022-09-13',
				  'firstname' => 'Jon',
				  'lastname' => 'Doe',
				  'first_name' => 'Jon',
				  'last_name' => 'Doe',
				  'customer_name' => 'Jon Doe',
				  'customer_id' => 'cus_MQZNZWHbZ4lVxG',
				  'custom_fields_post_data' => NULL,
				  'custom_fields' => NULL,
				  'custom' => NULL,
				  'email' => 'email@demodomain.test',
				  'phone' => NULL,
				  'country' => NULL,
				  'address1' => NULL,
				  'address2' => NULL,
				  'city' => NULL,
				  'state' => NULL,
				  'zip' => NULL,
				  'product_id' => '6',
				  'product_name' => 'Demo Subscription product',
				  'page_id' => NULL,
				  'page_url' => NULL,
				  'item_name' => '10 monthly',
				  'plan_id' => 'price_1LhiDvGa0zW4zxfkVg7OmCVK',
				  'option_id' => 'demosub',
				  'ip_address' => '172.69.70.166',
				  'tax_rate' => 0,
				  'tax_desc' => '',
				  'tax_data' => '',
				  'tax_type' => 'tax',
				  'stripe_tax_id' => '',
				  'user_account' => '1',
				  'auto_login' => NULL,
				  'coupon' => NULL,
				  'coupon_id' => NULL,
				  'on_sale' => 0,
				  'pay_method' => 'stripe',
				  'gateway_mode' => 'test',
				  'currency' => 'USD',
				  'main_offer' => NULL,
				  'main_offer_amt' => '10',
				  'us_parent' => NULL,
				  'ds_parent' => NULL,
				  'vat_number' => '',
				  'user' => 
				  array (
					'data' => 
					array (
					  'ID' => '1',
					  'user_login' => 'admin',
					  'user_pass' => '$P$BElKG035FBGHD8p0XorObdH//zh3dL1',
					  'user_nicename' => 'admin',
					  'user_email' => 'email@demodomain.test',
					  'user_url' => 'https://demodomain.test',
					  'user_registered' => '2020-08-05 23:33:08',
					  'user_activation_key' => '',
					  'user_status' => '0',
					  'display_name' => 'admin',
					),
					'ID' => 1,
					'caps' => 
					array (
					  'administrator' => true,
					),
					'cap_key' => 'wp_capabilities',
					'roles' => 
					array (
					  0 => 'subscriber',
					),
					'allcaps' => 
					array (
					  'read' => true,
					),
					'filter' => NULL,
				  ),
				  'start_date' => 'Sep 13, 2022',
				  'next_pay_date' => 'Oct 13, 2022',
				  'end_date' => false,
				  'sub_payment' => '<span class="sc-Price-amount amount"><span class="sc-Price-currencySymbol">&#36;</span>10.00</span> / month',
				  'sub_payment_plain' => '&#36;10.00 / month',
				  'sub_payment_terms' => '<span class="sc-Price-amount amount"><span class="sc-Price-currencySymbol">&#36;</span>10.00</span> / month',
				  'sub_payment_terms_plain' => '&#36;10.00 / month',
				),
			);

			return $data;
		}

	}

endif; // End if class_exists check.