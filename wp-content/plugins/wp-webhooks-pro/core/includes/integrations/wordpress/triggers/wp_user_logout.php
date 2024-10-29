<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Triggers_wp_user_logout' ) ) :

	/**
	 * Load the wp_user_logout trigger
	 *
	 * @since 5.2.4
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Triggers_wp_user_logout {

		public function get_callbacks(){

            return array(
                array(
                    'type' => 'action',
                    'hook' => 'wp_logout',
                    'callback' => array( $this, 'ironikus_trigger_wp_user_logout' ),
                    'priority' => 10,
                    'arguments' => 1,
                    'delayed' => true,
                ),
            );

		}

        public function get_details(){

			$parameter = array(
				'user_id'   => array( 'short_description' => __( 'The ID of the user.', 'wp-webhooks' ) ),
				'user_data'   => array( 'short_description' => __( 'The user data.', 'wp-webhooks' ) ),
				'user_meta'   => array( 'short_description' => __( 'Further details about the user.', 'wp-webhooks' ) ),
			);

			$settings = array(
				'load_default_settings' => true,
				'data' => array()
			);

            return array(
                'trigger'           => 'wp_user_logout',
                'name'              => __( 'User logged out', 'wp-webhooks' ),
                'sentence'              => __( 'a user was logged out', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'settings'          => $settings,
                'returns_code'      => $this->get_demo( array() ),
                'short_description' => __( 'This webhook fires after a user was logged out.', 'wp-webhooks' ),
                'description'       => array(), 
                'integration'       => 'wordpress',
                'premium'           => true,
            );

        }

        public function ironikus_trigger_wp_user_logout( $user_id ){

			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wp_user_logout' );
			$data_array = array(
				'user_id' => ( ! empty( $user_id ) ) ? $user_id : 0,
				'user_data' => ( ! empty( $user_id ) ) ? get_user_by( 'id ', $user_id ) : array(),
				'user_meta' => ( ! empty( $user_id ) ) ? get_user_meta( $user_id ) : array(),
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

        public function get_demo( $options = array() ) {

            $data = array (
				'user_id' => 179,
				'user_data' => 
				array (
				  'data' => 
				  array (
					'ID' => '179',
					'user_login' => 'user',
					'user_pass' => '$P$B3.GWxvqe.xcPLRBB2zRwEZRMo16X51',
					'user_nicename' => 'user',
					'user_email' => 'demouser@demodomain.com',
					'user_url' => '',
					'user_registered' => '2022-08-13 09:33:35',
					'user_activation_key' => '',
					'user_status' => '0',
					'display_name' => '',
					'spam' => '0',
					'deleted' => '0',
				  ),
				  'ID' => 179,
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
				'user_meta' => 
				array (
				  'nickname' => 
				  array (
					0 => 'user',
				  ),
				  'first_name' => 
				  array (
					0 => '',
				  ),
				  'last_name' => 
				  array (
					0 => '',
				  ),
				  'description' => 
				  array (
					0 => 'This is a demo description.',
				  ),
				  'rich_editing' => 
				  array (
					0 => 'true',
				  ),
				  'syntax_highlighting' => 
				  array (
					0 => 'true',
				  ),
				  'comment_shortcuts' => 
				  array (
					0 => 'false',
				  ),
				  'admin_color' => 
				  array (
					0 => 'fresh',
				  ),
				  'use_ssl' => 
				  array (
					0 => '0',
				  ),
				  'show_admin_bar_front' => 
				  array (
					0 => 'true',
				  ),
				  'locale' => 
				  array (
					0 => '',
				  ),
				  'account_status' => 
				  array (
					0 => 'approved',
				  ),
				  'dismissed_wp_pointers' => 
				  array (
					0 => '',
				  ),
				  'synced_gravatar_hashed_id' => 
				  array (
					0 => '932f647e072c000c6305eb5a625826ae',
				  ),
				  'primary_blog' => 
				  array (
					0 => '1',
				  ),
				  'source_domain' => 
				  array (
					0 => 'yourdomain.test',
				  ),
				  'wp_capabilities' => 
				  array (
					0 => 'a:1:{s:10:"subscriber";b:1;}',
				  ),
				  'wp_user_level' => 
				  array (
					0 => '0',
				  ),
				  'demo_field' => 
				  array (
					0 => '',
				  ),
				),
			);

            return $data;
        }

    }

endif; // End if class_exists check.