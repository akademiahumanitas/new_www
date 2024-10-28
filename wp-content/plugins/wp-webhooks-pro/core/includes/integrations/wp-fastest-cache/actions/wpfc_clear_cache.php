<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_fastest_cache_Actions_wpfc_clear_cache' ) ) :

	/**
	 * Load the wpfc_clear_cache action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_fastest_cache_Actions_wpfc_clear_cache {

		public function get_details() {


			$parameter = array();

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'    => array( 'short_description' => __( '(String) An informative message about the action.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The cache has been cleared successfully.',
			);

			return array(
				'action'            => 'wpfc_clear_cache', // required
				'name'              => __( 'Clear cache', 'wp-webhooks' ),
				'sentence'          => __( 'clear the cache of a single site', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Clear the cache of a single site within WP Fastest Cache.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'wp-fastest-cache',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
			);

			do_action( 'wpfc_clear_all_cache', true ); //true to clear minified cache

			$return_args['success'] = true;
			$return_args['msg']     = __( 'The cache has been cleared successfully.', 'wp-webhooks' );

			return $return_args;

		}

	}

endif; // End if class_exists check.
