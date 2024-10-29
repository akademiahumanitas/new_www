<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_ultimate_member_Triggers_um_profile_photo_changed' ) ) :

    /**
     * Load the um_profile_photo_changed trigger
     *
     * @since 5.2.2
     * @author Ironikus <info@ironikus.com>
     */
    class WP_Webhooks_Integrations_ultimate_member_Triggers_um_profile_photo_changed{

        public function get_callbacks(){

            return array(
                array(
                    'type' => 'filter',
                    'hook' => 'um_upload_image_process__profile_photo',
                    'callback' => array( $this, 'wpwh_trigger_um_profile_photo_changed' ),
                    'priority' => 10,
                    'arguments' => 7,
                    'delayed' => true,
                ),
            );
        }

        public function get_details(){

            $parameter = array(
                'response' => array( 'short_description' => __( '(Array) The formatted info about image.', 'wp-webhooks' ) ),
                'image_path' => array( 'short_description' => __( '(String) The image web path.', 'wp-webhooks' ) ),
                'src' => array( 'short_description' => __( '(String) The image filepath.', 'wp-webhooks' ) ),
                'key' => array( 'short_description' => __( '(String) The image name.', 'wp-webhooks' ) ),
                'coord' => array( 'short_description' => __( '(String) The coordination about image.', 'wp-webhooks' ) ),
                'crop' => array( 'short_description' => __( '(Array) The crop resolution.', 'wp-webhooks' ) ),
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
                'data' => array(
                    'wpwhpro_um_profile_photo_changed_trigger_on_users' => array(
                        'id'			=> 'wpwhpro_um_profile_photo_changed_trigger_on_users',
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
                    )
                ),
            );

            return array(
                'trigger'		  => 'um_profile_photo_changed',
                'name'			  => __( 'Profile photo changed', 'wp-webhooks' ),
                'sentence'		  => __( 'a profile photo was changed', 'wp-webhooks' ),
                'parameter'		  => $parameter,
                'settings'		  => $settings,
                'returns_code'	  => $this->get_demo( array() ),
                'short_description' => __( 'This webhook fires after UM\'s profile photo was changed.', 'wp-webhooks' ),
                'description'	   => array(),
                'integration'	  => 'ultimate-member',
            );

        }

        /**
         * Triggers once Profile cover photo has changed
         *
         * @param array $response
         * @param string $image_path
         * @param string $src
         * @param string $key
         * @param integer $user_id
         * @param string $coord
         * @param array $crop
         *
         */
        public function wpwh_trigger_um_profile_photo_changed( $response, $image_path, $src, $key, $user_id, $coord, $crop ){
            $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'um_profile_photo_changed' );

            $payload = array(
                'response' => $response,
                'image_path' => $image_path,
                'src' => $src,
                'key' => $key,
                'coord' => $coord,
                'crop'  => $crop,
                'user_id' => $user_id,
                'user_data' => ( ! empty( $user_id ) && is_numeric( $user_id ) ) ? get_user_by( 'id', $user_id ) : false,
                'user_meta' => ( ! empty( $user_id ) && is_numeric( $user_id ) ) ? get_user_meta( $user_id ) : false,
            );


            foreach( $webhooks as $webhook ){

                $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

                $is_valid = true;

                if( isset( $webhook['settings'] ) ){
                    foreach( $webhook['settings'] as $settings_name => $settings_data ) {
                        if( $settings_name === 'wpwhpro_um_profile_profile_photo_changed_trigger_on_users' && ! empty( $settings_data ) ){
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

            do_action( 'wpwhpro/webhooks/trigger_um_profile_photo_changed', $payload, $response_data_array);
        }

        public function get_demo( $options = array() ) {
            $data = array(
                'response' => array(
                    'image' => array(
                        'source_url' => 'https://somesite/uploads/ultimatemember/1/cover_photo.jpg?165402',
                        'source_path' => 'folder\uploads\ultimatemember\1\cover_photo.jpg',
                        'file_name' => 'cover_photo.jpg',
                    )
                ),
                'image_path' => 'https://demosite.com/um/key_temp/key/src.png',
                'src' => 'https://demosite.com/uploads/ultimatemember/1/cover_photo_temp.jpg?1654022278574',
                'key' => 'cover_photo',
                'coord' => '130,58,1038,384',
                'crop'  => [130, 58, 1038, 384],
                'user_id' => 12,
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