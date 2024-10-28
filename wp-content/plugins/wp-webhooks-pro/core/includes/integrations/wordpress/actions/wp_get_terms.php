<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_wp_get_terms' ) ) :

	/**
	 * Load the wp_get_terms action
	 *
	 * @since 5.1.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_wp_get_terms {

		public function get_details(){

			$parameter = array(
				'arguments' => array( 
					'required' => true,
					'type' => 'repeater',
					'multiple' => true, 
					'label' => __( 'Arguments', 'wp-webhooks' ), 
					'short_description' => __( '(String) The JSON formatted data for the WP term query.', 'wp-webhooks' ),
				),
			);

			ob_start();
			?>
<p><?php echo __( 'Here is an example JSON that will get the first two terms for the taxonomy with the slug post_tag:', 'wp-webhooks' ); ?></p>
<pre>
{
	"taxonomy": "post_tag",
	"number": 2
}
</pre>
<p><?php echo __( 'To learn more about all possible values, please refer to the following manual: ', 'wp-webhooks' ); ?> <a target="_blank" href="https://developer.wordpress.org/reference/classes/wp_term_query/__construct/">https://developer.wordpress.org/reference/classes/wp_term_query/__construct/</a></p>
			<?php
			$parameter['arguments']['description'] = ob_get_clean();

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		   => array( 'short_description' => __( '(mixed) The term id, as well as the taxonomy term id on success or wp_error on failure.', 'wp-webhooks' ) ),
				'msg'			=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The taxonomy terms have been fetched successfully.',
				'data' => 
				array (
				  0 => 
				  array (
					'term_id' => 70,
					'name' => 'Sport',
					'slug' => 'sport',
					'term_group' => 0,
					'term_taxonomy_id' => 70,
					'taxonomy' => 'post_tag',
					'description' => '',
					'parent' => 0,
					'count' => 1,
					'filter' => 'raw',
					'meta_data' => array(
						'demo_field' => array(
							'Value 1',
						)
					),
				  ),
				  1 => 
				  array (
					'term_id' => 72,
					'name' => 'Male',
					'slug' => 'male',
					'term_group' => 0,
					'term_taxonomy_id' => 72,
					'taxonomy' => 'post_tag',
					'description' => '',
					'parent' => 0,
					'count' => 1,
					'filter' => 'raw',
					'meta_data' => array(
						'demo_field' => array(
							'Value 1',
						)
					),
				  ),
				),
			);

			$description = array(
				'tipps' => array(
					__( 'To learn more about the argument you can use, please take a look at the following manual: ', 'wp-webhooks' ) . '<a target="_blank" href="https://developer.wordpress.org/reference/classes/wp_term_query/__construct/">https://developer.wordpress.org/reference/classes/wp_term_query/__construct/</a>',
				)
			);

			return array(
				'action'			=> 'wp_get_terms',
				'name'			  => __( 'Get taxonomy terms', 'wp-webhooks' ),
				'sentence'			  => __( 'get one or multiple taxonomy terms', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Get a one or multiple taxonomy terms for all, or a specific taxonomy.', 'wp-webhooks' ),
				'description'	   => $description,
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

			$arguments = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'arguments' );
			
			if( empty( $arguments ) ){
				$return_args['msg'] = __( "Please define the arguments argument.", 'action-wp_get_terms' );
				return $return_args;
			}

			$validated_arguments = array();
			if( WPWHPRO()->helpers->is_json( $arguments ) ){
				$validated_arguments = json_decode( $arguments, true );
			}

			if( empty( $validated_arguments ) || ! is_array( $validated_arguments ) ){
				$return_args['msg'] = __( "Your arguments argument is empty or could not be validated.", 'action-wp_get_terms' );
				return $return_args;
			}
			
			$terms = get_terms( $validated_arguments );
 
			if( $terms && ! is_wp_error( $terms ) ) {

				//apend meta data
				foreach( $terms as $term_key => $term ){
					if( isset( $term->term_id ) ){
						$terms[ $term_key ]->meta_data = get_term_meta( $term->term_id );
					}
				}
				$return_args['success'] = true;
				$return_args['msg'] = __( "The taxonomy terms have been fetched successfully.", 'action-wp_get_terms' );
				$return_args['data'] = $terms;
			} elseif( is_wp_error( $terms ) ){
				$return_args['data'] = $terms;
				$return_args['msg'] = __( "An error occured while fetching the taxonomy terms.", 'action-wp_get_terms' );
			} else {
				$return_args['data'] = $terms;
				$return_args['msg'] = __( "An unidentified error occured while fetching the taxonomy terms.", 'action-wp_get_terms' );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.