<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_learndash_Actions_ld_grant_course_access' ) ) :

	/**
	 * Load the ld_grant_course_access action
	 *
	 * @since 4.3.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_learndash_Actions_ld_grant_course_access {

	public function get_details(){

			$parameter = array(
				'user_id'		=> array( 'required' => true, 'short_description' => __( 'The user id (or user email) of the user you want to grant course access.', 'wp-webhooks' ) ),
				'course_ids'	=> array( 'required' => true, 'short_description' => __( 'Add the courses you want to grant access to the user. This argument accepts a comma-separated string.', 'wp-webhooks' ) ),
				'do_action'	  => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired triggers.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<?php echo __( "This argument accepts a single course id, as well as multiple course ids, separated by a comma:", 'wp-webhooks' ); ?>
<pre>124,5741,23</pre>
		<?php
		$parameter['course_ids']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>ld_grant_course_access</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $return_args, $user_id, $validated_course_ids ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo __( "All the values that are sent back as a response to the initial webhook action caller.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$user_id</strong> (string)<br>
		<?php echo __( "The id of the user that was granted access to.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$validated_course_ids</strong> (array)<br>
		<?php echo __( "All newly assigned course ids that have granted the user access to.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'The course access has been successfully granted.',
			'data' => array(
				'user_id' => 104,
				'course_id' => 8053,
			),
		);

		return array(
			'action'			=> 'ld_grant_course_access', //required
			'name'			   => __( 'Grant course access', 'wp-webhooks' ),
			'sentence'			   => __( 'grant course access to a user', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Grant one or multiple course access for a user within Learndash.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'learndash',
			'premium'		   => true,
		);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array()
			);

			$user_id		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' );
			$course_ids		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'course_ids' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $user_id ) || empty( $course_ids ) ){
				$return_args['msg'] = __( "Please set both the user_id and course_ids arguments.", 'action-ld_grant_course_access-error' );
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

			$validated_course_ids = array();
			if( is_numeric( $course_ids ) ){
				$validated_course_ids[] = intval( $course_ids );
			} else {
				$validated_course_ids = array_map( "trim", explode( ',', $course_ids ) );
			}

			foreach( $validated_course_ids as $scid ){

				$scid = intval( $scid );

				if( ! is_int( $scid ) ){
					continue;
				}

				ld_update_course_access( $user_id, $scid );
			}

				$return_args['success'] = true;
				$return_args['msg'] = __( "The course access has been successfully granted.", 'action-ld_grant_course_access-success' );
				$return_args['data']['user_id'] = $user_id;
				$return_args['data']['course_ids'] = $validated_course_ids;

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $user_id, $validated_course_ids );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.