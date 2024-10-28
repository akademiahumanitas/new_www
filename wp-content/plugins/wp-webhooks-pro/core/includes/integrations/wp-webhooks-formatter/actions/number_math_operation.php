<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_number_math_operation' ) ) :

	/**
	 * Load the number_math_operation action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_number_math_operation {

	public function get_details(){

		$parameter = array(
			'numbers'		=> array( 
				'required' => true, 
				'label' => __( 'Numbers', 'wp-webhooks' ), 
				'short_description' => __( 'A comma-separated string of numbers for your math operation.', 'wp-webhooks' ),
			),
			'operator' => array( 
				'type' => 'select',
				'multiple' => false,
				'choices' => array(
					'add' => array( 'label' => __( 'Add', 'wp-webhooks' ) ),
					'subtract' => array( 'label' => __( 'Subtract', 'wp-webhooks' ) ),
					'multiply' => array( 'label' => __( 'Multiply', 'wp-webhooks' ) ),
					'divide' => array( 'label' => __( 'Divide', 'wp-webhooks' ) ),
				),
				'default_value' => 'add',
				'label' => __( 'Operator', 'wp-webhooks' ), 
				'short_description' => __( 'The math operator you would like to use.', 'wp-webhooks' ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The math operation has been successfully executed.',
			'data' => '18',
		);

		return array(
			'action'			=> 'number_math_operation', //required
			'name'			   => __( 'Number math operation', 'wp-webhooks' ),
			'sentence'			   => __( 'perform a math operation', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Perform a math operation on various numbers.', 'wp-webhooks' ),
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

			$numbers = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'numbers' );
			$operator = sanitize_title( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'operator' ) );

			if( empty( $numbers ) ){
				$return_args['msg'] = __( "Please set the numbers argument as it is required.", 'action-number_math_operation-error' );
				return $return_args;
			}

			if( empty( $operator ) ){
				$operator = 'add';
			}

			$final_number = false;
			$validated_numbers = array();
			$numbers_array = explode( ',', $numbers );
			if( is_array( $numbers_array ) ){
				foreach( $numbers_array as $single_number ){
					$validated_numbers[] = trim( $single_number );
				}
			}

			if( ! empty( $validated_numbers ) ){
				foreach( $validated_numbers as $sn ){

					//Set number to 0 if no number given
					if( ! is_numeric( $sn ) ){
						$sn = 0;
					}

					//set first number
					if( $final_number === false ){
						$final_number = $sn;
						continue; 
					}

					switch( $operator ){
						case 'add':
							$final_number += $sn;
							break;
						case 'subtract':
							$final_number -= $sn;
							break;
						case 'multiply':
							$final_number *= $sn;
							break;
						case 'divide':
							$final_number /= $sn;
							break;
					}

				}
			}
			

			$return_args['success'] = true;
			$return_args['msg'] = __( "The math operation has been successfully executed.", 'action-number_math_operation-success' );
			$return_args['data'] = $final_number;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.