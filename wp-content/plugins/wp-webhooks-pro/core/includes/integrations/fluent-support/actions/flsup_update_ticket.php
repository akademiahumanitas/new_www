<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_fluent_support_Actions_flsup_update_ticket' ) ) :
	/**
	 * Load the flsup_update_ticket action
	 *
	 * @since 6.0.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_fluent_support_Actions_flsup_update_ticket {

		public function get_details() {
			$parameter = array(
				'id'              => array(
					'required'          => true,
					'label'             => __( 'The ticket id', 'wp-webhooks' ),
					'short_description' => __( '(Mixed) The id of the ticket to update.', 'wp-webhooks' ),
				),
				'email'           => array(
					'label'             => __( 'Customer Email', 'wp-webhooks' ),
					'short_description' => __(
						'(String) The customer email. You can provide an existing email address of a customer. If the customer doesn\'t exist, a new customer will be added by the given email argument.',
						'wp-webhooks'
					),
				),
				'first_name'      => array(
					'label'             => __( 'First name', 'wp-webhooks' ),
					'short_description' => __( '(String) The first name of the customer (In case the customer does not exist).', 'wp-webhooks' ),
				),
				'last_name'       => array(
					'label'             => __( 'Last name', 'wp-webhooks' ),
					'short_description' => __( '(String) The last name of the customer (In case the customer does not exist).', 'wp-webhooks' ),
				),
				'subject'         => array(
					'label'             => __( 'Subject', 'wp-webhooks' ),
					'short_description' => __( '(String) The subject (title) of the ticket.', 'wp-webhooks' ),
				),
				'details'         => array(
					'label'             => __( 'Ticket details', 'wp-webhooks' ),
					'short_description' => __( '(String) The ticket details (content).', 'wp-webhooks' ),
				),
				'agent'           => array(
					'label'             => __( 'Agent', 'wp-webhooks' ),
					'short_description' => __( '(Mixed) The agent. You can choose either the email or the ID of the agent.', 'wp-webhooks' ),
				),
				'product_id'      => array(
					'label'             => __( 'Product ID', 'wp-webhooks' ),
					'short_description' => __( '(Integer) The product ID.', 'wp-webhooks' ),
				),
				'priority'        => array(
					'label'             => __( 'Admin priority', 'wp-webhooks' ),
					'type'              => 'select',
					'default_value'     => 'normal',
					'choices'           => array(),
					'query'			=> array(
						'filter'	=> 'helpers',
						'args'		=> array(
							'integration' => 'fluent-support',
							'helper' => 'flsup_helpers',
							'function' => 'get_query_ticket_priorities',
						)
					),
					'short_description' => __( '(String) The priority of the ticket.', 'wp-webhooks' ),
				),
				'client_priority' => array(
					'label'             => __( 'The priority level set by the client', 'wp-webhooks' ),
					'type'              => 'select',
					'default_value'     => 'normal',
					'choices'           => array(),
					'query'			=> array(
						'filter'	=> 'helpers',
						'args'		=> array(
							'integration' => 'fluent-support',
							'helper' => 'flsup_helpers',
							'function' => 'get_query_client_ticket_priorities',
						)
					),
					'short_description' => __( '(String) The priority of a customer.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action status.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further information about the request.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'Ticket has been updated successfully.',
				'data'    =>
				array(
					'id'                     => 24,
					'customer_id'            => '11',
					'agent_id'               => '1',
					'mailbox_id'             => '1',
					'product_id'             => '1',
					'product_source'         => 'local',
					'privacy'                => 'private',
					'priority'               => 'Critical',
					'client_priority'        => 'Critical',
					'status'                 => 'new',
					'title'                  => 'Demo ticket',
					'slug'                   => 'demo-ticket',
					'hash'                   => 'cd1df8e553',
					'content_hash'           => 'f79a3e10a799742534e5e92b0dd7ace3',
					'message_id'             => null,
					'source'                 => null,
					'content'                => 'Demo details',
					'secret_content'         => null,
					'last_agent_response'    => null,
					'last_customer_response' => '2022-10-29 16:11:50',
					'waiting_since'          => '2022-10-29 16:11:50',
					'response_count'         => '0',
					'first_response_time'    => null,
					'total_close_time'       => null,
					'resolved_at'            => null,
					'closed_by'              => null,
					'created_at'             => '2022-10-29 16:11:50',
					'updated_at'             => '2022-10-29 16:11:50',
				),
			);

			return array(
				'action'            => 'flsup_update_ticket', // required
				'name'              => __( 'Update ticket', 'wp-webhooks' ),
				'sentence'          => __( 'update a ticket', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Update a ticket within Fluent Support.', 'wp-webhooks' ),
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

			$customer                  = array();
			$ticket                    = array();
			$customer['email']         = sanitize_email( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'email' ) );
			$customer['first_name']    = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'first_name' );
			$customer['last_name']     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'last_name' );
			$ticket['id']              = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'id' ) );
			$ticket['title']           = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'subject' );
			$ticket['content']         = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'details' );
			$ticket['priority']        = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'priority' );
			$ticket['product_id']      = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'product_id' ) );
			$ticket['client_priority'] = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'client_priority' );
			$ticket['mailbox_id']      = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'mailbox_id' ) );
			$ticket['agent_id']        = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'agent' );

			$ticketObj = \FluentSupport\App\Models\Ticket::where( 'id', $ticket['id'] )->first();

			if ( empty( $ticketObj ) ) {
				$return_args['msg'] = __( 'Ticket with the given ID has not been found.', 'wp-webhooks' );
				return $return_args;
			}

			$exists = \FluentSupport\App\Models\Customer::where( 'email', $customer['email'] )->first();

			if ( ! empty( $exists ) && $exists instanceof \FluentSupport\App\Models\Customer ) {
				$ticket['customer_id'] = intval( $exists->id );
			} else {
				$new_customer          = \FluentSupport\App\Models\Customer::maybeCreateCustomer( $customer );
				$ticket['customer_id'] = intval( $new_customer->id );
			}

			if ( empty( $ticket['customer_id'] ) ) {
				$return_args['msg'] = __( 'Unable to create or find the customer.', 'wp-webhooks' );
			}

			if ( isset( $ticket['agent_id'] ) ) {
				$user_id = WPWHPRO()->helpers->serve_user_id( $ticket['agent_id'] );
				$user    = get_user_by( 'ID', $user_id );
				if ( ! empty( $user ) ) {
					$ticket['agent_id'] = intval( $user->ID );
				}
			}

			foreach ( $ticket as $propName => $propValue ) {
				try {
					$prevValue = $ticketObj->{$propName};
					if ( $propName && $propValue && $prevValue != $propValue ) {
						$ticketObj->{$propName} = $propValue;
						$ticketObj->save();
					}
				} catch ( Exception $e ) {
					$return_args['msg'] = __( $e->getMessage(), 'wp-webhooks' );
					return $return_args;
				}
			}

			if ( ! empty( $ticketObj ) && $ticketObj instanceof \FluentSupport\App\Models\Ticket ) {
				$return_args['success'] = true;
				$return_args['msg']     = __( 'The ticket has been updated successfully.', 'wp-webhooks' );
				$return_args['data']    = $ticketObj;
			} else {
				$return_args['msg'] = __( 'An error occurred while updating the ticket.', 'wp-webhooks' );
			}

			return $return_args;

		}
	}
endif;
