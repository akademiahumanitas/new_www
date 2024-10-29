<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_autonami_Triggers_ami_tags_added_to_contact' ) ) :

    /**
     * Load the ami_tags_added_to_contact trigger
     *
     * @since 5.2.5
     * @author Ironikus <info@ironikus.com>
     */
    class WP_Webhooks_Integrations_autonami_Triggers_ami_tags_added_to_contact {

        public function get_callbacks(){

            return array(
                array(
                    'type' => 'action',
                    'hook' => 'bwfan_tags_added_to_contact',
                    'callback' => array( $this, 'wpwh_trigger_ami_tags_added_to_contact' ),
                    'priority' => 10,
                    'arguments' => 2,
                    'delayed' => true,
                ),
            );
        }

        public function get_details(){

            $parameter = array(
                'assigned_tags' => array( 'short_description' => __( '(Array) All tag IDs the contact have been added from this request.', 'wp-webhooks' ) ),
                'contact' => array( 'short_description' => __( '(Object) All details of the contact.', 'wp-webhooks' ) ),
            );

            $settings = array(
                'load_default_settings' => true,
                'data' => array(
                    'wpwhpro_autonami_trigger_on_tags' => array(
                       'id'		  => 'wpwhpro_autonami_trigger_on_tags',
                       'type'		=> 'select',
                       'multiple'	=> true,
                       'choices'	  => array(),
                       'query'			=> array(
                           'filter'	=> 'helpers',
                           'args'		=> array(
                               'integration' => 'autonami',
                               'helper' => 'ami_helpers',
                               'function' => 'get_query_tags',
                           )
                       ),
                       'label'	   => __( 'Trigger on selected tags', 'wp-webhooks' ),
                       'placeholder' => '',
                       'required'	=> false,
                       'description' => __( 'Select only the tags you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', 'wp-webhooks' )
                   ),
                )

            );

            return array(
                'trigger'		   => 'ami_tags_added_to_contact',
                'name'			  => __( 'Contact tags added', 'wp-webhooks' ),
                'sentence'			  => __( 'one or multiple tags have been added to a contact', 'wp-webhooks' ),
                'parameter'		 => $parameter,
                'settings'		  => $settings,
                'returns_code'	  => $this->get_demo( array() ),
                'short_description' => __( 'This webhook fires as soon as one or multiple tags have been added to a contact within FunnelKit Automations.', 'wp-webhooks' ),
                'description'	   => array(),
                'integration'	   => 'autonami',
                'premium'		   => true,
            );

        }

        /**
         * Triggers once a contact was added to a tag within FunnelKit Automations
         *
         * @param array $assigned_tags The assigned tags
         * @param object|Contact $contact The contact object
         */
        public function wpwh_trigger_ami_tags_added_to_contact( $assigned_tags, $contact ){

            $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'ami_tags_added_to_contact' );

            $tags = array();
            foreach ( $assigned_tags as $tag ) {
                if ( ! $tag instanceof BWFCRM_Tag ) {
                    continue;
                }
                $tag_id = $tag->get_id();
                $tags[$tag_id] = $tag->get_name();
            }

            $payload = array(
                'assigned_tags' => $tags,
                'contact_id' => ( $contact instanceof BWFCRM_Contact ) ? $contact->get_id() : 0,
                'email' => ( $contact instanceof BWFCRM_Contact ) ? $contact->contact->get_email() : ''
            );

            $response_data_array = array();

            foreach( $webhooks as $webhook ){

                $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
                $is_valid = true;

                if( isset( $webhook['settings'] ) ){
                    foreach( $webhook['settings'] as $settings_name => $settings_data ){

                        if( $settings_name === 'wpwhpro_autonami_trigger_on_tags' && ! empty( $settings_data ) ){
                            
                            $is_valid = false;

                            foreach( $tags as $tag_id => $tag_title ){
                                if( in_array( $tag_id, $settings_data ) ){
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

            do_action( 'wpwhpro/webhooks/trigger_ami_tags_added_to_contact', $payload, $response_data_array );
        }

        public function get_demo( $options = array() ) {

            $data = array (
                'assigned_tags' => array(
                    '2' => 'Free',
                    '3' => 'Conference',
                ),
                'contact_id' => '3',
                'email'=> 'hellodojo@gmail.com'
            );

            return $data;
        }

    }

endif; // End if class_exists check.