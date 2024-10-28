<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_number_format' ) ) :

	/**
	 * Load the number_format action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_number_format {

	public function get_details(){

		$parameter = array(
			'number'		=> array( 
				'required' => true, 
				'label' => __( 'Number', 'wp-webhooks' ), 
				'short_description' => __( 'The number you want to format. Accepts float and integer.', 'wp-webhooks' ),
			),
			'decimals'		=> array(
				'default_value' => 2,
				'label' => __( 'Decimals', 'wp-webhooks' ),
				'short_description' => __( 'The number of decimals you want to have available. Default is 2.', 'wp-webhooks' ),
			),
			'decimal_separator' => array(
				'default_value' => '.',
				'label' => __( 'Decimal separator', 'wp-webhooks' ),
				'short_description' => __( 'The type of separator used for decimals. Default: .', 'wp-webhooks' ),
			),
			'thousands_separator' => array(
				'default_value' => ',',
				'label' => __( 'Thousands separator', 'wp-webhooks' ),
				'short_description' => __( 'The type of separator used for thousands. Default: ,', 'wp-webhooks' ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The number has been succefully formatted.',
			'data' => '75,238.95',
		);

		return array(
			'action'			=> 'number_format', //required
			'name'			   => __( 'Number format', 'wp-webhooks' ),
			'sentence'			   => __( 'format a number', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Format a number to a specific format.', 'wp-webhooks' ),
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

			$number = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'number' );
			$decimals = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'decimals' );
			$decimal_separator = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'decimal_separator' );
			$thousands_separator = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'thousands_separator' );
			
			if( ! is_numeric( $number ) ){
				$return_args['msg'] = __( "Please set the number argument to a valid number as it is required.", 'action-number_format-error' );
				return $return_args;
			}

			if( empty( $decimals ) ){
				$decimals = 2;
			}
			
			if( empty( $decimal_separator ) ){
				$decimal_separator = '.';
			}
			
			if( empty( $thousands_separator ) ){
				$thousands_separator = ',';
			}

			$validated_number = number_format( $number, $decimals, $decimal_separator, $thousands_separator );

			$return_args['success'] = true;
			$return_args['msg'] = __( "The number has been succefully formatted.", 'action-number_format-success' );
			$return_args['data'] = $validated_number;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.