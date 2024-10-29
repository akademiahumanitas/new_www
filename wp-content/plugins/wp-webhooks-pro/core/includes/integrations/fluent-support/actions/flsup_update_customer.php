<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_fluent_support_Actions_flsup_update_customer' ) ) :
	/**
	 * Load the flsup_update_customer action
	 *
	 * @since 6.0.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_fluent_support_Actions_flsup_update_customer {

		public function get_details() {
			$parameter = array(
				'email'          => array(
					'required'          => true,
					'label'             => __( 'Email', 'wp-webhooks' ),
					'short_description' => __( '(String) The email address of the customer you want to update.', 'wp-webhooks' ),
				),
				'first_name'     => array(
					'label'             => __( 'First name', 'wp-webhooks' ),
					'short_description' => __( '(String) The first name.', 'wp-webhooks' ),
				),
				'last_name'      => array(
					'label'             => __( 'Last name', 'wp-webhooks' ),
					'short_description' => __( '(String) The last name.', 'wp-webhooks' ),
				),
				'job_title'      => array(
					'label'             => __( 'Job title', 'wp-webhooks' ),
					'short_description' => __( '(String) The job title.', 'wp-webhooks' ),
				),
				'status'         => array(
					'label'             => __( 'Customer status', 'wp-webhooks' ),
					'type'              => 'select',
					'default_value'     => 'active',
					'choices'           => array(
						'active'   => array( 'label' => __( 'Active', 'wp-webhooks' ) ),
						'inactive' => array( 'label' => __( 'Blocked', 'wp-webhooks' ) ),
					),
					'short_description' => __( '(String) The company status.', 'wp-webhooks' ),
				),
				'note'           => array(
					'label'             => __( 'Note', 'wp-webhooks' ),
					'short_description' => __( '(String) The note.', 'wp-webhooks' ),
				),
				'country'        => array(
					'type'              => 'select',
					'multiple'          => false,
					'query'             => array(
						'filter' => 'countries',
						'args'   => array(),
					),
					'label'             => __( 'Country', 'wp-webhooks' ),
					'short_description' => __( '(String) The country of the customer.', 'wp-webhooks' ),
				),
				'address_line_1' => array(
					'label'             => __( 'Address line 1', 'wp-webhooks' ),
					'short_description' => __( '(String) The address line 1.', 'wp-webhooks' ),
				),
				'address_line_2' => array(
					'label'             => __( 'Address line 2', 'wp-webhooks' ),
					'short_description' => __( '(String) The address line 2.', 'wp-webhooks' ),
				),
				'city'           => array(
					'label'             => __( 'City', 'wp-webhooks' ),
					'short_description' => __( '(String) The city.', 'wp-webhooks' ),
				),
				'state'          => array(
					'label'             => __( 'State', 'wp-webhooks' ),
					'short_description' => __( '(String) The state.', 'wp-webhooks' ),
				),
				'zip'            => array(
					'label'             => __( 'Zip Code', 'wp-webhooks' ),
					'short_description' => __( '(String) The zip code.', 'wp-webhooks' ),
				),
				'avatar'            => array(
					'label'             => __( 'Avatar URL', 'wp-webhooks' ),
					'short_description' => __( '(String) The URL to an avatar image of your choice.', 'wp-webhooks' ),
				),
			);
			$returns   = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action status.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further information about the request.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The customer has been updated successfully.',
				'data'    =>
				array(
					'email'          => 'demo5@example.com',
					'first_name'     => 'John',
					'last_name'      => 'Doe',
					'title'          => 'Engineer',
					'note'           => 'test note',
					'country'        => 'United States',
					'address_line_1' => 'Demo address line 1',
					'address_line_2' => 'Demo address line 2',
					'city'           => 'Washington',
					'state'          => 'Utah',
					'zip'            => '10004',
					'person_type'    => 'customer',
					'hash'           => 'a1af0a23183fe8ec002a106aebd82030',
					'updated_at'     => '2022-10-28 12:40:41',
					'created_at'     => '2022-10-28 12:40:41',
					'id'             => 6,
					'full_name'      => 'John Doe',
					'photo'          => 'https://www.gravatar.com/avatar/975f4727d4f48a8ce7d1f933b756db16?s=128',
				),
			);

			return array(
				'action'            => 'flsup_update_customer', // required
				'name'              => __( 'Update customer', 'wp-webhooks' ),
				'sentence'          => __( 'update a customer', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Update a customer within Fluent Support.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'fluent-support',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
				'data'    => array(),
			);

			if ( ! class_exists( '\FluentSupport\App\Models\Customer' ) ) {
				return $return_args['msg'] = __( 'The class \FluentSupport\App\Models\Customer does not exist.', 'wp-webhooks' );
			}

			$customer                   = array();
			$customer['email']          = sanitize_email( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'email' ) );
			$customer['first_name']     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'first_name' );
			$customer['last_name']      = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'last_name' );
			$customer['title']          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'job_title' );
			$customer['note']           = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'note' );
			$customer['country']        = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'country' );
			$customer['address_line_1'] = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'address_line_1' );
			$customer['address_line_2'] = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'address_line_2' );
			$customer['city']           = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'city' );
			$customer['state']          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'state' );
			$customer['status']         = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'status' );
			$customer['zip']            = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'zip' );
			$avatar          			= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'avatar' );
			$customer['remote_uid']     = 1;

			if ( empty( $customer['email'] ) ) {
				$return_args['msg'] = __( 'Please set the email argument to a valid email address.', 'wp-webhooks' );
				return $return_args;
			}

			$exists       = \FluentSupport\App\Models\Customer::where( 'email', $customer['email'] )->first();
			$new_customer = '';

			if ( isset( $exists->id ) && $exists->id > 0 && $exists instanceof \FluentSupport\App\Models\Customer ) {
				$new_customer = \FluentSupport\App\Models\Customer::maybeCreateCustomer( $customer );
			} else {
				$return_args['msg'] = __( 'Customer with a given email address has not been found.', 'wp-webhooks' );
				return $return_args;
			}

			if ( ! empty( $new_customer ) && $new_customer instanceof \FluentSupport\App\Models\Customer ) {

				if( $avatar ){
					$new_customer->avatar = $avatar;
					$new_customer->save();
				}

				//Unset in case it was given as the value won't be available with a normal update
				if( isset( $new_customer->avatar ) ){
					unset( $new_customer->avatar );
				}

				$return_args['success'] = true;
				$return_args['msg']     = __( 'The customer has been updated successfully.', 'wp-webhooks' );
				$return_args['data']    = $new_customer;
			} else {
				$return_args['msg'] = __( 'An error occurred while updating a customer.', 'wp-webhooks' );
			}

			return $return_args;

		}
	}
endif;
