<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_get_user' ) ) :

	/**
	 * Load the get_user action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_get_user {

		/*
	 * The core logic to get the user
	 */
	public function get_details(){

		$parameter = array(
			'user_value'	   => array( 'required' => true, 'short_description' => __( 'The user id of the user. You can also use certain other values by changing the value_type argument.', 'wp-webhooks' ) ),
			'value_type'	=> array(
				'short_description' => __( 'You can choose between certain value types. Possible: id, slug, email, login', 'wp-webhooks' ),
				'description' => __( "This argument is used to change the data you can add within the <strong>user_value</strong> argument. Possible values are: <strong>id, ID, slug, email, login</strong>", 'wp-webhooks' )
			),
			'do_action'	 => array( 'short_description' => __( 'Advanced: Register a custom action after the plugin fires this webhook.', 'wp-webhooks' ) )
		);

		ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>get_user</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $return_args, $user_value, $value_type, $user ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo __( "All the values that are sent back as a response the the initial webhook action caller.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$user_value</strong> (string)<br>
		<?php echo __( "The value you included into the user_value argument.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$value_type</strong> (string)<br>
		<?php echo __( "The value you included into the value_type argument.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$user</strong> (mixed)<br>
		<?php echo __( "Returns null in case an the user_value wasn't set, the user object on success or a wp_error object in case an error occurs.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns = array(
			'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'data'		=> array( 'short_description' => __( 'The data construct of the user qury. This depends on the parameters you send.', 'wp-webhooks' ) ),
			'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'User was successfully returned.',
			'data' => 
			array (
			  'data' => 
			  array (
				'ID' => '78',
				'user_login' => 'demo',
				'user_pass' => '$P$BNibyUQOn6H5wJNi1Nb9ZxI8iK5mZi0',
				'user_nicename' => 'demo',
				'user_email' => 'demo@demo.demo',
				'user_url' => '',
				'user_registered' => '2019-07-03 15:13:43',
				'user_activation_key' => '',
				'user_status' => '0',
				'display_name' => 'demo',
				'spam' => '0',
				'deleted' => '0',
			  ),
			  'ID' => 78,
			  'caps' => 
			  array (
				'editor' => true,
			  ),
			  'cap_key' => 'wp_capabilities',
			  'roles' => 
			  array (
				0 => 'editor',
			  ),
			  'allcaps' => 
			  array (
				'moderate_comments' => true,
				'manage_categories' => true,
				'manage_links' => true,
				'upload_files' => true,
				'unfiltered_html' => true,
				'edit_posts' => true,
				'edit_others_posts' => true,
				'edit_published_posts' => true,
				'publish_posts' => true,
				'edit_pages' => true,
				'read' => true,
				'level_7' => true,
				'level_6' => true,
				'level_5' => true,
				'level_4' => true,
				'level_3' => true,
				'level_2' => true,
				'level_1' => true,
				'level_0' => true,
				'edit_others_pages' => true,
				'edit_published_pages' => true,
				'publish_pages' => true,
				'delete_pages' => true,
				'delete_others_pages' => true,
				'delete_published_pages' => true,
				'delete_posts' => true,
				'delete_others_posts' => true,
				'delete_published_posts' => true,
				'delete_private_posts' => true,
				'edit_private_posts' => true,
				'read_private_posts' => true,
				'delete_private_pages' => true,
				'edit_private_pages' => true,
				'read_private_pages' => true,
				'edit_blocks' => true,
				'edit_others_blocks' => true,
				'publish_blocks' => true,
				'read_private_blocks' => true,
				'read_blocks' => true,
				'delete_blocks' => true,
				'delete_private_blocks' => true,
				'delete_published_blocks' => true,
				'delete_others_blocks' => true,
				'edit_private_blocks' => true,
				'edit_published_blocks' => true,
				'create_blocks' => true,
				'read_private_aggregator-records' => true,
				'edit_aggregator-records' => true,
				'edit_others_aggregator-records' => true,
				'edit_private_aggregator-records' => true,
				'edit_published_aggregator-records' => true,
				'delete_aggregator-records' => true,
				'delete_others_aggregator-records' => true,
				'delete_private_aggregator-records' => true,
				'delete_published_aggregator-records' => true,
				'publish_aggregator-records' => true,
				'publish_events' => true,
				'delete_others_events' => true,
				'edit_others_events' => true,
				'manage_others_bookings' => true,
				'publish_recurring_events' => true,
				'delete_others_recurring_events' => true,
				'edit_others_recurring_events' => true,
				'publish_locations' => true,
				'delete_others_locations' => true,
				'delete_locations' => true,
				'edit_others_locations' => true,
				'delete_event_categories' => true,
				'edit_event_categories' => true,
				'manage_bookings' => true,
				'upload_event_images' => true,
				'delete_events' => true,
				'edit_events' => true,
				'read_private_events' => true,
				'delete_recurring_events' => true,
				'edit_recurring_events' => true,
				'edit_locations' => true,
				'read_private_locations' => true,
				'read_others_locations' => true,
				'editor' => true,
			  ),
			  'filter' => NULL,
			),
			'user_meta' => 
			array (
			  'nickname' => 
			  array (
				0 => 'demo',
			  ),
			  'first_name' => 
			  array (
				0 => '',
			  ),
			  'last_name' => 
			  array (
				0 => '',
			  ),
			  'description' => 
			  array (
				0 => '',
			  ),
			  'rich_editing' => 
			  array (
				0 => 'true',
			  ),
			  'syntax_highlighting' => 
			  array (
				0 => 'true',
			  ),
			  'comment_shortcuts' => 
			  array (
				0 => 'false',
			  ),
			  'admin_color' => 
			  array (
				0 => 'fresh',
			  ),
			  'use_ssl' => 
			  array (
				0 => '0',
			  ),
			  'show_admin_bar_front' => 
			  array (
				0 => 'true',
			  ),
			  'locale' => 
			  array (
				0 => '',
			  ),
			  'wp_capabilities' => 
			  array (
				0 => 'a:1:{s:6:"editor";b:1;}',
			  ),
			  'wp_user_level' => 
			  array (
				0 => '7',
			  ),
			  'custom_field_1' => 
			  array (
				0 => '',
			  ),
			  'account_status' => 
			  array (
				0 => 'approved',
			  ),
			  'billing_first_name' => 
			  array (
				0 => '',
			  ),
			  'billing_last_name' => 
			  array (
				0 => '',
			  ),
			  'billing_company' => 
			  array (
				0 => '',
			  ),
			  'billing_address_1' => 
			  array (
				0 => '',
			  ),
			  'billing_address_2' => 
			  array (
				0 => '',
			  ),
			  'billing_city' => 
			  array (
				0 => '',
			  ),
			  'billing_postcode' => 
			  array (
				0 => '',
			  ),
			  'billing_country' => 
			  array (
				0 => '',
			  ),
			  'billing_state' => 
			  array (
				0 => '',
			  ),
			  'billing_phone' => 
			  array (
				0 => '',
			  ),
			  'billing_email' => 
			  array (
				0 => 'demo@demo.test',
			  ),
			  'shipping_first_name' => 
			  array (
				0 => '',
			  ),
			  'shipping_last_name' => 
			  array (
				0 => '',
			  ),
			  'shipping_company' => 
			  array (
				0 => '',
			  ),
			  'shipping_address_1' => 
			  array (
				0 => '',
			  ),
			  'shipping_address_2' => 
			  array (
				0 => '',
			  ),
			  'shipping_city' => 
			  array (
				0 => '',
			  ),
			  'shipping_postcode' => 
			  array (
				0 => '',
			  ),
			  'shipping_country' => 
			  array (
				0 => '',
			  ),
			  'shipping_state' => 
			  array (
				0 => '',
			  ),
			  'last_update' => 
			  array (
				0 => '1585681906',
			  ),
			  'wc_last_active' => 
			  array (
				0 => '1585612800',
			  ),
			  'primary_blog' => 
			  array (
				0 => '1',
			  ),
			),
			'user_posts_url' => 'https://yourdomain.test/blog/author/demo/',
			'acf_meta' => false,
		);

		$description =  array(
			'tipps' => array(
				__( 'In case you want to use e.g. the email instead of the user id, you need to set the argument <strong>value_type</strong> to <strong>email</strong>.', 'wp-webhooks' ),
				__( 'This webhook action uses the WordPress function <strong>get_user_by()</strong> to fetch the user from the database. To learn more about this function, please check the official WordPress docs:', 'wp-webhooks' ) . ' <a title="wordpress.org" target="_blank" href="https://developer.wordpress.org/reference/functions/get_user_by/">https://developer.wordpress.org/reference/functions/get_user_by/</a>',
			),
		);

		return array(
			'action'			=> 'get_user',
			'name'			  => __( 'Get user', 'wp-webhooks' ),
			'sentence'			  => __( 'get a user', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Returns the object of a user', 'wp-webhooks' ),
			'description'	   => $description,
			'integration'	   => 'wordpress',
			'premium' 			=> false,
		);

	}

		/**
		 * Get certain users using WP_User_Query
		 */
		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'	 => '',
				'data' => array()
			);

			$user_value	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_value' );
			$value_type	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'value_type' );
			$do_action   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );
			$user = null;

			if( empty( $user_value ) ){
				$return_args['msg'] = __( "It is necessary to define the user_value argument. Please define it first.", 'action-get_user-failure' );

				return $return_args;
			}

			if( empty( $value_type ) ){
				$value_type = 'id';
			}

			if( ! empty( $user_value ) && ! empty( $value_type ) ){
				$user = get_user_by( $value_type, $user_value );

				if ( is_wp_error( $user ) ) {
					$return_args['msg'] = __( $user->get_error_message(), 'wp-webhooks' );
				} else {

					if( ! empty( $user ) && ! is_wp_error( $user ) ){

						$user_meta = array();
						if( isset( $user->ID ) ){
							$user_meta = get_user_meta( $user->ID );
						}

						$return_args['msg'] = __( "User was successfully returned.", 'wp-webhooks' );
						$return_args['success'] = true;
						$return_args['data'] = $user;
						$return_args['user_meta'] = $user_meta;
						$return_args['user_posts_url'] = get_author_posts_url( $user->ID );

						if( WPWHPRO()->helpers->is_plugin_active( 'advanced-custom-fields' ) ){
							$return_args['acf_meta'] = get_fields( 'user_' . $user->ID );
						}

					} else {
						$return_args['data'] = $user;
						$return_args['msg'] = __( "No user found.", 'wp-webhooks' );
					}

				}

			} else {
				$return_args['msg'] = __( "There is an issue with your defined arguments. Please check them first.", 'wp-webhooks' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $user_value, $value_type, $user );
			}

			return $return_args;
		}

	}

endif; // End if class_exists check.