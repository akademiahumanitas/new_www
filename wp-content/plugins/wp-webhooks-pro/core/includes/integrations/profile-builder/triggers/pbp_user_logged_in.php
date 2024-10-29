<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_profile_builder_Triggers_pbp_user_logged_in' ) ) :

 /**
  * Load the pbp_user_logged_in trigger
  *
  * @since 6.1.5
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_profile_builder_Triggers_pbp_user_logged_in {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'wp_login',
				'callback' => array( $this, 'wp_login_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => false,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'user_login' => array( 'short_description' => __( '(String) The used login name for the user.', 'wp-webhooks' ) ),
			'user' => array( 'short_description' => __( '(Array) Further data about the user.', 'wp-webhooks' ) ),
		);

		$settings = array(
			'load_default_settings' => true,
			'data' => array()
		);

		return array(
			'trigger'		   => 'pbp_user_logged_in',
			'name'			  => __( 'User logged in', 'wp-webhooks' ),
			'sentence'			  => __( 'a user logged in', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a user logged in within Profile Builder by Cozmoslabs.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'profile-builder',
			'premium'		   => true,
		);

	}

	public function wp_login_callback( $user_login, $user ){

		//Bail if not initiated by Profile Builder
		if( ! did_action( 'wppb_process_login_start' ) ){
			return;
		}

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'pbp_user_logged_in' );

		$payload = array(
			'user_login' => $user_login,
			'user' => $user,
		);

		$response_data_array = array();

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

		do_action( 'wpwhpro/webhooks/trigger_pbp_user_logged_in', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'user_login' => 'jondoe',
			'user' => 
			array (
			  'data' => 
			  array (
				'ID' => '206',
				'user_login' => 'jondoe',
				'user_pass' => '$P$BbPBFB38.u7C7qdgF2RFDj6hMX1UWq/',
				'user_nicename' => 'Jon Doe',
				'user_email' => 'jon@doe.test',
				'user_url' => '',
				'user_approved' => '2023-06-09 09:19:07',
				'user_activation_key' => '',
				'user_status' => '0',
				'display_name' => 'Jon Doe',
				'spam' => '0',
				'deleted' => '0',
			  ),
			  'ID' => 206,
			  'caps' => 
			  array (
				'subscriber' => true,
			  ),
			  'cap_key' => 'wp_capabilities',
			  'roles' => 
			  array (
				0 => 'subscriber',
			  ),
			  'allcaps' => 
			  array (
				'read' => true,
				'level_0' => true,
				'subscriber' => true,
			  ),
			  'filter' => NULL,
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.