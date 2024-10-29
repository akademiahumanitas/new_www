<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_courseware_Actions_wpcw_enroll_user_course' ) ) :

	/**
	 * Load the wpcw_enroll_user_course action
	 *
	 * @since 4.3.5
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_courseware_Actions_wpcw_enroll_user_course {

	public function get_details(){

			$parameter = array(
				'user'		=> array( 'required' => true, 'short_description' => __( 'Set this argument to either the user id or the user email of the user you want to enroll into the courses.', 'wp-webhooks' ) ),
				'courses'	=> array( 'required' => true, 'short_description' => __( 'Add the course ids you want to enroll the user into. This argument accepts a comma-separated string, as well as a JSON construct.', 'wp-webhooks' ) ),
				'do_action'	  => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired actions.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<?php echo __( "In case you want to add multiple courses to the user, you can either comma-separate the course ids like <code>2,3,12,44</code>, or you can add them via a JSON construct:", 'wp-webhooks' ); ?>
<pre>{
  23,
  3,
  44
}</pre>
		<?php
		$parameter['courses']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>wpcw_enroll_user_course</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 1 );
function my_custom_callback_function( $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo __( "All the values that are sent back as a response to the initial webhook action caller.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'The user has been enrolled successfully.',
			'data' => 
			array (
			  'user_id' => 153,
			  'courses' => 
			  array (
				0 => 1,
				1 => 2,
			  ),
			),
		);

		return array(
			'action'			=> 'wpcw_enroll_user_course', //required
			'name'			   => __( 'Course enroll user', 'wp-webhooks' ),
			'sentence'			   => __( 'enroll a user to one or multiple courses', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Enroll a user to one or multiple courses within WP Courseware.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'wp-courseware',
			'premium'	   	=> true,
		);


		}

		public function execute( $return_data, $response_body ){
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'user_id' => 0,
					'courses' => '',
				)
			);

			$user		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$courses		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'courses' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $user ) ){
				$return_args['msg'] = __( "Please set the user argument.", 'action-wpcw_enroll_user_course-error' );
				return $return_args;
			}

			$user_id = 0;
			if( is_numeric( $user ) ){
				$user_id = intval( $user );
			} elseif( is_email( $user ) ) {
				$email = sanitize_email( $user );
				$user = get_user_by( 'email', $email );
				if( ! empty( $user ) ){
					if( ! empty( $user->ID ) ){
						$user_id = $user->ID;
					}
				}
			}

			if( empty( $user_id ) ){
				$return_args['msg'] = __( "We could not find a user for your given input.", 'action-wpcw_enroll_user_course-error' );
				return $return_args;
			}

			if( empty( $courses ) ){
				$return_args['msg'] = __( "Please set the courses argument.", 'action-wpcw_enroll_user_course-error' );
				return $return_args;
			}

			$validated_courses = array();
			if( WPWHPRO()->helpers->is_json( $courses ) ){
                $validated_courses = json_decode( $courses, true );
            } else {
				$validated_courses = explode( ',', $courses );
			}

            if( ! is_array( $validated_courses ) && ! empty( $validated_courses ) ){
                $validated_courses = array( $validated_courses );
            }

			foreach( $validated_courses as $tk => $tv ){
				$validated_courses[ $tk ] = intval( $tv );
			}

            wpcw()->enrollment->enroll_student( $user_id, $validated_courses, 'add', true );
			
			$return_args['success'] = true;
			$return_args['msg'] = __( "The user has been enrolled successfully.", 'action-wpcw_enroll_user_course-success' );
			$return_args['data']['user_id'] = $user_id;
			$return_args['data']['courses'] = $validated_courses;

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.