<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_plugin_activate' ) ) :

	/**
	 * Load the plugin_activate action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_plugin_activate {

		public function is_active(){

			//Backwards compatibility for the "Manage Plugins" integration
			if( defined( 'WPWHPRO_MNGPL_PLUGIN_NAME' ) ){
				return false;
			}

			return true;
		}

		public function get_details(){

				//These are the main arguments the user can use to input. You should always grab them within your action function.
			$parameter = array(
				'plugin_slug'	   => array( 'required' => true, 'short_description' => __( '(string) The plugin slug of the plugin you want to activate. Please check the description for further details.', 'wp-webhooks' ) ),
				'do_action'	 => array( 'short_description' => __( 'Advanced: Register a custom action after WP Webhooks Pro fires this webhook. Please check the description for further details.', 'wp-webhooks' ) )
			);

			ob_start();
?>
<?php echo __( "This argument contains the slug of a plugin. This isually is the folder name of the plugin, followed by a slash and the plugin file name + file extension. Down below is an example.", 'wp-webhooks' ); ?>
<pre>wpwh-comments/wpwh-plugin-file.php</pre>
<?php echo __( "The above slug is defined based on the plugin setup. <strong>wpwh-comments</strong> is the name of the plugin folder. <strong>wpwh-plugin-file.php</strong> is the file name of the plugin file within the folder (The file where you defined your plugin details within the comment).", 'wp-webhooks' ); ?>
<?php
			$parameter['plugin_slug']['description'] = ob_get_clean();

			ob_start();
?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>plugin_activate</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $plugin_slug, $check, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$plugin_slug</strong> (string)<br>
		<?php echo __( "The currently given slug (+ filename) of the plugin. (The given data from the plugin_slug argument)", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$check</strong> (bool)<br>
		<?php echo __( "True if the plugin was successfully activated, false if not.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo __( "All the values that are sent back as a response the the initial webhook action caller.", 'wp-webhooks' ); ?>
	</li>
</ol>
<?php
			$parameter['do_action']['description'] = ob_get_clean();

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'Plugin successfully activated.',
			);

			return array(
				'action'			=> 'plugin_activate',
				'name'			  => __( 'Activate plugin', 'wp-webhooks' ),
				'sentence'			  => __( 'activate a plugin', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'This webhook action allows you to activate a plugin within your WordPress website.', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'wordpress',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$plugin_helpers = WPWHPRO()->integrations->get_helper( 'wordpress', 'plugin_helpers' );
			$return_args = array(
				'success' => false
			);

			$plugin_slug	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'plugin_slug' );
			$do_action	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );
			
			if( empty( $plugin_slug ) ){
				$return_args['msg'] = __( "Please set the plugin_slug to continue.", 'action-plugin_activate-failure' );
				return $return_args;
			}

			$check = $plugin_helpers->activate( $plugin_slug );
			if( $check ){
				$return_args['msg'] = __( "Plugin successfully activated.", 'action-plugin_activate-success' );
				$return_args['success'] = true;
			} else {
				$return_args['msg'] = __( "The plugin was not activated.", 'action-plugin_activate-success' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $plugin_slug, $check, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.