<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_memberships_Actions_wcm_add_user_membership' ) ) :

	/**
	 * Load the wcm_add_user_membership action
	 *
	 * @since 4.3.7
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_woocommerce_memberships_Actions_wcm_add_user_membership {

	public function get_details(){

			$parameter = array(
				'user'		=> array( 'required' => true, 'short_description' => __( 'The user you want to assign the membership to. This argument accepts either the user ID or the user email.', 'wp-webhooks' ) ),
				'membership_plan_id'	=> array( 'required' => true, 'short_description' => __( 'The membership plan id.', 'wp-webhooks' ) ),
				'product_id'	=> array( 'short_description' => __( 'A product ID if you want to connect the memberhip with a product.', 'wp-webhooks' ) ),
				'order_id'	=> array( 'short_description' => __( 'The order ID if you want to connect the membership with an order.', 'wp-webhooks' ) ),
				'do_action'	  => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired actions.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			ob_start();
		?>
<?php echo __( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>wcm_add_user_membership</strong> action was fired.", 'wp-webhooks' ); ?>
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
			'msg' => 'The user membership has been successfully created.',
			'data' => 
			array (
			  'membership_id' => 9146,
			  'membership_plan_id' => 9143,
			  'user_id' => 8,
			  'product_id' => 0,
			  'order_id' => 0,
			),
		  );

		return array(
			'action'			=> 'wcm_add_user_membership', //required
			'name'			   => __( 'Add user membership', 'wp-webhooks' ),
			'sentence'			   => __( 'add a user membership', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Add a user membership within WooCommerce Memberships.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'woocommerce-memberships',
			'premium'	   	=> true,
		);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'membership_id' => 0,
					'membership_plan_id' => 0,
					'user_id' => 0,
					'product_id' => 0,
					'order_id' => 0,
				)
			);

			$user = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$membership_plan_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'membership_plan_id' ) );
			$product_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'product_id' ) );
			$order_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'order_id' ) );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $user ) ){
				$return_args['msg'] = __( "Please set the user argument.", 'action-wcm_add_user_membership-error' );
				return $return_args;
			}

			if( empty( $membership_plan_id ) ){
				$return_args['msg'] = __( "Please set the membership_plan_id argument.", 'action-wcm_add_user_membership-error' );
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
                $return_args['msg'] = __( "We could not find a user for your given user id.", 'action-wpfs_add_tags-error' );
				return $return_args;
            }

            if( wc_memberships_is_user_member( $user_id, $membership_plan_id ) ){
                $return_args['msg'] = __( "Your user is already a member of that membership plan.", 'action-wpfs_add_tags-error' );
				return $return_args;
            }

			$args = array(
				'plan_id' => $membership_plan_id,
				'user_id' => $user_id
			);

			if( ! empty( $product_id ) ){
				$args['product_id'] = $product_id;
			}

			if( ! empty( $order_id ) ){
				$args['order_id'] = $order_id;
			}

			$user_membership = wc_memberships_create_user_membership( $args );
			
			if( $user_membership ){
				$return_args['success'] = true;
				$return_args['msg'] = __( "The user membership has been successfully created.", 'action-wcm_add_user_membership-success' );
				$return_args['data']['membership_id'] = $user_membership->get_id();
				$return_args['data']['membership_plan_id'] = $membership_plan_id;
				$return_args['data']['user_id'] = $user_id;
				$return_args['data']['product_id'] = $product_id;
				$return_args['data']['order_id'] = $order_id;
			} else {
				$return_args['msg'] = __( "An error occured while creating the membership.", 'action-wcm_add_user_membership-success' );
				$return_args['data']['membership_plan_id'] = $membership_plan_id;
				$return_args['data']['user_id'] = $user_id;
				$return_args['data']['product_id'] = $product_id;
				$return_args['data']['order_id'] = $order_id;
			}
			

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.