<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Triggers_post_update' ) ) :

	/**
	 * Load the post_update trigger
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Triggers_post_update {

        /**
         * Preserver certain values
         *
         * @var array
         * @since 2.0.5
         */
        private $pre_action_values = array();

		/**
		 * Register the actual functionality of the webhook
		 *
		 * @param mixed $response
		 * @param string $action
		 * @param string $response_ident_value
		 * @param string $response_api_key
		 * @return mixed The response data for the webhook caller
		 */
		public function get_callbacks(){

            return array(
                array(
                    'type' => 'action',
                    'hook' => 'pre_post_update',
                    'callback' => array( $this, 'ironikus_prepare_pre_post_update' ),
                    'priority' => 20,
                    'arguments' => 2,
                    'delayed' => false,
                ),
                array(
                    'type' => 'action',
                    'hook' => 'post_updated',
                    'callback' => array( $this, 'ironikus_prepare_post_update' ),
                    'priority' => 10,
                    'arguments' => 3,
                    'delayed' => false,
                ),
                array(
                    'type' => 'action',
                    'hook' => 'wp_insert_post',
                    'callback' => array( $this, 'ironikus_trigger_post_update' ),
                    'priority' => 10,
                    'arguments' => 3,
                    'delayed' => true,
                ),
                array(
                    'type' => 'action',
                    'hook' => 'attachment_updated',
                    'callback' => array( $this, 'ironikus_trigger_post_update_preserve_attachment_init' ),
                    'priority' => 10,
                    'arguments' => 3,
                    'delayed' => false,
                ),
                array(
                    'type' => 'action',
                    'hook' => 'attachment_updated',
                    'callback' => array( $this, 'ironikus_trigger_post_update_attachment_init' ),
                    'priority' => 10,
                    'arguments' => 3,
                    'delayed' => true,
                ),
            );

		}

        /*
        * Register the post update trigger as an element
        *
        * @since 1.2
        */
        public function get_details(){

            $parameter = array(
                'post_id'   => array( 'short_description' => __( 'The post id of the updated post.', 'wp-webhooks' ) ),
                'post'      => array( 'short_description' => __( 'The whole post object with all of its values', 'wp-webhooks' ) ),
                'post_meta' => array( 'short_description' => __( 'An array of the whole post meta data.', 'wp-webhooks' ) ),
                'post_before' => array( 'short_description' => __( 'The post data before the update.', 'wp-webhooks' ) ),
                'post_permalink_before' => array( 'short_description' => __( 'The post permalink before the update.', 'wp-webhooks' ) ),
                'post_meta_before' => array( 'short_description' => __( 'The post meta data before the update.', 'wp-webhooks' ) ),
                'post_thumbnail' => array( 'short_description' => __( 'The full featured image/thumbnail URL in the full size.', 'wp-webhooks' ) ),
                'post_permalink' => array( 'short_description' => __( 'The permalink of the currently given post.', 'wp-webhooks' ) ),
                'taxonomies' => array( 'short_description' => __( '(Array) An array containing the taxonomy data of the assigned taxonomies. Custom Taxonomies are supported too.', 'wp-webhooks' ) ),
            );

            if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
                $parameter['acf_data'] = array( 'short_description' => __( 'The Advanced Custom Fields post meta is also pushed to the post object. You will find it on the first layer of the object as well. ', 'wp-webhooks' ) );
                $parameter['acf_data_before'] = array( 'short_description' => __( 'The Advanced Custom Fields data before the update.', 'wp-webhooks' ) );
            }

            $settings = array(
                'load_default_settings' => true,
                'data' => array(
                    'wpwhpro_post_update_trigger_on_post_type' => array(
                        'id'          => 'wpwhpro_post_update_trigger_on_post_type',
                        'type'        => 'select',
                        'multiple'    => true,
                        'choices'      => array(),
                        'query'			=> array(
							'filter'	=> 'post_types',
							'args'		=> array()
						),
                        'label'       => __( 'Trigger on selected post types', 'wp-webhooks' ),
                        'placeholder' => '',
                        'required'    => false,
                        'description' => __( 'Select only the post types you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', 'wp-webhooks' )
                    ),
                    'wpwhpro_post_update_trigger_on_specific_status' => array(
                        'id'          => 'wpwhpro_post_update_trigger_on_specific_status',
                        'type'        => 'text',
                        'label'       => __( 'Trigger on post status', 'wp-webhooks' ),
                        'placeholder' => '',
                        'required'    => false,
                        'description' => __( 'Fires as long as the post has one of your chosen post statuses. In case you want to add multiple once, please comma-separate them (e.g.: publish,draft). If none are set, all are triggered.', 'wp-webhooks' )
                    ),
                    'wpwhpro_post_update_trigger_on_post_status' => array(
                        'id'          => 'wpwhpro_post_update_trigger_on_post_status',
                        'type'        => 'text',
                        'label'       => __( 'Trigger on post status change', 'wp-webhooks' ),
                        'placeholder' => '',
                        'required'    => false,
                        'description' => __( 'Fires once a post status changed to one of your chosen ones. Define specifc post statuses that you want to fire the trigger on. In case you want to add multiple once, please comma-separate them (e.g.: publish,draft). If none are set, all are triggered.', 'wp-webhooks' )
                    ),
                    'wpwhpro_post_update_trigger_on_post_ids' => array(
                        'id'          => 'wpwhpro_post_update_trigger_on_post_ids',
                        'type'        => 'text',
                        'label'       => __( 'Trigger on specific post IDs', 'wp-webhooks' ),
                        'placeholder' => '',
                        'required'    => false,
                        'description' => __( 'Fires on the specified post IDs only. In case you want to add multiple once, please comma-separate them (e.g.: 123,456). If none are set, all are triggered.', 'wp-webhooks' )
                    ),
                )
            );

            return array(
                'trigger'           => 'post_update',
                'name'              => __( 'Post updated', 'wp-webhooks' ),
                'sentence'              => __( 'a post was updated', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'settings'          => $settings,
                'returns_code'      => $this->get_demo( array() ),
                'short_description' => __( 'This webhook fires after an existing post is updated.', 'wp-webhooks' ),
                'description'       => array(),
                'callback'          => 'test_post_create',
                'integration'       => 'wordpress',
            );

        }

        /*
        * Preserve the post meta on update_post
        *
        * @since 3.2.6
        */
        public function ironikus_prepare_pre_post_update( $post_ID, $data ){
            $this->pre_action_values['update_post_meta_before'] = get_post_meta( $post_ID );
            $this->pre_action_values['update_post_permalink_before'] = get_permalink( $post_ID );

            if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
                $this->pre_action_values['update_post_acf_meta_before'] = get_fields( $post_ID );
            }
            
        }

        /*
        * Preserve the post_before on update_post
        *
        * @since 2.0.5
        */
        public function ironikus_prepare_post_update( $post_ID, $post_after, $post_before ){
            $this->pre_action_values['update_post_post_before'] = $post_before;
        }

        /*
        * Add attachment logic to default post_update functionality
        *
        * @see https://github.com/Ironikus/wp-webhooks/issues/2
        * @since 2.1.8
        */
        public function ironikus_trigger_post_update_preserve_attachment_init( $post_ID, $post_after, $post_before ){
            $this->pre_action_values['update_post_post_before'] = $post_before;
        }

        /*
        * Re-format the variables to the main plugin notation
        */
        public function ironikus_trigger_post_update_attachment_init( $post_ID, $post_after, $post_before ){
            $this->ironikus_trigger_post_update( $post_ID, $post_after, true );
        }

        /*
        * Register the register post trigger logic
        *
        * @since 1.2
        */
        public function ironikus_trigger_post_update( $post_id, $post, $update ){

            if( $update ){

                $tax_output = array();
                $taxonomies = get_taxonomies( array(),'names' );
                if( ! empty( $taxonomies ) ){
                    $tax_terms = wp_get_post_terms( $post_id, $taxonomies );
                    foreach( $tax_terms as $sk => $sv ){

                        if( ! isset( $sv->taxonomy ) || ! isset( $sv->slug ) ){
                            continue;
                        }

                        if( ! isset( $tax_output[ $sv->taxonomy ] ) ){
                            $tax_output[ $sv->taxonomy ] = array();
                        }

                        if( ! isset( $tax_output[ $sv->taxonomy ][ $sv->slug ] ) ){
                            $tax_output[ $sv->taxonomy ][ $sv->slug ] = array();
                        }

                        $tax_output[ $sv->taxonomy ][ $sv->slug ] = $sv;

                    }
                }

                $post_before = isset( $this->pre_action_values['update_post_post_before'] ) ? $this->pre_action_values['update_post_post_before'] : false;
                $meta_before = isset( $this->pre_action_values['update_post_meta_before'] ) ? $this->pre_action_values['update_post_meta_before'] : false;
                $permalink_before = isset( $this->pre_action_values['update_post_permalink_before'] ) ? $this->pre_action_values['update_post_permalink_before'] : false;

                $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'post_update' );
                $data_array = array(
                    'post_id'   => $post_id,
                    'post'      => $post,
                    'post_meta' => get_post_meta( $post_id ),
                    'post_before' => $post_before,
                    'post_permalink_before' => $permalink_before,
                    'post_meta_before' => $meta_before,
                    'post_thumbnail' => get_the_post_thumbnail_url( $post_id,'full' ),
                    'post_permalink' => get_permalink( $post_id ),
                    'taxonomies'=> $tax_output
                );
                $response_data = array();

                if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
                    $data_array['acf_data'] = get_fields( $post_id );
                    $data_array['acf_data_before'] = isset( $this->pre_action_values['update_post_acf_meta_before'] ) ? $this->pre_action_values['update_post_acf_meta_before'] : false;
                }

                foreach( $webhooks as $webhook ){

                    $is_valid = true;

                    if( isset( $webhook['settings'] ) ){
                        foreach( $webhook['settings'] as $settings_name => $settings_data ){

                            if( $settings_name === 'wpwhpro_post_update_trigger_on_post_type' && ! empty( $settings_data ) ){
                                if( ! in_array( $post->post_type, $settings_data ) ){
                                    $is_valid = false;
                                }
                            }

                            if( $settings_name === 'wpwhpro_post_update_trigger_on_specific_status' && ! empty( $settings_data ) ){
                                
                                $allowed_statuses = explode( ',', $settings_data );
                                if( is_array( $allowed_statuses ) && is_object( $post ) ){

                                    if( ! in_array( $post->post_status, $allowed_statuses ) ){
                                        $is_valid = false;
                                    }
                                    
                                }
                                
                            }

                            if( $settings_name === 'wpwhpro_post_update_trigger_on_post_status' && ! empty( $settings_data ) ){
                                
                                $allowed_statuses = explode( ',', $settings_data );
                                if( is_array( $allowed_statuses ) && is_object( $post_before ) ){

                                    if( $post_before->post_status === $post->post_status || ! in_array( $post->post_status, $allowed_statuses ) ){
                                        $is_valid = false;
                                    }
                                    
                                }
                                
                            }

                            if( $settings_name === 'wpwhpro_post_update_trigger_on_post_ids' && ! empty( $settings_data ) ){
                                
                                $allowed_ids = explode( ',', $settings_data );
                                if( is_array( $allowed_ids ) ){

                                    if( ! in_array( $post->ID, $allowed_ids ) ){
                                        $is_valid = false;
                                    }
                                    
                                }
                                
                            }

                        }
                    }

                    if( $is_valid ){
                        $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

                        if( $webhook_url_name !== null ){
                            $response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
                        } else {
                            $response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
                        }
                    }
                }

                do_action( 'wpwhpro/webhooks/trigger_post_update', $post_id, $post, $response_data );
            }
        }

        /**
         * Register the demo post create trigger callback
         *
         * @since 1.2
         *
         * @param $data - The default data
         * @param $webhook - The current webhook
         * @param $webhook_group - The current webhook group (trigger-)
         *
         * @return array
         */
        public function get_demo( $options = array() ) {

            $data = array(
                    'post_id' => 1234,
                    'post' => array (
                        'ID' => 1,
                        'post_author' => '1',
                        'post_date' => '2022-11-06 14:19:18',
                        'post_date_gmt' => '2022-11-06 14:19:18',
                        'post_content' => 'Welcome to WordPress. This is your first post. Edit or delete it, then start writing!',
                        'post_title' => 'Hello world!',
                        'post_excerpt' => '',
                        'post_status' => 'publish',
                        'comment_status' => 'open',
                        'ping_status' => 'open',
                        'post_password' => '',
                        'post_name' => 'hello-world',
                        'to_ping' => '',
                        'pinged' => '',
                        'post_modified' => '2022-11-06 14:19:18',
                        'post_modified_gmt' => '2022-11-06 14:19:18',
                        'post_content_filtered' => '',
                        'post_parent' => 0,
                        'guid' => 'https://mydomain.test/?p=1',
                        'menu_order' => 0,
                        'post_type' => 'post',
                        'post_mime_type' => '',
                        'comment_count' => '1',
                        'filter' => 'raw',
                    ),
                    'post_meta' => array (
                        'key_0' =>
                            array (
                                0 => '1.00',
                            ),
                        'key_1' =>
                            array (
                                0 => '0',
                            ),
                        'key_2' =>
                            array (
                                0 => '1',
                            ),
                        'key_3' =>
                            array (
                                0 => '148724528:1',
                            ),
                        'key_4' =>
                            array (
                                0 => '10.00',
                            ),
                        'key_5' =>
                            array (
                                0 => 'a:0:{}',
                            ),
                    ),
                    'post_before' => array (
                        'ID' => 1,
                        'post_author' => '1',
                        'post_date' => '2022-11-06 14:19:18',
                        'post_date_gmt' => '2022-11-06 14:19:18',
                        'post_content' => 'Welcome to WordPress. This is your first post. Edit or delete it, then start writing!',
                        'post_title' => 'Hello world!',
                        'post_excerpt' => '',
                        'post_status' => 'draft',
                        'comment_status' => 'open',
                        'ping_status' => 'open',
                        'post_password' => '',
                        'post_name' => 'hello-world',
                        'to_ping' => '',
                        'pinged' => '',
                        'post_modified' => '2022-11-06 14:19:18',
                        'post_modified_gmt' => '2022-11-06 14:19:18',
                        'post_content_filtered' => '',
                        'post_parent' => 0,
                        'guid' => 'https://mydomain.test/?p=1',
                        'menu_order' => 0,
                        'post_type' => 'post',
                        'post_mime_type' => '',
                        'comment_count' => '1',
                        'filter' => 'raw',
                    ),
                    'post_permalink_before' => 'https://mydomain.test/?p=1',
                    'post_meta_before' => array (
                        'key_0' =>
                            array (
                                0 => '0.00',
                            ),
                        'key_1' =>
                            array (
                                0 => '0',
                            ),
                        'key_2' =>
                            array (
                                0 => '1',
                            ),
                        'key_3' =>
                            array (
                                0 => '148724528:1',
                            ),
                        'key_4' =>
                            array (
                                0 => '10.00',
                            ),
                        'key_5' =>
                            array (
                                0 => 'a:0:{}',
                            ),
                    ),
                    'post_thumbnail' => 'https://mydomain.test/images/image.jpg',
                    'post_permalink' => 'https://mydomain.test/the-post/permalink',
                    'taxonomies' => array (
                        'category' =>
                        array (
                        'uncategorized' =>
                        array (
                            'term_id' => 1,
                            'name' => 'Uncategorized',
                            'slug' => 'uncategorized',
                            'term_group' => 0,
                            'term_taxonomy_id' => 1,
                            'taxonomy' => 'category',
                            'description' => '',
                            'parent' => 10,
                            'count' => 7,
                            'filter' => 'raw',
                        ),
                        'secondcat' =>
                        array (
                            'term_id' => 2,
                            'name' => 'Second Cat',
                            'slug' => 'secondcat',
                            'term_group' => 0,
                            'term_taxonomy_id' => 2,
                            'taxonomy' => 'category',
                            'description' => '',
                            'parent' => 1,
                            'count' => 1,
                            'filter' => 'raw',
                        ),
                        ),
                    ),
            );

            if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
                $data['acf_data'] = array(
                    'demo_repeater_field' => array(
                        array(
                            'demo_field_1' => 'Demo Value 1',
                            'demo_field_2' => 'Demo Value 2',
                        ),
                        array(
                            'demo_field_1' => 'Demo Value 1',
                            'demo_field_2' => 'Demo Value 2',
                        ),
                    ),
                    'demo_text_field' => 'Some demo text',
                    'demo_true_false' => true,
                );
                $data['acf_data_before'] = array(
                    'demo_repeater_field' => array(
                        array(
                            'demo_field_1' => 'Demo Value 1',
                            'demo_field_2' => 'Demo Value 2',
                        ),
                        array(
                            'demo_field_1' => 'Demo Value 1',
                            'demo_field_2' => 'Demo Value 2',
                        ),
                    ),
                    'demo_text_field' => 'Some demo text',
                    'demo_true_false' => true,
                );
            }

            return $data;
        }

    }

endif; // End if class_exists check.