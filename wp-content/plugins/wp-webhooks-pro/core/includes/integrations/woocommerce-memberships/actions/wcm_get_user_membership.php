<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_memberships_Actions_wcm_get_user_membership' ) ) :

	/**
	 * Load the wcm_get_user_membership action
	 *
	 * @since 5.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_woocommerce_memberships_Actions_wcm_get_user_membership {

		public function get_details(){

				$parameter = array(
				'membership_id'		=> array( 'required' => true, 'short_description' => __( 'The ID of the membership you want to fetch.', 'wp-webhooks' ) ),
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired actions.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The user membership has been successfully returned.',
				'data' => 
				array (
				'membership_id' => 9148,
				'membership' => 
				array (
					'id' => 9148,
					'customer_id' => 72,
					'plan_id' => 9143,
					'status' => 'active',
					'order_id' => NULL,
					'product_id' => NULL,
					'subscription_id' => NULL,
					'date_created' => '2022-03-11T12:50:10',
					'date_created_gmt' => '2022-03-11T12:50:10',
					'start_date' => '2022-03-11T00:00:00',
					'start_date_gmt' => '2022-03-11T00:00:00',
					'end_date' => NULL,
					'end_date_gmt' => NULL,
					'paused_date' => NULL,
					'paused_date_gmt' => NULL,
					'cancelled_date' => NULL,
					'cancelled_date_gmt' => NULL,
					'view_url' => 'https://yourdomain.test/members-area/9143/my-membership-content/',
					'profile_fields' => 
					array (
					),
					'meta_data' => 
					array (
					),
				),
				),
			);

			return array(
				'action'			=> 'wcm_get_user_membership', //required
				'name'			   => __( 'Get user membership', 'wp-webhooks' ),
				'sentence'			   => __( 'get a user membership', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Get a user membership within WooCommerce Memberships.', 'wp-webhooks' ),
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
					'membership' => array(),
				)
			);

			$wcm_helpers = WPWHPRO()->integrations->get_helper( 'woocommerce-memberships', 'wcm_helpers' );
			$membership_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'membership_id' ) );

			if( empty( $membership_id ) ){
				$return_args['msg'] = __( "Please set the membership_id argument.", 'action-wcm_get_user_membership-error' );
				return $return_args;
			}

			$membership = $wcm_helpers->get_formatted_item_data( $membership_id );
			
			if( $membership ){
				$return_args['success'] = true;
				$return_args['msg'] = __( "The user membership has been successfully returned.", 'action-wcm_get_user_membership-success' );
				$return_args['data']['membership_id'] = $membership_id;
				$return_args['data']['membership'] = $membership;
			} else {
				$return_args['msg'] = __( "We could not find a valid membership for your given id.", 'action-wcm_get_user_membership-success' );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.