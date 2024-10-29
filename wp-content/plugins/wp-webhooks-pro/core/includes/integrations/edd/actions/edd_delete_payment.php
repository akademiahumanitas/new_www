<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_delete_payment' ) ) :

	/**
	 * Load the edd_delete_payment action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_delete_payment {

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
				'payment_id'       => array( 'required' => true, 'short_description' => __( '(Integer) The id of the payment you want to update.', 'wp-webhooks' ) ),
				'update_customer_stats'    => array( 
					'type' => 'select',
					'choices' => array(
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					),
					'multiple' => false,
					'default_value' => 'no',
					'short_description' => __( '(String) Set this value to "yes" to update the statistics of the customer. Default: no', 'wp-webhooks' ),
				),
				'delete_download_logs'    => array( 
					'type' => 'select',
					'choices' => array(
						'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
					),
					'multiple' => false,
					'default_value' => 'no',
					'short_description' => __( '(String) Set this value to "yes" to delete the payment including all its related download logs. Default: no', 'wp-webhooks' ),
				),
				'do_action'     => array( 'short_description' => __( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More infos are in the description.', 'wp-webhooks' ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'        => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'        => array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'        => array( 'short_description' => __( '(array) Within the data array, you will find further details about the response, as well as the payment id and further information.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The payment was successfully deleted.',
				'data' => 
				array (
				  'payment_id' => 747,
				),
			);

			ob_start();
			?>
<?php echo __( "Set this value to <strong>yes</strong> to decrease the purchase count, as well as the total value.", 'wp-webhooks' ); ?>
			<?php
			$parameter['update_customer_stats']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo __( "This argument can remove all download logs that are related to the payment id. To delete the logs, simply set the argument value to <strong>yes</strong>", 'wp-webhooks' ); ?>
			<?php
			$parameter['delete_download_logs']['description'] = ob_get_clean();

			$description = array(
				'tipps' => array(
					__( "By default, we do not update the statistics of the user (lifetime value, etc). To recalculate the statustics, please set the <strong>update_customer_stats</strong> argument to 'yes'.", 'wp-webhooks' ),
					__( "You can also delete all the download logs of the payment using the <strong>delete_download_logs</strong> argument. Set it to <strong>yes</strong> to delete them too.", 'wp-webhooks' ),
				),
			);

            return array(
                'action'            => 'edd_delete_payment',
                'name'              => __( 'Delete payment', 'wp-webhooks' ),
                'sentence'              => __( 'delete a payment', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => __( 'This webhook action allows you to delete a payment within Easy Digital Downloads.', 'wp-webhooks' ),
                'description'       => $description,
                'integration'       => 'edd',
                'premium' 			=> false,
            );

        }

        public function execute( $return_data, $response_body ){

            $return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(),
			);

			$payment_id     = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'payment_id' ) );
			$update_customer_stats     	= ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'update_customer_stats' ) === 'yes' ) ? true : false;
			$delete_download_logs   = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'delete_download_logs' ) === 'yes' ) ? true : false;
			
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $payment_id ) ){
				$return_args['msg'] = __( 'Payment not deleted. The argument payment_id cannot be empty.', 'wp-webhooks' );
	
				return $return_args;
			}

			$payment_exists = edd_get_payment_by( 'id', $payment_id );

			if( empty( $payment_exists ) ){
				$return_args['msg'] = __( 'The payment id you tried to delete, could not be fetched.', 'wp-webhooks' );
	
				return $return_args;
			}

			$return_args['data']['payment_id'] = $payment_id;
			$return_args['data']['update_customer'] = $update_customer_stats;
			$return_args['data']['delete_download_logs'] = $delete_download_logs;

			edd_delete_purchase( $payment_id, $update_customer_stats, $delete_download_logs ); //void function
			$return_args['success'] = true;
			$return_args['msg'] = __( "The payment was successfully deleted.", 'action-edd_delete_payment-success' );

			if( ! empty( $do_action ) ){
				do_action( $do_action, $payment_id, $return_args );
			}

			return $return_args;
            
        }

    }

endif; // End if class_exists check.