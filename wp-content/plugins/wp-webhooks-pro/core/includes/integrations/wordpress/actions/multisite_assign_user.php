<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_multisite_assign_user' ) ) :

	/**
	 * Load the multisite_assign_user action
	 *
	 * @since 5.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_multisite_assign_user {

		public function is_active(){
			$return = false;

			if( function_exists('is_multisite') ){
				$return = is_multisite();
			}

			return $return;
		}

	public function get_details(){

		$parameter = array(
			'user' => array( 'required' => true, 'short_description' => __( 'The user email or ID of the user you want to assign to the specific sub sites.', 'wp-webhooks' ) ),
			'blog_ids' => array( 'required' => true, 'short_description' => __( 'A comma-separated list of the IDs of the specific sub sites you want to assign the user to. Or "all" for all sub sites.', 'wp-webhooks' ) ),
			'role' => array( 'short_description' => __( 'The role you want to assign to the user. Default: The default role of your blog.', 'wp-webhooks' ) ),
			'do_action' => array( 'short_description' => __( 'Advanced: Register a custom action after the plugin fires this webhook.', 'wp-webhooks' ) )
		);

		$returns = array(
			'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg' => array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			'data' => array( 'short_description' => __( '(array) The adjusted meta data, includnig the response of the related WP function." )', 'wp-webhooks' ) ),
		);

		ob_start();
		?>
<?php echo __( "In case you want to add the user to multiple sub-sites, you can either comma-separate them like <code>2,3,12,44</code>, or you can add them via a JSON construct:", 'wp-webhooks' ); ?>
<pre>{
  23,
  3,
  44
}</pre>
<?php echo __( "Set this argument to <strong>all</strong> to assign the user to all sub-sits of the network.", 'wp-webhooks' ); ?>
		<?php
		$parameter['blog_ids']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>manage_term_meta</strong> action was fired.", 'wp-webhooks' ); ?>
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
		<strong>$return_args</strong> (array)
		<?php echo __( "Contains all the data we send back to the webhook action caller. The data includes the following key: msg, success, data", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'The user has assigned to the sites successfully.',
			'data' => 
			array (
			  'user_id' => 123,
			  'role' => 'editor',
			  'sites' => 
			  array (
				0 => '1',
				1 => '2',
			  ),
			  'errors' => 
			  array (
			  ),
			),
		);

			$description = array(
				'tipps' => array(
					__( "To create post meta values visually, you can use our meta value generator at: <a title=\"Visit our meta value generator\" href=\"https://wp-webhooks.com/blog/how-to-update-custom-post-meta-values-with-wp-webhooks/\" target=\"_blank\">https://wp-webhooks.com/blog/how-to-update-custom-post-meta-values-with-wp-webhooks/</a>.", 'wp-webhooks' ),
				)
			);

			return array(
				'action'			=> 'multisite_assign_user',
				'name'			  => __( 'Multisite assign user', 'wp-webhooks' ),
				'sentence'			  => __( 'assign a user to a multisite sub-site', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Assign a user to one, multiple, or all blogs within a WordPress multisite.', 'wp-webhooks' ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(),
			);
	
			$user = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$blog_ids = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'blog_ids' );
			$role = sanitize_title( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'role' ) );
			$do_action = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $blog_ids ) ){
				$return_args['msg'] = __( "Please set the blog_ids argument first.", 'wp-webhooks' );
				return $return_args;
			}

			$user_id = 0;

            if( ! empty( $user ) && is_numeric( $user ) ){
                $user_id = intval( $user );
            } elseif( ! empty( $user ) && is_email( $user ) ) {
                $user_data = get_user_by( 'email', $user );
                if( ! empty( $user_data ) && isset( $user_data->ID ) && ! empty( $user_data->ID ) ){
                    $user_id = $user_data->ID;
                }
            }

            if( empty( $user_id ) ){
                $return_args['msg'] = __( "We could not find a user for your given user argument value.", 'action-rcp_disable_membership-error' );
				return $return_args;
            }

			if( empty( $role ) ){
				$role = get_option( 'default_role' );
			}

			$errors = array();

			if( $blog_ids === 'all' ){

				$blog_ids_array = array();
				$blogs = get_sites();
				foreach( $blogs as $b ){
					if( isset( $b->blog_id  ) && ! empty( $b->blog_id  ) ){
						$blog_ids_array[] = $b->blog_id;
					}
				}

			} else {
				if( WPWHPRO()->helpers->is_json( $blog_ids ) ){
					$blog_ids_array = json_decode( $blog_ids, true );
				} else {
					$blog_ids_array = array_map( "trim", explode( ',', $blog_ids ) );
				}
			}
			
			
			if( ! empty( $blog_ids_array ) && is_array( $blog_ids_array ) ){
				foreach( $blog_ids_array as $blog_id ){

					$blog_id = intval( $blog_id );

					$result = add_user_to_blog( $blog_id, $user_id, $role );
					if( is_wp_error( $result ) ){
						$error[] = $result->get_error_message();
					}
				}
			}

			$return_args['data']['user_id'] = $user_id;
			$return_args['data']['role'] = $role;
			$return_args['data']['sites'] = $blog_ids_array;
			$return_args['data']['errors'] = $errors;

			if( empty( $errors ) ){
				$return_args['msg'] = __( "The user has assigned to the sites successfully.", 'action-plugin_activate-success' );
				$return_args['success'] = true;
			} else {
				$return_args['msg'] = __( "One or more errors occured while adding the user to the multisite sites.", 'action-plugin_activate-success' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}
	
			return $return_args;
	
		}

	}

endif; // End if class_exists check.