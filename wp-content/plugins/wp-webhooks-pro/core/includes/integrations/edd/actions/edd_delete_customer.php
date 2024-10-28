<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_delete_customer' ) ) :

	/**
	 * Load the edd_delete_customer action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_delete_customer {

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
				'customer_value'       => array( 'required' => true, 'short_description' => __( '(String) The actual value you want to use to determine the customer. In case you havent set the get_customer_by argument or you set it to email, place the customer email in here.', 'wp-webhooks' ) ),
				'get_customer_by'       => array( 'short_description' => __( '(String) The type of value you want to use to fetch the customer from the database. Possible values: email, customer_id, user_id. Default: email', 'wp-webhooks' ) ),
				'delete_records'     => array( 
					'type' => 'select',
					'choices' => array(
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					),
					'multiple' => false,
					'default_value' => 'no',
					'short_description' => __( 'Set this argument to "yes" if you want to delete all of the customer records (payments) from the database. More info is within the description.', 'wp-webhooks' ),
				),
				'do_action'     => array( 'short_description' => __( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More info is within the description.', 'wp-webhooks' ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'        => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'        => array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'customer_id'        => array( 'short_description' => __( '(Integer) The ID of the customer', 'wp-webhooks' ) ),
				'get_customer_by'        => array( 'short_description' => __( '(String) The type of value you want to use to fetch the customer from the database. Possible values: email, customer_id, user_id. Default: email', 'wp-webhooks' ) ),
				'customer_value'        => array( 'short_description' => __( '(String) The additional emails you set within the additional_emails argument.', 'wp-webhooks' ) ),
				'delete_records'        => array( 'short_description' => __( 'Set this argument to "yes" if you want to delete all of the customer records (payments) from the database.', 'wp-webhooks' ) ),
				'customer_data'        => array( 'short_description' => __( '(array) The Data from the EDD_Customer class.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The customer was successfully deleted.',
				'customer_id' => '5',
				'get_customer_by' => 'email',
				'customer_value' => 'jondoe@domain.test',
				'delete_records' => false,
				'customer_data' => 
				array (
				),
			);

			ob_start();
			?>
<?php echo __( "The value we use to determine the customer. In case you haven't set the <strong>get_user_by</strong> argument or you have set it to email, please include the customer email in here. If you have chosen the <strong>customer_id</strong>, please include the customer id and in case you set <strong>user_id</strong>, please include the user id.", 'wp-webhooks' ); ?>
			<?php
			$parameter['customer_value']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "Customize the default way we use to fetch the customer from the backend. Possible values are <strong>email</strong> (Default), <strong>customer_id</strong> or <strong>user_id</strong>.", 'wp-webhooks' ); ?>
			<?php
			$parameter['get_customer_by']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "This argument allows you to delete payments assigned to a customer. In case you haven't set it to <strong>yes</strong>, we only remove the user correlation to the payment.", 'wp-webhooks' ); ?>
			<?php
			$parameter['delete_records']['description'] = ob_get_clean();

			$description = array(
				'tipps' => array(
					__( "Deleting a customer is not the same as deleting a user. Easy Digital Downloads uses its own logic and tables for customers.", 'wp-webhooks' ),
					__( "You can also delete all related payment records assigned to a customer. To do that, simply set the <strong>delete_records</strong> argument to <strong>yes</strong>.", 'wp-webhooks' ),
				),
			);

            return array(
                'action'            => 'edd_delete_customer',
                'name'              => __( 'Delete customer', 'wp-webhooks' ),
                'sentence'              => __( 'delete a customer', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => __( 'This webhook action allows you to delete a customer within Easy Digital Downloads.', 'wp-webhooks' ),
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
				'get_customer_by' => '',
				'customer_value' => '',
				'delete_records' => '',
				'customer_data' => '',
			);

			$get_customer_by   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'get_customer_by' );
			$customer_value     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_value' );
			$delete_records     = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'delete_records' ) === 'yes' ) ? true : false;
			
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( ! class_exists( 'EDD_Customer' ) ){
				$return_args['msg'] = __( 'The class EDD_Customer() is undefined. The user could not be deleted.', 'wp-webhooks' );
	
				return $return_args;
			}

			if( empty( $customer_value ) ){
				$return_args['msg'] = __( 'User not deleted. The argument customer_value cannot be empty.', 'wp-webhooks' );
	
				return $return_args;
			}

			switch( $get_customer_by ){
				case 'customer_id':
					$customer = new EDD_Customer( intval( $customer_value ) );
				break;
				case 'user_id':
					$customer = new EDD_Customer( intval( $customer_value ), true );
				break;
				case 'email':
				default:
					$customer = new EDD_Customer( $customer_value );
				break;
			}

			if ( empty( $customer ) || empty( $customer->id ) ) {
				$return_args['msg'] = __( 'The user you tried to delete does not exist.', 'wp-webhooks' );
				return $return_args;
			}

			$customer_id = $customer->id;
			do_action( 'edd_pre_delete_customer', $customer_id, true, $delete_records ); //confirm is always true

			$payments_array = explode( ',', $customer->payment_ids );
			$success        = EDD()->customers->delete( $customer_id );

			if ( $success ) {

				if ( $delete_records ) {

					// Remove all payments, logs, etc
					foreach ( $payments_array as $payment_id ) {
						edd_delete_purchase( $payment_id, false, true );
					}

				} else {

					// Just set the payments to customer_id of 0
					foreach ( $payments_array as $payment_id ) {
						edd_update_payment_meta( $payment_id, '_edd_payment_customer_id', 0 );
					}

				}

				$return_args['customer_id'] = $customer_id;
				$return_args['get_customer_by'] = $get_customer_by;
				$return_args['customer_value'] = $customer_value;
				$return_args['delete_records'] = $delete_records;
				$return_args['customer_data'] = $customer;
				$return_args['msg'] = __( "The customer was successfully deleted.", 'action-edd_delete_customer-success' );
				$return_args['success'] = true;

			} else {

				$return_args['msg'] = __( "Error deleting the customer. (EDD error)", 'action-edd_delete_customer-success' );

			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $customer_id, $customer, $return_args );
			}

			return $return_args;
            
        }

    }

endif; // End if class_exists check.