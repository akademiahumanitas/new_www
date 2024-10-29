<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_klicktipp_Actions_klickt_add_tag' ) ) :

	/**
	 * Load the klickt_add_tag action
	 *
	 * @since 5.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_klicktipp_Actions_klickt_add_tag {

	public function get_details(){

		$parameter = array(
			'auth_template'		=> array( 
				'required' => false, 
				'type' => 'select', 
				'multiple' => false, 
				'label' => __( 'Authentication template', 'wp-webhooks' ), 
				'query'			=> array(
					'filter'	=> 'authentications',
					'args'		=> array(
						'auth_methods' => array( 'klickt_auth' )
					)
				),
				'short_description' => __( 'Use globally defined KlickTipp credentials to authenticate this action.', 'wp-webhooks' ),
				'description' => __( 'This argument accepts the ID of a KlickTipp authentication template of your choice. You can create new templates within the "Authentication" tab.', 'wp-webhooks' ),
			),
			'username'		=> array( 
				'required' => false, 
				'label' => __( 'Username', 'wp-webhooks' ), 
				'short_description' => __( 'The username of your account to authenticate to KlickTipp. This will be prioritized over the authentication template.', 'wp-webhooks' ),
			),
			'password'		=> array( 
				'required' => false, 
				'label' => __( 'Password', 'wp-webhooks' ), 
				'short_description' => __( 'The password of your account to authenticate to KlickTipp. This will be prioritized over the authentication template.', 'wp-webhooks' ),
			),
			'name'		=> array( 
				'required' => true, 
				'label' => __( 'Tag name', 'wp-webhooks' ), 
				'short_description' => __( 'The name of the tag you want to add within KlickTipp.', 'wp-webhooks' ),
			),
			'text'		=> array(
				'label' => __( 'Tag text', 'wp-webhooks' ), 
				'short_description' => __( 'Additional information about the tag.', 'wp-webhooks' ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			'data'		=> array( 'short_description' => __( '(Array) Further data about the request.', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The tag was successfully added.',
			'data' => 
			array (
			  'tag' => '8715238',
			),
		);

		return array(
			'action'			=> 'klickt_add_tag', //required
			'name'			   => __( 'Add tag', 'wp-webhooks' ),
			'sentence'			   => __( 'add a tag', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Add a tag within "KlickTipp".', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'klicktipp',
			'premium'	   	=> true
		);


		}

		public function execute( $return_data, $response_body ){
			
			$return_args = array(
				'success' => false,
				'msg' => '',
			);

			$auth_template = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'auth_template' );
			$username = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'username' );
			$password = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'password' );
			$name = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'name' );
			$text = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'text' );

			if( ! empty( $auth_template ) ){
				$klickt_auth = WPWHPRO()->integrations->get_auth( 'klicktipp', 'klickt_auth' );
				$credentials = $klickt_auth->get_credentials( $auth_template );

				if( 
					empty( $username ) 
					&& isset( $credentials['wpwhpro_klickt_username'] )
					&& ! empty( $credentials['wpwhpro_klickt_username'] )
				){
					$username = $credentials['wpwhpro_klickt_username'];
				}

				if( 
					empty( $password ) 
					&& isset( $credentials['wpwhpro_klickt_password'] )
					&& ! empty( $credentials['wpwhpro_klickt_password'] )
				){
					$password = $credentials['wpwhpro_klickt_password'];
				}
			}

			if( empty( $username ) || empty( $password ) ){
                $return_args['msg'] = __( "The provided credentials are invalid.", 'wp-webhooks' );
				return $return_args;
            }

			if( empty( $name ) ){
                $return_args['msg'] = __( "Please define the name argument.", 'wp-webhooks' );
				return $return_args;
            }

			if( empty( $text ) ){
				$text = '';
			}

			$klickt_helpers = WPWHPRO()->integrations->get_helper( 'klicktipp', 'klickt_helpers' );

			$connector = $klickt_helpers->get_klicktipp();

			if( ! empty( $connector ) ){

				$connector->login( $username, $password );

				$tag = $connector->tag_create( $name, $text );

				if( ! empty( $tag ) ){
					$return_args['success'] = true;
					$return_args['msg'] = __( "The tag was successfully added.", 'wp-webhooks' );
					$return_args['data']['tag'] = $tag;
				} else {
					$return_args['msg'] = __( "An error occured while adding the tag.", 'wp-webhooks' );
					$return_args['data']['error'] = $connector->get_last_error();
				}
				
				$connector->logout();

			} else {
				$return_args['msg'] = __( "An error occured while loading the KlickTipp helper.", 'wp-webhooks' );
			}
			
			return $return_args;
	
		}

	}

endif; // End if class_exists check.