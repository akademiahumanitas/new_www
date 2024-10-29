<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_wpforo_Triggers_wpforo_topic_created' ) ) :

	/**
	 * Load the wpforo_topic_created trigger
	 *
	 * @since 6.1.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wpforo_Triggers_wpforo_topic_created {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'wpforo_after_add_topic',
					'callback'  => array( $this, 'wpforo_topic_created_callback' ),
					'priority'  => 10,
					'arguments' => 2,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {

			$parameter = array(
				'success' => array( 'short_description' => __( '(Bool) True if the topic was successfully created.', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further details about the action.', 'wp-webhooks' ) ),
			);

			$settings = array(
				'load_default_settings' => true,
				'data' => array(
					'wpwhpro_wpforo_trigger_on_selected_forums' => array(
						'id'		  => 'wpwhpro_wpforo_trigger_on_selected_forums',
						'type'		=> 'select',
						'multiple'	=> true,
						'choices'	  => array(),
						'query'			=> array(
							'filter'	=> 'helpers',
							'args'		=> array(
								'integration' => 'wpforo',
								'helper' => 'wpforo_helpers',
								'function' => 'get_query_forums',
							)
						),
						'label'	   => __( 'Trigger on selected forums', 'wp-webhooks' ),
						'placeholder' => '',
						'required'	=> false,
						'description' => __( 'Select only the forums you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', 'wp-webhooks' )
					),
				)
			);

			return array(
				'trigger'           => 'wpforo_topic_created',
				'name'              => __( 'Topic created', 'wp-webhooks' ),
				'sentence'          => __( 'a topic was created', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires as soon as topic was created within wpForo.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'wpforo',
				'premium'           => true,
			);

		}

		/**
		 * Triggers when wpForo creates a topic
		 *
		 * @param $args Topic data
		 * @param $forum Forum data
		 */
		public function wpforo_topic_created_callback( $args, $forum ) {

			$webhooks            = WPWHPRO()->webhook->get_hooks( 'trigger', 'wpforo_topic_created' );
			$response_data_array = array();

			$forum_id = isset( $forum['forumid'] ) ? intval( $forum['forumid'] ) : 0;

			$payload = array(
				'success' => true,
				'msg'     => __( 'The topic was created.', 'wp-webhooks' ),
				'data'    => array(
					'topic_data'      => $args,
					'forum_data'      => $forum,
				),
			);

			foreach ( $webhooks as $webhook ) {

				$webhook_url_name = ( is_array( $webhook ) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
				$is_valid         = true;

				if( isset( $webhook['settings'] ) ){
					foreach( $webhook['settings'] as $settings_name => $settings_data ){
		  
					  if( $settings_name === 'wpwhpro_wpforo_trigger_on_selected_forums' && ! empty( $settings_data ) ){
						if( ! in_array( $forum_id, $settings_data ) ){
						  $is_valid = false;
						}
					  }
		  
					}
				}

				if ( $is_valid ) {
					if ( $webhook_url_name !== null ) {
						$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					} else {
						$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					}
				}
			}

			do_action( 'wpwhpro/webhooks/trigger_wpforo_topic_created', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array (
				'success' => true,
				'msg' => 'The topic was created.',
				'data' => 
				array (
				  'topic_data' => 
				  array (
					'forumid' => 2,
					'title' => 'Demo Topic 1',
					'body' => '<p>This is a demo topic</p>',
					'type' => 1,
					'private' => 1,
					'tags' => 'demo, demo1',
					'postmetas' => 
					array (
					),
					'name' => '',
					'email' => '',
					'slug' => 'demo-topic-1',
					'created' => '2022-12-28 12:06:35',
					'userid' => 1,
					'status' => 0,
					'topicid' => 2,
					'first_postid' => 2,
					'topicurl' => 'https://demodomain.test/community/main-forum/demo-topic-1/',
					'url' => 'https://demodomain.test/community/main-forum/demo-topic-1/',
				  ),
				  'forum_data' => 
				  array (
					'forumid' => '2',
					'title' => 'Main Forum',
					'slug' => 'main-forum',
					'description' => 'This is a simple parent forum',
					'parentid' => '1',
					'icon' => 'fas fa-comments',
					'cover' => 0,
					'cover_height' => '150',
					'last_topicid' => '0',
					'last_postid' => '0',
					'last_userid' => '0',
					'last_post_date' => '0000-00-00 00:00:00',
					'topics' => '0',
					'posts' => '0',
					'permissions' => 'a:5:{i:1;s:4:"full";i:2;s:9:"moderator";i:3;s:8:"standard";i:4;s:9:"read_only";i:5;s:8:"standard";}',
					'meta_key' => '',
					'meta_desc' => '',
					'status' => '1',
					'is_cat' => '0',
					'layout' => '4',
					'order' => '0',
					'color' => '#888888',
					'url' => 'https://demodomain.test/community/main-forum/',
					'cover_url' => '',
				  ),
				),
			);

			return $data;
		}

	}

endif; // End if class_exists check.
