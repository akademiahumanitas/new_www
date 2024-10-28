<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_jetpack_crm_Triggers_jpcrm_quote_created' ) ) :

	/**
	 * Load the jpcrm_quote_created trigger
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_jetpack_crm_Triggers_jpcrm_quote_created {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'zbs_new_quote',
					'callback'  => array( $this, 'jpcrm_quote_created_callback' ),
					'priority'  => 10,
					'arguments' => 1,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {

			$parameter = array(
				'success'  => array( 'short_description' => __( '(Bool) True if the quote was successfully created.', 'wp-webhooks' ) ),
				'msg'      => array( 'short_description' => __( '(String) Further details about the creation of a quote.', 'wp-webhooks' ) ),
				'quote_id' => array(
					'label'             => __( 'Quote ID', 'wp-webhooks' ),
					'short_description' => __( '(Integer) The quote id.', 'wp-webhooks' ),
				),
				'quote'    => array(
					'label'             => __( 'Quote data', 'wp-webhooks' ),
					'short_description' => __( '(Array) The further details about the created quote.', 'wp-webhooks' ),
				),
			);

			$settings = array(
				'load_default_settings' => true,
			);

			return array(
				'trigger'           => 'jpcrm_quote_created',
				'name'              => __( 'Quote created', 'wp-webhooks' ),
				'sentence'          => __( 'a quote was created', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires as soon as a quote was created within Jetpack CRM.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'jetpack-crm',
				'premium'           => true,
			);

		}

		/**
		 * Triggers when Jetpack CRM creates a quote
		 *
		 * @param $quote_id Quote's id
		 */
		public function jpcrm_quote_created_callback( $quote_id ) {

			global $zbs;

			$quote = $zbs->DAL->quotes->getQuote( $quote_id );

			$webhooks            = WPWHPRO()->webhook->get_hooks( 'trigger', 'jpcrm_quote_created' );
			$response_data_array = array();

			$payload = array(
				'success'  => true,
				'msg'      => __( 'The quote has been created.', 'wp-webhooks' ),
				'quote_id' => $quote_id,
				'quote'    => $quote,
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

			do_action( 'wpwhpro/webhooks/trigger_jpcrm_quote_created', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array(
				'success'  => true,
				'msg'      => 'The quote has been created.',
				'quote_id' => 3,
				'quote'    => array(
					'id'               => '2',
					'owner'            => '1',
					'id_override'      => '',
					'title'            => 'Demo Quote',
					'currency'         => '',
					'value'            => '500.00',
					'date'             => 1665705600,
					'date_date'        => '10/14/2022',
					'template'         => 1,
					'content'          => '',
					'notes'            => 'Demo quote notes',
					'send_attachments' => true,
					'hash'             => 'LTcewXCT5ef9IsLaaTa',
					'lastviewed'       => 0,
					'lastviewed_date'  => false,
					'viewed_count'     => 0,
					'accepted'         => 0,
					'accepted_date'    => false,
					'acceptedsigned'   => 0,
					'acceptedip'       => 0,
					'created'          => 1665942570,
					'created_date'     => '10/16/2022',
					'lastupdated'      => 1665942570,
					'lastupdated_date' => '10/16/2022',
					'status'           => -2,
					'lineitems'        =>
					array(),
					'contact'          =>
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
							'secaddr_addr1'      => 'Demo address 1',
							'secaddr_addr2'      => 'Demo address 2',
							'secaddr_city'       => 'London',
							'secaddr_county'     => 'Demo State',
							'secaddr_country'    => 'United Kingdom',
							'secaddr_postcode'   => '222222',
							'hometel'            => '123 455',
							'worktel'            => '333 33 33',
							'mobtel'             => '555 15 22',
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
							'fullname'           => 'Mr John Do2',
							'name'               => 'Mr John Do2',
						),
					),
					'company'          =>
					array(),
				),

			);

			return $data;
		}

	}

endif; // End if class_exists check.
