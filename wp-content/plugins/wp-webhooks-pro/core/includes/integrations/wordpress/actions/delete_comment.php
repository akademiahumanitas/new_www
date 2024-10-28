<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_delete_comment' ) ) :

	/**
	 * Load the delete_comment action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_delete_comment {

		public function is_active(){

			//Backwards compatibility for the "Comments" integration
			if( class_exists( 'WP_Webhooks_Comments' ) ){
				return false;
			}

			return true;
		}

		public function get_details(){

				$parameter = array(
				'comment_id' => array( 'required' => true, 'short_description' => __( '(int) The comment id of the comment you want to delete.', 'wp-webhooks' ) ),
				'force_delete' => array( 
					'type' => 'select',
					'choices' => array(
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					),
					'multiple' => false,
					'default_value' => 'no',
					'short_description' => __( '(string) Wether you want to bypass the trash or not. You can set this value to "yes" or "no". Default "no"', 'wp-webhooks' ),
				),
				'do_action'	  => array( 'short_description' => __( 'Advanced: Register a custom action after this webhook is executed.', 'wp-webhooks' ) )
			);

			ob_start();
			?>
<?php echo __( "Please note: The attachment is moved to the trash instead of being permanently deleted, unless trash for media is disabled, the item is already in the trash, or force_delete is true.", 'wp-webhooks' ); ?>
			<?php
			$parameter['force_delete']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>delete_attachment</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $comment_id, $deleted, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$comment_id</strong> (integer)<br>
		<?php echo __( "The id of the comment you deleted.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$deleted</strong> (bool)<br>
		<?php echo __( "True if the comment was deleted, false if not.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo __( "All the values that are sent back as a response to the initial webhook action caller.", 'wp-webhooks' ); ?>
	</li>
</ol>
			<?php
			$parameter['do_action']['description'] = ob_get_clean();

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		   => array( 'short_description' => __( '(array) The comment id as comment_id and the force_delete status.', 'wp-webhooks' ) ),
				'msg'			=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The comment was successfully trashed.',
				'data' => 
				array (
				  'comment_id' => 4,
				  'force_delete' => false,
				),
			);

			return array(
				'action'			=> 'delete_comment',
				'name'			  => __( 'Delete comment', 'wp-webhooks' ),
				'sentence'			  => __( 'delete a comment', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Delete a comment using webhooks.', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'wordpress',
				'premium' 			=> false,
			);

		}

		public function execute( $return_data, $response_body ){

			$textdomain_context = 'delete_comment';
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'comment_id'   => 0,
					'force_delete'   => 0,
				),
			);

			$comment_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_id' ));
			$force_delete = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'force_delete' ) == 'yes' ) ? true : false;

			$do_action = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );


			if( empty( $comment_id ) ){
				$return_args['msg'] = __( "A comment id is required to delete the comment.", 'wp-webhooks' );

				return $return_args;
			}
 
			$return_args['data']['comment_id'] = $comment_id;
			$return_args['data']['force_delete'] = $force_delete;
			
			$deleted = wp_delete_comment( $comment_id, $force_delete );

			if( $deleted ){
				$return_args['success'] = true;

				if( $force_delete ){
					$return_args['msg'] = __( "The comment was successfully deleted.", 'wp-webhooks' );
				} else {
					$return_args['msg'] = __( "The comment was successfully trashed.", 'wp-webhooks' );
				}
				
			} else {
				$return_args['msg'] = __( "Error while deleting the comment.", 'wp-webhooks' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $comment_id, $deleted, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.