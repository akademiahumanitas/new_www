<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_givewp_Actions_give_get_donor' ) ) :

	/**
	 * Load the give_get_donor action
	 *
	 * @since 6.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_givewp_Actions_give_get_donor {

	public function get_details(){

			$parameter = array(
				'get_by'			=> array(
					'label' => __( 'Gey by', 'wp-webhooks' ),
					'required' => true, 
					'type' => 'select', 
					'multiple' => false, 
					'choices' => array(
						'id' => array(
							'label' => __( 'Donor ID', 'wp-webhooks' ),
						),
						'email' => array(
							'label' => __( 'Email', 'wp-webhooks' ),
						),
						'user_id' => array(
							'label' => __( 'User iD', 'wp-webhooks' ),
						),
					), 
					'short_description' => __( 'Select the type of value you want to get the donor by.', 'wp-webhooks' )
				),
				'value'		=> array(
					'label' => __( 'Value', 'wp-webhooks' ),
					'short_description' => __( 'Set this field to the value depending on what you selected withi the get_by argument.', 'wp-webhooks' )
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired triggers.', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'wp-webhooks' ) ),
			);

		$returns_code = array (
			'success' => true,
			'msg' => 'The donor has been returned successfully.',
			'data' => 
			array (
			  'donor_id' => '3',
			  'donor' => 
			  array (
				'id' => '3',
				'user_id' => '154',
				'email' => 'donoremail@emaildomain.test',
				'name' => 'Jon Doe',
				'purchase_value' => '0.000000',
				'purchase_count' => '0',
				'payment_ids' => '',
				'date_created' => '2022-01-24 05:37:43',
				'token' => '',
				'verify_key' => '',
				'verify_throttle' => '0000-00-00 00:00:00',
			  ),
			  'donor_meta' => 
			  array (
				'_give_donor_company' => 
				array (
				  0 => 'Demo Corp',
				),
				'_give_donor_first_name' => 
				array (
				  0 => 'Jon',
				),
				'_give_donor_last_name' => 
				array (
				  0 => 'Doe',
				),
				'_give_donor_title_prefix' => 
				array (
				  0 => 'Dr.',
				),
			  ),
			),
		  );

		return array(
			'action'			=> 'give_get_donor', //required
			'name'			   => __( 'Get donor', 'wp-webhooks' ),
			'sentence'			   => __( 'get the details of a donor', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => __( 'Get the details of a donor within GiveWP.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'givewp',
            'premium'		   => true,
		);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array()
			);

			$get_by		= sanitize_title( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'get_by' ) );
			$value		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'value' );
			
			if( empty( $get_by ) ){
				$return_args['msg'] = __( "Please set the get_by argument.", 'action-give_get_donor-error' );
				return $return_args;
			}
			
			if( empty( $value ) ){
				$return_args['msg'] = __( "Please set the value argument.", 'action-give_get_donor-error' );
				return $return_args;
			}

			$donor = Give()->donors->get_donor_by( $get_by, $value );

			if( ! is_object( $donor ) || ! isset( $donor->id ) || empty( $donor->id ) ){
				$return_args['msg'] = __( "No donor was found based on your given data.", 'action-give_get_donor-error' );
				return $return_args;
			}

			$donor_id = $donor->id;

			$return_args['success'] = true;
			$return_args['msg'] = __( "The donor has been returned successfully.", 'action-give_get_donor-success' );
			$return_args['data'] = array(
				'donor_id' => $donor_id,
				'donor' => $donor,
				'donor_meta' => Give()->donor_meta->get_meta( $donor_id ),
			);

			return $return_args;
	
		}

	}

endif; // End if class_exists check.