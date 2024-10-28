<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_super_cache_Actions_wpsc_clear_full_cache' ) ) :

	/**
	 * Load the wpsc_clear_full_cache action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_super_cache_Actions_wpsc_clear_full_cache {

		public function get_details() {


			$parameter = array();

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'    => array( 'short_description' => __( '(String) An informative message about the action.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The full cache has been cleared.',
			);

			return array(
				'action'            => 'wpsc_clear_full_cache', // required
				'name'              => __( 'Clear full cache', 'wp-webhooks' ),
				'sentence'          => __( 'clear the full cache', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Clear the full cache within Litespeed Cache.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'wp-super-cache',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {
			global $file_prefix;

			$return_args = array(
				'success' => false,
				'msg'     => '',
			);

			wp_cache_clean_cache( $file_prefix, true );

			$return_args['success'] = true;
			$return_args['msg']     = __( 'The full cache has been cleared.', 'wp-webhooks' );

			return $return_args;

		}

	}

endif; // End if class_exists check.
