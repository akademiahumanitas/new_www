<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_match_expression' ) ) :

	/**
	 * Load the text_match_expression action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_match_expression {

	public function get_details(){

		$parameter = array(
			'value'		=> array( 
				'required' => true, 
				'label' => __( 'Value', 'wp-webhooks' ), 
				'short_description' => __( 'The string we are going to extract the numbers from.', 'wp-webhooks' ),
			),
			'expression'		=> array( 
				'required' => true, 
				'label' => __( 'Regular Expression', 'wp-webhooks' ), 
				'short_description' => __( 'The regular expression (PHP) you want to use to extract the data for.', 'wp-webhooks' ),
				'description' => __( 'This argument accepts regular PHP expressions. To extract an integer number, you can use !\d+!', 'wp-webhooks' ),
			),
			'return_all' => array( 
				'type' => 'select',
				'multiple' => false,
				'choices' => array(
					'yes' => array( 'label' => 'Yes' ),
					'no' => array( 'label' => 'No' ),
				),
				'default_value' => 'no',
				'label' => __( 'Return all matches', 'wp-webhooks' ), 
				'short_description' => __( 'Define whether to extract only the first, or all matches.', 'wp-webhooks' ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The matches have been successfully extracted.',
			'data' => 
			array (
			  0 => '89',
			  1 => '9',
			  2 => '22',
			  3 => '56',
			  4 => '88',
			  5 => '89',
			),
		  );

		return array(
			'action'			=> 'text_match_expression', //required
			'name'			   => __( 'Text match expression', 'wp-webhooks' ),
			'sentence'			   => __( 'match a regular expression on a text value', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Match a regular expression to a text value.', 'wp-webhooks' ),
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
			$expression = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'expression' );
			$return_all = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'return_all' ) === 'yes' ) ? true : false;

			if( empty( $value ) ){
				$return_args['msg'] = __( "Please set the value argument as it is required.", 'action-text_match_expression-error' );
				return $return_args;
			}

			preg_match_all( $expression, $value, $matches );

			$matches_validated = array();

			if( is_array( $matches ) && isset( $matches[0] ) && is_array( $matches[0] ) ){
				if( $return_all ){
					$matches_validated = $matches[0];
				} else {
					if( isset( $matches[0][0] ) ){
						$matches_validated = $matches[0][0];
					}
				}
			}

			$return_args['success'] = true;

			if( $return_all ){
				$return_args['msg'] = __( "The matches have been successfully extracted.", 'action-text_match_expression-success' );
			} else {
				$return_args['msg'] = __( "The match has been successfully extracted.", 'action-text_match_expression-success' );
			}
			
			$return_args['data'] = $matches_validated;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.