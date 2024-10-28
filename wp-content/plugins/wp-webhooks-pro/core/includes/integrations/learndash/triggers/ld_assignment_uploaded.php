<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_learndash_Triggers_ld_assignment_uploaded' ) ) :

 /**
  * Load the ld_assignment_uploaded trigger
  *
  * @since 4.3.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_learndash_Triggers_ld_assignment_uploaded {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'learndash_assignment_uploaded',
				'callback' => array( $this, 'learndash_assignment_uploaded_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'assignment_post_id' => array( 'short_description' => __( '(Integer) The id of the uploaded assignment.', 'wp-webhooks' ) ),
			'file_name' => array( 'short_description' => __( '(String) The name of the uploaded file including the file extension.', 'wp-webhooks' ) ),
			'file_link' => array( 'short_description' => __( '(String) The URL of the uploaded assignment file.', 'wp-webhooks' ) ),
			'user_name' => array( 'short_description' => __( '(String) The name of the user that uploaded the assignment.', 'wp-webhooks' ) ),
			'disp_name' => array( 'short_description' => __( '(String) The display name of the user that uploaded the assignment.', 'wp-webhooks' ) ),
			'file_path' => array( 'short_description' => __( '(String) The full file path of the uploaded assignment.', 'wp-webhooks' ) ),
			'user_id' => array( 'short_description' => __( '(Integer) The id of the user that uploaded the assignment.', 'wp-webhooks' ) ),
			'course_id' => array( 'short_description' => __( '(Integer) The ID of the course the assignment was uploaded to.', 'wp-webhooks' ) ),
			'lesson_id' => array( 'short_description' => __( '(Integer) The ID of the lesson the assignment was uploaded to.', 'wp-webhooks' ) ),
			'lesson_title' => array( 'short_description' => __( '(String) The title of the lesson the assignment was uploaded to.', 'wp-webhooks' ) ),
			'lesson_type' => array( 'short_description' => __( '(String) The post type of the lesson the assignment was uploaded to.', 'wp-webhooks' ) ),
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
				'wpwhpro_learndash_trigger_on_lessons' => array(
					'id'		  => 'wpwhpro_learndash_trigger_on_lessons',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'posts',
						'args'		=> array(
							'post_type' => 'sfwd-lessons',
							'post_status' => 'publish',
						)
					),
					'label'	   => __( 'Trigger on selected lessons', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'Select only the lessons you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', 'wp-webhooks' )
				),
			)
		);

		return array(
			'trigger'		   => 'ld_assignment_uploaded',
			'name'			  => __( 'Assignment uploaded', 'wp-webhooks' ),
			'sentence'			  => __( 'an assignment was uploaded', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as an assignment was uploaded within LearnDash.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'learndash',
			'premium'		   => true,
		);

	}

	/**
	 * Triggers once an assigment was uploaded within LearnDash
	 *
	 * @param array $assignment_post_id - The ID of the uploaded assignment
	 * @param array $assignment_meta - Further data about the course, lesson, etc.
	 */
	public function learndash_assignment_uploaded_callback( $assignment_post_id, $assignment_meta ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'ld_assignment_uploaded' );

		$payload = array(
			'assignment_post_id' => $assignment_post_id,
			'course_id' => 0,
			'lesson_id' => 0,
		);
		$payload = array_merge( $payload, $assignment_meta );

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){

				if( $is_valid && isset( $webhook['settings']['wpwhpro_learndash_trigger_on_courses'] ) && ! empty( $webhook['settings']['wpwhpro_learndash_trigger_on_courses'] ) ){
					if( ! in_array( $payload['course_id'], $webhook['settings']['wpwhpro_learndash_trigger_on_courses'] ) ){
						$is_valid = false;
					}
				}

				if( $is_valid && isset( $webhook['settings']['wpwhpro_learndash_trigger_on_lessons'] ) && ! empty( $webhook['settings']['wpwhpro_learndash_trigger_on_lessons'] ) ){
					if( ! in_array( $payload['lesson_id'], $webhook['settings']['wpwhpro_learndash_trigger_on_lessons'] ) ){
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

		do_action( 'wpwhpro/webhooks/trigger_ld_assignment_uploaded', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'assignment_post_id' => 8077,
			'file_name' => 'assignment_8055_163986028318_demo_pdf_file.pdf',
			'file_link' => 'https://doe.test/wp-content/uploads/assignments/assignment_8055_163986028318_demo_pdf_file.pdf',
			'user_name' => 'admin',
			'disp_name' => 'admin',
			'file_path' => '%2Fthe%2Ftull%2Ffile%2Fpath%2Fwp-content%2Fuploads%2Fassignments%assignment_8055_163986028318_demo_pdf_file.pdf',
			'user_id' => 1,
			'lesson_id' => 8055,
			'course_id' => 8053,
			'lesson_title' => 'Demo Lesson 1',
			'lesson_type' => 'sfwd-lessons',
		  );

		return $data;
	}

  }

endif; // End if class_exists check.