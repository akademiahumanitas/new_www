<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_tutor_lms_Actions_tutor_unroll_user' ) ) :

	/**
	 * Load the tutor_unroll_user action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_tutor_lms_Actions_tutor_unroll_user {

		public function get_details(){

				$parameter = array(
				'user'	   => array( 'required' => true, 'short_description' => __( '(Mixed) The user id or email of the user you want to unroll.', 'wp-webhooks' ) ),
				'course_id'	=> array( 'required' => true, 'short_description' => __( '(Mixed) The course if of the user you want to unroll the user to. You can also use "any" to unroll the user to all courses.', 'wp-webhooks' ) ),
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further details about the sent data.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The user has been successfully unrolled.',
				'data' => 
				array (
				  'courses' => 
				  array (
					0 => 9213,
				  ),
				),
			);

			return array(
				'action'			=> 'tutor_unroll_user',
				'name'			  => __( 'Unroll user', 'wp-webhooks' ),
				'sentence'			  => __( 'unroll a user to a course', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'This webhook action allows you to unroll a user to a course within "Tutor LMS".', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'tutor-lms',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(),
			);

			$user	 	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$course_id	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'course_id' );

			if( empty( $course_id ) ){
				$return_args['msg'] = __( "Please set the course_id argument.", 'action-tutor_unroll_user-failure' );
				return $return_args;
			}

			$course_id = ( $course_id === 'all' ) ? 'all' : intval( $course_id );
			$user_id = 0;
			$courses = array();

            if( ! empty( $user ) && is_numeric( $user ) ){
                $user_id = intval( $user );
            } elseif( ! empty( $user ) && is_email( $user ) ) {
                $user_data = get_user_by( 'email', $user );
                if( ! empty( $user_data ) && isset( $user_data->ID ) && ! empty( $user_data->ID ) ){
                    $user_id = $user_data->ID;
                }
            }

            if( empty( $user_id ) ){
                $return_args['msg'] = __( "We could not find a user for your given user data.", 'action-tutor_unroll_user-error' );
				return $return_args;
            }

			if( $course_id === 'any' ) {
				$courses = tutor_utils()->get_enrolled_courses_ids_by_user( $user_id );
			} else {
				$courses[] = $course_id;
			}

			foreach( $courses as $course ){
				//no return given so we have to consider it cancelled
				tutor_utils()->cancel_course_enrol( $course, $user_id );
			}

			$return_args['success'] = true;
			$return_args['msg'] = __( "The user has been successfully unrolled.", 'action-tutor_unroll_user-succcess' );
			$return_args['data']['courses'] = $courses;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.