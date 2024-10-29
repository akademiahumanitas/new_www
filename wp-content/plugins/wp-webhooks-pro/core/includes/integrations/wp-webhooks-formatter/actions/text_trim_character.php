<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_trim_character' ) ) :

	/**
	 * Load the text_trim_character action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_trim_character {

	public function get_details(){

		$parameter = array(
			'value'		=> array( 
				'required' => true, 
				'label' => __( 'Value', 'wp-webhooks' ), 
				'short_description' => __( 'The string that is going to be trimmed.', 'wp-webhooks' ),
			),
			'characters'		=> array(
				'default_value' => ' ', 
				'label' => __( 'Characters', 'wp-webhooks' ), 
				'short_description' => __( 'A sequence of characters that should be trimmed from the string. By default, we trim whitespaces. If you se this argument to xx, we would trim xx from the beginning and the end.', 'wp-webhooks' ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The text was successfully trimmed.',
			'data' => 'The trimmed text.',
		);

		return array(
			'action'			=> 'text_trim_character', //required
			'name'			   => __( 'Text trim character', 'wp-webhooks' ),
			'sentence'			   => __( 'trim one or multiple characters from a string', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Trim one or multiple characters from the beginning and the end of a string.', 'wp-webhooks' ),
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
			$characters = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'characters' );

			if( empty( $characters ) ){
				$characters = ' ';
			}

			$value = trim( $value, $characters );

			$return_args['success'] = true;
			$return_args['msg'] = __( "The text was successfully trimmed.", 'action-text_trim_character-success' );
			$return_args['data'] = $value;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.