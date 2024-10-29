<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_tutor_lms_Actions_tutor_enroll_user' ) ) :

	/**
	 * Load the tutor_enroll_user action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_tutor_lms_Actions_tutor_enroll_user {

		public function get_details(){

				//These are the main arguments the user can use to input. You should always grab them within your action function.
			$parameter = array(
				'user'	   => array( 'required' => true, 'short_description' => __( '(Mixed) The user id or email of the user you want to enroll.', 'wp-webhooks' ) ),
				'course_id'	=> array( 'required' => true, 'short_description' => __( '(Mixed) The course if of the user you want to enroll the user to. You can also use "any" to enroll the user to all courses.', 'wp-webhooks' ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further details about the sent data.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The user has been successfully enrolled.',
				'data' => 
				array (
				  'courses' => 
				  array (
					0 => 9213,
				  ),
				),
			);

			return array(
				'action'			=> 'tutor_enroll_user',
				'name'			  => __( 'Enroll user', 'wp-webhooks' ),
				'sentence'			  => __( 'enroll a user to a course', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'This webhook action allows you to enroll a user to a course within "Tutor LMS".', 'wp-webhooks' ),
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
			
			$tutor_helpers = WPWHPRO()->integrations->get_helper( 'tutor-lms', 'tutor_helpers' );
			$user	 	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$course_id	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'course_id' );

			if( empty( $course_id ) ){
				$return_args['msg'] = __( "Please set the course_id argument.", 'action-tutor_enroll_user-failure' );
				return $return_args;
			}

			$course_id = ( $course_id === 'all' ) ? 'all' : intval( $course_id );
			$user_id = 0;

            if( ! empty( $user ) && is_numeric( $user ) ){
                $user_id = intval( $user );
            } elseif( ! empty( $user ) && is_email( $user ) ) {
                $user_data = get_user_by( 'email', $user );
                if( ! empty( $user_data ) && isset( $user_data->ID ) && ! empty( $user_data->ID ) ){
                    $user_id = $user_data->ID;
                }
            }

            if( empty( $user_id ) ){
                $return_args['msg'] = __( "We could not find a user for your given user data.", 'action-tutor_enroll_user-error' );
				return $return_args;
            }

			$courses = $tutor_helpers->enroll_user_to_course( $course_id, $user_id );

			if( $courses ){
				$return_args['success'] = true;
				$return_args['msg'] = __( "The user has been successfully enrolled.", 'action-tutor_enroll_user-succcess' );
				$return_args['data']['courses'] = $courses;
			} else {
				$return_args['msg'] = __( "An error occured while enrolling the user.", 'action-tutor_enroll_user-succcess' );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.