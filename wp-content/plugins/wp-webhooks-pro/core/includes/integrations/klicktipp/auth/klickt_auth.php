<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_klicktipp_Auth_klickt_auth' ) ) :

	/**
	 * Load the klicktipp authentcation template
	 *
	 * @since 6.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_klicktipp_Auth_klickt_auth {

        public function get_details(){
			
			return array(
				'name' => __( 'KlickTipp credentials', 'wp-webhooks' ),
				'short_description' => __( 'Add your KlickTipp username and password', 'wp-webhooks' ),
				'description' => '',
				'fields' => array(

					'wpwhpro_klickt_username' => array(
						'id'          => 'wpwhpro_klickt_username',
						'type'        => 'text',
						'label'       => __( 'Username', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'Set the key you have to use to recognize the API key from the other endpoint.', 'wp-webhooks' ),
						'description' => '',
					),

					'wpwhpro_klickt_password' => array(
						'id'          => 'wpwhpro_klickt_password',
						'type'        => 'text',
						'label'       => __( 'Password', 'wp-webhooks' ),
						'placeholder' => '',
						'default_value' => '',
						'short_description' => __( 'This is the field you can include your API key. ', 'wp-webhooks' ),
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