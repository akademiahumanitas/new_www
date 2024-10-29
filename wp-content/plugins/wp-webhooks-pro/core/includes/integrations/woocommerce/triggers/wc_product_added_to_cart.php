<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_Triggers_wc_product_added_to_cart' ) ) :

 /**
  * Load the wc_product_added_to_cart trigger
  *
  * @since 4.3.7
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_woocommerce_Triggers_wc_product_added_to_cart {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'woocommerce_add_to_cart',
				'callback' => array( $this, 'wc_product_added_to_cart_callback' ),
				'priority' => 20,
				'arguments' => 6,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'cart_item_key' => array( 'short_description' => __( '(String) The key of the current cart item.', 'wp-webhooks' ) ),
			'product_id' => array( 'short_description' => __( '(Integer) The id of the added product.', 'wp-webhooks' ) ),
			'quantity' => array( 'short_description' => __( '(Integer) The quantity of the product.', 'wp-webhooks' ) ),
			'variation_id' => array( 'short_description' => __( '(Integer) The variation id of the product (in case given).', 'wp-webhooks' ) ),
			'user_id' => array( 'short_description' => __( '(Integer) The id of the user who vies it (in case given)', 'wp-webhooks' ) ),
			'variation' => array( 'short_description' => __( '(Array) Further details about the variation.', 'wp-webhooks' ) ),
		);

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_woocommerce_trigger_on_product' => array(
					'id'		  => 'wpwhpro_woocommerce_trigger_on_product',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'posts',
						'args'		=> array(
							'post_type' => 'product',
							'post_status' => array( 'private', 'publish' ),
						)
					),
					'label'	   => __( 'Trigger on selected products', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'Select only the products you want to fire the trigger on. If none is selected, all are triggered.', 'wp-webhooks' )
				),
			)
		);

		return array(
			'trigger'		   => 'wc_product_added_to_cart',
			'name'			  => __( 'Product added to cart', 'wp-webhooks' ),
			'sentence'			  => __( 'a product was added to the cart', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a product was added to the cart within Woocommerce.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'woocommerce',
			'premium'		   => true,
		);

	}

	/**
	 * Triggers once a coupon was created
	 *
	 * @param mixed $arg
	 */
	public function wc_product_added_to_cart_callback( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wc_product_added_to_cart' );
		$user_id = get_current_user_id();
		$payload = array(
			'cart_item_key' => $cart_item_key,
			'product_id' => $product_id,
			'quantity' => $quantity,
			'variation_id' => $variation_id,
			'user_id' => $user_id,
			'variation' => $variation,
		);

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){

				if( $is_valid && isset( $webhook['settings']['wpwhpro_woocommerce_trigger_on_product'] ) && is_array( $webhook['settings']['wpwhpro_woocommerce_trigger_on_product'] ) ){
					if( ! in_array( $product_id, $webhook['settings']['wpwhpro_woocommerce_trigger_on_product'] ) ){
						$is_valid = false;
					}
				}

			}

			if( $is_valid ){
				if( $webhook_url_name !== null ){
					$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					$payload_track[] = $payload;
				} else {
					$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				}
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_wc_product_added_to_cart', $payload, $response_data_array, $payload_track );
	}
	
	public function get_demo( $options = array() ) {

		$data = array (
			'cart_item_key' => 'a95aa4e62b22c9bc5bca4e83cadfaa82',
			'product_id' => 8096,
			'quantity' => 1,
			'variation_id' => 0,
			'user_id' => 1,
			'variation' => 
			array (
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.