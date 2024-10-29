<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_delete_discount' ) ) :

	/**
	 * Load the edd_delete_discount action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_delete_discount {

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
				'discount_id'       => array( 'required' => true, 'short_description' => __( '(Mixed) The dicsount ID or discount code of the discount you want to delete.', 'wp-webhooks' ) ),
				'do_action'     => array( 'short_description' => __( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More info is within the description.', 'wp-webhooks' ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'        => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'        => array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'        => array( 'short_description' => __( '(Array) Containing the discount id of the deleted discount.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The discount code was successfully deleted.',
				'data' => 
				array (
				  'discount_id' => 803,
				),
			);

			$description = array(
				'tipps' => array(
					__( "In case you do not have the discount id, you can also use the discount code to delete the discount.", 'wp-webhooks' )
				),
			);

            return array(
                'action'            => 'edd_delete_discount',
                'name'              => __( 'Delete discount', 'wp-webhooks' ),
                'sentence'              => __( 'delete a discount', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => __( 'This webhook action allows you to delete a dicsount code within Easy Digital Downloads.', 'wp-webhooks' ),
                'description'       => $description,
                'integration'       => 'edd',
                'premium' 			=> false,
            );

        }

        public function execute( $return_data, $response_body ){

            $discount = new stdClass;
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'discount_id' => 0,
				),
			);

			$discount_id   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'discount_id' );
			
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( ! function_exists( 'edd_get_discount_by_code' ) && ! function_exists( 'edd_get_discount_by' ) && ! function_exists( 'edd_remove_discount' ) ){
				$return_args['msg'] = __( 'The functions edd_remove_discount() and edd_get_discount_by() are undefined. The discount code could not be deleted.', 'wp-webhooks' );
	
				return $return_args;
			}

			if( ! empty( $discount_id ) ){
				//Fetch the discount id from the code
				if( ! is_numeric( $discount_id ) ){
					$tmp_dsc_obj = edd_get_discount_by_code( $discount_id );
					if( ! empty( $tmp_dsc_obj->ID ) ){
						$discount_id = $tmp_dsc_obj->ID;
					}
				}
			}

			if( empty( $discount_id ) || ! is_numeric( $discount_id ) ){
				$return_args['msg'] = __( 'We could not find any discount for your given value.', 'wp-webhooks' );
	
				return $return_args;
			}

			edd_remove_discount( $discount_id );

			$return_args['msg'] = __( "The discount code was successfully deleted.", 'action-edd_delete_discount-success' );
			$return_args['data']['discount_id'] = $discount_id;
			$return_args['success'] = true;

			if( ! empty( $do_action ) ){
				do_action( $do_action, $discount_id, $discount, $return_args );
			}

			return $return_args;
            
        }

    }

endif; // End if class_exists check.