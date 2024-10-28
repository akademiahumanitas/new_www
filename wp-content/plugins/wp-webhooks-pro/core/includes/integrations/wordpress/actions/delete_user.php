<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_delete_user' ) ) :

	/**
	 * Load the delete_user action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_delete_user {

		/*
	 * The core logic to delete a specified user
	 */
	public function get_details(){

		$parameter = array(
			'user_id'	   => array( 
				'label' => __( 'User ID', 'wp-webhooks' ),
				'short_description' => __( '(Optional if user_email is defined) Include the numeric id of the user.', 'wp-webhooks' ),
			),
			'user_email'	=> array( 
				'label' => __( 'User email', 'wp-webhooks' ),
				'short_description' => __( '(Optional if user_email is defined) Include the assigned email of the user.', 'wp-webhooks' ),
			),
			'send_email'	=> array(
				'label' => __( 'Send email', 'wp-webhooks' ),
				'type' => 'select',
				'choices' => array(
					'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
					'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
				),
				'multiple' => false,
				'default_value' => 'no',
				'short_description' => __( 'Set this field to "yes" to send a email to the user that the account got deleted.', 'wp-webhooks' ),
				'description' => __( "In case you set the <strong>send_email</strong> argument to <strong>yes</strong>, we will send an email from this WordPress site to the user email, containing the notice of the deleted account.", 'wp-webhooks' )
			),
			'remove_from_network'	=> array( 
				'label' => __( 'Remove from network', 'wp-webhooks' ),
				'type' => 'select',
				'choices' => array(
					'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
					'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
				),
				'multiple' => false,
				'default_value' => 'no',
				'short_description' => __( 'Set this field to "yes" to delete a user from the whole network. WARNING: This will delete all posts authored by the user. Default: "no"', 'wp-webhooks' ),
			),
			'remove_from_subsites'	=> array( 
				'label' => __( 'Remove from sub sites', 'wp-webhooks' ),
				'type' => 'select',
				'query'			=> array(
					'filter'	=> 'network_sites',
					'args'		=> array()
				),
				'multiple' => true,
				'short_description' => __( 'Set the IDs of the subsite you want to remove the user from.', 'wp-webhooks' ),
			),
			'do_action'	 => array( 'short_description' => __( 'Advanced: Register a custom action after the plugin fires this webhook.', 'wp-webhooks' ) )
		);

		ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>delete_user</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $user, $user_id, $user_email, $send_email ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$user</strong> (object)<br>
		<?php echo __( "Contains the WordPress user object.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$user_id</strong> (integer)<br>
		<?php echo __( "Contains the user id of the deleted user. Please note that it can also contain a wp_error object since it is the response of the wp_insert_user() function.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$user_email</strong> (string)<br>
		<?php echo __( "Contains the user email.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$send_email</strong> (string)<br>
		<?php echo __( "Returns either yes or no, depending on your settings for the send_email argument.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'data'		=> array( 'short_description' => __( '(array) User related data as an array. We return the user id with the key "user_id" and the user delete success boolean with the key "user_deleted". E.g. array( \'data\' => array(...) )', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'User successfully deleted.',
			'data' => 
			array (
			  'user_deleted' => true,
			  'user_id' => 112,
			),
		);

		$description = array(
			'tipps' => array(
				__( 'Please note that deleting a user inside of a multisite network without setting the <strong>remove_from_network</strong> argument just deletes the user from the current site, but not from the whole network.', 'wp-webhooks' )
			),
		);

		return array(
			'action'			=> 'delete_user',
			'name'			  => __( 'Delete user', 'wp-webhooks' ),
			'sentence'			  => __( 'delete a user', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Deletes a user on your WordPress website or network.', 'wp-webhooks' ),
			'description'	   => $description,
			'integration'	   => 'wordpress',
			'premium' 			=> false,
		);

	}

		/**
		 * Delete function for defined action
		 */
		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'	 => '',
				'data' => array(
					'user_deleted' => false,
					'user_id' => 0
				)
			);

			$user_id	 = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' ) );
			$user_email  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_email' );
			$do_action   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );
			$send_email  = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'send_email' ) == 'yes' ) ? 'yes' : 'no';
			$remove_from_network  = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'remove_from_network' ) == 'yes' ) ? 'yes' : 'no';
			$remove_from_subsites  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'remove_from_subsites' );
			$user = '';

			if( ! empty( $user_id ) ){
				$user = get_user_by( 'id', $user_id );
			} elseif( ! empty( $user_email ) ){
				$user = get_user_by( 'email', $user_email );
			}

			if( ! empty( $user ) ){
				if( ! empty( $user->ID ) ){

					$user_id = $user->ID;

					$delete_administrators = apply_filters( 'wpwhpro/run/delete_action_user_admins', false );
					if ( in_array( 'administrator', $user->roles ) && ! $delete_administrators ) {
						exit;
					}

					require_once( ABSPATH . 'wp-admin/includes/user.php' );

					if( is_multisite() && $remove_from_network == 'yes' ){

						if( ! function_exists( 'wpmu_delete_user' ) ){
							require_once( ABSPATH . 'wp-admin/includes/ms.php' );
						}

						$checkdelete = wpmu_delete_user( $user_id );
					} else {

						if( is_multisite() && ! empty( $remove_from_subsites ) ){

							$site_ids = array();
							if( WPWHPRO()->helpers->is_json( $remove_from_subsites ) ){
								$site_ids = json_decode( $remove_from_subsites, true );
							} elseif ( is_string( $remove_from_subsites ) ) {
								$site_ids = explode( ',', $remove_from_subsites );
							}

							if( is_array( $site_ids ) ){
								$prev_blog_id = get_current_blog_id();
								
								foreach( $site_ids as $site_id ){
									if( is_numeric( $site_id ) ){
										switch_to_blog( intval( $site_id ) );
										$checkdelete = wp_delete_user( $user_id );
									}
								}

								//Switch back to the original blog
								switch_to_blog( intval( $prev_blog_id ) );
							}

						} else {
							$checkdelete = wp_delete_user( $user_id );
						}
						
					}

					if ( $checkdelete ) {

						$send_admin_notification = apply_filters( 'wpwhpro/run/delete_action_user_notification', true );
						if( $send_admin_notification && $send_email == 'yes' ){
							$blog_name = get_bloginfo( "name" );
							$blog_email = get_bloginfo( "admin_email" );
							$headers = 'From: ' . $blog_name . ' <' . $blog_email . '>' . "\r\n";
							$subject = __( 'Your account has been deleted.', 'wp-webhooks' );
							$content = sprintf( __( "Hello %s,\r\n", 'action-delete-user' ), $user->user_nicename );
							$content .= sprintf( __( 'Your account at %s (%d) has been deleted.' . "\r\n", 'action-delete-user' ), $blog_name, home_url() );
							$content .= sprintf( __( 'Please contact %s for further questions.', 'wp-webhooks' ), $blog_email );

							wp_mail( $user_email, $subject, $content, $headers );
						}

						do_action( 'wpwhpro/run/delete_action_user_deleted' );

						$return_args['msg'] = __( "User successfully deleted.", 'wp-webhooks' );
						$return_args['success'] = true;
						$return_args['data']['user_deleted'] = true;
						$return_args['data']['user_id'] = $user_id;
					} else {
						$return_args['msg'] = __( "Error deleting user.", 'wp-webhooks' );
					}

				} else {
					$return_args['msg'] = __( "Could not delete user because the user not given.", 'wp-webhooks' );
				}
			} else {
				$return_args['msg'] = __( "We could not locate a user for your given user email/ID. If you want to delete the user on a network level, please make sure to set the remove_from_network argument.", 'wp-webhooks' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $user, $user_id, $user_email, $send_email );
			}

			return $return_args;
		}

	}

endif; // End if class_exists check.