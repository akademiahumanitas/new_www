<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_unserialize' ) ) :

	/**
	 * Load the text_unserialize action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_unserialize {

	public function get_details(){

		$parameter = array(
			'value'		=> array( 
				'required' => true, 
				'label' => __( 'Value', 'wp-webhooks' ), 
				'short_description' => __( 'The serialized string.', 'wp-webhooks' ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The data has been successfuly unserialized.',
			'data' => array(
				'key_1' => 'Value 1',
				'key_2' => 'Value 2',
				'key_3' => 'Value 3',
			),
		);

		return array(
			'action'			=> 'text_unserialize', //required
			'name'			   => __( 'Text unserialize', 'wp-webhooks' ),
			'sentence'			   => __( 'unserialize a serialized data string', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Unserialize a serialized data string into an accessible format.', 'wp-webhooks' ),
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

			if( empty( $value ) ){
				$return_args['msg'] = __( "Please set the value argument as it is required.", 'action-text_unserialize-error' );
				return $return_args;
			}

			$json_data = maybe_unserialize( $value );

			$return_args['success'] = true;
			$return_args['msg'] = __( "The data has been successfuly unserialized.", 'action-text_unserialize-success' );
			$return_args['data'] = $json_data;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.