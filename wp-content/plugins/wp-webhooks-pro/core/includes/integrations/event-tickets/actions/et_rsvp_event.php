<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_event_tickets_Actions_et_rsvp_event' ) ) :
	/**
	 * Load the et_rsvp_event action
	 *
	 * @since 6.1.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_event_tickets_Actions_et_rsvp_event {

		public function get_details() {
			$parameter = array(
				'event_id'       => array(
					'required'          => true,
					'label'             => __( 'Event ID', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(),
					'query'             => array(
						'filter' => 'posts',
						'args'   => array(
							'post_type' => 'tribe_events',
						),
					),
					'short_description' => __(
						'(String) The event ID.',
						'wp-webhooks'
					),
				),
				'attendee_name'  => array(
					'required'          => false,
					'label'             => __( 'Attendee name', 'wp-webhooks' ),
					'short_description' => __(
						'(String) The name of the attendee.',
						'wp-webhooks'
					),
				),
				'attendee_email' => array(
					'required'          => true,
					'label'             => __( 'Attendee', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(),
					'query'             => array(
						'filter' => 'users',
						'args'   => array(),
					),
					'short_description' => __(
						'(String) The email of the attendee. You can also select a user from the given list. We then select the email automatically.',
						'wp-webhooks'
					),
				),
				'rsvp_ticket_id' => array(
					'required'          => true,
					'label'             => __( 'RSVP ticket', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(),
					'query'             => array(
						'filter' => 'posts',
						'args'   => array(
							'post_type' => 'tribe_rsvp_tickets',
						),
					),
					'short_description' => __( '(Integer) The RSVP ticket ID. You should choose the ticket ID which belongs to the selected event.', 'wp-webhooks' ),
				),
				'quantity'       => array(
					'label'             => __( 'Quantity', 'wp-webhooks' ),
					'short_description' => __( '(Integer) The ticket quantity.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action status.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further information about the request.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'Spots have been successfully reserved via RSVP tickets.',
				'data'    =>
				array(
					'rsvp_tickets' =>
					array(
						0 => 485,
						1 => 486,
					),
				),
			);

			return array(
				'action'            => 'et_rsvp_event', // required
				'name'              => __( 'RSVP event', 'wp-webhooks' ),
				'sentence'          => __( 'RSVP an event', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'RSVP on behalf of an attendee within Event Tickets.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'event-tickets',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
				'data'    => array(),
			);

			$event_id        = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'event_id' ) );
			$attendee_name   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'attendee_name' );
			$attendee_email  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'attendee_email' );
			$ticket_quantity = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'quantity' );
			$attendee_qty    = isset( $ticket_quantity ) ? intval( $ticket_quantity ) : 1;
			$ticket_id       = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'rsvp_ticket_id' );

			if ( empty( $attendee_name ) ) {
				$return_args['msg'] = __( 'Please set the attendee name argument.', 'wp-webhooks' );
				return $return_args;
			}

			if ( empty( $attendee_email ) ) {
				$return_args['msg'] = __( 'Please set the attendee argument.', 'wp-webhooks' );
				return $return_args;
			}

			$event_post     = get_post( $event_id );
			$ticket_handler = new Tribe__Tickets__Tickets_Handler();
			$rsvp_tickets   = $ticket_handler->get_event_rsvp_tickets( $event_post );

			if ( empty( $rsvp_tickets ) ) {
				$return_args['msg'] = __( 'There are no RSVP tickets available for the selected event.', 'wp-webhooks' );
				return $return_args;
			}

			$user_id = 0;
			$user    = $attendee_email;

			if ( ! empty( $user ) ) {
				$user_id = WPWHPRO()->helpers->serve_user_id( $user );
			}

			if ( $user_id > 0 ) {
				$user           = get_userdata( $user_id );
				$attendee_email = $user->user_email;
				$attendee_name  = $user->display_name;
			}

			$product_id = 0;

			$rsvp       = tribe( 'tickets.rsvp' );
			$post_id    = $event_id;
			$order_id   = \Tribe__Tickets__RSVP::generate_order_id();
			$product_id = $ticket_id;

			$attendee_details = array(
				'full_name'    => $attendee_name,
				'email'        => $attendee_email,
				'order_status' => 'yes',
				'optout'       => false,
				'order_id'     => $order_id,
			);

			$has_tickets = $rsvp->generate_tickets_for( $product_id, $attendee_qty, $attendee_details, false );

			if ( is_wp_error( $has_tickets ) ) {
				$return_args['msg'] = $has_tickets->get_error_message();
				return $return_args;
			}

			/**
			 * Fires when RSVP attendee tickets have been generated.
			 *
			 * @param int $order_id ID of the RSVP order
			 * @param int $post_id ID of the post the order was placed for
			 * @param string $attendee_order_status 'yes' if the user indicated they will attend
			 */
			do_action( 'event_tickets_rsvp_tickets_generated', $order_id, $post_id, 'yes' );

			$send_mail_stati = array( 'yes' );

			/**
			 * Filters whether a confirmation email should be sent or not for RSVP tickets.
			 *
			 * This applies to attendance and non attendance emails.
			 *
			 * @param bool $send_mail Defaults to `true`.
			 */
			$send_mail = apply_filters( 'tribe_tickets_rsvp_send_mail', true );

			if ( $send_mail && $has_tickets ) {
				/**
				 * Filters the attendee order stati that should trigger an attendance confirmation.
				 *
				 * Any attendee order status not listed here will trigger a non attendance email.
				 *
				 * @param array $send_mail_stati An array of default stati triggering an attendance email.
				 * @param int $order_id ID of the RSVP order
				 * @param int $post_id ID of the post the order was placed for
				 * @param string $attendee_order_status 'yes' if the user indicated they will attend
				 */
				$send_mail_stati = apply_filters(
					'tribe_tickets_rsvp_send_mail_stati',
					$send_mail_stati,
					$order_id,
					$post_id,
					'yes'
				);

				// No point sending tickets if their current intention is not to attend
				if ( in_array( 'yes', $send_mail_stati, true ) ) {
					$rsvp->send_tickets_email( $order_id, $post_id );
				} else {
					$rsvp->send_non_attendance_confirmation( $order_id, $post_id );
				}
			}

			$return_args['success']              = true;
			$return_args['msg']                  = __( 'Spots have been successfully reserved via RSVP tickets.', 'wp-webhooks' );
			$return_args['data']['rsvp_tickets'] = $has_tickets;

			return $return_args;

		}


	}
endif;
