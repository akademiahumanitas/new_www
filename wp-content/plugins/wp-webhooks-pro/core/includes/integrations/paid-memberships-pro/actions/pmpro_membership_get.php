<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_paid_memberships_pro_Actions_pmpro_membership_get' ) ) :

	/**
	 * Load the pmpro_membership_get action
	 *
	 * @since 4.2.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_paid_memberships_pro_Actions_pmpro_membership_get {

	public function get_details(){

		$parameter = array(
			'user' => array( 'required' => true, 'short_description' => __( 'The ID or email of the user you want to get the membership level from.', 'wp-webhooks' ) ),
			'do_action' => array( 'short_description' => __( 'Advanced: Register a custom action after the plugin fires this webhook.', 'wp-webhooks' ) )
		);

		$returns = array(
			'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
			'msg' => array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			'membership' => array( 'short_description' => __( '(array) Every data related to the assigned membership." )', 'wp-webhooks' ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The memberships have been returned successfully.',
			'membership' => 
			array (
			  'ID' => '2',
			  'id' => '2',
			  'subscription_id' => '13',
			  'name' => 'Second Level',
			  'description' => 'the second level',
			  'confirmation' => '',
			  'expiration_number' => '0',
			  'expiration_period' => '',
			  'allow_signups' => '1',
			  'initial_payment' => 0,
			  'billing_amount' => 0,
			  'cycle_number' => '0',
			  'cycle_period' => '',
			  'billing_limit' => '0',
			  'trial_amount' => 0,
			  'trial_limit' => '0',
			  'code_id' => '0',
			  'startdate' => '1626965948',
			  'enddate' => NULL,
			),
		);

			ob_start();
		?>
<?php echo __( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the pmpro_membership_get action was fired.", 'wp-webhooks' ); ?>
<br>
<?php echo __( "You can use it to trigger further logic after the webhook action. Here's an example:", 'wp-webhooks' ); ?>
<br>
<br>
<?php echo __( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", 'wp-webhooks' ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $user_id, $user_level, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo __( "Here's an explanation to each of the variables that are sent over within the custom function.", 'wp-webhooks' ); ?>
<ol>
	<li>
		<strong>$user_id</strong> (integer)<br>
		<?php echo __( "The ID of the user you get the membership level from.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$user_level</strong> (array)<br>
		<?php echo __( "The assigned user level.", 'wp-webhooks' ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo __( "An array containing the information we will send back as the response to the initial webhook caller.", 'wp-webhooks' ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			return array(
				'action'			=> 'pmpro_membership_get',
				'name'			  => __( 'Get user membership', 'wp-webhooks' ),
				'sentence'			  => __( 'get a user membership', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Get the current membership of a user within "Paid Memberships Pro".', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'paid-memberships-pro',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'membership' => array(),
			);
	
			$user_id = null;
			$user = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$do_action = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( is_numeric( $user ) ){
				$user_id = intval( $user );
			} else {
				$email = sanitize_email( $user );
				$user = get_user_by( 'email', $email );
				if( ! empty( $user ) ){
					if( ! empty( $user->ID ) ){
						$user_id = $user->ID;
					}
				}
			}

			if( empty( $user_id ) ){
				$return_args['msg'] = __( "We could not find any user for the value of the user argument.", 'wp-webhooks' );
				return $return_args;
			}

			$return_args['success'] = true;
			$user_level = pmpro_getMembershipLevelForUser( $user_id );
			if( empty( $user_level ) ){
				$return_args['msg'] = __( "The given user does not have any memberships.", 'wp-webhooks' );
			} else {
				$return_args['msg'] = __( "The membership has been returned successfully.", 'wp-webhooks' );
				$return_args['membership'] = $user_level;
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $user_id, $user_level, $return_args );
			}
	
			return $return_args;
	
		}

	}

endif; // End if class_exists check.