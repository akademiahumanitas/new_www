<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_Triggers_wc_product_view' ) ) :

 /**
  * Load the wc_product_view trigger
  *
  * @since 4.3.7
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_woocommerce_Triggers_wc_product_view {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'template_redirect',
				'callback' => array( $this, 'wc_product_view_callback' ),
				'priority' => 20,
				'arguments' => 1,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'product_id' => array( 'short_description' => __( '(Integer) The id of the viewed product.', 'wp-webhooks' ) ),
			'user_id' => array( 'short_description' => __( '(Integer) The id of the user who vies it (in case given)', 'wp-webhooks' ) ),
			'user' => array( 'short_description' => __( '(Array) The user data of the given user.', 'wp-webhooks' ) ),
			'user_meta' => array( 'short_description' => __( '(Array) The user meta of the given user.', 'wp-webhooks' ) ),
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
			'trigger'		   => 'wc_product_view',
			'name'			  => __( 'Product viewed', 'wp-webhooks' ),
			'sentence'			  => __( 'a product was viewed', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a product was viewed within Woocommerce.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'woocommerce',
			'premium'		   => true,
		);

	}

	public function wc_product_view_callback(){

		global $post;

		if( is_admin() || ! $post instanceof WP_Post || $post->post_type !== 'product' ){
			return;
		}

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wc_product_view' );
		$user_id = get_current_user_id();
		$product_id = $post->ID;
		$payload = array(
			'product_id' => $product_id,
			'user_id' => $user_id,
			'user' => ( ! empty( $user_id ) ) ? get_userdata( $user_id ) : array(),
			'user_meta' => ( ! empty( $user_id ) ) ? get_user_meta( $user_id ) : array(),
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
				} else {
					$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				}
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_wc_product_view', $payload, $response_data_array );
	}
	
	public function get_demo( $options = array() ) {

		$data = array (
			'product_id' => 8096,
			'user_id' => 1,
			'user' => 
			array (
			  'data' => 
			  array (
				'ID' => '1',
				'user_login' => 'jondoe',
				'user_pass' => '$P$B4B1t8fCXXXXXXXXXXFN8GWC7EbzY1',
				'user_nicename' => 'Jon Doe',
				'user_email' => 'jondoe@demo.test',
				'user_url' => '',
				'user_registered' => '2022-07-27 23:58:11',
				'user_activation_key' => '',
				'user_status' => '0',
				'display_name' => 'Jon Doe',
				'spam' => '0',
				'deleted' => '0',
			  ),
			  'ID' => 1,
			  'caps' => 
			  array (
				'subscriber' => true,
			  ),
			  'cap_key' => 'wp_capabilities',
			  'roles' => 
			  array (
				29 => 'subscriber',
			  ),
			  'allcaps' => 
			  array (
				0 => 'read',
			  ),
			  'filter' => NULL,
			),
			'user_meta' => 
			array (
			  'nickname' => 
			  array (
				0 => 'jondoe',
			  ),
			  'first_name' => 
			  array (
				0 => 'Jon',
			  ),
			  'last_name' => 
			  array (
				0 => 'Doe',
			  ),
			  'description' => 
			  array (
				0 => '',
			  ),
			  'rich_editing' => 
			  array (
				0 => 'true',
			  ),
			  'comment_shortcuts' => 
			  array (
				0 => 'false',
			  ),
			  'admin_color' => 
			  array (
				0 => 'fresh',
			  ),
			  'use_ssl' => 
			  array (
				0 => '0',
			  ),
			  'show_admin_bar_front' => 
			  array (
				0 => 'false',
			  ),
			  'locale' => 
			  array (
				0 => '',
			  ),
			  'wp_capabilities' => 
			  array ()
			)
		);

		return $data;
	}

  }

endif; // End if class_exists check.