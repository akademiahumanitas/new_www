<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_learndash_Actions_ld_create_group' ) ) :

	/**
	 * Load the ld_create_group action
	 *
	 * @since 4.3.7
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_learndash_Actions_ld_create_group {

	public function get_details(){

			$parameter = array(
				'user_id'		=> array( 'required' => true, 'short_description' => __( 'The user id (or user email) of the user you want to create the group with and set as group leader.', 'wp-webhooks' ) ),
				'group_name'	=> array( 'required' => true, 'short_description' => __( 'The name of the group.', 'wp-webhooks' ) ),
				'course_ids'	=> array( 'short_description' => __( 'Add the course IDs of the courses you want to connect to the group. This argument accepts a comma-separated string of course IDs.', 'wp-webhooks' ) ),
				'do_action'	  => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) ),
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired triggers.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<?php echo __( "This argument accepts a single course id, as well as multiple course ids, separated by commas (Multiple course ids will add each of the courses to the group):", 'wp-webhooks' ); ?>
<pre>124,5741,23</pre>
		<?php
		$parameter['course_ids']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>ld_create_group</strong> action was fired.", 'wp-webhooks' ); ?>
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
			'msg' => 'The group has been successfully created.',
			'data' => 
			array (
			  'user_id' => 0,
			  'course_ids' => 
			  array (
				0 => 8053,
			  ),
			  'group_id' => 9135,
			  'arguments' => 
			  array (
				'post_type' => 'groups',
				'group_name' => 'Demo group',
				'post_status' => 'publish',
				'post_author' => 73,
			  ),
			),
		);

		$description = array(
			'tipps' => array(
				__( 'Creating a group will also set the user as a group leader.', 'wp-webhooks' ),
			),
		);

		return array(
			'action'			=> 'ld_create_group', //required
			'name'			   => __( 'Create group', 'wp-webhooks' ),
			'sentence'			   => __( 'create a group', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Create a group within Learndash.', 'wp-webhooks' ),
			'description'	   => $description,
			'integration'	   => 'learndash',
			'premium'		   => true,
		);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'user_id' => 0,
					'course_ids' => array(),
					'arguments' => array(),
				),
			);

			$user_id		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' );
			$group_name		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'group_name' );
			$course_ids		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'course_ids' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $user_id ) ){
				$return_args['msg'] = __( "Please set the user_id argument.", 'action-ld_create_group-error' );
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

			$user = get_user_by( 'id', $user_id );
			if( empty( $user ) ){
				$return_args['msg'] = __( "No user was found for your given user_id data.", 'action-ld_create_group-error' );
				return $return_args;
			}

			$validated_course_ids = array();
			if( is_numeric( $course_ids ) ){
				$validated_course_ids[] = intval( $course_ids );
			} else {
				$validated_course_ids = array_map( "trim", explode( ',', $course_ids ) );
			}

			$args = array(
				'post_type'    => learndash_get_post_type_slug( 'group' ),
				'post_title'   => $group_name,
				'post_status'  => 'publish',
				'post_author'  => $user_id,
			);
	
			$group_id = wp_insert_post( $args );

			if( $group_id ){

				if( ! is_wp_error( $group_id ) ){

					$group_id = intval( $group_id );

					if( ! isset( $user->roles ) || ! is_array( $user->roles ) || ! in_array( 'group_leader', $user->roles ) ){
						$user->add_role( 'group_leader' );
					}

					ld_update_leader_group_access( $user_id, $group_id );

					foreach( $validated_course_ids as $course_id ){
						$course_id = intval( $course_id );

						ld_update_course_group_access( $course_id, $group_id, false );

						//clear the cache as well to show immediate impact
						$transient_key = 'learndash_course_groups_' . $course_id;
						LDLMS_Transients::delete( $transient_key );
					}
					

					$return_args['success'] = true;
					$return_args['msg'] = __( "The group has been successfully created.", 'action-ld_create_group-success' );
					$return_args['data']['group_id'] = $group_id;
					$return_args['data']['course_ids'] = $validated_course_ids;
					$return_args['data']['arguments'] = $args;
				} else {
					$return_args['msg'] = $group_id->get_error_message();
					$return_args['data']['arguments'] = $args;
					$return_args['data']['course_ids'] = $validated_course_ids;
				}
				
			} else {
				$return_args['msg'] = __( "There was an issue creating the group.", 'action-ld_create_group-success' );
				$return_args['data']['arguments'] = $args;
				$return_args['data']['course_ids'] = $validated_course_ids;
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.