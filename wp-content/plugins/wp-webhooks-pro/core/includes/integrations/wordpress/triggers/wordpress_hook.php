<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Triggers_wordpress_hook' ) ) :

	/**
	 * Load the wordpress_hook trigger
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Triggers_wordpress_hook {

		public function get_callbacks(){

            return array(
                array(
                    'type' => 'action',
                    'hook' => 'wpwhpro/integrations/callbacks_registered',
                    'callback' => array( $this, 'register_wrodpress_hook_callbacks' ),
                    'priority' => 5,
                    'arguments' => 0,
                    'delayed' => false,
                ),
            );

		}

        public function get_details(){

            $parameter = array(
                'none'   => array( 'short_description' => __( 'No default values given. Send over whatever you like.', 'wp-webhooks' ) ),
            );

            $settings = array(
                'load_default_settings' => true,
                'data' => array(
                    'wpwhpro_wordpress_hook_definition_type' => array(
						'id'          => 'wpwhpro_wordpress_hook_definition_type',
						'type'        => 'select',
						'multiple'    => false,
						'choices'      => array(
                            'action' => __( 'Action', 'wp-webhooks' ),
                            'filter' => __( 'Filter', 'wp-webhooks' ),
                        ),
						'label'       => __( 'WordPress hook type', 'wp-webhooks' ),
						'placeholder' => '',
						'required'    => false,
						'description' => __( 'Select whether your defined hook is a filter or an action.', 'wp-webhooks' )
					),
					'wpwhpro_wordpress_hook_definition' => array(
						'id'          => 'wpwhpro_wordpress_hook_definition',
						'type'        => 'text',
						'label'       => __( 'WordPress hook name', 'wp-webhooks' ),
						'placeholder' => '',
						'required'    => false,
						'description' => __( 'Add the WordPress hook name that you want to use to fire this trigger on.', 'wp-webhooks' )
					),
					'wpwhpro_wordpress_hook_definition_priority' => array(
						'id'          => 'wpwhpro_wordpress_hook_definition_priority',
						'type'        => 'text',
						'label'       => __( 'WordPress hook priority', 'wp-webhooks' ),
						'placeholder' => '',
						'required'    => false,
						'description' => __( 'Add a custom WordPress hook priority. Default: 10', 'wp-webhooks' )
					),
					'wpwhpro_wordpress_hook_definition_arguments' => array(
						'id'          => 'wpwhpro_wordpress_hook_definition_arguments',
						'type'        => 'text',
						'label'       => __( 'WordPress hook arguments', 'wp-webhooks' ),
						'placeholder' => '',
						'required'    => false,
						'description' => __( 'Define the number of arguments this hook has. Default: 1', 'wp-webhooks' )
					),
				)
            );

            return array(
                'trigger'           => 'wordpress_hook',
                'name'              => __( 'WordPress hook fired', 'wp-webhooks' ),
                'sentence'              => __( 'a WordPress hook was fired', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'settings'          => $settings,
                'returns_code'      => $this->get_demo(),
                'short_description' => __( 'This webhook fires once your selected WordPress hook (filter or action) has been called.', 'wp-webhooks' ),
                'description'       => array(),
                'integration'       => 'wordpress',
                'premium'           => true,
            );

        }


        public function register_wrodpress_hook_callbacks(){

            $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wordpress_hook' );

            foreach( $webhooks as $webhook_key => $webhook ){

                $hook_type = null;
                $hook = null;
                $priority = 10;
                $arguments = 1;

                if( isset( $webhook['settings'] ) ){
					foreach( $webhook['settings'] as $settings_name => $settings_data ){

						if( $settings_name === 'wpwhpro_wordpress_hook_definition_type' && ! empty( $settings_data ) ){
                            $hook_type = $settings_data;
						}

                        if( $settings_name === 'wpwhpro_wordpress_hook_definition' && ! empty( $settings_data ) ){
                            $hook = $settings_data;
                        }

                        if( $settings_name === 'wpwhpro_wordpress_hook_definition_priority' && ! empty( $settings_data ) ){
                            $priority = $settings_data;
                        }

                        if( $settings_name === 'wpwhpro_wordpress_hook_definition_arguments' && ! empty( $settings_data ) ){
                            $arguments = $settings_data;
                        }
					}
				}

                if( ! empty( $hook_type ) && ! empty( $hook ) ){ 

                    $callback_func = function() use ( $webhook, $hook_type ) {
                        
                        $data = func_get_args();
                        $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

                        if( $webhook_url_name !== null ){
                            $response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data );
                        } else {
                            $response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data );
                        }

                        if( $hook_type === 'filter' ){
                            if( is_array( $data ) ){
                                foreach( $data as $return ){
                                    return $return; //whatever comes first
                                }
                            } else {
                                return $data;
                            }
                        }
                        
                    };  

                    switch( $hook_type ){
                        case 'action':
                            add_action( $hook, $callback_func, $priority, $arguments );
                            break;
                        case 'filter':
                            add_filter( $hook, $callback_func, $priority, $arguments );
                            break;
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

            return array( __( 'The data construct of your given hook callback.', 'wp-webhooks' ) ); // Custom content from the action
        }

    }

endif; // End if class_exists check.