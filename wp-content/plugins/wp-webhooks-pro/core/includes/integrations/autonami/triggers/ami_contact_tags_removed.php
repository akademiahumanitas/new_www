<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_autonami_Triggers_ami_contact_tags_removed' ) ) :

    /**
     * Load the ami_contact_tags_removed trigger
     *
     * @since 5.2.5
     * @author Ironikus <info@ironikus.com>
     */
    class WP_Webhooks_Integrations_autonami_Triggers_ami_contact_tags_removed {

        public function get_callbacks(){
            return array(
                array(
                    'type' => 'action',
                    'hook' => 'bwfan_tags_removed_from_contact',
                    'callback' => array( $this, 'wpwh_trigger_ami_contact_tags_removed' ),
                    'priority' => 10,
                    'arguments' => 2,
                    'delayed' => true,
                ),
            );
        }

        public function get_details(){

            $parameter = array(
                'removed_tags' => array( 'short_description' => __( '(Array) All tag IDs the contact have been removed from this request.', 'wp-webhooks' ) ),
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
                'trigger'		   => 'ami_contact_tags_removed',
                'name'			  => __( 'Contact tags removed', 'wp-webhooks' ),
                'sentence'			  => __( 'one or multiple contact tags have been removed', 'wp-webhooks' ),
                'parameter'		 => $parameter,
                'settings'		  => $settings,
                'returns_code'	  => $this->get_demo( array() ),
                'short_description' => __( 'This webhook fires as soon as one or multiple contact tags have been removed within FunnelKit Automations.', 'wp-webhooks' ),
                'description'	   => array(),
                'integration'	   => 'autonami',
                'premium'		   => true,
            );

        }

        /**
         * Triggers once a contact tag was removed within FunnelKit Automations
         *
         * @param array $removed_tags The removed tag
         * @param object|Contact $contact The contact object
         */
        public function wpwh_trigger_ami_contact_tags_removed( $removed_tags, $contact ){

            $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'ami_contact_tags_removed' );

            $payload = array(
                'removed_tags' => $removed_tags,
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

                            foreach( $removed_tags as $tag_id ){
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

            do_action( 'wpwhpro/webhooks/trigger_ami_contact_tags_removed', $payload, $response_data_array );
        }

        public function get_demo( $options = array() ) {

            $data = array (
                'removed_tags' => array(
                    2
                ),
                'contact_id' => '3',
                'email'=> 'hellodojo@gmail.com'
            );

            return $data;
        }

    }

endif; // End if class_exists check.