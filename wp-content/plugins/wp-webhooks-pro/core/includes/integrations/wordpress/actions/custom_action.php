<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_custom_action' ) ) :

	/**
	 * Load the custom_action action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_custom_action {

		public function get_details(){


			$parameter = array(
				'wpwh_identifier'	   => array(
					'short_description' => __( 'This argument is sent over within the PHP code as seen in the example to diversify between the actions. You can use it to fire your customizations only on specific actions.', 'wp-webhooks' ),
					'description' => __( "Set this argument to identify your webhook call within the add_filter() function. It can be used to diversify between multiple calls that use this custom action. You can set it to e.g. <strong>validate-user</strong> and then check within the add_filter() callback against it to only fire it for this specific webhook call. You can also define this argument within the URL as a parameter, e.g. <code>&wpwh_identifier=my-custom-identifier</code> (the query parameter is only availble within Webhooks). In case you have defined the wpwh_identifier within the payload and the URL, we prioritize the parameter set within the payload.", 'wp-webhooks' ),
				),
			);

			$returns = array(
				'custom'		=> array( 'short_description' => __( 'This webhook returns whatever you define within the filters.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'Custom action was successfully fired.',
			);

			ob_start();
?>
<?php echo __( "The code:", 'wp-webhooks' ); ?>
<pre>add_filter( 'wpwhpro/run/actions/custom_action/return_args', 'wpwh_fire_my_custom_logic', 10, 3 );
function wpwh_fire_my_custom_logic( $return_args, $identifier, $response_body ){

	//If the identifier doesn't match, do nothing
	if( $identifier !== 'ilovewebhooks' ){
		return $return_args;
	}

	//This is how you can validate the incoming value. This field will return the value for the key user_email
	$email = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_email' );

	//Include your own logic here....

	//This is what the webhook returns back to the caller of this action (response)
	//By default, we return an array with success => true and msg -> Some Text
	return $return_args;

}</pre>
<?php echo __( "The custom add_filter() callback accepts three parameters, which are explained down below:", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo __( "This is what the webhook call returns as a response. You can modify it to return your own custom data.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$identifier</strong> (string)<br>
		<?php echo __( "This is the wpwh_identifier you may have set up within the webhook call. (We also allow to set this specific argument within the URL as &wpwh_identifier=my_identifier).", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$response_body</strong> (array)<br>
		<?php echo __( "This returns the validated payload of the incoming webhook call. You can use <code>WPWHPRO()->helpers->validate_request_value()</code> to validate single entries (See example)", 'wp-webhooks' ); ?>
	</li>
</ol>
<?php
			$shortcode_html = ob_get_clean();

			$description = array(
				'steps' => array(
					__( 'Copy the code below and paste it into your functions.php file or custom plugin', 'wp-webhooks' ),
					__( 'Adjust the <code>$identifier</code> variable to the same value you are going to use for the <strong>wpwh_identifier</strong> argument (In this example ilovewebhooks)', 'wp-webhooks' ),
					__( 'Once done, customize the PHP code to whatever you would like to do with it', 'wp-webhooks' ),
					__( 'To customize the response, simply adjust the <code>$return_args</code> variable', 'wp-webhooks' ),
					$shortcode_html,
				),
			);

			return array(
				'action'			=> 'custom_action',
				'name'			  => __( 'Custom PHP action', 'wp-webhooks' ),
				'sentence'			  => __( 'fire custom PHP code', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Fire a custom PHP function within your WordPress website.', 'wp-webhooks' ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> false,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => true,
				'msg' => __( "Custom action was successfully fired.", 'wp-webhooks' )
			);
	
			$identifier = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'wpwh_identifier' );
			if( empty( $identifier ) && isset( $_GET['wpwh_identifier'] ) ){
				$identifier = $_GET['wpwh_identifier'];
			}
	
			$return_args = apply_filters( 'wpwhpro/run/actions/custom_action/return_args', $return_args, $identifier, $response_body );
	
			return $return_args;
	
		}

	}

endif; // End if class_exists check.