<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_bounceless_Auth_bounceless_auth' ) ) :

	/**
	 * Load the bounceless authentcation template
	 *
	 * @since 6.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_bounceless_Auth_bounceless_auth {

        public function get_details(){
			
			return array(
				'name' => __( 'Bounceless API key', 'wp-webhooks' ),
				'short_description' => __( 'Add your Bounceless API key into the input field. You can retrieve your access token <a title="Go to Bounceless" href="https://app.bounceless.com/settings/api/" target="_blank">here</a>.', 'wp-webhooks' ),
				'description' => '',
				'fields' => array(

					'wpwhpro_bounceless_api_key' => array(
						'id'          => 'wpwhpro_bounceless_api_key',
						'type'        => 'text',
						'label'       => __( 'API Key', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'Add your Bounceless API key here. You can retrieve it from within your <a title="Go to Bounceless" href="https://app.bounceless.com/settings/api/" target="_blank">Bounceless account dashboard</a>.', 'wp-webhooks' ),
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