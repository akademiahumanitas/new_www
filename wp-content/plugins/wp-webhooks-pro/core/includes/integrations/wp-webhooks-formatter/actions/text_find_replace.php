<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_find_replace' ) ) :

	/**
	 * Load the text_find_replace action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_find_replace {

	public function get_details(){

		$parameter = array(
			'value'		=> array( 
				'required' => true, 
				'label' => __( 'Value', 'wp-webhooks' ), 
				'short_description' => __( 'The string we are going to remove the HTML from.', 'wp-webhooks' ),
			),
			'find'		=> array( 
				'required' => true, 
				'label' => __( 'Find', 'wp-webhooks' ), 
				'short_description' => __( 'The text we are going to look for within the value argument.', 'wp-webhooks' ),
			),
			'replace'		=> array(
				'label' => __( 'Replace', 'wp-webhooks' ), 
				'short_description' => __( 'The text we are using to replace the value of the find argument. Leave empty to remove Find.', 'wp-webhooks' ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The value has been successfully validated.',
			'data' => 'The string with the replaced data.',
		);

		return array(
			'action'			=> 'text_find_replace', //required
			'name'			   => __( 'Text find and replace', 'wp-webhooks' ),
			'sentence'			   => __( 'find and replace text within a text value', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Find and replace text wihtin a given text value.', 'wp-webhooks' ),
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
			$find = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'find' );
			$replace = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'replace' );

			//If nothing is given, set it to nothing
			if( $replace === false ){
				$replace = '';
			}

			$value = str_replace( $find, $replace, $value );

			$return_args['success'] = true;
			$return_args['msg'] = __( "The value has been successfully validated.", 'action-text_find_replace-success' );
			$return_args['data'] = $value;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.