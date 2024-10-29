<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_Triggers_wc_user_password_reset' ) ) :

	/**
	 * Load the wc_user_password_reset trigger
	 *
	 * @since 6.1.5
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_woocommerce_Triggers_wc_user_password_reset {

		public function get_callbacks(){

            return array(
                array(
                    'type' => 'action',
                    'hook' => 'woocommerce_save_account_details_errors',
                    'callback' => array( $this, 'ironikus_trigger_wc_user_password_reset' ),
                    'priority' => 20,
                    'arguments' => 2,
                    'delayed' => false,
                ),
            );

		}

        public function get_details(){

			$parameter = array(
				'new_pass'   => array( 'short_description' => __( 'The new password.', 'wp-webhooks' ) ),
				'user_id'   => array( 'short_description' => __( 'The user ID', 'wp-webhooks' ) ),
				'user_email'   => array( 'short_description' => __( 'The user email', 'wp-webhooks' ) ),
			);

			$settings = array(
				'load_default_settings' => true,
				'data' => array()
			);

            return array(
                'trigger'           => 'wc_user_password_reset',
                'name'              => __( 'User password updated', 'wp-webhooks' ),
                'sentence'          => __( 'a user password is updated', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'settings'          => $settings,
                'returns_code'      => $this->get_demo( array() ),
                'short_description' => __( 'This webhook fires after a user password is updated within a WooCommerce account page.', 'wp-webhooks' ),
                'description'       => array(), 
                'integration'       => 'woocommerce',
                'premium'           => true,
            );

        }

        public function ironikus_trigger_wc_user_password_reset( $errors, $user ){

			//Bail if something is not correct
			if(
				! is_object( $user )
				|| ! isset( $user->user_pass )
			){
				return;
			}

			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wc_user_password_reset' );
			$data_array = array(
				'new_pass' => $user->user_pass,
				'user_id' => $user->ID,
				'user_email' =>( isset(  $user->user_email ) ) ?  $user->user_email : '',
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
				'user_id' => 123,
				'user_email' => 'jondoe@yourdomain.com',
			);

            return $data;
        }

    }

endif; // End if class_exists check.