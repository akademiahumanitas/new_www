<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_wp_delete_term' ) ) :

	/**
	 * Load the wp_delete_term action
	 *
	 * @since 5.1.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_wp_delete_term {

		public function get_details(){

				$parameter = array(
				'term_id' => array( 
					'required' => true, 
					'label' => __( 'Term ID', 'wp-webhooks' ), 
					'short_description' => __( '(Integer) The ID of the taxonomy term you want to delete.', 'wp-webhooks' ),
				),
				'taxonomy_slug' => array( 
					'required' => true, 
					'label' => __( 'Taxonomy slug', 'wp-webhooks' ), 
					'short_description' => __( '(String) The slug of the taxonomy to delete the term from.', 'wp-webhooks' ), 
				),
				'default_id' => array( 
					'label' => __( 'Default ID', 'wp-webhooks' ), 
					'short_description' => __( '(Integer) The term ID to make the default term. This will only override the terms found if there is only one term found. Any other and the found terms are used.', 'wp-webhooks' ),
				),
				'force_default' => array( 
					'type' => 'select', 
					'default_value' => 'yes', 
					'label' => __( 'Force default', 'wp-webhooks' ), 
					'choices' => array( 
						'yes' => __( 'Yes', 'wp-webhooks' ),
						'no' => __( 'No', 'wp-webhooks' ),
					), 
					'short_description' => __( '(String) Whether to force the supplied term as default to be assigned even if the object was not going to be term-less. Default: no', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		   => array( 'short_description' => __( '(mixed) The term id, as well as the taxonomy term id on success or wp_error on failure.', 'wp-webhooks' ) ),
				'msg'			=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The taxonomy term has been deleted successfully.',
				'data' => '',
			);

			return array(
				'action'			=> 'wp_delete_term',
				'name'			  => __( 'Delete taxonomy term', 'wp-webhooks' ),
				'sentence'			  => __( 'delete a taxonomy term', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Delete a taxonomy term for a specific taxonomy.', 'wp-webhooks' ),
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
			$default_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'default_id' ) );
			$force_default = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'force_default' ) === 'yes' ) ? true : false;

			if( empty( $term_id ) ){
				$return_args['msg'] = __( "Please define the term_id argument.", 'action-wp_delete_term' );
				return $return_args;
			}

			if( empty( $taxonomy_slug ) ){
				$return_args['msg'] = __( "Please define the taxonomy_slug argument.", 'action-wp_delete_term' );
				return $return_args;
			}

			$args = array();

			if( ! empty( $default_id ) ){
				$args['default'] = $default_id;
			}

			if( ! empty( $force_default ) ){
				$args['force_default'] = $force_default;
			}
			
			$term_data = wp_delete_term( $term_id, $taxonomy_slug, $args );
 
			if( $term_data === true ) {
				$return_args['success'] = true;
				$return_args['msg'] = __( "The taxonomy term has been deleted successfully.", 'action-wp_delete_term' );
			} elseif( $term_data === false ){
				$return_args['success'] = true;
				$return_args['msg'] = __( "The term was not deleted as it did not exist in the first place.", 'action-wp_delete_term' );
			} elseif( $term_data === 0 ){
				$return_args['msg'] = __( "Error deleting the taxonomy term as it is set as the default term.", 'action-wp_delete_term' );
			} elseif( is_wp_error( $term_data ) ){
				$return_args['data'] = $term_data;
				$return_args['msg'] = __( "An error occured deleting the taxonomy term.", 'action-wp_delete_term' );
			} else {
				$return_args['data'] = $term_data;
				$return_args['msg'] = __( "An unidentified error occured while deleting the taxonomy term.", 'action-wp_delete_term' );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.