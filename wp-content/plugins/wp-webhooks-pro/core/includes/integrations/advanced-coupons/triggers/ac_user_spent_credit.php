<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_advanced_coupons_Triggers_ac_user_spent_credit' ) ) :

	/**
	 * Load the ac_user_spent_credit trigger
	 *
	 * @since 6.1.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_advanced_coupons_Triggers_ac_user_spent_credit {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'acfw_after_order_paid_with_store_credits',
					'callback'  => array( $this, 'ac_user_spent_credit_callback' ),
					'priority'  => 10,
					'arguments' => 4,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {

			$parameter = array(
				'success' => array( 'short_description' => __( '(Bool) True if the topic was successfully created.', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further details about the action.', 'wp-webhooks' ) ),
			);

			$settings = array(
				'load_default_settings' => true,
				'data'                  => array(
					'wpwhpro_advanced_coupons_trigger_on_users' => array(
						'id'          => 'wpwhpro_advanced_coupons_trigger_on_users',
						'required'    => false,
						'multiple'    => true,
						'type'        => 'select',
						'choices'     => array(),
						'query'       => array(
							'filter' => 'users',
							'args'   => array(),
						),
						'label'       => __( 'Trigger on specified user', 'wp-webhooks' ),
						'description' => __( 'Select only the users you want to fire the trigger on. You can also choose multiple ones.', 'wp-webhooks' ),
					),
				),
			);

			return array(
				'trigger'           => 'ac_user_spent_credit',
				'name'              => __( 'User spent credit balance', 'wp-webhooks' ),
				'sentence'          => __( 'a user has spent the credit balance', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires as soon as a user has spent credit balance within Advanced Coupons.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'advanced-coupons',
				'premium'           => true,
			);

		}

		/**
		 * Triggers when User spended credit balance within Advanced Coupons
		 *
		 * @param $args data
		 */
		public function ac_user_spent_credit_callback( $amount, $new_balance, $order, $store_credit_entry ) {

			if ( $amount == 0 || empty( $amount ) ) {
				return;
			}

			$webhooks            = WPWHPRO()->webhook->get_hooks( 'trigger', 'ac_user_spent_credit' );
			$response_data_array = array();
			$ac_helpers          = WPWHPRO()->integrations->get_helper( 'advanced-coupons', 'ac_helpers' );

			$user_id                    = get_current_user_id();
			$data['spend_amount']       = $amount;
			$data['new_balance']        = $new_balance;
			$data['order']              = $order;
			$data['store_credit_entry'] = $store_credit_entry;
			$data['current_balance']    = \ACFWF()->Store_Credits_Calculate->get_customer_balance( $user_id );
			$data['total_credits']      = $ac_helpers->get_total_credits_user( $user_id );
			$data['user_id']            = $user_id;

			$payload = array(
				'data' => $data,
			);

			foreach ( $webhooks as $webhook ) {

				$webhook_url_name = ( is_array( $webhook ) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
				$is_valid         = true;
				if ( isset( $webhook['settings'] ) ) {

					if( isset( $webhook['settings']['wpwhpro_advanced_coupons_trigger_on_users'] ) 
					&& ! empty( $webhook['settings']['wpwhpro_advanced_coupons_trigger_on_users'] ) ) 
					{
						if ( ! in_array( $user_id, $webhook['settings']['wpwhpro_advanced_coupons_trigger_on_users'] )) 
						{
							$is_valid = false;
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

			do_action( 'wpwhpro/webhooks/trigger_ac_user_spent_credit', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array(
				'data' =>
				array(
					'spend_amount'       => 60,
					'new_balance'        => 120,
					'order'              =>
					array(
						'customer_id' => null,
					),
					'store_credit_entry' =>
					array(),
					'current_balance'    => 120,
					'total_credits'      => 720,
					'user_id'            => 2,
					'condition'          => 'greater',
				),
			);

			return $data;
		}

	}

endif; // End if class_exists check.
