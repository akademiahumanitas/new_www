<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_extract_url' ) ) :

	/**
	 * Load the text_extract_url action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_extract_url {

	public function get_details(){

			$parameter = array(
				'value'		=> array( 
					'required' => true, 
					'label' => __( 'Value', 'wp-webhooks' ), 
					'short_description' => __( 'The string we are going to extract the URLs from.', 'wp-webhooks' ),
				),
				'return_all' => array( 
					'type' => 'select',
					'multiple' => false,
					'choices' => array(
						'yes' => array( 'label' => 'Yes' ),
						'no' => array( 'label' => 'No' ),
					),
					'default_value' => 'no',
					'label' => __( 'Return all URLs', 'wp-webhooks' ), 
					'short_description' => __( 'Define whether to extract only the first, or all URLs.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

		$returns_code = array (
			'success' => true,
			'msg' => 'The URLs have been successfully extracted.',
			'data' => 
			array (
			  0 => 
			  array (
				'raw_url' => 'https://demo.domain.com/some/path?demo=123&more=argument#navigate',
				'parts' => 
				array (
				  'scheme' => 'https',
				  'host' => 'demo.domain.com',
				  'path' => '/some/path',
				  'query' => 'demo=123&more=argument',
				  'fragment' => 'navigate',
				),
			  ),
			  1 => 
			  array (
				'raw_url' => 'https://www.domain.com/',
				'parts' => 
				array (
				  'scheme' => 'https',
				  'host' => 'www.domain.com',
				  'path' => '/',
				),
			  ),
			),
		);

		return array(
			'action'			=> 'text_extract_url', //required
			'name'			   => __( 'Text extract URL', 'wp-webhooks' ),
			'sentence'			   => __( 'extract one or multiple URLs from text', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Extract one or multiple URLs from a text value.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'wp-webhooks-formatter',
			'premium'	   => true
		);


		}

		public function execute( $return_data, $response_body ){

			$regex = "((https?|ftp)\:\/\/)?"; //validate the protocol
			$regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; //validate subdomains
			$regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; //validate the domain
			$regex .= "(\:[0-9]{2,5})?"; //validate the custom port
			$regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; //validate the custom path
			$regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; //validate query parameters
			$regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; //validate onpage tags
			
			$return_args = array(
				'success' => false,
				'msg' => '',
			);

			$value = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'value' );
			$return_all = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'return_all' ) === 'yes' ) ? true : false;
			
			if( empty( $value ) ){
				$return_args['msg'] = __( "Please set the value argument as it is required.", 'action-text_extract_url-error' );
				return $return_args;
			}

			preg_match_all( '/' . $regex . '/i', $value, $matches );

			$urls = array();
			if( is_array( $matches ) && isset( $matches[0] ) && is_array( $matches[0] ) ){
				if( $return_all ){
					$urls = $matches[0];
				} else {
					if( isset( $matches[0][0] ) ){
						$urls = $matches[0][0];
					}
				}
			}

			//append the parts
			if( is_array( $urls ) ){
				foreach( $urls as $urlkey => $url ){
					$urls[ $urlkey ] = array(
						'raw_url' => $url,
						'parts' => parse_url( $url ),
					);
				}
			} else {
				$urls = array(
					'raw_url' => $urls,
					'parts' => parse_url( $urls ),
				);
			}

			$return_args['success'] = true;

			if( $return_all ){
				$return_args['msg'] = __( "The URLs have been successfully extracted.", 'action-text_extract_url-success' );
			} else {
				$return_args['msg'] = __( "The URL has been successfully extracted.", 'action-text_extract_url-success' );
			}
			
			$return_args['data'] = $urls;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.