<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_awesome_support_Actions_wpas_add_department' ) ) :
	/**
	 * Load the wpas_add_department action
	 *
	 * @since 6.0.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_awesome_support_Actions_wpas_add_department {

		public function get_details() {
			$parameter = array(
				'name'        => array(
					'required'          => true,
					'label'             => __( 'Name', 'wp-webhooks' ),
					'short_description' => __(
						'(String) The name of the department. You can provide the existing name or an ID of the department. If no department exists, a new one will be added.',
						'wp-webhooks'
					),
				),
				'slug'        => array(
					'label'             => __( 'Slug', 'wp-webhooks' ),
					'short_description' => __( '(String) The slug of the department. The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'wp-webhooks' ),
				),
				'parent'      => array(
					'label'             => __( 'Parent department', 'wp-webhooks' ),
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
					'short_description' => __( '(String) The ID of another department to set it as a parent. Otherwise leave empty.', 'wp-webhooks' ),
				),
				'description' => array(
					'label'             => __( 'Description', 'wp-webhooks' ),
					'short_description' => __( '(String) The description of the department.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action status.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further information about the request.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'Department has been added successfully.',
				'data' => 
				array (
				  'term_id' => 147,
				  'term_taxonomy_id' => 147,
				),
			);

			return array(
				'action'            => 'wpas_add_department', // required
				'name'              => __( 'Add department', 'wp-webhooks' ),
				'sentence'          => __( 'Add a department', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Add a department within Awesome Support.', 'wp-webhooks' ),
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

			$department['name']        = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'name' );
			$department['description'] = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'description' );
			$department['parent']      = ( $parent ) ? intval( $parent ) : 0;
			$department['slug']        = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'slug' );
			$department['taxonomy']    = 'department';

			if( ! empty( $department['parent'] ) ){
				$parent_term = get_term( $department['parent'] );

				if( $parent_term->taxonomy !== $department['taxonomy'] ){
					$return_args['msg'] = __( 'The parent department is from a different taxonomy.', 'wp_webhooks' );
					return $return_args;
				}
			}

			$department_result = wp_insert_term( $department['name'], $department['taxonomy'], $department );

			if ( is_array( $department_result ) ) {
				$return_args['success'] = true;
				$return_args['msg']     = __( 'Department has been added successfully.', 'wp_webhooks' );
			} elseif( is_wp_error( $department_result ) ){
				$return_args['msg'] = $department_result->get_error_message();
			} else {
				$return_args['msg'] = __( 'An error occured while adding the department.', 'wp_webhooks' );
			}

			$return_args['data'] = $department_result;

			return $return_args;

		}


	}
endif;
