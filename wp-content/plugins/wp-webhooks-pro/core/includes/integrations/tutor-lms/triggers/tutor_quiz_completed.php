<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_tutor_lms_Triggers_tutor_quiz_completed' ) ) :

 /**
  * Load the tutor_quiz_completed trigger
  *
  * @since 5.1
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_tutor_lms_Triggers_tutor_quiz_completed {

  public function get_callbacks(){

   return array(
	array(
		'type' => 'action',
		'hook' => 'tutor_quiz/attempt_ended',
		'callback' => array( $this, 'ironikus_trigger_tutor_quiz_completed' ),
		'priority' => 20,
		'arguments' => 3,
		'delayed' => true,
	  ),
	);

  }

	public function get_details(){

		$parameter = array(
			'attempt_id' => array( 'short_description' => __( '(Integer) The id of the attempt.', 'wp-webhooks' ) ),
			'course_id' => array( 'short_description' => __( '(Integer) The id of the course the quiz is assigned to.', 'wp-webhooks' ) ),
			'user_id' => array( 'short_description' => __( '(Integer) The ID of the user that completed the quiz.', 'wp-webhooks' ) ),
			'quiz_id' => array( 'short_description' => __( '(Integer) The id of the quiz.', 'wp-webhooks' ) ),
			'attempt' => array( 'short_description' => __( '(Array) Further data about the attempt.', 'wp-webhooks' ) ),
			'attempt_info' => array( 'short_description' => __( '(Array) Further info about the attempt.', 'wp-webhooks' ) ),
		);

	  	$settings = array(
			'load_default_settings' => true,
			'data' => array(
			'wpwhpro_tutor_quiz_completed_trigger_on_quiz' => array(
				'id'	 => 'wpwhpro_tutor_quiz_completed_trigger_on_quiz',
				'type'	=> 'select',
				'multiple'  => true,
				'choices'   => array(),
				'query'			=> array(
					'filter'	=> 'posts',
					'args'		=> array(
						'post_type' => apply_filters( 'tutor_quiz_post_type', 'tutor_quiz' ),
					)
				),
				'label'	=> __( 'Trigger on selected quizzes', 'wp-webhooks' ),
				'placeholder' => '',
				'required'  => false,
				'description' => __( 'Select only the quizzes you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', 'wp-webhooks' )
			),
			'wpwhpro_tutor_quiz_completed_trigger_on_status' => array(
				'id'	 => 'wpwhpro_tutor_quiz_completed_trigger_on_status',
				'type'	=> 'select',
				'multiple'  => true,
				'choices'   => array(
					'pass' => array( 'label' => __( 'Passes', 'wp-webhooks' ) ),
					'fail' => array( 'label' => __( 'Fails', 'wp-webhooks' ) ),
				),
				'label'	=> __( 'Trigger on quiz status', 'wp-webhooks' ),
				'placeholder' => '',
				'required'  => false,
				'description' => __( 'Select only the status you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', 'wp-webhooks' )
			),
			)
		);

		return array(
			'trigger'	  => 'tutor_quiz_completed',
			'name'	   => __( 'Quiz completed', 'wp-webhooks' ),
			'sentence'	   => __( 'a quiz was completed', 'wp-webhooks' ),
			'parameter'	 => $parameter,
			'settings'	 => $settings,
			'returns_code'   => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires after a Tutor LMS quiz was completed.', 'wp-webhooks' ),
			'description'	=> array(),
			'callback'	 => 'test_tutor_quiz_completed',
			'integration'	=> 'tutor-lms',
			'premium'	=> true,
		);

	}

	public function ironikus_trigger_tutor_quiz_completed( $attempt_id, $course_id, $user_id ){

		$attempt = tutor_utils()->get_attempt( $attempt_id );
		$quiz_id = ( is_object( $attempt ) ) ? $attempt->quiz_id : '';

		if( 
			empty( $quiz_id ) 
			|| empty( $attempt )
			|| get_post_type( $quiz_id ) !== apply_filters( 'tutor_quiz_post_type', 'tutor_quiz' )
			|| ! isset( $attempt->attempt_status )
			|| ! in_array( $attempt->attempt_status, array( 'review_required', 'attempt_ended' ) )
		){
			return;
		}

	  $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'tutor_quiz_completed' );
	  $data_array = array(
		'attempt_id' => $attempt_id,
		'course_id' => $course_id,
		'user_id' => $user_id,
		'quiz_id' => $quiz_id,
		'attempt' => $attempt,
		'attempt_info' => ( isset( $attempt->attempt_info ) ) ? tutor_utils()->quiz_attempt_info( $attempt->attempt_info ) : array(),
	  );
	  $response_data = array();

	  foreach( $webhooks as $webhook ){

		$is_valid = true;

		if( isset( $webhook['settings'] ) ){
		  foreach( $webhook['settings'] as $settings_name => $settings_data ){

			if( $settings_name === 'wpwhpro_tutor_quiz_completed_trigger_on_quiz' && ! empty( $settings_data ) ){
			  if( ! in_array( $quiz_id, $settings_data ) ){
				$is_valid = false;
			  }
			}

			if( $is_valid && $settings_name === 'wpwhpro_tutor_quiz_completed_trigger_on_status' && ! empty( $settings_data ) ){
				
				$is_valid = false;

				if( $attempt->earned_marks >= 0 ){

					$reached_marks = ( $attempt->earned_marks * 100 ) / $attempt->total_marks;
            		$required_marks = intval( tutor_utils()->get_quiz_option( $quiz_id, 'passing_grade', 0 ) );

					if( $reached_marks >= $required_marks && in_array( 'pass', $settings_data ) ){
						$is_valid = true;
					} elseif( $reached_marks <= $required_marks && in_array( 'fail', $settings_data ) ) {
						$is_valid = true;
					}
				}
				
			}

		  }
		}

		if( $is_valid ) {
		  $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

		  if( $webhook_url_name !== null ){
			$response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
		  } else {
			$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
		  }
		}
	  }

	  do_action( 'wpwhpro/webhooks/trigger_tutor_quiz_completed', $data_array, $response_data );
	}

	/*
	* Register the demo post delete trigger callback
	*
	* @since 1.2
	*/
	public function get_demo( $options = array() ) {

		$data = array (
			'attempt_id' => 6,
			'course_id' => '9217',
			'user_id' => 153,
			'quiz_id' => '9220',
			'attempt' => 
			array (
			'attempt_id' => '6',
			'course_id' => '9217',
			'quiz_id' => '9220',
			'user_id' => '153',
			'total_questions' => '1',
			'total_answered_questions' => '1',
			'total_marks' => '1.00',
			'earned_marks' => '1.00',
			'attempt_info' => 'a:8:{s:10:"time_limit";a:3:{s:10:"time_value";s:1:"0";s:9:"time_type";s:7:"minutes";s:18:"time_limit_seconds";i:0;}s:16:"attempts_allowed";s:2:"10";s:13:"passing_grade";s:2:"80";s:24:"max_questions_for_answer";s:2:"10";s:20:"question_layout_view";s:0:"";s:15:"questions_order";s:4:"rand";s:29:"short_answer_characters_limit";s:3:"200";s:34:"open_ended_answer_characters_limit";s:3:"500";}',
			'attempt_status' => 'review_required',
			'attempt_ip' => '127.0.0.1',
			'attempt_started_at' => '2022-04-11 16:26:30',
			'attempt_ended_at' => '2022-04-11 16:26:34',
			'is_manually_reviewed' => NULL,
			'manually_reviewed_at' => NULL,
			),
			'attempt_info' => 
			array (
			'time_limit' => 
			array (
				'time_value' => '0',
				'time_type' => 'minutes',
				'time_limit_seconds' => 0,
			),
			'attempts_allowed' => '10',
			'passing_grade' => '80',
			'max_questions_for_answer' => '10',
			'question_layout_view' => '',
			'questions_order' => 'rand',
			'short_answer_characters_limit' => '200',
			'open_ended_answer_characters_limit' => '500',
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.