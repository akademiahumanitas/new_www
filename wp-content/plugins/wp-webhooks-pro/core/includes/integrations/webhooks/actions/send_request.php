<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_webhooks_Actions_send_request' ) ) :

	/**
	 * Load the send_request action
	 *
	 * @since 4.3.6
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_webhooks_Actions_send_request {

		public function get_details(){

				//These are the main arguments the user can use to input. You should always grab them within your action function.
			$parameter = array(
				'url'	   => array( 'required' => true, 'multiple' => true, 'short_description' => __( '(string) A URL you want to send the data to. Our actions URLs are supported too.', 'wp-webhooks' ) ),
				'auth_template'		=> array( 
					'required' => false, 
					'type' => 'select', 
					'multiple' => false, 
					'label' => __( 'Authentication template', 'wp-webhooks' ), 
					'query'			=> array(
						'filter'	=> 'authentications',
						'args'		=> array()
					),
					'short_description' => __( 'Use a globally defined authentication template with this action.', 'wp-webhooks' ),
					'description' => __( 'This argument accepts the ID of an authentication template of your choice. You can create new templates within the "Authentication" tab.', 'wp-webhooks' ),
				),
				'method'	=> array( 'required' => true, 'default_value' => 'POST', 'type' => 'select', 'choices' => array(
					'POST' => array( 'label' => 'POST' ),
					'GET' => array( 'label' => 'GET' ),
					'HEAD' => array( 'label' => 'HEAD' ),
					'PUT' => array( 'label' => 'PUT' ),
					'DELETE' => array( 'label' => 'DELETE' ),
					'TRACE' => array( 'label' => 'TRACE' ),
					'OPTIONS' => array( 'label' => 'OPTIONS' ),
					'PATCH' => array( 'label' => 'PATCH' ),
				), 'short_description' => __( '(string) The request type used to send the request.', 'wp-webhooks' ) ),
				'headers'	   => array( 'type' => 'repeater', 'multiple' => true, 'short_description' => __( '(string) A JSON formatted string containing further header details.', 'wp-webhooks' ) ),
				'raw_body'	   => array(
					'label' => __( 'Raw body (Payload data)', 'wp-webhooks' ), 
					'short_description' => __( '(string) The raw body. If this argument is set, the "payload" argument is ignored.', 'wp-webhooks' ),
				),
				'payload'	   => array( 'type' => 'repeater', 'variable' => false, 'multiple' => true, 'short_description' => __( '(string) A JSON formatted string containing further payoad data.', 'wp-webhooks' ) ),
				'timeout'	=> array( 'short_description' => __( '(integer) Filters the timeout value for an HTTP request. Default: 5', 'wp-webhooks' ) ),
				'redirection'	=> array( 'short_description' => __( '(integer) Filters the number of redirects allowed during an HTTP request. Default 5', 'wp-webhooks' ) ),
				'httpversion'	=> array( 'short_description' => __( '(string) Filters the version of the HTTP protocol used in a request. Default: 1.0', 'wp-webhooks' ) ),
				'user-agent'	=> array( 'short_description' => __( '(string) Filters the user agent value sent with an HTTP request.', 'wp-webhooks' ) ),
				'blocking'	=> array( 
					'type' => 'select', 
					'choices' => array( 
						'yes' => __( 'Yes', 'wp-webhooks' ),
						'no' => __( 'No', 'wp-webhooks' ),
					), 
					'short_description' => __( '(bool) Filter whether to wait for a response of the recipient or not. Default: true', 'wp-webhooks' ) 
				),
				'reject_unsafe_urls'	=> array( 
					'type' => 'select', 
					'choices' => array( 
						'yes' => __( 'Yes', 'wp-webhooks' ),
						'no' => __( 'No', 'wp-webhooks' ),
					), 
					'short_description' => __( '(string) Filters whether to pass URLs through wp_http_validate_url() in an HTTP request. Default: no', 'wp-webhooks' )
				),
				'sslverify'	=> array( 
					'type' => 'select', 
					'choices' => array( 
						'yes' => __( 'Yes', 'wp-webhooks' ),
						'no' => __( 'No', 'wp-webhooks' ),
					), 
					'short_description' => __( '(string) Validates the senders SSL certificate before sending the data. Default: no', 'wp-webhooks' )
				),
				'limit_response_size'	=> array( 'short_description' => __( '(integer) Limit the response size of the data coming back from the recpient. Default: null', 'wp-webhooks' ) ),
				'cookies'	   => array( 'short_description' => __( '(string) A JSON formatted string containing additional cookie data.', 'wp-webhooks' ) ),
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
			<?php echo __( "The header argument accepts a JSON formatted string, containing additional header information. Down below you will find an example using two simple header settings:", 'wp-webhooks' ); ?>
			<pre>{
  "Content-Type": "application/json",
  "Custom-Header": "Some demo header"
}</pre>
			<?php
			$parameter['headers']['description'] = ob_get_clean();

			ob_start();
			?>
			<?php echo __( "The payload argument accepts a JSON formatted string, containing your main information. Down below you will find an example for the payload:", 'wp-webhooks' ); ?>
			<pre>{
  "user-email": "jon@doe.test",
  "user-name": "Jon Doe"
}</pre>
			<?php
			$parameter['payload']['description'] = ob_get_clean();

			ob_start();
			?>
			<?php echo __( "The cookies argument accepts a JSON formatted string, containing further cookie information. Down below you will find an example for the payload:", 'wp-webhooks' ); ?>
			<pre>{
  "test-cookie": "The Test Cookie"
}</pre>
			<?php
			$parameter['cookies']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "This argument allows you to change the mehtod of this request. Default is POST.", 'wp-webhooks' ); ?>
		<?php
		$parameter['method']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "This argument allows you to either send the request synchronously (waiting for a response) or asynchronously (response will be empty).", 'wp-webhooks' ); ?>
		<?php
		$parameter['blocking']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "Set this argument to false to use unsafe looking URLs like zfvshjhfbssdf.szfdhdf.com.", 'wp-webhooks' ); ?>
		<?php
		$parameter['reject_unsafe_urls']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "Set this argument to no to use unverified SSL connections for this URL.", 'wp-webhooks' ); ?>
		<?php
		$parameter['sslverify']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>send_request</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $check, $arguments, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$check</strong> (bool)<br>
		<?php echo __( "Returns the HTTP object if the request was successful - WP Error or false if not.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$arguments</strong> (array)<br>
		<?php echo __( "The arguments used to send the HTTP request.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo __( "Contains the response data of the request.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			$returns_code = array (
				'success' => true,
				'data' => 
				array (
				  'headers' => 
				  array (
				  ),
				  'body' => '{"some_key":"The response was successful"}',
				  'response' => 
				  array (
					'code' => 200,
					'message' => 'OK',
				  ),
				  'cookies' => 
				  array (
					0 => 
					array (
					  'name' => 'laravel_session',
					  'value' => '4hfXTJvekTA8kMXsZO6rL9pWF7hqHGxESj8Y3CJI',
					  'expires' => 1633887216,
					  'path' => '/',
					  'domain' => 'webhook.site',
					  'host_only' => true,
					),
				  ),
				  'filename' => NULL,
				  'http_response' => 
				  array (
					'data' => NULL,
					'headers' => NULL,
					'status' => NULL,
				  ),
				),
				'msg' => 'The request was sent successfully.',
			  );

			return array(
				'action'			=> 'send_request',
				'name'			  => __( 'Send webhook request', 'wp-webhooks' ),
				'sentence'			  => __( 'send a webhook request', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'This webhook action allows you to send a webhook request to a URL of your choice from your WordPress site.', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'webhooks',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'data' => array()
			);

			$url	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'url' );
			$headers	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'headers' );
			$raw_body	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'raw_body' );
			$payload	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'payload' );
			$cookies	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'cookies' );
			$method	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'method' );
			$timeout	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'timeout' );
			$redirection	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'redirection' );
			$httpversion	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'httpversion' );
			$blocking	 = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'blocking' ) === 'no' ) ? false : true;
			$user_agent	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user-agent' );
			$sslverify	 = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'sslverify' ) === 'no' ) ? false : true;
			$reject_unsafe_urls	 = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'reject_unsafe_urls' ) === 'yes' ) ? true : false;
			$limit_response_size	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'limit_response_size' );
			$auth_template = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'auth_template' );
			$do_action		  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $url ) ){
				$return_args['msg'] = __( "Please set the url argument.", 'action-send_request-failure' );
				return $return_args;
			}

			$arguments = array(
				'blocking' => $blocking,
				'sslverify' => $sslverify,
				'headers' => array(
					'content-type' => 'application/json'
				),
			);

			if( ! empty( $headers ) && WPWHPRO()->helpers->is_json( $headers ) ){
				$validated_headers = json_decode( $headers, true );
				if( is_array( $validated_headers ) && ! empty( $validated_headers ) ){

					foreach( $validated_headers as $header_key => $header_value ){

						//sanitize based on HTTP/2
						$header_key = strtolower( $header_key );

						$arguments['headers'][ $header_key ] = $header_value;
					}
					
				}
			}

			if( ! empty( $raw_body ) ){
				$arguments['body'] = $raw_body;
			} else {
				if( ! empty( $payload ) ){
					$arguments['body'] = $payload;
				}
			}

			if( ! empty( $cookies ) ){
				$arguments['cookies'] = $cookies;
			}

			if( ! empty( $sslverify ) ){
				$arguments['sslverify'] = $sslverify;
			}

			if( ! empty( $method ) ){
				$arguments['method'] = $method;
			}

			if( ! empty( $timeout ) ){
				$arguments['timeout'] = $timeout;
			}

			if( ! empty( $redirection ) ){
				$arguments['redirection'] = $redirection;
			}

			if( ! empty( $httpversion ) ){
				$arguments['httpversion'] = $httpversion;
			}

			if( ! empty( $user_agent ) ){
				$arguments['user-agent'] = $user_agent;
			}

			if( ! empty( $reject_unsafe_urls ) ){
				$arguments['reject_unsafe_urls'] = $reject_unsafe_urls;
			}

			if( ! empty( $limit_response_size ) ){
				$arguments['limit_response_size'] = $limit_response_size;
			}

			if( ! empty( $auth_template ) ){
				$arguments = WPWHPRO()->auth->apply_auth( $arguments, $auth_template );
			}

			$check = WPWHPRO()->http->send_http_request( $url, $arguments );

			//merge the new form parameters
			$response_body = '';
			if( isset( $check['content'] ) && ! empty( $check['content'] ) ){
				$response_body = $check['content'];
				unset( $check['content'] );
			}
			$check['body'] = $response_body;

			$return_args['data'] = $check;	

			if( $check && ! is_wp_error( $check ) ){
				$return_args['msg'] = __( "The request was sent successfully.", 'action-send_request-success' );
				$return_args['success'] = true;
			} else {
				$return_args['msg'] = __( "An error occured while sending the request.", 'action-send_request-success' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $check, $arguments, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.