<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_event_tickets_Helpers_event_tickets_helpers' ) ) :

	/**
	 * Load the Event Tickets helpers
	 *
	 * @since 6.1.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_event_tickets_Helpers_event_tickets_helpers {
        //For future updates - dynamic fields event_id->ticket_id
		public function get_query_rsvp_tickets( $entries, $query_args, $args ) {

			// bail for paged values as everything is returned at once
			if ( isset( $args['paged'] ) && (int) $args['paged'] > 1 ) {
				return $entries;
			}

			$tickets      = array();
			if ( class_exists( '\Tribe__Tickets__Tickets' ) ) {
				$tickets = \Tribe__Tickets__Tickets::get_all_event_tickets( 0 );
			}

			if ( ! empty( $tickets ) ) {
				foreach ( $tickets as $ticket ) {

					if ( 'Tribe__Tickets__RSVP' !== $ticket->provider_class ) {
						continue;
					}
					$name  = ( isset( $ticket->ID ) ) ? $ticket->ID : '';
					$title = ( isset( $ticket->name ) ) ? $ticket->name : '';

					// skip search values that don't occur if set
					if ( isset( $args['s'] ) && $args['s'] !== '' ) {
						if ( strpos( $name, $args['s'] ) === false
							&& strpos( $title, $args['s'] ) === false
						) {
							continue;
						}
					}

					// skip unselected values in a selected statement
					if ( isset( $args['selected'] ) && ! empty( $args['selected'] ) ) {
						if ( ! in_array( $name, $args['selected'] ) ) {
							continue;
						}
					}

					$entries['items'][ $name ] = array(
						'value' => $name,
						'label' => $title,
					);
				}
			}

            // calculate total
			$entries['total'] = count( $entries['items'] );

			// set all items to be visible on one page
			$entries['per_page'] = count( $entries['items'] );

			return $entries;
		}

	}

endif; // End if class_exists check.
