<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_Triggers_wc_product_review_approved' ) ) :

 /**
  * Load the wc_product_review_approved trigger
  *
  * @since 4.3.7
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_woocommerce_Triggers_wc_product_review_approved {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'comment_post',
				'callback' => array( $this, 'wc_product_review_approved_callback' ),
				'priority' => 20,
				'arguments' => 3,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'review_id' => array( 'short_description' => __( '(Integer) The id of the review.', 'wp-webhooks' ) ),
			'product_id' => array( 'short_description' => __( '(Integer) The id of the reviewed product.', 'wp-webhooks' ) ),
			'rating' => array( 'short_description' => __( '(Integer) The rating of the comment.', 'wp-webhooks' ) ),
			'comment' => array( 'short_description' => __( '(Array) Further details about the comment.', 'wp-webhooks' ) ),
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
			'trigger'		   => 'wc_product_review_approved',
			'name'			  => __( 'Product review approved', 'wp-webhooks' ),
			'sentence'			  => __( 'a product review has been approved', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a product has been approved within Woocommerce.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'woocommerce',
			'premium'		   => true,
		);

	}

	public function wc_product_review_approved_callback( $comment_id, $comment_approved, $comment ){

		if( $comment_approved !== 1 ){
			return;
		}

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wc_product_review_approved' );
		$product_id = ( isset( $comment[ 'comment_post_ID' ] ) ) ? $comment[ 'comment_post_ID' ] : 0;
		$rating = get_comment_meta( $comment_id, 'rating', true );
		$payload = array(
			'review_id' => $comment_id,
			'product_id' => $product_id,
			'rating' => $rating,
			'comment' => $comment,
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

		do_action( 'wpwhpro/webhooks/trigger_wc_product_review_approved', $payload, $response_data_array );
	}
	
	public function get_demo( $options = array() ) {

		$data = array (
			'review_id' => 296,
			'product_id' => 8096,
			'rating' => "4",
			'comment' => 
			array (
			  'comment_post_ID' => 8096,
			  'comment_author' => 'jondoe',
			  'comment_author_email' => 'jondoe@domain.test',
			  'comment_author_url' => '',
			  'comment_content' => 'This is a product review description.',
			  'comment_type' => 'review',
			  'comment_parent' => 0,
			  'user_ID' => 1,
			  'user_id' => 1,
			  'comment_author_IP' => '127.0.0.1',
			  'comment_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Safari/537.36',
			  'comment_date' => '2022-03-11 07:50:07',
			  'comment_date_gmt' => '2022-03-11 07:50:07',
			  'filtered' => true,
			  'comment_approved' => 1,
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.