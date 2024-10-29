<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_letter_case' ) ) :

	/**
	 * Load the text_letter_case action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_letter_case {

	public function get_details(){

			$parameter = array(
				'formatting_type' => array( 
					'type' => 'select',
					'multiple' => false,
					'choices' => array(
						'uppercase' => array( 'label' => 'Upper case' ),
						'lowercase' => array( 'label' => 'Lower case' ),
						'capitalfirst' => array( 'label' => 'First letter capital' ),
					),
					'required' => true, 
					'label' => __( 'Formatting type', 'wp-webhooks' ), 
					'short_description' => __( 'The type of adjustment.', 'wp-webhooks' ),
				),
				'value'		=> array( 
					'required' => true, 
					'label' => __( 'Value', 'wp-webhooks' ), 
					'short_description' => __( 'The string that will be adjusted.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

		$returns_code = array (
			'success' => true,
			'msg' => 'The value has been successfully adjusted.',
			'data' => 'some demo value',
		  );

		return array(
			'action'			=> 'text_letter_case', //required
			'name'			   => __( 'Text letter case adjustments', 'wp-webhooks' ),
			'sentence'			   => __( 'do adjustments to the letter case', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Adjust the letter case of a string to capitalize it, set it to lower case, or set the first character to uppercase.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'wp-webhooks-formatter',
			'premium'	   => false
		);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
			);

			$wpwhf_helpers = WPWHPRO()->integrations->get_helper( 'wp-webhooks-formatter', 'wpwhf_helpers' );
			$formatting_type = sanitize_title( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'formatting_type' ) );
			$value	= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'value' );

			if( empty( $formatting_type ) ){
				$return_args['msg'] = __( "Please set the formatting_type argument as it is required.", 'action-text_letter_case-error' );
				return $return_args;
			}

			if( ! is_string( $value ) ){
				$value = '';
			}

			switch( $formatting_type ){
				case 'uppercase':
					$value = mb_strtoupper( $value );
					break;
				case 'lowercase':
					$value = mb_strtolower( $value );
					break;
				case 'capitalfirst':
					$value = $wpwhf_helpers->capitalize_first_character( $value );
					break;
			}

			$return_args['success'] = true;
			$return_args['msg'] = __( "The value has been successfully adjusted.", 'action-text_letter_case-success' );
			$return_args['data'] = $value;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.