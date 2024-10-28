<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_tutor_lms_Actions_tutor_complete_lesson' ) ) :

	/**
	 * Load the tutor_complete_lesson action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_tutor_lms_Actions_tutor_complete_lesson {

		public function get_details(){

				//These are the main arguments the user can use to input. You should always grab them within your action function.
			$parameter = array(
				'user'	   => array( 'required' => true, 'short_description' => __( '(Mixed) The user id or email of the user you want to complete the lesson for.', 'wp-webhooks' ) ),
				'lesson_id'	=> array( 'required' => true, 'short_description' => __( '(Mixed) The ID of the lesson you want to complete.', 'wp-webhooks' ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further details about the sent data.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The lesson was successfully completed.',
			);

			return array(
				'action'			=> 'tutor_complete_lesson',
				'name'			  => __( 'Complete lesson', 'wp-webhooks' ),
				'sentence'			  => __( 'complete a lesson', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'This webhook action allows you to complete a lesson for a user within "Tutor LMS".', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'tutor-lms',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
			);
			
			$user	 	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$lesson_id	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'lesson_id' );

			if( empty( $lesson_id ) ){
				$return_args['msg'] = __( "Please set the lesson_id argument.", 'action-tutor_complete_lesson-failure' );
				return $return_args;
			}

			$lesson_id = intval( $lesson_id );
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
                $return_args['msg'] = __( "We could not find a user for your given user data.", 'action-tutor_complete_lesson-error' );
				return $return_args;
            }

			//we have to accept that there is no return value to check against
			tutils()->mark_lesson_complete( $lesson_id, $user_id );

			$return_args['success'] = true;
			$return_args['msg'] = __( "The lesson was successfully completed.", 'action-tutor_complete_lesson-succcess' );

			return $return_args;
	
		}

	}

endif; // End if class_exists check.