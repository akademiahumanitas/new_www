<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_ironikus_test' ) ) :

	/**
	 * Load the ironikus_test action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_ironikus_test {

	public function get_details(){

		$parameter = array(
			'test_var'	   => array( 'required' => true, 'short_description' => __( 'A test var. Include the following value to get a success message back: test-value123', 'wp-webhooks' ) )
		);

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'test_var'		=> array( 'short_description' => __( '(string) The variable that was set for the request.', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
		);

			$returns_code = array (
				'success' => true,
				'msg' => 'Test value successfully filled.',
				'test_var' => 'test-value123',
			);

			$description = array(
				'tipps' => array(
					sprintf( __( "This webhook makes sense if you want to test if %s works properly on your WordPress website. You can try to setup different values to see how the webhook interacts with your site.", 'wp-webhooks' ), WPWHPRO()->settings->get_page_title() )
				),
			);

			return array(
				'action'			=> 'ironikus_test',
				'name'			  => __( 'Test action', 'wp-webhooks' ),
				'sentence'			  => __( 'send a demo action', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Test the functionality of this plugin by sending over a demo request.', 'wp-webhooks' ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> false,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'test_var' => ''
			);
	
			$test_var = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'test_var' );
	
			if( $test_var === 'test-value123' ){
				$return_args['success'] = true;
				$return_args['msg'] = __( "Test value successfully filled.", 'wp-webhooks' );
			} else {
				$return_args['msg'] = __( "test_var was not filled properly. Please set it to 'test-value123'", 'wp-webhooks' );
			}
	
			$return_args['test_var'] = $test_var;
	
			return $return_args;
	
		}

	}

endif; // End if class_exists check.