<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_Actions_fire_trigger' ) ) :

	/**
	 * Load the fire_trigger action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_Actions_fire_trigger {

	public function get_details(){

			$parameter = array(
				'trigger'			=> array( 'required' => true, 'short_description' => __( 'Please set the trigger slug of the trigger you want to fire.', 'wp-webhooks' ) ),
				'trigger_data'		=> array( 'required' => true, 'short_description' => __( 'Set the data you want to fire for the trigger. This field accepts a JSON formatted string.', 'wp-webhooks' ) ),
				'trigger_url_name'	=> array( 'short_description' => __( 'In case you only want to fire a specific trigger URL, please define the trigger webhook name here. You can also add multiple names by comma-separating them.', 'wp-webhooks' ) ),
				'do_action'	  => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired triggers.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<?php echo __( "The <strong>trigger_data</strong> argument accepts a JSON formatted string, containing the data that should be sent within the payload to the selected trigger.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "Down below, you will find an example string for the <strong>send_email</strong> webhook trigger.", 'wp-webhooks' ); ?>
<pre>{
    "to": "newmail@testdomain.test",
    "subject": "This is a demo subject",
    "message": "This field also accepts HTML.",
    "headers": [
        "Content-Type: text\/html; charset=UTF-8",
        "From: Sender Name ",
        "Cc: Receiver Name ",
        "Cc: onlyemail@someemail.demo",
        "Bcc: bccmail@someemail.demo",
        "Reply-To: Reply Name"
    ]
}
</pre>
		<?php
		$parameter['trigger_data']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The <strong>trigger_url_name</strong> argument allows you to fire only one or multiple, specific webhook trigger URLs. If you have, for exmaple, three URLs for a trigger, you can specify which ones you want to send the data to. If you want to fire it for multiple webhook trigger URLs, simply comma-separate the trigger webhook names.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "Here is an example that would fire two webhook trigger URLs for a given trigger:", 'wp-webhooks' ); ?>
<pre>trigger-name-1,trigger-2</pre>
		<?php
		$parameter['trigger_url_name']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>fire_trigger</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $return_args, $trigger, $trigger_data ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo __( "All the values that are sent back as a response to the the initial webhook action caller.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$trigger</strong> (string)<br>
		<?php echo __( "The trigger that was fired by this webhook action.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$trigger_data</strong> (integer)<br>
		<?php echo __( "The data that was sent to the currently given trigger.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'Triggers have been fired.',
			'data' => 
			array (
			  'response_data' => 
			  array (
				'demo_trigger' => 
				array (
				  'headers' => 
				  array (
				  ),
				  'body' => '',
				  'response' => 
				  array (
					'code' => 200,
					'message' => 'OK',
				  ),
				  'cookies' => 
				  array (
					0 => 
					array (
					  'name' => 'some_cookie',
					  'value' => 'Ny7AzUX3re4QqiUUt',
					  'expires' => 1630853569,
					  'path' => '/',
					  'domain' => 'domain.test',
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
				  'body_validated' => 
				  array (
				  ),
				),
			  ),
			),
		  );

		return array(
			'action'			=> 'fire_trigger', //required
			'name'			   => __( 'Fire trigger', 'wp-webhooks' ),
			'sentence'			   => __( 'fire a trigger', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Fire a trigger using webhooks.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'wp-webhooks'
		);


		}

		public function execute( $return_data, $response_body ){

			$response_data = array();
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'response_data' => 0
				)
			);

			$trigger		= sanitize_title( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'trigger' ) );
			$trigger_data	= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'trigger_data' );
			$trigger_url_name	= sanitize_title( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'trigger_url_name' ) );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $trigger ) ){
				$return_args['msg'] = __( "Please set the trigger argument as it is required.", 'action-fire_trigger-error' );
				return $return_args;
			}
			if( empty( $trigger_data ) ){
				$return_args['msg'] = __( "Please set the trigger_data argument as it is required.", 'action-fire_trigger-error' );
				return $return_args;
			}

			$validated_trigger_url_names = array();
			if( ! empty( $trigger_url_name ) ){
				$validated_trigger_url_names = explode( ',', $trigger_url_name );
			}

			$validated_trigger_data = array();
			if( is_string( $trigger_data ) && WPWHPRO()->helpers->is_json( $trigger_data ) ){
				$json_data = json_decode( $trigger_data, true );
				if( ! empty( $json_data ) ){
					$validated_trigger_data = $json_data;
				}
			} elseif( is_object( $trigger_data ) || is_array( $trigger_data ) ) {
				$validated_trigger_data = $trigger_data;
			}

			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', $trigger );
			if( is_array( $webhooks ) && ! empty( $webhooks ) ){
				foreach( $webhooks as $webhook ){

					$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
					if( ! empty( $validated_trigger_url_names ) && ! in_array( $webhook_url_name, $validated_trigger_url_names ) ){
						continue;
					}

					if( $webhook_url_name !== null ){
						$response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $validated_trigger_data, array( 'blocking' => true ) );
					} else {
						$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $validated_trigger_data, array( 'blocking' => true ) );
					}

				}

				$return_args['success'] = true;
				$return_args['msg'] = __( "Triggers have been fired.", 'action-fire_trigger-success' );
				$return_args['data']['response_data'] = $response_data;
			} else {
				$return_args['msg'] = __( "Error: We could not find any trigger for your given data.", 'action-fire_trigger-error' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $trigger, $trigger_data );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.