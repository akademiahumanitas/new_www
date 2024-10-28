<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_surecart_Triggers_surecart_product_purchased' ) ) :

	/**
	 * Load the surecart_product_purchased trigger
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_surecart_Triggers_surecart_product_purchased {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'surecart/purchase_created',
					'callback'  => array( $this, 'surecart_product_purchased_callback' ),
					'priority'  => 10,
					'arguments' => 1,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {

			$parameter = array(
				'success' => array( 'short_description' => __( '(Bool) True if the form was successfully submitted.', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further details about the submitting a form.', 'wp-webhooks' ) ),

			);

			$settings = array(
				'load_default_settings' => true,
				'data' => array(
					'wpwhpro_surecart_trigger_on_products' => array(
						'id'			=> 'wpwhpro_surecart_trigger_on_products',
						'type'			=> 'select',
						'multiple'		=> true,
						'choices'		=> array(),
						'query'			=> array(
							'filter'	=> 'helpers',
							'args'		=> array(
								'integration' => 'surecart',
								'helper' => 'surecart_helpers',
								'function' => 'get_query_products',
							)
						),
						'label'			=> __( 'Products', 'wp-webhooks' ),
						'placeholder'	=> '',
						'required'		=> false,
						'description'	=> __( 'Select only the products you want to fire the trigger on. You can also choose multiple ones. If none are selected, all are triggered.', 'wp-webhooks' )
					),
				),
			);

			return array(
				'trigger'           => 'surecart_product_purchased',
				'name'              => __( 'Product purchased', 'wp-webhooks' ),
				'sentence'          => __( 'a product was purchased', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires as soon as a product was purchased within SureCart.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'surecart',
				'premium'           => true,
			);

		}

		/**
		 * Triggers when JetFormBuilder submits a form
		 *
		 * @param $data Object
		 * @param $is_success Bool
		 */
		public function surecart_product_purchased_callback( $purchase ) {

			$webhooks            = WPWHPRO()->webhook->get_hooks( 'trigger', 'surecart_product_purchased' );
			$response_data_array = array();

			$payload = array(
				'success' => true,
				'msg'     => __( 'The user has purchased a product successfully.', 'wp-webhooks' ),
				'data'    => $purchase,
			);

			foreach ( $webhooks as $webhook ) {

				$webhook_url_name = ( is_array( $webhook ) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
				$is_valid         = true;

				if( isset( $webhook['settings'] ) ){
					foreach( $webhook['settings'] as $settings_name => $settings_data ){
						if( $settings_name === 'wpwhpro_surecart_trigger_on_products' && ! empty( $settings_data ) ){
							if( ! in_array( $purchase->product_id, $settings_data ) ){
								$is_valid = false;
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

			do_action( 'wpwhpro/webhooks/trigger_surecart_product_purchased', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array (
				'success' => true,
				'msg' => 'The user has purchased a product successfully.',
				'data' => 
				array (
				  'id' => '347bc5b3-b494-4435-a554-xxxxxxxxxxxx',
				  'object' => 'purchase',
				  'live_mode' => true,
				  'quantity' => 1,
				  'revoked' => false,
				  'revoked_at' => NULL,
				  'customer' => '499de2ae-a254-4d77-a5bf-xxxxxxxxxxxx',
				  'initial_order' => 'eb1cb7b4-f436-4c71-8863-xxxxxxxxxxxx',
				  'license' => NULL,
				  'product' => '1bee0b8d-a447-47f0-af55-xxxxxxxxxxxx',
				  'created_at' => 1667807998,
				  'updated_at' => 1667807998,
				  'has_product_changed' => false,
				  'previous_product_id' => false,
				  'previous_quantity' => false,
				  'customer_id' => '499de2ae-a254-4d77-a5bf-xxxxxxxxxxxx',
				  'product_id' => '1bee0b8d-a447-47f0-af55-xxxxxxxxxxxx',
				  'subscription_id' => NULL,
				  'refund_id' => NULL,
				),
			);

			return $data;
		}

	}

endif; // End if class_exists check.
