<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_jetengine_Actions_jetengine_get_relations' ) ) :

	/**
	 * Load the jetengine_get_relations action
	 *
	 * @since 4.3.7
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_jetengine_Actions_jetengine_get_relations {

	public function get_details(){

		$parameter = array(
			'post_id' => array( 'required' => true, 'short_description' => __( 'The ID of the post you want to get the active relations from.', 'wp-webhooks' ) ),
			'do_action' => array( 'short_description' => __( 'Advanced: Register a custom action after the plugin fires this webhook.', 'wp-webhooks' ) )
		);

		$returns = array(
			'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg' => array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			'data' => array( 'short_description' => __( '(array) The adjusted meta data, includnig the response of the related ACF function." )', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The relations have been returned successfully.',
			'data' => 
			array (
			  'post_id' => 9114,
			  'post_type' => 'page',
			  'type_name' => 'posts::page',
			  'relations' => 
			  array (
				3 => 
				array (
				  'relation_id' => '3',
				  'relation_type' => 'children',
				  'relation_object' => 'posts::post',
				  'relations' => 
				  array (
					0 => '7914',
				  ),
				),
				2 => 
				array (
				  'relation_id' => '2',
				  'relation_type' => 'children',
				  'relation_object' => 'posts::post',
				  'relations' => 
				  array (
					0 => '7914',
				  ),
				),
				1 => 
				array (
				  'relation_id' => '1',
				  'relation_type' => 'children',
				  'relation_object' => 'posts::post',
				  'relations' => 
				  array (
					0 => '7914',
				  ),
				),
			  ),
			),
		);

			ob_start();
		?>
<?php echo __( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the jetengine_get_relations action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 1 );
function my_custom_callback_function( $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo __( "An array containing the information we will send back as the response to the initial webhook caller.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			return array(
				'action'			=> 'jetengine_get_relations',
				'name'			  => __( 'Get active post relations', 'wp-webhooks' ),
				'sentence'			  => __( 'get all active relations for a post', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Get all active relations for a post within "JetEngine".', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'jetengine',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(),
			);
	
			$post_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_id' ) );
			$do_action = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $post_id ) ){
				$return_args['msg'] = __( "Please set the post_id argument first.", 'wp-webhooks' );
				return $return_args;
			}

			$post_type = get_post_type( $post_id );

			if( ! $post_type || ! isset( jet_engine()->relations->types_helper ) ){
				$return_args['msg'] = __( "We could not determine the given post type for your post id.", 'wp-webhooks' );
				return $return_args;
			}
	
			$type_name = jet_engine()->relations->types_helper->type_name_by_parts( 'posts', $post_type );
			$relations = jet_engine()->relations->get_active_relations();
			$relations_validated = array();

			if( ! empty( $relations ) ){
				foreach ( $relations  as $relation ) {

					$relation_id           = 0;
					$relation_ids           = array();
					$relation_object        = null;
					$relation_object_type = null;
		
					if( $relation->get_args( 'parent_object' ) === $type_name ){
						$relation_id = $relation->get_id();
						$relation_object = $relation->get_args( 'child_object' );
						$relation_object_type = 'children';
						$relation_ids = $relation->get_children( $post_id, 'ids' );
					} elseif( $relation->get_args( 'child_object' ) === $type_name ){
						$relation_id = $relation->get_id();
						$relation_object = $relation->get_args( 'parent_object' );
						$relation_object_type = 'parent';
						$relation_ids = $relation->get_parents( $post_id, 'ids' );
					}
		
					if( $relation_object && $relation_id ){
						$relations_validated[ $relation_id ] = array(
							'relation_id' => $relation_id,
							'relation_type' => $relation_object_type,
							'relation_object' => $relation_object,
							'relations' => $relation_ids,
						);
					}
		
				}
			}

			$return_args['success'] = true;
			$return_args['msg'] = __( "The relations have been returned successfully.", 'wp-webhooks' );
			$return_args['data']['post_id'] = $post_id;
			$return_args['data']['post_type'] = $post_type;
			$return_args['data']['type_name'] = $type_name;
			$return_args['data']['relations'] = $relations_validated;

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}
	
			return $return_args;
	
		}

	}

endif; // End if class_exists check.