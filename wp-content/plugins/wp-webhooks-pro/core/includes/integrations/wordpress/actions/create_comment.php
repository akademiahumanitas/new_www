<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_create_comment' ) ) :

	/**
	 * Load the create_comment action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_create_comment {

		public function is_active(){

			//Backwards compatibility for the "Comments" integration
			if( class_exists( 'WP_Webhooks_Comments' ) ){
				return false;
			}

			return true;
		}

		public function get_details(){

				$parameter = array(
				'comment_agent' => array( 'short_description' => __( '(string) The HTTP user agent of the comment_author when the comment was submitted. Default empty.', 'wp-webhooks' ) ),
				'comment_approved' => array( 'short_description' => __( '(int|string) Whether the comment has been approved. Default 1.', 'wp-webhooks' ) ),
				'comment_author' => array( 'short_description' => __( '(string) The name of the author of the comment. Default empty.', 'wp-webhooks' ) ),
				'comment_author_email' => array( 'short_description' => __( '(string) The email address of the $comment_author. Default empty.', 'wp-webhooks' ) ),
				'comment_author_IP' => array( 'short_description' => __( '(string) The IP address of the $comment_author. Default empty.', 'wp-webhooks' ) ),
				'comment_author_url' => array( 'short_description' => __( '(string) The URL address of the $comment_author. Default empty.', 'wp-webhooks' ) ),
				'comment_content' => array( 'short_description' => __( '(string) The content of the comment. Default empty.', 'wp-webhooks' ) ),
				'comment_date' => array( 'short_description' => __( '(string) The date the comment was submitted. To set the date manually, comment_date_gmt must also be specified. Default is the current time.', 'wp-webhooks' ) ),
				'comment_date_gmt' => array( 'short_description' => __( '(string) The date the comment was submitted in the GMT timezone. Default is comment_date in the site\'s GMT timezone.', 'wp-webhooks' ) ),
				'comment_karma' => array( 'short_description' => __( '(int) The karma of the comment. Default 0.', 'wp-webhooks' ) ),
				'comment_parent' => array( 'short_description' => __( '(int) ID of this comment\'s parent, if any. Default 0.', 'wp-webhooks' ) ),
				'comment_post_ID' => array( 'short_description' => __( '(int) ID of the post that relates to the comment, if any. Default 0.', 'wp-webhooks' ) ),
				'comment_type' => array( 'short_description' => __( '(string) Comment type. Default empty.', 'wp-webhooks' ) ),
				'comment_meta' => array( 'short_description' => __( '(array) Optional. Array of key/value pairs to be stored in commentmeta for the new comment. More info within the description.', 'wp-webhooks' ) ),
				'user_id' => array( 'short_description' => __( '(int) ID of the user who submitted the comment. Default 0.', 'wp-webhooks' ) ),
				'do_action'	 => array( 'short_description' => __( 'Advanced: Register a custom action after this webhook was fired.', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<p><?php echo __( 'You can also add custom comment meta. Here is an example on how this would look like using the simple structure (We also support json):', 'wp-webhooks' ); ?></p>
<br><br>
<pre>meta_key_1,meta_value_1;my_second_key,add_my_value</pre>
<br><br>
<?php echo __( 'To separate the meta from the value, you can use a comma ",". To separate multiple meta settings from each other, easily separate them with a semicolon ";" (It is not necessary to set a semicolon at the end of the last one)', 'wp-webhooks' ); ?>
<br><br>
<?php echo __( 'This is an example on how you can include the comment meta using JSON.', 'wp-webhooks' ); ?>
<br>
<pre>
{
	"meta_key_1": "This is my meta value 1",
	"another_meta_key": "This is my second meta key!"
}
</pre>
		<?php
		$parameter['comment_meta']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $comment_id, $commentdata, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$comment_id</strong> (integer)<br>
		<?php echo __( "The ID of the newly created comment.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$commentdata</strong> (array)<br>
		<?php echo __( "Further daa about the created comment.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo __( "An array containing the information we will send back as the response to the initial webhook caller.", 'wp-webhooks' ); ?>
	</li>
</ol>
			<?php
			$parameter['do_action']['description'] = ob_get_clean();

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		   => array( 'short_description' => __( '(array) The data related to the comment, as well as the user and the post object, incl. the meta values.', 'wp-webhooks' ) ),
				'msg'			=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'Comment created successfully.',
				'data' => 
				array (
				  'comment_id' => 4,
				  'comment_data' => 
				  array (
					'comment_ID' => '4',
					'comment_post_ID' => '0',
					'comment_author' => '',
					'comment_author_email' => '',
					'comment_author_url' => '',
					'comment_author_IP' => '',
					'comment_date' => '2021-06-01 19:23:53',
					'comment_date_gmt' => '2021-06-01 19:23:53',
					'comment_content' => 'Demo comment',
					'comment_karma' => '0',
					'comment_approved' => '0',
					'comment_agent' => '',
					'comment_type' => 'comment',
					'comment_parent' => '0',
					'user_id' => '0',
				  ),
				  'comment_meta' => 
				  array (
				  ),
				  'current_post_id' => 0,
				  'current_post_data' => 
				  array (
				  ),
				  'current_post_data_meta' => 
				  array (
				  ),
				  'user_id' => 0,
				  'user_data' => 
				  array (
				  ),
				  'user_data_meta' => 
				  array (
				  ),
				),
			);

			return array(
				'action'			=> 'create_comment',
				'name'			  => __( 'Create comment', 'wp-webhooks' ),
				'sentence'			  => __( 'create a comment', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Create a comment using webhooks.', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'wordpress',
				'premium' 			=> false,
			);

		}

		public function execute( $return_data, $response_body ){

			$plugin_helpers = WPWHPRO()->integrations->get_helper( 'wordpress', 'comment_helpers' );
			$textdomain_context = 'create_comment';
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'comment_id'   => 0,
					'comment_data'  => array(),
					'comment_meta'  => array(),
					'current_post_id' => 0,
					'current_post_data' => array(),
					'current_post_data_meta' => array(),
					'user_id' => 0,
					'user_data' => array(),
					'user_data_meta' => array(),
				),
			);

			$comment_agent		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_agent' );
			$comment_approved		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_approved' );
			$comment_author		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_author' );
			$comment_author_email		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_author_email' );
			$comment_author_IP		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_author_IP' );
			$comment_author_url		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_author_url' );
			$comment_content		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_content' );
			$comment_date		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_date' );
			$comment_date_gmt		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_date_gmt' );
			$comment_karma		= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_karma' ) );
			$comment_parent		= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_parent' ) );
			$comment_post_ID		= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_post_ID' ) );
			$comment_type		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_type' );
			$comment_meta		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_meta' );
			$user_id		= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' ));
			$comment_ID		= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_ID' ));

			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			$commentdata = array();

			if( empty( $comment_agent ) ){
				$commentdata['comment_agent'] = '';
			} else {
				$commentdata['comment_agent'] = $comment_agent;
			}

			if( $comment_approved == 0 ){
				$commentdata['comment_approved'] = 0;
			} else {
				$commentdata['comment_approved'] = $comment_approved;
			}

			if( empty( $comment_author ) ){
				$commentdata['comment_author'] = '';
			} else {
				$commentdata['comment_author'] = $comment_author;
			}

			if( empty( $comment_author_email ) ){
				$commentdata['comment_author_email'] = '';
			} else {
				if( is_email( $comment_author_email ) ){
					$commentdata['comment_author_email'] = $comment_author_email;
				}
			}

			if( empty( $comment_author_IP ) ){
				$commentdata['comment_author_IP'] = '';
			} else {
				$commentdata['comment_author_IP'] = $comment_author_IP;
			}

			if( empty( $comment_author_url ) ){
				$commentdata['comment_author_url'] = '';
			} else {
				$commentdata['comment_author_url'] = $comment_author_url;
			}

			if( empty( $comment_date ) ){
				$commentdata['comment_date'] = current_time( 'mysql' );
			} else {
				$commentdata['comment_date'] = $comment_date;
			}

			if( empty( $comment_date_gmt ) ){
				if( ! empty( $commentdata['comment_date'] ) ){
					$commentdata['comment_date_gmt'] = $commentdata['comment_date'];
				} else {
					$commentdata['comment_date_gmt'] = current_time( 'mysql' );
				}
			} else {
				$commentdata['comment_date_gmt'] = $comment_date_gmt;
			}

			if( empty( $comment_content ) ){
				$commentdata['comment_content'] = '';
			} else {
				$commentdata['comment_content'] = $comment_content;
			}

			if( empty( $comment_karma ) ){
				$commentdata['comment_karma'] = 0;
			} else {
				$commentdata['comment_karma'] = $comment_karma;
			}

			if( empty( $comment_parent ) ){
				$commentdata['comment_parent'] = 0;
			} else {
				$commentdata['comment_parent'] = $comment_parent;
			}

			if( empty( $comment_post_ID ) ){
				$commentdata['comment_post_ID'] = 0;
			} else {
				$commentdata['comment_post_ID'] = $comment_post_ID;
			}

			if( empty( $comment_type ) ){
				$commentdata['comment_type'] = '';
			} else {
				$commentdata['comment_type'] = $comment_type;
			}

			if( empty( $user_id ) ){
				$commentdata['user_id'] = 0;
			} else {
				$commentdata['user_id'] = $user_id;
			}

			$commentdata = apply_filters( 'wpwhpro/webhooks/trigger_create_comment_commentdata', $commentdata );

			add_action( 'wp_insert_comment', array( $plugin_helpers, 'create_update_comment_add_meta' ), 8, 1 );
			$comment_id = wp_insert_comment( $commentdata );
			remove_action( 'wp_insert_comment', array( $plugin_helpers, 'create_update_comment_add_meta' ), 8 );
 
			if ( ! empty( $comment_id ) ) {
				$return_args['success'] = true;
				$return_args['data']['comment_id'] = $comment_id;
				$return_args['data']['comment_data'] = get_comment( $comment_id );
				$return_args['data']['comment_meta'] = get_comment_meta( $comment_id );

				$return_args['msg'] = __( "Comment created successfully.", 'action-' . $textdomain_context );

				$comment = get_comment( $comment_id );

				if( isset( $comment->comment_post_ID ) ){
					$post_id = $comment->comment_post_ID;
					if( ! empty( $post_id ) ){
						$return_args['data']['current_post_id'] = $post_id;
						$return_args['data']['current_post_data'] = get_post( $post_id );
						$return_args['data']['current_post_data_meta'] = get_post_meta( $post_id );
					}
				}
	
				if( isset( $comment->comment_author_email ) && is_email( $comment->comment_author_email ) ){
					$user = get_user_by( 'email', sanitize_email( $comment->comment_author_email ) );
					if( ! empty( $user ) && ! is_wp_error( $user ) ){
						$return_args['data']['user_id'] = $user->data->ID;
						$return_args['data']['user_data'] = $user;
						$return_args['data']['user_data_meta'] = get_user_meta( $user->data->ID );
	
						//Restrict password
						$restrict = apply_filters( 'wpwhpro/webhooks/action_create_comment_restrict_user_values', array( 'user_pass' ) );
						
						if( is_array( $restrict ) && ! empty( $restrict ) ){
	
							foreach( $restrict as $data_key ){
								if( ! empty( $return_args['data']['user_data'] ) && isset( $return_args['data']['user_data']->data ) && isset( $return_args['data']['user_data']->data->{$data_key} )){
									unset( $return_args['data']['user_data']->data->{$data_key} );
								}
							}
							
						}
	
					}
				}

			} else {
				$return_args['msg'] = __( "Error while the comment.", 'action-' . $textdomain_context );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $comment_id, $commentdata, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.