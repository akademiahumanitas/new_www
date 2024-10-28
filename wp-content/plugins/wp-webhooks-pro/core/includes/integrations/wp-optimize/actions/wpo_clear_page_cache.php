<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_optimize_Actions_wpo_clear_page_cache' ) ) :

	/**
	 * Load the wpo_clear_page_cache action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_optimize_Actions_wpo_clear_page_cache {

		public function get_details() {


			$parameter = array();

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) An informative message about the action.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'Page cache purged successfully',
			);

			return array(
				'action'            => 'wpo_clear_page_cache', // required
				'name'              => __( 'Clear page cache', 'wp-webhooks' ),
				'sentence'          => __( 'clear the page cache', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Clear the page cache within WP-Optimize.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'wp-optimize',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
			);

			if ( class_exists( 'WP_Optimize_Cache_Commands' ) ) {
				$wp_optimize = new WP_Optimize_Cache_Commands();
				$data = $wp_optimize->purge_page_cache();
			} else {
				$return_args['msg'] = __( 'We could not locate the logic to clear the page cache for WP-Optimize.', 'wp-webhooks' );
				return $return_args;
			}

			if( is_array( $data ) && isset( $data['success'] ) && $data['success'] ){
				$return_args['success']            = $data['success'];
				$return_args['msg']                = $data['message'];
			} else {
				if( is_array( $data ) && isset( $data['message'] ) ){
					$return_args['msg'] = $data['message'];
				} else {
					$return_args['msg'] = __( 'An error occured while clearing the page cache.', 'wp-webhooks' );
				}
			}
			
			return $return_args;

		}

	}

endif; // End if class_exists check.
