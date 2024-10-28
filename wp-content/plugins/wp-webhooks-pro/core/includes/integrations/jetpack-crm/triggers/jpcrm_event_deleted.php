<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_jetpack_crm_Triggers_jpcrm_event_deleted' ) ) :

	/**
	 * Load the jpcrm_event_deleted trigger
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_jetpack_crm_Triggers_jpcrm_event_deleted {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'zbs_delete_event',
					'callback'  => array( $this, 'jpcrm_event_deleted_callback' ),
					'priority'  => 10,
					'arguments' => 1,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {


			$parameter = array(
				'success' => array( 'short_description' => __( '(Bool) True if the event was successfully deleted.', 'wp-webhooks' ) ),
                'msg' => array( 'short_description' => __( '(String) Further details about the deletion of an event.', 'wp-webhooks' ) ),
				'event_id'   => array(
					'label'             => __( 'Event ID', 'wp-webhooks' ),
					'short_description' => __( '(Integer) The event id.', 'wp-webhooks' ),
				),
			);

			$settings = array(
				'load_default_settings' => true,
			);

			return array(
				'trigger'           => 'jpcrm_event_deleted',
				'name'              => __( 'Event deleted', 'wp-webhooks' ),
				'sentence'          => __( 'an event was deleted', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires as soon as an event was deleted within Jetpack CRM.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'jetpack-crm',
				'premium'           => true,
			);

		}

		/**
		 * Triggers when Jetpack CRM deletes an event
		 *
		 * @param $event_id Event's id
		 */
		public function jpcrm_event_deleted_callback($event_id) {

			$webhooks            = WPWHPRO()->webhook->get_hooks( 'trigger', 'jpcrm_event_deleted' );
			$response_data_array = array();
			
			$payload = array(
				'success' => true,
				'msg'     => __( 'The event has been deleted.', 'wp-webhooks' ),
				'event_id' => $event_id
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

			do_action( 'wpwhpro/webhooks/trigger_jpcrm_event_deleted', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array(
				'success' => true,
				'event_id' => 22,
				'msg'     => 'The event has been deleted.',
			);

			return $data;
		}

	}

endif; // End if class_exists check.
