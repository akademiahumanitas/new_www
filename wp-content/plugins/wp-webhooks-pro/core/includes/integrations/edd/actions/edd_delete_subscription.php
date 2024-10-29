<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_delete_subscription' ) ) :

	/**
	 * Load the edd_delete_subscription action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_delete_subscription {

        public function is_active(){

            $is_active = defined( 'EDD_RECURRING_PRODUCT_NAME' );

            //Backwards compatibility for the "Easy Digital Downloads" integration
            if( defined( 'WPWH_EDD_NAME' ) ){
                $is_active = false;
            }

            return $is_active;
        }

        public function get_details(){

            $parameter = array(
				'subscription_id'       => array( 'required' => true, 'short_description' => __( '(Integer) The id of the subscription you would like to delete.', 'wp-webhooks' ) ),
				'keep_payment_meta'       => array( 
					'type' => 'select',
					'choices' => array(
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					),
					'multiple' => false,
					'default_value' => 'no',
					'short_description' => __( '(String) Set this value to "yes" if you do not want to delet the relation of the subscription on the related payment. Default: no', 'wp-webhooks' ),
				),
				'keep_list_of_trials'       => array( 
					'type' => 'select',
					'choices' => array(
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					),
					'multiple' => false,
					'default_value' => 'no',
					'short_description' => __( '(String) Set this value to "yes" to delete the list of trials of the user that are related to the given subscription id. Default: no', 'wp-webhooks' ),
				),
				'do_action'     => array( 'short_description' => __( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More info is within the description.', 'wp-webhooks' ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'        => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'        => array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'        => array( 'short_description' => __( '(Array) Containing the new susbcription id and other arguments set during the deletion of the subscription.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The subscription was successfully deleted.',
				'data' => 
				array (
				  'subscription_id' => 21,
				  'keep_payment_meta' => false,
				  'keep_list_of_trials' => false,
				),
			);

			ob_start();
			?>
<?php echo __( "The id of the subscription you would like to delete. Please note that the subscription needs to be existent, otherwise we will throw an error.", 'wp-webhooks' ); ?>
			<?php
			$parameter['subscription_id']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "Set this value to <strong>yes</strong> to keep the payment meta (meta key on the payment: _edd_subscription_payment). Usually, it makes sense to remove this relation as well. That's why this value is deleted by default. Please only set it to <strong>yes</strong> in case you need to keep the meta key.", 'wp-webhooks' ); ?>
			<?php
			$parameter['keep_payment_meta']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "Set this value to <strong>yes</strong> to keep the meta entry for the list of trials (meta key on the user: edd_recurring_trials). Usually, it makes sense to remove this relation as well. That's why this value is deleted by default. Please only set it to <strong>yes</strong> in case you need to keep the meta key.", 'wp-webhooks' ); ?>
			<?php
			$parameter['keep_list_of_trials']['description'] = ob_get_clean();

			$description = array(
				'tipps' => array(
					__( "By default, we properly erase the subsription including the relations on the customer and payments.", 'wp-webhooks' ),
				),
			);

            return array(
                'action'            => 'edd_delete_subscription',
                'name'              => __( 'Delete subscription', 'wp-webhooks' ),
                'sentence'              => __( 'delete a subscription', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => __( 'This webhook action allows you to delete a subscription within Easy Digital Downloads - Recurring.', 'wp-webhooks' ),
                'description'       => $description,
                'integration'       => 'edd',
                'premium' 			=> false,
            );

        }

        public function execute( $return_data, $response_body ){

            $return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'subscription_id' => 0,
					'keep_payment_meta' => false,
					'keep_list_of_trials' => false,
				),
			);

			$subscription_id   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'subscription_id' ) );
			$keep_payment_meta   = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'keep_payment_meta' ) === 'yes' ) ? true : false;
			$keep_list_of_trials   = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'keep_list_of_trials' ) === 'yes' ) ? true : false;
			
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( ! class_exists( 'EDD_Subscription' ) ){
				$return_args['msg'] = __( 'The class EDD_Subscription() does not exist. The subscription was not deleted.', 'wp-webhooks' );
				return $return_args;
			}

			$subscription = new EDD_Subscription( $subscription_id );
			if( empty( $subscription ) ){
				$return_args['msg'] = __( 'Error: Invalid subscription id provided.', 'wp-webhooks' );
				return $return_args;
			}

			if( ! $keep_payment_meta && isset( $subscription->parent_payment_id ) ){
				delete_post_meta( $subscription->parent_payment_id, '_edd_subscription_payment' );
			}

			// Delete subscription from list of trials customer has used
			if( ! $keep_list_of_trials && isset( $subscription->product_id ) ){
				$subscription->customer->delete_meta( 'edd_recurring_trials', $subscription->product_id );
			}

			$check = $subscription->delete();

			if( $check ){
				$return_args['msg'] = __( "The subscription was successfully deleted.", 'action-edd_delete_subscription-success' );
				$return_args['success'] = true;
				$return_args['data']['subscription_id'] = $subscription_id;
				$return_args['data']['keep_payment_meta'] = $keep_payment_meta;
				$return_args['data']['keep_list_of_trials'] = $keep_list_of_trials;
			} else {
				$return_args['msg'] = __( "Error deleting the subscription.", 'action-edd_delete_subscription-success' );
			}
		
			

			if( ! empty( $do_action ) ){
				do_action( $do_action, $subscription_id, $subscription, $return_args );
			}

			return $return_args;
    
        }

    }

endif; // End if class_exists check.