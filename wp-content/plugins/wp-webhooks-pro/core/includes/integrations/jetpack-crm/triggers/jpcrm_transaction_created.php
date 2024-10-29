<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_jetpack_crm_Triggers_jpcrm_transaction_created' ) ) :

	/**
	 * Load the jpcrm_transaction_created trigger
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_jetpack_crm_Triggers_jpcrm_transaction_created {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'zbs_new_transaction',
					'callback'  => array( $this, 'jpcrm_transaction_created_callback' ),
					'priority'  => 10,
					'arguments' => 1,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {

			$parameter = array(
				'success'        => array( 'short_description' => __( '(Bool) True if the transaction was successfully created.', 'wp-webhooks' ) ),
				'msg'            => array( 'short_description' => __( '(String) Further details about the creation of an transaction.', 'wp-webhooks' ) ),
				'transaction_id' => array(
					'label'             => __( 'Transaction ID', 'wp-webhooks' ),
					'short_description' => __( '(Integer) The transaction id.', 'wp-webhooks' ),
				),
				'transaction'    => array(
					'label'             => __( 'Transaction data', 'wp-webhooks' ),
					'short_description' => __( '(Array) The further details about the created transaction.', 'wp-webhooks' ),
				),
			);

			$settings = array(
				'load_default_settings' => true,
			);

			return array(
				'trigger'           => 'jpcrm_transaction_created',
				'name'              => __( 'Transaction created', 'wp-webhooks' ),
				'sentence'          => __( 'a transaction was created', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires as soon as a transaction was created within Jetpack CRM.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'jetpack-crm',
				'premium'           => true,
			);

		}

		/**
		 * Triggers when Jetpack CRM creates a transaction
		 *
		 * @param $transaction_id Transaction's id
		 */
		public function jpcrm_transaction_created_callback( $transaction_id ) {

			global $zbs;

			$transaction = $zbs->DAL->transactions->getTransaction( $transaction_id );

			$webhooks            = WPWHPRO()->webhook->get_hooks( 'trigger', 'jpcrm_transaction_created' );
			$response_data_array = array();

			$payload = array(
				'success'        => true,
				'msg'            => __( 'The transaction has been created.', 'wp-webhooks' ),
				'transaction_id' => $transaction_id,
				'transaction'    => $transaction,
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

			do_action( 'wpwhpro/webhooks/trigger_jpcrm_transaction_created', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array(
				'success'        => true,
				'msg'            => 'The transaction has been created.',
				'transaction_id' => 13,
				'transaction'    => array(
					'id'                  => '13',
					'owner'               => '1',
					'status'              => 'Succeeded',
					'type'                => 'Sale',
					'status_bool'         => 1,
					'type_accounting'     => 'debit',
					'ref'                 => 'crmt_634c45c2d83a4',
					'origin'              => '',
					'parent'              => 0,
					'hash'                => '',
					'title'               => 'Demo Transaction',
					'desc'                => 'Demo description',
					'date'                => 1665878400,
					'date_date'           => '2022-10-16 00:00:00',
					'customer_ip'         => '',
					'currency'            => 'USD',
					'net'                 => '0.00',
					'fee'                 => '0.00',
					'discount'            => '0.00',
					'shipping'            => '0.00',
					'shipping_taxes'      => '',
					'shipping_tax'        => '0.00',
					'taxes'               => '',
					'tax'                 => '0.00',
					'total'               => '20.00',
					'date_paid'           => 1665878400,
					'date_paid_date'      => '2022-10-16 00:00:00',
					'date_completed'      => 1665878400,
					'date_completed_date' => '2022-10-16 00:00:00',
					'created'             => 1665943020,
					'created_date'        => '10/16/2022',
					'lastupdated'         => 1665943020,
					'lastupdated_date'    => '2022-10-16 17:57:00',
					'lineitems'           =>
					array(),
					'contact'             =>
					array(
						0 =>
						array(
							'id'                 => '3',
							'owner'              => '1',
							'status'             => 'Lead',
							'email'              => 'johndoe@example.com',
							'prefix'             => 'Mr',
							'fname'              => 'John',
							'lname'              => 'Doe',
							'addr1'              => '1 Sample Road',
							'addr2'              => 'Sample Town',
							'city'               => 'Berlin',
							'county'             => 'Samples',
							'country'            => 'Germany',
							'postcode'           => '10000',
							'secaddr_addr1'      => 'Second Address',
							'secaddr_addr2'      => '',
							'secaddr_city'       => 'London',
							'secaddr_county'     => 'Demo State',
							'secaddr_country'    => 'United Kingdom',
							'secaddr_postcode'   => '10003',
							'hometel'            => '123 455 32',
							'worktel'            => '222 222 22',
							'mobtel'             => '555 135 22',
							'wpid'               => '-1',
							'avatar'             => '',
							'tw'                 => '',
							'li'                 => '',
							'fb'                 => '',
							'created'            => '2022-10-05 11:30:43',
							'lastcontacted'      => -1,
							'createduts'         => '1664969443',
							'created_date'       => '10/05/2022',
							'lastupdated'        => '1664969547',
							'lastupdated_date'   => '10/05/2022',
							'lastcontacteduts'   => '-1',
							'lastcontacted_date' => false,
							'fullname'           => 'Mr John Doe',
							'name'               => 'Mr John Doe',
						),
					),
					'company'             =>
					array(
						0 =>
						array(
							'id'                 => '2',
							'owner'              => '0',
							'status'             => 'Lead',
							'name'               => 'Brand',
							'email'              => 'demo@gmail.com',
							'addr1'              => 'Baker st 20',
							'addr2'              => '',
							'city'               => 'Manchester',
							'county'             => 'Leich',
							'country'            => 'UK',
							'postcode'           => '10003',
							'secaddr1'           => 'Second address demo',
							'secaddr2'           => '',
							'seccity'            => 'Berlin',
							'seccounty'          => 'Demo state',
							'seccountry'         => 'Germany',
							'secpostcode'        => '10002',
							'maintel'            => '888 334 444',
							'sectel'             => '333 33 33',
							'wpid'               => 0,
							'avatar'             => '',
							'tw'                 => '',
							'li'                 => '',
							'fb'                 => '',
							'created'            => 1664988175,
							'created_date'       => '10/05/2022',
							'lastupdated'        => 1664988739,
							'lastupdated_date'   => '10/05/2022',
							'lastcontacted'      => 0,
							'lastcontacted_date' => false,
							'contacts'           =>
							array(),
						),
					),
					'invoice_id'          => false,
				),
			);

			return $data;
		}

	}

endif; // End if class_exists check.
