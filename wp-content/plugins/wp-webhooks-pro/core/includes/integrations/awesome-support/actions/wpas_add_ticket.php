<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_awesome_support_Actions_wpas_add_ticket' ) ) :
	/**
	 * Load the wpas_add_ticket action
	 *
	 * @since 6.0.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_awesome_support_Actions_wpas_add_ticket {

		public function get_details() {
			$parameter = array(
				'user'          => array(
					'required'          => true,
					'label'             => __( 'User', 'wp-webhooks' ),
					'short_description' => __(
						'(String) The user ID or email of the ticket creator. You can provide an existing email address or an ID of a user.',
						'wp-webhooks'
					),
				),
				'title'         => array(
					'required'          => true,
					'label'             => __( 'Title', 'wp-webhooks' ),
					'short_description' => __( '(String) The title of the ticket.', 'wp-webhooks' ),
				),
				'message'       => array(
					'required'          => true,
					'label'             => __( 'Ticket message', 'wp-webhooks' ),
					'short_description' => __( '(String) The ticket content details.', 'wp-webhooks' ),
				),
				'agent'         => array(
					'label'             => __( 'Support staff', 'wp-webhooks' ),
					'type'              => 'select',
					'default_value'     => 'normal',
					'choices'           => array(),
					'query'             => array(
						'filter' => 'users',
						'args'   => array(
							'role__in' => array( 'wpas_agent', 'wpas_manager', 'wpas_support_manager' ),
						),
					),
					'short_description' => __( '(Mixed) The agent ID or email. You can choose email or the ID of the agent.', 'wp-webhooks' ),
				),
				'product_id'       => array(
					'label'             => __( 'Product ID', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(),
					'query'             => array(
						'filter' => 'terms',
						'args'   => array(
							'taxonomy'   => 'product',
							'orderby'    => 'name',
							'order'      => 'ASC',
							'hide_empty' => false,
						),
					),
					'short_description' => __( '(Integer) The product ID.', 'wp-webhooks' ),
				),
				'priority_id'      => array(
					'label'             => __( 'Ticket priority ID', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(),
					'query'             => array(
						'filter' => 'terms',
						'args'   => array(
							'taxonomy'   => 'ticket_priority',
							'orderby'    => 'name',
							'order'      => 'ASC',
							'hide_empty' => false,

						),
					),
					'short_description' => __( '(Integer) The priority ID of the ticket.', 'wp-webhooks' ),
				),
				'department_id'    => array(
					'label'             => __( 'Department.', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(),
					'query'             => array(
						'filter' => 'terms',
						'args'   => array(
							'taxonomy'   => 'department',
							'orderby'    => 'name',
							'order'      => 'ASC',
							'hide_empty' => false,
						),
					),
					'short_description' => __( '(Integer) The department ID where ticket belongs.', 'wp-webhooks' ),
				),
				'channel_id'       => array(
					'label'             => __( 'Channel.', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(),
					'query'             => array(
						'filter' => 'terms',
						'args'   => array(
							'taxonomy'   => 'ticket_channel',
							'orderby'    => 'name',
							'order'      => 'ASC',
							'hide_empty' => false,
						),
					),
					'short_description' => __( '(Integer) The channel ID.', 'wp-webhooks' ),
				),
				'tags'          => array(
					'label'             => __( 'Ticket tags', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(),
					'query'             => array(
						'filter' => 'terms',
						'args'   => array(
							'taxonomy'   => 'ticket-tag',
							'orderby'    => 'name',
							'order'      => 'ASC',
							'hide_empty' => false,
						),
					),
					'multiple'           => true,
					'short_description'  => __( '(String) The ticket tags. You can seperate tags with commas.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action status.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further information about the request.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'Ticket has been added successfully.',
				'data'    => array(
					'ID'                    => 338,
					'post_author'           => '15',
					'post_date'             => '2022-11-15 09:01:52',
					'post_date_gmt'         => '2022-11-15 09:01:52',
					'post_content'          => 'This is the demo content of the ticket.',
					'post_title'            => 'Demo Ticket',
					'post_excerpt'          => '',
					'post_status'           => 'queued',
					'comment_status'        => 'closed',
					'ping_status'           => 'closed',
					'post_password'         => '',
					'post_name'             => 'new_ticket2',
					'to_ping'               => '',
					'pinged'                => '',
					'post_modified'         => '2022-11-15 09:01:52',
					'post_modified_gmt'     => '2022-11-15 09:01:52',
					'post_content_filtered' => '',
					'post_parent'           => 0,
					'guid'                  => 'https://demo-site.test/ticket/new_ticket2/',
					'menu_order'            => 0,
					'post_type'             => 'ticket',
					'post_mime_type'        => '',
					'comment_count'         => '0',
					'filter'                => 'raw',
				),

			);

			$description = array(
				'tipps' => array(
					__( 'Some of the features within this action might have to be activated within Awesome Support first. Please check the settings of Awesome Support.', 'wp-webhooks' ),
				),
			);

			return array(
				'action'            => 'wpas_add_ticket', // required
				'name'              => __( 'Add ticket', 'wp-webhooks' ),
				'sentence'          => __( 'Add a ticket', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Add a ticket within Awesome Support.', 'wp-webhooks' ),
				'description'       => $description,
				'integration'       => 'awesome-support',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
				'data'    => array(),
			);

			$user     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$agent    = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'agent' );

			$user_id  = WPWHPRO()->helpers->serve_user_id( $user );
			$agent_id = WPWHPRO()->helpers->serve_user_id( $agent );

			$user  = get_user_by( 'id', $user_id );
			$agent = get_user_by( 'id', $agent_id );

			$allowed_roles = array( 'administrator', 'wpas_agent', 'wpas_manager', 'wpas_support_manager' );
		
			if ( ! array_intersect( $allowed_roles, $user->roles ) || ! array_intersect( $allowed_roles, $agent->roles ) ) {
				$return_args['msg'] = __( 'The user or the agent doesn\'t have permissions to open or review a ticket.', 'wp_webhooks' );
				return $return_args;
			}

			$ticket_tags = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tags' );

			if ( isset( $ticket_tags ) ) {
				if ( WPWHPRO()->helpers->is_json( $ticket_tags ) ) {
					$tags = json_decode( $ticket_tags, true );
				} else {
					$tags = preg_split( '/(\s*,*\s*)*,+(\s*,*\s*)*/', $ticket_tags );
				}
			}

			$custom_fields                    = array();
			$custom_fields['ticket-tag']      = $tags;
			$custom_fields['product']         = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'product_id' ) );
			$custom_fields['ticket_priority'] = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'priority_id' ) );
			$custom_fields['ticket_channel']  = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'channel_id' ) );
			$custom_fields['department']      = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'department_id' ) );

			$ticket                   = array();
			$ticket['post_author']    = $user_id;
			$ticket['post_title']     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'title' );
			$ticket['post_name']      = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'title' );
			$ticket['post_content']   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'message' );
			$ticket['agent']          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'agent' );
			$ticket['status']         = 'queed';
			$ticket['type']           = 'ticket';
			$ticket['ping_status']    = 'closed';
			$ticket['comment_status'] = 'closed';

			$ticket_id = wpas_insert_ticket( $ticket, false, $agent_id, 'standard-ticket-form' );

			if ( $ticket_id > 0 ) {

				foreach ( $custom_fields as $field_key => $field_val ) {

					if ( $field_key == 'ticket-tag' ) {

						wp_set_object_terms( $ticket_id, $field_val, $field_key );

					} else {
						
						$terms    = get_the_terms( $ticket_id, $field_key );
						$the_term = '';

						if ( is_array( $terms ) ) {
							foreach ( $terms as $term ) {
								$the_term = $term->term_id;
							}
						}

						if ( isset( $field_val ) && $the_term !== (int) $field_val ) {
							$term = get_term_by( 'id', (int) $field_val, $field_key );
							wp_set_object_terms( $ticket_id, (int) $field_val, $field_key, false );
						}
					}
				}

				$return_args['msg']     = __( 'The ticket has been added successfully', 'wp_webhooks' );
				$return_args['success'] = true;
				$return_args['data']    = get_post( $ticket_id );
			} else {
				$return_args['msg'] = __( 'An error has occured while adding a ticket.', 'wp_webhooks' );
			}

			return $return_args;

		}


	}
endif;
