<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_wp_fusion_Triggers_wpfs_auto_login_started' ) ) :

 /**
  * Load the wpfs_auto_login_started trigger
  *
  * @since 6.1.0
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_wp_fusion_Triggers_wpfs_auto_login_started {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'wpf_started_auto_login',
				'callback' => array( $this, 'wpfs_auto_login_started_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'user_id' => array( 'short_description' => __( '(Integer) The ID of the user that started the auto login.', 'wp-webhooks' ) ),
			'contact_id' => array( 'short_description' => __( '(Integer) The ID of the contact that started the auto login.', 'wp-webhooks' ) ),
		);

		$settings = array(
			'load_default_settings' => true,
			'data' => array()
		);

		$description = array(
			'tipps' => array(
				__( 'To learn more about the auto login feature of WP Fusion, please refer to their manual:', 'wp-webhooks' ) . ' <a target="_blank" href="https://wpfusion.com/documentation/tutorials/auto-login-links/">https://wpfusion.com/documentation/tutorials/auto-login-links/</a>',
			)
		);

		return array(
			'trigger'		   => 'wpfs_auto_login_started',
			'name'			  => __( 'Auto login started', 'wp-webhooks' ),
			'sentence'			  => __( 'a user started an auto login', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a user started an auto login within WP Fusion.', 'wp-webhooks' ),
			'description'	   => $description,
			'integration'	   => 'wp-fusion',
			'premium'		   => true,
		);

	}

	public function wpfs_auto_login_started_callback( $user_id, $contact_id ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wpfs_auto_login_started' );

		$response_data_array = array();
		$payload = array(
			'user_id' => $user_id,
			'contact_id' => $contact_id,
		);

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( $is_valid ){
				if( $webhook_url_name !== null ){
					$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				} else {
					$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				}
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_wpfs_auto_login_started', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'user_id' => 155,
			'contact_id' => '4',
		);

		return $data;
	}

  }

endif; // End if class_exists check.