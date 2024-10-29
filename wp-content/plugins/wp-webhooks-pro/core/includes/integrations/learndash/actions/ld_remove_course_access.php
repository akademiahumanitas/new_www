<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_learndash_Actions_ld_remove_course_access' ) ) :

	/**
	 * Load the ld_remove_course_access action
	 *
	 * @since 4.3.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_learndash_Actions_ld_remove_course_access {

	public function get_details(){

			$parameter = array(
				'user_id'		=> array( 'required' => true, 'short_description' => __( 'The user id (or user email) of the user you want to remove the course access from.', 'wp-webhooks' ) ),
				'course_ids'	=> array( 'short_description' => __( 'Add the course IDs of the courses you want to remove the access for. This argument accepts the value "all" to remove access to all courses of the user, a single course id, or a comma-separated string of course IDs.', 'wp-webhooks' ) ),
				'do_action'	  => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) ),
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired triggers.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<?php echo __( "This argument accepts the value 'all' to set all courses as completed, a single course id, as well as multiple course ids, separated by commas (Multiple course ids will set all the courses to completed for the given course of the specified user):", 'wp-webhooks' ); ?>
<pre>124,5741,23</pre>
		<?php
		$parameter['course_ids']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>ld_remove_course_access</strong> action was fired.", 'wp-webhooks' ); ?>
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

		$returns_code = array(
			'success' => true,
			'msg' => 'The course access has been successfully removed.',
			'data' => 
			array (
			  'user_id' => 109,
			  'course_ids' => '8053',
			  'removed_access' => 
			  array (
				8053 => 
				array (
				  'user_id' => 109,
				  'course_id' => 8053,
				  'response' => true,
				),
			  ),
			),
		);

		return array(
			'action'			=> 'ld_remove_course_access', //required
			'name'			   => __( 'Remove course access', 'wp-webhooks' ),
			'sentence'			   => __( 'remove course access from a user', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Remove one or multiple course access for a user within Learndash.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'learndash',
			'premium'		   => true,
		);


		}

		public function execute( $return_data, $response_body ){

			$ld_helpers = WPWHPRO()->integrations->get_helper( 'learndash', 'ld_helpers' );
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'user_id' => 0,
					'course_ids' => 0,
					'removed_access' => false,
				),
			);

			$user_id		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' );
			$course_ids		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'course_ids' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $user_id ) || empty( $course_ids ) ){
				$return_args['msg'] = __( "Please set the user_id and course_ids arguments.", 'action-ld_remove_course_access-error' );
				return $return_args;
			}

			if( is_numeric( $user_id ) ){
				$user_id = intval( $user_id );
			} elseif( ! empty( $user_id ) && is_email( $user_id ) ) {
				$user_data = get_user_by( 'email', $user_id );
				if( ! empty( $user_data ) && isset( $user_data->ID ) ){
					$user_id = $user_data->ID;
				}
			}

			$removed_access = array();
			if( $course_ids === 'all' ){
				$user_courses = learndash_user_get_enrolled_courses( $user_id );
			} else {
				$user_courses_array = array_map( "trim", explode( ',', $course_ids ) );
				$user_courses = array();
				foreach( $user_courses_array as $sugk => $sugv ){
					$user_courses[ $sugk ] = intval( $sugv );
				}
			}

			foreach( $user_courses as $course_id ){

				if( ! is_numeric( $course_id ) ){
					continue;
				}

				$removed_access[ $course_id ] = array(
					'user_id' => $user_id,
					'course_id' => $course_id,
					'response' => ld_update_course_access( $user_id, $course_id, true ),
				);
			}

			if( ! empty( $removed_access ) ){
				$return_args['success'] = true;
				$return_args['msg'] = __( "The course access has been successfully removed.", 'action-ld_remove_course_access-success' );
				$return_args['data']['user_id'] = $user_id;
				$return_args['data']['course_ids'] = $course_ids;
				$return_args['data']['removed_access'] = $removed_access;
			} else {
				$return_args['msg'] = __( "No course access has been removed for the given user within Learndash.", 'action-ld_remove_course_access-success' );
				$return_args['data']['user_id'] = $user_id;
				$return_args['data']['course_ids'] = $course_ids;
				$return_args['data']['removed_access'] = $removed_access;
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.