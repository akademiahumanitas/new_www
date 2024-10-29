<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_awesome_support_Actions_wpas_add_priority' ) ) :
	/**
	 * Load the wpas_add_priority action
	 *
	 * @since 6.0.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_awesome_support_Actions_wpas_add_priority {

		public function get_details() {
			$parameter = array(
				'name'        => array(
					'required'          => true,
					'label'             => __( 'Name', 'wp-webhooks' ),
					'short_description' => __(
						'(String) The name of the priority. You can provide the existing name or an ID of the priority. If no priority exists, a new one will be added.',
						'wp-webhooks'
					),
				),
				'slug'        => array(
					'label'             => __( 'Slug', 'wp-webhooks' ),
					'short_description' => __( '(String) The slug of the priority. The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'wp-webhooks' ),
				),
				'parent'      => array(
					'label'             => __( 'Parent priority', 'wp-webhooks' ),
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
					'short_description' => __( '(String) The ID of another priority to set it as a parent. Otherwise leave empty.', 'wp-webhooks' ),
				),
				'description' => array(
					'label'             => __( 'Description', 'wp-webhooks' ),
					'short_description' => __( '(String) The description of the priority.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action status.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further information about the request.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'Priority has been added successfully.',
				'data' => 
				array (
				  'term_id' => 147,
				  'term_taxonomy_id' => 147,
				),
			);

			return array(
				'action'            => 'wpas_add_priority', // required
				'name'              => __( 'Add priority', 'wp-webhooks' ),
				'sentence'          => __( 'Add a priority', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Add a priority within Awesome Support.', 'wp-webhooks' ),
				'description'       => array(),
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

			$parent = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'parent' );

			if ( WPWHPRO()->helpers->is_json( $parent ) ) {
				$parent = json_decode( $parent, true );
			}

			$priority['name']        = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'name' );
			$priority['description'] = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'description' );
			$priority['parent']      = ( $parent ) ? intval( $parent ) : 0;
			$priority['slug']        = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'slug' );
			$priority['taxonomy']    = 'ticket_priority';

			if( ! empty( $priority['parent'] ) ){
				$parent_term = get_term( $priority['parent'] );

				if( $parent_term->taxonomy !== $priority['taxonomy'] ){
					$return_args['msg'] = __( 'The parent priority is from a different taxonomy.', 'wp_webhooks' );
					return $return_args;
				}
			}

			$priority_result = wp_insert_term( $priority['name'], $priority['taxonomy'], $priority );

			if ( is_array( $priority_result ) ) {
				$return_args['success'] = true;
				$return_args['msg']     = __( 'Priority has been added successfully.', 'wp_webhooks' );
			} elseif( is_wp_error( $priority_result ) ){
				$return_args['msg'] = $priority_result->get_error_message();
			} else {
				$return_args['msg'] = __( 'An error occured while adding the priority.', 'wp_webhooks' );
			}

			$return_args['data'] = $priority_result;

			return $return_args;

		}


	}
endif;
