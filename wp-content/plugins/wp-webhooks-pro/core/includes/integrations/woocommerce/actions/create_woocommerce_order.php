<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_Actions_create_woocommerce_order' ) ) :

	/**
	 * Load the create_woocommerce_order action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_woocommerce_Actions_create_woocommerce_order {

		public function get_details(){

				$parameter = array(
				'billing_address'			=> array( 'short_description' => __( 'The billing address of the order. Please see the description for more information.', 'wp-webhooks' ) ),
				'shipping_address'			=> array( 'short_description' => __( 'The shipping address of the order. Please see the description for more information.', 'wp-webhooks' ) ),
				'shipping_lines'			=> array( 'short_description' => __( 'This argument allows you to add certain shipping lines to your order. Please see the description for further details.', 'wp-webhooks' ) ),
				'add_products'			=> array( 'short_description' => __( 'The slist with the product ids and the quantity. More information within the description.', 'wp-webhooks' ) ),
				'calculate_totals'			=> array( 
					'type' => 'select',
					'choices' => array(
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					),
					'multiple' => false,
					'default_value' => 'no',
					'short_description' => __( 'Set it to "yes" in case you want to calculate the order total. Default "no".', 'wp-webhooks' ),
				),
				'payment_complete'			=> array( 
					'type' => 'select',
					'choices' => array(
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					),
					'multiple' => false,
					'default_value' => 'no',
					'short_description' => __( 'Set it to "yes" in case you want to set the payment to complete. You can also set a transation id instead of "yes".', 'wp-webhooks' ),
				),
				'legacy_set_total'			=> array( 'short_description' => __( 'Set the legacy total amount and type. More information within the description.', 'wp-webhooks' ) ),
				'order_meta'			=> array( 'short_description' => __( 'You can also set custom order meta. This meta will be saved as custom values within the post meta table. More information within the description.', 'wp-webhooks' ) ),
				'order_status'			=> array( 'short_description' => __( 'The order status you want to use for the order. Please check the description for more information.', 'wp-webhooks' ) ),
				'customer_id'			=> array( 'short_description' => __( 'The id or the email of the customer for the order.', 'wp-webhooks' ) ),
				'customer_note'			=> array( 'short_description' => __( 'Some text that will be displayed as the customer note.', 'wp-webhooks' ) ),
				'order_parent'			=> array( 'short_description' => __( 'The id of a parent order.', 'wp-webhooks' ) ),
				'created_via'			=> array( 'short_description' => __( 'In identifier where the order was created from. E.g. "wp-webhooks".', 'wp-webhooks' ) ),
				'cart_hash'			=> array( 'short_description' => __( 'A cart hash value.', 'wp-webhooks' ) ),
				'order_id'			=> array( 'short_description' => __( 'A custom order id (Please note that this value may NOT be the order id of the order you currently create).', 'wp-webhooks' ) ),
				'do_action'	  => array( 'short_description' => __( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', 'wp-webhooks' ) )
			);

			ob_start();
?>
<p><?php echo __( 'To add a shipping and/or billing address, you can separate the value from the key with a comma and each dataset with a semicolon. Here is an example of that:', 'wp-webhooks' ); ?></p>
				<pre>
first_name,Max;last_name,Mustermann;company,Demo Copmpany;email,max@mustermann.com;phone,123-456-789;address_1,Street name 12;address_2,Room 123;postcode,12345;city,Mustercity;state,CA;country,USA
				</pre>
				<p><?php echo __( 'The keys (e.g. first_name) are the direct identifier for the value. You can use any key that is available within Woocommerce.', 'wp-webhooks' ); ?></p>
<?php
			$parameter['billing_address']['description'] = ob_get_clean();

			ob_start();
?>
<p><?php echo __( 'To add a shipping and/or billing address, you can separate the value from the key with a comma and each dataset with a semicolon. Here is an example of that:', 'wp-webhooks' ); ?></p>
				<pre>
first_name,Max;last_name,Mustermann;company,Demo Copmpany;email,max@mustermann.com;phone,123-456-789;address_1,Street name 12;address_2,Room 123;postcode,12345;city,Mustercity;state,CA;country,USA
				</pre>
				<p><?php echo __( 'The keys (e.g. first_name) are the direct identifier for the value. You can use any key that is available within Woocommerce.', 'wp-webhooks' ); ?></p>
<?php
			$parameter['shipping_address']['description'] = ob_get_clean();

			ob_start();
?>
<p><?php echo __( 'For adding products, it is required that you know the product id. To add a product, set the product id and comma-separate the quantity of the product. To add multiple products, easily separate them with a comma. Hee is an example:', 'wp-webhooks' ); ?></p>
<pre>156,1;155,2</pre>
<?php
			$parameter['add_products']['description'] = ob_get_clean();

			ob_start();
?>
<p><?php echo __( 'To set the legacy total, it is required to set the amount and with a ":" separated the type. It should look like that (Please define only the values without the double quotes): "123.33:total". Down below you will see a list with all deault types:', 'wp-webhooks' ); ?></p>
<pre>array( 'shipping', 'tax', 'shipping_tax', 'total', 'cart_discount', 'cart_discount_tax' )</pre>
<?php
			$parameter['legacy_set_total']['description'] = ob_get_clean();

			ob_start();
?>
<p><?php echo __( 'You can also add custom order meta. This meta will be added to the post meta table. Here is an example on how this would look like using the simple structure (We also support json):', 'wp-webhooks' ); ?></p>
<br><br>
<pre>meta_key_1,meta_value_1;my_second_key,add_my_value</pre>
<br><br>
<?php echo __( 'To separate the meta from the value, you can use a comma ",". To separate multiple meta settings from each other, easily separate them with a semicolon ";" (It is not necessary to set a semicolon at the end of the last one)', 'wp-webhooks' ); ?>
<br><br>
<?php echo __( 'This is an example on how you can include the order meta using JSON.', 'wp-webhooks' ); ?>
<br>
<pre>{
  "meta_key_1": "This is my meta value 1",
  "another_meta_key": "This is my second meta key!",
  "another_meta_key_1": "ironikus-delete"
}</pre>
<?php
			$parameter['order_meta']['description'] = ob_get_clean();

			ob_start();
?>
<p><?php echo __( 'The order status contains the woocommerce order status. Please also include the woocommerce order status prefix (e.g. wc-pending). Here are the default values as examples in form of an array:', 'wp-webhooks' ); ?></p>
				<pre>
$order_statuses = array(
	'wc-pending'	=> _x( 'Pending payment', 'Order status', 'woocommerce' ),
	'wc-processing' => _x( 'Processing', 'Order status', 'woocommerce' ),
	'wc-on-hold'	=> _x( 'On hold', 'Order status', 'woocommerce' ),
	'wc-completed'  => _x( 'Completed', 'Order status', 'woocommerce' ),
	'wc-cancelled'  => _x( 'Cancelled', 'Order status', 'woocommerce' ),
	'wc-refunded'   => _x( 'Refunded', 'Order status', 'woocommerce' ),
	'wc-failed'	 => _x( 'Failed', 'Order status', 'woocommerce' ),
  );
				</pre>
<?php
			$parameter['order_status']['description'] = ob_get_clean();

			ob_start();
?>
<?php echo __( 'You can also add shipping lines. To do so, you need to parse a JSON construct within the shipping_lines argument.', 'wp-webhooks' ); ?>
				<br>
				<?php echo __( 'Here\'s an example:', 'wp-webhooks' ); ?>
				<br>
				<pre>
[
  {
	"tax_country_code": "DE",
	"tax_state": "",
	"tax_postcode": "",
	"tax_city": "",
	"method_title": "REMOTE SHIP",
	"method_id": "",
	"price": "18.33",
	"end_date": "Dec 27, 2019 07:00PM"
  }
]
				</pre>
				<br>
				<?php echo __( 'For the method_id you can define an already existing id. For example: flat_rate:14', 'wp-webhooks' ); ?>
				<br>
				<?php echo __( 'If you want to add multiple shipping lines, you can comma separate them within the first dimension of the JSON construct.', 'wp-webhooks' ); ?>
<?php
			$parameter['shipping_lines']['description'] = ob_get_clean();

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(mixed) The set data (inc. order id) in success, the error on failure.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'shipping_address'		=> array( 'short_description' => __( '(string) The argument input of the shipping address field.', 'wp-webhooks' ) ),
				'billing_address'		=> array( 'short_description' => __( '(string) The argument input of the billing address field.', 'wp-webhooks' ) ),
				'add_products'		=> array( 'short_description' => __( '(string) The input of add_products argument.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'Order created successfully.',
				'data' => 
				array (
				  'meta_data' => NULL,
				  'default_args' => NULL,
				  'add_products' => NULL,
				  'billing_address' => NULL,
				  'shipping_address' => NULL,
				  'legacy_set_total' => NULL,
				  'calculate_totals' => NULL,
				  'new_order_id' => NULL,
				  'order_status' => NULL,
				),
				'shipping_address' => 'first_name,Max;last_name,Mustermann;company,Demo Copmpany;email,max@mustermann.com;phone,123-456-789;address_1,Street name 12;address_2,Room 123;postcode,12345;city,Mustercity;state,CA;country,USA
			  ',
				'billing_address' => 'first_name,Max;last_name,Mustermann;company,Demo Copmpany;email,max@mustermann.com;phone,123-456-789;address_1,Street name 12;address_2,Room 123;postcode,12345;city,Mustercity;state,CA;country,USA',
				'add_products' => '9,2',
				'default_args' => 
				array (
				),
				'meta_data' => false,
				'calculate_totals' => 'yes',
				'legacy_set_total' => false,
				'new_order_id' => 10,
				'order_status' => false,
				'shipping_lines' => '[
				{
				  "tax_country_code": "DE",
				  "tax_state": "",
				  "tax_postcode": "",
				  "tax_city": "",
				  "method_title": "REMOTE SHIP",
				  "method_id": "",
				  "price": "18.33",
				  "end_date": "Dec 27, 2019 07:00PM"
				}
			  ]',
				'pay_now_url' => 'https://yourdomain.test/?page_id=7&#038;order-pay=10&#038;pay_for_order=true&#038;key=wc_order_YahlGo76pk5k1',
			);

			return array(
				'action'			=> 'create_woocommerce_order', //required
				'name'			   => __( 'Create Woocommerce order', 'wp-webhooks' ),
				'sentence'			   => __( 'create a Woocommerce order', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Create a Woocommerce order on your website using webhooks.', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'woocommerce',
				'premium'		  => true,
			);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'meta_data' => null,
					'default_args' => null,
					'add_products' => null,
					'billing_address' => null,
					'shipping_address' => null,
					'legacy_set_total' => null,
					'calculate_totals' => null,
					'new_order_id' => null,
					'order_status' => null,
				)
			);

			$billing_address			= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'billing_address' );
			$shipping_address		   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'shipping_address' );
			$add_products		   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'add_products' );
			$calculate_totals		   = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'calculate_totals' ) == 'yes' ) ? 'yes' : 'no';
			$payment_complete		   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'payment_complete' );
			$legacy_set_total		   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'legacy_set_total' );
			$order_meta		   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'order_meta' );
			$shipping_lines		   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'shipping_lines' );

			//Default args
			$order_status		   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'order_status' );
			$customer_id		   	= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_id' );
			$customer_note		   	= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_note' );
			$order_parent		   	= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'order_parent' );
			$created_via		   	= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'created_via' );
			$cart_hash		   	= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'cart_hash' );
			$order_id		   	= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'order_id' ) );

			$do_action	  = sanitize_title( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' ) );

			
			$default_args = array();

			if( ! empty( $customer_id ) ){ 

				$customer_real_id = 0;
				if( is_numeric( $customer_id ) ){
					$customer_real_id = intval( $customer_id );
				} elseif ( is_email( $customer_id ) ) {
					$get_user = get_user_by( 'email', $customer_id );
					if( ! empty( $get_user ) && ! empty( $get_user->data ) && ! empty( $get_user->data->ID ) ){
						$customer_real_id = $get_user->data->ID;
					}
				}
				
				$default_args['customer_id'] = $customer_real_id; 
			}
			if( ! empty( $customer_note ) ){ $default_args['customer_note'] = $customer_note; }
			if( ! empty( $order_parent ) ){ $default_args['parent'] = $order_parent; }
			if( ! empty( $created_via ) ){ $default_args['created_via'] = $created_via; }
			if( ! empty( $cart_hash ) ){ $default_args['cart_hash'] = $cart_hash; }
			if( ! empty( $order_id ) ){ $default_args['order_id'] = $order_id; }

			$new_order_id = 0;
			$order = wc_create_order( $default_args );

			if( ! empty( $order ) ){

				$new_order_id = $order->get_id();

				//Set billing address
				if( ! empty( $billing_address ) ){
					$validated_billing_address = array();
					$billing_array = explode( ';', $billing_address );
					if( is_array( $billing_array ) ){
						foreach( $billing_array as $single_billing ){
							$billing_single_data = explode( ',', $single_billing, 2 );
							if( isset( $billing_single_data[0] ) && isset( $billing_single_data[1] ) ){
								$validated_billing_address[ $billing_single_data[0] ] = $billing_single_data[1];
							}
						}
						
						if( ! empty( $validated_billing_address ) ){
							$order->set_address( $validated_billing_address, 'billing' );
						}
	
					}
				}

				//Set shipping address
				if( ! empty( $shipping_address ) ){
					$validated_shipping_address = array();
					$shipping_array = explode( ';', $shipping_address );
					if( is_array( $shipping_array ) ){
						foreach( $shipping_array as $single_shipping ){
							$shipping_single_data = explode( ',', $single_shipping, 2 );
							if( isset( $shipping_single_data[0] ) && isset( $shipping_single_data[1] ) ){
								$validated_shipping_address[ $shipping_single_data[0] ] = $shipping_single_data[1];
							}
						}
						
						if( ! empty( $validated_shipping_address ) ){
							$order->set_address( $validated_shipping_address, 'shipping' );
						}
	
					}
				}

				//Set products
				if( ! empty( $add_products ) ){
					$validated_product_array = explode( ';', $add_products );
					if( is_array( $validated_product_array ) ){
						foreach( $validated_product_array as $single_product ){
							$single_product_data = explode( ',', $single_product, 2 );
							if( isset( $single_product_data[0] ) && isset( $single_product_data[1] ) && is_numeric( $single_product_data[0] ) && is_numeric( $single_product_data[1] ) ){
								$order->add_product( wc_get_product( intval( $single_product_data[0] ) ), intval( $single_product_data[1] ) );
							}
						}
	
					}
				}

				//Shipping related logic
				$country_code = $order->get_shipping_country();
				if( ! empty( $shipping_lines ) && WPWHPRO()->helpers->is_json( $shipping_lines ) ){
					$shipping_line_items = json_decode( $shipping_lines, true );
					if( is_array( $shipping_line_items ) ){
						foreach( $shipping_line_items as $item_key => $item_values ){
							$calculate_tax_for = array(
								'country' => isset( $item_values['tax_country_code'] ) ? $item_values['tax_country_code'] : $country_code,
								'state' => isset( $item_values['tax_state'] ) ? $item_values['tax_state'] : '', // Can be set (optional)
								'postcode' => isset( $item_values['tax_postcode'] ) ? $item_values['tax_postcode'] : '', // Can be set (optional)
								'city' => isset( $item_values['tax_city'] ) ? $item_values['tax_city'] : '', // Can be set (optional)
							);
							$shipping_item = new WC_Order_Item_Shipping();

							if( isset( $item_values['method_title'] ) ){
								$shipping_item->set_method_title( esc_sql( $item_values['method_title'] ) ); // e.g. "My Flatrate"
							}

							if( isset( $item_values['method_id'] ) ){
								$shipping_item->set_method_id( $item_values['method_id'] ); // set an existing Shipping method rate ID e.g.: "flat_rate:14"
							}

							if( isset( $item_values['price'] ) ){
								$shipping_item->set_total( $item_values['price'] ); // (optional)
							}

							$shipping_item->calculate_taxes($calculate_tax_for);

							$order->add_item( $shipping_item );
						}
					}
						
				}


				if( ! empty( $legacy_set_total ) ){
					$legacy_set_total_data = explode( ':', $legacy_set_total );
					if( isset( $legacy_set_total_data[0] ) && is_numeric( $legacy_set_total_data[0] ) && isset( $legacy_set_total_data[1] ) ){
						$order->legacy_set_total( $legacy_set_total_data[0], sanitize_title( $legacy_set_total_data[1] ) );
					}
				}

				$order->set_created_via( 'wpwhpro-woocommerce' );
				$order->set_prices_include_tax( 'yes' === get_option( 'woocommerce_prices_include_tax' ) );

				if( $calculate_totals === 'yes' ){
					$order->calculate_totals();
				}

				if( ! empty( $order_status ) ){ 
					$default_args['status'] = $order_status; 
					$order->set_status( $order_status );
				}

				$order->save();

				if( ! empty( $payment_complete ) && $payment_complete !== 'no' ){
					if( $payment_complete === 'yes' ){
						$transction_id = '';
					} else {
						$transction_id = $payment_complete;
					}
					$order->payment_complete( $transction_id );
				}

				//Custom meta at the end (priority)
				if( ! empty( $order_meta ) ){

					if( WPWHPRO()->helpers->is_json( $order_meta ) ){
	
						$post_meta_data = json_decode( $order_meta, true );
						foreach( $post_meta_data as $skey => $svalue ){
	
							if( ! empty( $skey ) ){
								if( $svalue == 'ironikus-delete' ){
									$order->delete_meta_data( $skey );
								} else {
	
									$ident = 'ironikus-serialize';
									if( is_string( $svalue ) && substr( $svalue , 0, strlen( $ident ) ) === $ident ){
										$serialized_value = trim( str_replace( $ident, '', $svalue ),' ' );
	
										if( WPWHPRO()->helpers->is_json( $serialized_value ) ){
											$serialized_value = json_decode( $svalue );
										}
	
										$order->update_meta_data( $skey, $serialized_value );
	
									} else {
										$order->update_meta_data( $skey, maybe_unserialize( $svalue ) );
									}
	
								}
							}
						}
	
					} else {
	
						$post_meta_data = explode( ';', trim( $order_meta, ';' ) );
						foreach( $post_meta_data as $single_meta ){
							$single_meta_data   = explode( ',', $single_meta );
							$meta_key		   = sanitize_text_field( $single_meta_data[0] );
							$meta_value		 = sanitize_text_field( $single_meta_data[1] );
	
							if( ! empty( $meta_key ) ){
								if( $meta_value == 'ironikus-delete' ){
									$order->delete_meta_data( $meta_key );
								} else {
	
									$ident = 'ironikus-serialize';
									if( substr( $meta_value , 0, strlen( $ident ) ) === $ident ){
										$serialized_value = trim( str_replace( $ident, '', $meta_value ),' ' );
	
										if( WPWHPRO()->helpers->is_json( $serialized_value ) ){
											$serialized_value = json_decode( $meta_value );
										}
	
										$order->update_meta_data( $meta_key, $serialized_value );
	
									} else {
										$order->update_meta_data( $meta_key, maybe_unserialize( $meta_value ) );
									}
								}
							}
						}
	
					}

					//Save the meta
					$order->save_meta_data();
	
				}

				$return_args['shipping_address'] = $shipping_address;
				$return_args['billing_address'] = $billing_address;
				$return_args['add_products'] = $add_products;
				$return_args['default_args'] = $default_args;
				$return_args['meta_data'] = $order_meta;
				$return_args['calculate_totals'] = $calculate_totals;
				$return_args['legacy_set_total'] = $legacy_set_total;
				$return_args['new_order_id'] = $new_order_id;
				$return_args['order_status'] = $order_status;
				$return_args['shipping_lines'] = $shipping_lines;

				//Add the payment URL
				$order = wc_get_order( $new_order_id );
				$pay_now_url = esc_url( $order->get_checkout_payment_url() );
				$return_args['pay_now_url'] = $pay_now_url;

				$return_args['success'] = true;
				$return_args['msg'] = __( "Order created successfully.", 'action-create_woocommerce_order-success' );

			} else {
				$return_args['data'] = $order;
				$return_args['msg'] = __( "Error while creating the base order.", 'action-create_woocommerce_order' );
			}


			if( ! empty( $do_action ) ){
				do_action( $do_action, $new_order_id, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.