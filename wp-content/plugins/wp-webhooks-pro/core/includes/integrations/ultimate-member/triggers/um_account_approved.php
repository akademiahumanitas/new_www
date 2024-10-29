<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_ultimate_member_Triggers_um_account_approved' ) ) :

    /**
     * Load the um_account_approved trigger
     *
     * @since 5.2.2
     * @author Ironikus <info@ironikus.com>
     */
    class WP_Webhooks_Integrations_ultimate_member_Triggers_um_account_approved{

        public function get_callbacks(){

            return array(
                array(
                    'type' => 'action',
                    'hook' => 'um_after_user_is_approved',
                    'callback' => array( $this, 'wpwh_trigger_um_account_approved' ),
                    'priority' => 10,
                    'arguments' => 1,
                    'delayed' => true,
                ),
            );
        }

        public function get_details(){

            $parameter = array(
                'user_id' => array( 
                    'label' => __( 'User ID', 'wp-webhooks' ),
                    'short_description' => __( '(Integer) The user id.', 'wp-webhooks' ),
                ),
                'user_data' => array( 
                    'label' => __( 'User data', 'wp-webhooks' ),
                    'short_description' => __( '(Array) Further information about the user.', 'wp-webhooks' ),
                ),
                'user_meta' => array( 
                    'label' => __( 'User meta', 'wp-webhooks' ),
                    'short_description' => __( '(Array) The user meta data.', 'wp-webhooks' ),
                ),
            );

            $settings = array(
                'load_default_settings' => true,
                'load_supported_data' => array(
                    'wp_user' => array(
                        'args' => array(
                            'user_id' => 'user_id'
                        )
                    ),
                ),
                'data' => array(
                    'wpwhpro_um_account_approved_trigger_on_users' => array(
                        'id'			=> 'wpwhpro_um_account_approved_trigger_on_users',
                        'type'			=> 'select',
                        'multiple'		=> true,
                        'choices'		=> array(),
                        'query'			=> array(
                            'filter'	=> 'users',
                            'args'		=> array()
                        ),
                        'label'			=> __( 'Trigger on users', 'wp-webhooks' ),
                        'placeholder'	=> '',
                        'required'		=> false,
                        'description'	=> __( 'Select only the users you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', 'wp-webhooks' )
                    ),
                )
            );

            return array(
                'trigger'		  => 'um_account_approved',
                'name'			  => __( 'Account approved', 'wp-webhooks' ),
                'sentence'		  => __( 'an account was approved', 'wp-webhooks' ),
                'parameter'		  => $parameter,
                'settings'		  => $settings,
                'returns_code'	  => $this->get_demo( array() ),
                'short_description' => __( 'This webhook fires after UM account was approved.', 'wp-webhooks' ),
                'description'	   => array(),
                'integration'	  => 'ultimate-member',
            );

        }

        /**
         * Triggers once an Account approved
         *
         * @param Integer $user_id User id
         */
        public function wpwh_trigger_um_account_approved( $user_id ){
            $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'um_account_approved' );

            $payload = array(
                'user_id' => $user_id,
                'user_data' => ( ! empty( $user_id ) && is_numeric( $user_id ) ) ? get_user_by( 'id', $user_id ) : false,
                'user_meta' => ( ! empty( $user_id ) && is_numeric( $user_id ) ) ? get_user_meta( $user_id ) : false,
            );

            foreach( $webhooks as $webhook ){

                $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
                $is_valid = true;

                if( isset( $webhook['settings'] ) ){
                    foreach( $webhook['settings'] as $settings_name => $settings_data ){
                        if( $settings_name === 'wpwhpro_um_account_approved_trigger_on_users' && ! empty( $settings_data ) ){
                            if( ! in_array( $user_id, $settings_data ) ){
                                $is_valid = false;
                            }
                        }
                    }
                }
                if($is_valid){
                    if( $webhook_url_name !== null ){
                        $response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
                    } else {
                        $response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
                    }
                }
            }

            do_action( 'wpwhpro/webhooks/trigger_um_account_approved', $payload, $response_data_array );
        }

        public function get_demo( $options = array() ) {

            $data = array(
                'user_id' => 1,
                'user_data' => 
                    array (
                      'data' => 
                      array (
                        'ID' => '179',
                        'user_login' => 'user',
                        'user_pass' => '$P$BlHzF8wgUEr.uYdOERYQQeauGsq/Uh.',
                        'user_nicename' => 'user',
                        'user_email' => 'demouser@demodomain.com',
                        'user_url' => '',
                        'user_registered' => '2022-08-13 09:33:35',
                        'user_activation_key' => '1660383215:$P$BU4uz4Sne2vV.WVOimBn5ZxwUCv4Qy1',
                        'user_status' => '0',
                        'display_name' => 'user',
                        'spam' => '0',
                        'deleted' => '0',
                      ),
                      'ID' => 179,
                      'caps' => 
                      array (
                        'subscriber' => true,
                      ),
                      'cap_key' => 'wp__capabilities',
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
                        0 => '',
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
                      'um_user_profile_url_slug_user_login' => 
                      array (
                        0 => 'user',
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
                        0 => 'mydomain.test',
                      ),
                      'wp__capabilities' => 
                      array (
                        0 => 'a:1:{s:10:"subscriber";b:1;}',
                      ),
                      'wp__user_level' => 
                      array (
                        0 => '0',
                      ),
                ),
            );

            return $data;
        }

    }

endif; // End if class_exists check.