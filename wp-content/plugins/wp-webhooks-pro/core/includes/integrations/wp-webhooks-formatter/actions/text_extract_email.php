<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_extract_email' ) ) :

	/**
	 * Load the text_extract_email action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_extract_email {

	public function get_details(){

			$parameter = array(
				'value'		=> array( 
					'required' => true, 
					'label' => __( 'Value', 'wp-webhooks' ), 
					'short_description' => __( 'The string we are going to extract the emails from.', 'wp-webhooks' ),
				),
				'return_all' => array( 
					'type' => 'select',
					'multiple' => false,
					'choices' => array(
						'yes' => array( 'label' => 'Yes' ),
						'no' => array( 'label' => 'No' ),
					),
					'default_value' => 'no',
					'label' => __( 'Return all emails', 'wp-webhooks' ), 
					'short_description' => __( 'Define whether to extract only the first, or all email addresses.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

		$returns_code = array (
			'success' => true,
			'msg' => 'The emails have been successfully extracted.',
			'data' => 
			array (
			  0 => 'demoemail@test.test',
			  1 => 'jondoe@demo.test',
			),
		  );

		return array(
			'action'			=> 'text_extract_email', //required
			'name'			   => __( 'Text extract email', 'wp-webhooks' ),
			'sentence'			   => __( 'extract one or multiple emails from text', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Extract one or multiple emails from a text value.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'wp-webhooks-formatter',
			'premium'	   => true
		);


		}

		public function execute( $return_data, $response_body ){

			$email_regex = '/([_A-Za-z0-9-]+(\\.[_A-Za-z0-9-]+)*@[A-Za-z0-9-]+(\\.[A-Za-z0-9-]+)*(\\.[A-Za-z]{2,}))/i';
			$return_args = array(
				'success' => false,
				'msg' => '',
			);

			$value = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'value' );
			$return_all = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'return_all' ) === 'yes' ) ? true : false;

			if( empty( $value ) ){
				$return_args['msg'] = __( "Please set the value argument as it is required.", 'action-text_extract_email-error' );
				return $return_args;
			}

			preg_match_all( $email_regex, $value, $matches );

			$emails = array();
			if( is_array( $matches ) && isset( $matches[0] ) && is_array( $matches[0] ) ){
				if( $return_all ){
					$emails = $matches[0];
				} else {
					if( isset( $matches[0][0] ) ){
						$emails = $matches[0][0];
					}
				}
			}

			$return_args['success'] = true;

			if( $return_all ){
				$return_args['msg'] = __( "The emails have been successfully extracted.", 'action-text_extract_email-success' );
			} else {
				$return_args['msg'] = __( "The email has been successfully extracted.", 'action-text_extract_email-success' );
			}
			
			$return_args['data'] = $emails;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.