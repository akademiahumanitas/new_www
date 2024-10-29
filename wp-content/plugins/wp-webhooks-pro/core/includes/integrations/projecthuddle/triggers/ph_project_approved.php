<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_projecthuddle_Triggers_ph_project_approved' ) ) :

 /**
  * Load the ph_project_approved trigger
  *
  * @since 6.0
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_projecthuddle_Triggers_ph_project_approved {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'ph_project_approval',
				'callback' => array( $this, 'ph_project_approved_callback' ),
				'priority' => 20,
				'arguments' => 3,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'project_id' => array( 'short_description' => __( '(Integer) The id of the project.', 'wp-webhooks' ) ),
			'project_type' => array( 'short_description' => __( '(String) The project type.', 'wp-webhooks' ) ),
			'project_data' => array( 'short_description' => __( '(Array) The project data.', 'wp-webhooks' ) ),
			'project_is_new' => array( 'short_description' => __( '(Bool) Whether this project is a new project with a first approval.', 'wp-webhooks' ) ),
		);

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_projecthuddle_trigger_on_project_type' => array(
					'id'		  => 'wpwhpro_projecthuddle_trigger_on_project_type',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	=> array(
						'website' => array( 'label' => __( 'Website', 'wp-webhooks' ) ),
						'project' => array( 'label' => __( 'Mockup', 'wp-webhooks' ) ),
					),
					'label'	   => __( 'Trigger on selected project types', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'Decide wether you want to fire this trigger on a website, mockup, or both. If you don\'t select any, all are triggered.', 'wp-webhooks' )
				),
			)
		);

		return array(
			'trigger'		   => 'ph_project_approved',
			'name'			  => __( 'Project approved', 'wp-webhooks' ),
			'sentence'			  => __( 'a project was approved', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a project was approved within ProjectHuddle.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'projecthuddle',
			'premium'		   => true,
		);

	}

	public function ph_project_approved_callback( $model_project, $approved, $is_new ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'ph_project_approved' );
		$response_data_array = array();

		$payload = array(
			'project_id' => ( isset( $model_project->ID ) ) ? $model_project->ID : 0,
			'project_type' => ( isset( $model_project->post ) && isset( $model_project->post->post_type ) ) ? $model_project->post->post_type : '',
			'project_data' => ( isset( $model_project->post ) ) ? $model_project->post : array(),
			'project_is_new' => $is_new,
		);

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){
	  
				  if( $settings_name === 'wpwhpro_projecthuddle_trigger_on_project_type' && ! empty( $settings_data ) ){
					$is_valid = false;

					$validated_project_type = str_replace( 'ph-', '', $payload['project_type'] );
					
					if( in_array( $validated_project_type, $settings_data ) ){
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

		do_action( 'wpwhpro/webhooks/trigger_ph_project_approved', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'project_id' => 9340,
			'project_type' => 'ph-website',
			'project_data' => 
			array (
			  'ID' => 9340,
			  'post_author' => '1',
			  'post_date' => '2022-08-13 11:21:42',
			  'post_date_gmt' => '2022-08-13 11:21:42',
			  'post_content' => '',
			  'post_title' => 'yourdomain',
			  'post_excerpt' => '',
			  'post_status' => 'publish',
			  'comment_status' => 'closed',
			  'ping_status' => 'closed',
			  'post_password' => '',
			  'post_name' => 'yourdomain',
			  'to_ping' => '',
			  'pinged' => '',
			  'post_modified' => '2022-08-13 20:13:04',
			  'post_modified_gmt' => '2022-08-13 20:13:04',
			  'post_content_filtered' => '',
			  'post_parent' => 0,
			  'guid' => 'https://yourdomain.test/website/yourdomain/?access_token=c66ce5dfc6cc20cf002cc7f1a14dc2fd',
			  'menu_order' => 0,
			  'post_type' => 'ph-website',
			  'post_mime_type' => '',
			  'comment_count' => '0',
			  'filter' => 'raw',
			),
			'project_is_new' => false,
		);

		return $data;
	}

  }

endif; // End if class_exists check.