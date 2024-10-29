<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_wp_get_term' ) ) :

	/**
	 * Load the wp_get_term action
	 *
	 * @since 5.1.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_wp_get_term {

		public function get_details(){

				$parameter = array(
				'term_id' => array( 
					'required' => true, 
					'label' => __( 'Term ID', 'wp-webhooks' ), 
					'short_description' => __( '(Integer) The ID of the taxonomy term you want to get.', 'wp-webhooks' ),
				),
				'taxonomy_slug' => array(
					'label' => __( 'Taxonomy slug', 'wp-webhooks' ), 
					'short_description' => __( '(String) The slug of the taxonomy to fetch the term from.', 'wp-webhooks' ), 
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		   => array( 'short_description' => __( '(mixed) The term id, as well as the taxonomy term id on success or wp_error on failure.', 'wp-webhooks' ) ),
				'msg'			=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The taxonomy term has been fetched successfully.',
				'data' => 
				array (
				  'term_id' => 93,
				  'name' => 'Remotely',
				  'slug' => 'remotely',
				  'term_group' => 0,
				  'term_taxonomy_id' => 93,
				  'taxonomy' => 'category',
				  'description' => 'A short demo description.',
				  'parent' => 90,
				  'count' => 0,
				  'filter' => 'raw',
				  'meta_data' => array(
					  'demo_field' => array(
						  'Value 1',
					  )
				  ),
				),
			);

			return array(
				'action'			=> 'wp_get_term',
				'name'			  => __( 'Get taxonomy term', 'wp-webhooks' ),
				'sentence'			  => __( 'get a single taxonomy term', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Get a single taxonomy term for all, or a specific taxonomy.', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'wordpress',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => '',
			);

			$term_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'term_id' ) );
			$taxonomy_slug = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'taxonomy_slug' );
			
			if( empty( $term_id ) ){
				$return_args['msg'] = __( "Please define the term_id argument.", 'action-wp_get_term' );
				return $return_args;
			}
			
			$term_data = get_term( $term_id, $taxonomy_slug, ARRAY_A );
 
			if( $term_data && ! is_wp_error( $term_data ) ) {

				//append the meta
				if( isset( $term_data['term_id'] ) ){
					$term_data['meta_data'] = get_term_meta( $term_data['term_id'] );
				}

				$return_args['success'] = true;
				$return_args['msg'] = __( "The taxonomy term has been fetched successfully.", 'action-wp_get_term' );
				$return_args['data'] = $term_data;
			} elseif( is_wp_error( $term_data ) ){
				$return_args['data'] = $term_data;
				$return_args['msg'] = __( "An error occured while fetching the taxonomy term.", 'action-wp_get_term' );
			} else {
				$return_args['data'] = $term_data;
				$return_args['msg'] = __( "An unidentified error occured while fetching the taxonomy term.", 'action-wp_get_term' );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.