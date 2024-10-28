<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_set_terms' ) ) :

	/**
	 * Load the set_terms action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_set_terms {

		public function is_active(){

			//Backwards compatibility for the "Comments" integration
			if( class_exists( 'WP_Webhooks_Manage_Taxonomy_Terms' ) ){
				return false;
			}

			return true;
		}

		public function get_details(){

				$parameter = array(
				'object_id'			=> array( 'required' => true, 'short_description' => __( 'The object to relate to. (Post ID)', 'wp-webhooks' ) ),
				'taxonomy'			=> array( 'required' => true, 'short_description' => __( 'The context in which to relate the term to the object. (Taxonomy slug)', 'wp-webhooks' ) ),
				'terms'			=> array( 'short_description' => __( 'The terms you want to set. Please see the description for more information.', 'wp-webhooks' ) ),
				'append'			=> array( 
					'type' => 'select',
					'choices' => array(
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					),
					'multiple' => false,
					'default_value' => 'no',
					'short_description' => __( 'Please set this value to "yes" in case you want to append the taxonomies. If set to no, all previous entries to the defined taxonomies will be deleted. Default "no"', 'wp-webhooks' ),
				),
				'do_action'		  => array( 'short_description' => __( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More infos are in the description.', 'wp-webhooks' ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		   => array( 'short_description' => __( '(mixed) The taxonomy term ids on success or wp_error on failure.', 'wp-webhooks' ) ),
				'msg'			=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<?php echo __( "To append taxonomy terms, simple separate them with a comma. You can either use a single term slug, single term id, or array of either term slugs or ids. Here is an example:", 'wp-webhooks' ); ?>
<pre>term-1,term-2,term-3</pre>
<?php echo __( "<strong>Important</strong>: Passing an empty value will remove all related terms.", 'wp-webhooks' ); ?>
		<?php
		$parameter['terms']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "Set this argument to <strong>yes</strong> if you to append the taxonomies. If the argument is set to <strong>no</strong>, all existing taxonomies on the given post (via the object_id argument) will be removed before the new ones are added. Default is <strong>no</strong>", 'wp-webhooks' ); ?>
		<?php
		$parameter['append']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>set_terms</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 5 );
function my_custom_callback_function( $return_args, $object_id, $terms, $taxonomy, $append ){
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
		<strong>$object_id</strong> (integer)<br>
		<?php echo __( "Contains the post id of the post you want to assign the taxonomies to.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$terms</strong> (string)<br>
		<?php echo __( "Contains the value of the <strong>terms</strong> argument that was set within the webhook call.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$taxonomy</strong> (string)<br>
		<?php echo __( "Contains the taxonomy slug of the taxonomy you want to assign to the given post.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$append</strong> (bool)<br>
		<?php echo __( "Contains <strong>true</strong> if the <strong>append</strong> argument was set to <strong>yes</strong> and <strong>false</strong> if it was set to <strong>no</strong>.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'Taxonomy terms were set successfully.',
			'data' => 
			array (
				0 => '17',
			),
		);

			return array(
				'action'			=> 'set_terms',
				'name'			  => __( 'Set taxonomy terms', 'wp-webhooks' ),
				'sentence'			  => __( 'set custom taxonomy terms', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Set (and create) taxonomy terms on a post.', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'wordpress',
				'premium' 			=> false,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => '',
			);

			$object_id		= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'object_id' ));
			$terms		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'terms' );
			$taxonomy		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'taxonomy' );
			$append		= ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'append' ) == 'yes' ) ? true : false;

			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $object_id ) || empty( $taxonomy ) ){
				$return_args['msg'] = __( "Object id and/or taxonomy not defined.", 'action-set_terms' );
				return $return_args;
			}

			$term_array = explode( ',', trim( $terms, ',' ) );
			if( empty( $term_array ) ){
				$term_array = array();
			}

			$term_taxonomy_ids = wp_set_object_terms( $object_id, $term_array, $taxonomy, $append );
 
			if ( ! is_wp_error( $term_taxonomy_ids ) ) {
				$return_args['success'] = true;
				$return_args['data'] = $term_taxonomy_ids;
				$return_args['msg'] = __( "Taxonomy terms were set successfully.", 'action-set_terms' );
			} else {
				$return_args['data'] = $term_taxonomy_ids;
				$return_args['msg'] = __( "Error while setting taxonomy terms", 'action-set_terms' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $object_id, $terms, $taxonomy, $append );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.