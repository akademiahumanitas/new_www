<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_webhooks_Actions_resolve_target_url' ) ) :

	/**
	 * Load the resolve_target_url action
	 *
	 * @since 4.3.6
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_webhooks_Actions_resolve_target_url {

		public function get_details(){

				//These are the main arguments the user can use to input. You should always grab them within your action function.
			$parameter = array(
				'url'	   => array( 'required' => true, 'short_description' => __( '(string) The URL you want to resolve.', 'wp-webhooks' ) ),
				'do_action'	=> array( 'short_description' => __( 'Advanced: Register a custom action after the webhook fires.', 'wp-webhooks' ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further details about the sent data.', 'wp-webhooks' ) ),
			);

			ob_start();
			?>
			<?php echo __( "The URL refers to the URL you would like to resolve. This means that we check the destination of the URL until the URL does not have any redirects anymore.", 'wp-webhooks' ); ?>
			<?php
			$parameter['url']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>resolve_target_url</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 1 );
function my_custom_callback_function( $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo __( "Contains the response data of the request.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			$returns_code = array (
				'success' => true,
				'msg' => 'The URL was successfully resolved.',
				'data' => 
				array (
				  'original_url' => 'https://originaldomain.test',
				  'resolved_url' => 'https://resolveddomain.test/',
				),
			);

			return array(
				'action'			=> 'resolve_target_url',
				'name'			  => __( 'Resolve target URL', 'wp-webhooks' ),
				'sentence'			  => __( 'resolve a target URL', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'This webhook action allows you to resolve a target URL of your choice from your WordPress site.', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'webhooks',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array()
			);
			
			$url	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'url' );
			$do_action   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $url ) ){
				$return_args['msg'] = __( "Please set the url argument.", 'action-resolve_target_url-failure' );
				return $return_args;
			}

			$ch = curl_init( $url );
			curl_setopt( $ch, CURLOPT_NOBODY, 1 );
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
			curl_setopt( $ch, CURLOPT_AUTOREFERER, 1 );
			curl_exec( $ch );
			$target = curl_getinfo( $ch, CURLINFO_EFFECTIVE_URL );
			curl_close( $ch );

			if( ! empty( $target ) ){
				$return_args['success'] = true;
				$return_args['msg'] = __( "The URL was successfully resolved.", 'action-resolve_target_url-succcess' );
				$return_args['data']['original_url'] = $url;
				$return_args['data']['resolved_url'] = $target;
			} else {
				$return_args['msg'] = __( "An error occured while resolving the URL.", 'action-resolve_target_url-succcess' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.