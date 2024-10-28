<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_wp_call_php_function' ) ) :

	/**
	 * Load the wp_call_php_function action
	 *
	 * @since 5.2.6
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_wp_call_php_function {

		public function get_details(){

			$parameter = array(
				'function_name' => array( 
					'required' => true, 
					'label' => __( 'The function name', 'wp-webhooks' ), 
					'short_description' => __( '(String) The function name of the PHP function you want to call.', 'wp-webhooks' ),
				),
				'function_arguments' => array(
					'label' => __( 'Callback arguments', 'wp-webhooks' ), 
					'short_description' => __( '(String) The arguments you want to send over within the function call. Use the variable name as the key and the value for the variable value. JSON and serialized data will be converted to its original format. To avoid it, please wrap the value in double quotes.', 'wp-webhooks' ), 
					'type' => 'repeater',
					'choices' => array(),
				),
			);

			ob_start();
?>
<p><?php echo __( 'This arugment allows you to pass custom variables to the function you are going to call. Below you see an example that explains in detail how a JSON looks like that used two vriables', 'wp-webhooks' ) ?></p>
<pre>
{
	'firstvar': 'Some string',
	'secondvar': {
		"your_key_1": "Some string", 
		"your_key_2": "Some string" 
	}
}
</pre>
<p><?php echo __( 'The above JSON will cause your function to receive two variables. Lets assume you have used <code>my_custom_function</code> as the value for the <code>function_name</code> argument. This will cause your function to be fired as followed:', 'wp-webhooks' ) ?></p>
<pre>
function my_custom_function( $firstvar, $secondvar ){

	//Do something

	return 'Demo Response';
} 
</pre>
<?php
			$parameter['function_arguments']['description'] = ob_get_clean();

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		   => array( 'short_description' => __( '(mixed) The term id, as well as the taxonomy term id on success or wp_error on failure.', 'wp-webhooks' ) ),
				'msg'			=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The function was successfully executed.',
				'data' => 
				array (
				  'response' => 'This is a demo response',
				  'error' => '',
				),
			);

			$description = array(
				'tipps' => array(
					__( 'If you add a JSON within the Value field, it will be automatically turned into an array. To avoid that, simply wrap your JSON within "double quotes". ', 'wp-webhooks' ),
					__( 'The response of this action contains the feedback from the custom PHP function you are trying to call.', 'wp-webhooks' ),
				),
			);

			return array(
				'action'			=> 'wp_call_php_function',
				'name'			  => __( 'Call PHP function', 'wp-webhooks' ),
				'sentence'			  => __( 'call a PHP function', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Call a PHP function within WordPress.', 'wp-webhooks' ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'response' => '',
					'error' => '',
				),
			);

			$function_name = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'function_name' );
			$function_arguments = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'function_arguments' );
			
			if( empty( $function_name ) ){
				$return_args['msg'] = __( "Please define the function_name argument.", 'action-wp_call_php_function' );
				return $return_args;
			}

			$validated_args = array();

			if( ! empty( $function_arguments ) && WPWHPRO()->helpers->is_json( $function_arguments ) ){
				$array_arguments = json_decode( $function_arguments, true );
				if( is_array( $array_arguments ) ){
					foreach( $array_arguments as $sak => $sav ){
						$validated_args[ $sak ] = WPWHPRO()->helpers->maybe_format_string( $sav );
					}
				}
			}

			$response = '';
			$error_message = '';

			if( function_exists( $function_name ) ){
				try {
					$response = call_user_func_array( $function_name, $validated_args );
				} catch ( \Exception $e ) {
					$error_message = $e->getMessage();
				}
			} else {
				$error_message = __( "The given function does not exist.", 'action-wp_call_php_function' );
			}
 
			if( $error_message === '' ) {
				$return_args['success'] = true;
				$return_args['data']['response'] = $response;
				$return_args['msg'] = __( "The function was successfully executed.", 'action-wp_call_php_function' );
			} else {
				$return_args['data']['error'] = $error_message;
				$return_args['msg'] = __( "An error occured while executing the function callback", 'action-wp_call_php_function' );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.