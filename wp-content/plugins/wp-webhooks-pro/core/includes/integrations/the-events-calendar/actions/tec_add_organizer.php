<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_the_events_calendar_Actions_tec_add_organizer' ) ) :
		/**
		 * Load the tec_add_organizer action
		 *
		 * @since 6.1.0
		 * @author Ironikus <info@ironikus.com>
		 */
	class WP_Webhooks_Integrations_the_events_calendar_Actions_tec_add_organizer {


		public function get_details() {
			$parameter = array(
				'name'           => array(
					'required'          => true,
					'label'             => __( 'Organizer name', 'wp_webhooks' ),
					'short_description' => __( '(String) The name of the event organizer.', 'wp_webhooks' ),
				),
				'user'           => array(
					'required'          => true,
					'label'             => __( 'Author', 'wp-webhooks' ),
					'type'				=> 'select',
					'query'             => array(
						'filter' => 'users',
						'args'   => array(),
					),
					'short_description' => __(
						'(String) The user ID or email of the creator of the organizer. You can provide an existing email address or an ID of a user.',
						'wp-webhooks'
					),
				),
				'details'        => array(
					'label'             => __( 'Details', 'wp_webhooks' ),
					'short_description' => __( '(String) The slug of the event organizer.', 'wp_webhooks' ),
				),
				'excerpt'        => array(
					'label'             => __( 'Short description (Excerpt)', 'wp_webhooks' ),
					'short_description' => __( '(String) The short description (excerpt) of the organizer.', 'wp_webhooks' ),
				),
				'phone'          => array(
					'label'             => __( 'Phone', 'wp_webhooks' ),
					'short_description' => __( '(String) The phone number.', 'wp_webhooks' ),
				),
				'website'        => array(
					'label'             => __( 'Website', 'wp_webhooks' ),
					'short_description' => __( '(String) The website address.', 'wp_webhooks' ),
				),
				'email'          => array(
					'label'             => __( 'Email', 'wp_webhooks' ),
					'short_description' => __( '(String) The email of the organizer.', 'wp_webhooks' ),
				),
				'featured_image' => array(
					'label'             => __( 'Feautered image', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(),
					'query'             => array(
						'filter' => 'posts',
						'args'   => array(
							'post_type'   => 'attachment',
							'post_status' => 'any',
						),
					),
					'short_description' => __( '(String) The image url or ID of a featured image.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action status.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further information about the request.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The event organizer has been added successfully.',
				'data'    =>
				array(
					'ID'                    => 441,
					'post_author'           => '1',
					'post_date'             => '2022-11-30 14:38:41',
					'post_date_gmt'         => '2022-11-30 14:38:41',
					'post_content'          => 'This is a long description.',
					'post_title'            => 'Demo Organizer',
					'post_excerpt'          => 'This is a short description',
					'post_status'           => 'publish',
					'comment_status'        => 'closed',
					'ping_status'           => 'closed',
					'post_password'         => '',
					'post_name'             => 'demo-organizer',
					'to_ping'               => '',
					'pinged'                => '',
					'post_modified'         => '2022-11-30 14:38:41',
					'post_modified_gmt'     => '2022-11-30 14:38:41',
					'post_content_filtered' => '',
					'post_parent'           => 0,
					'guid'                  => 'https://demodomain.test/organizer/demo-organizer/',
					'menu_order'            => 0,
					'post_type'             => 'tribe_organizer',
					'post_mime_type'        => '',
					'comment_count'         => '0',
					'filter'                => 'raw',
				),
			);

			return array(
				'action'            => 'tec_add_organizer', // required
				'name'              => __( 'Add organizer', 'wp-webhooks' ),
				'sentence'          => __( 'add an organizer', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Add an organizer within The Events Calendar.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'the-events-calendar',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
				'data'    => array(),
			);

			$user    = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$user_id = WPWHPRO()->helpers->serve_user_id( $user );

			$organizer_email = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'email' );

			if ( isset( $organizer_email ) && ! is_email( $organizer_email ) ) {
				$return_args['msg'] = __( 'Please provide a valid email address.', 'wp_webhooks' );
				return $return_args;
			}

			$event_organizer                  = array();
			$event_organizer['Organizer']     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'name' );
			$event_organizer['Description']   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'details' );
			$event_organizer['post_excerpt']  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'excerpt' );
			$event_organizer['Phone']         = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'phone' );
			$event_organizer['Website']       = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'website' );
			$event_organizer['Email']         = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'email' );
			$event_organizer['FeaturedImage'] = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'featured_image' );

			if( ! empty( $user_id ) ){
				wp_set_current_user( $user_id );
			}

			$organizer_id = tribe_create_organizer( $event_organizer );

			if ( $organizer_id > 0 ) {
				wp_update_post(
					array(
						'ID'           => $organizer_id,
						'post_excerpt' => $event_organizer['post_excerpt'],
					)
				);
				$return_args['msg']     = __( 'The event organizer has been added successfully.', 'wp_webhooks' );
				$return_args['success'] = true;
				$return_args['data']    = get_post( $organizer_id );
			} else {
				$return_args['msg'] = __( 'An error occurred while creating the organizer.', 'wp_webhooks' );
			}

			return $return_args;

		}
	}
endif;
