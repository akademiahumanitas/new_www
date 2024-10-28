<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_tomba_Auth_tomba_auth' ) ) :

	/**
	 * Load the tomba authentcation template
	 *
	 * @since 6.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_tomba_Auth_tomba_auth {

        public function get_details(){
			
			return array(
				'name' => __( 'Tomba.io API keys', 'wp-webhooks' ),
				'short_description' => __( 'Add your Tomba.io API keys into the input fields. You can retrieve your API Keys <a title="Go to Tomba.io" href="https://app.tomba.io/api" target="_blank">here</a>.', 'wp-webhooks' ),
				'description' => '',
				'fields' => array(

					'wpwhpro_tomba_api_key' => array(
						'id'          => 'wpwhpro_tomba_api_key',
						'type'        => 'text',
						'label'       => __( 'API Key', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'Add your Tomba.io API key here (It starts with "ta_"). You can retrieve it from within your <a title="Go to Tomba.io" href="https://app.tomba.io/api" target="_blank">Tomba.io account dashboard</a>.', 'wp-webhooks' ),
						'description' => '',
					),

					'wpwhpro_tomba_api_key_secret' => array(
						'id'          => 'wpwhpro_tomba_api_key_secret',
						'type'        => 'text',
						'label'       => __( 'API Secret Key', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'Add your Tomba.io API secret key here (It starts with "ts_"). You can retrieve it from within your <a title="Go to Tomba.io" href="https://app.tomba.io/api" target="_blank">Tomba.io account dashboard</a>.', 'wp-webhooks' ),
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