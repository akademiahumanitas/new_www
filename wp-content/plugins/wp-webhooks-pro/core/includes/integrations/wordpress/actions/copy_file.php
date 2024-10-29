<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_copy_file' ) ) :

	/**
	 * Load the copy_file action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_copy_file {

		public function is_active(){

			//Backwards compatibility for the "Comments" integration
			if( class_exists( 'WP_Webhooks_Pro_Remote_File_Control' ) ){
				return false;
			}

			return true;
		}

		public function get_details(){

				$parameter = array(
				'source_path'	   => array( 'required' => true, 'short_description' => __( 'The relative path of the file you want to copy. This can be a relative path or an external url. For example: wp-content/themes/demo-theme/demo-file.php or a full url like https://my-domain/image.jpg', 'wp-webhooks' ) ),
				'destination_path'	   => array( 'required' => true, 'short_description' => __( 'The relative path as well as file name. For example: wp-content/uploads/demo-image.jpg (See the main description for more information)', 'wp-webhooks' ) ),
				'do_action'	 => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook.', 'wp-webhooks' ) )
			);

			ob_start();
			?>
<p><?php echo __( 'Please note: If destination_path is a URL, the copy operation may fail if the wrapper does not support overwriting of existing files. If the destination file already exists, it will be overwritten.', 'wp-webhooks' ); ?></p>
<br>
<pre>wp-content/uploads/demo-image.jpg</pre>
<br>
<p><?php echo __( 'In case you want to copy a file into the WordPress root folder, just declare the file itself:', 'wp-webhooks' ); ?></p>
<br>
<pre>demo-image.jpg</pre>
<br>
<br>
<p><?php echo __( 'It is also possible to rename the file while you copy it. Just set a custom file name for the destination_path:', 'wp-webhooks' ); ?></p>
<br>
<pre>wp-content/uploads/another-demo-image.jpg</pre>
			<?php
			$parameter['destination_path']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the action was fired.", 'wp-webhooks' ); ?>
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
		<strong>$return_args</strong> (array)<br>
		<?php echo __( "An array containing the information we will send back as the response to the initial webhook caller.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$source_path</strong> (string)<br>
		<?php echo __( "The source path you set within the webhook request.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$destination_path</strong> (string)<br>
		<?php echo __( "The destination path you set within the webhook request.", 'wp-webhooks' ); ?>
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
				'msg' => 'The file was successfully copied.',
			);

			$description = array(
				'tipps' => array(
					__( 'This webhook enables you to copy a local or remote file inside of your WordPress folder structure. (The previous file will not be removed)', 'wp-webhooks' ),
					__( 'For security reasons, we restrict copying of files to the WordPress root folder and its sub folders. This means, that you have to define the destination_path in a relative way. Here is an example:', 'wp-webhooks' ) . '<code>wp-content/uploads/demo-image.jpg</code>',
				)
			);

			return array(
				'action'			=> 'copy_file',
				'name'			  => __( 'Copy file', 'wp-webhooks' ),
				'sentence'			  => __( 'copy a file', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Copy a local or remote file via a webhook inside of your WordPress folder structure.', 'wp-webhooks' ),
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
				$check = $file_helpers->copy_file( $source_path, $destination_path );
				if( $check ){
					$return_args['msg'] = __( "The file was successfully copied.", 'wp-webhooks' );
					$return_args['success'] = true;
				} else {
					$return_args['msg'] = __( "File was not copied because of an error.", 'wp-webhooks' );
				}
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $source_path, $destination_path );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.