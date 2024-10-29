<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_learndash_Triggers_ld_course_access_granted' ) ) :

 /**
  * Load the ld_course_access_granted trigger
  *
  * @since 4.3.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_learndash_Triggers_ld_course_access_granted {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'learndash_update_course_access',
				'callback' => array( $this, 'learndash_update_course_access_callback' ),
				'priority' => 20,
				'arguments' => 4,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'user_id' => array( 'short_description' => __( '(Integer) The id of the user that got access to the course.', 'wp-webhooks' ) ),
			'course_id' => array( 'short_description' => __( '(Integer) The id of the course the user got access to.', 'wp-webhooks' ) ),
			'course_access_list' => array( 'short_description' => __( '(String) A comma-separated list of course IDs the user has access to.', 'wp-webhooks' ) ),
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
			'trigger'		   => 'ld_course_access_granted',
			'name'			  => __( 'Course access granted', 'wp-webhooks' ),
			'sentence'			  => __( 'a course access was granted', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a course access was granted for a user within LearnDash.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'learndash',
			'premium'		   => true,
		);

	}

	/**
	 * Triggers once a course access was updated
	 *
	 * @param int          $user_id            User ID.
	 * @param int          $course_id          Course ID.
	 * @param string|null  $course_access_list A comma-separated list of user IDs used for the course_access_list field.
	 * @param boolean      $remove             Whether to remove course access from the user.
	 */
	public function learndash_update_course_access_callback( $user_id, $course_id, $course_access_list, $remove ){

		if( ! empty( $remove ) ){
			return;
		}

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'ld_course_access_granted' );

		$payload = array(
			'user_id' => $user_id,
			'course_id' => $course_id,
			'course_access_list' => $course_access_list,
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

		do_action( 'wpwhpro/webhooks/trigger_ld_course_access_granted', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'user_id' => 94,
			'course_id' => 8053,
			'course_access_list' => '110,94',
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