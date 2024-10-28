<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_wp_fire_do_action_hook' ) ) :

	/**
	 * Load the wp_fire_do_action_hook action
	 *
	 * @since 5.2.6
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_wp_fire_do_action_hook {

		public function get_details(){

				$parameter = array(
				'hook_name' => array( 
					'required' => true, 
					'label' => __( 'The do_action hook name', 'wp-webhooks' ), 
					'short_description' => __( '(String) The do_action hook name of the hook you want to call.', 'wp-webhooks' ),
				),
				'callback_arguments' => array(
					'label' => __( 'Callback arguments', 'wp-webhooks' ), 
					'short_description' => __( '(String) The arguments you want to send over within the callback. Use the variable name as the key and the value for the variable value. JSON and serialized data will be converted to its original format. To avoid it, please wrap the value in double quotes.', 'wp-webhooks' ), 
					'type' => 'repeater',
					'choices' => array(),
				),
				'buffer_response' => array(
					'type' => 'select',
					'choices' => array(
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					),
					'multiple' => false,
					'default_value' => 'no',
					'short_description' => __( '(String) Set this string to "yes" to capture the response of the action. This is specifically interesting if the action you want to call outputs content.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		   => array( 'short_description' => __( '(mixed) The term id, as well as the taxonomy term id on success or wp_error on failure.', 'wp-webhooks' ) ),
				'msg'			=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The do_action was successfully executed.',
				'data' => '',
			);

			$description = array(
				'tipps' => array(
					__( 'The response of this action contains the feedback from the custom PHP function you are trying to call.', 'wp-webhooks' ),
				),
			);

			return array(
				'action'			=> 'wp_fire_do_action_hook',
				'name'			  => __( 'Fire do_action hook', 'wp-webhooks' ),
				'sentence'			  => __( 'fire a do_action hook', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Fire a do_action hook within WordPress.', 'wp-webhooks' ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => '',
			);

			$hook_name = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'hook_name' );
			$callback_arguments = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'callback_arguments' );
			$buffer_response = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'buffer_response' ) === 'yes' ) ? true : false;
			
			if( empty( $hook_name ) ){
				$return_args['msg'] = __( "Please define the hook_name argument.", 'action-wp_fire_do_action_hook' );
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

			$error_message = '';
			$response_buffer = '';

			if( $buffer_response ){
				ob_start();
			}

			try {
				call_user_func_array( 'do_action', $validated_args );
			} catch ( \Exception $e ) {
				$error_message = $e->getMessage();
			}

			if( $buffer_response ){
				$response_buffer = ob_get_clean();
			}
 
			if( $error_message === '' ) {
				$return_args['success'] = true;
				$return_args['msg'] = __( "The do_action was successfully executed.", 'action-wp_fire_do_action_hook' );
				$return_args['data'] = $response_buffer;
			} else {
				$return_args['data'] = $error_message;
				$return_args['msg'] = __( "An error occured while executing the do_action callback", 'action-wp_fire_do_action_hook' );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.