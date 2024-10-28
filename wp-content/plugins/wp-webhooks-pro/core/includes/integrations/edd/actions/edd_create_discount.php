<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_create_discount' ) ) :

	/**
	 * Load the edd_create_discount action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_create_discount {

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
				'code'       => array( 'required' => true, 'short_description' => __( '(String) The dicsount code you would like to set for this dicsount. Only alphanumeric characters are allowed.', 'wp-webhooks' ) ),
				'name'       => array( 'short_description' => __( '(String) The name to identify the discount code.', 'wp-webhooks' ) ),
				'status'     => array( 'short_description' => __( '(String) The status of the discount code. Default: active', 'wp-webhooks' ) ),
				'current_uses'     => array( 'short_description' => __( '(Integer) A number that tells how many times the coupon code has been already used.', 'wp-webhooks' ) ),
				'max_uses'     => array( 'short_description' => __( '(Integer) The number of how often the discount code can be used in total.', 'wp-webhooks' ) ),
				'amount'     => array( 'short_description' => __( '(Mixed) The amount of the discount code. If chosen percent, use an interger, for an amount, use float. More info is within the description.', 'wp-webhooks' ) ),
				'start_date'     => array( 'short_description' => __( '(String) The start date of the availability of the discount code. More info is within the description.', 'wp-webhooks' ) ),
				'expiration_date'     => array( 'short_description' => __( '(String) The end date of the availability of the discount code. More info is within the description.', 'wp-webhooks' ) ),
				'type'     => array( 'short_description' => __( '(String) The type of the discount code. Default: percent. More info is within the description.', 'wp-webhooks' ) ),
				'min_price'     => array( 'short_description' => __( '(Mixed) The minimum price that needs to be reached to use the discount code. More info is within the description.', 'wp-webhooks' ) ),
				'product_requirement'     => array( 'short_description' => __( 'A comma-separated list of download IDs that are required to apply the discount code. More info is within the description.', 'wp-webhooks' ) ),
				'product_condition'     => array( 'short_description' => __( '(String) A string containing further conditions on when the discount code can be applied. More info is within the description.', 'wp-webhooks' ) ),
				'excluded_products'     => array( 'short_description' => __( '(String) A comma-separated list, containing all the products that are excluded from the discount code. More info is within the description.', 'wp-webhooks' ) ),
				'is_not_global'     => array(
					'type' => 'select',
					'choices' => array(
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					),
					'multiple' => false,
					'default_value' => 'no',
					'short_description' => __( '(String) Set this argument to "yes" if you do not want to apply the discount code globally to all products. Default: no. More info is within the description.', 'wp-webhooks' ),
				),
				'is_single_use'     => array( 
					'type' => 'select',
					'choices' => array(
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					),
					'multiple' => false,
					'default_value' => 'no',
					'short_description' => __( '(String) Set this argument to "yes" if you want to limit this discount code to only a single use per customer. Default: no. More info is within the description.', 'wp-webhooks' ),
				),
				'do_action'     => array( 'short_description' => __( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More info is within the description.', 'wp-webhooks' ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'        => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'        => array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'        => array( 'short_description' => __( '(Array) Containing all of the predefined data of the webhook, as well as the discount id in case it was successfully created.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The discount code was successfully created.',
				'data' => 
				array (
				  'code' => 'erthsashtsw',
				  'name' => 'Demo Discount Code',
				  'status' => 'inactive',
				  'uses' => '5',
				  'max' => '10',
				  'amount' => '11.10',
				  'start' => '05/23/2020 00:00:00',
				  'expiration' => '06/27/2020 23:59:59',
				  'type' => 'flat',
				  'min_price' => '22',
				  'products' => 
				  array (
					0 => '176',
					1 => '772',
				  ),
				  'product_condition' => 'any',
				  'excluded-products' => 
				  array (
					0 => '774',
				  ),
				  'not_global' => true,
				  'use_once' => true,
				  'discount_id' => 805,
				),
			);

			ob_start();
			?>
<?php echo __( "Defines the status in which you want to create the discount code with. Possible values are <strong>active</strong> and <strong>inactive</strong>. By default, this value is set to <strong>active</strong>.", 'wp-webhooks' ); ?>
			<?php
			$parameter['status']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "This argument accepts a number that defines how often this discount code has been already used. Usually, you do not need to define this argument for creating a discount code.", 'wp-webhooks' ); ?>
			<?php
			$parameter['current_uses']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "This argument defines the maximal number on how often this discount code can be applied. Set it to <strong>0</strong> for unlimited uses.", 'wp-webhooks' ); ?>
			<?php
			$parameter['max_uses']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "The amount argument accepts different values, based on the type you set. By default, you can set this value to the number of percents you want to discount the order. E.g.: <strong>10</strong> will be represented as ten percent. If the <strong>type</strong> argument is set to <strong>flat</strong>, it would discount 10$ (or the currency you choose for your shop).", 'wp-webhooks' ); ?>
			<?php
			$parameter['amount']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "Set the date you want this discount code do become active. We recommend using the SQL format: <strong>2020-03-10 17:16:18</strong>. This arguments also accepts other formats - if you have no chance of changing the date format, its the best if you simply give it a try.", 'wp-webhooks' ); ?>
			<?php
			$parameter['start_date']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "Set the date you want this discount code do become inactive. We recommend using the SQL format: <strong>2020-03-10 17:16:18</strong>. This arguments also accepts other formats - if you have no chance of changing the date format, its the best if you simply give it a try.", 'wp-webhooks' ); ?>
			<?php
			$parameter['expiration_date']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "This argument defines the type of the discount code. If you want to use a percentage, set this argument to <strong>percent</strong>. If you would like to use a flat amount, please set it to <strong>flat</strong>. Based on the given value, you might also want to adjust the <strong>amount</strong> argument.", 'wp-webhooks' ); ?>
			<?php
			$parameter['type']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "Set a minimum price that needs to be reached for a purchase to actually apply this discount code. Please write the price in the following format: 19.99", 'wp-webhooks' ); ?>
			<?php
			$parameter['min_price']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "In case you want to limit the discount code to only certain downloads, this argument is made for you. Simply separate the download IDs that are required by a comma. Here is an example:", 'wp-webhooks' ); ?>
<pre>123,443</pre>
			<?php
			$parameter['product_requirement']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "In case you set this argument to <strong>all</strong>, it is required to have all downloads from the <strong>product_requirement</strong> argument within the cart before the coupon will be applied. If you set the argument to <strong>any</strong>, only one of the products mentioned within the <strong>product_requirement</strong> argument have to be within the cart.", 'wp-webhooks' ); ?>
			<?php
			$parameter['product_condition']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "In case you want to limit certain downloads from applying this coupon code to, this argument is made for you. Simply comma-separate the download IDs that the coupon code should ignore. Here is an example:", 'wp-webhooks' ); ?>
			<?php
			$parameter['excluded_products']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "Set this argument to <strong>yes</strong> in case you do not want to apply the discount code globally on the whole order. If you set this argument to <strong>yes</strong>, it will only be applied to the downloads you defined within the <strong>product_requirement</strong> argument. Default: <strong>no</strong>", 'wp-webhooks' ); ?>
			<?php
			$parameter['is_not_global']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "Set this argument to <strong>yes</strong> in case you want to limit the use of this discount code to only one time per customer. Default: <strong>no</strong>", 'wp-webhooks' ); ?>
			<?php
			$parameter['is_single_use']['description'] = ob_get_clean();

			$description = array(
				'tipps' => array(
					__( "By changing the <strong>type</strong> argument, you can switch between flat or percentage based discounts.", 'wp-webhooks' )
				),
			);

            return array(
                'action'            => 'edd_create_discount',
                'name'              => __( 'Create discount', 'wp-webhooks' ),
                'sentence'          => __( 'create a discount', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => __( 'This webhook action allows you to create a dicsount code within Easy Digital Downloads.', 'wp-webhooks' ),
                'description'       => $description,
                'integration'       => 'edd',
                'premium' 			=> false,
            );

        }

        public function execute( $return_data, $response_body ){

            $discount_id = 0;
			$discount = new stdClass;
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'code'              => '',
					'name'              => '',
					'status'            => 'active',
					'current_uses'		=> '',
					'max_uses'          => '',
					'amount'            => '',
					'start_date'             => '',
					'expiration_date'        => '',
					'type'              => '',
					'min_price'         => '',
					'product_requirement'      => array(),
					'product_condition' => '',
					'excluded_products' => array(),
					'is_not_global'     => false,
					'is_single_use'     => false,
				),
			);

			$code   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'code' );
			$name     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'name' );
			$status     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'status' );
			$current_uses     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'current_uses' );
			$max_uses     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'max_uses' );
			$amount     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'amount' );
			$start_date     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'start_date' );
			$expiration_date     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'expiration_date' );
			$type     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'type' );
			$min_price     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'min_price' );
			$product_requirement     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'product_requirement' );
			$product_condition     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'product_condition' );
			$excluded_products     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'excluded_products' );
			$is_not_global     = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'is_not_global' ) === 'yes' ) ? true : false;
			$is_single_use     = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'is_single_use' ) === 'yes' ) ? true : false;
			
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( ! class_exists( 'EDD_Discount' ) ){
				$return_args['msg'] = __( 'The class EDD_Discount() is undefined. The discount code could not be created.', 'wp-webhooks' );
	
				return $return_args;
			}

			if( empty( $code ) ){
				$return_args['msg'] = __( 'No code given. The argument code cannot be empty.', 'wp-webhooks' );
	
				return $return_args;
			}

			$discount = new EDD_Discount();
			$discount_args = array(
				'code' => $code
			);

			if( ! empty( $name ) ){
				$discount_args['name'] = $name;
			}

			if( ! empty( $status ) ){
				$discount_args['status'] = $status;
			}

			if( ! empty( $current_uses ) ){
				$discount_args['uses'] = $current_uses;
			}

			if( ! empty( $max_uses ) ){
				$discount_args['max'] = $max_uses;
			}

			if( ! empty( $amount ) ){
				$discount_args['amount'] = $amount;
			}

			if( ! empty( $start_date ) ){
				$discount_args['start'] = $start_date;
			}

			if( ! empty( $expiration_date ) ){
				$discount_args['expiration'] = $expiration_date;
			}

			if( ! empty( $type ) ){
				$discount_args['type'] = $type;
			}

			if( ! empty( $min_price ) ){
				$discount_args['min_price'] = $min_price;
			}

			if( ! empty( $product_requirement ) ){
				$product_requirement = explode( ',', trim( $product_requirement, ',' ) );
				$discount_args['products'] = $product_requirement;
			}

			if( ! empty( $product_condition ) ){
				$discount_args['product_condition'] = $product_condition;
			}

			if( ! empty( $excluded_products ) ){
				$excluded_products = explode( ',', trim( $excluded_products, ',' ) );
				$discount_args['excluded-products'] = $excluded_products;
			}

			if( ! empty( $is_not_global ) ){
				$discount_args['not_global'] = $is_not_global;
			}

			if( ! empty( $is_single_use ) ){
				$discount_args['use_once'] = $is_single_use;
			}

			$discount_args = apply_filters( 'wpwh/actions/edd_create_discount/filter_discount_arguments', $discount_args );

			$discount_id = $discount->add( $discount_args );
			
			//fallback since the ID is not directly available within the class
			if( ! empty( $discount_id ) && is_numeric( $discount_id ) ){
				$discount = new EDD_Discount( $discount_id );
			}

			if ( empty( $discount ) || empty( $discount->ID ) ) {
				$return_args['msg'] = __( 'The discount code was not created.', 'wp-webhooks' );
				return $return_args;
			}

			$return_args['data'] = $discount_args;
			$return_args['data']['discount_id'] = $discount_id;
			$return_args['msg'] = __( "The discount code was successfully created.", 'action-edd_create_discount-success' );
			$return_args['success'] = true;

			if( ! empty( $do_action ) ){
				do_action( $do_action, $discount_id, $discount, $return_args );
			}

			return $return_args;
            
        }

    }

endif; // End if class_exists check.