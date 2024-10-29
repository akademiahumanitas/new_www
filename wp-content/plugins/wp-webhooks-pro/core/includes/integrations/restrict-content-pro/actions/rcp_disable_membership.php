<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_restrict_content_pro_Actions_rcp_disable_membership' ) ) :

	/**
	 * Load the rcp_disable_membership action
	 *
	 * @since 4.3.6
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_restrict_content_pro_Actions_rcp_disable_membership {

	public function get_details(){

			$parameter = array(
				'user'		=> array( 'required' => true, 'short_description' => __( 'Set this argument to the id of the user. In case you do not have the user id, you can also assign the user via a given email.', 'wp-webhooks' ) ),
				'membership_level'	=> array( 'required' => true, 'short_description' => __( 'The ID of the membership level that you want to disable. Set this argument to all to disable all memberships.', 'wp-webhooks' ) ),
				'do_action'	  => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired actions.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<?php echo __( "You can set this argument to <code>all</code> to disable all memberships for the user instead.", 'wp-webhooks' ); ?>
		<?php
		$parameter['membership_level']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>rcp_disable_membership</strong> action was fired.", 'wp-webhooks' ); ?>
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
		<?php echo __( "The data used to disable the membership.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'The memberships have been successfully disabled.',
			'data' => 
			array (
			  'customer_id' => '12',
			  'user_id' => 140,
			  'disabled' => array(
				  14
			  ),
			),
		);

		$description = array(
			'tipps' => array(
				__( 'Disabling a membership does the following:', 'wp-webhooks' ),
				__( 'Cancels the payment profile to stop recurring billing.', 'wp-webhooks' ),
				__( 'Disables the membership so the customer loses access to associated content.', 'wp-webhooks' ),
				__( 'Removes the associated role from the user account.', 'wp-webhooks' ),
				__( 'Hides the membership from the customer so it can no longer be renewed or cancelled.', 'wp-webhooks' ),
				__( 'This is basically a way of deleting the membership while still keeping it in the database.', 'wp-webhooks' ),
			),
		);

		return array(
			'action'			=> 'rcp_disable_membership', //required
			'name'			   => __( 'Disable user membership', 'wp-webhooks' ),
			'sentence'			   => __( 'disable one or all user memberships', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Disable one or all memberships for a user within Restrict Content Pro.', 'wp-webhooks' ),
			'description'	   => $description,
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
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $user ) ){
				$return_args['msg'] = __( "Please set the user argument to either the user id or user email of an existing user.", 'action-rcp_disable_membership-error' );
				return $return_args;
			}

			if( empty( $membership_level ) ){
				$return_args['msg'] = __( "Please set the membership_level argument.", 'action-rcp_disable_membership-error' );
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
                $return_args['msg'] = __( "We could not find a user for your given user argument value.", 'action-rcp_disable_membership-error' );
				return $return_args;
            }

            $customer = rcp_get_customer_by_user_id( $user_id );

			if( empty( $customer ) ){
                $return_args['msg'] = __( "There was an issue retrieving the customer.", 'action-rcp_disable_membership-error' );
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

			$disabled = array();
			if( ! empty( $memberships ) ){
				foreach( $memberships as $membership ){
					$membership->disable();
					$disabled[] = intval( $membership->get_id() );
				}
			}

			$return_args['success'] = true;
			$return_args['msg'] = __( "The memberships have been successfully disabled.", 'action-rcp_disable_membership-success' );
			$return_args['data']['customer_id'] = $customer->get_id();
			$return_args['data']['user_id'] = $user_id;
			$return_args['data']['disabled'] = $disabled;

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.