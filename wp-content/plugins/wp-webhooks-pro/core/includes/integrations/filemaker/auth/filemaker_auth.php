<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_filemaker_Auth_filemaker_auth' ) ) :

	/**
	 * Load the filemaker authentcation template
	 *
	 * @since 6.1.4
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_filemaker_Auth_filemaker_auth {

		private $session_tokens = array();

        public function get_details(){
			
			return array(
				'name' => __( 'FileMaker credentials', 'wp-webhooks' ),
				'short_description' => __( 'Add your FileMaker Account credentials below.', 'wp-webhooks' ),
				'description' => 'For Claris hosts, use the extended Claris account name and password you set in Claris Studio. See “Create an extended Claris account for Claris Server services” in Claris Studio Help Center. To learn more about the authentication, please visit this URL: <a title="Go to Claris Documentation" target="_blank" href="https://help.claris.com/en/data-api-guide/content/log-in-database-session.html">https://help.claris.com/en/data-api-guide/content/log-in-database-session.html</a>',
				'fields' => array(

					'wpwhpro_filemaker_server_url' => array(
						'id'          => 'wpwhpro_filemaker_server_url',
						'type'        => 'text',
						'label'       => __( 'Server URL', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'required' => true,
						'short_description' => __( 'The URL of the FileMaker server. Please include the URL starting with https://.', 'wp-webhooks' ),
						'description' => '',
					),

					'wpwhpro_filemaker_version' => array(
						'id'          => 'wpwhpro_filemaker_version',
						'type'        => 'text',
						'label'       => __( 'FileMaker Data API version', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => 'vLatest',
						'required' => true,
						'short_description' => __( 'The version can either be v1, v2, or vLatest (Default).', 'wp-webhooks' ),
						'description' => '',
					),

					'wpwhpro_filemaker_db' => array(
						'id'          => 'wpwhpro_filemaker_db',
						'type'        => 'text',
						'label'       => __( 'FileMaker Database', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'required' => true,
						'short_description' => __( 'The database associated with this authentication.', 'wp-webhooks' ),
						'description' => '',
					),

					'wpwhpro_filemaker_account_name' => array(
						'id'          => 'wpwhpro_filemaker_account_name',
						'type'        => 'text',
						'label'       => __( 'Account name', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'required' => true,
						'short_description' => __( 'The FileMaker account name.', 'wp-webhooks' ),
						'description' => '',
					),

					'wpwhpro_filemaker_account_password' => array(
						'id'          => 'wpwhpro_filemaker_account_password',
						'type'        => 'text',
						'label'       => __( 'Account password', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'required' => true,
						'short_description' => __( 'The FileMaker password.', 'wp-webhooks' ),
						'description' => '',
					),

				)
			);
		}

		public function auth_login( $credentials_data ){
			$return = null;

			$server_url = ( isset( $credentials_data->template['wpwhpro_filemaker_server_url'] ) ) ? $credentials_data->template['wpwhpro_filemaker_server_url'] : '';
			$fm_db = ( isset( $credentials_data->template['wpwhpro_filemaker_db'] ) ) ? $credentials_data->template['wpwhpro_filemaker_db'] : '';
			$account_name = ( isset( $credentials_data->template['wpwhpro_filemaker_account_name'] ) ) ? $credentials_data->template['wpwhpro_filemaker_account_name'] : '';
			$account_pw = ( isset( $credentials_data->template['wpwhpro_filemaker_account_password'] ) ) ? $credentials_data->template['wpwhpro_filemaker_account_password'] : '';

			//Bail if the template was not properly filled
			if( 
				empty( $server_url ) 
				|| empty( $fm_db ) 
				|| empty( $account_name ) 
				|| empty( $account_pw )
			){
				return $credentials_data;
			}

			$basic_string = base64_encode( $account_name . ':' . $account_pw );

			$url = $this->get_base_url( $credentials_data ) . '/sessions'; 

			$http_args = array(
				'timeout' => ( ! empty( $timeout ) ) ? $timeout : 30,
				'headers' => array(
					'authorization' => 'Basic ' . $basic_string,
					'content-type' => 'application/json',
					'content-length' => 0,
				),
				'body' => ''
			);

			$response = WPWHPRO()->http->send_http_request( $url, $http_args );
	
			if( isset( $response['code'] ) && $response['code'] === 200 ){

				if( 
					isset( $response['content']['response'] )
					&& isset( $response['content']['response']['token'] )
					&& ! empty( $response['content']['response']['token'] )
				){
					$return = sanitize_title( $response['content']['response']['token'] );
				}
				
			} else {
				$error_message = __( 'The FileMaker access token authentication failed. This is the response we got: ', 'wp-webhooks' ) . ( is_array( $response['content'] ) || is_object( $response['content'] ) ) ? json_encode( $response['content'] ) : esc_html( $response['code'] );
				WPWHPRO()->helpers->log_issue( $error_message );
			}

			return $return;
		}

		public function apply_auth( $template_id, $http_args = array() ){

			$return = false;
			
			if( empty( $template_id ) ){
				return $return;
			}
			
			$credentials_data = WPWHPRO()->auth->get_template( $template_id );
			
			if( isset( $credentials_data->template ) && is_array( $credentials_data->template ) ){

				if( isset( $this->session_tokens[ $template_id ] ) ){
					$session_token = $this->session_tokens[ $template_id ];
				} else {
					$session_token = $this->auth_login( $credentials_data );
					if( ! empty( $session_token ) ){
						$this->session_tokens[ $template_id ] = $session_token;
					}
				}

				if( ! empty( $session_token ) ){

					if( ! isset( $http_args['headers'] ) ){
						$http_args['headers'] = array();
					}

					$http_args['headers']['Authorization'] = 'Bearer ' . $session_token;
				}

				$return = $http_args;
			}
			
			return $return;
		}

		public function get_base_url( $credentials_data ){
			$return = '';
			
			if( empty( $credentials_data ) ){
				return $return;
			}
			
			if( is_numeric( $credentials_data ) ){
				$credentials_data = WPWHPRO()->auth->get_template( intval( $credentials_data ) );
			}
			
			if( isset( $credentials_data->template ) && is_array( $credentials_data->template ) ){
				
				$server_url = ( isset( $credentials_data->template['wpwhpro_filemaker_server_url'] ) ) ? $credentials_data->template['wpwhpro_filemaker_server_url'] : '';
				$fm_version = ( isset( $credentials_data->template['wpwhpro_filemaker_version'] ) ) ? $credentials_data->template['wpwhpro_filemaker_version'] : '';
				$fm_db = ( isset( $credentials_data->template['wpwhpro_filemaker_db'] ) ) ? $credentials_data->template['wpwhpro_filemaker_db'] : '';

				//Bail if the template was not properly filled
				if( 
					empty( $server_url ) 
					|| empty( $fm_db )
				){
					return $return;
				}

				switch( $fm_version ){
					case 'v1':
						$fm_version = 'v1';
						break;
					case 'v2':
						$fm_version = 'v2';
						break;
					default:
						$fm_version = 'vLatest';
						break;
				}

				$return = trim( $server_url, '/' ) . '/fmi/data/' . $fm_version . '/databases/' . urlencode( $fm_db );
			}
			
			return $return;
		}

	}

endif; // End if class_exists check.