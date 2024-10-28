<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_bitly_Actions_bitly_create_short_link' ) ) :

	/**
	 * Load the bitly_create_short_link action
	 *
	 * @since 6.1.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_bitly_Actions_bitly_create_short_link {

	public function get_details(){

		$parameter = array(
			'auth_template'		=> array( 
				'required' => true, 
				'type' => 'select', 
				'multiple' => false, 
				'label' => __( 'Authentication template', 'wp-webhooks' ), 
				'query'			=> array(
					'filter'	=> 'authentications',
					'args'		=> array(
						'auth_methods' => array( 'bitly_auth' )
					)
				),
				'short_description' => __( 'Use globally defined Bitly credentials to authenticate this action using authentication templates.', 'wp-webhooks' ),
				'description' => __( 'This argument accepts the ID of a Bitly authentication template of your choice. You can create new templates within the "Authentication" tab.', 'wp-webhooks' ),
			),
			'long_url'		=> array( 
				'required' => true, 
				'label' => __( 'Long URL', 'wp-webhooks' ), 
				'short_description' => __( 'The URL you would like to shorten.', 'wp-webhooks' ),
			),
			'domain'		=> array( 
				'required' => false, 
				'type' => 'text', 
				'label' => __( 'Domain', 'wp-webhooks' ), 
				'short_description' => __( 'The domain of the shortened URL. Default: bit.ly', 'wp-webhooks' ),
				'placeholder' => 'bit.ly',
				'default_value' => 'bit.ly',
			),
			'group_guid'		=> array( 
				'required' => false, 
				'label' => __( 'Group GUID', 'wp-webhooks' ), 
				'short_description' => __( 'The GUID of a specific group within Bitly.', 'wp-webhooks' ),
				'default_value' => 'bit.ly',
			),
			'title'		=> array( 
				'required' => false, 
				'label' => __( 'Bitlink title', 'wp-webhooks' ), 
				'short_description' => __( 'The title for this Bitlink.', 'wp-webhooks' ),
			),
			'tags'		=> array( 
				'required' => false, 
				'label' => __( 'Bitlink tags', 'wp-webhooks' ), 
				'short_description' => __( 'A comma-separated string (or JSON) of tags for your bitlink.', 'wp-webhooks' ),
			),
			'full_response' => array(
				'label' => __( 'Return full response', 'wp-webhooks' ), 
				'type' => 'select',
				'multiple' => false,
				'choices' => array(
					'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
				),
				'default_value' => 'no',
				'short_description' => __( 'Return the full HTTP response instead of our simplified version. This gives you access to cookies, headers, and much more. Default: "no"', 'wp-webhooks' ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			'data'		=> array( 'short_description' => __( '(Array) Further data about the request.', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The link was successfully shortened.',
			'data' => 
			array (
			  'created_at' => '2023-03-08T10:29:27+0000',
			  'id' => 'bit.ly/xxxxxxx',
			  'link' => 'https://bit.ly/xxxxxxx',
			  'custom_bitlinks' => 
			  array (
			  ),
			  'long_url' => 'https://wp-webhooks.com/',
			  'archived' => false,
			  'tags' => 
			  array (
			  ),
			  'deeplinks' => 
			  array (
			  ),
			  'references' => 
			  array (
				'group' => 'https://api-ssl.bitly.com/v4/groups/xxxxxxxxxxx',
			  ),
			),
		);

		$description = array(
			'tipps' => array(
				__( 'If you would like to learn more about the Bitly API, please follow <a title="Go to bitly.com" target="_blank" href="https://dev.bitly.com/api-reference/#createBitlink">this link</a>.', 'wp-webhooks' ),
			)
		);

		return array(
			'action'			=> 'bitly_create_short_link', //required
			'name'			   => __( 'Create short link', 'wp-webhooks' ),
			'sentence'			   => __( 'create a short link', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Create a short link within "Bitly".', 'wp-webhooks' ),
			'description'	   => $description,
			'integration'	   => 'bitly',
			'premium'	   	=> true
		);


		}

		public function execute( $return_data, $response_body ){
			
			$return_args = array(
				'success' => false,
				'msg' => '',
			);

			$auth_template = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'auth_template' );
			$long_url = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'long_url' );
			$domain = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'domain' );
			$group_guid = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'group_guid' );
			$title = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'title' );
			$tags = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tags' );
			$full_response = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'full_response' ) === 'yes' ) ? true : false;
			$access_token = '';

			if( empty( $long_url ) ){
                $return_args['msg'] = __( "Please define the long_url argument.", 'wp-webhooks' );
				return $return_args;
            }

			if( ! empty( $auth_template ) ){
				$bitly_auth = WPWHPRO()->integrations->get_auth( 'bitly', 'bitly_auth' );
				$credentials = $bitly_auth->get_credentials( $auth_template );

				if( 
					empty( $username ) 
					&& isset( $credentials['wpwhpro_bitly_access_token'] )
					&& ! empty( $credentials['wpwhpro_bitly_access_token'] )
				){
					$access_token = $credentials['wpwhpro_bitly_access_token'];
				}
			}

			if( empty( $access_token ) ){
                $return_args['msg'] = __( "The provided access token is invalid.", 'wp-webhooks' );
				return $return_args;
            }

			if( empty( $domain ) ){
				$domain = 'bit.ly';
			}

			$body = array(
				'long_url' => $long_url,
				'domain' => $domain,
			);

			if( ! empty( $group_guid ) ){
				$body['group_guid'] = $group_guid;
			}

			if( ! empty( $title ) ){
				$body['title'] = $title;
			}

			if( ! empty( $tags ) ){
				$validated_tags = array();

				if( WPWHPRO()->helpers->is_json( $tags ) ){
					$tags = json_decode( $tags, true );
					if( is_array( $tags ) ){
						$validated_tags = $tags;
					}
				} else {
					$tags = explode( ',', $tags );
					if( is_array( $tags ) ){
						foreach( $tags as $tag ){
							$validated_tags[] = trim( $tag );
						}
					}
				}

				$body['tags'] = $validated_tags;
			}

			$http_args = array(
				'method' => 'POST',
				'blocking' => true,
				'httpversion' => '1.1',
				'headers' => array(
					'Authorization' => 'Bearer ' . $access_token,
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/json',
				),
				'body'    => $body,
				'timeout' => 20,
			);
	
			$api_url = 'https://api-ssl.bitly.com/v4/bitlinks';

			$response = WPWHPRO()->http->send_http_request( $api_url, $http_args );

			if( $full_response ){
				//Map the response
				$return_args = array_merge( $return_args, $response );
			} else {
				if( $response['success'] ){
					$return_args['success'] = true;
					$return_args['msg'] = __( "The link was successfully shortened.", 'wp-webhooks' );
					$return_args['data'] = $response['content'];
				} else {
					$return_args['msg'] = __( "An error occured while shorteing the link.", 'wp-webhooks' );
					$return_args['data'] = $response['content'];
				}
			}
			
			return $return_args;
	
		}

	}

endif; // End if class_exists check.