<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_delete_license' ) ) :

	/**
	 * Load the edd_delete_license action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_delete_license {

        public function is_active(){

            $is_active = class_exists( 'EDD_Software_Licensing' );

            //Backwards compatibility for the "Easy Digital Downloads" integration
            if( defined( 'WPWH_EDD_NAME' ) ){
                $is_active = false;
            }

            return $is_active;
        }

        public function get_details(){

			$parameter = array(
				'license_id'       => array( 'required' => true, 'short_description' => __( '(Mixed) The license id or the license key of the license you would like to delete. Please see the description for further details.', 'wp-webhooks' ) ),
				'do_action'     => array( 'short_description' => __( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More info is within the description.', 'wp-webhooks' ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'        => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'        => array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
				'data'        => array( 'short_description' => __( '(Array) Containing the license id, as well as the license object.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
                'success' => true,
                'msg' => 'The license was successfully deleted.',
                'data' => 
                array (
                  'license_id' => '4fc336680bf576cc0298777278ceb15a',
                  'license' => 
                  array (
                    'ID' => 16,
                  ),
                ),
            );

            ob_start();
			?>
<?php echo __( "This argument accepts either the numeric license id or the license key that was set for the license. E.g. 4fc336680bf576cc0298777278ceb15a", 'wp-webhooks' ); ?>
			<?php
			$parameter['license_id']['description'] = ob_get_clean();

			$description = array(
				'tipps' => array(
					__( "In case you already deleted the license and there is no license with your given id or key anymore, we will also throw an error.", 'wp-webhooks' ),
				),
			);

            return array(
                'action'            => 'edd_delete_license',
                'name'              => __( 'Delete license', 'wp-webhooks' ),
                'sentence'              => __( 'delete a license', 'wp-webhooks' ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => __( 'This webhook action allows you to delete a license within Easy Digital Downloads - Software Licensing.', 'wp-webhooks' ),
                'description'       => $description,
                'integration'       => 'edd',
                'premium' 			=> false,
            );

        }

        public function execute( $return_data, $response_body ){

            $license_id = 0;
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'license_id' => 0,
					'license' => 0,
				),
			);

			$license_id   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'license_id' );
			
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( ! class_exists( 'EDD_SL_License' ) ){
				$return_args['msg'] = __( 'The class EDD_SL_License() does not exist. The license was not renewed.', 'wp-webhooks' );
				return $return_args;
			}

			if( empty( $license_id ) ){
				$return_args['msg'] = __( 'The license_id argument cannot be empty. The license was not renewed.', 'wp-webhooks' );
				return $return_args;
			}
            
            $license = new EDD_SL_License( $license_id );

			$check = $license->delete();

			if( $check ){
				$return_args['msg'] = __( "The license was successfully deleted.", 'action-edd_delete_license-success' );
				$return_args['success'] = true;
				$return_args['data']['license_id'] = $license_id;
				$return_args['data']['license'] = $license;
			} else {
				$return_args['msg'] = __( "Error deleting the license.", 'action-edd_delete_license-success' );
			}
		
			

			if( ! empty( $do_action ) ){
				do_action( $do_action, $license_id, $license, $return_args );
			}

			return $return_args;
    
        }

    }

endif; // End if class_exists check.