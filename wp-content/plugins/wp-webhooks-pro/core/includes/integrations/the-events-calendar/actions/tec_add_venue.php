<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_the_events_calendar_Actions_tec_add_venue' ) ) :
		/**
		 * Load the tec_add_venue action
		 *
		 * @since 6.1.0
		 * @author Ironikus <info@ironikus.com>
		 */
	class WP_Webhooks_Integrations_the_events_calendar_Actions_tec_add_venue {


		public function get_details() {
			$parameter = array(
				'name'                => array(
					'required'          => true,
					'label'             => __( 'Venue name', 'wp_webhooks' ),
					'short_description' => __( '(String) The venue name.', 'wp_webhooks' ),
				),
				'user'                => array(
					'label'             => __( 'Author', 'wp-webhooks' ),
					'type'				=> 'select',
					'query'             => array(
						'filter' => 'users',
						'args'   => array(),
					),
					'short_description' => __(
						'(String) The user ID or email of the venue creator. You can provide an existing email address or an ID of a user.',
						'wp-webhooks'
					),
				),
				'details'             => array(
					'label'             => __( 'Venue Details', 'wp_webhooks' ),
					'short_description' => __( '(String) The event venue details.', 'wp_webhooks' ),
				),
				'event_show_map_link' => array(
					'label'             => __( 'Event show map link', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
						'no'  => array( 'label' => __( 'No', 'wp-webhooks' ) ),
					),
					'default_value'		=> 'yes',
					'short_description' => __( '(String) Set to yes to display a link to the map in the event view.', 'wp-webhooks' ),
				),
				'event_show_map'      => array(
					'label'             => __( 'Event show map', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
						'no'  => array( 'label' => __( 'No', 'wp-webhooks' ) ),
					),
					'default_value'		=> 'yes',
					'short_description' => __( '(String) Set to yes to embed the map in the event view.', 'wp-webhooks' ),
				),
				'excerpt'             => array(
					'label'             => __( 'Short description (Excerpt)', 'wp_webhooks' ),
					'short_description' => __( '(String) The short description (excerpt) of the venue.', 'wp_webhooks' ),
				),
				'address'             => array(
					'label'             => __( 'Address', 'wp_webhooks' ),
					'short_description' => __( '(String) The address.', 'wp_webhooks' ),
				),
				'city'                => array(
					'label'             => __( 'City', 'wp_webhooks' ),
					'short_description' => __( '(String) The city.', 'wp_webhooks' ),
				),
				'country'             => array(
					'label'             => __( 'Country', 'wp_webhooks' ),
					'type'              => 'select',
					'query'             => array(
						'filter' => 'countries',
						'args'   => array(),
					),
					'short_description' => __( '(String) The country name or code.', 'wp_webhooks' ),
				),
				'state'               => array(
					'label'             => __( 'State or Province', 'wp_webhooks' ),
					'short_description' => __( '(String) The state.', 'wp_webhooks' ),
				),
				'postal_code'         => array(
					'label'             => __( 'Postal code', 'wp_webhooks' ),
					'short_description' => __( '(String) The postal code.', 'wp_webhooks' ),
				),
				'phone'               => array(
					'label'             => __( 'Phone', 'wp_webhooks' ),
					'short_description' => __( '(String) The phone.', 'wp_webhooks' ),
				),
				'website'             => array(
					'label'             => __( 'Website', 'wp_webhooks' ),
					'short_description' => __( '(String) The website.', 'wp_webhooks' ),
				),
				'featured_image'      => array(
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
				'msg'     => 'The event venue has been added successfully.',
				'data'    =>
				array(
					'ID'                    => 448,
					'post_author'           => '1',
					'post_date'             => '2022-11-30 17:19:23',
					'post_date_gmt'         => '2022-11-30 17:19:23',
					'post_content'          => 'Demo details',
					'post_title'            => 'Demo venue',
					'post_excerpt'          => 'demo-excerpt',
					'post_status'           => 'publish',
					'comment_status'        => 'closed',
					'ping_status'           => 'closed',
					'post_password'         => '',
					'post_name'             => 'demo-venue-6',
					'to_ping'               => '',
					'pinged'                => '',
					'post_modified'         => '2022-11-30 17:19:23',
					'post_modified_gmt'     => '2022-11-30 17:19:23',
					'post_content_filtered' => '',
					'post_parent'           => 0,
					'guid'                  => 'https://demo.com/venue/demo-venue-6/',
					'menu_order'            => 0,
					'post_type'             => 'tribe_venue',
					'post_mime_type'        => '',
					'comment_count'         => '0',
					'filter'                => 'raw',
				),
			);

			return array(
				'action'            => 'tec_add_venue', // required
				'name'              => __( 'Add venue', 'wp-webhooks' ),
				'sentence'          => __( 'add a venue', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Add a venue within The Events Calendar.', 'wp-webhooks' ),
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

			$event_venue                     = array();
			$event_venue['Venue']            = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'name' );
			$event_venue['Description']      = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'details' );
			$event_venue['VenueShowMapLink'] = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'event_show_map_link' ) == 'yes' ? true : false;
			$event_venue['VenueShowMap']     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'event_show_map' ) == 'yes' ? true : false;
			$event_venue['post_excerpt']     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'excerpt' );
			$event_venue['Phone']            = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'phone' );
			$event_venue['URL']              = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'website' );
			$event_venue['City']             = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'city' );
			$event_venue['Country']          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'country' );
			$event_venue['Zip']              = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'postal_code' );
			$event_venue['Province']         = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'state' );
			$event_venue['Address']          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'address' );
			$event_venue['FeaturedImage']    = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'featured_image' );

			
			if( ! empty( $user_id ) ){
				wp_set_current_user( $user_id );
			}

			//Maybe format the country to the name
			if( ! empty( $event_venue['Country'] ) ){
				$countries = WPWHPRO()->helpers->get_country_list();
				$country_key = strtoupper( $event_venue['Country'] );
				if( isset( $countries[ $country_key ] ) ){
					$event_venue['Country'] = $countries[ $country_key ];
				}
			}

			$venue_id = tribe_create_venue( $event_venue );

			if ( $venue_id > 0 ) {
				wp_update_post(
					array(
						'ID'           => $venue_id,
						'post_excerpt' => $event_venue['post_excerpt'],
					)
				);
				$return_args['msg']     = __( 'The event venue has been added successfully.', 'wp_webhooks' );
				$return_args['success'] = true;
				$return_args['data']    = get_post( $venue_id );
			} else {
				$return_args['msg'] = __( 'An error occurred while adding the venue.', 'wp_webhooks' );
			}

			return $return_args;

		}
	}
endif;
