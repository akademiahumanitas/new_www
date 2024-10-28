<?php
use ACFWF\Models\Objects\Store_Credit_Entry;
use ACFWF\Helpers\Plugin_Constants;
if ( ! class_exists( 'WP_Webhooks_Integrations_advanced_coupons_Actions_ac_remove_credit_user' ) ) :
	/**
	 * Load the ac_remove_credit_user action
	 *
	 * @since 6.1.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_advanced_coupons_Actions_ac_remove_credit_user {


		public function get_details() {
			$parameter = array(
				'user'   => array(
					'required'          => true,
					'label'             => __( 'User', 'wp-webhooks' ),
					'type'              => 'select',
					'query'             => array(
						'filter' => 'users',
						'args'   => array(),
					),
					'short_description' => __(
						'(String) The user ID or email. You can provide an existing email address or an ID of a user.',
						'wp-webhooks'
					),
				),
				'amount' => array(
					'label'             => __( 'Amount', 'wp_webhooks' ),
					'short_description' => __( '(Floatval) The amount of the credit you want to remove.', 'wp_webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action status.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further information about the request.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'User credit balance has been adjusted successfully.',
				'data'    =>
				array(
					'user_id' => 22,
					'balance' => 20,
				),
			);

			return array(
				'action'            => 'ac_remove_credit_user', // required
				'name'              => __( 'Remove user credit', 'wp-webhooks' ),
				'sentence'          => __( 'remove credit from a user', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Remove credit from a user within Advanced Coupons.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'advanced-coupons',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
				'data'    => array(),
			);

			$user    = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$user_id = WPWHPRO()->helpers->serve_user_id( $user );
			$amount  = floatval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'amount' ) );

			if ( $amount <= 0 ) {
				return;
			}

				$params = array(
					'user_id'   => $user_id,
					'type'      => 'decrease',
					'amount'    => $amount,
					'object_id' => $user_id,
					'action'    => 'admin_decrease',
					'date'      => gmdate( 'Y-m-d H:i:s' ),
					'note'      => 'WP Webhooks',
				);

				$date_format = isset( $params['date_format'] ) ? $params['date_format'] : Plugin_Constants::DB_DATE_FORMAT;
				$store_credit_entry = new Store_Credit_Entry();

				foreach ( $params as $prop => $value ) {
					if ( $value && 'date' === $prop ) {
						$store_credit_entry->set_date_prop( $prop, $value, $date_format );
					} else {
						$store_credit_entry->set_prop( $prop, $value );
					}

					if ( 'action' === $prop && in_array(
						$value,
						array(
							'admin_increase',
							'admin_decrease',
						),
						true
					) ) {
						$store_credit_entry->set_prop( 'object_id', $user_id );
					}
				}

				$result = $store_credit_entry->save();

				if ( is_wp_error( $result ) ) {
					$return_args['msg'] = __( 'The store credit entry could not be created due to an error.', 'wp-webhooks' );
					return;
				}

				$return_args['msg']     = __( 'User credit balance has been adjusted successfully.', 'wp-webhooks' );
				$return_args['success'] = true;
				$return_args['data']    = array(
					'user_id'         => $user_id,
					'current_balance' => \ACFWF()->Store_Credits_Calculate->get_customer_balance( $user_id ),
				);

				return $return_args;

		}
	}
endif;
