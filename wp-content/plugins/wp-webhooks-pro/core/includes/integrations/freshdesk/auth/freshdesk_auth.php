<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_freshdesk_Auth_freshdesk_auth' ) ) :

	/**
	 * Load the Freshdesk authentcation template
	 *
	 * @since 6.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_freshdesk_Auth_freshdesk_auth {

        public function get_details(){
			
			return array(
				'name' => __( 'Freshdesk API key', 'wp-webhooks' ),
				'short_description' => __( 'Add your Freshdesk API key into the input field. You can retrieve your access token <a title="Go to Freshdesk" href="https://support.freshdesk.com/en/support/solutions/articles/215517-how-to-find-your-api-key" target="_blank">here</a>.', 'wp-webhooks' ),
				'description' => '',
				'fields' => array(

					'wpwhpro_freshdesk_domain' => array(
						'id'          => 'wpwhpro_freshdesk_domain',
						'type'        => 'text',
						'label'       => __( 'Freshdesk subdomain', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'Add your freshdesk subdomain here. E.g. if you access your tickets via https://samplecorp.freshdesk.com/a/tickets/, then <strong>samplecorp</strong> is your subdomain.', 'wp-webhooks' ),
						'description' => '',
					),

					'wpwhpro_freshdesk_api_key' => array(
						'id'          => 'wpwhpro_freshdesk_api_key',
						'type'        => 'text',
						'label'       => __( 'API Key', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'Add your Freshdesk API key here. You can retrieve it from within your <a title="Go to Freshdesk" href="https://support.freshdesk.com/en/support/solutions/articles/215517-how-to-find-your-api-key" target="_blank">Freshdesk account dashboard</a>.', 'wp-webhooks' ),
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