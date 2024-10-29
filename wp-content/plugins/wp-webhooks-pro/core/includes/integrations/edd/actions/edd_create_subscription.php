<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_create_subscription' ) ) :

	/**
	 * Load the edd_create_subscription action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_create_subscription {

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
				'expiration_date'       => array( 'required' => true, 'short_description' => __( '(String) The date for the expiration of the subscription. Recommended format: 2021-05-25 11:11:11', 'wp-webhooks' ) ),
				'profile_id'       => array( 'required' => true, 'short_description' => __( '(String) This is the unique ID of the subscription in the merchant processor, such as PayPal or Stripe.', 'wp-webhooks' ) ),
				'download_id'       => array( 'required' => true, 'short_description' => __( '(Integer) The ID of the download you want to connect with the subscription.', 'wp-webhooks' ) ),
				'customer_email'       => array( 'required' => true, 'short_description' => __( '(String) The email of the customer. Please see the description for further details.', 'wp-webhooks' ) ),
				'period'       => array( 'required' => true, 'short_description' => __( '(String) The billing period of the subscription. Please see the description for further details.', 'wp-webhooks' ) ),
				'initial_amount'       => array( 'short_description' => __( '(Mixed) The amount for the initial payment. E.g. 39.97', 'wp-webhooks' ) ),
				'recurring_amount'       => array( 'short_description' => __( '(Mixed) The recurring amount for the subscription. E.g. 19.97', 'wp-webhooks' ) ),
				'transaction_id'       => array( 'short_description' => __( '(String) This is the unique ID of the initial transaction inside of the merchant processor, such as PayPal or Stripe.', 'wp-webhooks' ) ),
				'status'       => array( 'short_description' => __( '(String) The status of the given subscription. Please see the description for further details.', 'wp-webhooks' ) ),
				'created_date'       => array( 'short_description' => __( '(String) The date of creation of the subscription. Recommended format: 2021-05-25 11:11:11', 'wp-webhooks' ) ),
				'bill_times'       => array( 'short_description' => __( '(Integer) This refers to the number of times the subscription will be billed before being marked as Completed and payments stopped. Enter 0 if payments continue indefinitely.', 'wp-webhooks' ) ),
				'parent_payment_id'       => array( 'short_description' => __( '(Integer) Use this argument to connect the subscription with an already existing payment. Otherwise, a new one is created. Please see the description for further details.', 'wp-webhooks' ) ),
				'customer_id'       => array( 'short_description' => __( '(Integer) The id of the customer you want to connect. If it is not given, we try to fetch the user from the customer_email argument. Please see the description for further details.', 'wp-webhooks' ) ),
				'customer_first_name'       => array( 'short_description' => __( '(String) The first name of the customer. Please see the description for further details.', 'wp-webhooks' ) ),
				'customer_last_name'       => array( 'short_description' => __( '(String) The last name of the customer. Please see the description for further details.', 'wp-webhooks' ) ),
				'edd_price_option'       => array( 'short_description' => __( '(Integer) The variation id for a download price option. Please see the description for further details.', 'wp-webhooks' ) ),
				'gateway'       => array( 'short_description' => __( '(String) The gateway you want to use for your subscription (and maybe payment). Please see the description for further details.', 'wp-webhooks' ) ),
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

			$returns_code = array (
				'success' => true,
				'msg' => 'The subscription was successfully created.',
				'data' => 
				array (
				  'subscription_id' => '23',
				  'payment_id' => 843,
				  'customer_id' => 8,
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
			$default_subscription_statuses = apply_filters( 'wpwh/descriptions/actions/edd_create_subscription/default_subscription_statuses', $default_subscription_statuses );
			$beautified_subscription_statuses = json_encode( $default_subscription_statuses, JSON_PRETTY_PRINT );

			$default_subscription_periods = array (
				'day' => __( 'Daily', 'wp-webhooks' ),
				'week' => __( 'Weekly', 'wp-webhooks' ),
				'month' => __( 'Monthly', 'wp-webhooks' ),
				'quarter' => __( 'Quarterly', 'wp-webhooks' ),
				'semi-year' => __( 'Semi-Yearly', 'wp-webhooks' ),
				'year' => __( 'Yearly', 'wp-webhooks' ),
			);
			$default_subscription_periods = apply_filters( 'wpwh/descriptions/actions/edd_create_subscription/default_subscription_periods', $default_subscription_periods );
			$beautified_subscription_periods = json_encode( $default_subscription_periods, JSON_PRETTY_PRINT );

			$default_subscription_gateways = array ();
			if( function_exists( 'edd_get_payment_gateways' ) ){
				foreach( edd_get_payment_gateways() as $gwslug => $gwdata ){
					$default_subscription_gateways[ $gwslug ] = ( isset( $gwdata['admin_label'] ) ) ? $gwdata['admin_label'] : $gwdata['checkout_label'];
				}
			}
			$default_subscription_gateways = apply_filters( 'wpwh/descriptions/actions/edd_create_subscription/default_subscription_gateways', $default_subscription_gateways );
			$beautified_subscription_gateways = json_encode( $default_subscription_gateways, JSON_PRETTY_PRINT );

			ob_start();
			?>
<?php echo __( "This argument accepts a date string what contains the date of expiration of the subscription. As a format, we recommend the SQL format (2021-05-25 11:11:11), but it also accepts other formats. Please note that in case you set the <strong>status</strong> argument to <strong>trialling</strong>, this date field will be ignored since we will calculate the expiration date based on the in the product given trial period.", 'wp-webhooks' ); ?>
			<?php
			$parameter['expiration_date']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "This is the unique ID of the subscription in the merchant processor, such as PayPal or Stripe. It accepts any kind of string.", 'wp-webhooks' ); ?>
			<?php
			$parameter['profile_id']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "This argument accepts the email of the customer you create the subscription for. In case we could not find a customer with your given data, it will be created. Please note that creating a customer does not automatically create a user within your WordPress system.", 'wp-webhooks' ); ?>
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
<?php echo __( "Please note that in case you choose <strong>trialling</strong> as a subscription status, we will automatically apply the given trial period instead of the given expiration date from the <strong>expiration_date</strong> argument.", 'wp-webhooks' ); ?>
			<?php
			$parameter['status']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "This argument accepts a date string what contains the date of expiration of the subscription. As a format, we recommend the SQL format (2021-05-25 11:11:11), but it also accepts other formats. Please note that in case you set the <strong>status</strong> argument to <strong>trialling</strong>, this argument will influence the expiration date of the trial perdod, which is defined within the download itself.", 'wp-webhooks' ); ?>
			<?php
			$parameter['created_date']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "This argument allows you to connect your subscription with an already existing payment. Please note that if you set this argument, the <strong>gateway</strong> argument is ignored since the gateway will be based on the gateway of the payment you try to add. If you do not set this argument, we will create a payment automatically for you.", 'wp-webhooks' ); ?>
			<?php
			$parameter['parent_payment_id']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "Use this argument to connect an already existing customer with your newly created subscription. Please use the customer id and not the user id since these are different things. Please note, that in case you leave this argument empty, we will first try to find an existing customer based on your given email within the <strong>customer_email</strong> argument, and if we cannot find any customer, we will create one for you based on the given email within the <strong>customer_email</strong> argument.", 'wp-webhooks' ); ?>
			<?php
			$parameter['customer_id']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "This argument allows you to add a first name to the customer in case it does not exist at that point. If we could find a customer to your given email or the cucstomer id, this argument is ignored. It is only used once a new customer is created. If it is not set, we will use the email as the default name.", 'wp-webhooks' ); ?>
			<?php
			$parameter['customer_first_name']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "This argument allows you to add a last name to the customer in case it does not exist at that point. If we could find a customer to your given email or the cucstomer id, this argument is ignored. It is only used once a new customer is created. If it is not set, we will use the email as the default name.", 'wp-webhooks' ); ?>
			<?php
			$parameter['customer_last_name']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "In case you work with multiple price options, please define the chosen price option for your download here. Please note, that the price option needs to be available within the download you chose for the <strong>download_id</strong> argument.", 'wp-webhooks' ); ?>
			<?php
			$parameter['edd_price_option']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "Define the gateway you want to use for this subscription. Please note that if you set the <strong>parent_payment_id</strong> argument, the gateway of the payment is used and this argument is ignored. Please use the slug of the gateway (e.g. <strong>paypal</strong>). Here is a list of all currently available gateways:", 'wp-webhooks' ); ?>
<pre><?php echo $beautified_subscription_gateways; ?></pre>
			<?php
			$parameter['gateway']['description'] = ob_get_clean();

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
					__( "Creating a subscription will also create a payment, except you define the <strong>parent_payment_id</strong> argument.", 'wp-webhooks' ),
					__( "Creating the subscription will also create a customer from the given email address of the <strong>customer_email</strong> argument, except you set the <strong>customer_id</strong> argument.", 'wp-webhooks' ),
				),
			);

            return array(
                'action'            => 'edd_create_subscription',
                'name'              => __( 'Create subscription', 'wp-webhooks' ),
                'sentence'              => __( 'create a subscription', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => __( 'This webhook action allows you to create a subscription within Easy Digital Downloads - Recurring.', 'wp-webhooks' ),
                'description'       => $description,
                'integration'       => 'edd',
                'premium' 			=> false,
            );

        }

        public function execute( $return_data, $response_body ){

            $subscription_id = 0;
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'subscription_id' => 0,
					'payment_id' => 0,
					'customer_id' => 0,
				),
			);

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
			$parent_payment_id   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'parent_payment_id' );
			$customer_id   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_id' );
			$customer_email   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_email' );
			$customer_first_name     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_first_name' );
			$customer_last_name     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_last_name' );
			$edd_price_option   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'edd_price_option' );
			$gateway   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'gateway' );
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

			if( empty( $expiration_date ) ){
				$return_args['msg'] = __( 'The expiration_date argument cannot be empty. ', 'wp-webhooks' );
				return $return_args;
			}

			if( empty( $profile_id ) ){
				$return_args['msg'] = __( 'The profile_id argument cannot be empty. ', 'wp-webhooks' );
				return $return_args;
			}

			if( empty( $customer_email ) ){
				$return_args['msg'] = __( 'The customer_email argument cannot be empty. ', 'wp-webhooks' );
				return $return_args;
			}

			if( empty( $download_id ) ){
				$return_args['msg'] = __( 'The download_id argument cannot be empty. ', 'wp-webhooks' );
				return $return_args;
			}

			if( empty( $period ) ){
				$return_args['msg'] = __( 'The period argument cannot be empty. ', 'wp-webhooks' );
				return $return_args;
			}

			if( empty( $initial_amount ) ){
				$initial_amount = 0; //set it default to 0
			}

			if( empty( $recurring_amount ) ){
				$recurring_amount = 0; //set it default to 0
			}

			if( empty( $customer_first_name ) && empty( $customer_last_name ) ) {
				$customer_name = $customer_email;
			} else {
				$customer_name = trim( $customer_first_name . ' ' . $customer_last_name );
			}

			if( ! empty( $created_date ) ) {
				$created_date = date( 'Y-m-d ' . date( 'H:i:s', current_time( 'timestamp' ) ), strtotime( $created_date, current_time( 'timestamp' ) ) );
			} else {
				$created_date = date( 'Y-m-d H:i:s',current_time( 'timestamp' ) );
			}

			//try to fetch the customer
			if( empty( $customer_id ) ){
				$tmpcustomer = EDD()->customers->get_customer_by( 'email', $customer_email );
				if( isset( $tmpcustomer->id ) && ! empty( $tmpcustomer->id ) ) {
					$customer_id = $tmpcustomer->id;
				}
			}

			if( ! empty( $customer_id ) ) {

				$customer    = new EDD_Recurring_Subscriber( absint( $customer_id ) );
				$customer_id = $customer->id;
				$email       = $customer->email;
		
			} else {
		
				$email       = sanitize_email( $customer_email );
				$user        = get_user_by( 'email', $email );
				$user_id     = $user ? $user->ID : 0;
				$customer    = new EDD_Recurring_Subscriber;
				$customer_id = $customer->create( array( 'email' => $email, 'user_id' => $user_id, 'name' => $customer_name ) );
		
			}
		
			$customer_id = absint( $customer_id );
		
			if( ! empty( $parent_payment_id ) ) {
		
				$payment_id = absint( $parent_payment_id );
				$payment    = new EDD_Payment( $payment_id );
		
			} else {
		
				$options = array();
				if ( ! empty( $edd_price_option ) ) {
					$options['price_id'] = absint( $edd_price_option );
				}
		
				$payment = new EDD_Payment;
				$payment->add_download( absint( $download_id ), $options );
				$payment->customer_id = $customer_id;
				$payment->email       = $email;
				$payment->user_id     = $customer->user_id;
				$payment->gateway     = sanitize_text_field( $gateway );
				$payment->total       = edd_sanitize_amount( sanitize_text_field( $initial_amount ) );
				$payment->date        = $created_date;
				$payment->status      = 'pending';
				$payment->save();
				$payment->status = 'complete';
				$payment->save();

				$payment_id = absint( $payment->ID );
			}

			$sub_args = array(
				'expiration'        => date( 'Y-m-d 23:59:59', strtotime( $expiration_date, current_time( 'timestamp' ) ) ),
				'created'           => date( 'Y-m-d H:i:s', strtotime( $created_date, current_time( 'timestamp' ) ) ),
				'status'            => sanitize_text_field( $status ),
				'profile_id'        => sanitize_text_field( $profile_id ),
				'transaction_id'    => sanitize_text_field( $transaction_id ),
				'initial_amount'    => edd_sanitize_amount( sanitize_text_field( $initial_amount ) ),
				'recurring_amount'  => edd_sanitize_amount( sanitize_text_field( $recurring_amount ) ),
				'bill_times'        => absint( $bill_times ),
				'period'            => sanitize_text_field( $period ),
				'parent_payment_id' => $payment_id,
				'product_id'        => absint( $download_id ),
				'price_id'          => absint( $edd_price_option ),
				'customer_id'       => $customer_id,
			);

			//these arguments are added extra on top of the default "Add subscription function just to keep it compliant with the default EDD logic
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

			//Add trial period
			if( sanitize_text_field( $status ) === 'trialling' ){
				if( ! empty( $edd_price_option ) ){
					$trial_period = edd_recurring()->get_trial_period( $download_id, $edd_price_option );
				} else {
					$trial_period = edd_recurring()->get_trial_period( $download_id );
				}
				if( ! empty( $trial_period ) ){
					$sub_args['trial_period'] = '+' . $trial_period['quantity'] . ' ' . $trial_period['unit'];

					if( ! empty( $created_date ) ){
						$sub_args['expiration'] = date( 'Y-m-d 23:59:59', strtotime( $sub_args['trial_period'], strtotime( $created_date, current_time( 'timestamp' ) ) ) );
					} else {
						$sub_args['expiration'] = date( 'Y-m-d 23:59:59', strtotime( $sub_args['trial_period'], current_time( 'timestamp' ) ) );
					}
					
				}
			}

			$subscription = new EDD_Subscription;
			$check = $subscription->create( $sub_args );

			if( $check ){
				if( 'trialling' === $subscription->status ) {
					$customer->add_meta( 'edd_recurring_trials', $subscription->product_id );
				}

				if( ! empty( $notes ) ){
					if( WPWHPRO()->helpers->is_json( $notes ) ){
						$notes_arr = json_decode( $notes, true );
						foreach( $notes_arr as $snote ){
							$subscription->add_note( $snote );
						}
					}
				}
			
				$payment->update_meta( '_edd_subscription_payment', true );
	
				$return_args['msg'] = __( "The subscription was successfully created.", 'action-edd_create_subscription-success' );
				$return_args['success'] = true;
				$return_args['data']['subscription_id'] = $subscription->id;
				$return_args['data']['payment_id'] = $payment_id;
				$return_args['data']['customer_id'] = $customer_id;
				$subscription_id = $subscription->id;
			} else {
				$return_args['msg'] = __( "Error creating the subscription.", 'action-edd_create_subscription-success' );
			}
		
			

			if( ! empty( $do_action ) ){
				do_action( $do_action, $subscription_id, $subscription, $payment, $customer, $return_args );
			}

			return $return_args;
    
        }

    }

endif; // End if class_exists check.