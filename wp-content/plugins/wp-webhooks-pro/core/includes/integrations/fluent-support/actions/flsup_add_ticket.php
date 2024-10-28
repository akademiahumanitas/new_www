<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_fluent_support_Actions_flsup_add_ticket' ) ) :
	/**
	 * Load the flsup_add_ticket action
	 *
	 * @since 6.0.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_fluent_support_Actions_flsup_add_ticket {

		public function get_details() {
			$parameter = array(
				'email'           => array(
					'required'          => true,
					'label'             => __( 'Customer email', 'wp-webhooks' ),
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
					'required'          => true,
					'label'             => __( 'Subject', 'wp-webhooks' ),
					'short_description' => __( '(String) The subject (title) of the ticket.', 'wp-webhooks' ),
				),
				'details'         => array(
					'required'          => true,
					'label'             => __( 'Ticket details', 'wp-webhooks' ),
					'short_description' => __( '(String) The ticket details (content).', 'wp-webhooks' ),
				),
				'mailbox_id'      => array(
					'label'             => __( 'Mailbox id', 'wp-webhooks' ),
					'short_description' => __( '(String) The mailbox id. If not set will assign to default Mailbox.', 'wp-webhooks' ),
				),
				'agent'           => array(
					'label'             => __( 'Agent', 'wp-webhooks' ),
					'short_description' => __( '(Mixed) The agent. You can choose email or the id of the agent user.', 'wp-webhooks' ),
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

			$returns_code = array (
				'success' => true,
				'msg' => 'Ticket has been added successfully.',
				'data' => 
				array (
				  'title' => 'This is a demo subject',
				  'content' => 'This is a demo seciton for the details.',
				  'priority' => 'critical',
				  'mailbox_id' => 0,
				  'product_id' => 0,
				  'client_priority' => false,
				  'customer_id' => 3,
				  'agent_id' => 1,
				  'slug' => 'this-is-a-demo-subject',
				  'hash' => '1188a21a94',
				  'last_customer_response' => '2022-11-06 12:17:04',
				  'content_hash' => '73fae65f5759d37e99105313e10a60a8',
				  'created_at' => '2022-11-06 12:17:04',
				  'updated_at' => '2022-11-06 12:17:04',
				  'waiting_since' => '2022-11-06 12:17:04',
				  'id' => 4,
				),
			  );

			return array(
				'action'            => 'flsup_add_ticket', // required
				'name'              => __( 'Add ticket', 'wp-webhooks' ),
				'sentence'          => __( 'add a ticket', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Add a ticket within Fluent Support.', 'wp-webhooks' ),
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
			$ticket['title']           = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'subject' );
			$ticket['content']         = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'details' );
			$ticket['priority']        = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'priority' );
			$ticket['mailbox_id']      = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'mailbox_id' ) );
			$ticket['product_id']      = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'product_id' ) );
			$ticket['client_priority'] = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'client_priority' );
			$ticket['agent']           = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'agent' );
			$ticket['status']           = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'status' );

			if ( empty( $customer['email'] ) ) {
				$return_args['msg'] = __( 'Please set the email argument to a valid email address.', 'wp-webhooks' );
				return $return_args;
			}

			$exists = \FluentSupport\App\Models\Customer::where( 'email', $customer['email'] )->first();

			if ( isset( $exists->id ) && ! empty( $exists ) && $exists instanceof \FluentSupport\App\Models\Customer ) {
				$ticket['customer_id'] = $exists->id;
			} else {
				$new_customer          = \FluentSupport\App\Models\Customer::maybeCreateCustomer( $customer );
				$ticket['customer_id'] = $new_customer->id;
			}

			if ( empty( $ticket['customer_id'] ) ) {
				$return_args['msg'] = __( 'Unable to create or find the customer.', 'wp-webhooks' );
			}

			if ( isset( $ticket['agent'] ) ) {
				$user_id = WPWHPRO()->helpers->serve_user_id( $ticket['agent'] );
				$user    = get_user_by( 'ID', $user_id );
				if ( ! empty( $user ) ) {
					$ticket['agent_id'] = $user->ID;
				}
			}

			$ticketObj      = new \FluentSupport\App\Models\Ticket();
			$created_ticket = $ticketObj->createTicket( $ticket );

			if ( ! empty( $created_ticket ) && $created_ticket instanceof \FluentSupport\App\Models\Ticket ) {
				$return_args['success'] = true;
				$return_args['msg']     = __( 'Ticket has been added successfully.', 'wp-webhooks' );
				$return_args['data']    = $created_ticket;
			} else {
				$return_args['msg'] = __( 'An error occurred while adding the ticket.', 'wp-webhooks' );
			}

			return $return_args;

		}
	}
endif;
