<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_update_payment' ) ) :

	/**
	 * Load the edd_update_payment action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_update_payment {

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
				'payment_status'    => array( 'short_description' => __( '(String) The status of the payment. Please see the description for further details.', 'wp-webhooks' ) ),
				'do_action'     => array( 'short_description' => __( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More infos are in the description.', 'wp-webhooks' ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'        => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'        => array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'        => array( 'short_description' => __( '(array) Within the data array, you will find further details about the response, as well as the payment id and further information.', 'wp-webhooks' ) ),
				'errors'        => array( 'short_description' => __( '(array) An array containing all errors that might happened during the update.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
                'success' => true,
                'msg' => 'The payment was successfully updated or no changes have been made.',
                'data' => 
                array (
                  'payment_id' => 749,
                  'payment_status' => 'processing',
                ),
                'errors' => 
                array (
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

            ob_start();
			?>
<?php echo __( "This argument allows you to update the status of the payment. Here is a list of the default payment statuses you can use:", 'wp-webhooks' ); ?>
<ol>
    <?php foreach( $payment_statuses as $ps_slug => $ps_name ) : ?>
        <li>
            <strong><?php echo __(  $ps_name, 'wp-webhooks' ); ?></strong>: <?php echo $ps_slug; ?>
        </li>
    <?php endforeach; ?>
</ol>
			<?php
			$parameter['payment_status']['description'] = ob_get_clean();

            return array(
                'action'            => 'edd_update_payment',
                'name'              => __( 'Update payment', 'wp-webhooks' ),
                'sentence'              => __( 'update a payment', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => __( 'This webhook action allows you to update a payment within Easy Digital Downloads.', 'wp-webhooks' ),
                'description'       => array(),
                'integration'       => 'edd',
                'premium' 			=> false,
            );

        }

        public function execute( $return_data, $response_body ){

            $return_args = array(
				'success' => false,
				'msg' => __( "The payment was successfully updated or no changes have been made.", 'action-edd_update_payment-success' ),
				'data' => array(),
				'errors' => array(),
			);

			$payment_id     = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'payment_id' ) );
			$payment_status     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'payment_status' );
			
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $payment_id ) ){
				$return_args['msg'] = __( 'Payment not updated. The argument payment_id cannot be empty.', 'wp-webhooks' );
	
				return $return_args;
			}

			$payment_exists = edd_get_payment_by( 'id', $payment_id );

			if( empty( $payment_exists ) ){
				$return_args['msg'] = __( 'The payment id you tried to update, could not be fetched.', 'wp-webhooks' );
	
				return $return_args;
			}

			$return_args['data']['payment_id'] = $payment_id;
			$return_args['data']['payment_status'] = $payment_status;

			if( ! empty( $payment_status ) ){
				$updates_status = edd_update_payment_status( $payment_id, $payment_status );
				if( ! empty( $updates_status ) ){
					$return_args['success'] = true;
				} else {
					$return_args['msg'] = __( "There have been partial issues with updates", 'action-edd_update_payment-success' );
					$return_args['errors'][] = __( "There was an issue updating the payment status.", 'action-edd_update_payment-success' );
				}
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $payment_id, $return_args );
			}

			return $return_args;
            
        }

    }

endif; // End if class_exists check.