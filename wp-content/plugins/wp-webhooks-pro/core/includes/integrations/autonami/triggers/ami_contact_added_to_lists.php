<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_autonami_Triggers_ami_contact_added_to_lists' ) ) :

    /**
     * Load the ami_contact_added_to_lists trigger
     *
     * @since 5.2.5
     * @author Ironikus <info@ironikus.com>
     */
    class WP_Webhooks_Integrations_autonami_Triggers_ami_contact_added_to_lists {

        public function get_callbacks(){

            return array(
                array(
                    'type' => 'action',
                    'hook' => 'bwfan_contact_added_to_lists',
                    'callback' => array( $this, 'wpwh_trigger_ami_contact_added_to_lists' ),
                    'priority' => 10,
                    'arguments' => 2,
                    'delayed' => true,
                ),
            );
        }

        public function get_details(){

            $parameter = array(
                'assigned_lists' => array( 'short_description' => __( '(Array) All list IDs the contact have been added from this request.', 'wp-webhooks' ) ),
                'contact_id' => array( 'short_description' => __( '(Integer) The ID of the given contact.', 'wp-webhooks' ) ),
                'email' => array( 'short_description' => __( '(String) The contact email.', 'wp-webhooks' ) ),
            );

            $settings = array(
                'load_default_settings' => true,
                 'data' => array(
                     'wpwhpro_autonami_trigger_on_lists' => array(
                        'id'		  => 'wpwhpro_autonami_trigger_on_lists',
                        'type'		=> 'select',
                        'multiple'	=> true,
                        'choices'	  => array(),
                        'query'			=> array(
                            'filter'	=> 'helpers',
                            'args'		=> array(
                                'integration' => 'autonami',
                                'helper' => 'ami_helpers',
                                'function' => 'get_query_lists',
                            )
                        ),
                        'label'	   => __( 'Trigger on selected lists', 'wp-webhooks' ),
                        'placeholder' => '',
                        'required'	=> false,
                        'description' => __( 'Select only the lists you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', 'wp-webhooks' )
                    ),
                 )

            );

            return array(
                'trigger'		   => 'ami_contact_added_to_lists',
                'name'			  => __( 'Contact added to list', 'wp-webhooks' ),
                'sentence'			  => __( 'a contact was added to one or multiple lists', 'wp-webhooks' ),
                'parameter'		 => $parameter,
                'settings'		  => $settings,
                'returns_code'	  => $this->get_demo( array() ),
                'short_description' => __( 'This webhook fires as soon as a contact was added to one or multiple lists within FunnelKit Automations.', 'wp-webhooks' ),
                'description'	   => array(),
                'integration'	   => 'autonami',
                'premium'		   => true,
            );

        }

        /**
         * Triggers once a contact was added to a list within FunnelKit Automations
         *
         * @param array $assigned_lists The assigned lists
         * @param object|Contact $contact The contact object
         */
        public function wpwh_trigger_ami_contact_added_to_lists( $assigned_lists, $contact ){

            $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'ami_contact_added_to_lists' );

            $lists = array();
            foreach ( $assigned_lists as $list ) {
                if ( ! $list instanceof BWFCRM_Lists ) {
                    continue;
                }

                $list_id = $list->get_id();
                $lists[ $list_id ] = $list->get_name();
            }

            $payload = array(
                'assigned_lists' => $lists,
                'contact_id' => ( $contact instanceof BWFCRM_Contact ) ? $contact->get_id() : 0,
                'email' => ( $contact instanceof BWFCRM_Contact ) ? $contact->contact->get_email() : ''
            );

            $response_data_array = array();

            foreach( $webhooks as $webhook ){

                $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
                $is_valid = true;

                if( isset( $webhook['settings'] ) ){
                    foreach( $webhook['settings'] as $settings_name => $settings_data ){

                        if( $settings_name === 'wpwhpro_autonami_trigger_on_lists' && ! empty( $settings_data ) ){
                            
                            $is_valid = false;

                            foreach( $lists as $list_id => $list_title ){
                                if( in_array( $list_id, $settings_data ) ){
                                    $is_valid = true;
                                    break;
                                }
                            }

                        }
                    }
                }


                if( $is_valid ){
                    if( $webhook_url_name !== null ){
                        $response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
                    } else {
                        $response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
                    }
                }

            }

            do_action( 'wpwhpro/webhooks/trigger_ami_contact_added_to_lists', $payload, $response_data_array );
        }

        public function get_demo( $options = array() ) {

            $data = array (
                'assigned_lists' => array(
                    '2' => 'Free',
                    '3' => 'Conference',
                ),
                'contact_id' => '3',
                'email'=> 'email@yourdomain.com'
            );

            return $data;
        }

    }

endif; // End if class_exists check.