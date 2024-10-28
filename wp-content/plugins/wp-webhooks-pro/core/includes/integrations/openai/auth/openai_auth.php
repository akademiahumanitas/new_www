<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_openai_Auth_openai_auth' ) ) :

	/**
	 * Load the openai authentcation template
	 *
	 * @since 6.1.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_openai_Auth_openai_auth {

        public function get_details(){
			
			return array(
				'name' => __( 'OpenAI credentials', 'wp-webhooks' ),
				'short_description' => __( 'Add your OpenAI username and password', 'wp-webhooks' ),
				'description' => '',
				'fields' => array(

					'wpwhpro_openai_api_key' => array(
						'id'          => 'wpwhpro_openai_api_key',
						'type'        => 'text',
						'label'       => __( 'API key', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'required' => true,
						'short_description' => __( 'You will find the API key from within your account dashboard of OpenAI.', 'wp-webhooks' ),
						'description' => '',
					),

					'wpwhpro_openai_organization' => array(
						'id'          => 'wpwhpro_openai_organization',
						'type'        => 'text',
						'label'       => __( 'Organization ID', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'The ID of an organization you want to count the request quota against. This is optional.', 'wp-webhooks' ),
						'description' => '',
					),

				)
			);
		}

		public function apply_auth( $template_id, $http_args = array() ){

			$return = false;
			
			if( empty( $template_id ) ){
				return $return;
			}
			
			$credentials_data = WPWHPRO()->auth->get_template( $template_id );
			
			if( isset( $credentials_data->template ) && is_array( $credentials_data->template ) ){
				
				$api_key = ( isset( $credentials_data->template['wpwhpro_openai_api_key'] ) ) ? $credentials_data->template['wpwhpro_openai_api_key'] : '';
				if( ! empty( $api_key ) ){

					if( ! isset( $http_args['headers'] ) ){
						$http_args['headers'] = array();
					}

					$http_args['headers']['Authorization'] = 'Bearer ' . $api_key;
				}
				
				$organization = ( isset( $credentials_data->template['wpwhpro_openai_organization'] ) ) ? $credentials_data->template['wpwhpro_openai_organization'] : '';
				if( ! empty( $organization ) ){

					if( ! isset( $http_args['headers'] ) ){
						$http_args['headers'] = array();
					}

					$http_args['headers']['OpenAI-Organization'] = $organization;
				}

				$return = $http_args;
			}
			
			return $return;
		}

	}

endif; // End if class_exists check.