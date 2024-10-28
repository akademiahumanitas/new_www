<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_json_to_json' ) ) :

	/**
	 * Load the text_json_to_json action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_json_to_json {

	public function get_details(){

		$parameter = array(
			'json'		=> array( 
				'required' => true, 
				'label' => __( 'JSON', 'wp-webhooks' ), 
				'short_description' => __( 'The JSON formatted string.', 'wp-webhooks' ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The JSON has been successfully constructed.',
			'data' => array(
				'key_1' => 'Value 1',
				'key_2' => 'Value 2',
				'key_3' => 'Value 3',
			),
		);

		return array(
			'action'			=> 'text_json_to_json', //required
			'name'			   => __( 'Text JSON construct', 'wp-webhooks' ),
			'sentence'			   => __( 'convert a JSON string to a JSON construct', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Convert a JSON formatted string to an acessible JSON construct.', 'wp-webhooks' ),
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

			$json = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'json' );

			if( empty( $json ) ){
				$return_args['msg'] = __( "Please set the json argument as it is required.", 'action-text_json_to_json-error' );
				return $return_args;
			}

			$json_validated = '';
			if( WPWHPRO()->helpers->is_json( $json ) ){

				$json_array = json_decode( $json, true );
				if( ! empty( $json_array ) ){
					$json_validated = $json_array;
				}
				
			}

			$return_args['success'] = true;
			$return_args['msg'] = __( "The JSON has been successfully constructed.", 'action-text_json_to_json-success' );
			$return_args['data'] = $json_validated;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.