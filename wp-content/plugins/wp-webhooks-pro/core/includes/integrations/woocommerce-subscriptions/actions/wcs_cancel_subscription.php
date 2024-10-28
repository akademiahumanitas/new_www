<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_subscriptions_Actions_wcs_cancel_subscription' ) ) :

	/**
	 * Load the wcs_cancel_subscription action
	 *
	 * @since 5.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_woocommerce_subscriptions_Actions_wcs_cancel_subscription {

		public function get_details(){

				$parameter = array(
				'user'		=> array( 
					'required' => true, 
					'label' => __( 'User ID/email', 'wp-webhooks' ), 
					'short_description' => __( 'The user you want to assign the membership to. This argument accepts either the user ID or the user email.', 'wp-webhooks' )
				),
				'subscription_ids'		=> array( 
					'label' => __( 'Subscription IDs', 'wp-webhooks' ), 
					'short_description' => __( 'A comma-separated list of subscription IDs for which you want to cancel the subscriptions. Please note: In case the subscription contains more products, they will be cancelled as well.', 'wp-webhooks' )
				),
				'product_ids'		=> array( 
					'label' => __( 'Product IDs', 'wp-webhooks' ), 
					'short_description' => __( 'A comma-separated list of product IDs for which you want to cancel the subscriptions. Please note: In case the subscription contains more products, they will be cancelled as well.', 'wp-webhooks' )
				),
				'cancel_all'		=> array( 
					'type' => 'checkbox',
					'label' => __( 'Cancel all', 'wp-webhooks' ), 
					'short_description' => __( 'Cancel all subscriptions for the given user.', 'wp-webhooks' )
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired actions.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The user subscriptions have been successfully cancelled.',
				'data' => 
				array (
				'user_id' => 8,
				'cancelled' => array(
					9278
				),
				'errors' => array(),
				),
			);

			return array(
				'action'			=> 'wcs_cancel_subscription', //required
				'name'			   => __( 'Cancel user subscription', 'wp-webhooks' ),
				'sentence'			   => __( 'cancel one or multiple user subscriptions', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Cancel one or multiple user subscriptions within WooCommerce Subscriptions.', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'woocommerce-subscriptions',
				'premium'	   	=> true,
			);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'user_id' => 0,
					'cancelled' => array(),
					'errors' => array(),
				)
			);

			$user = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$subscription_ids = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'subscription_ids' );
			$product_ids = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'product_ids' );
			$cancel_all = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'cancel_all' ) === 'yes' ) ? true : false;

			if( empty( $user ) ){
				$return_args['msg'] = __( "Please set the user argument.", 'action-wcs_cancel_subscription-error' );
				return $return_args;
			}

			$validated_subscription_ids = array();
			if( ! empty( $subscription_ids ) ){
				$validated_subscription_ids = array_map( 'intval', array_map( 'trim' , explode( ',', $subscription_ids ) ) );
			}

			$validated_product_ids = array();
			if( ! empty( $product_ids ) ){
				$validated_product_ids = array_map( 'intval', array_map( 'trim' , explode( ',', $product_ids ) ) );
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
                $return_args['msg'] = __( "We could not find a user for your given user id/email.", 'action-wpfs_add_tags-error' );
				return $return_args;
            }

			$subscriptions = wcs_get_users_subscriptions( $user_id );

            if( empty( $subscriptions ) ){
                $return_args['msg'] = __( "There are no subscriptions given for your current user.", 'action-wpfs_add_tags-error' );
				return $return_args;
            }

			$subs_to_cancel = array();
			$cancelled = array();
			$errors = array();

			foreach( $subscriptions as $subscription ){
				$sub_id = $subscription->get_id();	

				if( ! $cancel_all ){
					if( in_array( $sub_id, $validated_subscription_ids ) ){
						$subs_to_cancel[] = $subscription;
						continue;
					}
				} else {
					$subs_to_cancel[] = $subscription;
				}

				$items = $subscription->get_items();
				if( ! empty( $items ) ){
					foreach( $items as $index => $item ){

						$product_id = $item->get_product_id();

						if( in_array( $product_id, $validated_product_ids ) ){
							$subs_to_cancel[] = $subscription;
							break;
						}

						
					}
				}
				
			}

			if( ! empty( $subs_to_cancel ) ){
				foreach( $subs_to_cancel as $sub ){
					if( $sub->has_status( array( 'active' ) ) ){

						if( $sub->can_be_updated_to( 'cancelled' ) ){
							$sub->update_status( 'cancelled' );
							$cancelled[] = $sub->get_id();
							break;
						} else {
							$errors[] = sprintf( __( "WooCommerce Subscriptions prevented the cancellation of the subscription #%$1d", 'action-wpfs_add_tags-error' ), $sub->get_id() );
						}
						
					}
				}
			}

			$return_args['data']['user_id'] = $user_id;
			
			if( empty( $errors ) ){
				$return_args['success'] = true;
				$return_args['msg'] = __( "The user subscriptions have been successfully cancelled.", 'action-wcs_cancel_subscription-success' );
				$return_args['data']['cancelled'] = $cancelled;
				$return_args['data']['errors'] = $errors;
			} else {
				$return_args['msg'] = __( "Some of the subscription could not be cancelled.", 'action-wcs_cancel_subscription-success' );
				$return_args['data']['cancelled'] = $cancelled;
				$return_args['data']['errors'] = $errors;
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.