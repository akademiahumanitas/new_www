<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_delete_folder' ) ) :

	/**
	 * Load the delete_folder action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_delete_folder {

		public function is_active(){

			//Backwards compatibility for the "Comments" integration
			if( class_exists( 'WP_Webhooks_Pro_Remote_File_Control' ) ){
				return false;
			}

			return true;
		}

		public function get_details(){

				$parameter = array(
				'folder'	   => array( 'required' => true, 'short_description' => __( 'The relative path as well as the folder name. For example: wp-content/themes/demo-theme/demo-folder (See the main description for more information)', 'wp-webhooks' ) ),
				'do_action'	 => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook.', 'wp-webhooks' ) )
			);

			ob_start();
			?>
<p><?php echo __( 'In case you want to delete a folder within the WordPress root folder, just declare the folder itself:', 'wp-webhooks' ); ?></p>
<br>
<pre>demo-folder</pre>
				<?php
			$parameter['folder']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>delete_attachment</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 2 );
function my_custom_callback_function(  $return_args, $folder ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)
		<?php echo __( "All the values that are sent back as a response to the initial webhook action caller.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$folder</strong> (string)
		<?php echo __( "The relative path of the folder you deleted.", 'wp-webhooks' ); ?>
	</li>
</ol>
			<?php
			$parameter['do_action']['description'] = ob_get_clean();

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'Folder and sub folder/files successfully deleted.',
			);

			$description = array(
				'tipps' => array(
					__( 'For security reasons, we restrict the deletion of folders to the WordPress root folder and its sub folders. This means, that you have to define the path in a relative way. Here is an example:', 'wp-webhooks' ) . '<code>wp-content/themes/demo-theme/demo-folder</code>',
				),
			);

			return array(
				'action'			=> 'delete_folder',
				'name'			  => __( 'Delete folder', 'wp-webhooks' ),
				'sentence'			  => __( 'delete a folder', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Delete a folder and all of its sub folders and files via a webhook inside of your WordPress folder structure.', 'wp-webhooks' ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$file_helpers = WPWHPRO()->integrations->get_helper( 'wordpress', 'file_helpers' );
			$return_args = array(
				'success' => false,
				'msg' => ''
			);

			$folder	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'folder' );
			$do_action   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( ! empty( $folder ) ){
				$check = $file_helpers->delete_folder( $folder );
				if( $check ){
					$return_args['msg'] = __( "Folder and sub folder/files successfully deleted.", 'wp-webhooks' );
					$return_args['success'] = true;
				} else {
					$return_args['msg'] = __( "Folder was not deleted because of an error.", 'wp-webhooks' );
				}
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $folder );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.