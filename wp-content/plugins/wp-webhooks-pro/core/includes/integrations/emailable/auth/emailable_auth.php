<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_emailable_Auth_emailable_auth' ) ) :

	/**
	 * Load the emailable authentcation template
	 *
	 * @since 6.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_emailable_Auth_emailable_auth {

        public function get_details(){
			
			return array(
				'name' => __( 'Emailable API key', 'wp-webhooks' ),
				'short_description' => __( 'Add your Emailable API key into the input field. You can retrieve your access token <a title="Go to Emailable" href="https://app.emailable.com/api" target="_blank">here</a>.', 'wp-webhooks' ),
				'description' => '',
				'fields' => array(

					'wpwhpro_emailable_api_key' => array(
						'id'          => 'wpwhpro_emailable_api_key',
						'type'        => 'text',
						'label'       => __( 'API Key', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'Add your Emailable API key here. You can retrieve it from within your <a title="Go to Emailable" href="https://app.emailable.com/api" target="_blank">Emailable account dashboard</a>.', 'wp-webhooks' ),
						'description' => '',
					),

				)
			);
		}

		public function get_credentials( $template_id ){
			$credentials = array();
			
			$credentials_data = WPWHPRO()->auth->get_template( $template_id );
			
			if( isset( $credentials_data->template ) && is_array( $credentials_data->template ) ){
				$credentials = $credentials_data->template;
			}
			
			return $credentials;
		}

	}

endif; // End if class_exists check.