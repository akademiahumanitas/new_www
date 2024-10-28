<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_event_tickets_Triggers_et_attendee_registered_event' ) ) :

	/**
	 * Load the et_attendee_registered_event trigger
	 *
	 * @since 6.1.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_event_tickets_Triggers_et_attendee_registered_event {


		public function get_callbacks() {
			return array(
				array(
					'type'      => 'action',
					'hook'      => 'event_tickets_rsvp_attendee_created',
					'callback'  => array( $this, 'et_attendee_registered_event_callback' ),
					'priority'  => 10,
					'arguments' => 4,
					'delayed'   => true,
				),
				array(
					'type'      => 'action',
					'hook'      => 'event_tickets_tpp_attendee_created',
					'callback'  => array( $this, 'et_attendee_registered_event_callback' ),
					'priority'  => 10,
					'arguments' => 5,
					'delayed'   => true,
				),
				array(
					'type'      => 'action',
					'hook'      => 'event_tickets_tpp_attendee_updated',
					'callback'  => array( $this, 'et_attendee_registered_event_callback' ),
					'priority'  => 10,
					'arguments' => 5,
					'delayed'   => true,
				),
				array(
					'type'      => 'action',
					'hook'      => 'tec_tickets_commerce_attendee_after_create',
					'callback'  => array( $this, 'et_attendee_registered_event_callback' ),
					'priority'  => 10,
					'arguments' => 4,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {
			$parameter = array(
				'success' => array( 'short_description' => __( '(Bool) True if the attendee successfully registered for an event.', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the attendee registered for an event.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) The details about the attendee registered for an event.', 'wp-webhooks' ) ),
			);

			$settings = array(
				'load_default_settings' => true,
			);

			$description = array(
				'tipps' => array(
					__( 'Please note that this webhook can fire multiple times, depending on the number of attendees selected for RSVP. For example: If the user comes with two additional people, this webhook fires three times.', 'wp-webhooks' ),
				),
			);

			return array(
				'trigger'           => 'et_attendee_registered_event',
				'name'              => __( 'Attendee registered event', 'wp-webhooks' ),
				'sentence'          => __( 'an attendee registered for an event', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires as soon as an attendee registered for an event.', 'wp-webhooks' ),
				'description'       => $description,
				'integration'       => 'event-tickets',
				'premium'           => true,
			);
		}

		/**
		 * Triggers when attendee registered for an event
		 *
		 * @param $package
		 */
		public function et_attendee_registered_event_callback( $attendee_id, $order, $ticket, $order_attendee_id, $attendee_order_status = null ) {

			$webhooks            = WPWHPRO()->webhook->get_hooks( 'trigger', 'et_attendee_registered_event' );
			$response_data_array = array();


			if ( is_object( $attendee_id ) && 'tec_tickets_commerce_attendee_after_create' === (string) current_action() ) {
				$attendee_id = intval( $attendee_id->ID );
			}

			if ( ! $attendee_id ) {
				return;
			}

			$attendee_details = tribe_tickets_get_attendees( $attendee_id );

			if ( empty( $attendee_details ) ) {
				return;
			}

			$attendees = array();

			foreach( $attendee_details as $detail ){
				$event_id = $detail['event_id'];
				$event      = get_post( $event_id );
				$attendees[] = array(
					'event_title'              => $event->post_title,
					'event_id'                 => $event->ID,
					'event_url'                => get_permalink( $event->ID ),
					'event_featured_image_id'     => get_post_thumbnail_id( $event->ID ),
					'event_featured_image_url' => get_the_post_thumbnail_url( $event->ID ),
					'attendee_name' => $detail['holder_name'],
					'attendee_email' => $detail['holder_email'],
				);
			}


			$payload = array(
				'success' => true,
				'msg'     => __( 'The attendee registered for an event succesfully.', 'wp-webhooks' ),
				'data'    => array(
					'attendee' => ( isset( $attendees[0] ) ) ? $attendees[0] : array(),
				),
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

			do_action( 'wpwhpro/webhooks/trigger_et_attendee_registered_event', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {
			$data = array (
				'success' => true,
				'msg' => 'The attendee registered for an event succesfully.',
				'data' => 
				array (
				  'attendee' => 
				  array (
					'event_title' => 'DemoEvent 91',
					'event_id' => 474,
					'event_url' => 'https://demodomain.test/website/event/demoevent-99-3/',
					'event_featured_image_id' => 56,
					'event_featured_image_url' => 'https://demodomain.test/website/wp-content/uploads/2022/08/demo-image.png',
					'attendee_name' => 'John Doe',
					'attendee_email' => 'johndoe@demodomain.test',
				  ),
				),
			);

			return $data;
		}
	}

endif; // End if class_exists check.
