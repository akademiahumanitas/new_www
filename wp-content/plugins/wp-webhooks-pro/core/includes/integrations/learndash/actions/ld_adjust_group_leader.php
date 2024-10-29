<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_learndash_Actions_ld_adjust_group_leader' ) ) :

	/**
	 * Load the ld_adjust_group_leader action
	 *
	 * @since 4.3.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_learndash_Actions_ld_adjust_group_leader {

	public function get_details(){

			$parameter = array(
				'user_id'		=> array( 'required' => true, 'short_description' => __( 'The user id (or user email) of the user you want to set as a group leader.', 'wp-webhooks' ) ),
				'group_ids'	=> array( 'required' => true, 'short_description' => __( 'Add the groups you want to set the user as a leader. This argument accepts a comma-separated string of group IDs.', 'wp-webhooks' ) ),
				'group_action'	  => array( 'required' => true, 'short_description' => __( 'The action you want to perfrom for adjustment. Possible values: add, replace, remove.', 'wp-webhooks' ) ),
				'do_action'	  => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) ),
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired triggers.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<?php echo __( "This argument accepts a single group id, as well as multiple group ids, separated by a comma:", 'wp-webhooks' ); ?>
<pre>124,5741,23</pre>
		<?php
		$parameter['group_ids']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "This argument defines the action you want to perform for adjustment. Down below you will see further details of the available values:", 'wp-webhooks' ); ?>
<ul>
	<li><strong>add</strong>: <?php echo __( 'This adds the user as a leader.', 'wp-webhooks' ) ?></li>
	<li><strong>replace</strong>: <?php echo __( 'Replaces the user as a leader.', 'wp-webhooks' ) ?></li>
	<li><strong>remove</strong>: <?php echo __( 'Removes the user as a leader.', 'wp-webhooks' ) ?></li>
</ul>
		<?php
		$parameter['group_action']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>ld_adjust_group_leader</strong> action was fired.", 'wp-webhooks' ); ?>
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
            'msg' => 'The group leader has been successfully adjusted.',
            'data' => array(
				'user_id' => 104,
				'group_ids' => 8053,
				'group_action' => 'add',
			),
        );

		return array(
			'action'			=> 'ld_adjust_group_leader', //required
			'name'			   => __( 'Adjust group leader', 'wp-webhooks' ),
			'sentence'			   => __( 'adjust a group leader', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Add, replace, or remove a user as a group leader within Learndash.', 'wp-webhooks' ),
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
					'user_id' => 0,
					'group_ids' => 0,
					'group_action' => '',
				),
			);

			$user_id		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' );
			$group_ids		= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'group_ids' ) );
			$group_action		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'group_action' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $user_id ) || empty( $group_ids ) || empty( $group_action ) ){
				$return_args['msg'] = __( "Please set the user_id, group_ids, and group_action arguments.", 'action-ld_adjust_group_leader-error' );
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

			$validated_group_ids = array();
			if( is_int( $group_ids ) ){
				$validated_group_ids[] = $group_ids;
			} else {
				$validated_group_ids = array_map( "trim", explode( ',', $group_ids ) );
			}

			foreach( $validated_group_ids as $sgid ){

				if( ! is_int( $sgid ) ){
					continue;
				}

				switch( $group_action ){
					case 'add':

						if( ! user_can( $user, 'group_leader' ) ) {
							$user->add_role( 'group_leader' );
						}

						ld_update_leader_group_access( $user_id, $sgid );
						break;
					case 'replace':

						if( ! user_can( $user, 'group_leader' ) ) {
							$user->set_role( 'group_leader' );
						}

						ld_update_leader_group_access( $user_id, $sgid );
						break;
					case 'remove':

						ld_update_leader_group_access( $user_id, $sgid, true );

						$remove_role = true;
						$all_user_meta = get_user_meta( $user_id );
						if( ! empty( $all_user_meta ) ){
							foreach( $all_user_meta as $meta_key => $meta_set ){

								$ident = 'learndash_group_leaders_';
								if( $ident == substr( $meta_key, 0, strlen( $ident ) ) ){

									//make sure there's also no cached version somewhere
									if( $meta_key !== $ident . $sgid ){
										$remove_role = false;
									}
									
								}
							}
						}

						if( $remove_role ){
							$user->remove_role( 'group_leader' );
						}
						
						break;
				}
			}

				$return_args['success'] = true;
				$return_args['msg'] = __( "The group leader has been successfully adjusted.", 'action-ld_adjust_group_leader-success' );
				$return_args['data']['user_id'] = $user_id;
				$return_args['data']['group_ids'] = $validated_group_ids;
				$return_args['data']['group_action'] = $group_action;

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.