<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Triggers_wp_user_password_reset' ) ) :

	/**
	 * Load the wp_user_password_reset trigger
	 *
	 * @since 6.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Triggers_wp_user_password_reset {

		public function get_callbacks(){

            return array(
                array(
                    'type' => 'action',
                    'hook' => 'after_password_reset',
                    'callback' => array( $this, 'ironikus_trigger_wp_user_password_reset' ),
                    'priority' => 10,
                    'arguments' => 2,
                    'delayed' => true,
                ),
            );

		}

        public function get_details(){

			$parameter = array(
				'new_pass'   => array( 'short_description' => __( 'The new password.', 'wp-webhooks' ) ),
				'user'   => array( 'short_description' => __( 'Further details about the user.', 'wp-webhooks' ) ),
			);

			$settings = array(
				'load_default_settings' => true,
				'data' => array()
			);

            return array(
                'trigger'           => 'wp_user_password_reset',
                'name'              => __( 'User password reset', 'wp-webhooks' ),
                'sentence'          => __( 'a user password was reset', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'settings'          => $settings,
                'returns_code'      => $this->get_demo( array() ),
                'short_description' => __( 'This webhook fires after a user password was reset.', 'wp-webhooks' ),
                'description'       => array(), 
                'integration'       => 'wordpress',
                'premium'           => true,
            );

        }

        public function ironikus_trigger_wp_user_password_reset( $user, $new_pass ){

			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wp_user_password_reset' );
			$data_array = array(
				'new_pass' => $new_pass,
				'user' => $user,
			);
			$response_data = array();

			foreach( $webhooks as $webhook ){

				$is_valid = true;

				if( $is_valid ){
					$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

					if( $webhook_url_name !== null ){
						$response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
					} else {
						$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
					}
				}
			}

		}

        /*
        * Register the demo post delete trigger callback
        *
        * @since 1.6.4
        */
        public function get_demo( $options = array() ) {

            $data = array (
				'new_pass' => 'P(d)20C!9H^Q!JXg',
				'user' => 
				array (
				  'data' => 
				  array (
					'ID' => '204',
					'user_login' => 'demo',
					'user_pass' => '$P$Bnm7nPREIhFTzzk84IyAn4XEe/Siea/',
					'user_nicename' => 'demo',
					'user_email' => 'demo@demo.test',
					'user_url' => '',
					'user_registered' => '2022-11-16 13:27:55',
					'user_activation_key' => '1668605489:$P$BZCyvOvzpsu.4IihPp6JvPttfI5lfR0',
					'user_status' => '0',
					'display_name' => 'demou',
					'spam' => '0',
					'deleted' => '0',
				  ),
				  'ID' => 204,
				  'caps' => 
				  array (
					'subscriber' => true,
				  ),
				  'cap_key' => 'wp_capabilities',
				  'roles' => 
				  array (
					0 => 'subscriber',
				  ),
				  'allcaps' => 
				  array (
					'read' => true,
					'subscriber' => true,
				  ),
				  'filter' => NULL,
				),
			);

            return $data;
        }

    }

endif; // End if class_exists check.