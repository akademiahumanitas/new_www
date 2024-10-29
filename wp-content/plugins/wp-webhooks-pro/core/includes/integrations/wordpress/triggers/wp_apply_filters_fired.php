<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Triggers_wp_apply_filters_fired' ) ) :

	/**
	 * Load the wp_apply_filters_fired trigger
	 *
	 * @since 6.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Triggers_wp_apply_filters_fired {

		public function get_callbacks(){

			$callbacks = array();
			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wp_apply_filters_fired' );

			foreach( $webhooks as $webhook ){

				$hook_name = '';
				$hook_priority = 10;
				$hook_arguments = 1;

				if( isset( $webhook['settings'] ) ){
                    foreach( $webhook['settings'] as $settings_name => $settings_data ){
                           
                        if( $settings_name === 'wpwhpro_wp_apply_filters_fired_hook_name' && ! empty( $settings_data ) ){
                            $hook_name = $settings_data;
                        }
                           
                        if( $settings_name === 'wpwhpro_wp_apply_filters_fired_hook_priority' && ! empty( $settings_data ) ){
                            $hook_priority = $settings_data;
                        }
                           
                        if( $settings_name === 'wpwhpro_wp_apply_filters_fired_hook_arguments' && ! empty( $settings_data ) ){
                            $hook_arguments = $settings_data;
                        }

                    }
                }

				if( ! empty( $hook_name ) ){
					$callbacks[] = array(
						'type' => 'filter',
						'hook' => $hook_name,
						'callback' => array( $this, 'wp_apply_filters_fired_callback' ),
						'priority' => $hook_priority,
						'arguments' => $hook_arguments,
						'delayed' => false,
					);
				}

			}

            return $callbacks;

		}

        public function get_details(){

			$parameter = array(
				'custom_data'   => array( 
					'label' => __( 'Custom Data ', 'wp-webhooks' ),
					'short_description' => __( 'The response of this webhook depends on the apply_filters function that was fired.', 'wp-webhooks' ),
				),
			);

			$settings = array(
				'load_default_settings' => true,
				'data' => array(
                    'wpwhpro_wp_apply_filters_fired_hook_name' => array(
                        'id'		  => 'wpwhpro_wp_apply_filters_fired_hook_name',
                        'type'		=> 'text',
                        'label'	   => __( 'Filter hook name', 'wp-webhooks' ),
                        'placeholder' => '',
                        'required'	=> true,
                        'short_description' => __( 'Set the name of the action hook you want to fire this trigger on.', 'wp-webhooks' ),
                        'description' => __( 'The action hook is the name of the apply_filters function. If the function is set to <code>apply_filters( "this_is_my_action_hook", "this_is_my_callback", "some data" )</code>, then <code>this_is_my_action_hook</code> is the name you use for the argument.', 'wp-webhooks' ),
                    ),
                    'wpwhpro_wp_apply_filters_fired_hook_priority' => array(
                        'id'		  => 'wpwhpro_wp_apply_filters_fired_hook_priority',
                        'type'		=> 'text',
                        'label'	   => __( 'Filter hook priority', 'wp-webhooks' ),
                        'placeholder' => '',
                        'required'	=> true,
                        'default_value'	=> 10,
                        'short_description' => __( 'Set the priority you want to use to fire the trigger. By default, we use the priority 10 to fire it.', 'wp-webhooks' ),
                        'description' => __( 'The hook priority determines at which position the webhook should be triggered. If some other plugin or theme uses the hook with a higher priority (which means it will be executed later), then you can adjust this setting to match it to your requirements.', 'wp-webhooks' ),
                    ),
                    'wpwhpro_wp_apply_filters_fired_hook_arguments' => array(
                        'id'		  => 'wpwhpro_wp_apply_filters_fired_hook_arguments',
                        'type'		=> 'text',
                        'label'	   => __( 'Filter hook arguments', 'wp-webhooks' ),
                        'placeholder' => '',
                        'required'	=> true,
                        'default_value'	=> 1,
                        'short_description' => __( 'By default, we return only the first argument that has been added by the apply_filters function. If the action supports multiple arguments, you can increase the number depending on the number of arguments available.', 'wp-webhooks' ),
                        'description' => __( 'Adjust the amount of arguments that will be sent via this webhok action. For example, if the action call looks like this <code>apply_filters( "this_is_my_action_hook", "this_is_my_callback", "First Argument", "Second Argument" )</code>, then we will send back only the first argument. If you set this setting to 2, we will return both the first and the second argument.', 'wp-webhooks' ),
                    ),
                )
			);

			$description = array(
				'steps' => array(
					__( 'To make sure this webhook trigger fires as expected, please set the "Filter hook name" within the settings.', 'wp-webhooks' ),
				),
				'tipps' => array(
					__( 'To learn more about the apply_filters function of WordPress, please follow this link:', 'wp-webhooks' ) . ' <a title="' . __( 'Visit the apply_filters hook argument', 'wp-webhooks' ) . '" target="_blank" href="https://developer.wordpress.org/reference/functions/apply_filters/">https://developer.wordpress.org/reference/functions/apply_filters/</a>',
					__( 'Please make sure that the apply_filters you try to call is not executed before the plugins_loaded hook:', 'wp-webhooks' ) . ' <a title="' . __( 'Visit the apply_filters hook argument', 'wp-webhooks' ) . '" target="_blank" href="https://developer.wordpress.org/reference/hooks/plugins_loaded/">https://developer.wordpress.org/reference/hooks/plugins_loaded/</a>',
				),
			);

            return array(
                'trigger'           => 'wp_apply_filters_fired',
                'name'              => __( 'Hook apply_filters fired', 'wp-webhooks' ),
                'sentence'          => __( 'a specific apply_filters hook was fired', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'settings'          => $settings,
                'returns_code'      => $this->get_demo( array() ),
                'short_description' => __( 'This webhook fires after a specific apply_filters hook has been fired within WordPress.', 'wp-webhooks' ),
                'description'       => $description, 
                'integration'       => 'wordpress',
                'premium'           => true,
            );

        }

        public function wp_apply_filters_fired_callback(){

			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wp_apply_filters_fired' );
			$data_array = func_get_args();
			$data_array_validated = array();
			$response_data = array();

			//validate the arguments with a named version
			$arg_counter = 1;
			foreach( $data_array as $ak => $av ){
				$data_array_validated[ 'argument_' . $arg_counter ] = $av;
				$arg_counter++;
			}

			foreach( $webhooks as $webhook ){

				$is_valid = true;

				if( $is_valid ){
					$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

					if( $webhook_url_name !== null ){
						$response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array_validated );
					} else {
						$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array_validated );
					}
				}
			}

		}

        public function get_demo( $options = array() ) {

            $data = array (
				'custom_data' => __( 'The response depends on the filter that you are calling.', 'wp-webhooks' ),
			);

            return $data;
        }

    }

endif; // End if class_exists check.