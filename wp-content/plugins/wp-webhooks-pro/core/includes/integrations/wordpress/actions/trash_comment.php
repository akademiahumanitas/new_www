<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_trash_comment' ) ) :

	/**
	 * Load the trash_comment action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_trash_comment {

		public function is_active(){

			//Backwards compatibility for the "Comments" integration
			if( class_exists( 'WP_Webhooks_Comments' ) ){
				return false;
			}

			return true;
		}

		public function get_details(){

				$parameter = array(
				'comment_id' => array( 'required' => true, 'short_description' => __( '(int) The comment id of the comment you want to trash.', 'wp-webhooks' ) ),
				'do_action' => array( 'short_description' => __( 'Advanced: Register a custom action after the action was fired.', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>trash_comment</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $comment_id, $trashed, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$comment_id</strong> (integer)<br>
		<?php echo __( "The ID of the comment you trashed.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$trashed</strong> (bool)<br>
		<?php echo __( "The respone of the wp_trash_comment() function.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo __( "Contains all the data we send back to the webhook action caller. The data includes the following key: msg, success, data", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		   => array( 'short_description' => __( '(array) The comment id as comment_id.', 'wp-webhooks' ) ),
				'msg'			=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The comment was successfully trashed.',
				'data' => 
				array (
				  'comment_id' => 4,
				),
			);

			return array(
				'action'			=> 'trash_comment',
				'name'			  => __( 'Trash comment', 'wp-webhooks' ),
				'sentence'			  => __( 'trash a comment', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Trash a comment using webhooks.', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'wordpress',
				'premium' 			=> false,
			);

		}

		public function execute( $return_data, $response_body ){

			$textdomain_context = 'trash_comment';
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'comment_id'   => 0,
				),
			);

			$comment_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_id' ));

			$do_action = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );


			if( empty( $comment_id ) ){
				$return_args['msg'] = __( "A comment id is required to trash the comment.", 'wp-webhooks' );

				return $return_args;
			}
 
			$return_args['data']['comment_id'] = $comment_id;
			
			$trashed = wp_trash_comment( $comment_id );

			if( $trashed ){
				$return_args['success'] = true;
				$return_args['msg'] = __( "The comment was successfully trashed.", 'wp-webhooks' );
			} else {
				$return_args['msg'] = __( "Error while trashing the comment.", 'wp-webhooks' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $comment_id, $trashed, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.