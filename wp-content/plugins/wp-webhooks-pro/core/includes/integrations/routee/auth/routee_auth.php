<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_routee_Auth_routee_auth' ) ) :

	/**
	 * Load the routee authentcation template
	 *
	 * @since 6.1.4
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_routee_Auth_routee_auth {

        public function get_details(){
			
			return array(
				'name' => __( 'Routee credentials', 'wp-webhooks' ),
				'short_description' => __( 'Add your Routee Application Id and Application secret.', 'wp-webhooks' ),
				'description' => 'Please make sure the application you use was created with a key validity of unlimited.',
				'fields' => array(

					'wpwhpro_routee_app_id' => array(
						'id'          => 'wpwhpro_routee_app_id',
						'type'        => 'text',
						'label'       => __( 'Application Id', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'required' => true,
						'short_description' => __( 'You will find the Id within your Routee account > Applications > The application of your choice.', 'wp-webhooks' ),
						'description' => '',
					),

					'wpwhpro_routee_app_secret' => array(
						'id'          => 'wpwhpro_routee_app_secret',
						'type'        => 'text',
						'label'       => __( 'Application secret', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'The secret key of your application.', 'wp-webhooks' ),
						'description' => '',
					),

					'wpwhpro_routee_access_token' => array(
						'id'          => 'wpwhpro_routee_access_token',
						'type'        => 'text',
						'label'       => __( 'Access token', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'This is a dynamically generated value. It will be created once the first authentication template is used.', 'wp-webhooks' ),
						'description' => '',
						'attributes' => array(
							'readonly' => null
						),
					),

				)
			);
		}

		public function reauth_credentials( $credentials_data ){

			$app_id = ( isset( $credentials_data->template['wpwhpro_routee_app_id'] ) ) ? $credentials_data->template['wpwhpro_routee_app_id'] : '';
			$app_secret = ( isset( $credentials_data->template['wpwhpro_routee_app_secret'] ) ) ? $credentials_data->template['wpwhpro_routee_app_secret'] : '';

			//Bail if the template was not properly filled
			if( empty( $app_id ) || empty( $app_secret ) ){
				return $credentials_data;
			}

			$basic_string = base64_encode( $app_id . ':' . $app_secret );

			$http_args = array(
				'timeout' => ( ! empty( $timeout ) ) ? $timeout : 30,
				'headers' => array(
					'authorization' => 'Basic ' . $basic_string,
					'content-type' => 'application/x-www-form-urlencoded',
				),
			);

			$http_args['body'] = array(
				'grant_type' => 'client_credentials'
			);

			$response = WPWHPRO()->http->send_http_request( 'https://auth.routee.net/oauth/token', $http_args );
	
			if( isset( $response['code'] ) && $response['code'] === 200 ){

				if( 
					isset( $response['content'] )
					&& isset( $response['content']['access_token'] )
					&& ! empty( $response['content']['access_token'] )
				){
					$access_token = sanitize_title( $response['content']['access_token'] );

					$credentials_data->template['wpwhpro_routee_access_token'] = $access_token;

					$check = WPWHPRO()->auth->update_template( $credentials_data->id, array(
						'template' => json_encode( $credentials_data->template ),
					  ) );
				}
				
			} else {
				$error_message = __( 'The Routee access token authentication failed. This is the response we got: ', 'wp-webhooks' ) . ( is_array( $response['code'] ) || is_object( $response['code'] ) ) ? json_encode( $response['code'] ) : esc_html( $response['code'] );
				WPWHPRO()->helpers->log_issue( $error_message );
			}

			return $credentials_data;
		}

		public function apply_auth( $template_id, $http_args = array() ){

			$return = false;
			
			if( empty( $template_id ) ){
				return $return;
			}
			
			$credentials_data = WPWHPRO()->auth->get_template( $template_id );
			
			if( isset( $credentials_data->template ) && is_array( $credentials_data->template ) ){
				
				$access_token = ( isset( $credentials_data->template['wpwhpro_routee_access_token'] ) ) ? $credentials_data->template['wpwhpro_routee_access_token'] : '';

				if( empty( $access_token ) ){
					$credentials_data = $this->reauth_credentials( $credentials_data );
					$access_token = ( isset( $credentials_data->template['wpwhpro_routee_access_token'] ) ) ? $credentials_data->template['wpwhpro_routee_access_token'] : '';
				}

				if( ! empty( $access_token ) ){

					if( ! isset( $http_args['headers'] ) ){
						$http_args['headers'] = array();
					}

					$http_args['headers']['Authorization'] = 'Bearer ' . $access_token;
				}

				$return = $http_args;
			}
			
			return $return;
		}

	}

endif; // End if class_exists check.