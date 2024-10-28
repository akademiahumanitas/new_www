<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_url_decode' ) ) :

	/**
	 * Load the text_url_decode action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_url_decode {

	public function get_details(){

		$parameter = array(
			'value'		=> array( 
				'required' => true, 
				'label' => __( 'Value', 'wp-webhooks' ), 
				'short_description' => __( 'The string we are going to URL-decode.', 'wp-webhooks' ),
			),
			'convert_plus' => array( 
				'type' => 'select',
				'multiple' => false,
				'choices' => array(
					'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
				),
				'default_value' => 'no',
				'label' => __( 'Convert + to space', 'wp-webhooks' ), 
				'short_description' => __( 'By default, we keep the + character instead of turning it into a space. Set this to "yes" to change it to a space too.', 'wp-webhooks' ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The string has been successfully URL-decoded',
			'data' => 'The URL-decoded string.',
		);

		return array(
			'action'			=> 'text_url_decode', //required
			'name'			   => __( 'Text URL-decode', 'wp-webhooks' ),
			'sentence'			   => __( 'URL-decode a given text', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'URL-decode a given text to make it compatible with URL query parameters.', 'wp-webhooks' ),
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
			$convert_plus = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'convert_plus' ) === 'yes' ) ? true : false;
			
			//temporarily convert pluses
			if( ! $convert_plus ){
				$value = str_replace( '+', '_____plus_____', $value );
			}

			$value = urldecode( $value );

			if( ! $convert_plus ){
				$value = str_replace( '_____plus_____', '+', $value );
			}

			$return_args['success'] = true;
			$return_args['msg'] = __( "The string has been successfully URL-decoded.", 'action-text_url_decode-success' );
			$return_args['data'] = $value;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.