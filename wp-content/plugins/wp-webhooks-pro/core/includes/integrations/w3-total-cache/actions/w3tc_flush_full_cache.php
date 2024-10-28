<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_w3_total_cache_Actions_w3tc_flush_full_cache' ) ) :

	/**
	 * Load the w3tc_flush_full_cache action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_w3_total_cache_Actions_w3tc_flush_full_cache {

		public function get_details() {


			$parameter = array();

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'    => array( 'short_description' => __( '(String) An informative message about the action.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The full cache has been flushed.',
			);

			return array(
				'action'            => 'w3tc_flush_full_cache',
				'name'              => __( 'Flush full cache', 'wp-webhooks' ),
				'sentence'          => __( 'flush the full cache', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Flush the full cache within W3 Total Cache.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'w3-total-cache',
				'premium'           => true,
			);

		}

		/**
		 * Execute function
		 *
		 * @param array $return_data Returning data.
		 * @param  array $response_body Response body.
		 * @return array $return_args
		 */
		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
			);

			w3tc_flush_all();
			
			$return_args['success'] = true;
			$return_args['msg']     = __( 'The full cache has been flushed.', 'wp-webhooks' );

			return $return_args;

		}

	}

endif; // End if class_exists check.
