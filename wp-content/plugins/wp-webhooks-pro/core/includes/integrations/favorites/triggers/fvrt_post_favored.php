<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_favorites_Triggers_fvrt_post_favored' ) ) :

    /**
     * Load the fvrt_post_favored trigger
     *
     * @since 5.1.2
     * @author Ironikus <info@ironikus.com>
     */
    class WP_Webhooks_Integrations_favorites_Triggers_fvrt_post_favored {

        public function get_callbacks(){

            return array(
                array(
                    'type' => 'action',
                    'hook' => 'favorites_after_favorite',
                    'callback' => array( $this, 'wpwh_trigger_fvrt_post_favored' ),
                    'priority' => 10,
                    'arguments' => 4,
                    'delayed' => true,
                ),
            );
        }

        public function get_details(){

            $parameter = array(
                'user_id' => array( 'short_description' => __( '(Integer) The user id.', 'wp-webhooks' ) ),
                'post_id' => array( 'short_description' => __( '(Integer) The post id.', 'wp-webhooks' ) ),
                'site_id' => array( 'short_description' => __( '(Integer) The site id.', 'wp-webhooks' ) ),
            );

            $settings = array(
                'load_default_settings' => true,
                'data' => array(
                    'wpwhpro_fvrt_users_post_favorite_trigger_on_users' => array(
                        'id'			=> 'wpwhpro_fvrt_users_post_favorite_trigger_on_users',
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
                'trigger'		  => 'fvrt_post_favored',
                'name'			  => __( 'Post favored', 'wp-webhooks' ),
                'sentence'		  => __( 'a post got favored', 'wp-webhooks' ),
                'parameter'		  => $parameter,
                'settings'		  => $settings,
                'returns_code'	  => $this->get_demo( array() ),
                'short_description' => __( 'This webhook fires once a post was favored.', 'wp-webhooks' ),
                'description'	   => array(),
                'integration'	  => 'favorites',
            );

        }

        /**
         * Triggers once a Post got favorite
         *
         * @param  integer $user_id User ID
         * @param  integer $post_id Post ID
         * @param  integer $site_id Site ID
         * @param  string $status Favorites status
         */
        public function wpwh_trigger_fvrt_post_favored( $post_id, $status, $site_id, $user_id ){
            $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'fvrt_post_favored' );

            // Only active favorites
            if( $status !== 'active' ) {
                return;
            }

            $payload = array(
                'user_id' => $user_id,
                'post_id' => $post_id,
                'site_id' => $site_id
            );

            foreach( $webhooks as $webhook ){

                $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
                $is_valid = true;

                if( isset( $webhook['settings'] ) ){
                    foreach( $webhook['settings'] as $settings_name => $settings_data ){

                        if( $settings_name === 'wpwhpro_fvrt_users_post_favorite_trigger_on_users' && ! empty( $settings_data ) ){
                        if( ! in_array( $user_id, $settings_data ) ){
                            $is_valid = false;
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

            do_action( 'wpwhpro/webhooks/trigger_fvrt_post_got_favorite', $post_id, $status, $site_id, $user_id, $response_data_array );
        }

        public function get_demo( $options = array() ) {

            $data = array(
                'user_id' => 1,
                'post_id' => 1,
                'site_id' => 1
            );

            return $data;
        }

    }

endif; // End if class_exists check.