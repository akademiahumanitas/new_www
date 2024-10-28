<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_bitly_Auth_bitly_auth' ) ) :

	/**
	 * Load the bitly authentcation template
	 *
	 * @since 6.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_bitly_Auth_bitly_auth {

        public function get_details(){
			
			return array(
				'name' => __( 'Bitly access token', 'wp-webhooks' ),
				'short_description' => __( 'Add your Bitly access token into the input field. You can retrieve your access token <a title="Go to Bitly" href="https://app.bitly.com/settings/api/" target="_blank">here</a>.', 'wp-webhooks' ),
				'description' => '',
				'fields' => array(

					'wpwhpro_bitly_access_token' => array(
						'id'          => 'wpwhpro_bitly_access_token',
						'type'        => 'text',
						'label'       => __( 'Access token', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'Add your Bitly access token here. You can retrieve it from within your <a title="Go to Bitly" href="https://app.bitly.com/settings/api/" target="_blank">Bitly account dashboard</a>.', 'wp-webhooks' ),
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