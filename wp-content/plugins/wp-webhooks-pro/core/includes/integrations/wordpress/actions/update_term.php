<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_update_term' ) ) :

	/**
	 * Load the update_term action
	 *
	 * @since 4.2.3
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_update_term {

		public function get_details(){

				$parameter = array(
				'term_id'			=> array( 'required' => true, 'short_description' => __( '(String) The id for the taxonomy term you want to update.', 'wp-webhooks' ) ),
				'taxonomy'			=> array( 'required' => true, 'short_description' => __( '(String) The slug of the taxonomy to relate the term with.', 'wp-webhooks' ) ),
				'alias_of'			=> array( 'short_description' => __( '(String) Slug of the term to make this term an alias of. Accepts a term slug.', 'wp-webhooks' ) ),
				'description'			=> array( 'short_description' => __( '(String) The term description.', 'wp-webhooks' ) ),
				'parent'			=> array( 'short_description' => __( '(String) The id of the parent term.', 'wp-webhooks' ) ),
				'slug'			=> array( 'short_description' => __( '(String) The term slug to use.', 'wp-webhooks' ) ),
				'name'			=> array( 'short_description' => __( '(String) The name you want to set for the term.', 'wp-webhooks' ) ),
				'do_action'		  => array( 'short_description' => __( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More infos are in the description.', 'wp-webhooks' ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		   => array( 'short_description' => __( '(mixed) The term id, as well as the taxonomy term id on success or wp_error on failure.', 'wp-webhooks' ) ),
				'msg'			=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

		ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>update_term</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $return_args, $term_id, $taxonomy, $term_args ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo __( "Contains all the data we send back to the webhook action caller. The data includes the following key: msg, success, data", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$term_id</strong> (string)<br>
		<?php echo __( "Contains the id of the term that was just updated.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$taxonomy</strong> (string)<br>
		<?php echo __( "Contains the taxonomy slug of the taxonomy the term is connected to.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$term_args</strong> (array)<br>
		<?php echo __( "Contains the additional information you set within the update_term webhook action.", 'wp-webhooks' ); ?>
	</li>
</ol>
			<?php
			$parameter['do_action']['description'] = ob_get_clean();

			$returns_code = array (
				'success' => true,
				'msg' => 'The taxonomy term was successfully updated.',
				'data' => 
				array (
					'term_id' => 93,
					'term_taxonomy_id' => 93,
				),
			);

			return array(
				'action'			=> 'update_term',
				'name'			  => __( 'Update taxonomy term', 'wp-webhooks' ),
				'sentence'			  => __( 'update a taxonomy term', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Update a taxonomy term for a specific taxonomy.', 'wp-webhooks' ),
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
			$taxonomy = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'taxonomy' );
			$alias_of = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'alias_of' );
			$description = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'description' );
			$parent = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'parent' ) );
			$slug = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'slug' );
			$name = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'name' );

			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $term_id ) ){
				$return_args['msg'] = __( "Please define the term_id argument first.", 'action-update_term' );
				return $return_args;
			}

			if( empty( $taxonomy ) ){
				$return_args['msg'] = __( "Please define the taxonomy argument first.", 'action-update_term' );
				return $return_args;
			}

			$term = get_term( $term_id, $taxonomy );

			if( is_wp_error( $term ) ){
				$return_args['data'] = $term;
				$return_args['msg'] = __( "An error occured while fetching your term.", 'action-update_term' );
				return $return_args;
			}

			if( empty( $term ) ){
				$return_args['msg'] = __( "We could not find any term for your given term_id", 'action-update_term' );
				return $return_args;
			}

			$term_args = array();

			if( ! empty( $name ) ){
				$term_args['name'] = $name;
			}

			if( ! empty( $alias_of ) ){
				$term_args['alias_of'] = $alias_of;
			}

			if( ! empty( $description ) ){
				$term_args['description'] = $description;
			}

			if( ! empty( $parent ) ){
				$term_args['parent'] = $parent;
			}

			if( ! empty( $slug ) ){
				$term_args['slug'] = $slug;
			}

			$term_data = wp_update_term( $term_id, $taxonomy, $term_args );
 
			if ( ! is_wp_error( $term_data ) ) {
				$return_args['success'] = true;
				$return_args['data'] = $term_data;
				$return_args['msg'] = __( "The taxonomy term was successfully updated.", 'action-update_term' );
			} else {
				$return_args['data'] = $term_data;
				$return_args['msg'] = __( "Error while updating the taxonomy term.", 'action-update_term' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $term_id, $taxonomy, $term_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.