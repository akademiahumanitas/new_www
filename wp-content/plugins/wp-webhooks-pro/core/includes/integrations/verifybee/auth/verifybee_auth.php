<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_verifybee_Auth_verifybee_auth' ) ) :

	/**
	 * Load the verifybee authentcation template
	 *
	 * @since 6.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_verifybee_Auth_verifybee_auth {

        public function get_details(){
			
			return array(
				'name' => __( 'VerifyBee API key', 'wp-webhooks' ),
				'short_description' => __( 'Add your VerifyBee API key into the input field. You can retrieve your access token <a title="Go to VerifyBee" href="https://app.verifybee.com/api" target="_blank">here</a>.', 'wp-webhooks' ),
				'description' => '',
				'fields' => array(

					'wpwhpro_verifybee_api_key' => array(
						'id'          => 'wpwhpro_verifybee_api_key',
						'type'        => 'text',
						'label'       => __( 'API Key', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'Add your VerifyBee API key here. You can retrieve it from within your <a title="Go to VerifyBee" href="https://app.verifybee.com/api" target="_blank">VerifyBee account dashboard</a>.', 'wp-webhooks' ),
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