<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_update_subscription' ) ) :

	/**
	 * Load the edd_update_subscription action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_update_subscription {

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
				'subscription_id'       => array( 'required' => true, 'short_description' => __( '(Integer) The id of the subscription you would like to update.', 'wp-webhooks' ) ),
				'expiration_date'       => array( 'short_description' => __( '(String) The date for the expiration of the subscription. Recommended format: 2021-05-25 11:11:11', 'wp-webhooks' ) ),
				'profile_id'       => array( 'short_description' => __( '(String) This is the unique ID of the subscription in the merchant processor, such as PayPal or Stripe.', 'wp-webhooks' ) ),
				'download_id'       => array( 'short_description' => __( '(Integer) The ID of the download you want to connect with the subscription.', 'wp-webhooks' ) ),
				'customer_email'       => array( 'short_description' => __( '(String) The email of the customer in case you do not have the customer id. Please see the description for further details.', 'wp-webhooks' ) ),
				'period'       => array( 'short_description' => __( '(String) The billing period of the subscription. Please see the description for further details.', 'wp-webhooks' ) ),
				'initial_amount'       => array( 'short_description' => __( '(Mixed) The amount for the initial payment. E.g. 39.97', 'wp-webhooks' ) ),
				'recurring_amount'       => array( 'short_description' => __( '(Mixed) The recurring amount for the subscription. E.g. 19.97', 'wp-webhooks' ) ),
				'transaction_id'       => array( 'short_description' => __( '(String) This is the unique ID of the initial transaction inside of the merchant processor, such as PayPal or Stripe.', 'wp-webhooks' ) ),
				'status'       => array( 'short_description' => __( '(String) The status of the given subscription. Please see the description for further details.', 'wp-webhooks' ) ),
				'created_date'       => array( 'short_description' => __( '(String) The date of creation of the subscription. Recommended format: 2021-05-25 11:11:11', 'wp-webhooks' ) ),
				'bill_times'       => array( 'short_description' => __( '(Integer) This refers to the number of times the subscription will be billed before being marked as Completed and payments stopped. Enter 0 if payments continue indefinitely.', 'wp-webhooks' ) ),
				'parent_payment_id'       => array( 'short_description' => __( '(Integer) Use this argument to connect the subscription with an already existing payment. Please see the description for further details.', 'wp-webhooks' ) ),
				'customer_id'       => array( 'short_description' => __( '(Integer) The id of the customer you want to connect. If it is not given, we try to fetch the user from the customer_email argument. Please see the description for further details.', 'wp-webhooks' ) ),
				'edd_price_option'       => array( 'short_description' => __( '(Integer) The variation id for a download price option. Please see the description for further details.', 'wp-webhooks' ) ),
				'initial_tax_rate'       => array( 'short_description' => __( '(Integer) The percentage for your initial tax rate. Please see the description for further details.', 'wp-webhooks' ) ),
				'initial_tax'       => array( 'short_description' => __( '(Float) The amount of tax for your initial tax amount. Please see the description for further details.', 'wp-webhooks' ) ),
				'recurring_tax_rate'       => array( 'short_description' => __( '(Integer) The percentage for your recurring tax rate. Please see the description for further details.', 'wp-webhooks' ) ),
				'recurring_tax'       => array( 'short_description' => __( '(Float) The amount of tax for your recurring tax amount. Please see the description for further details.', 'wp-webhooks' ) ),
				'notes'       => array( 'short_description' => __( '(String) A JSON formatted string containing one or multiple subscription notes. Please check the description for further details.', 'wp-webhooks' ) ),
				'do_action'     => array( 'short_description' => __( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More info is within the description.', 'wp-webhooks' ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'        => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'        => array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'        => array( 'short_description' => __( '(Array) Containing the new susbcription id, the payment id, customer id, as well as further details about the subscription.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'subscription_id' => 0,
					'payment_id' => 0,
					'customer_id' => 0,
				),
			);

            $default_subscription_statuses = array (
                'pending' => __( 'Pending', 'wp-webhooks' ),
                'active' => __( 'Active', 'wp-webhooks' ),
                'cancelled' => __( 'Cancelled', 'wp-webhooks' ),
                'expired' => __( 'Expired', 'wp-webhooks' ),
                'trialling' => __( 'Trialling', 'wp-webhooks' ),
                'failing' => __( 'Failing', 'wp-webhooks' ),
                'completed' => __( 'Completed', 'wp-webhooks' ),
            );
            $default_subscription_statuses = apply_filters( 'wpwh/descriptions/actions/edd_update_subscription/default_subscription_statuses', $default_subscription_statuses );
            $beautified_subscription_statuses = json_encode( $default_subscription_statuses, JSON_PRETTY_PRINT );
            
            $default_subscription_periods = array (
                'day' => __( 'Daily', 'wp-webhooks' ),
                'week' => __( 'Weekly', 'wp-webhooks' ),
                'month' => __( 'Monthly', 'wp-webhooks' ),
                'quarter' => __( 'Quarterly', 'wp-webhooks' ),
                'semi-year' => __( 'Semi-Yearly', 'wp-webhooks' ),
                'year' => __( 'Yearly', 'wp-webhooks' ),
            );
            $default_subscription_periods = apply_filters( 'wpwh/descriptions/actions/edd_update_subscription/default_subscription_periods', $default_subscription_periods );
            $beautified_subscription_periods = json_encode( $default_subscription_periods, JSON_PRETTY_PRINT );
            
			ob_start();
			?>
<?php echo __( "The id of the subscription you would like to update. Please note that the subscription needs to be existent, otherwise we will throw an error.", 'wp-webhooks' ); ?>
			<?php
			$parameter['subscription_id']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "This argument accepts a date string what contains the date of expiration of the subscription. As a format, we recommend the SQL format (2021-05-25 11:11:11), but it also accepts other formats.", 'wp-webhooks' ); ?>
			<?php
			$parameter['expiration_date']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "This is the unique ID of the subscription in the merchant processor, such as PayPal or Stripe. It accepts any kind of string.", 'wp-webhooks' ); ?>
			<?php
			$parameter['profile_id']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "This argument accepts the email of the customer you would like to set for the susbcription. You can set this argument in case you do not have the customer id of the customer available.", 'wp-webhooks' ); ?>
			<?php
			$parameter['customer_email']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "This is the frequency of the renewals for the subscription. Down below, you will find a list with all of the default subscription periods. Please use the slug as a value (e.g. <strong>month</strong>).", 'wp-webhooks' ); ?>
<pre><?php echo $beautified_subscription_periods;  ?></pre>
			<?php
			$parameter['period']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "This is the unique ID of the initial transaction inside of the merchant processor, such as PayPal or Stripe. The argument accepts any kind of string.", 'wp-webhooks' ); ?>
			<?php
			$parameter['transaction_id']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "This argument allows you to customize the status of the subscription. Please use the slug of the status as a value (e.g. <strong>completed</strong>). Down below, you will find a list with all available default statuses:", 'wp-webhooks' ); ?>
<pre><?php echo $beautified_subscription_statuses; ?></pre>
			<?php
			$parameter['status']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "This argument accepts a date string what contains the date of expiration of the subscription. As a format, we recommend the SQL format (2021-05-25 11:11:11), but it also accepts other formats.", 'wp-webhooks' ); ?>
			<?php
			$parameter['created_date']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "This argument allows you to connect your subscription with an already existing payment.", 'wp-webhooks' ); ?>
			<?php
			$parameter['parent_payment_id']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "Use this argument to connect an already existing customer with your subscription. Please use the customer id and not the user id since these are different things. Please note, that in case you leave this argument empty, we will first try to find an existing customer based on your given email within the <strong>customer_email</strong> argument, and if we found a customer with it, we will map the customer id automatically.", 'wp-webhooks' ); ?>
			<?php
			$parameter['customer_id']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "In case you work with multiple price options, please define the chosen price option for your download here. Please note, that the price option needs to be available within the download you chose for the <strong>download_id</strong> argument.", 'wp-webhooks' ); ?>
			<?php
			$parameter['edd_price_option']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "This argument accepts the percentage of tax that is included within your initial price. E.g.: In case you add 20, it is interpreted as 20% tax.", 'wp-webhooks' ); ?>
			<?php
			$parameter['initial_tax_rate']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "This argument accepts the amount of tax for the initial payment. E.g.: In case your tax is 13.54$, simply add 13.54", 'wp-webhooks' ); ?>
			<?php
			$parameter['initial_tax']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "This argument accepts the percentage of tax that is included within your recurring price. E.g.: In case you add 20, it is interpreted as 20% tax.", 'wp-webhooks' ); ?>
			<?php
			$parameter['recurring_tax_rate']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "This argument accepts the amount of tax for the recurring payment. E.g.: In case your tax is 13.54$, simply add 13.54", 'wp-webhooks' ); ?>
			<?php
			$parameter['recurring_tax']['description'] = ob_get_clean();
            
			ob_start();
			?>
<?php echo __( "Use this argument to add one or multiple subscription notes to the subscription. This value accepts a JSON, containing one subscription note per line. Here is an example:", 'wp-webhooks' ); ?>
<pre>[
    "First Note 1",
    "First Note 2"
]</pre>
<?php echo __( "The example above adds two notes.", 'wp-webhooks' ); ?>
			<?php
			$parameter['notes']['description'] = ob_get_clean();

			$description = array(
				'tipps' => array(
					__( "In case you would like to update the customer but you do not have the customer id, simply provide the customer email within the <strong>customer_email</strong> argument and we will fetch the customer automatically.", 'wp-webhooks' ),
				),
			);

            return array(
                'action'            => 'edd_update_subscription',
                'name'              => __( 'Update subscription', 'wp-webhooks' ),
                'sentence'              => __( 'update a subscription', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => __( 'This webhook action allows you to update a subscription within Easy Digital Downloads - Recurring.', 'wp-webhooks' ),
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
					'payment_id' => 0,
					'customer_id' => 0,
				),
			);

			$subscription_id   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'subscription_id' ) );
			$expiration_date   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'expiration_date' );
			$profile_id   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'profile_id' );
			$initial_amount   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'initial_amount' );
			$recurring_amount   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'recurring_amount' );
			$download_id   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_id' );
			$transaction_id   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'transaction_id' );
			$status   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'status' );
			$created_date   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'created_date' );
			$bill_times   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'bill_times' );
			$period   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'period' );
			$parent_payment_id   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'parent_payment_id' ) );
			$customer_id   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_id' ) );
			$customer_email   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_email' );
			$edd_price_option   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'edd_price_option' );
			$initial_tax_rate   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'initial_tax_rate' );
			$initial_tax   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'initial_tax' );
			$recurring_tax_rate   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'recurring_tax_rate' );
			$recurring_tax   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'recurring_tax' );
			$notes   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'notes' );
			
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( ! class_exists( 'EDD_Subscription' ) ){
				$return_args['msg'] = __( 'The class EDD_Subscription() does not exist. The subscription was not created.', 'wp-webhooks' );
				return $return_args;
			}

			if( empty( $subscription_id ) ){
				$return_args['msg'] = __( 'The subscription_id argument cannot be empty. ', 'wp-webhooks' );
				return $return_args;
			}

			$subscription = new EDD_Subscription( $subscription_id );
			if( empty( $subscription ) ){
				$return_args['msg'] = __( 'Error: Invalid subscription id provided.', 'wp-webhooks' );
				return $return_args;
			}

			//try to fetch the customer
			if( empty( $customer_id ) ){
				if( ! empty( $customer_email ) ){
					$tmpcustomer = EDD()->customers->get_customer_by( 'email', $customer_email );
					if( isset( $tmpcustomer->id ) && ! empty( $tmpcustomer->id ) ) {
						$customer_id = $tmpcustomer->id;
					}
				}
			} else {
				$tmpcustomer = EDD()->customers->get_customer_by( 'id', $customer_id );
				if( isset( $tmpcustomer->id ) && ! empty( $tmpcustomer->id ) ) {
					$customer_id = $tmpcustomer->id;
				}
			}

			$sub_args = array();
			
			if( $expiration_date ){
				$sub_args['expiration'] = date( 'Y-m-d H:i:s', strtotime( $expiration_date, current_time( 'timestamp' ) ) );
			}
			
			if( $created_date ){
				$sub_args['created'] = date( 'Y-m-d H:i:s', strtotime( $created_date, current_time( 'timestamp' ) ) );
			}
			
			if( $status ){
				$sub_args['status'] = sanitize_text_field( $status );
			}
			
			if( $profile_id ){
				$sub_args['profile_id'] = sanitize_text_field( $profile_id );
			}
			
			if( $transaction_id ){
				$sub_args['transaction_id'] = sanitize_text_field( $transaction_id );
			}
			
			if( $initial_amount ){
				$sub_args['initial_amount'] = edd_sanitize_amount( sanitize_text_field( $initial_amount ) );
			}
			
			if( $recurring_amount ){
				$sub_args['recurring_amount'] = edd_sanitize_amount( sanitize_text_field( $recurring_amount ) );
			}
			
			if( $bill_times ){
				$sub_args['bill_times'] = absint( $bill_times );
			}
			
			if( $period ){
				$sub_args['period'] = sanitize_text_field( $period );
			}
			
			if( $parent_payment_id ){
				$sub_args['parent_payment_id'] = $parent_payment_id;
			}
			
			if( $download_id ){
				$sub_args['product_id'] = absint( $download_id );
			}
			
			if( $edd_price_option ){
				$sub_args['price_id'] = absint( $edd_price_option );
			}
			
			if( $customer_id ){
				$sub_args['customer_id'] = $customer_id;
			}
			
			if( $initial_tax_rate ){
				$sub_args['initial_tax_rate'] = edd_sanitize_amount( (float) $initial_tax_rate / 100 );
			}

			if( $initial_tax ){
				$sub_args['initial_tax'] = edd_sanitize_amount( $initial_tax );
			}

			if( $recurring_tax_rate ){
				$sub_args['recurring_tax_rate'] = edd_sanitize_amount( (float) $recurring_tax_rate / 100 );
			}

			if( $recurring_tax ){
				$sub_args['recurring_tax'] = edd_sanitize_amount( $recurring_tax );
			}

			$check = $subscription->update( $sub_args );

			if( $check ){

				if( ! empty( $notes ) ){
					if( WPWHPRO()->helpers->is_json( $notes ) ){
						$notes_arr = json_decode( $notes, true );
						foreach( $notes_arr as $snote ){
							$subscription->add_note( $snote );
						}
					}
				}
	
				$return_args['msg'] = __( "The subscription was successfully updated.", 'action-edd_update_subscription-success' );
				$return_args['success'] = true;
				$return_args['data']['subscription_id'] = $subscription->id;
				$return_args['data']['subscription_arguments'] = $sub_args;
				$subscription_id = $subscription->id;
			} else {
				if( empty( $sub_args ) ){
					$return_args['msg'] = __( "Error updating the subscription. No arguments/values for an update given.", 'action-edd_update_subscription-success' );
				} else {
					$return_args['msg'] = __( "Error updating the subscription.", 'action-edd_update_subscription-success' );
				}
			}
		
			

			if( ! empty( $do_action ) ){
				do_action( $do_action, $subscription_id, $subscription, $sub_args, $return_args );
			}

			return $return_args;
    
        }

    }

endif; // End if class_exists check.