<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_create_customer' ) ) :

	/**
	 * Load the edd_create_customer action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_create_customer {

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
				'customer_email'       => array( 'required' => true, 'short_description' => __( '(String) The email of the customer you want to create. In case the user already exists, we do not update it.', 'wp-webhooks' ) ),
				'customer_first_name'       => array( 'short_description' => __( '(String) The first name of the customer.', 'wp-webhooks' ) ),
				'customer_last_name'       => array( 'short_description' => __( '(String) The last name of the customer.', 'wp-webhooks' ) ),
				'additional_emails'       => array( 'short_description' => __( '(String) A comma-separated list of additional email addresses. Please check the description for further details.', 'wp-webhooks' ) ),
				'attach_payments'       => array( 'short_description' => __( '(String) A comma-, and doublepoint-separated list of payment ids you want to assign to the user. Please check the description for further details.', 'wp-webhooks' ) ),
				'increase_purchase_count'       => array( 'short_description' => __( '(Integer) increase the purchase count for the customer.', 'wp-webhooks' ) ),
				'increase_lifetime_value'       => array( 'short_description' => __( '(Float) The price you want to add to the lifetime value of the customer. Please check the description for further details.', 'wp-webhooks' ) ),
				'set_primary_email'       => array( 'short_description' => __( '(String) The email you want to set as the new primary email. Default: customer_email', 'wp-webhooks' ) ),
				'customer_notes'       => array( 'short_description' => __( '(String) A JSON formatted string containing one or multiple customer notes. Please check the description for further details.', 'wp-webhooks' ) ),
				'customer_meta'       => array( 'short_description' => __( '(String) A JSON formatted string containing one or multiple customer meta data. Please check the description for further details.', 'wp-webhooks' ) ),
				'user_id'       => array( 
					'short_description' => __( '(Integer) The user id of the WordPress user you want to assign to the customer. Please read the description for further details.', 'wp-webhooks' ),
					'description' => __( "This argument allows you to assign a user to the Easy Digital Downloads customer. In case the user id is not defined, we will automatically try to match the primary email with a WordPress user.", 'wp-webhooks' ),
				),
				'do_action'     => array( 'short_description' => __( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More infos are in the description.', 'wp-webhooks' ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'        => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'        => array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'customer_id'        => array( 'short_description' => __( '(Integer) The ID of the customer', 'wp-webhooks' ) ),
				'customer_email'        => array( 'short_description' => __( '(String) The email you set within the customer_email argument.', 'wp-webhooks' ) ),
				'additional_emails'        => array( 'short_description' => __( '(String) The additional emails you set within the additional_emails argument.', 'wp-webhooks' ) ),
				'customer_first_name'        => array( 'short_description' => __( '(String) The first name you set within the customer_first_name argument.', 'wp-webhooks' ) ),
				'customer_last_name'        => array( 'short_description' => __( '(String) The last name you set within the customer_last_name argument.', 'wp-webhooks' ) ),
				'attach_payments'        => array( 'short_description' => __( '(String) The payment ids you set within the attach_payments argument.', 'wp-webhooks' ) ),
				'increase_purchase_count'        => array( 'short_description' => __( '(Integer) The purchase count you set within the increase_purchase_count argument.', 'wp-webhooks' ) ),
				'increase_lifetime_value'        => array( 'short_description' => __( '(Float) The lifetime value you set within the increase_lifetime_value argument.', 'wp-webhooks' ) ),
				'customer_notes'        => array( 'short_description' => __( '(String) The customer notes you set within the customer_notes argument.', 'wp-webhooks' ) ),
				'customer_meta'        => array( 'short_description' => __( '(String) The customer meta you set within the customer_meta argument.', 'wp-webhooks' ) ),
				'user_id'        => array( 'short_description' => __( '(String) The user id you set within the user_id argument.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The customer was successfully created.',
				'customer_id' => '5',
				'customer_email' => 'test@domain.com',
				'additional_emails' => 'second@domain.com,thir@domain.com',
				'customer_first_name' => 'John',
				'customer_last_name' => 'Doe',
				'attach_payments' => '747',
				'increase_purchase_count' => 2,
				'increase_lifetime_value' => '55.46',
				'customer_notes' => '["First Note 1","First Note 2"]',
				'customer_meta' => '{"meta_1": "test1","meta_2": "test2"}',
				'user_id' => 23,
			);

			ob_start();
			?>
<?php echo __( "This argument allows you to add one or multiple customer meta values to your newly created customer, using a JSON string. Easy Digital Downloads uses a custom table for these meta values. Here are some examples on how you can use it:", 'wp-webhooks' ); ?>
<ul class="list-group list-group-flush">
    <li class="list-group-item">
        <strong><?php echo __( "Add/update meta values", 'wp-webhooks' ); ?></strong>
        <br>
        <?php echo __( "This JSON shows you how to add simple meta values for your customer.", 'wp-webhooks' ); ?>
        <pre>{
  "meta_1": "test1",
  "meta_2": "test2"
}</pre>
        <?php echo __( "The key is always the customer meta key. On the right, you always have the value for the customer meta value. In this example, we add two meta values to the customer meta. In case a meta key already exists, it will be updated.", 'wp-webhooks' ); ?>
    </li>
    <li class="list-group-item">
        <strong><?php echo __( "Delete meta values", 'wp-webhooks' ); ?></strong>
        <br>
        <?php echo __( "You can also delete existing meta key by setting the value to <strong>ironikus-delete</strong>. This way, the meta will be removed. Here is an example:", 'wp-webhooks' ); ?>
        <pre>{
  "meta_1": "test1",
  "meta_2": "ironikus-delete"
}</pre>
        <?php echo __( "The example above will add the meta key <strong>meta_1</strong> with the value <strong>test1</strong> and it deletes the meta key <strong>meta_2</strong> including its value.", 'wp-webhooks' ); ?>
    </li>
    <li class="list-group-item">
        <strong><?php echo __( "Add/update/remove serialized meta values", 'wp-webhooks' ); ?></strong>
        <br>
        <?php echo __( "Sometimes, it is necessary to add serialized arrays to your data. Using the json below, you can do exactly that. You can use a simple JSON string as the meta value and we automatically convert it to a serialized array once you place the identifier <strong>ironikus-serialize</strong> in front of it. Here is an example:", 'wp-webhooks' ); ?>
        <pre>{
  "meta_1": "test1",
  "meta_2": "ironikus-serialize{\"test_key\":\"wow\",\"testval\":\"new\"}"
}</pre>
        <?php echo __( "This example adds a simple meta with <strong>meta_1</strong> as the key and <strong>test1</strong> as the value. The second meta value contains a json value with the identifier <strong>ironikus-serialize</strong> in the front. Once this value is saved to the database, it gets turned into a serialized array. In this example, it would look as followed: ", 'wp-webhooks' ); ?>
        <pre>a:2:{s:8:"test_key";s:3:"wow";s:7:"testval";s:3:"new";}</pre>
    </li>
</ul>
			<?php
			$parameter['customer_meta']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "Use this argument to add one or multiple customer notes to the customer. This value accepts a JSON, containing one customer note per line. Here is an example:", 'wp-webhooks' ); ?>
<pre>[
  "First Note 1",
  "First Note 2"
]</pre>
<?php echo __( "The example above adds two notes.", 'wp-webhooks' ); ?>
			<?php
			$parameter['customer_notes']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "This field accepts a decimalnumber, which is added on top of the existing lifetime value. If you are going to add one payment with a price of 20$ for a new customer, and you set this value to 5$, the total lifetime value will show 25$.", 'wp-webhooks' ); ?>
			<?php
			$parameter['increase_lifetime_value']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "This field accepts a number, which is added on top of the existing purchase count. If you are going to add three payments for a new customer, and you set this value to 1, your total purchase count will show 4.", 'wp-webhooks' ); ?>
			<?php
			$parameter['increase_purchase_count']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "This argument allows you to connect certain payment ids to the user. To set multiple payments, please separate them with a comma. By default, it recalculates the total amount. If you do not want that, add <strong>:no_update_stats</strong> after the payment id. Here is an example:", 'wp-webhooks' ); ?>
<pre>125,365,444:no_update_stats,777</pre>
<?php echo __( "The example above asigns the payment ids 125, 365, 444, 777 to the customer. It also assigns the payment id 444, but it does not update the statistics.", 'wp-webhooks' ); ?>
			<?php
			$parameter['attach_payments']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "You can add additional emails to a customer. To do that, simply comma-separate the emails within the field. The primary email address is always the <strong>customer_email</strong> argument. Here is an example:", 'wp-webhooks' ); ?>
<pre>jondoe@mydomain.com,anotheremail@domain.com</pre>
			<?php
			$parameter['additional_emails']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "The customer email is the email address of the customer you want to associate with the newly created customer. In case there is no existing EDD customer with this email available, EDD will create one. (An EDD customer is not the same as a WordPress user. There is no WordPress user created by simply defining the email.) To associate a WordPress user with the EDD customer, please check out the <strong>user_id</strong> argument.", 'wp-webhooks' ); ?>
			<?php
			$parameter['customer_email']['description'] = ob_get_clean();

			$description = array(
				'tipps' => array(
					__( "Creating a customer is not the same as creating a user. Easy Digital Downloads uses its own logic and tables for customers. Still, you can assign a user to a customer usign the <strong>user_id</strong> argument.", 'wp-webhooks' ),
					__( "In case the email you try to use, for adding the customer, already exists within the customer table, the customer won't be created.", 'wp-webhooks' ),
				),
			);

            return array(
                'action'            => 'edd_create_customer',
                'name'              => __( 'Create customer', 'wp-webhooks' ),
                'sentence'              => __( 'create a customer', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => __( 'This webhook action allows you to create a customer within Easy Digital Downloads.', 'wp-webhooks' ),
                'description'       => $description,
                'integration'       => 'edd',
                'premium' 			=> false,
            );

        }

        public function execute( $return_data, $response_body ){

            $customer_id = 0;
			$customer = new stdClass;
			$return_args = array(
				'success' => false,
				'msg' => '',
				'customer_id' => 0,
				'customer_email' => '',
				'additional_emails' => '',
				'customer_first_name' => '',
				'customer_last_name' => '',
				'attach_payments' => '',
				'increase_purchase_count' => '',
				'increase_lifetime_value' => '',
				'customer_notes' => '',
				'customer_meta' => '',
				'user_id' => '',
			);

			$customer_email     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_email' );
			$customer_first_name     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_first_name' );
			$customer_last_name     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_last_name' );
			$additional_emails     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'additional_emails' );
			$attach_payments     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'attach_payments' );
			$increase_purchase_count     = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'increase_purchase_count' ) );
			$user_id     = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' ) );
			$increase_lifetime_value     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'increase_lifetime_value' );
			$set_primary_email     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'set_primary_email' );
			$customer_notes     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_notes' );
			$customer_meta     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_meta' );
			
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( ! class_exists( 'EDD_Customer' ) ){
				$return_args['msg'] = __( 'The class EDD_Customer() is undefined. The user could not be created.', 'wp-webhooks' );
	
				return $return_args;
			}

			if ( ! empty( $customer_email ) ) {
				$customer = new EDD_Customer( $customer_email );
			}

			if( empty( $customer->id ) ){

				if( empty( $customer_first_name ) && empty( $customer_last_name ) ) {
					$name = $customer_email;
				} else {
					$name = trim( $customer_first_name . ' ' . $customer_last_name );
				}
	
				$customer_data = array(
					'name'        => $name,
					'email'       => $customer_email
				);

				//tro to match a WordPress user with an email
				if( empty( $user_id ) && ! empty( $customer_email ) && is_email( $customer_email ) ){
					$wp_user = get_user_by( 'email', sanitize_email( $customer_email ) );
					if ( ! empty( $wp_user ) ) {
						$user_id = $wp_user->ID;
					}
				}

				if( ! empty( $user_id ) ){
					$customer_data['user_id'] = $user_id;
				}
	
				$customer_id = $customer->create( $customer_data );
				
				if( ! empty( $customer_id ) ){

					if( ! empty( $additional_emails ) ){
						$email_arr = explode( ',', $additional_emails );
						if( is_array( $email_arr ) ){
							foreach( $email_arr as $semail ){
								if( is_email( $semail ) ){
									$customer->add_email( $semail );
								}
							}
						}
					}

					if( ! empty( $set_primary_email ) && is_email( $set_primary_email ) ){
						$customer->set_primary_email( $set_primary_email );
					}

					if( ! empty( $attach_payments ) ){
						$payments_arr = explode( ',', $attach_payments );
						if( is_array( $payments_arr ) ){
							foreach( $payments_arr as $spayment ){
								$spayment_settings = explode( ':', $spayment );
								if( in_array( 'no_update_stats', $spayment_settings ) ){
									$customer->attach_payment( intval( $spayment_settings[0] ), false );
								} else {
									$customer->attach_payment( intval( $spayment_settings[0] ) );
								}
							}
						}
					}

					if( ! empty( $increase_purchase_count ) && is_numeric( $increase_purchase_count ) ){
						$customer->increase_purchase_count( $increase_purchase_count );
					}

					if( ! empty( $increase_lifetime_value ) && is_numeric( $increase_lifetime_value ) ){
						$customer->increase_value( $increase_lifetime_value );
					}

					if( ! empty( $customer_notes ) ){
						if( WPWHPRO()->helpers->is_json( $customer_notes ) ){
							$customer_notes_arr = json_decode( $customer_notes, true );
							foreach( $customer_notes_arr as $snote ){
								$customer->add_note( $snote );
							}
						}
					}

					if( ! empty( $customer_meta ) ){
						if( WPWHPRO()->helpers->is_json( $customer_meta ) ){
							$customer_meta_arr = json_decode( $customer_meta, true );
							foreach( $customer_meta_arr as $skey => $sval ){

								if( ! empty( $skey ) ){
									if( $sval == 'ironikus-delete' ){
										$customer->delete_meta( $skey );
									} else {
										$ident = 'ironikus-serialize';
										if( is_string( $sval ) && substr( $sval , 0, strlen( $ident ) ) === $ident ){
											$serialized_value = trim( str_replace( $ident, '', $sval ),' ' );

											if( WPWHPRO()->helpers->is_json( $serialized_value ) ){
												$serialized_value = json_decode( $serialized_value );
											}

											$customer->update_meta( $skey, $serialized_value );

										} else {
											$customer->update_meta( $skey, maybe_unserialize( $sval ) );
										}
									}
								}
							}
						}
					}

					$return_args['customer_id'] = $customer_id;
					$return_args['customer_email'] = $customer_email;
					$return_args['additional_emails'] = $additional_emails;
					$return_args['customer_first_name'] = $customer_first_name;
					$return_args['customer_last_name'] = $customer_last_name;
					$return_args['attach_payments'] = $attach_payments;
					$return_args['increase_purchase_count'] = $increase_purchase_count;
					$return_args['increase_lifetime_value'] = $increase_lifetime_value;
					$return_args['customer_notes'] = $customer_notes;
					$return_args['customer_meta'] = $customer_meta;
					$return_args['user_id'] = $user_id;
					$return_args['msg'] = __( "The customer was successfully created.", 'action-edd_create_customer-success' );
					$return_args['success'] = true;
				} else {
					$return_args['customer_id'] = $customer_id;
					$return_args['msg'] = __( "An error occured creating the user.", 'action-edd_create_customer-success' );
				}

			} else {
				$return_args['msg'] = __( "We could not create the customer. Please set the user_id or the customer_email.", 'action-edd_create_customer-success' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $customer_id, $return_args );
			}

			return $return_args;
            
        }

    }

endif; // End if class_exists check.