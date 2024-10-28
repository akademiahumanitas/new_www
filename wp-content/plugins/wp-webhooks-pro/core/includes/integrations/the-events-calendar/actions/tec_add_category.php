<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_the_events_calendar_Actions_tec_add_category' ) ) :
		/**
		 * Load the tec_add_category action
		 *
		 * @since 6.1.0
		 * @author Ironikus <info@ironikus.com>
		 */
	class WP_Webhooks_Integrations_the_events_calendar_Actions_tec_add_category {


		public function get_details() {
			$parameter = array(
				'name'            => array(
					'required'          => true,
					'label'             => __( 'Category name', 'wp_webhooks' ),
					'short_description' => __( '(String) The event category name.', 'wp_webhooks' ),
				),
				'slug'            => array(
					'label'             => __( 'Category slug', 'wp_webhooks' ),
					'short_description' => __( '(String) The event category slug. If none was set, we will generate it based on the category name.', 'wp_webhooks' ),
				),
				'parent_category' => array(
					'label'             => 'Parent category',
					'short_description' => __( '(String) The parent category.', 'wp_webhooks' ),
				),
				'description'     => array(
					'label'             => __( 'Description', 'wp_webhooks' ),
					'short_description' => __( '(String) The event category description.', 'wp_webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action status.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further information about the request.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The event category has been added successfully',
				'data'    =>
				array(
					'term_id'          => 94,
					'term_taxonomy_id' => 94,
				),
			);

			return array(
				'action'            => 'tec_add_category', // required
				'name'              => __( 'Add category', 'wp-webhooks' ),
				'sentence'          => __( 'add a category', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Add a category within The Events Calendar.', 'wp-webhooks' ),
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

			$event_category                = array();
			$event_category['name']        = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'name' );
			$event_category['description'] = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'description' );
			$event_category['slug']        = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'slug' );
			$event_category['parent']      = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'parent_category' );

			if( empty( $event_category['name'] ) ){
				$return_args['msg'] = __( 'Please set the name argument.', 'wp_webhooks' );
				return $return_args;
			}

			if( empty( $event_category['slug'] ) ){
				$event_category['slug'] = sanitize_title( $event_category['name'] );
			}

			$category_result = wp_insert_term( $event_category['name'], 'tribe_events_cat', $event_category );

			if ( is_array( $category_result ) ) {
				$return_args['msg']     = __( 'The event category has been added successfully', 'wp_webhooks' );
				$return_args['success'] = true;
				$return_args['data']    = $category_result;
			} else {
				$return_args['msg'] = __( 'An error has been occurred', 'wp_webhooks' );
			}

			return $return_args;

		}
	}
endif;
