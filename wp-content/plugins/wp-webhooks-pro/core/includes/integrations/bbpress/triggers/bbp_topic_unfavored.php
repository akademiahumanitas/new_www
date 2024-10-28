<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_bbpress_Triggers_bbp_topic_unfavored' ) ) :

 /**
  * Load the bbp_topic_unfavored trigger
  *
  * @since 5.1
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_bbpress_Triggers_bbp_topic_unfavored {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'bbp_remove_user_favorite',
				'callback' => array( $this, 'bbp_topic_unfavored_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'user_id' => array( 'short_description' => __( '(Integer) The ID of the user who unfavored the topic.', 'wp-webhooks' ) ),
			'topic_id' => array( 'short_description' => __( '(Integer) The ID of the topic.', 'wp-webhooks' ) ),
			'forum_id' => array( 'short_description' => __( '(Integer) The ID of the forum related to the topic.', 'wp-webhooks' ) ),
			'topic_data' => array( 'short_description' => __( '(Array) Further data about the topic.', 'wp-webhooks' ) ),
		);

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_bbp_topic_unfavored_trigger_on_forums' => array(
					'id'	 => 'wpwhpro_bbp_topic_unfavored_trigger_on_forums',
					'type'	=> 'select',
					'multiple'  => true,
					'choices'   => array(),
					'query'			=> array(
						'filter'	=> 'posts',
						'args'		=> array(
							'post_type' => ( function_exists( 'bbp_get_forum_post_type' ) ) ? bbp_get_forum_post_type() : 'forum',
						)
					),
					'label'	=> __( 'Trigger on selected forums', 'wp-webhooks' ),
					'placeholder' => '',
					'required'  => false,
					'description' => __( 'Select only the forums you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', 'wp-webhooks' )
				),
			)
		);

		return array(
			'trigger'		   => 'bbp_topic_unfavored',
			'name'			  => __( 'Topic unfavored', 'wp-webhooks' ),
			'sentence'			  => __( 'a topic was unfavored', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a topic was unfavored within bbPress.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'bbpress',
			'premium'		   => false,
		);

	}

	public function bbp_topic_unfavored_callback( $user_id, $topic_id ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'bbp_topic_unfavored' );
		$response_data_array = array();
		$forum_id = bbp_get_topic_forum_id( $topic_id );

		$payload = array(
			'user_id' => $user_id,
			'topic_id' => $topic_id,
			'forum_id' => $forum_id,
			'topic_data' => array(
				'topic_title' => bbp_get_topic_title( $topic_id ),
				'topic_permalink' => bbp_get_topic_permalink( $topic_id ),
				'topic_status' => bbp_get_topic_status( $topic_id ),
				'topic_short_description' => bbp_get_topic_excerpt( $topic_id ),
				'topic_content' => bbp_get_topic_content( $topic_id ),
				'topic_created_date' => bbp_get_topic_post_date( $topic_id ),
				'topic_is_sticky' => bbp_is_topic_sticky( $topic_id ),
				'topic_is_super_sticky' => bbp_is_topic_super_sticky( $topic_id ),
				'topic_is_anonymous' => bbp_is_topic_anonymous( $topic_id ),
				'topic_author_display_name' => bbp_get_topic_author_display_name( $topic_id ),
				'topic_author_name' => bbp_get_topic_author( $topic_id ),
				'topic_author_id' => bbp_get_topic_author_id( $topic_id ),
				'topic_author_email' => bbp_get_topic_author_email( $topic_id ),
				'topic_author_role' => bbp_get_topic_author_role( $topic_id ),
				'topic_author_avatar' => bbp_get_topic_author_avatar( $topic_id ),
				'topic_author_url' => bbp_get_topic_author_url( $topic_id ),
				'topic_forum_title' => bbp_get_topic_forum_title( $topic_id ),
				'topic_forum_id' => bbp_get_topic_forum_id( $topic_id ),
				'topic_last_active_id' => bbp_get_topic_last_active_id( $topic_id ),
				'topic_last_reply_id' => bbp_get_topic_last_reply_id( $topic_id ),
				'topic_last_reply_title' => bbp_get_topic_last_reply_title( $topic_id ),
				'topic_last_reply_permalink' => bbp_get_topic_last_reply_permalink( $topic_id ),
				'topic_reply_count' => bbp_get_topic_reply_count( $topic_id ),
			),
		);

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){

					if( $settings_name === 'wpwhpro_bbp_topic_unfavored_trigger_on_forums' && ! empty( $settings_data ) ){
						if( ! in_array( $forum_id, $settings_data ) ){
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

		do_action( 'wpwhpro/webhooks/trigger_bbp_topic_unfavored', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'user_id' => 1,
			'topic_id' => 9254,
			'forum_id' => 9247,
			'topic_data' => 
			array (
			  'topic_title' => 'test',
			  'topic_permalink' => 'https://yourdomain.test/forums/topic/test/',
			  'topic_status' => 'publish',
			  'topic_short_description' => 'test',
			  'topic_content' => '<p>test</p>
		  ',
			  'topic_created_date' => 'April 15, 2022 at 6:58 am',
			  'topic_is_sticky' => false,
			  'topic_is_super_sticky' => false,
			  'topic_is_anonymous' => false,
			  'topic_author_display_name' => 'admin',
			  'topic_author_name' => 'admin',
			  'topic_author_id' => 1,
			  'topic_author_email' => 'admin@yourdomain.test',
			  'topic_author_role' => '<div class="bbp-author-role">Keymaster</div>',
			  'topic_author_avatar' => '<img alt=\'\' src=\'https://secure.gravatar.com/avatar/xxxxxxxxxxxxxx?s=40&#038;d=mm&#038;r=g\' srcset=\'https://secure.gravatar.com/avatar/xxxxxxxxxxxxxx?s=80&#038;d=mm&#038;r=g 2x\' class=\'avatar avatar-40 photo\' height=\'40\' width=\'40\' loading=\'lazy\'/>',
			  'topic_author_url' => 'https://yourdomain.test/forums/users/admin/',
			  'topic_forum_title' => 'Demo Forum 8',
			  'topic_forum_id' => 9247,
			  'topic_last_active_id' => 9254,
			  'topic_last_reply_id' => 0,
			  'topic_last_reply_title' => 'test',
			  'topic_last_reply_permalink' => 'https://yourdomain.test/forums/topic/test/',
			  'topic_reply_count' => '0',
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.