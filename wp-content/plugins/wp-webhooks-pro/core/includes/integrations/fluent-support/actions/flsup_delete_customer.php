<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_fluent_support_Actions_flsup_delete_customer' ) ) :
	/**
	 * Load the flsup_delete_customer action
	 *
	 * @since 6.0.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_fluent_support_Actions_flsup_delete_customer {

		public function get_details() {
			$parameter = array(
				'customer'       => array(
					'required'          => true,
					'label'             => __( 'Customer', 'wp-webhooks' ),
					'short_description' => __( '(Mixed) The id or email of the customer to delete.', 'wp-webhooks' ),
				),
				'remove_tickets' => array(
					'label'             => __( 'Remove tickets', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(
						'no'  => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					),
					'multiple'          => false,
					'default_value'     => 'no',
					'short_description' => __( '(String) Remove tickets. Choose yes if you want remove tickets associated with the customer. ', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action status.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further information about the request.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'Customer has been deleted successfully.',
				'data'    =>
				array(
					'customer_id' => 10,
					'tickets'     =>
					array(
						'ids' =>
						array(
							0 => 13,
							1 => 14,
						),
					),
				),
			);

			return array(
				'action'            => 'flsup_delete_customer', // required
				'name'              => __( 'Delete customer', 'wp-webhooks' ),
				'sentence'          => __( 'delete a customer', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Delete a customer within Fluent Support.', 'wp-webhooks' ),
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

			if ( ! class_exists( '\FluentSupport\App\Models\Customer' ) ) {
				return $return_args['msg'] = __( 'The class \FluentSupport\App\Models\Customer does not exist.', 'wp-webhooks' );
			}

			$customer        = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer' );
			$remove_tickets  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'remove_tickets' ) === 'yes' ? true : false;
			$customer_id     = '';
			$deleted_tickets = array();

			if ( is_email( $customer ) ) {
				$customer    = \FluentSupport\App\Models\Customer::where( 'email', $customer )->first();
				$customer_id = $customer->id;

				if ( ! empty( $customer ) ) {
					$customer_id = $customer->id;
				} else {
					$return_args['msg'] = __( 'A customer with the given email has not been found.', 'wp-webhooks' );
					return $return_args;
				}
			} elseif ( is_numeric( $customer ) ) {
				$customer_id = intval( $customer );
				$customer    = \FluentSupport\App\Models\Customer::where( 'id', $customer_id )->first();

				if ( empty( $customer ) ) {
					$return_args['msg'] = __( 'A customer with the given id has not been found.', 'wp-webhooks' );
					return $return_args;
				}
			}

			if ( $remove_tickets ) {
				$tickets = \FluentSupport\App\Models\Ticket::where( 'customer_id', $customer_id );
				foreach ( $tickets->get() as $ticket ) {
					$deleted_tickets['ids'][] += $ticket->id;
					( new \FluentSupport\App\Hooks\Handlers\CleanupHandler() )->deleteTicketAttachments( $ticket );
				}
				$tickets->delete();
			}

			$customer->delete();

			$return_args['success']             = true;
			$return_args['msg']                 = __( 'Customer has been deleted successfully.', 'wp-webhooks' );
			$return_args['data']['customer_id'] = $customer_id;
			$return_args['data']['tickets']     = $deleted_tickets;

			return $return_args;

		}
	}
endif;
