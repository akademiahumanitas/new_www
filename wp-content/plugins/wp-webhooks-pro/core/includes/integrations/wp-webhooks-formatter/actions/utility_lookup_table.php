<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_utility_lookup_table' ) ) :

	/**
	 * Load the utility_lookup_table action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_utility_lookup_table {

	public function get_details(){

		$parameter = array(
			'lookup_key'		=> array(
				'label' => __( 'Lookup key', 'wp-webhooks' ), 
				'short_description' => __( 'The key you want to match to any of the keys (values on the left) of the lookup table.', 'wp-webhooks' ),
			),
			'lookup_table'		=> array(
				'type' => 'repeater', 
				'variable' => true, 
				'label' => __( 'Lookup table', 'wp-webhooks' ), 
				'short_description' => __( 'The lookup table data. As a key, please add the key you want to lookup and on the right the value you want to match.', 'wp-webhooks' ),
			),
			'lookup_fallback'		=> array(
				'label' => __( 'Lookup fallback', 'wp-webhooks' ), 
				'short_description' => __( 'A fallback value in case we could not match the lookup key to the lookup table data.', 'wp-webhooks' ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			'data'		=> array( 'short_description' => __( '(array) Further information about the response.', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The lookup key was matched successfully.',
			'data' => 
			array (
			  'lookup_value' => 'matchedvalue',
			  'is_fallback' => false,
			),
		  );

		return array(
			'action'			=> 'utility_lookup_table', //required
			'name'			   => __( 'Lookup table', 'wp-webhooks' ),
			'sentence'			   => __( 'create a lookup table', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Create a lookup table to match a specific value to another one.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'wp-webhooks-formatter',
			'premium'	   => true
		);


		}

		public function execute( $return_data, $response_body ){
			
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(),
			);

			$lookup_key = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'lookup_key' );
			$lookup_table = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'lookup_table' );
			$lookup_fallback = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'lookup_fallback' );

			if( empty( $lookup_key ) ){
				$return_args['msg'] = __( "Please set the lookup_key argument.", 'action-utility_lookup_table-success' );
				return $return_args;
			}

			if( empty( $lookup_table ) ){
				$return_args['msg'] = __( "Please set the lookup_table argument.", 'action-utility_lookup_table-success' );
				return $return_args;
			}

			$is_fallback = false;
			$validated_value = '';
			if( WPWHPRO()->helpers->is_json( $lookup_table ) ){

				$lookup_array = json_decode( $lookup_table, true );
				if( is_array( $lookup_array ) ){

					$lookup_value = null;

					if( isset( $lookup_array[ $lookup_key ] ) ){
						$lookup_value = $lookup_array[ $lookup_key ];

						if( $lookup_value === '' ){
							$lookup_value = $lookup_fallback;
							$is_fallback = true;
						}
					} else {
						$lookup_value = $lookup_fallback;
						$is_fallback = true;
					}
					
				}
				
			}
			

			$return_args['success'] = true;

			if( ! $is_fallback ){
				$return_args['msg'] = __( "The lookup key was matched successfully.", 'action-utility_lookup_table-success' );
			} else {
				$return_args['msg'] = __( "The lookup key was matched with the fallback.", 'action-utility_lookup_table-success' );
			}

			
			$return_args['data']['lookup_value'] = $lookup_value;
			$return_args['data']['is_fallback'] = $is_fallback;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.