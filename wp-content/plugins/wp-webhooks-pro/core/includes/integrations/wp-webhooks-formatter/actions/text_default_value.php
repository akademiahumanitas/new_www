<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_default_value' ) ) :

	/**
	 * Load the text_default_value action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_default_value {

	public function get_details(){

		$parameter = array(
			'value'		=> array( 
				'required' => true, 
				'label' => __( 'Current value', 'wp-webhooks' ), 
				'short_description' => __( 'The value to check against if it is empty.', 'wp-webhooks' ),
			),
			'default_value'		=> array( 
				'required' => true, 
				'label' => __( 'Default value', 'wp-webhooks' ), 
				'short_description' => __( 'The default value in case the Current value was empty. A value is considered empy if nothing was sent through, or one of the following values was given: false, 0, no.', 'wp-webhooks' ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The default value has been applied successfully.',
			'data' => 'Some default value',
		  );

		return array(
			'action'			=> 'text_default_value', //required
			'name'			   => __( 'Text default value', 'wp-webhooks' ),
			'sentence'			   => __( 'set a default value if value is empty', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Set a default value in case the given value is empty or has one of the following data: false, no, 0', 'wp-webhooks' ),
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

			$value	= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'value' );
			$default_value	= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'default_value' );

			if( 
				empty( $value ) 
				|| $value === 'false'
				|| $value === 'no'
				|| $value === '0'
			){
				$value = $default_value;
			}

			$return_args['success'] = true;

			if( $default_value === $value ){
				$return_args['msg'] = __( "The default value has been applied successfully.", 'action-text_default_value-success' );
			} else {
				$return_args['msg'] = __( "The value field was set correctly. No default value has been applied.", 'action-text_default_value-success' );
			}
			
			$return_args['data'] = $value;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.