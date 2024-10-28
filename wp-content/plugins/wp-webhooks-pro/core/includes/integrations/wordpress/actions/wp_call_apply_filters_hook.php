<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_wp_call_apply_filters_hook' ) ) :

	/**
	 * Load the wp_call_apply_filters_hook action
	 *
	 * @since 5.2.6
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_wp_call_apply_filters_hook {

		public function get_details(){

				$parameter = array(
				'hook_name' => array( 
					'required' => true, 
					'label' => __( 'The apply_filters hook name', 'wp-webhooks' ), 
					'short_description' => __( '(String) The apply_filters hook name of the hook you want to call.', 'wp-webhooks' ),
				),
				'callback_arguments' => array(
					'label' => __( 'Callback arguments', 'wp-webhooks' ), 
					'short_description' => __( '(String) The arguments you want to send over within the callback. Use the variable name as the key and the value for the variable value. JSON and serialized data will be converted to its original format. To avoid it, please wrap the value in double quotes.', 'wp-webhooks' ), 
					'type' => 'repeater',
					'choices' => array(),
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		   => array( 'short_description' => __( '(mixed) The term id, as well as the taxonomy term id on success or wp_error on failure.', 'wp-webhooks' ) ),
				'msg'			=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The apply_filters was successfully called.',
				'data' => 
				array (
				  'response' => 'This is a demo response',
				  'error' => '',
				),
			);

			$description = array(
				'tipps' => array(
					__( 'The response of this action contains the feedback from all the add_filter() callbacks that have been fired within this instance.', 'wp-webhooks' ),
				),
			);

			return array(
				'action'			=> 'wp_call_apply_filters_hook',
				'name'			  => __( 'Call apply_filters hook', 'wp-webhooks' ),
				'sentence'			  => __( 'call a apply_filters hook', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Call a apply_filters hook within WordPress.', 'wp-webhooks' ),
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

			$hook_name = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'hook_name' );
			$callback_arguments = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'callback_arguments' );
			
			if( empty( $hook_name ) ){
				$return_args['msg'] = __( "Please define the hook_name argument.", 'action-wp_call_apply_filters_hook' );
				return $return_args;
			}

			$validated_args = array(
				'hook_name' => $hook_name
			);

			if( ! empty( $callback_arguments ) && WPWHPRO()->helpers->is_json( $callback_arguments ) ){
				$array_arguments = json_decode( $callback_arguments, true );
				if( is_array( $array_arguments ) ){
					foreach( $array_arguments as $sak => $sav ){
						$validated_args[ $sak ] = WPWHPRO()->helpers->maybe_format_string( $sav );
					}
				}
			}

			$response = '';
			$error_message = '';

			try {
				$response = call_user_func_array( 'apply_filters', $validated_args );
			} catch ( \Exception $e ) {
				$error_message = $e->getMessage();
			}
 
			if( $error_message === '' ) {
				$return_args['success'] = true;
				$return_args['data']['response'] = $response;
				$return_args['msg'] = __( "The apply_filters was successfully called.", 'action-wp_call_apply_filters_hook' );
			} else {
				$return_args['data']['error'] = $error_message;
				$return_args['msg'] = __( "An error occured while calling the apply_filters callback", 'action-wp_call_apply_filters_hook' );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.