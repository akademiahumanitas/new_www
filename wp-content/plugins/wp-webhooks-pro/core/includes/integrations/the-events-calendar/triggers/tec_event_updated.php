<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_the_events_calendar_Triggers_tec_event_updated' ) ) :

	/**
	 * Load the tec_event_updated trigger
	 *
	 * @since 6.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_the_events_calendar_Triggers_tec_event_updated {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'tribe_events_update_meta',
					'callback'  => array( $this, 'tec_event_updated_callback' ),
					'priority'  => 10,
					'arguments' => 3,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {

			$parameter = array(
				'success' => array( 'short_description' => __( '(Bool) True if the event was successfully updated.', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action.', 'wp-webhooks' ) ),
                'data'    => array( 'short_description' => __( '(Array) Further details about the action.', 'wp-webhooks' ) ),
			);

			$settings = array(
				'load_default_settings' => true,
			);

			return array(
				'trigger'           => 'tec_event_updated',
				'name'              => __( 'Event updated', 'wp-webhooks' ),
				'sentence'          => __( 'an event was updated', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires as soon as an event was updated within The Events Calendar.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'the-events-calendar',
				'premium'           => true,
			);

		}

		/**
		 * Triggers when The Events Calendar updates an event
		 *
		 * @param $event_id Event's id
		 * @param $data Meta update
		 * @param $event Event data
		 */
		public function tec_event_updated_callback( $event_id, $data, $event ) {

			//Prevent other post types like revisions from firing
			if( 
				! is_object( $event )
				|| ! isset( $event->post_type )
				|| $event->post_type !== 'tribe_events'
			){
				return;
			}

			$webhooks            = WPWHPRO()->webhook->get_hooks( 'trigger', 'tec_event_updated' );
			$response_data_array = array();

			$payload = array(
				'success' => true,
				'msg'     => __( 'The event has been updated.', 'wp-webhooks' ),
				'data'    => array(
					'event_id'    => $event_id,
					'meta_update' => $data,
					'event_data'  => $event,
				),
			);

			foreach ( $webhooks as $webhook ) {

				$webhook_url_name = ( is_array( $webhook ) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
				$is_valid         = true;

				if ( $is_valid ) {
					if ( $webhook_url_name !== null ) {
						$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					} else {
						$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					}
				}
			}

			do_action( 'wpwhpro/webhooks/trigger_tec_event_updated', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array(
				'success' => true,
				'msg'     => 'The event has been updated.',
				'data'    =>
				array(
					'event_id'    => 457,
					'meta_update' =>
					array(
						'_wpnonce'                         => '383ddcb387',
						'_wp_http_referer'                 => '/website/wp-admin/post-new.php?post_type=tribe_events',
						'user_ID'                          => 1,
						'action'                           => 'editpost',
						'originalaction'                   => 'editpost',
						'post_author'                      => 1,
						'post_type'                        => 'tribe_events',
						'original_post_status'             => 'auto-draft',
						'referredby'                       => 'https://demo-site.com/website/wp-admin/post.php?post=455&action=edit',
						'_wp_original_http_referer'        => 'http://demo-site.com/website/wp-admin/post.php?post=455&action=edit',
						'auto_draft'                       => '',
						'post_ID'                          => '457',
						'meta-box-order-nonce'             => '53a4ccb0ca',
						'closedpostboxesnonce'             => 'cb2fe50993',
						'post_title'                       => 'Demo 7',
						'samplepermalinknonce'             => 'a3a2b860cf',
						'content'                          => 'Demo details',
						'wp-preview'                       => '',
						'hidden_post_status'               => 'draft',
						'post_status'                      => 'publish',
						'hidden_post_password'             => '',
						'hidden_post_visibility'           => 'public',
						'visibility'                       => 'public',
						'post_password'                    => '',
						'mm'                               => '11',
						'jj'                               => '30',
						'aa'                               => '2022',
						'hh'                               => '17',
						'mn'                               => '36',
						'ss'                               => '13',
						'hidden_mm'                        => '11',
						'cur_mm'                           => '11',
						'hidden_jj'                        => '30',
						'cur_jj'                           => '30',
						'hidden_aa'                        => '2022',
						'cur_aa'                           => '2022',
						'hidden_hh'                        => '17',
						'cur_hh'                           => '17',
						'hidden_mn'                        => '36',
						'cur_mn'                           => '36',
						'original_publish'                 => 'Publish',
						'publish'                          => 'Publish',
						'tax_input'                        =>
						array(
							'post_tag'         => 'tags,some,new',
							'tribe_events_cat' =>
							array(
								0 => '0',
								1 => '94',
								2 => '84',
								3 => '93',
							),
						),
						'newtag'                           =>
						array(
							'post_tag' => '',
						),
						'newtribe_events_cat'              => 'New Event Category Name',
						'newtribe_events_cat_parent'       => '-1',
						'_ajax_nonce-add-tribe_events_cat' => '575b52f02c',
						'EventHideFromUpcoming'            => 'yes',
						'EventShowInCalendar'              => 'yes',
						'feature_event'                    => 'yes',
						'tribe-events-status'              =>
						array(
							'nonce'  => '1152c6ef9a',
							'status' => 'scheduled',
						),
						'_thumbnail_id'                    => '56',
						'ecp_nonce'                        => 'ea63b3a6c8',
						'EventStartDate'                   => '2022-11-30 08:00:00',
						'EventStartTime'                   => '8:00am',
						'EventEndTime'                     => '5:00pm',
						'EventEndDate'                     => '2022-11-30 17:00:00',
						'EventTimezone'                    => 'UTC+0',
						'venue'                            =>
						array(
							'VenueID'          =>
							array(
								0 => '444',
							),
							'Venue'            =>
							array(
								0 => 'Demo venue',
							),
							'Address'          =>
							array(
								0 => 'Demo address',
							),
							'City'             =>
							array(
								0 => 'Amsterdam',
							),
							'Country'          =>
							array(
								0 => 'Netherlands',
							),
							'Province'         =>
							array(
								0 => 'Some province',
							),
							'State'            => 'Some state',
							'Zip'              =>
							array(
								0 => '4444',
							),
							'Phone'            =>
							array(
								0 => '666666',
							),
							'URL'              =>
							array(
								0 => 'demo-example.comm',
							),
							'EventShowMap'     =>
							array(
								0 => '1',
							),
							'EventShowMapLink' =>
							array(
								0 => '1',
							),
						),
						'organizer'                        =>
						array(
							'OrganizerID' =>
							array(
								0 => '324',
							),
							'Organizer'   =>
							array(
								0 => 'John Doe',
							),
							'Phone'       =>
							array(
								0 => '5555555',
							),
							'Website'     =>
							array(
								0 => 'demo-example.com',
							),
							'Email'       =>
							array(
								0 => 'johndoe@example.com',
							),
						),
						'EventURL'                         => 'demo-example.com',
						'EventCurrencySymbol'              => '$',
						'EventCurrencyPosition'            => 'suffix',
						'EventCurrencyCode'                => '33',
						'EventCost'                        => '55',
						'excerpt'                          => 'demo-excerpt',
						'metakeyselect'                    => '#NONE#',
						'metakeyinput'                     => '',
						'metavalue'                        => '',
						'_ajax_nonce-add-meta'             => 'a5299d9125',
						'advanced_view'                    => '1',
						'comment_status'                   => 'open',
						'post_name'                        => '',
						'post_author_override'             => '1',
						'post_mime_type'                   => '',
						'ID'                               => 457,
						'post_content'                     => 'Demo details',
						'post_excerpt'                     => 'demo-excerpt',
						'ping_status'                      => 'closed',
						'Organizer'                        =>
						array(
							'OrganizerID' =>
							array(
								0 => '441',
							),
							'Organizer'   =>
							array(
								0 => '',
							),
							'Phone'       =>
							array(
								0 => '444444',
							),
							'Website'     =>
							array(
								0 => 'demo-example.com',
							),
							'Email'       =>
							array(
								0 => 'demo@example.com',
							),
						),
						'Venue'                            =>
						array(
							'VenueID'          =>
							array(
								0 => '444',
							),
							'Venue'            =>
							array(
								0 => '444',
							),
							'Address'          =>
							array(
								0 => 'Demo address',
							),
							'City'             =>
							array(
								0 => 'Rio',
							),
							'Country'          =>
							array(
								0 => 'Brazil',
							),
							'Province'         =>
							array(
								0 => 'Demo province',
							),
							'State'            => 'Demo state',
							'Zip'              =>
							array(
								0 => '10000',
							),
							'Phone'            =>
							array(
								0 => '44444',
							),
							'URL'              =>
							array(
								0 => '',
							),
							'EventShowMap'     =>
							array(
								0 => '1',
							),
							'EventShowMapLink' =>
							array(
								0 => '1',
							),
						),
						'EventTimezoneAbbr'                => 'UTC+0',
						'EventStartDateUTC'                => '2022-11-30 08:00:00',
						'EventEndDateUTC'                  => '2022-11-30 17:00:00',
						'EventDuration'                    => 32400,
					),
					'event_data'  =>
					array(
						'ID'                    => 457,
						'post_author'           => '1',
						'post_date'             => '2022-11-30 17:37:06',
						'post_date_gmt'         => '2022-11-30 17:37:06',
						'post_content'          => 'Demo details',
						'post_title'            => 'Demo 7',
						'post_excerpt'          => 'demo-excerpt',
						'post_status'           => 'publish',
						'comment_status'        => 'open',
						'ping_status'           => 'closed',
						'post_password'         => '',
						'post_name'             => 'demo-7',
						'to_ping'               => '',
						'pinged'                => '',
						'post_modified'         => '2022-11-30 17:37:06',
						'post_modified_gmt'     => '2022-11-30 17:37:06',
						'post_content_filtered' => '',
						'post_parent'           => 0,
						'guid'                  => 'http://demodomain.test/site/?post_type=tribe_events&#038;p=457',
						'menu_order'            => 0,
						'post_type'             => 'tribe_events',
						'post_mime_type'        => '',
						'comment_count'         => '0',
						'filter'                => 'raw',
					),
				),
			);

			return $data;
		}

	}

endif; // End if class_exists check.
