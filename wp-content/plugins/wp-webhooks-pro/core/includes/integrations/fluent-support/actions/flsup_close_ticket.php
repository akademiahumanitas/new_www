<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_fluent_support_Actions_flsup_close_ticket' ) ) :
	/**
	 * Load the flsup_close_ticket action
	 *
	 * @since 6.0.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_fluent_support_Actions_flsup_close_ticket {

		public function get_details() {
			$parameter = array(
				'id'             => array(
					'required'          => true,
					'label'             => __( 'Ticket ID', 'wp-webhooks' ),
					'short_description' => __(
						'(Integer) The ticket id.',
						'wp-webhooks'
					),
				),
				'user'           => array(
					'required'          => true,
					'label'             => __( 'User', 'wp-webhooks' ),
					'short_description' => __( '(Mixed) The user id or email of the agent that closes the ticket.', 'wp-webhooks' ),
				),
				'internal_note'  => array(
					'label'             => __( 'Internal note', 'wp-webhooks' ),
					'short_description' => __( '(Mixed) The internal note. Notes for admins to describe the reason for closing the ticket. ', 'wp-webhooks' ),
				),
				'close_silently' => array(
					'label'             => __( 'Close silently', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(
						'no'  => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					),
					'multiple'          => false,
					'default_value'     => 'no',
					'short_description' => __( '(String) Close silently. Choose yes if you want close ticket without triggering ticket closed actions. This prevents email notifications and other adjustments within Fluent Support.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action status.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further information about the request.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'Ticket has been closed successfully.',
				'data'    =>
				array(
					'id'                     => 1,
					'customer_id'            => '1',
					'agent_id'               => '3',
					'mailbox_id'             => null,
					'product_id'             => '0',
					'product_source'         => null,
					'privacy'                => 'private',
					'priority'               => 'normal',
					'client_priority'        => 'critical',
					'status'                 => 'closed',
					'title'                  => 'New ticket',
					'slug'                   => 'new-ticket',
					'hash'                   => '5dbc9d0153',
					'content_hash'           => '6ee4deac3e3b1fed1dd544757e4486af',
					'message_id'             => null,
					'source'                 => null,
					'content'                => '<p>Ticket details description.</p>',
					'secret_content'         => null,
					'last_agent_response'    => null,
					'last_customer_response' => '2022-10-28 11:20:57',
					'waiting_since'          => '2022-10-28 11:20:57',
					'response_count'         => '0',
					'first_response_time'    => null,
					'total_close_time'       => '28129',
					'resolved_at'            => '2022-10-28 19:09:46',
					'closed_by'              => 3,
					'created_at'             => '2022-10-28 11:20:57',
					'updated_at'             => '2022-10-28 20:11:02',
				),
			);

			return array(
				'action'            => 'flsup_close_ticket', // required
				'name'              => __( 'Close ticket', 'wp-webhooks' ),
				'sentence'          => __( 'close a ticket', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Close a ticket within Fluent Support.', 'wp-webhooks' ),
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

			$ticket_id      = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'id' ) );
			$user           = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$internal_note  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'internal_note' );
			$close_silently = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'close_silently' ) == 'yes' ? true : false;

			$user_id = WPWHPRO()->helpers->serve_user_id( $user );

			try {
				$agent  = \FluentSupport\App\Services\Helper::getAgentByUserId( $user_id );
				$ticket = \FluentSupport\App\Models\Ticket::findOrFail( $ticket_id );

				//Provide support with older versions as the silently argument isn't available in previous versions
				if( $close_silently ){
					$ticket_service         = ( new \FluentSupport\App\Services\Tickets\TicketService() )->close( $ticket, $agent, $internal_note, $close_silently );
				} else {
					$ticket_service         = ( new \FluentSupport\App\Services\Tickets\TicketService() )->close( $ticket, $agent, $internal_note );
				}
				
				$return_args['success'] = true;
				$return_args['msg']     = __( 'Ticket has been closed successfully.', 'wp-webhooks' );
				$return_args['data']    = $ticket_service;
			} catch ( Exception $e ) {
				$return_args['msg'] = __( $e->getMessage(), 'wp-webhooks' );
			}

			return $return_args;

		}
	}
endif;
