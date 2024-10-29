<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_truncate' ) ) :

	/**
	 * Load the text_truncate action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_truncate {

	public function get_details(){

		$parameter = array(
			'value'		=> array( 
				'required' => true, 
				'label' => __( 'Value', 'wp-webhooks' ), 
				'short_description' => __( 'The string we are going to remove the HTML from.', 'wp-webhooks' ),
			),
			'length'		=> array( 
				'required' => true, 
				'label' => __( 'Length', 'wp-webhooks' ), 
				'short_description' => __( 'The max length of the string allowed. You can also use negative numbers to truncate the string from the end.', 'wp-webhooks' ),
			),
			'offset'		=> array(
				'label' => __( 'Offset', 'wp-webhooks' ), 
				'short_description' => __( 'The offset of the string. You can also use negative numbers to truncate the string from the end.', 'wp-webhooks' ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The string has been successfully truncated.',
			'data' => 'The truncated str',
		);

		return array(
			'action'			=> 'text_truncate', //required
			'name'			   => __( 'Text truncate', 'wp-webhooks' ),
			'sentence'			   => __( 'truncate a given text', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Truncate a given text to a specific length only.', 'wp-webhooks' ),
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
			$offset = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'offset' );
			$length = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'length' );

			if( empty( $length ) ){
				$return_args['msg'] = __( "Please set the length argument as it is required.", 'action-text_extract_number-error' );
				return $return_args;
			}

			if( empty( $offset ) ){
				$offset = 0;
			}

			$value = substr( $value, $offset, $length );

			$return_args['success'] = true;
			$return_args['msg'] = __( "The string has been successfully truncated.", 'action-text_truncate-success' );
			$return_args['data'] = $value;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.