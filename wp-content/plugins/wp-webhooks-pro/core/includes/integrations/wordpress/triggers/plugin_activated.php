<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Triggers_plugin_activated' ) ) :

	/**
	 * Load the plugin_activated trigger
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Triggers_plugin_activated {

        public function is_active(){

            //Backwards compatibility for the "Manage Plugins" integration
            if( defined( 'WPWHPRO_MNGPL_PLUGIN_NAME' ) ){
                return false;
            }

            return true;
        }

		public function get_callbacks(){

            return array(
                array(
                    'type' => 'action',
                    'hook' => 'activated_plugin',
                    'callback' => array( $this, 'ironikus_trigger_plugin_activated' ),
                    'priority' => 10,
                    'arguments' => 2,
                    'delayed' => false,
                ),
            );

		}

        public function get_details(){

            $parameter = array(
				'plugin_slug' => array( 'short_description' => __( '(String) The slug of the plugin. You will find an example within the demo data.', 'wp-webhooks' ) ),
				'network_wide' => array( 'short_description' => __( '(Bool) True if the plugin was activated for the whole network of a multisite, false if not.', 'wp-webhooks' ) ),
			);

			$settings = array(
				'load_default_settings' => true,
				'data' => array(
					'wpwhpro_manage_plugins_plugin_activated_network' => array(
						'id'          => 'wpwhpro_manage_plugins_plugin_activated_network',
						'type'        => 'select',
						'multiple'    => true,
						'choices'      => array(
                            'single' => __( 'Single site', 'wp-webhooks' ),
                            'multi' => __( 'Multisite', 'wp-webhooks' ),
                        ),
						'label'       => __( 'Fire trigger on single or multisite.', 'wp-webhooks' ),
						'placeholder' => '',
						'required'    => false,
						'description' => __( 'In case you run a multisite network, select if you want to trigger the webhook on multisite activations, single site activations or both. If nothing is selected, both are triggered.', 'wp-webhooks' )
					),
				)
			);

            return array(
                'trigger'           => 'plugin_activated',
                'name'              => __( 'Plugin activated', 'wp-webhooks' ),
                'sentence'              => __( 'a plugin was activated', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'settings'          => $settings,
                'returns_code'      => $this->get_demo( array() ),
                'short_description' => __( 'This webhook fires as soon as a plugin was activated.', 'wp-webhooks' ),
                'description'       => array(),
                'callback'          => 'test_plugin_activated',
                'integration'       => 'wordpress',
                'premium'           => true,
            );

        }

        public function ironikus_trigger_plugin_activated( $plugin, $network_wide ){
			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'plugin_activated' );
			$response_data = array();
			$data = array(
				'plugin_slug' => $plugin,
				'network_wide' => $network_wide,
			);

			foreach( $webhooks as $webhook ){

				$is_valid = true;

				if( isset( $webhook['settings'] ) ){
					foreach( $webhook['settings'] as $settings_name => $settings_data ){

						if( $settings_name === 'wpwhpro_manage_plugins_plugin_activated_network' && ! empty( $settings_data ) ){
							
							$is_valid = false;
							if( in_array( 'single', $settings_data ) && ! $network_wide ){
								$is_valid = true;
							}
							if( in_array( 'multi', $settings_data ) && $network_wide ){
								$is_valid = true;
							}

						}

					}
				}

				if( $is_valid ) {
                    $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

                    if( $webhook_url_name !== null ){
                        $response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data );
                    } else {
                        $response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data );
                    }
				}

			}

			do_action( 'wpwhpro/webhooks/trigger_plugin_activated', $plugin, $network_wide, $data, $response_data );
		}

        /*
        * Register the demo post delete trigger callback
        *
        * @since 1.6.4
        */
        public function get_demo( $options = array() ) {

            $data = array(
				'plugin_slug' => 'plugin-folder/plugin-file.php',
				'network_wide' => 'false',
			);

            return $data;
        }

    }

endif; // End if class_exists check.