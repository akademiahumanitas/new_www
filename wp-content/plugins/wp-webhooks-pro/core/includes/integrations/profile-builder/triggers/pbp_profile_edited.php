<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_profile_builder_Triggers_pbp_profile_edited' ) ) :

 /**
  * Load the pbp_profile_edited trigger
  *
  * @since 6.1.5
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_profile_builder_Triggers_pbp_profile_edited {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'wppb_edit_profile_success',
				'callback' => array( $this, 'wppb_edit_profile_success_callback' ),
				'priority' => 20,
				'arguments' => 3,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'fields' => array( 'short_description' => __( '(Array) The fields submitted via the form.', 'wp-webhooks' ) ),
			'form_name' => array( 'short_description' => __( '(String) The form name that was used to update the user profile.', 'wp-webhooks' ) ),
			'user_id' => array( 'short_description' => __( '(Integer) The ID of the newly updated user profile.', 'wp-webhooks' ) ),
			'user' => array( 'short_description' => __( '(Array) Further data about the user.', 'wp-webhooks' ) ),
		);

		$settings = array(
			'load_default_settings' => true,
			'data' => array()
		);

		return array(
			'trigger'		   => 'pbp_profile_edited',
			'name'			  => __( 'Profile edited', 'wp-webhooks' ),
			'sentence'			  => __( 'a user profile is edited', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a user profile is edited within Profile Builder by Cozmoslabs.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'profile-builder',
			'premium'		   => true,
		);

	}

	public function wppb_edit_profile_success_callback( $request, $form_name, $user_id ){

		$user_id = intval( $user_id );

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'pbp_profile_edited' );

		$payload = array(
			'fields' => $request,
			'form_name' => $form_name,
			'user_id' => $user_id,
			'user' => get_user_by( 'ID', $user_id ),
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

		do_action( 'wpwhpro/webhooks/trigger_pbp_profile_edited', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'fields' => 
			array (
			  'username' => 'jondoe',
			  'first_name' => 'Jon',
			  'last_name' => 'Doe',
			  'nickname' => '',
			  'email' => 'jon@doe.test',
			  'website' => '',
			  'description' => '',
			  'passw1' => '1234',
			  'passw2' => '1234',
			  'custom_field_1' => '',
			  'redirect_to' => '',
			  'action' => 'register',
			  'edit_profile' => 'Update',
			  'form_name' => 'Demo Form',
			  'form_id' => 123,
			  'register_unspecified_nonce_field' => 'f7f9bbd9a2',
			  '_wp_http_referer' => '/register/',
			  'woocommerce-login-nonce' => NULL,
			  '_wpnonce' => NULL,
			  'woocommerce-reset-password-nonce' => NULL,
			  'woocommerce-edit-address-nonce' => NULL,
			  'save-account-details-nonce' => NULL,
			),
			'form_name' => 'Demo Form',
			'user_id' => 206,
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
				'user_registered' => '2023-06-09 09:19:07',
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