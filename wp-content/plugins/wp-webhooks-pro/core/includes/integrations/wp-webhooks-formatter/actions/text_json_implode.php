<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_json_implode' ) ) :

	/**
	 * Load the text_json_implode action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_json_implode {

	public function get_details(){

		$parameter = array(
			'value'		=> array( 
				'required' => true, 
				'label' => __( 'Value', 'wp-webhooks' ), 
				'short_description' => __( 'The JSON string that should be turned into a character-separated string.', 'wp-webhooks' ),
			),
			'separator'		=> array(
				'default_value' => ',', 
				'label' => __( 'Separator', 'wp-webhooks' ), 
				'short_description' => __( 'The separator that is used to separate the values of the first level of the JSON. By default, we separate the values using a comma.', 'wp-webhooks' ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The JSON was successfully imploded.',
			'data' => 'value1,value2,value3',
		);

		return array(
			'action'			=> 'text_json_implode', //required
			'name'			   => __( 'Text JSON implode', 'wp-webhooks' ),
			'sentence'			   => __( 'implode a JSON construct to a string', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Implode the first level of a JSON string construct to a character-separated string using your preferred separator.', 'wp-webhooks' ),
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

			$validated_value = '';
			if( ! is_numeric( $value ) && WPWHPRO()->helpers->is_json( $value ) ){

				$value_array = json_decode( $value, true );
				if( is_array( $value_array ) ){

					//Unset non-scalar values
					foreach( $value_array as $vk => $vv ){
						if( ! is_scalar( $vv ) ){
							unset( $value_array[ $vk ] );
						}
					}

					if( is_array( $value_array ) && ! empty( $value_array ) ){
						$validated_value = implode( $separator, $value_array );
					}
					
				}
				
			} else {
				//Wrap it in an array to always enfore a result
				$validated_value = implode( $separator, array( $value ) );
			}
			

			$return_args['success'] = true;
			$return_args['msg'] = __( "The JSON was successfully imploded.", 'action-text_json_implode-success' );
			$return_args['data'] = $validated_value;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.