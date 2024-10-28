<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_jetpack_crm_Triggers_jpcrm_event_created' ) ) :

	/**
	 * Load the jpcrm_event_created trigger
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_jetpack_crm_Triggers_jpcrm_event_created {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'zbs_new_event',
					'callback'  => array( $this, 'jpcrm_event_created_callback' ),
					'priority'  => 10,
					'arguments' => 1,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {

			$parameter = array(
				'success'  => array( 'short_description' => __( '(Bool) True if the event was successfully created.', 'wp-webhooks' ) ),
				'msg'      => array( 'short_description' => __( '(String) Further details about the creation of an event.', 'wp-webhooks' ) ),
				'event_id' => array(
					'label'             => __( 'Event ID', 'wp-webhooks' ),
					'short_description' => __( '(Integer) The event id.', 'wp-webhooks' ),
				),
				'event'    => array(
					'label'             => __( 'Event data', 'wp-webhooks' ),
					'short_description' => __( '(Array) The further details about the created event.', 'wp-webhooks' ),
				),
			);

			$settings = array(
				'load_default_settings' => true,
			);

			return array(
				'trigger'           => 'jpcrm_event_created',
				'name'              => __( 'Event created', 'wp-webhooks' ),
				'sentence'          => __( 'an event was created', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires as soon as an event was created within Jetpack CRM.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'jetpack-crm',
				'premium'           => true,
			);

		}

		/**
		 * Triggers when Jetpack CRM creates an event
		 *
		 * @param $event_id Event's id
		 */
		public function jpcrm_event_created_callback( $event_id ) {

			global $zbs;

			$event = $zbs->DAL->events->getEvent( $event_id );

			$webhooks            = WPWHPRO()->webhook->get_hooks( 'trigger', 'jpcrm_event_created' );
			$response_data_array = array();

			$payload = array(
				'success'  => true,
				'msg'      => __( 'The event has been created.', 'wp-webhooks' ),
				'event_id' => $event_id,
				'event'    => $event,
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

			do_action( 'wpwhpro/webhooks/trigger_jpcrm_event_created', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array(
				'success'  => true,
				'msg'      => 'The event has been created.',
				'event_id' => 2,
				'event'    => array(
					'id'               => '24',
					'owner'            => '1',
					'title'            => 'Demo Event',
					'desc'             => 'Demo description',
					'start'            => 1665944347,
					'start_date'       => '2022-10-16 18:19:07',
					'end'              => 1665947947,
					'end_date'         => '2022-10-16 19:19:07',
					'complete'         => -1,
					'show_on_portal'   => 0,
					'show_on_cal'      => 1,
					'created'          => 1665940776,
					'created_date'     => '2022-10-16 17:19:36',
					'lastupdated'      => 1665940776,
					'lastupdated_date' => '2022-10-16 17:19:36',
					'reminders'        =>
					array(
						0 =>
						array(
							'id'               => '2',
							'owner'            => '1',
							'event'            => 24,
							'remind_at'        => -86400,
							'sent'             => -1,
							'created'          => 1665940776,
							'created_date'     => '2022-10-16 17:19:36',
							'lastupdated'      => 1665940776,
							'lastupdated_date' => '2022-10-16 17:19:36',
						),
					),
					'tags'             =>
					array(),
					'contact'          =>
					array(
						0 =>
						array(
							'id'                 => '4',
							'owner'              => '1',
							'status'             => 'Lead',
							'email'              => 'johndo3@example.com',
							'prefix'             => 'Mr',
							'fname'              => 'John',
							'lname'              => 'Doe',
							'addr1'              => '1 Sample Road',
							'addr2'              => 'Sample Town',
							'city'               => 'London',
							'county'             => 'Samples',
							'country'            => '',
							'postcode'           => '10004',
							'secaddr_addr1'      => 'Germany',
							'secaddr_addr2'      => 'Demo street',
							'secaddr_city'       => 'Berlin',
							'secaddr_county'     => 'Demo state',
							'secaddr_country'    => 'United Kingdom',
							'secaddr_postcode'   => '222222',
							'hometel'            => '123 455',
							'worktel'            => '333 333 333',
							'mobtel'             => '555 135',
							'wpid'               => '-1',
							'avatar'             => '',
							'tw'                 => '',
							'li'                 => '',
							'fb'                 => '',
							'created'            => '2022-10-05 11:33:56',
							'lastcontacted'      => -1,
							'createduts'         => '1664969636',
							'created_date'       => '10/05/2022',
							'lastupdated'        => '1664969636',
							'lastupdated_date'   => '10/05/2022',
							'lastcontacteduts'   => '-1',
							'lastcontacted_date' => false,
							'fullname'           => 'Mr John Do3',
							'name'               => 'Mr John Do3',
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
