<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_the_events_calendar_Actions_tec_add_event' ) ) :
	/**
	 * Load the tec_add_event action
	 *
	 * @since 6.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_the_events_calendar_Actions_tec_add_event {

		public function get_details() {
			$parameter = array(
				'title'                    => array(
					'required'          => true,
					'label'             => __( 'Event Title', 'wp-webhooks' ),
					'short_description' => __( '(String) The title of the event.', 'wp-webhooks' ),
				),
				'event_start_date'              => array(
					'required'          => true,
					'label'             => __( 'Event start date', 'wp-webhooks' ),
					'short_description' => __( '(String) The event start date and time.', 'wp-webhooks' ),
				),
				'event_end_date'                => array(
					'required'          => true,
					'label'             => __( 'Event end date', 'wp-webhooks' ),
					'short_description' => __( '(String) The event end date and time.', 'wp-webhooks' ),
				),
				'content'                  => array(
					'label'             => __( 'Event content', 'wp-webhooks' ),
					'short_description' => __( '(String) The event content details.', 'wp-webhooks' ),
				),
				'excerpt'                  => array(
					'label'             => __( 'Event excerpt', 'wp-webhooks' ),
					'short_description' => __( '(String) The event short description (excerpt).', 'wp-webhooks' ),
				),
				'event_all_day'            => array(
					'label'             => __( 'Event all day', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(
						'yes' => array( 'label' => 'Yes' ),
						'no'  => array( 'label' => 'No' ),
					),
					'default_value' => 'no',
					'short_description' => __( '(String) If set to yes, this event will run all day.', 'wp-webhooks' ),
				),
				'event_hide_from_upcoming' => array(
					'label'             => __( 'Event hide from upcoming', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(
						'yes' => array( 'label' => 'Yes' ),
						'no'  => array( 'label' => 'No' ),
					),
					'default_value' => 'no',
					'short_description' => __( '(String) Set to yes to hide this event from the upcoming list view.', 'wp-webhooks' ),
				),
				'event_show_in_calendar'   => array(
					'label'             => __( 'Event show in calendar', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(
						'yes' => array( 'label' => 'Yes' ),
						'no'  => array( 'label' => 'No' ),
					),
					'default_value' => 'yes',
					'short_description' => __( '(String)  Set to yes in order to display first in the list of events shown within a given day block.', 'wp-webhooks' ),
				),
				'event_featured'           => array(
					'label'             => __( 'Featured event', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(
						'yes' => array( 'label' => 'Yes' ),
						'no'  => array( 'label' => 'No' ),
					),
					'default_value' => 'no',
					'short_description' => __( '(String) Set to yes to highlight this event in views, archives, and widgets.', 'wp-webhooks' ),
				),
				'event_status'           => array(
					'label'             => __( 'Event status', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(),
					'query'             => array(
						'filter' => 'post_statuses',
						'args'   => array(),
					),
					'short_description' => __( '(String) Set the status of an event. If none is set, the event is added as draft.', 'wp-webhooks' ),
				),
				'event_cost'               => array(
					'label'             => __( 'Event cost', 'wp-webhooks' ),
					'short_description' => __( '(String) The cost of the event by default.', 'wp-webhooks' ),
				),
				'currency_symbol'          => array(
					'label'             => __( 'Currency symbol', 'wp-webhooks' ),
					'short_description' => __( '(String) The currency symbol. You can set symbols like $, €, £, etc.', 'wp-webhooks' ),
				),
				'currency_position'        => array(
					'label'             => __( 'Currency symbol position', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(
						'prefix' => array( 'label' => 'Before cost' ),
						'suffix' => array( 'label' => 'After cost' ),
					),
					'short_description' => __( '(String) The currency symbol position. For example before as $33 or after 33$.', 'wp-webhooks' ),
				),
				'currency_code'            => array(
					'label'             => __( 'ISO currency code', 'wp-webhooks' ),
					'short_description' => __( '(String) The ISO currency code of the event.', 'wp-webhooks' ),
				),
				'event_url'                => array(
					'label'             => __( 'Event url', 'wp-webhooks' ),
					'short_description' => __( '(String) A link to the event side or third-party page.', 'wp-webhooks' ),
				),
				'featured_image'           => array(
					'label'             => __( 'Featured image', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(),
					'query'             => array(
						'filter' => 'posts',
						'args'   => array(
							'post_type'   => 'attachment',
							'post_status' => 'any',
						),
					),
					'short_description' => __( '(String) The image url or attachment ID of a featured image.', 'wp-webhooks' ),
				),
				'user'                  => array(
					'label'             => __( 'Author', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(),
					'query'             => array(
						'filter' 		=> 'users',
						'args'   		=> array(),
					),
					'short_description' => __(
						'(String) The user ID or email of the event creator. You can provide an existing email address or an ID of a user.',
						'wp-webhooks'
					),
				),
				'venue'                    => array(
					'label'             => __( 'Venue', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(),
					'query'             => array(
						'filter' => 'posts',
						'args'   => array(
							'post_type' => 'tribe_venue',
						),
					),
					'short_description' => __( '(Integer) The venue ID of the venue where event is conducted.', 'wp-webhooks' ),
				),
				'event_show_map_link'      => array(
					'label'             => __( 'Event show map link', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(
						'yes' => array( 'label' => 'Yes' ),
						'no'  => array( 'label' => 'No' ),
					),
					'default_value' => 'yes',
					'short_description' => __( '(String) Set to yes to display a link to the map in the event view.', 'wp-webhooks' ),
				),
				'event_show_map'           => array(
					'label'             => __( 'Event show map', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(
						'yes' => array( 'label' => 'Yes' ),
						'no'  => array( 'label' => 'No' ),
					),
					'default_value' => 'yes',
					'short_description' => __( '(String) Set to yes to embed the map in the event view.', 'wp-webhooks' ),
				),
				'organizer'                => array(
					'label'             => __( 'Organizer', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(),
					'query'             => array(
						'filter' => 'posts',
						'args'   => array(
							'post_type' => 'tribe_organizer',
						),
					),
					'short_description' => __( '(Integer) The organizer ID of the venue of the event.', 'wp-webhooks' ),
				),
				'category'                 => array(
					'label'             => __( 'Event category', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(),
					'query'             => array(
						'filter' => 'terms',
						'args'   => array(
							'taxonomy'   => 'tribe_events_cat',
							'orderby'    => 'name',
							'order'      => 'ASC',
							'hide_empty' => false,
						),
					),
					'multiple'          => true,
					'short_description' => __( '(String) The event categories.', 'wp-webhooks' ),
				),
				'tags'                     => array(
					'label'             => __( 'Event tags', 'wp-webhooks' ),
					'type'              => 'select',
					'choices'           => array(),
					'query'             => array(
						'filter' => 'terms',
						'args'   => array(
							'taxonomy'   => 'post_tag',
							'orderby'    => 'name',
							'order'      => 'ASC',
							'hide_empty' => false,
						),
					),
					'multiple'          => true,
					'short_description' => __( '(String) The event tags.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action status.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further information about the request.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The event has been successfully added.',
				'data'    =>
				array(
					'ID'                    => 436,
					'post_author'           => '55',
					'post_date'             => '2022-11-30 11:46:57',
					'post_date_gmt'         => '2022-11-30 11:46:57',
					'post_content'          => 'Content by default	',
					'post_title'            => 'DemoEvent 6',
					'post_excerpt'          => 'excerpt-demo	',
					'post_status'           => 'publish',
					'comment_status'        => 'open',
					'ping_status'           => 'closed',
					'post_password'         => '',
					'post_name'             => 'demoevent-6-32',
					'to_ping'               => '',
					'pinged'                => '',
					'post_modified'         => '2022-11-30 11:46:58',
					'post_modified_gmt'     => '2022-11-30 11:46:58',
					'post_content_filtered' => '',
					'post_parent'           => 0,
					'guid'                  => 'https://demodomain.test/event/demoevent-6-32/',
					'menu_order'            => 0,
					'post_type'             => 'tribe_events',
					'post_mime_type'        => '',
					'comment_count'         => '0',
					'filter'                => 'raw',
				),
			);

			return array(
				'action'            => 'tec_add_event', // required
				'name'              => __( 'Add event', 'wp-webhooks' ),
				'sentence'          => __( 'add an event', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Add an event within The Events Calendar.', 'wp-webhooks' ),
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

			$post_title = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'title' );
			$tags       = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tags' );
			$categories = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'category' );
			$event_start_date = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'event_start_date' );
			$event_end_date = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'event_end_date' );

			if( empty( $post_title ) ){
				$return_args['msg']     = __( 'Please set the post_title argument.', 'wp-webhooks' );
				return $return_args;
			}

			if( empty( $event_start_date ) ){
				$return_args['msg']     = __( 'Please set the event_start_date argument.', 'wp-webhooks' );
				return $return_args;
			}

			if( empty( $event_end_date ) ){
				$return_args['msg']     = __( 'Please set the event_end_date argument.', 'wp-webhooks' );
				return $return_args;
			}

			$start_time_timestamp = 0;
			if( ! empty( $event_start_date ) ){
				$start_time_timestamp = strtotime( $event_start_date );
			} else {
				$start_time_timestamp = time();
			}

			$end_time_timestamp = 0;
			if( ! empty( $event_end_date ) ){
				$end_time_timestamp = strtotime( $event_end_date );
			} else {
				$end_time_timestamp = time();
			}

			$user_id = 0;
			$user    = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );

			if( ! empty( $user ) ){
				$user_id = WPWHPRO()->helpers->serve_user_id( $user );
			}

			if ( ! empty( $tags ) ) {
				if ( WPWHPRO()->helpers->is_json( $tags ) ) {
					$tags = json_decode( $tags, true );
					$tags = array_map( 'intval', $tags );
				} else {
					$tags = preg_split( '/(\s*,*\s*)*,+(\s*,*\s*)*/', $tags );
				}

				if( ! is_array( $tags ) && ! empty( $tags ) ){
					$tags = array( $tags );
				}
			}

			if ( ! empty( $categories ) ) {
				if ( WPWHPRO()->helpers->is_json( $categories ) ) {
					$categories = json_decode( $categories, true );
				} else {
					$categories = preg_split( '/(\s*,*\s*)*,+(\s*,*\s*)*/', $categories );
				}

				if( ! is_array( $categories ) && ! empty( $categories ) ){
					$categories = array( $categories );
				}
			}

			$event_status = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'event_status' );

			$event                                  = array();
			$event['post_title']                    = $post_title;
			$event['post_content']                  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'content' );
			$event['post_status']                   = ( ! empty( $event_status ) ) ? $event_status : 'publish';
			$event['post_excerpt']                  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'excerpt' );
			$event['post_author']                   = $user_id;
			$event['post_content']                  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'content' );
			$event['EventStartDate']                = WPWHPRO()->helpers->get_formatted_date( $start_time_timestamp, 'm/d/Y' );
			$event['EventEndDate']                  = WPWHPRO()->helpers->get_formatted_date( $end_time_timestamp, 'm/d/Y' );
			$event['EventAllDay']                   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'event_all_day' ) == 'yes' ? true : false;
			$event['EventStartHour']                = WPWHPRO()->helpers->get_formatted_date( $start_time_timestamp, 'H' );
			$event['EventStartMinute']              = WPWHPRO()->helpers->get_formatted_date( $start_time_timestamp, 'i' );
			$event['EventStartMeridian']            = WPWHPRO()->helpers->get_formatted_date( $start_time_timestamp, 'a' );
			$event['EventEndHour']                	= WPWHPRO()->helpers->get_formatted_date( $end_time_timestamp, 'H' );
			$event['EventEndMinute']              	= WPWHPRO()->helpers->get_formatted_date( $end_time_timestamp, 'i' );
			$event['EventEndMeridian']            	= WPWHPRO()->helpers->get_formatted_date( $end_time_timestamp, 'a' );
			$event['EventHideFromUpcoming']         = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'event_hide_from_upcoming' ) === 'yes' ? true : false;
			$event['EventShowInCalendar']           = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'event_show_in_calendar' );
			$event['feature_event']                 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'event_featured' ) === 'yes' ? true : false;
			$event['venue']['EventShowMapLink']     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'event_show_map_link' ) === 'yes' ? true : false;
			$event['venue']['EventShowMap']         = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'event_show_map' ) === 'yes' ? true : false; //doesnt unset eben while set. Maybe a bug? 
			$event['EventCost']                     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'event_cost' );
			$event['EventCurrencySymbol']           = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'currency_symbol' );
			$event['EventCurrencyPosition']         = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'currency_position' );
			$event['EventCurrencyCode']             = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'currency_code' );
			$event['EventURL']                      = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'event_url' );
			$event['FeaturedImage']                 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'featured_image' );

			$venue_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'venue' ) );
			if( ! empty( $venue_id ) ){
				$event['EventVenueID'] = $venue_id;
			}

			$organizer_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'organizer' ) );
			if( ! empty( $organizer_id ) ){
				$event['Organizer']['OrganizerID'] = array( $organizer_id );
			}

			if ( $event['EventAllDay'] == true ) {
				$event['EventStartHour'] = '08';
				$event['EventEndHour']   = '05';
				$event['EventStartTime'] = '8:00am';
				$event['EventEndTime']   = '5:00pm';
			}
			
			$event_id = tribe_create_event( $event );

			if ( $event_id > 0 ) {

				if( ! empty( $tags ) && is_array( $tags ) ){
					wp_set_object_terms( $event_id, $tags, 'post_tag' );
				}

				if( ! empty( $categories ) && is_array( $categories ) ){
					wp_set_object_terms( $event_id, $categories, 'tribe_events_cat' );
				}

				$event_data             = get_post( $event_id );
				$return_args['success'] = true;
				$return_args['data']    = $event_data;
				$return_args['msg']     = __( 'The event has been successfully added.', 'wp-webhooks' );
			} else {
				$return_args['msg'] = __( 'An error occurred while adding the event.', 'wp-webhooks' );
			}

			return $return_args;

		}


	}
endif;
