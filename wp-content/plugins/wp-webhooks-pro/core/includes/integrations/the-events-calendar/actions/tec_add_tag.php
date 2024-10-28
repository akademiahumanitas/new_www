<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_the_events_calendar_Actions_tec_add_tag' ) ) :
		/**
		 * Load the tec_add_tag action
		 *
		 * @since 6.1.0
		 * @author Ironikus <info@ironikus.com>
		 */
	class WP_Webhooks_Integrations_the_events_calendar_Actions_tec_add_tag {


		public function get_details() {
			$parameter = array(
				'name'            => array(
					'required'          => true,
					'label'             => __( 'Tag name', 'wp_webhooks' ),
					'short_description' => __( '(String) The event tag name.', 'wp_webhooks' ),
				),
				'slug'            => array(
					'required'          => false,
					'label'             => __( 'Tag slug', 'wp_webhooks' ),
					'short_description' => __( '(String) The event tag slug. If none was set, we will generate it based on the tag name.', 'wp_webhooks' ),
				),
				'description'     => array(
					'label'             => __( 'Description', 'wp_webhooks' ),
					'short_description' => __( '(String) The event tag description.', 'wp_webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action status.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further information about the request.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The event tag has been added successfully',
				'data'    =>
				array(
					'term_id'          => 94,
					'term_taxonomy_id' => 94,
				),
			);

			return array(
				'action'            => 'tec_add_tag', // required
				'name'              => __( 'Add tag', 'wp-webhooks' ),
				'sentence'          => __( 'add a tag', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Add a tag within The Events Calendar.', 'wp-webhooks' ),
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

			$event_tag                = array();
			$event_tag['name']        = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'name' );
			$event_tag['description'] = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'description' );
			$event_tag['slug']        = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'slug' );

			if( empty( $event_tag['name'] ) ){
				$return_args['msg'] = __( 'Please set the name argument.', 'wp_webhooks' );
				return $return_args;
			}

			if( empty( $event_tag['slug'] ) ){
				$event_tag['slug'] = sanitize_title( $event_tag['name'] );
			}

			$tag_result = wp_insert_term( $event_tag['name'], 'post_tag', $event_tag );

			if ( is_array( $tag_result ) ) {
				$return_args['msg']     = __( 'The event tag has been added successfully', 'wp_webhooks' );
				$return_args['success'] = true;
				$return_args['data']    = $tag_result;
			} else {
				$return_args['msg'] = __( 'An error has been occurred', 'wp_webhooks' );
			}

			return $return_args;

		}
	}
endif;
