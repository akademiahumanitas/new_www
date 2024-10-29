<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_learndash_Actions_ld_get_group_leaders' ) ) :

	/**
	 * Load the ld_get_group_leaders action
	 *
	 * @since 4.3.7
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_learndash_Actions_ld_get_group_leaders {

	public function get_details(){

			$parameter = array(
				'group_ids'	=> array( 'short_description' => __( 'Add the group IDs of the groups you want to fetch the group leaders for. This argument accepts a comma-separated string of group IDs.', 'wp-webhooks' ) ),
				'do_action'	  => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) ),
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired triggers.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<?php echo __( "This argument accepts a single group id, as well as multiple group IDs, separated by commas:", 'wp-webhooks' ); ?>
<pre>124,5741,23</pre>
		<?php
		$parameter['group_ids']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>ld_get_group_leaders</strong> action was fired.", 'wp-webhooks' ); ?>
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
			'msg' => 'The group leaders have been successfully fetched.',
			'data' => 
			array (
			  'leaders' => 
			  array (
				9135 => 
				array (
				  0 => 
				  array (
					'data' => 
					array (
					  'ID' => '73',
					  'user_login' => 'jondoe',
					  'user_pass' => '$P$BoI5l9XXXXXXXXXXBoJFzvkPJ71',
					  'user_nicename' => 'jondoe',
					  'user_email' => 'jondoe@test.com',
					  'user_url' => '',
					  'user_registered' => '2022-05-11 23:04:19',
					  'user_activation_key' => '',
					  'user_status' => '0',
					  'display_name' => 'Jon Doe',
					  'spam' => '0',
					  'deleted' => '0',
					),
					'ID' => 73,
					'caps' => 
					array (
					  'Subscriber' => true,
					  'subscriber' => true,
					  'group_leader' => true,
					),
					'cap_key' => 'wp_capabilities',
					'roles' => 
					array (
					  1 => 'subscriber',
					  2 => 'group_leader',
					),
					'allcaps' => 
					array (
					  'read' => true,
					  'level_0' => true,
					  'read_private_locations' => true,
					  'read_private_events' => true,
					  'manage_resumes' => true,
					  'group_leader' => true,
					  'edit_essays' => true,
					  'edit_others_essays' => true,
					  'publish_essays' => true,
					  'read_essays' => true,
					  'read_private_essays' => true,
					  'delete_essays' => true,
					  'edit_published_essays' => true,
					  'delete_others_essays' => true,
					  'delete_published_essays' => true,
					  'read_assignment' => true,
					  'edit_assignments' => true,
					  'edit_others_assignments' => true,
					  'edit_published_assignments' => true,
					  'delete_others_assignments' => true,
					  'delete_published_assignments' => true,
					  'level_1' => false,
					  'Subscriber' => true,
					  'subscriber' => true,
					),
					'filter' => NULL,
				  ),
				),
			  ),
			),
		);

		return array(
			'action'			=> 'ld_get_group_leaders', //required
			'name'			   => __( 'Get group leaders', 'wp-webhooks' ),
			'sentence'			   => __( 'get all group leaders for one or multiple groups', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Get all group leaders for one or multiple groups within Learndash.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'learndash',
			'premium'		   => true,
		);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'leaders' => array(),
				),
			);

			$group_ids		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'group_ids' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $group_ids ) ){
				$return_args['msg'] = __( "Please set the group_ids argument.", 'action-ld_get_group_leaders-error' );
				return $return_args;
			}

			$validated_group_ids = array();
			if( is_numeric( $group_ids ) ){
				$validated_group_ids[] = intval( $group_ids );
			} else {
				$validated_group_ids = array_map( "trim", explode( ',', $group_ids ) );
			}

			$leaders = array();

			foreach( $validated_group_ids as $group_id ){

				if( ! isset( $leaders[ $group_id ] ) ){
					$leaders[ $group_id ] = learndash_get_groups_administrators( $group_id );
				}

			}

			if( ! empty( $leaders ) ){
				$return_args['success'] = true;
				$return_args['msg'] = __( "The group leaders have been successfully fetched.", 'action-ld_get_group_leaders-success' );
				$return_args['data']['leaders'] = $leaders;
			} else {
				$return_args['msg'] = __( "There was an issue fetching the group leaders.", 'action-ld_get_group_leaders-success' );
				$return_args['data']['leaders'] = $leaders;
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.