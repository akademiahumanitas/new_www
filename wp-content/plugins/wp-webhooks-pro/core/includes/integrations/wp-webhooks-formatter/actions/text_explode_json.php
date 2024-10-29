<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_explode_json' ) ) :

	/**
	 * Load the text_explode_json action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_explode_json {

		public function get_details(){

				$parameter = array(
				'value'		=> array( 
					'required' => true, 
					'label' => __( 'Value', 'wp-webhooks' ), 
					'short_description' => __( 'The character-separated string you want to turn into a JSON construct.', 'wp-webhooks' ),
				),
				'separator'		=> array(
					'default_value' => ',', 
					'label' => __( 'Separator', 'wp-webhooks' ), 
					'short_description' => __( 'The separator that is used to separate the values used for the JSON construct. By default, we separate the values using a comma.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The string was successfully exploded to a JSON construct.',
				'data' => 
				array (
				  0 => 'value1',
				  1 => 'value2',
				  2 => 'value3',
				),
			);

			return array(
				'action'			=> 'text_explode_json', //required
				'name'			   => __( 'Text explode JSON', 'wp-webhooks' ),
				'sentence'			   => __( 'explode a string to a JSON construct', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Explode a character-separated string to a JSON construct.', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'wp-webhooks-formatter',
				'premium'	   => true
			);


		}

		public function execute( $return_data, $response_body ){
			
			$return_args = array(
				'success' => false,
				'msg' => '',
			);

			$value = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'value' );
			$separator = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'separator' );

			if( empty( $separator ) ){
				$separator = ',';
			}

			$value = explode( $separator, $value );

			$return_args['success'] = true;
			$return_args['msg'] = __( "The string was successfully exploded to a JSON construct.", 'action-text_explode_json-success' );
			$return_args['data'] = $value;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.