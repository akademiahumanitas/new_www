<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_Actions_wpwh_verify_trigger_signature' ) ) :

	/**
	 * Load the wpwh_verify_trigger_signature action
	 *
	 * @since 5.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_Actions_wpwh_verify_trigger_signature {

	public function get_details(){

			$parameter = array(
				'trigger_name'			=> array( 'required' => true, 'short_description' => __( 'The name of the chosen trigger.', 'wp-webhooks' ) ),
				'trigger_url_name'		=> array( 'required' => true, 'short_description' => __( 'The name ot the chosen trigger URL.', 'wp-webhooks' ) ),
				'trigger_signature'	=> array( 'required' => true, 'short_description' => __( 'The signature of the trigger you would like to verify.', 'wp-webhooks' ) ),
				'do_action'	  => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<?php echo __( "You will find the trigger signature within the headers of the sent request. The header key is called: x-wp-webhook-signature", 'wp-webhooks' ); ?>
		<?php
		$parameter['trigger_signature']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>wpwh_verify_trigger_signature</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 2 );
function my_custom_callback_function( $return_args, $trigger ){
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
		<?php echo __( "The trigger that gets validated.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'The trigger signature is valid.',
		);

		return array(
			'action'			=> 'wpwh_verify_trigger_signature', //required
			'name'			   => __( 'Verify trigger signature', 'wp-webhooks' ),
			'sentence'			   => __( 'verify the signature of a trigger', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Verify the signature of a trigger URL from the "Send Data" tab.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'wp-webhooks'
		);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
			);

			$trigger_name		= sanitize_title( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'trigger_name' ) );
			$trigger_url_name	= sanitize_title( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'trigger_url_name' ) );
			$trigger_signature	= strtr( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'trigger_signature' ), '._-', '+/=' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $trigger_name ) ){
				$return_args['msg'] = __( "Please set the trigger_name argument as it is required.", 'action-wpwh_verify_trigger_signature-error' );
				return $return_args;
			}
			
			if( empty( $trigger_url_name ) ){
				$return_args['msg'] = __( "Please set the trigger_url_name argument as it is required.", 'action-wpwh_verify_trigger_signature-error' );
				return $return_args;
			}

			if( empty( $trigger_signature ) ){
				$return_args['msg'] = __( "Please set the trigger_signature argument as it is required.", 'action-wpwh_verify_trigger_signature-error' );
				return $return_args;
			}

			$trigger = WPWHPRO()->webhook->get_hooks( 'trigger', $trigger_name, $trigger_url_name );

			if( empty( $trigger ) ){
				$return_args['msg'] = __( "We could not find a trigger URL for your given data.", 'action-wpwh_verify_trigger_signature-error' );
				return $return_args;
			}

			if( ! isset( $trigger['secret'] ) || empty( $trigger['secret'] ) ){
				$return_args['msg'] = __( "Your given trigger URL has no secret key. Please regenerate it first.", 'action-wpwh_verify_trigger_signature-error' );
				return $return_args;
			}

			$data = array( 
				'date_created' => $trigger['date_created'],
				'webhook_name' => $trigger['webhook_name'],
				'webhook_url_name' => $trigger['webhook_url_name'],
			);
			$signature = WPWHPRO()->webhook->generate_trigger_signature( json_encode( $data ), $trigger['secret'] );
			
			if( $signature === $trigger_signature ){
				$return_args['success'] = true;
				$return_args['msg'] = __( "The trigger signature is valid.", 'action-wpwh_verify_trigger_signature-success' );
			} else {
				$return_args['msg'] = __( "The trigger signature is not valid.", 'action-wpwh_verify_trigger_signature-error' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $trigger );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.