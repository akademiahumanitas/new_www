<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_learndash_Actions_ld_complete_topics' ) ) :

	/**
	 * Load the ld_complete_topics action
	 *
	 * @since 4.3.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_learndash_Actions_ld_complete_topics {

	public function get_details(){

			$parameter = array(
				'user_id'		=> array( 'required' => true, 'short_description' => __( 'The user id (or user email) of the user you want to set the topics to complete.', 'wp-webhooks' ) ),
				'course_id'	=> array( 'required' => true, 'short_description' => __( 'The id of the course you want to set the topics to completed.', 'wp-webhooks' ) ),
				'lesson_id'	=> array( 'short_description' => __( 'The id of a lesson you want to complete the topics for.', 'wp-webhooks' ) ),
				'topic_ids'	=> array( 'short_description' => __( 'Add the topic IDs of the topics you want to set to complete. This argument accepts the value "all" to set all lessons as complete, a single topic id, or a comma-separated string of topic IDs.', 'wp-webhooks' ) ),
				'do_action'	  => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) ),
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired triggers.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<p><?php echo __( "Please note: once the lesson_id argument is set to a lesson id, all topics for that lesson are completed, regardless of the ones defined within the topic_ids argument.", 'wp-webhooks' ); ?></p>
		<?php
		$parameter['lesson_id']['description'] = ob_get_clean();

			ob_start();
		?>
<p><?php echo __( "Please note: once the lesson_id argument is set to a lesson id, all topics for that lesson are completed.", 'wp-webhooks' ); ?></p>
<?php echo __( "This argument accepts the value 'all' to set all lessons as complete, a single topic id, as well as multiple topic ids, separated by commas (Multiple topic ids will set all the topics to completed for the given course of the specified user):", 'wp-webhooks' ); ?>
<pre>124,5741,23</pre>
		<?php
		$parameter['topic_ids']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>ld_complete_topics</strong> action was fired.", 'wp-webhooks' ); ?>
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
			'msg' => 'The topics have been successfully completed.',
			'data' => 
			array (
			  'user_id' => 1,
			  'topic_ids' => 'all',
			  'topics_completed' => 
			  array (
				'success' => true,
				'topics' => 
				array (
				  8075 => 
				  array (
					'user_id' => 1,
					'course_id' => 8053,
					'lesson_id' => 8055,
					'topic_id' => 8075,
					'response' => true,
				  ),
				),
			  ),
			),
		);

		$description = array(
			'tipps' => array(
				__( 'If you do not set the <strong>topic_ids</strong> argument (or you set its value to "all"), we will mark all topics for the given course and user as completed.', 'wp-webhooks' ),
				__( 'Once you set the <strong>lesson_id</strong> argument to a lesson id, all topics of that lesson are set to complete.', 'wp-webhooks' ),
			),
		);

		return array(
			'action'			=> 'ld_complete_topics', //required
			'name'			   => __( 'Complete topics', 'wp-webhooks' ),
			'sentence'			   => __( 'complete one or multiple topics', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Complete one or multiple topics of a course for a user within Learndash.', 'wp-webhooks' ),
			'description'	   => $description,
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
					'topic_ids' => 0,
					'topics_completed' => array(),
				),
			);

			$user_id		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' );
			$course_id		= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'course_id' ) );
			$lesson_id		= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'lesson_id' ) );
			$topic_ids		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'topic_ids' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $user_id ) || empty( $course_id ) ){
				$return_args['msg'] = __( "Please set the user_id and course_id arguments.", 'action-ld_complete_topics-error' );
				return $return_args;
			}

			if( empty( $topic_ids ) ){
				$topic_ids = 'all';
			}

			if( is_numeric( $user_id ) ){
				$user_id = intval( $user_id );
			} elseif( ! empty( $user_id ) && is_email( $user_id ) ) {
				$user_data = get_user_by( 'email', $user_id );
				if( ! empty( $user_data ) && isset( $user_data->ID ) ){
					$user_id = $user_data->ID;
				}
			}

			$validated_topic_ids = array();
			if( $topic_ids === 'all' ){
				$validated_topic_ids = 'all';
			} else {
				$validated_topic_ids = array_map( "trim", explode( ',', $topic_ids ) );
			}

			$topics_completed = $ld_helpers->complete_topics( $user_id, $course_id, $lesson_id, $validated_topic_ids );

			if( $topics_completed ){
				$return_args['success'] = true;
				$return_args['msg'] = __( "The topics have been successfully completed.", 'action-ld_complete_topics-success' );
				$return_args['data']['user_id'] = $user_id;
				$return_args['data']['topic_ids'] = $validated_topic_ids;
				$return_args['data']['topics_completed'] = $topics_completed;
			} else {
				$return_args['msg'] = __( "The topics could not be fully completed.", 'action-ld_complete_topics-success' );
				$return_args['data']['user_id'] = $user_id;
				$return_args['data']['topic_ids'] = $validated_topic_ids;
				$return_args['data']['topics_completed'] = $topics_completed;
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.