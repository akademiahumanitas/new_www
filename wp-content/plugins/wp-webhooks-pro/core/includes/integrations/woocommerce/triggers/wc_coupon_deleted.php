<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_Triggers_wc_coupon_deleted' ) ) :

 /**
  * Load the wc_coupon_deleted trigger
  *
  * @since 4.3.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_woocommerce_Triggers_wc_coupon_deleted {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'wp_trash_post',
				'callback' => array( $this, 'wc_coupon_deleted_callback' ),
				'priority' => 20,
				'arguments' => 1,
				'delayed' => false,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'id' => array( 'short_description' => __( 'The id of the deleted (trashed) coupon.', 'wp-webhooks' ) ),
		);

		$description = array(
			'tipps' => array(
				__( 'If the webhook does not work as expected due to an error like "woocommerce_rest_cannot_view", please adjust the user ID within the trigger settings.', 'wp-webhooks' ),
				__( 'You can fire this trigger as well on a specific Woocommerce API version. To do that, select a version within the webhook URL settings.', 'wp-webhooks' ),
				__( 'You can also set a custom secret key just as for the default Woocommerce webhooks. IF you do not set one, there will be one automatically generated.', 'wp-webhooks' ),
			)
		);

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_woocommerce_set_user' => array(
					'id'		  => 'wpwhpro_woocommerce_set_user',
					'type'		=> 'text',
					'label'	   => __( 'Set user id', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'Set the numeric ID of a user that has permission to view the Woocommerce REST API. By default we try to locate the ID of the first admin user.', 'wp-webhooks' )
				),
				'wpwhpro_woocommerce_set_api_version' => array(
					'id'		  => 'wpwhpro_woocommerce_set_api_version',
					'type'		=> 'select',
					'multiple'	=> false,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'helpers',
						'args'		=> array(
							'integration' => 'woocommerce',
							'helper' => 'wc_helpers',
							'function' => 'get_query_wc_api_versions',
						)
					),
					'label'	   => __( 'Set API version', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'default_value'	=> 'wp_api_v2',
					'description' => __( 'Select the Woocommerce API version you want to use for this request. By default, we use wp_api_v2', 'wp-webhooks' )
				),
				'wpwhpro_woocommerce_set_secret' => array(
					'id'		  => 'wpwhpro_woocommerce_set_secret',
					'type'		=> 'text',
					'label'	   => __( 'Set secret', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'Set a custom secret that gets validated by Woocommerce, just as you know it from the default Woocommerce webhooks.', 'wp-webhooks' )
				),
			)
		);

		return array(
			'trigger'		   => 'wc_coupon_deleted',
			'name'			  => __( 'Coupon deleted', 'wp-webhooks' ),
			'sentence'			  => __( 'a coupon was deleted', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a coupon was deleted within Woocommerce.', 'wp-webhooks' ),
			'description'	   => $description,
			'integration'	   => 'woocommerce',
			'premium'		   => true,
		);

	}

	/**
	 * Triggers once a coupon was deleted
	 *
	 * @param mixed $arg
	 */
	public function wc_coupon_deleted_callback( $arg ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wc_coupon_deleted' );
		$payload = array();
		$payload_track = array();
		$wc_helpers = WPWHPRO()->integrations->get_helper( 'woocommerce', 'wc_helpers' );

		$topic = 'coupon.deleted';
		$api_version = 'wp_api_v2';

		if( ! class_exists( 'WC_Webhook' ) ){
			return;
		}

		$wc_webhook = new WC_Webhook();
		$wc_webhook->set_name( 'wpwh-' . $topic );
		$wc_webhook->set_status( 'active' );
		$wc_webhook->set_topic( $topic );
		$wc_webhook->set_user_id( 0 );
		$wc_webhook->set_pending_delivery( false );
		#$wc_webhook->set_delivery_url(  );

		//Make sure we follow Woocommerce standards of verifying webhooks
		$continue_webhook = false;
		
		//revalidate ourselves to avoid woocommerce issues
		if( is_numeric( $arg ) ){
			$post_id = $arg;
			$post_type = get_post_type( $post_id );
			if( $post_type === 'shop_coupon' ){
				$continue_webhook = true;
			}
		}

		if ( ! $continue_webhook ) {
			return;
		}

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){

				if( $is_valid && isset( $webhook['settings']['wpwhpro_woocommerce_set_api_version'] ) && ! empty( $webhook['settings']['wpwhpro_woocommerce_set_api_version'] ) ){
					$api_version = $webhook['settings']['wpwhpro_woocommerce_set_api_version'];
				}

				if( $is_valid && isset( $webhook['settings']['wpwhpro_woocommerce_set_secret'] ) && ! empty( $webhook['settings']['wpwhpro_woocommerce_set_secret'] ) ){
					$wc_webhook->set_secret( $webhook['settings']['wpwhpro_woocommerce_set_secret'] );
				}

				if( $is_valid 
					&& isset( $webhook['settings']['wpwhpro_woocommerce_set_user'] ) 
					&& ! empty( $webhook['settings']['wpwhpro_woocommerce_set_user'] ) 
					&& is_numeric( $webhook['settings']['wpwhpro_woocommerce_set_user'] )
				){
					$wc_webhook->set_user_id( intval( $webhook['settings']['wpwhpro_woocommerce_set_user'] ) );
				}

				//Make sure we automatically prevent the webhook from firing twice due to the Woocommerce hook notation
				$webhook['settings']['wpwhpro_trigger_single_instance_execution'] = 1;
			} else {
				$webhook['settings'] = array(
					'wpwhpro_trigger_single_instance_execution' => 1,
				);
			}

			if( $is_valid ){

				//Maybe assign a default user ID of none hase been given
				if( ! $wc_webhook->get_user_id() ){
					$admin_id = $wc_helpers->get_first_admin_id();

					if( ! empty( $admin_id ) ){
						$wc_webhook->set_user_id( intval( $admin_id ) );
					}
				}

				$wc_webhook->set_api_version( $api_version );
				$payload = $wc_webhook->build_payload( $arg );

				//setup headers
				$headers	                                      = array();
				$headers['Content-Type']      		 = 'application/json';
				$headers['X-WC-Webhook-Source']      = home_url( '/' ); // Since 2.6.0.
				$headers['X-WC-Webhook-Topic']       = $wc_webhook->get_topic();
				$headers['X-WC-Webhook-Resource']    = $wc_webhook->get_resource();
				$headers['X-WC-Webhook-Event']       = $wc_webhook->get_event();
				$headers['X-WC-Webhook-Signature']   = $wc_webhook ->generate_signature( trim( wp_json_encode( $payload ) ) );
				$headers['X-WC-Webhook-ID']          = 0;
				$headers['X-WC-Webhook-Delivery-ID'] = 0;

				if( $webhook_url_name !== null ){
					$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload, array( 'headers' => $headers ) );
					$payload_track[] = $payload;
				} else {
					$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload, array( 'headers' => $headers ) );
				}
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_wc_coupon_deleted', $payload, $response_data_array, $payload_track );
	}
	
	public function get_demo( $options = array() ) {

		$data = array (
			'id' => 8090
		);

		return $data;
	}

  }

endif; // End if class_exists check.