<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_rename_file_folder' ) ) :

	/**
	 * Load the rename_file_folder action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_rename_file_folder {

		public function is_active(){

			//Backwards compatibility for the "Comments" integration
			if( class_exists( 'WP_Webhooks_Pro_Remote_File_Control' ) ){
				return false;
			}

			return true;
		}

		public function get_details(){

				$parameter = array(
				'source_path'	   => array( 'required' => true, 'short_description' => __( 'The relative path of the folder you want to rename (and the file name and extension if you want to rename a file). For example: wp-content/themes/demo-theme/demo-folder or for a file wp-content/themes/demo-theme/demo-file.php', 'wp-webhooks' ) ),
				'destination_path'	   => array( 'required' => true, 'short_description' => __( 'The relative path with the new folder name (or the new file name and extension if you want to rename a file). For example: wp-content/themes/demo-theme/new-demo-folder or for a file wp-content/themes/demo-theme/new-demo-file.php', 'wp-webhooks' ) ),
				'do_action'	 => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook.', 'wp-webhooks' ) )
			);

			ob_start();
			?>
<p><?php echo __( 'In case you want to rename a file or a folder inside the WordPress root folder, just declare the file/folder itself:', 'wp-webhooks' ); ?></p>
<br>
<pre>demo-file.php</pre>
			<?php
			$parameter['destination_path']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>manage_term_meta</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $return_args, $source_path, $destination_path ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)
		<?php echo __( "Contains all the data we send back to the webhook action caller. The data includes the following key: msg, success", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$source_path</strong> (string)
		<?php echo __( "The path of the folder/file you moved.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$destination_path</strong> (string)
		<?php echo __( "The new path after the folder/file was moved.", 'wp-webhooks' ); ?>
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
				'msg' => 'The file/folder was successfully renamed.',
			);

			$description = array(
				'tipps' => array(
					__( 'For security reasons, we restrict renaming of files to the WordPress root folder and its sub folders. This means, that you have to define the destination_path in a relative way. Here is an example:', 'wp-webhooks' ) . ' <code>wp-content/uploads/demo-file.php</code>',
					__( 'It is also possible to change the extension of a file. just change it for the destination path.', 'wp-webhooks' ),
				),
			);

			return array(
				'action'			=> 'rename_file_folder',
				'name'			  => __( 'Rename file or folder', 'wp-webhooks' ),
				'sentence'			  => __( 'rename a file or a folder', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Rename a local file or folder via a webhook inside of your WordPress folder structure.', 'wp-webhooks' ),
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

			$source_path	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'source_path' );
			$destination_path	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'destination_path' );
			$do_action   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( ! empty( $source_path ) && ! empty( $destination_path ) ){
				$check = $file_helpers->rename_file_or_folder( $source_path, $destination_path );
				if( $check ){
					$return_args['msg'] = __( "The file/folder was successfully renamed.", 'wp-webhooks' );
					$return_args['success'] = true;
				} else {
					$return_args['msg'] = __( "File/folder was not renamed because of an error.", 'wp-webhooks' );
				}
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $source_path, $destination_path );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.