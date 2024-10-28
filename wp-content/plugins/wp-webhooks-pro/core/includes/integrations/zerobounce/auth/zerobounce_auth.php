<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_zerobounce_Auth_zerobounce_auth' ) ) :

	/**
	 * Load the zerobounce authentcation template
	 *
	 * @since 6.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_zerobounce_Auth_zerobounce_auth {

        public function get_details(){
			
			return array(
				'name' => __( 'ZeroBounce API key', 'wp-webhooks' ),
				'short_description' => __( 'Add your ZeroBounce API key into the input field. You can retrieve your access token <a title="Go to ZeroBounce" href="https://app.zerobounce.com/api" target="_blank">here</a>.', 'wp-webhooks' ),
				'description' => '',
				'fields' => array(

					'wpwhpro_zerobounce_api_key' => array(
						'id'          => 'wpwhpro_zerobounce_api_key',
						'type'        => 'text',
						'label'       => __( 'API Key', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'Add your ZeroBounce API key here. You can retrieve it from within your <a title="Go to ZeroBounce" href="https://app.zerobounce.com/api" target="_blank">ZeroBounce account dashboard</a>.', 'wp-webhooks' ),
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