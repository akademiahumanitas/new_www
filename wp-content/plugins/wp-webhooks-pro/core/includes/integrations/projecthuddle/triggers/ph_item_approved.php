<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_projecthuddle_Triggers_ph_item_approved' ) ) :

 /**
  * Load the ph_item_approved trigger
  *
  * @since 6.0
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_projecthuddle_Triggers_ph_item_approved {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'ph_item_approval',
				'callback' => array( $this, 'ph_item_approved_callback' ),
				'priority' => 20,
				'arguments' => 3,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'item_id' => array( 'short_description' => __( '(Integer) The id of the item.', 'wp-webhooks' ) ),
			'item_type' => array( 'short_description' => __( '(String) The item type.', 'wp-webhooks' ) ),
			'item_data' => array( 'short_description' => __( '(Array) The item data.', 'wp-webhooks' ) ),
			'item_is_new' => array( 'short_description' => __( '(Bool) Whether this item is a new item with a first approval.', 'wp-webhooks' ) ),
		);

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_projecthuddle_trigger_on_item_type' => array(
					'id'		  => 'wpwhpro_projecthuddle_trigger_on_item_type',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	=> array(
						'webpage' => array( 'label' => __( 'Web page', 'wp-webhooks' ) ),
						'image' => array( 'label' => __( 'Mockup Image', 'wp-webhooks' ) ),
					),
					'label'	   => __( 'Trigger on selected item types', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'Decide wether you want to fire this trigger on a website, mockup, or both. If you don\'t select any, all are triggered.', 'wp-webhooks' )
				),
			)
		);

		return array(
			'trigger'		   => 'ph_item_approved',
			'name'			  => __( 'Item approved', 'wp-webhooks' ),
			'sentence'			  => __( 'an item was approved', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as an item was approved within ItemHuddle.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'projecthuddle',
			'premium'		   => true,
		);

	}

	public function ph_item_approved_callback( $model, $approved, $is_new ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'ph_item_approved' );
		$response_data_array = array();

		$payload = array(
			'item_id' => ( isset( $model->ID ) ) ? $model->ID : 0,
			'item_type' => ( isset( $model->post ) && isset( $model->post->post_type ) ) ? $model->post->post_type : '',
			'item_data' => ( isset( $model->post ) ) ? $model->post : array(),
			'item_is_new' => $is_new,
		);

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){
	  
				  if( $settings_name === 'wpwhpro_projecthuddle_trigger_on_item_type' && ! empty( $settings_data ) ){
					
					$is_valid = false;
					
					if( in_array( 'webpage', $settings_data ) && $payload['item_type'] === 'ph-webpage' ){
					  $is_valid = true;
					} elseif( in_array( 'image', $settings_data ) && $payload['item_type'] === 'project_image' ){
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

		do_action( 'wpwhpro/webhooks/trigger_ph_item_approved', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'item_id' => 9355,
			'item_type' => 'ph-webpage',
			'item_data' => 
			array (
			  'ID' => 9355,
			  'post_author' => '1',
			  'post_date' => '2022-08-13 21:09:02',
			  'post_date_gmt' => '2022-08-13 21:09:02',
			  'post_content' => '',
			  'post_title' => 'The post title',
			  'post_excerpt' => '',
			  'post_status' => 'publish',
			  'comment_status' => 'closed',
			  'ping_status' => 'closed',
			  'post_password' => '',
			  'post_name' => 'the-post-title',
			  'to_ping' => '',
			  'pinged' => '',
			  'post_modified' => '2022-08-13 21:18:21',
			  'post_modified_gmt' => '2022-08-13 21:18:21',
			  'post_content_filtered' => '',
			  'post_parent' => 0,
			  'guid' => 'https://yourdomain.test/website-page/the-post-title/',
			  'menu_order' => 0,
			  'post_type' => 'ph-webpage',
			  'post_mime_type' => '',
			  'comment_count' => '0',
			  'filter' => 'raw',
			),
			'item_is_new' => false,
		);

		return $data;
	}

  }

endif; // End if class_exists check.