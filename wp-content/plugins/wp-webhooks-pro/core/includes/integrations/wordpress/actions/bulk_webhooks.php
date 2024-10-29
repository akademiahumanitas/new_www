<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_bulk_webhooks' ) ) :

	/**
	 * Load the bulk_webhooks action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_bulk_webhooks {

		function __construct(){
			$this->page_title   = WPWHPRO()->settings->get_page_title();
		}

		/*
	 * The core logic to use a bulk action webhook
	 */
	public function get_details(){

		$trigger_settings = WPWHPRO()->settings->get_required_trigger_settings();

		$parameter = array(
			'actions'	   => array( 'short_description' => __( 'This argument contains all of your executable webhook calls and settings.', 'wp-webhooks' ) )
		);

		ob_start();
		?>
<?php echo __( "This argument contains a JSON construct that allows you to register multiple webhooks, which will then be executed in the given order. Each of the row acts as a separate webhook call with all of the available settings and configurations.", 'wp-webhooks' ); ?>
<pre>{
  "first_webhook_call": {
	  "http_arguments": {
		  "sslverify": false
	  },
	  "webhook_url": null,
	  "webhook_name": "bulk_actions",
	  "webhook_status": "active",
	  "webhook_settings": {
		  "wpwhpro_trigger_allow_unverified_ssl": 1,
		  "wpwhpro_trigger_allow_unsafe_urls": 1
	  },
	  "payload_data": {
		  "action": "ironikus_test",
		  "test_var": "test-value123"
	  }
  },
  "second_webhook_call": {
	  "payload_data": {
		  "action": "ironikus_test",
		  "test_var": "test-value123"
	  }
  }
}</pre>
<?php echo __( "The JSON can contain multiple webhook calls that are marked via the top level key within the JSON (first_webhook_call, second_webhook_call, ...). This top-level-key indicates the webhook you want to fire and is later used within the response to add the response for that call. Down below you will find an explanation on each of the available settings.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>http_arguments</strong> (array)<br>
		<?php echo __( "This key accepts an array containing multiple arguments from the WP_Http object within WordPress. You can take a look at the argumet list by visiting on the following link:", 'wp-webhooks' ); ?> <a title="wordpress.org" target="_blank" href="https://developer.wordpress.org/reference/classes/WP_Http/request/">https://developer.wordpress.org/reference/classes/WP_Http/request/</a>
	</li>
	<li>
		<strong>webhook_url</strong> (string)<br>
		<?php echo __( "This contains the webhook URL you want to send the request to. By default, it is set to the same webhook URL you are sending this webhook action call to. You can also define external URL's here and send data out of WordPress.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>webhook_name</strong> (string)<br>
		<?php echo __( "The name as an identicator when you sent the webhook. By default, it is set to <strong>bulk_actions</strong>. This value will be sent over to the webhook call within the header as well.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>webhook_status</strong> (string)<br>
		<?php echo __( "Use this argumet to prevent the webhook from being sent in the first place. This allows you to temporarily deactivate the call instead of removing it completely fromthe JSON. To deactivate it, please set it to <strong>inactive</strong>. Default: <strong>active</strong>", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>webhook_settings</strong> (array)<br>
		<?php echo __( "This powerful argument allows you to assign All the settings features, that are available for triggers, for ANY webhook call. That means you can even assign authentication and data mapping templates to triggers just for reformatting the data. Down below, you will find a list with all default trigger settings and its possible values:", 'wp-webhooks' ); ?>
		<ol>
			<?php
				foreach( $trigger_settings as $setting => $setting_data ){
					$value = '';
					$type = 'unknown';

					if( isset( $setting_data['type'] ) ){
						$type = $setting_data['type'];

						if( $setting_data['type'] === 'select' ){

							$choices = $setting_data['choices'];
							
							if( ! is_array( $choices ) ){
								$choices = null;
							}

							if( ! empty( $choices ) ){
								$value .= '<ul class="pl-3">';
								foreach( $choices as $ck => $cv ){
									$value .= '<li><strong>' . sanitize_title( $ck ) . '</strong> (' . sanitize_text_field( $cv ) . ')' . '</li>';
								}
								$value .= '</ul>';
							}
							

						} elseif( $setting_data['type'] === 'checkbox' ){
							$value .= '<ul class="pl-3">';
							$value .= '<li>0</li>';
							$value .= '<li>1</li>';
							$value .= '</ul>';
						}
					}


					echo '<li>';
					echo '<strong>' . sanitize_title( $setting ) . '</strong> (' . __( "Type", 'wp-webhooks' ) . ' ' . $type . '): ';
					echo $value;
					echo '</li>';
				}
			?>
		</ol>
	</li>
	<li>
		<strong>payload_data</strong> (mixed)<br>
		<?php echo __( "This key contains all of the actual data you would like to send to this specific webhook call.", 'wp-webhooks' ); ?>
	</li>
</ol>
<h5><?php echo __( "do_action", 'wp-webhooks' ); ?></h5>
<?php echo __( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the bulk_webhooks action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 2 );
function my_custom_callback_function( $actions, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$actions</strong> (array)<br>
		<?php echo __( "Contains the validated data from the <code>actions</code> argument.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo __( "Contains the full response that is sent back to the webhook caller.s", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['actions']['description'] = ob_get_clean();

		$returns = array(
			'actions'		=> array( 'short_description' => __( 'A list of all executed actions and their responses.', 'wp-webhooks' ) ),
		);

			$returns_code = array(
				'success' => false,
				'msg' => '',
				'actions' => ''
			);

			$description = array(
				'tipps' => array(
					__( 'This webhook enables you to use the full functionality of the woocommerce REST API. It also works with all integrated extensions like <strong>Woocommerce Memberships</strong>, <strong>Woocommerce Subscriptions</strong> or <strong>Woocommerce Bookings</strong>.', 'wp-webhooks' ) . '<br>' . __( 'You can also add a custom action, that fires after the webhook was called. Simply specify your webhook identifier (e.g. my_csutom_webhook) and call it within your theme or plugin (e.g. add_action( "my_csutom_webhook", "my_csutom_webhook_callback" ) ).', 'wp-webhooks' ),
				)
			);

			return array(
				'action'			=> 'bulk_webhooks',
				'name'			  => __( 'Fire multiple webhooks', 'wp-webhooks' ),
				'sentence'			  => __( 'fire multiple webhooks within one request', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Execute multiple webhooks within a single webhook call.', 'wp-webhooks' ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium'		   => true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => __( "No actions have been executed.", 'action-bulk_webhooks-success' ),
				'actions' => array(),
			);

			$actions = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'actions' );
			$do_action = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );
	
			if( empty( $actions ) ){
				$return_args['msg'] = __( "The actions argument cannot be empty.", 'action-bulk_webhooks-success' );
				return $return_args;
			}
	
			//make sure we have accessible data
			if( is_string( $actions ) && WPWHPRO()->helpers->is_json( $actions ) ){
				$actions = json_decode( $actions, true );
			}
	
			if( is_object( $actions ) ){
				$actions = json_decode( json_encode( $actions ), true );
			}
	
			foreach( $actions as $action_key => $action_data ){
	
				if( isset( $action_data['webhook_url'] ) && ! empty( $action_data['webhook_url'] ) ){
					$webhook_url = $action_data['webhook_url'];
				} else {
					$webhook_url = WPWHPRO()->helpers->built_url( WPWHPRO()->helpers->safe_home_url( '/' ), $_GET );
				}
	
				$webhook_name = 'bulk_actions';
				if( isset( $action_data['webhook_name'] ) && ! empty( $action_data['webhook_name'] ) ){
					$webhook_name = $action_data['webhook_name'];
				}
	
				$webhook_settings = array();
				if( isset( $action_data['webhook_settings'] ) && ! empty( $action_data['webhook_settings'] ) ){
					$webhook_settings = $action_data['webhook_settings'];
				}
	
				$webhook_status = array();
				if( isset( $action_data['webhook_status'] ) && ! empty( $action_data['webhook_status'] ) ){
					$webhook_status = $action_data['webhook_status'];
				}
	
				$webhook_data = array(
					'webhook_url' => $webhook_url,
					'webhook_name' => $webhook_name,
					'settings' => $webhook_settings,
					'status' => $webhook_status,
				);
	
				$payload_data = array();
				if( isset( $action_data['payload_data'] ) ){
					$payload_data = $action_data['payload_data'];
				}
	
				$http_arguments = array(
					'blocking' => true, //Make sure we capture the response
				);
				if( isset( $action_data['http_arguments'] ) ){
					$http_arguments = array_merge( $http_arguments, $action_data['http_arguments'] );
				}
	
				$webhook_data = apply_filters( 'wpwhpro/run/actions/bulk_webhooks/webhook_data', $webhook_data, $action_key, $action_data );
				$payload_data = apply_filters( 'wpwhpro/run/actions/bulk_webhooks/payload_data', $payload_data, $action_key, $action_data );
				$http_arguments = apply_filters( 'wpwhpro/run/actions/bulk_webhooks/http_arguments', $http_arguments, $action_key, $action_data );
	
				$return_args['actions'][ $action_key ] = WPWHPRO()->webhook->post_to_webhook( $webhook_data, $payload_data, $http_arguments );
			}
	
			$return_args['success'] = true;
			$return_args['msg'] = __( "All webhook calls have been executed.", 'action-bulk_webhooks-success' );
			$return_args = apply_filters( 'wpwhpro/run/actions/bulk_webhooks/return_args', $return_args, $actions, $response_body );
	
			if( ! empty( $do_action ) ){
				do_action( $do_action, $actions, $return_args );
			}
	
			return $return_args;
	
		}

	}

endif; // End if class_exists check.