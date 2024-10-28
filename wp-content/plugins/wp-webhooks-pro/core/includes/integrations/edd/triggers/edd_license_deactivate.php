<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Triggers_edd_license_deactivate' ) ) :

 /**
  * Load the edd_license_deactivate trigger
  *
  * @since 4.2.0
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_edd_Triggers_edd_license_deactivate {

	public function is_active(){

		$is_active = class_exists( 'EDD_Software_Licensing' );

		//Backwards compatibility for the "Easy Digital Downloads" integration
		if( defined( 'WPWH_EDD_NAME' ) ){
			$is_active = false;
		}

		return $is_active;
	}

  /**
   * Register the actual functionality of the webhook
   *
   * @param mixed $response
   * @param string $action
   * @param string $response_ident_value
   * @param string $response_api_key
   * @return mixed The response data for the webhook caller
   */
	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'edd_sl_deactivate_license',
				'callback' => array( $this, 'wpwh_trigger_edd_license_deactivate' ),
				'priority' => 10,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'ID' => array( 'short_description' => __( '(Integer) The license id.', 'wp-webhooks' ) ),
			'key' => array( 'short_description' => __( '(String) The license key.', 'wp-webhooks' ) ),
			'customer_email' => array( 'short_description' => __( '(String) The email of the customer.', 'wp-webhooks' ) ),
			'customer_name' => array( 'short_description' => __( '(String) The full customer name.', 'wp-webhooks' ) ),
			'product_id' => array( 'short_description' => __( '(String) The id of the product.', 'wp-webhooks' ) ),
			'product_name' => array( 'short_description' => __( '(String) The full product name.', 'wp-webhooks' ) ),
			'activation_limit' => array( 'short_description' => __( '(Integer) The activation limit.', 'wp-webhooks' ) ),
			'activation_count' => array( 'short_description' => __( '(Integer) The number of total activations.', 'wp-webhooks' ) ),
			'activated_urls' => array( 'short_description' => __( '(String) A list of activated URLs.', 'wp-webhooks' ) ),
			'expiration' => array( 'short_description' => __( '(String) The expiration date in SQL format.', 'wp-webhooks' ) ),
			'is_lifetime' => array( 'short_description' => __( '(Integer) The number 1 or 0 if it is a lifetime.', 'wp-webhooks' ) ),
			'status' => array( 'short_description' => __( '(String) The current license status.', 'wp-webhooks' ) ),
		);

		$settings = array();

		return array(
			'trigger'		   => 'edd_license_deactivate',
			'name'			  => __( 'License deactivated', 'wp-webhooks' ),
			'sentence'			  => __( 'a license was deactivated', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires on deactivation of a license within Easy Digital Downloads.', 'wp-webhooks' ),
			'description'	   => array(),
			'callback'		  => 'test_edd_license_deactivate',
			'integration'	   => 'edd',
		);

	}

	/**
	 * Triggers once a new EDD payment was changed
	 *
	 * @param  integer $customer_id   Customer ID.
	 * @param  array   $args		  Customer data.
	 */
	public function wpwh_trigger_edd_license_deactivate( $license_id = 0, $download_id = 0 ){
		$edd_helpers = WPWHPRO()->integrations->get_helper( 'edd', 'edd_helpers' );
		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'edd_license_deactivate' );
		$response_data_array = array();

		foreach( $webhooks as $webhook ){
			$license_data = $edd_helpers->edd_get_license_data( $license_id, $download_id );

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

			if( $webhook_url_name !== null ){
				$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $license_data );
			} else {
				$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $license_data );
			}

			do_action( 'wpwhpro/webhooks/trigger_edd_license_deactivate', $license_id, $download_id, $license_data, $response_data_array );
		}
	}

	public function get_demo( $options = array() ) {

		$data = array(
			'ID'			   => 1234,
			'key'			  => '736b31fec1ecb01c28b51a577bb9c2b3',
			'customer_name'	=> 'Jane Doe',
			'customer_email'   => 'jane@test.com',
			'product_id'	   => 4321,
			'product_name'	 => 'Sample Product',
			'activation_limit' => 1,
			'activation_count' => 1,
			'activated_urls'   => 'sample.com',
			'expiration'	   => date( 'Y-n-d H:i:s', current_time( 'timestamp' ) ),
			'is_lifetime'	  => 0,
			'status'		   => 'inactive',
		);

	  return $data;
	}

  }

endif; // End if class_exists check.