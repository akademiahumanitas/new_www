<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_studiocart_Triggers_sc_order_completed' ) ) :

	/**
	 * Load the sc_order_completed trigger
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_studiocart_Triggers_sc_order_completed {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'sc_order_complete',
					'callback'  => array( $this, 'wpwh_trigger_sc_order_completed' ),
					'priority'  => 20,
					'arguments' => 3,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {

				$parameter = array(
				'status'     => array( 'short_description' => __( '(String) The status of the order.', 'wp-webhooks' ) ),
				'order_type' => array( 'short_description' => __( '(String) The type of the order.', 'wp-webhooks' ) ),
				'order_info' => array( 'short_description' => __( '(Array) Further information about the order', 'wp-webhooks' ) ),
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
						'description' => __( 'Select only the products you want to fire the trigger on. You can also choose multiple ones. If none are selected, all are triggered.', 'wp-webhooks' )
					),
				)
			);

			return array(
				'trigger'           => 'sc_order_completed',
				'name'              => __( 'Order completed', 'wp-webhooks' ),
				'sentence'          => __( 'an order was completed', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires after an order was completed within Studiocart.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'studiocart',
			);

		}

		/**
		 * Triggers once User completes an order
		 *
		 * @param string $status Status of an order
		 * @param array $order_info Order data
		 * @param string $order_type Order type
		 */
		public function wpwh_trigger_sc_order_completed( $status, $order_info, $order_type ) {

			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'sc_order_completed' );

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

			do_action( 'wpwhpro/webhooks/trigger_sc_order_completed', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array(
				'status'     => 'paid',
				'order_type' => 'main',
				'order_info' =>
					array(
						'ID'                      => 68,
						'date'                    => 'August 26, 2022',
						'id'                      => 68,
						'transaction_id'          => null,
						'status'                  => 'completed',
						'payment_status'          => 'completed',
						'custom_fields_post_data' => null,
						'custom_fields'           => null,
						'custom_prices'           => null,
						'product_id'              => '46',
						'product_name'            => 'Subscription Product',
						'page_id'                 => '46',
						'page_url'                => '/wpwhpro/products/subscription-product/',
						'item_name'               => 'subscription1',
						'plan'                    =>
							array(
								'type'               => 'recurring',
								'option_id'          => 'Sub1',
								'name'               => 'subscription1',
								'stripe_id'          => 'Sub1',
								'price'              => '22',
								'initial_payment'    => 22,
								'cancel_immediately' => '',
								'tax_rate'           => '',
								'installments'       => '12',
								'interval'           => 'month',
								'frequency'          => '1',
								'trial_days'         => '',
								'fee'                => '',
								'next_bill_date'     => 1664150400,
								'cancel_at'          => 1693051468,
								'db_cancel_at'       => '2023-08-26',
								'text'               => '<span class="sc-Price-currencySymbol">&#36;</span>22.00 / month x 12',
							),
						'plan_id'                 => 'Sub1',
						'option_id'               => 'Sub1',
						'invoice_total'           => '22',
						'invoice_subtotal'        => '22.00',
						'amount'                  => '22.00',
						'main_offer_amt'          => '22',
						'pre_tax_amount'          => 0,
						'tax_amount'              => 0,
						'auto_login'              => null,
						'coupon'                  => null,
						'coupon_id'               => null,
						'on_sale'                 => 0,
						'accept_terms'            => null,
						'accept_privacy'          => null,
						'consent'                 => null,
						'purchase_note'           => null,
						'order_log'               =>
							array(
								'1661513407 - sc931322980'  => 'Creating order.',
								'1661515468 - sc1386784732' => 'Order status updated to completed',
							),
						'order_bumps'             => null,
						'us_parent'               => null,
						'ds_parent'               => null,
						'us_offer'                => null,
						'order_parent'            => null,
						'refund_log'              => null,
						'order_type'              => null,
						'subscription_id'         => '67',
						'firstname'               => 'Jon',
						'lastname'                => 'Doe',
						'first_name'              => 'Jon',
						'last_name'               => 'Doe',
						'customer_name'           => 'Jon Doe',
						'customer_id'             => null,
						'email'                   => 'email@yourdomain.test',
						'phone'                   => null,
						'country'                 => null,
						'address1'                => null,
						'address2'                => null,
						'city'                    => null,
						'state'                   => null,
						'zip'                     => null,
						'ip_address'              => '::1',
						'user_account'            => '2',
						'pay_method'              => 'paypal',
						'gateway_mode'            => 'test',
						'currency'                => 'USD',
						'tax_rate'                => 0,
						'tax_desc'                => '',
						'tax_data'                => '',
						'tax_type'                => 'tax',
						'vat_number'              => '',
						'stripe_tax_id'           => '',
						'invoice_number'          => null,
						'invoice_link'            => 'http://yourdomain.test/wpwhpro?sc-invoice=681661513407&id=68&dl=1',
						'invoice_link_html'       => '<a href="http://yourdomain.test/wpwhpro?sc-invoice=681661513407&id=68&dl=1" target="_blank" rel="noopener noreferrer">Download Invoice</a>',
						'user'                    =>
							array(
								'data'    =>
									array(
										'ID'                  => '2',
										'user_login'          => 'hello',
										'user_pass'           => '$P$B3mbPLWalbAdFFaeAN/q9CBcVrWXdI/',
										'user_nicename'       => 'hello',
										'user_email'          => 'email@yourdomain.test',
										'user_url'            => 'https://yourdomain.test',
										'user_registered'     => '2022-08-26 11:23:58',
										'user_activation_key' => '1661513043:$P$BxRvDyhi3LBMKIemCWjlyPgvSIlIcr1',
										'user_status'         => '0',
										'display_name'        => 'Jon Doe',
									),
								'ID'      => 2,
								'caps'    =>
									array(
										'contributor' => true,
									),
								'cap_key' => 'wp_capabilities',
								'roles'   =>
									array(
										0 => 'subscriber',
									),
								'allcaps' =>
									array(
										'read'         => true,
									),
								'filter'  => null,
							),
						'renewal_order'           => '',
					),
			);

			return $data;
		}

	}

endif; // End if class_exists check.