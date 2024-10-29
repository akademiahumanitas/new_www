<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_memberships_Triggers_wcm_membership_expired' ) ) :

 /**
  * Load the wcm_membership_expired trigger
  *
  * @since 4.3.7
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_woocommerce_memberships_Triggers_wcm_membership_expired {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'wc_memberships_user_membership_status_changed',
				'callback' => array( $this, 'wcm_membership_expired_callback' ),
				'priority' => 20,
				'arguments' => 3,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'user_id' => array( 'short_description' => __( '(Integer) The ID of the user the membership was expired for.', 'wp-webhooks' ) ),
			'user_membership_id' => array( 'short_description' => __( '(Integer) The ID of the user membership.', 'wp-webhooks' ) ),
			'membership_plan_id' => array( 'short_description' => __( '(Integer) The ID of the membership plan.', 'wp-webhooks' ) ),
			'user' => array( 'short_description' => __( '(Array) Further details about the user.', 'wp-webhooks' ) ),
		);

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_woocommerce_trigger_on_member_plan' => array(
					'id'		  => 'wpwhpro_woocommerce_trigger_on_member_plan',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'helpers',
						'args'		=> array(
							'integration' => 'woocommerce-memberships',
							'helper' => 'wcm_helpers',
							'function' => 'get_query_membership_plans',
						)
					),
					'label'	   => __( 'Trigger on selected membership plans', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'Select only the membership plans you want to fire the trigger on. If none is selected, all are triggered.', 'wp-webhooks' )
				),
			)
		);

		return array(
			'trigger'		   => 'wcm_membership_expired',
			'name'			  => __( 'Membership expired', 'wp-webhooks' ),
			'sentence'			  => __( 'a membership expired', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a membership expired within WooCommerce Memberships.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'woocommerce-memberships',
			'premium'		   => true,
		);

	}

	public function wcm_membership_expired_callback( $user_membership, $old_status, $new_status ){	

		if( $old_status === $new_status || $new_status !== 'expired' ){
			return;
		}

		if ( is_numeric( $user_membership ) || $user_membership instanceof \WP_Post ) {
			$user_membership = wc_memberships_get_user_membership( $user_membership );
		}

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wcm_membership_expired' );
		$payload = array(
			'user_id' => ( ! empty( $user_membership ) ) ? $user_membership->get_user_id() : 0,
			'user_membership_id' => ( ! empty( $user_membership ) ) ? $user_membership->get_id() : 0,
			'membership_plan_id' => ( ! empty( $user_membership ) ) ? $user_membership->get_plan_id() : 0,
			'user' => ( ! empty( $user_membership ) ) ? get_userdata( $user_membership->get_user_id() ) : array(),
		);

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){

				if( $is_valid && isset( $webhook['settings']['wpwhpro_woocommerce_trigger_on_member_plan'] ) && is_array( $webhook['settings']['wpwhpro_woocommerce_trigger_on_member_plan'] ) ){
					if( ! in_array( $payload['membership_plan_id'], $webhook['settings']['wpwhpro_woocommerce_trigger_on_member_plan'] ) ){
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

		do_action( 'wpwhpro/webhooks/trigger_wcm_membership_expired', $payload, $response_data_array );
	}
	
	public function get_demo( $options = array() ) {

		$data = array (
			'user_id' => 87,
			'user_membership_id' => 9152,
			'membership_plan_id' => 0,
			'user' => 
			array (
			  'data' => 
			  array (
				'ID' => '87',
				'user_login' => 'jondoe',
				'user_pass' => '$P$BEkpnevKHXvnXXXXXXXXXYTJ85P/',
				'user_nicename' => 'Jon Doe',
				'user_email' => 'jondoe@domain.test',
				'user_url' => '',
				'user_registered' => '2021-07-03 15:44:54',
				'user_activation_key' => '',
				'user_status' => '0',
				'display_name' => 'Jon Doe',
				'spam' => '0',
				'deleted' => '0',
			  ),
			  'ID' => 87,
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
				'read_private_locations' => true,
				'read_private_events' => true,
				'manage_resumes' => true,
				'subscriber' => true,
			  ),
			  'filter' => NULL,
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.