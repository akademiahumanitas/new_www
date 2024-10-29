<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_create_file' ) ) :

	/**
	 * Load the create_file action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_create_file {

		public function is_active(){

			//Backwards compatibility for the "Comments" integration
			if( class_exists( 'WP_Webhooks_Pro_Remote_File_Control' ) ){
				return false;
			}

			return true;
		}

		public function get_details(){

				$parameter = array(
				'file'	   => array( 'required' => true, 'short_description' => __( 'The path as well as the file name and extension. For example: wp-content/themes/demo-theme/index.php (See the main description for more information)', 'wp-webhooks' ) ),
				'content'	   => array( 'required' => true, 'short_description' => __( 'The content for your file.', 'wp-webhooks' ) ),
				'mode'	   => array( 'short_description' => __( 'The mode of the file. Default "w" (Write)', 'wp-webhooks' ) ),
				'do_action'	 => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) )
			);

			ob_start();
			?>
<p><?php echo __( 'Please note: The folder structure must exist before you can create the file. Otherwise this webhook will return an error.', 'wp-webhooks' ); ?></p>
<br>
<pre>wp-content/themes/demo-theme/index.php</pre>
<br>
<p><?php echo __( 'In case you want to create a file within the WordPress root folder, just declare the file:', 'wp-webhooks' ); ?></p>
<br>
<pre>demo.php</pre>
			<?php
			$parameter['file']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $return_args, $file, $content, $mode ){
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
		<strong>$file</strong> (integer)<br>
		<?php echo __( "The path of the created file.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$content</strong> (array)<br>
		<?php echo __( "The content of the created file.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$mode</strong> (array)<br>
		<?php echo __( "The given mode of the file. Default: w", 'wp-webhooks' ); ?>
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
				'msg' => 'File successfully created.',
			);

			$description = array(
				'tipps' => array(
					__( "For security reasons, we restrict the creation of files to the WordPress root folder and its sub folders. This means, that you have to define the path in a relative way. Here is an example:", 'wp-webhooks' ) . '<code>wp-content/themes/demo-theme/index.php</code>',
					__( "Please note: The folder structure must exist before you can create the file. Otherwise this webhook will return an error.", 'wp-webhooks' ),
				)
			);

			return array(
				'action'			=> 'create_file',
				'name'			  => __( 'Create file', 'wp-webhooks' ),
				'sentence'			  => __( 'create a file', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Create a file via a webhook inside of your WordPress folder structure.', 'wp-webhooks' ),
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

			$file	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'file' );
			$content	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'content' );
			$mode	 = sanitize_title( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'mode' ) );
			$do_action   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( ! empty( $content ) && ! empty( $file ) ){
				$mode = ! empty( $mode ) ? $mode : 'w';
				$check = $file_helpers->create_file( $file, $content, $mode );
				if( $check ){
					$return_args['msg'] = __( "File successfully created.", 'wp-webhooks' );
					$return_args['success'] = true;
				} else {
					$return_args['msg'] = __( "File was not created because of an error or because it already exists", 'wp-webhooks' );
				}
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $file, $content, $mode );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.