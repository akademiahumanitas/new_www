<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_restrict_content_pro_Actions_rcp_renew_membership' ) ) :

	/**
	 * Load the rcp_renew_membership action
	 *
	 * @since 4.3.6
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_restrict_content_pro_Actions_rcp_renew_membership {

	public function get_details(){

			$parameter = array(
				'user'		=> array( 'required' => true, 'short_description' => __( 'Set this argument to the id of the user. In case you do not have the user id, you can also assign the user via a given email.', 'wp-webhooks' ) ),
				'membership_level'	=> array( 'required' => true, 'short_description' => __( 'The ID of the membership level that you want to renew. Set this argument to all to renew all memberships.', 'wp-webhooks' ) ),
				'recurring'	=> array( 'required' => true, 'short_description' => __( 'Set this argument to yes if your membership is recurring. Default: no', 'wp-webhooks' ) ),
				'status'	=> array( 'required' => true, 'short_description' => __( 'Customize the status of the membership. Default: active', 'wp-webhooks' ) ),
				'expiration_date'	=> array( 'required' => true, 'short_description' => __( 'Set a custom expiration date for the membership. If not set, we automatically calculate the expiration date based on the membership.', 'wp-webhooks' ) ),
				'do_action'	  => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired actions.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<?php echo __( "You can set this argument to <code>all</code> to renew all memberships for the user instead.", 'wp-webhooks' ); ?>
		<?php
		$parameter['membership_level']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>rcp_renew_membership</strong> action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 2 );
function my_custom_callback_function( $return_args, $data ){
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
		<strong>$data</strong> (array)<br>
		<?php echo __( "The data used to renew the membership.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array(
			"success" => true,
			"msg" => "The memberships have been successfully renewed.",
			"data" => array(
				"customer_id" => "12",
				"user_id" => 140,
				"renewed" => [
					14
				],
				"renewed_objects" => [
					array(
						"membership_id" => "14",
						"user_id" => 140,
						"user" => array(
							"data" => array(
								"ID" => "140",
								"user_login" => "jondoe",
								"user_pass" => "THE_HASHED_USER_PASSWORD",
								"user_nicename" => "Jon Doe",
								"user_email" => "jondoe@demo.test",
								"user_url" => "",
								"user_registered" => "2021-05-28 10:48:40",
								"user_activation_key" => "",
								"user_status" => "0",
								"display_name" => "jondoe",
								"spam" => "0",
								"deleted" => "0"
							),
							"ID" => 140,
							"caps" => array(
								"homey_host" => true,
								"subscriber" => true
							),
							"cap_key" => "wp_capabilities",
							"roles" => [
								"homey_host",
								"subscriber"
							],
							"allcaps" => array(
								"read" => true,
								"edit_posts" => false,
								"delete_posts" => false,
								"read_listing" => true,
								"publish_posts" => false,
								"edit_listing" => true,
								"create_listings" => true,
								"edit_listings" => true,
								"delete_listings" => true,
								"edit_published_listings" => true,
								"publish_listings" => true,
								"delete_published_listings" => true,
								"delete_private_listings" => true,
								"level_0" => true,
								"read_private_locations" => true,
								"read_private_events" => true,
								"manage_resumes" => true,
								"homey_host" => true,
								"subscriber" => true
							),
							"filter" => null
						),
						"membership" => array(
							"customer_id" => "12",
							"customer" => array(
								"id" => "12",
								"user_id" => "140",
								"date_registered" => "February 22, 2022",
								"email_verification_status" => "none",
								"last_login" => "",
								"ips" => [],
								"has_trialed" => false,
								"notes" => "",
								"is_pending_verification" => false,
								"has_active_membership" => true,
								"has_paid_membership" => true,
								"lifetime_value" => 0
							),
							"membership_level_name" => "Demo Level Paid",
							"currency" => "USD",
							"initial_amount" => "10",
							"recurring_amount" => "10",
							"biling_cycle_formatted" => "&#36;10.00",
							"status" => "active",
							"expiration_date" => "none",
							"expiration_time" => false,
							"created_date" => "February 22, 2022",
							"activated_date" => "2022-02-22 09:28:45",
							"trial_end_date" => null,
							"renewed_date" => "February 26, 2022",
							"cancellation_date" => "February 22, 2022",
							"times_billed" => 0,
							"maximum_renewals" => "0",
							"gateway" => "wpwh",
							"gateway_customer_id" => "",
							"gateway_subscription_id" => "",
							"subscription_key" => "8bbc1b18ba278dc1ed922bbfa51716e4",
							"get_upgraded_from" => "0",
							"was_upgrade" => false,
							"payment_plan_completed_date" => null,
							"notes" => "February 22, 2022 09:28:45 - Membership activated.\n\nFebruary 22, 2022 10:16:32 - Status changed from active to cancelled.\n\nFebruary 26, 2022 07:54:17 - Status changed from cancelled to expired.\n\nFebruary 26, 2022 07:54:17 - Expiration Date changed from  to 2022-02-25 07:54:17.\n\nFebruary 26, 2022 07:54:52 - Membership disabled.\n\nFebruary 26, 2022 08:16:37 - Expiration Date changed from 2022-02-25 07:54:17 to .\n\nFebruary 26, 2022 08:16:37 - Status changed from expired to active.\n\nFebruary 26, 2022 08:16:37 - Membership renewed.",
							"signup_method" => "live",
							"prorate_credit_amount" => 0,
							"payments" => [],
							"card_details" => []
						),
						"membership_level" => array(
							"id" => 2,
							"name" => "Demo Level Paid",
							"description" => "",
							"is_lifetime" => true,
							"duration" => 0,
							"duration_unit" => "day",
							"has_trial" => false,
							"trial_duration" => 0,
							"trial_duration_unit" => "day",
							"get_price" => 10,
							"is_free" => false,
							"fee" => 0,
							"renewals" => 0,
							"access_level" => 0,
							"status" => "active",
							"role" => "subscriber",
							"get_date_created" => "2022-02-21 10:38:07"
						)
					)
				]
			)
		);

		return array(
			'action'			=> 'rcp_renew_membership', //required
			'name'			   => __( 'Renew user membership', 'wp-webhooks' ),
			'sentence'			   => __( 'renew one or all user memberships', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Renew one or all memberships for a user within Restrict Content Pro.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'restrict-content-pro',
			'premium'	   	=> true,
		);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array()
			);

			$user		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$membership_level		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'membership_level' );
			$recurring		= ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'recurring' ) === 'yes' ) ? true : false;
			$status		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'status' );
			$expiration_date		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'expiration_date' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $user ) ){
				$return_args['msg'] = __( "Please set the user argument to either the user id or user email of an existing user.", 'action-rcp_renew_membership-error' );
				return $return_args;
			}

			if( empty( $membership_level ) ){
				$return_args['msg'] = __( "Please set the membership_level argument.", 'action-rcp_renew_membership-error' );
				return $return_args;
			}

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
                $return_args['msg'] = __( "We could not find a user for your given user argument value.", 'action-rcp_renew_membership-error' );
				return $return_args;
            }

            $customer = rcp_get_customer_by_user_id( $user_id );

			if( empty( $customer ) ){
                $return_args['msg'] = __( "There was an issue retrieving the customer.", 'action-rcp_renew_membership-error' );
				return $return_args;
            }

			if( $membership_level === 'all' ){
				$memberships = rcp_get_memberships( array(
					'customer_id' => absint( $customer->get_id() ),
					'number'      => 999,
				) );
			} else {
				$memberships = rcp_get_memberships( array(
					'customer_id' => absint( $customer->get_id() ),
					'object_id'   => $membership_level,
					'number'      => 999,
				) );
			}

			if( empty( $status ) ){
				$status = 'active';
			}

			if( empty( $expiration_date ) ){
				$expiration_date = '';
			} else {
				$expiration_date = WPWHPRO()->helpers->get_formatted_date( $expiration_date, 'Y-m-d H:i:s' );
			}

			$rcp_helpers = WPWHPRO()->integrations->get_helper( 'restrict-content-pro', 'rcp_helpers' );

			$renewed = array();
			$renewed_objects = array();
			if( ! empty( $memberships ) ){
				foreach( $memberships as $membership ){
					$membership->renew( $recurring, $status, $expiration_date );
					$renewed[] = intval( $membership->get_id() );
					$renewed_objects[] = $rcp_helpers->build_payload( $membership );
				}
			}

			$return_args['success'] = true;
			$return_args['msg'] = __( "The memberships have been successfully renewed.", 'action-rcp_renew_membership-success' );
			$return_args['data']['customer_id'] = $customer->get_id();
			$return_args['data']['user_id'] = $user_id;
			$return_args['data']['renewed'] = $renewed;
			$return_args['data']['renewed_objects'] = $renewed_objects;

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.