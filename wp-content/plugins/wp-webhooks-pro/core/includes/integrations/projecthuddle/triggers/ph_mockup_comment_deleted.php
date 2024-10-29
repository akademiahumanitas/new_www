<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_projecthuddle_Triggers_ph_mockup_comment_deleted' ) ) :

 /**
  * Load the ph_mockup_comment_deleted trigger
  *
  * @since 6.0
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_projecthuddle_Triggers_ph_mockup_comment_deleted {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'ph_mockup_delete_comment',
				'callback' => array( $this, 'ph_mockup_comment_deleted_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'comment_id' => array( 'short_description' => __( '(Integer) The id of the comment.', 'wp-webhooks' ) ),
			'project_id' => array( 'short_description' => __( '(Integer) The id of the project.', 'wp-webhooks' ) ),
			'mockup_id' => array( 'short_description' => __( '(Integer) The id of the connected mockup.', 'wp-webhooks' ) ),
			'comment_data' => array( 'short_description' => __( '(Array) The comment data.', 'wp-webhooks' ) ),
			'comment_meta' => array( 'short_description' => __( '(Array) Further details about the comment.', 'wp-webhooks' ) ),
			'mockup_url' => array( 'short_description' => __( '(String) The URL of the connected mockup.', 'wp-webhooks' ) ),
			'mockup_data' => array( 'short_description' => __( '(Array) The details about the connected mockup.', 'wp-webhooks' ) ),
			'mockup_meta' => array( 'short_description' => __( '(Array) Further details about the connected mockup.', 'wp-webhooks' ) ),
		);

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_projecthuddle_trigger_on_approved_comments' => array(
					'id'		  => 'wpwhpro_projecthuddle_trigger_on_approved_comments',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	=> array(
						'approved' => array( 'label' => __( 'Approved', 'wp-webhooks' ) ),
						'unapproved' => array( 'label' => __( 'Unapproved', 'wp-webhooks' ) ),
					),
					'label'	   => __( 'Trigger on selected comment status', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'Decide wether you want to fire this trigger is a comment is approved, unapprove, or both. If you don\'t add any, all are triggered.', 'wp-webhooks' )
				),
				'wpwhpro_projecthuddle_trigger_on_comment_status' => array(
					'id'		  => 'wpwhpro_projecthuddle_trigger_on_comment_status',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	=> array(
						'active' => array( 'label' => __( 'Active', 'wp-webhooks' ) ),
						'in_progress' => array( 'label' => __( 'In Progress', 'wp-webhooks' ) ),
						'in_review' => array( 'label' => __( 'In Review', 'wp-webhooks' ) ),
						'resolved' => array( 'label' => __( 'Resolved', 'wp-webhooks' ) ),
					),
					'label'	   => __( 'Trigger on comment status', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'Decide wether you want to fire this trigger is on a specific comment status. If you don\'t add any, all are triggered.', 'wp-webhooks' )
				),
				'wpwhpro_projecthuddle_trigger_on_mockups' => array(
					'id'			=> 'wpwhpro_projecthuddle_trigger_on_mockups',
					'type'			=> 'select',
					'multiple'		=> true,
 					'choices'		=> array(),
 					'query'			=> array(
						 'filter'	=> 'posts',
						 'args'		=> array(
							 'post_type' => 'ph-project'
						 )
					 ),
					'label'			=> __( 'Trigger on mockups', 'wp-webhooks' ),
					'placeholder'	=> '',
					'required'		=> false,
					'description'	=> __( 'Fire this trigger on specific mockups only. If none are selected, all are triggered.', 'wp-webhooks' )
				),
			)
		);

		return array(
			'trigger'		   => 'ph_mockup_comment_deleted',
			'name'			  => __( 'Mockup comment deleted', 'wp-webhooks' ),
			'sentence'			  => __( 'a mockup comment was deleted', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a mockup comment was deleted within ProjectHuddle.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'projecthuddle',
			'premium'		   => true,
		);

	}

	public function ph_mockup_comment_deleted_callback( $id, $comment ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'ph_mockup_comment_deleted' );
		$response_data_array = array();
		$comment_meta = get_comment_meta( $id );
		$project_id = ( is_array( $comment_meta ) && ! empty( $comment_meta ) && isset( $comment_meta['project_id'] ) ) ? intval( $comment_meta['project_id'][0] ) : 0;
		$page_id = ( is_object( $comment ) && isset( $comment->comment_post_ID ) ) ? intval( $comment->comment_post_ID ) : 0;

		$payload = array(
			'comment_id' => $id,
			'project_id' => $project_id,
			'mockup_id' => $page_id,
			'comment_data' => $comment,
			'comment_meta' => $comment_meta,
			'mockup_url' => get_permalink( $page_id ),
			'mockup_data' => get_post( $page_id ),
			'mockup_meta' => get_post_meta( $page_id ),
		);

		$comment_status = ( is_array( $payload['comment_meta'] ) && ! empty( $payload['comment_meta'] ) && isset( $payload['comment_meta']['is_status'] ) ) ? $payload['comment_meta']['is_status'][0] : false;

		//maybe unset html for performance reasons
		if( is_array( $payload['mockup_meta'] ) && ! empty( $payload['mockup_meta'] ) && isset( $payload['mockup_meta']['html'] ) ){
			unset( $payload['mockup_meta']['html'] );
		}

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){
	  
				  if( $settings_name === 'wpwhpro_projecthuddle_trigger_on_approved_comments' && ! empty( $settings_data ) ){
					$is_valid = false;
					
					if( in_array( 'approved', $settings_data ) && (int) $comment->comment_approved === 1 ){
					  $is_valid = true;
					} elseif ( in_array( 'unapproved', $settings_data ) && (int) $comment->comment_approved === 0 ){
						$is_valid = true;
					}
				  }

				  if( $is_valid && $settings_name === 'wpwhpro_projecthuddle_trigger_on_comment_status' && ! empty( $settings_data ) ){
					$is_valid = false;
					
					if( in_array( $comment_status, $settings_data ) ){
					  $is_valid = true;
					}
				  }

				  if( $is_valid && $settings_name === 'wpwhpro_projecthuddle_trigger_on_mockups' && ! empty( $settings_data ) ){
					$is_valid = false;

					if( in_array( $project_id, $settings_data ) ){
					  $is_valid = true;
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

		do_action( 'wpwhpro/webhooks/trigger_ph_mockup_comment_deleted', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'comment_id' => 444,
			'project_id' => 9350,
			'mockup_id' => 9371,
			'comment_data' => 
			array (
			  'comment_ID' => '444',
			  'comment_post_ID' => '9350',
			  'comment_author' => 'admin',
			  'comment_author_email' => 'youremail@yourdomain.test',
			  'comment_author_url' => '',
			  'comment_author_IP' => '127.0.0.1',
			  'comment_date' => '2022-08-13 13:54:30',
			  'comment_date_gmt' => '2022-08-13 13:54:30',
			  'comment_content' => '<p>This is a demo comment</p>',
			  'comment_karma' => '0',
			  'comment_approved' => '1',
			  'comment_agent' => '',
			  'comment_type' => 'ph_comment',
			  'comment_parent' => '0',
			  'user_id' => '1',
			),
			'comment_meta' => 
			array (
				'item_id' => 
				array (
				0 => '9342',
				),
				'project_id' => 
				array (
				0 => '9340',
				),
				'is_status' => 
				array (
				0 => 'active',
				),
				'_wp_trash_meta_status' => 
				array (
				0 => '1',
				),
				'_wp_trash_meta_time' => 
				array (
				0 => '1660420149',
				),
			),
			'mockup_url' => 'https://yourdomain.test/the-page-slug/',
			'mockup_data' =>
			array (
				'ID' => 9371,
				'post_author' => '1',
				'post_date' => '2022-08-24 22:19:14',
				'post_date_gmt' => '2022-08-24 22:19:14',
				'post_content' => '',
				'post_title' => 'Test Comment',
				'post_excerpt' => '',
				'post_status' => 'publish',
				'comment_status' => 'open',
				'ping_status' => 'closed',
				'post_password' => '',
				'post_name' => 'test-comment',
				'to_ping' => '',
				'pinged' => '',
				'post_modified' => '2022-08-24 22:19:14',
				'post_modified_gmt' => '2022-08-24 22:19:14',
				'post_content_filtered' => '',
				'post_parent' => 0,
				'guid' => 'https://yourdomain.test/website-thread/9371/',
				'menu_order' => 0,
				'post_type' => 'phw_comment_loc',
				'post_mime_type' => '',
				'comment_count' => '1',
				'filter' => 'raw',
			),
			'mockup_meta' => 
			array (
				'parent_id' => 
				array (
				0 => '9342',
				),
				'project_id' => 
				array (
				0 => '9340',
				),
				'item_id' => 
				array (
				0 => '9342',
				),
				'screenshot' => 
				array (
				0 => 'https://yourdomain.test/wp-content/uploads/ph-screenshots/screenshot_9371.jpg',
				),
				'path' => 
				array (
				0 => 'div#page-container > div#et-main-area > div#main-content > div.container > div.clearfix#content-area > div#left-area > article.et_pb_post.post-7917.post.type-post.status-publish.format-standard.hentry.category-uncategorized.pmpro-has-access#post-7917:nth-of-type(4)',
				),
				'xPath' => 
				array (
				0 => '/div[1]/div[1]/div[1]/div[1]/div[1]/div[1]/article[4]',
				),
				'relativeX' => 
				array (
				0 => '0.79433962264151',
				),
				'relativeY' => 
				array (
				0 => '0.64348958333333',
				),
				'pageX' => 
				array (
				0 => '0.54861111111111',
				),
				'pageY' => 
				array (
				0 => '0.36045729657028',
				),
				'html' => 
				array (
				0 => '<article id="post-7917" class="et_pb_post post-7917 post type-post status-publish format-standard hentry category-uncategorized pmpro-has-access">

							
																		<h2 class="entry-title"><a href="https://yourdomain.test/blog/2021/12/31/a-demo-title-6/">A demo title</a></h2>
								
								The content of the post, including all HTML				
								</article>',
				),
				'screenPosition' => 
				array (
				0 => 'a:2:{i:0;d:0;i:1;d:0.041758241758241756;}',
				),
				'resX' => 
				array (
				0 => '1728',
				),
				'resY' => 
				array (
				0 => '910',
				),
				'resolved' => 
				array (
				0 => '',
				),
				'browser' => 
				array (
				0 => 'Chrome',
				),
				'browserVersion' => 
				array (
				0 => '99',
				),
				'browserOS' => 
				array (
				0 => 'OS X 10.15.7 64-bit',
				),
				'page_url' => 
				array (
				0 => 'https://yourdomain.test/',
				),
				'page_title' => 
				array (
				0 => 'yourdomain',
				),
				'website_id' => 
				array (
				0 => '9340',
				),
				'assigned' => 
				array (
				0 => '0',
				),
				'is_status' => 
				array (
				0 => 'active',
				),
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.