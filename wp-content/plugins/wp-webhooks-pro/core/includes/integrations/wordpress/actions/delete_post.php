<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_delete_post' ) ) :

	/**
	 * Load the delete_post action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_delete_post {

		/*
	 * The core logic to delete a specified user
	 */
	public function get_details(){


		$parameter = array(
			'post_id'	   => array( 'required' => true, 'short_description' => __( 'The post id of your specified post. This field is required.', 'wp-webhooks' ) ),
			'force_delete'  => array(
				'type' => 'select',
				'choices' => array(
					'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
					'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
				),
				'multiple' => false,
				'default_value' => 'no',
				'short_description' => __( '(optional) Whether to bypass trash and force deletion (added in WordPress 2.9). Possible values: "yes" and "no". Default: "no". Please note that soft deletion just works for the "post" and "page" post type.', 'wp-webhooks' ),
				'description' => __( "In case you set the <strong>force_delete</strong> argument to <strong>yes</strong>, the post will be completely removed from your WordPress website.", 'wp-webhooks' ),
			),
			'do_action'	 => array( 'short_description' => __( 'Advanced: Register a custom action after the plugin fires this webhook.', 'wp-webhooks' ) )
		);

		ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>delete_post</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $post, $post_id, $check, $force_delete ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$post</strong> (object)<br>
		<?php echo __( "Contains the WordPress post object of the already deleted post.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$post_id</strong> (integer)<br>
		<?php echo __( "Contains the post id of the deleted post.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$check</strong> (mixed)<br>
		<?php echo __( "Contains the response of the wp_delete_post() function.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$force_delete</strong> (string)<br>
		<?php echo __( "Returns either yes or no, depending on your settings for the force_delete argument.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Post related data as an array. We return the post id with the key "post_id" and the force delete boolean with the key "force_delete". E.g. array( \'data\' => array(...) )', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);
		
			$returns_code = array (
				'success' => true,
				'msg' => 'Post successfully deleted.',
				'data' => 
				array (
				  'post_id' => 1337,
				  'force_delete' => false,
				  'permalink' => 'https://yourdomain.test/?p=1337',
				),
			);

			$description = array(
				'tipps' => array(
					__( 'Please note that deleting a post without defining the <strong>force_delete</strong> argument, only moves default posts and pages to the trash (wherever applicable) - otherwise they will be directly deleted.', 'wp-webhooks' )
				),
			);

			return array(
				'action'			=> 'delete_post',
				'name'			  => __( 'Delete post', 'wp-webhooks' ),
				'sentence'			  => __( 'delete a post', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => sprintf( __( 'Delete a post via %s.', 'wp-webhooks' ), WPWHPRO()->settings->get_page_title() ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> false,
			);

		}

		/**
		 * The action for deleting a post
		 */
		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'post_id' => 0,
					'force_delete' => false,
					'permalink' => ''
				)
			);

			$post_id		 = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_id' ) ) ? WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_id' ) : 0;
			$force_delete	= ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'force_delete' ) == 'yes' ) ? true : false;
			$do_action	   = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' ) ) ? WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' ) : '';
			$post = '';
			$check = '';

			if( ! empty( $post_id ) ){
				$post = get_post( $post_id );
			}

			if( ! empty( $post ) ){
				if( ! empty( $post->ID ) ){

					$permalink = get_permalink( $post_id );
					$check = wp_delete_post( $post->ID, $force_delete );

					if ( $check ) {

						do_action( 'wpwhpro/run/delete_action_post_deleted' );

						$return_args['msg']	 = __( "Post successfully deleted.", 'wp-webhooks' );
						$return_args['success'] = true;
						$return_args['data']['post_id'] = $post->ID;
						$return_args['data']['force_delete'] = $force_delete;
						$return_args['data']['permalink'] = $permalink;
					} else {
						$return_args['msg']  = __( "Error deleting post. Please check wp_delete_post( " . $post->ID . ", " . $force_delete . " ) for more information.", 'wp-webhooks' );
						$return_args['data']['post_id'] = $post->ID;
						$return_args['data']['force_delete'] = $force_delete;
					}

				} else {
					$return_args['msg'] = __( "Could not delete the post: No ID given.", 'wp-webhooks' );
				}
			} else {
				$return_args['msg']  = __( "No post found to your specified post id.", 'wp-webhooks' );
				$return_args['data']['post_id'] = $post_id;
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $post, $post_id, $check, $force_delete );
			}

			return $return_args;
		}

	}

endif; // End if class_exists check.