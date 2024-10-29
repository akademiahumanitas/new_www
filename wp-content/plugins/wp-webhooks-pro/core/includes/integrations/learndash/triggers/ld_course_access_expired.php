<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_learndash_Triggers_ld_course_access_expired' ) ) :

 /**
  * Load the ld_course_access_expired trigger
  *
  * @since 4.3.7
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_learndash_Triggers_ld_course_access_expired {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'learndash_user_course_access_expired',
				'callback' => array( $this, 'learndash_user_course_access_expired_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'user_id' => array( 'short_description' => __( '(Integer) The id of the user that course access expired.', 'wp-webhooks' ) ),
			'course_id' => array( 'short_description' => __( '(Integer) The id of the course that expired.', 'wp-webhooks' ) ),
			'user' => array( 'short_description' => __( '(Array) Further data about the assigned user.', 'wp-webhooks' ) ),
		);

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_learndash_trigger_on_courses' => array(
					'id'		  => 'wpwhpro_learndash_trigger_on_courses',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'posts',
						'args'		=> array(
							'post_type' => 'sfwd-courses',
							'post_status' => 'publish',
						)
					),
					'label'	   => __( 'Trigger on selected courses', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'Select only the courses you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', 'wp-webhooks' )
				),
			)
		);

		return array(
			'trigger'		   => 'ld_course_access_expired',
			'name'			  => __( 'Course access expired', 'wp-webhooks' ),
			'sentence'			  => __( 'a course access expired', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a course access expired for a user within LearnDash.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'learndash',
			'premium'		   => true,
		);

	}

	public function learndash_user_course_access_expired_callback( $user_id, $course_id ){

		if( ! empty( $remove ) ){
			return;
		}

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'ld_course_access_expired' );

		$payload = array(
			'user_id' => $user_id,
			'course_id' => $course_id,
			'user' => ( ! empty( $user_id ) ) ? get_user_by( 'id', $user_id ) : '',
		);

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){

				if( $is_valid && isset( $webhook['settings']['wpwhpro_learndash_trigger_on_courses'] ) && ! empty( $webhook['settings']['wpwhpro_learndash_trigger_on_courses'] ) ){
					if( ! in_array( $course_id, $webhook['settings']['wpwhpro_learndash_trigger_on_courses'] ) ){
						$is_valid = false;
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

		do_action( 'wpwhpro/webhooks/trigger_ld_course_access_expired', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'user_id' => 141,
			'course_id' => 354,
			'user' => 
			array (
				'data' => 
				array (
				  'ID' => '94',
				  'user_login' => 'jondoe',
				  'user_pass' => '$P$Bh2I8hUTsh0UvuUBNeXXXXXXXXs.',
				  'user_nicename' => 'Jon Doe',
				  'user_email' => 'jon@doe.test',
				  'user_url' => '',
				  'user_registered' => '2019-09-06 16:08:27',
				  'user_activation_key' => '1567786108:$P$Bz.CL7BJuyXXXXXXXXXXLNdFfG0',
				  'user_status' => '0',
				  'display_name' => '',
				  'spam' => '0',
				  'deleted' => '0',
				),
				'ID' => 94,
				'caps' => 
				array (
				  'subscriber' => true,
				),
				'cap_key' => 'wp_capabilities',
				'roles' => 
				array (
				  0 => 'subscriber',
				),
				'allcaps' => 
				array (
				  'read' => true,
				  'level_0' => true,
				  'read_private_locations' => true,
				  'read_private_events' => true,
				  'subscriber' => true,
				),
				'filter' => NULL,
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.