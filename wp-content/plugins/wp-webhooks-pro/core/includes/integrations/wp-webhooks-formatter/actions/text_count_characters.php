<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_count_characters' ) ) :

	/**
	 * Load the text_count_characters action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_count_characters {

	public function get_details(){

		$parameter = array(
			'value'		=> array( 
				'required' => true, 
				'label' => __( 'Value', 'wp-webhooks' ), 
				'short_description' => __( 'The string we are going to count the characters of.', 'wp-webhooks' ),
			),
			'ignore_chars' => array( 
				'type' => 'select',
				'multiple' => false,
				'choices' => array(
					'yes' => array( 'label' => 'Yes' ),
					'no' => array( 'label' => 'No' ),
				),
				'default_value' => 'no',
				'label' => __( 'Ignore low priority characters', 'wp-webhooks' ), 
				'short_description' => __( 'Set this value to yes to ignore white spaces, line breaks, and tabs.', 'wp-webhooks' ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The characters have been successfully counted.',
			'data' => 223,
		);

		return array(
			'action'			=> 'text_count_characters', //required
			'name'			   => __( 'Text count characters', 'wp-webhooks' ),
			'sentence'			   => __( 'count the characters of a given text', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Count the total characters of a given text value.', 'wp-webhooks' ),
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

			$value = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'value' );
			$ignore_chars = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'ignore_chars' ) === 'yes' ) ? true : false;
			
			if( ! is_string( $value ) ){
				$value = '';
			}

			if( $ignore_chars ){
				$chars_to_ignore = array(
					'\n', //linebreak
					'\r', //pilcrow
					'\t', //tab
					PHP_EOL, //eol
					' ', //space
				);

				$value = str_replace( $chars_to_ignore, '', $value );
			}

			$character_count = strlen( $value );

			$return_args['success'] = true;
			$return_args['msg'] = __( "The characters have been successfully counted.", 'action-text_count_characters-success' );
			$return_args['data'] = $character_count;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.