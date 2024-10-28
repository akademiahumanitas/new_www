<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_awesome_support_Actions_wpas_add_product' ) ) :
	/**
	 * Load the wpas_add_product action
	 *
	 * @since 6.0.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_awesome_support_Actions_wpas_add_product {

		public function get_details() {
			$parameter = array(
				'name'        => array(
					'required'          => true,
					'label'             => __( 'Name', 'wp-webhooks' ),
					'short_description' => __(
						'(String) The name of the product. You can provide the existing name or an ID of the product. If no product exists, a new one will be added.',
						'wp-webhooks'
					),
				),
				'slug'        => array(
					'label'             => __( 'Slug', 'wp-webhooks' ),
					'short_description' => __( '(String) The slug of the product. The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'wp-webhooks' ),
				),
				'parent'      => array(
					'label'             => __( 'Parent product', 'wp-webhooks' ),
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
					'short_description' => __( '(String) The ID of another product to set it as a parent. Otherwise leave empty.', 'wp-webhooks' ),
				),
				'description' => array(
					'label'             => __( 'Description', 'wp-webhooks' ),
					'short_description' => __( '(String) The description of the product.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action status.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further information about the request.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'Product has been added successfully.',
				'data' => 
				array (
				  'term_id' => 147,
				  'term_taxonomy_id' => 147,
				),
			);

			return array(
				'action'            => 'wpas_add_product', // required
				'name'              => __( 'Add product', 'wp-webhooks' ),
				'sentence'          => __( 'Add a product', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Add a product within Awesome Support.', 'wp-webhooks' ),
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

			$product['name']        = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'name' );
			$product['description'] = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'description' );
			$product['parent']      = ( $parent ) ? intval( $parent ) : 0;
			$product['slug']        = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'slug' );
			$product['taxonomy']    = 'product';

			if( ! empty( $product['parent'] ) ){
				$parent_term = get_term( $product['parent'] );

				if( $parent_term->taxonomy !== $product['taxonomy'] ){
					$return_args['msg'] = __( 'The parent product is from a different taxonomy.', 'wp_webhooks' );
					return $return_args;
				}
			}

			$product_result = wp_insert_term( $product['name'], $product['taxonomy'], $product );

			if ( is_array( $product_result ) ) {
				$return_args['success'] = true;
				$return_args['msg']     = __( 'Product has been added successfully.', 'wp_webhooks' );
			} elseif( is_wp_error( $product_result ) ){
				$return_args['msg'] = $product_result->get_error_message();
			} else {
				$return_args['msg'] = __( 'An error occured while adding the product.', 'wp_webhooks' );
			}

			$return_args['data'] = $product_result;

			return $return_args;

		}


	}
endif;
