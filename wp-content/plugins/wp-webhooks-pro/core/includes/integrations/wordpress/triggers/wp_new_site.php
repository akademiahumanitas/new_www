<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Triggers_wp_new_site' ) ) :

	/**
	 * Load the wp_new_site trigger
	 *
	 * @since 5.2.4
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Triggers_wp_new_site {

        public function is_active(){
			return function_exists( 'is_multisite' ) ? is_multisite() : false;
        }

		public function get_callbacks(){

            return array(
                array(
                    'type' => 'action',
                    'hook' => 'wp_initialize_site',
                    'callback' => array( $this, 'ironikus_trigger_wp_new_site' ),
                    'priority' => 10,
                    'arguments' => 2,
                    'delayed' => true,
                ),
            );

		}

        public function get_details(){

			$parameter = array(
				'site_id'   => array( 'short_description' => __( 'The id of the newly created site.', 'wp-webhooks' ) ),
				'domain'   => array( 'short_description' => __( 'The domain of the site.', 'wp-webhooks' ) ),
				'path'   => array( 'short_description' => __( 'The path of the new site.', 'wp-webhooks' ) ),
				'network_id'   => array( 'short_description' => __( 'The ID of the network site.', 'wp-webhooks' ) ),
				'registered'   => array( 'short_description' => __( 'When the site was registered, in SQL datetime format.', 'wp-webhooks' ) ),
				'last_updated'   => array( 'short_description' => __( 'When the site was last updated, in SQL datetime format.', 'wp-webhooks' ) ),
				'public'   => array( 'short_description' => __( 'Whether the site is public.', 'wp-webhooks' ) ),
				'archived'   => array( 'short_description' => __( 'Whether the site is archieved.', 'wp-webhooks' ) ),
				'mature'   => array( 'short_description' => __( 'Whether the site is mature.', 'wp-webhooks' ) ),
				'spam'   => array( 'short_description' => __( 'Whether the site is spam.', 'wp-webhooks' ) ),
				'deleted'   => array( 'short_description' => __( 'Whether the site is deleted.', 'wp-webhooks' ) ),
				'lang_id'   => array( 'short_description' => __( 'The sites language ID.', 'wp-webhooks' ) ),
				'title'   => array( 'short_description' => __( 'The site title.', 'wp-webhooks' ) ),
				'user_id'   => array( 'short_description' => __( 'The user ID of the site administrator.', 'wp-webhooks' ) ),
				'options'   => array( 'short_description' => __( 'Further information about the site.', 'wp-webhooks' ) ),
				'meta'   => array( 'short_description' => __( 'Further data about the site.', 'wp-webhooks' ) ),
			);

			$settings = array(
				'load_default_settings' => true,
				'data' => array()
			);

            return array(
                'trigger'           => 'wp_new_site',
                'name'              => __( 'New site created', 'wp-webhooks' ),
                'sentence'              => __( 'a new site was created', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'settings'          => $settings,
                'returns_code'      => $this->get_demo( array() ),
                'short_description' => __( 'This webhook fires after a new site was created.', 'wp-webhooks' ),
                'description'       => array(), 
                'integration'       => 'wordpress',
                'premium'           => true,
            );

        }

        public function ironikus_trigger_wp_new_site( $new_site, $args ){

			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wp_new_site' );
			$data_array = array(
				'site_id' => ( isset( $new_site->blog_id ) ) ? $new_site->blog_id : 0,
				'domain' => ( isset( $new_site->domain ) ) ? $new_site->domain : '',
				'path' => ( isset( $new_site->path ) ) ? $new_site->path : '',
				'network_id' => ( isset( $new_site->site_id ) ) ? $new_site->site_id : '',
				'registered' => ( isset( $new_site->registered ) ) ? $new_site->registered : '',
				'last_updated' => ( isset( $new_site->last_updated ) ) ? $new_site->last_updated : '',
				'public' => ( isset( $new_site->public ) ) ? $new_site->public : '',
				'archived' => ( isset( $new_site->archived ) ) ? $new_site->archived : '',
				'mature' => ( isset( $new_site->mature ) ) ? $new_site->mature : '',
				'spam' => ( isset( $new_site->spam ) ) ? $new_site->spam : '',
				'deleted' => ( isset( $new_site->deleted ) ) ? $new_site->deleted : '',
				'lang_id' => ( isset( $new_site->lang_id ) ) ? $new_site->lang_id : '',
			);
			$data_array = array_merge( $data_array, $args );
			$response_data = array();

			foreach( $webhooks as $webhook ){

				$is_valid = true;

				if( $is_valid ){
					$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

					if( $webhook_url_name !== null ){
						$response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
					} else {
						$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
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

            $data = array (
				'site_id' => '5',
				'domain' => 'zipfme.test',
				'path' => '/demo3/',
				'network_id' => '1',
				'registered' => '2022-06-23 19:29:37',
				'last_updated' => '2022-06-23 19:29:37',
				'public' => '1',
				'archived' => '0',
				'mature' => '0',
				'spam' => '0',
				'deleted' => '0',
				'lang_id' => '0',
				'title' => 'Demo Site title',
				'user_id' => 1,
				'options' => 
				array (
				  'WPLANG' => '',
				),
				'meta' => 
				array (),
			);

            return $data;
        }

    }

endif; // End if class_exists check.