<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_delete_attachment' ) ) :

	/**
	 * Load the delete_attachment action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_delete_attachment {

		public function is_active(){

			//Backwards compatibility for the "Manage Media Files" integration
			if( class_exists( 'WP_Webhooks_Pro_Manage_Media_Files' ) ){
				return false;
			}

			return true;
		}

		public function get_details(){

				$parameter = array(
				'attachment_id'			=> array( 'required' => true, 'short_description' => __( 'The id of the attachment you want to delete.', 'wp-webhooks' ) ),
				'force_delete' => array( 'short_description' => __( 'Whether to bypass trash and force deletion. Default: no'. 'Please read the description for more information.', 'wp-webhooks' ) ),
				'do_action'	  => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) )
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
function my_custom_callback_function( $attachment_id, $parent_post_id, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$attachment_id</strong> (integer)<br>
		<?php echo __( "The attachment id of the attachment you just deleted.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$parent_post_id</strong> (integer)<br>
		<?php echo __( "The parent post id. In case it wasn't given, we return 0.", 'wp-webhooks' ); ?>
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
				'data'		=> array( 'short_description' => __( '(mixed) The attachment data (post data) on success, false or null on error.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'Attachment successfully deleted.',
				'data' => 
				array (
				  'data' => NULL,
				  'post_data' => 
				  array (
					'ID' => 14,
					'post_author' => '0',
					'post_date' => '2021-06-01 21:29:30',
					'post_date_gmt' => '2021-06-01 21:29:30',
					'post_content' => '',
					'post_title' => 'icon',
					'post_excerpt' => '',
					'post_status' => 'inherit',
					'comment_status' => 'open',
					'ping_status' => 'closed',
					'post_password' => '',
					'post_name' => 'icon-2',
					'to_ping' => '',
					'pinged' => '',
					'post_modified' => '2021-06-01 21:29:30',
					'post_modified_gmt' => '2021-06-01 21:29:30',
					'post_content_filtered' => '',
					'post_parent' => 0,
					'guid' => 'https://yourdomain.test/wp-content/uploads/2021/06/icon.png',
					'menu_order' => 0,
					'post_type' => 'attachment',
					'post_mime_type' => 'image/png',
					'comment_count' => '0',
					'filter' => 'raw',
				  ),
				),
			);

			return array(
				'action'			=> 'delete_attachment',
				'name'			  => __( 'Delete attachment', 'wp-webhooks' ),
				'sentence'			  => __( 'delete an attachment', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Delete an attachment from your website using webhooks.', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'wordpress',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'data' => null,
				)
			);

			$attachment_id  = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'attachment_id' ) );
			$force_delete   = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'force_delete' ) == 'yes' ) ? true : false;
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( ! empty( $attachment_id ) ){

				$check = wp_delete_attachment( $attachment_id, $force_delete );

				if( ! empty( $check ) ){

					$return_args['data']['post_data'] = $check;
					$return_args['success'] = true;
					$return_args['msg'] = __( "Attachment successfully deleted.", 'action-delete_attachment-success' );

				} else {

					$return_args['data']['post_data'] = $check;
					$return_args['msg'] = __( "Error while deleting the attachment.", 'action-delete_attachment-success' );

				}


			} else {

				$return_args['msg'] = __( "No attachment id set.", 'action-delete_attachment-success' );

			}



			if( ! empty( $do_action ) ){
				do_action( $do_action, $attachment_id, $force_delete, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.