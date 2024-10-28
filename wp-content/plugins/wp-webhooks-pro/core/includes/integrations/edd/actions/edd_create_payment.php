<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_create_payment' ) ) :

	/**
	 * Load the edd_create_payment action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_create_payment {

        public function is_active(){

            $is_active = true;

            //Backwards compatibility for the "Easy Digital Downloads" integration
            if( defined( 'WPWH_EDD_NAME' ) ){
                $is_active = false;
            }

            return $is_active;
        }

        public function get_details(){

            $parameter = array(
				'customer_email'       			=> array( 'required' => true, 'short_description' => __( '(String) The email of the customer you want to associate with the payment. Please see the description for further details.', 'wp-webhooks' ) ),
				'discounts'    					=> array( 'short_description' => __( '(String) A comma-separated list of discount codes. Please see the description for further details.', 'wp-webhooks' ) ),
				'gateway'    					=> array( 'short_description' => __( '(String) The slug of the currently used gateway. Please see the description for further details. Default empty.', 'wp-webhooks' ) ),
				'currency'    					=> array( 'short_description' => __( '(String) The currency code of the payment. Default is your default currency. Please see the description for further details.', 'wp-webhooks' ) ),
				'parent_payment_id'    			=> array( 'short_description' => __( '(Integer) The payment id of a parent payment.', 'wp-webhooks' ) ),
				'payment_status'    			=> array( 'short_description' => __( '(String) The status of the payment. Default is "pending". Please see the description for further details.', 'wp-webhooks' ) ),
				'product_data'    				=> array( 'short_description' => __( '(String) A JSON formatted string, containing all the product data and options. Please refer to the description for examples and further details.', 'wp-webhooks' ) ),
				'edd_agree_to_terms'    		=> array( 
				'type' => 'select',
							'choices' => array(
								'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
								'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
							),
				'multiple' => false,
							'default_value' => 'no',
				'short_description' => __( '(String) Defines if a user agreed to the terms. Set it to "yes" to mark the user as agreed. Default: no', 'wp-webhooks' ),
				),
						'edd_agree_to_privacy_policy'	=> array( 
				'type' => 'select',
							'choices' => array(
								'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
								'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
							),
				'multiple' => false,
							'default_value' => 'no',
				'short_description' => __( '(String) Defines if a user agreed to the privacy policy. Set it to "yes" to mark the user as agreed. Default: no', 'wp-webhooks' ),
				),
						'payment_date'    				=> array( 'short_description' => __( '(String) Set a custom payment date. The format is flexible, but we recommend SQL format.', 'wp-webhooks' ) ),
						'user_id'    					=> array( 'short_description' => __( '(Integer) The user id of the WordPress user. If not defined, we try to fetch the id using the customer_email.', 'wp-webhooks' ) ),
						'customer_first_name'    		=> array( 'short_description' => __( '(String) The first name of the customer. Please see the description for further details.', 'wp-webhooks' ) ),
						'customer_last_name'    		=> array( 'short_description' => __( '(String) The last name of the customer. Please see the description for further details.', 'wp-webhooks' ) ),
						'customer_country'    			=> array(
							'type'			=> 'select',
							'multiple'		=> false,
							'query'			=> array(
								'filter'	=> 'countries',
								'args'		=> array()
							),
							'label' => __( 'The customer country', 'wp-webhooks' ),
							'short_description' => __( '(String) The country code of the customer.', 'wp-webhooks' ) 
						),
						'customer_state'    			=> array( 'short_description' => __( '(String) The state of the customer.', 'wp-webhooks' ) ),
						'customer_zip'    				=> array( 'short_description' => __( '(String) The zip of the customer.', 'wp-webhooks' ) ),
						'send_receipt'    				=> array( 
				'type' => 'select',
							'choices' => array(
								'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
								'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
							),
				'multiple' => false,
							'default_value' => 'no',
				'short_description' => __( '(String) Set it to "yes" for sending out a receipt to the customer. Default "no". Please see the description for further details.', 'wp-webhooks' ),
				),
				'do_action'     				=> array( 'short_description' => __( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More infos are in the description.', 'wp-webhooks' ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'       => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'        	=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'        	=> array( 'short_description' => __( '(array) Within the data array, you will find further details about the response, as well as the payment id and further information.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
                'success' => true,
                'msg' => 'The payment was successfully created.',
                'data' => 
                array (
                  'payment_id' => 747,
                  'payment_data' => 
                  array (
                    'purchase_key' => 'aa10bc587fb544b10c01fe13905fba74',
                    'user_email' => 'jondoe@test.test',
                    'user_info' => 
                    array (
                      'id' => 0,
                      'email' => 'jondoe@test.test',
                      'first_name' => 'Jannis',
                      'last_name' => 'Testing',
                      'discount' => false,
                      'address' => 
                      array (
                        'country' => 'AE',
                        'state' => false,
                        'zip' => false,
                      ),
                    ),
                    'gateway' => 'paypal',
                    'currency' => 'EUR',
                    'cart_details' => 
                    array (
                      0 => 
                      array (
                        'id' => 176,
                        'quantity' => 1,
                        'name' => "Demo Product",
                        'item_price' => 49,
                        'tax' => 5,
                        'discount' => 4,
                        'fees' => 
                        array (
                          0 => 
                          array (
                            'label' => 'Custom Fee',
                            'amount' => 10,
                            'type' => 'fee',
                            'id' => '',
                            'no_tax' => false,
                            'download_id' => 435,
                          ),
                        ),
                        'item_number' => 
                        array (
                          'options' => 
                          array (
                            'price_id' => NULL,
                          ),
                        ),
                      ),
                    ),
                    'parent' => false,
                    'status' => 'publish',
                    'post_date' => '2020-04-23 00:00:00',
                  ),
                ),
            );

			//load default edd statuses
			$payment_statuses = array(
				'pending'   => __( 'Pending', 'wp-webhooks' ),
				'publish'   => __( 'Complete', 'wp-webhooks' ),
				'refunded'  => __( 'Refunded', 'wp-webhooks' ),
				'failed'    => __( 'Failed', 'wp-webhooks' ),
				'abandoned' => __( 'Abandoned', 'wp-webhooks' ),
				'revoked'   => __( 'Revoked', 'wp-webhooks' ),
				'processing' => __( 'Processing', 'wp-webhooks' )
			);

			if( function_exists( 'edd_get_payment_statuses' ) ){
				$payment_statuses = array_merge( $payment_statuses, edd_get_payment_statuses() );
			}
			$payment_statuses = apply_filters( 'wpwh/descriptions/actions/edd_create_payment/payment_statuses', $payment_statuses );

			$default_cart_details = array (
				array (
				'id' => 176,
				'quantity' => 1,
				'item_price' => 49,
				'tax' => 5,
				'discount' => 4,
				'fees' => 
				array (
					array (
					'label' => 'Custom Fee',
					'amount' => 10,
					'type' => 'fee',
					'id' => '',
					'no_tax' => false,
					'download_id' => 435,
					),
				),
				'item_number' => 
				array (
					'options' => 
					array (
					'price_id' => NULL,
					),
				),
				),
			);
			$default_cart_details = apply_filters( 'wpwh/descriptions/actions/edd_create_payment/default_cart_details', $default_cart_details );

			$beautified_cart_details = json_encode( $default_cart_details, JSON_PRETTY_PRINT );

			ob_start();
			?>
<?php echo __( "The customer email is the email address of the customer you want to associate with the payment. In case there is no existing EDD customer with this email available, EDD will create one. (An EDD customer is not the same as a WordPress user. There is no WordPRess user created by simply defining the email.) To associate a WordPress user with the EDD customer, please check out the <strong>user_id</strong> argument.", 'wp-webhooks' ); ?>
			<?php
			$parameter['customer_email']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "This argument accepts a single discount code or a comma-separated list of multiple discount codes. Down below, you will find an example on how to use multiple discount codes. <strong>Please note</strong>: This only adds the discount code to the payment, but it does not affect the pricing. If you want to apply the discounts to the payment pricing, you need to use the discount key within the <strong>product_data</strong> line item argument.", 'wp-webhooks' ); ?>
<pre>10PERCENTOFF,EASTERDISCOUNT10</pre>
			<?php
			$parameter['discounts']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "The slug of the gateway you want to use. Down below, you will find further details on the available default gateways:", 'wp-webhooks' ); ?>
<ol>
    <li>
        <strong><?php echo __( "PayPal Standard", 'wp-webhooks' ); ?></strong>: paypal
    </li>
    <li>
        <strong><?php echo __( "Test Payment", 'wp-webhooks' ); ?></strong>: manual
    </li>
    <li>
        <strong><?php echo __( "Amazon", 'wp-webhooks' ); ?></strong>: amazon
    </li>

    <?php do_action( 'wpwh/descriptions/actions/edd_create_payment/after_gateway_items' ) ?>

</ol>
			<?php
			$parameter['gateway']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "The currency code of the currency you want to use for this payment. You can set it to e.g. <strong>EUR</strong> or <strong>USD</strong>. If you leave it empty, we use your default currency. ( edd_get_currency() )", 'wp-webhooks' ); ?>
			<?php
			$parameter['currency']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "Use this argument to set a custom payment status. Down below, you will find a list of all available, default payment names and its slugs. To make this argument work, please define the slug of the status you want. If you don't define any, <strong>pending</strong> is used.", 'wp-webhooks' ); ?>
<ol>
    <?php foreach( $payment_statuses as $ps_slug => $ps_name ) : ?>
        <li>
            <strong><?php echo __(  $ps_name, 'wp-webhooks' ); ?></strong>: <?php echo $ps_slug; ?>
        </li>
    <?php endforeach; ?>
</ol>
			<?php
			$parameter['payment_status']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "This argument accepts a JSON formatted String, which contains all the downloads you want to add, including further details about the pricing. Due to the complexity of the string, we explained each section of the following JSON down below. The JSON below contains a list with one product which is added to your payment details. They also determine the pricing of the payment and other information.", 'wp-webhooks' ); ?>
<pre><?php echo $beautified_cart_details; ?></pre>
<?php echo __( "The above JSON adds a single download to the payment. If you want to add multiple products, simply add another entry within the [] brackets. HEre are all the values explained:", 'wp-webhooks' ); ?>
<ol>

  <li>
    <strong>id</strong> (<?php echo __( "Required", 'wp-webhooks' ); ?>)<br>
    <?php echo __( "This is the download id within WordPress.", 'wp-webhooks' ); ?>
  </li>
  
  <li>
    <strong>quantity</strong> (<?php echo __( "Required", 'wp-webhooks' ); ?>)<br>
    <?php echo __( "The number of how many times this product should be added.", 'wp-webhooks' ); ?>
  </li>

  <li>
    <strong>item_price</strong> (<?php echo __( "Required", 'wp-webhooks' ); ?>)<br>
    <?php echo __( "The price of the product you want to add", 'wp-webhooks' ); ?>
  </li>

  <li>
    <strong>tax</strong> (<?php echo __( "Required", 'wp-webhooks' ); ?>)<br>
    <?php echo __( "The amount of tax that should be added to the item_price", 'wp-webhooks' ); ?>
  </li>

  <li>
    <strong>discount</strong><br>
    <?php echo __( "The amount of discount that should be removed from the item_price", 'wp-webhooks' ); ?>
  </li>

  <li>
    <strong>fees</strong><br>
    <?php echo __( "Fees are extra prices that are added on top of the product price. Usually this is set for signup fees or other prices that are not directly related with the download. The values set within the fees are all optional, but recommended to be available within the JSON.", 'wp-webhooks' ); ?>
  </li>

  <li>
    <strong>item_number</strong><br>
    <?php echo __( "The item number contains variation related data about the product. In case you want to add a variation, you can define the price id there.", 'wp-webhooks' ); ?>
  </li>

  <?php do_action( 'wpwh/descriptions/actions/edd_create_payment/after_cart_details_items', $default_cart_details ); ?>

</ol>
			<?php
			$parameter['product_data']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "The send_receipt argument allows you to send out the receipt for the payment you just made. Please note that this logic uses the EDD default functionality. The receipt is only send based on the given payment status.", 'wp-webhooks' ); ?>
			<?php
			$parameter['send_receipt']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "Please not that defining the customer first name (or last name) are only affecting the custoemr in case it doesn't exist at that point. For existing customers, the first and last name is not updated.", 'wp-webhooks' ); ?>
			<?php
			$parameter['customer_first_name']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "Please not that defining the customer last name (or first name) are only affecting the custoemr in case it doesn't exist at that point. For existing customers, the first and last name is not updated.", 'wp-webhooks' ); ?>
			<?php
			$parameter['customer_last_name']['description'] = ob_get_clean();

			$description = array(
				'tipps' => array(
					__( "This webhook action is very versatile. Depending on your active extensions of the plugin, you will see different arguments and descriptions. This way, we can always provide you personalized features based on your active plugins.", 'wp-webhooks' ),
				),
			);

            return array(
                'action'            => 'edd_create_payment',
                'name'              => __( 'Create payment', 'wp-webhooks' ),
                'sentence'          => __( 'create a payment', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => __( 'This webhook action allows you to create a payment within Easy Digital Downloads.', 'wp-webhooks' ),
                'description'       => $description,
                'integration'       => 'edd',
                'premium' 			=> false,
            );

        }

        public function execute( $return_data, $response_body ){

            $edd_helpers = WPWHPRO()->integrations->get_helper( 'edd', 'edd_helpers' );
            $return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array()
			);

			$purchase_key     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'purchase_key' );
			$discounts     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'discounts' );
			$gateway     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'gateway' );
			$parent_payment_id     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'parent_payment_id' );
			$currency     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'currency' );
			$payment_status     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'payment_status' );
			$product_data     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'product_data' );
			$edd_agree_to_terms     = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'edd_agree_to_terms' ) === 'yes' ) ? true : false;
			$edd_agree_to_privacy_policy     = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'edd_agree_to_privacy_policy' ) === 'yes' ) ? true : false;
			$payment_date     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'payment_date' );

			$user_id     = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' ) );
			$customer_email     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_email' );
			$customer_first_name     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_first_name' );
			$customer_last_name     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_last_name' );
			$customer_country     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_country' );
			$customer_state     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_state' );
			$customer_zip     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_zip' );

			$send_receipt     = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'send_receipt' ) === 'yes' ) ? true : false;
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $user_id ) && ! empty( $customer_email ) ){
				$wp_user = get_user_by( 'email', sanitize_email( $customer_email ) );
				if ( ! empty( $wp_user ) ) {
					$user_id = $wp_user->ID;
				}
			}

			$user_info = array(
				'id'            => $user_id,
				'email'         => $customer_email,
				'first_name'    => $customer_first_name,
				'last_name'     => $customer_last_name,
				'discount'      => $discounts,
				'address'		=> array(
					'country'	=> $customer_country,
					'state'	=> $customer_state,
					'zip'	=> $customer_zip,
				)
			);

			$product_details = array();
			if( ! empty( $product_data ) && WPWHPRO()->helpers->is_json( $product_data ) ){
				$product_details = json_decode( $product_data, true );
			}

			$purchase_data = array(
				'purchase_key'  => ( ! empty( $purchase_key ) ) ? $purchase_key : strtolower( md5( uniqid() ) ),
				'user_email'    => $customer_email,
				'user_info'     => $user_info,
				'gateway'     	=> ( ! empty( $gateway ) ) ? $gateway : '',
				'currency'      => ( ! empty( $currency ) ) ? $currency : edd_get_currency(),
				'cart_details'  => $product_details,
				'parent'        => $parent_payment_id,
				'status'        => 'pending',
			);

			if ( ! empty( $payment_date ) ) {
				$purchase_data['post_date'] = date( "Y-m-d H:i:s", strtotime( $payment_date ) );
			}

			if ( ! empty( $edd_agree_to_terms ) ) {
				$purchase_data['agree_to_terms_time'] = current_time( 'timestamp' );
			}

			if ( ! empty( $edd_agree_to_privacy_policy ) ) {
				$purchase_data['agree_to_privacy_time'] = current_time( 'timestamp' );
			}

			$purchase_data = apply_filters( 'wpwh/actions/edd_create_payment/purchase_data', $purchase_data, $payment_status, $send_receipt );

			//Validate required fields
			$valid_payment_data = $edd_helpers->validate_payment_data( $purchase_data );
			if( ! $valid_payment_data['success'] ){

				$valid_payment_data['msg'] = __( "Your payment was not created. Please check the errors for further details.", 'action-edd_create_payment-failure' );

				return $valid_payment_data;
			}

			if( ! $send_receipt ){
				remove_action( 'edd_complete_purchase', 'edd_trigger_purchase_receipt', 999 );

				// if we're using EDD Per Product Emails, prevent the custom email from being sent
				if ( class_exists( 'EDD_Per_Product_Emails' ) ) {
					remove_action( 'edd_complete_purchase', 'edd_ppe_trigger_purchase_receipt', 999, 1 );
				}
			}

			$payment_id = edd_insert_payment( $purchase_data );

			//Make sure the status is updated after
			if( $payment_id && ! empty( $payment_status ) && $payment_status !== 'pending' ){
				edd_update_payment_status( $payment_id, $payment_status );
			}


			if( ! $send_receipt ){
				add_action( 'edd_complete_purchase', 'edd_trigger_purchase_receipt', 999, 3 );

				// if we're using EDD Per Product Emails, prevent the custom email from being sent
				if ( class_exists( 'EDD_Per_Product_Emails' ) ) {
					add_action( 'edd_complete_purchase', 'edd_ppe_trigger_purchase_receipt', 999, 1 );
				}
			}

			if( ! empty( $payment_id ) ){

				$return_args['data']['payment_id'] = $payment_id;
				$return_args['data']['payment_data'] = $purchase_data;
				$return_args['msg'] = __( "The payment was successfully created.", 'action-edd_create_payment-success' );
				$return_args['success'] = true;

			} else {
				$return_args['msg'] = __( "No payment was created.", 'action-edd_create_payment-success' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $payment_id, $purchase_data, $send_receipt, $return_args );
			}

			return $return_args;
            
        }

    }

endif; // End if class_exists check.