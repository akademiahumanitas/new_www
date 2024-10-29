<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_copy_folder' ) ) :

	/**
	 * Load the copy_folder action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_copy_folder {

		public function is_active(){

			//Backwards compatibility for the "Comments" integration
			if( class_exists( 'WP_Webhooks_Pro_Remote_File_Control' ) ){
				return false;
			}

			return true;
		}

		public function get_details(){

				$parameter = array(
				'source_path'	   => array( 'required' => true, 'short_description' => __( 'The relative path of the folder you want to copy. For example: wp-content/uploads/demo-folder', 'wp-webhooks' ) ),
				'destination_path'	   => array( 'required' => true, 'short_description' => __( 'The relative path of the destination. For example: wp-content/uploads/new-folder (See the main description for more information)', 'wp-webhooks' ) ),
				'mode'	   => array( 'short_description' => __( 'The mode is 0777 by default, which means the widest possible access.', 'wp-webhooks' ) ),
				'recursive'	   => array( 
					'type' => 'select',
					'choices' => array(
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					),
					'multiple' => false,
					'default_value' => 'no',
					'short_description' => __( 'Allows the creation of nested directories specified in the pathname. Possible values: "yes" and "no". Default: "no". If set to yes, all in your path mentioned folders will be created if they don\'t exist.', 'wp-webhooks' ),
				),
				'do_action'	 => array( 'short_description' => __( 'Advanced: Register a custom action after this webhook was fired.', 'wp-webhooks' ) )
			);

			ob_start();
			?>
<p><?php echo __( 'In case you want to copy a folder into the WordPress root folder, just set the following:', 'wp-webhooks' ); ?></p>
<br>
<pre>/</pre>
<br>
<br>
<p><?php echo __( 'It is also possible to rename the folder while you copy it. Just set a custom folder name for the destination_path:', 'wp-webhooks' ); ?></p>
<br>
<pre>wp-content/uploads/another-new-folder</pre>
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
				'data'		=> array( 'short_description' => __( '(array) The folder data, as well as the single successful actions of moving the file.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The folder was successfully copied.',
				'data' => 
				array (
				  'success' => true,
				  'data' => 
				  array (
					'folder' => 
					array (
					  0 => 
					  array (
						'success' => true,
						'data' => true,
						'source' => '/your/absolute/file/path/wp-content/upgrade',
						'destination' => '/your/absolute/file/path/wp-content/upgrade-2',
					  ),
					  1 => 
					  array (
						'success' => true,
						'childs' => 
						array (
						  'success' => true,
						  'data' => 
						  array (
							'file' => 
							array (
							  0 => 
							  array (
								'success' => true,
								'source' => '/your/absolute/file/path/wp-content/upgrade/subfolder/index.php',
								'destination' => '/your/absolute/file/path/wp-content/upgrade-2/subfolder/index.php',
							  ),
							),
						  ),
						),
						'source' => '/your/absolute/file/path/wp-content/upgrade/subfolder',
						'destination' => '/your/absolute/file/path/wp-content/upgrade-2/subfolder',
					  ),
					),
					'file' => 
					array (
					  0 => 
					  array (
						'success' => true,
						'source' => '/your/absolute/file/path/wp-content/upgrade/index.php',
						'destination' => '/your/absolute/file/path/wp-content/upgrade-2/index.php',
					  ),
					),
				  ),
				),
			);

			$description = array(
				'tipps' => array(
					__( 'This webhook enables you to copy a local folder with all its files and sub folders inside of your WordPress folder structure. (The previous folder will not be removed)', 'wp-webhooks' ),
					__( 'For security reasons, we restrict copying of folders to the WordPress root folder and its sub folders. This means, that you have to define the destination_path in a relative way. Here is an example:', 'wp-webhooks' ) . '<code>wp-content/uploads/new-folder</code>',
				)
			);

			return array(
				'action'			=> 'copy_folder',
				'name'			  => __( 'Copy folder', 'wp-webhooks' ),
				'sentence'			  => __( 'copy a folder', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Copy a local folder via a webhook inside of your WordPress folder structure.', 'wp-webhooks' ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$file_helpers = WPWHPRO()->integrations->get_helper( 'wordpress', 'file_helpers' );
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array()
			);

			$source_path	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'source_path' );
			$destination_path	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'destination_path' );
			$mode	 = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'mode' ) );
			$recursive	 = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'recursive' ) == 'yes' ) ? true : false;
			$do_action   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $mode ) ){
				$mode = 0777;
			}

			if( ! empty( $source_path ) && ! empty( $destination_path ) ){
				$check = $file_helpers->copy_folder( $source_path, $destination_path, $mode, $recursive );
				$return_args['data'] = $check;

				if( is_array( $check ) && $check['success'] == true ){
					$return_args['msg'] = __( "The folder was successfully copied.", 'wp-webhooks' );
					$return_args['success'] = true;
				} else {
					$return_args['msg'] = __( "Folder was not copied because of an error.", 'wp-webhooks' );
				}
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $source_path, $destination_path );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.