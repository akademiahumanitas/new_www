<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_jetpack_crm_Triggers_jpcrm_customer_created' ) ) :

	/**
	 * Load the jpcrm_customer_created trigger
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_jetpack_crm_Triggers_jpcrm_customer_created {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'zbs_new_customer',
					'callback'  => array( $this, 'jpcrm_customer_created_callback' ),
					'priority'  => 10,
					'arguments' => 1,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {

			$parameter = array(
				'success'     => array( 'short_description' => __( '(Bool) True if the customer was successfully created.', 'wp-webhooks' ) ),
				'msg'         => array( 'short_description' => __( '(String) Further details about the creation of a customer.', 'wp-webhooks' ) ),
				'customer_id' => array(
					'label'             => __( 'Customer ID', 'wp-webhooks' ),
					'short_description' => __( '(Integer) The customer id.', 'wp-webhooks' ),
				),
				'customer' => array(
					'label'             => __( 'Customer data', 'wp-webhooks' ),
					'short_description' => __( '(Array) The further details about the created customer.', 'wp-webhooks' ),
				),
			);

			$settings = array(
				'load_default_settings' => true,
			);

			return array(
				'trigger'           => 'jpcrm_customer_created',
				'name'              => __( 'Customer created', 'wp-webhooks' ),
				'sentence'          => __( 'a customer was created', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires as soon as a customer was created within Jetpack CRM.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'jetpack-crm',
				'premium'           => true,
			);

		}

		/**
		 * Triggers when Jetpack CRM creates a customer
		 *
		 * @param $customer_id Customer's id
		 */
		public function jpcrm_customer_created_callback( $customer_id ) {

			global $zbs;

			$customer = $zbs->DAL->contacts->getContact( $customer_id );

			$webhooks            = WPWHPRO()->webhook->get_hooks( 'trigger', 'jpcrm_customer_created' );
			$response_data_array = array();

			$payload = array(
				'success'     => true,
				'msg'         => __( 'The customer has been created.', 'wp-webhooks' ),
				'customer_id' => $customer_id,
				'customer'    => $customer,
			);

			foreach ( $webhooks as $webhook ) {

				$webhook_url_name = ( is_array( $webhook ) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
				$is_valid         = true;

				if ( $is_valid ) {
					if ( $webhook_url_name !== null ) {
						$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					} else {
						$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					}
				}
			}

			do_action( 'wpwhpro/webhooks/trigger_jpcrm_customer_created', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array(
				'success'     => true,
				'msg'         => 'The customer has been created.',
				'customer_id' => 2,
				'customer'    => array(
					'id'                 => '23',
					'owner'              => '-1',
					'status'             => 'Lead',
					'email'              => '',
					'prefix'             => 'Mrs',
					'fname'              => 'John',
					'lname'              => 'Doe',
					'addr1'              => 'Demo location',
					'addr2'              => '',
					'city'               => 'London',
					'county'             => 'Baker',
					'country'            => 'UK',
					'postcode'           => '233333',
					'secaddr_addr1'      => 'Demo address',
					'secaddr_addr2'      => 'Second demo address',
					'secaddr_city'       => 'Berlin',
					'secaddr_county'     => 'Demo State',
					'secaddr_country'    => 'Germany',
					'secaddr_postcode'   => '222333',
					'hometel'            => '233 33 33',
					'worktel'            => '333 33 33',
					'mobtel'             => '444 44 44',
					'wpid'               => '-1',
					'avatar'             => '',
					'tw'                 => '',
					'li'                 => '',
					'fb'                 => '',
					'created'            => '2022-10-16 17:02:42',
					'lastcontacted'      => -1,
					'createduts'         => '1665939762',
					'created_date'       => '10/16/2022',
					'lastupdated'        => '1665939762',
					'lastupdated_date'   => '10/16/2022',
					'lastcontacteduts'   => '-1',
					'lastcontacted_date' => false,
					'fullname'           => 'Mr John Doe',
					'name'               => 'Mr John Doe',
				),
			);

			return $data;
		}

	}

endif; // End if class_exists check.
