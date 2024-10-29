<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_fluent_support_Actions_flsup_delete_ticket' ) ) :
	/**
	 * Load the flsup_delete_ticket action
	 *
	 * @since 6.0.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_fluent_support_Actions_flsup_delete_ticket {

		public function get_details() {
			$parameter = array(
				'tickets' => array(
					'required'          => true,
					'label'             => __( 'Ticket IDs', 'wp-webhooks' ),
					'short_description' => __( '(String) The IDs of the tickets you want to delete. This arument supports a JSON formatted string or the comma-separated IDs.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action status.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further information about the request data.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The ticket(s) have been deleted successfully.',
				'data'    =>
				array(
					'tickets' =>
					array(
						0 => 20,
						1 => 21,
					),
				),
			);

			return array(
				'action'            => 'flsup_delete_ticket', // required
				'name'              => __( 'Delete tickets', 'wp-webhooks' ),
				'sentence'          => __( 'delete one or multiple tickets', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Delete one or multiple tickets within Fluent Support.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'fluent-support',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
				'data'    => array(),
			);

			if ( ! class_exists( '\FluentSupport\App\Models\Ticket' ) ) {
				return $return_args['msg'] = __( 'The class \FluentSupport\App\Models\Ticket does not exist.', 'wp-webhooks' );
			}

			$ticket_ids = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tickets' );
			if ( isset( $ticket_ids ) ) {

				if ( WPWHPRO()->helpers->is_json( $ticket_ids ) ) {
					$tickets = json_decode( $ticket_ids, true );
				} else {
					$tickets = array_map( 'intval', explode( ',', $ticket_ids ) );

				}
			}

			$deleted_tickets = array();
			if ( is_array( $tickets ) ) {
				foreach ( $tickets as $key => $ticket_id ) {
					$ticket = \FluentSupport\App\Models\Ticket::where( 'id', $ticket_id )->first();
					if ( ! empty( $ticket ) ) {
						( new \FluentSupport\App\Hooks\Handlers\CleanupHandler() )->deleteTicketAttachments( $ticket );
						$deleted_tickets[] += $ticket->id;
						$ticket->delete();
					} else {
						$return_args['msg'] = __( 'Tickets are not found.', 'wp-webhooks' );
						return $return_args;
					}
				}
			}

			$return_args['success']         = true;
			$return_args['msg']             = __( 'The ticket(s) have been deleted successfully.', 'wp-webhooks' );
			$return_args['data']['tickets'] = $deleted_tickets;

			return $return_args;

		}
	}
endif;
