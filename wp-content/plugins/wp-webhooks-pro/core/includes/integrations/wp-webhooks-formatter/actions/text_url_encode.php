<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_url_encode' ) ) :

	/**
	 * Load the text_url_encode action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_url_encode {

	public function get_details(){

		$parameter = array(
			'value'		=> array( 
				'required' => true, 
				'label' => __( 'Value', 'wp-webhooks' ), 
				'short_description' => __( 'The string we are going to URL-encode.', 'wp-webhooks' ),
			),
			'convert_spaces' => array( 
				'type' => 'select',
				'multiple' => false,
				'choices' => array(
					'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
				),
				'default_value' => 'no',
				'label' => __( 'Convert space to +', 'wp-webhooks' ), 
				'short_description' => __( 'By default, we convert spaces to %20 instead of +. Set this to "yes" to change that.', 'wp-webhooks' ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The string has been successfully URL-encoded',
			'data' => 'The%20URL-encoded%20string.',
		);

		return array(
			'action'			=> 'text_url_encode', //required
			'name'			   => __( 'Text URL-encode', 'wp-webhooks' ),
			'sentence'			   => __( 'URL-encode a given text', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'URL-encode a given text to make it compatible with URL query parameters.', 'wp-webhooks' ),
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
			$convert_spaces = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'convert_spaces' ) === 'yes' ) ? true : false;
			
			$value = urlencode( $value );

			if( ! $convert_spaces ){
				$value = str_replace( '+', '%20', $value );
			}

			$return_args['success'] = true;
			$return_args['msg'] = __( "The string has been successfully URL-encoded.", 'action-text_url_encode-success' );
			$return_args['data'] = $value;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.