<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_autoptimize_Actions_ao_clear_cache' ) ) :

	/**
	 * Load the ao_clear_cache action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_autoptimize_Actions_ao_clear_cache {

		public function get_details() {

				$parameter = array();

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'    => array( 'short_description' => __( '(String) Further information about the action.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The cache has been cleared.',
			);

			return array(
				'action'            => 'ao_clear_cache', //required
				'name'              => __( 'Clear cache', 'wp-webhooks' ),
				'sentence'          => __( 'clear the full cache', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Clear the full cache within Autoptimize.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'autoptimize',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
			);

			$cleared = autoptimizeCache::clearall();

			if( $cleared ){
				autoptimizeOptionWrapper::update_option( 'autoptimize_cache_clean', 0 );

				$return_args['success'] = true;
				$return_args['msg']     = __( "The cache has been cleared.", 'action-ao_clear_cache-success' );
			} else {
				$return_args['msg']     = __( "An error occured within Autoptimize while clearing the cache.", 'action-ao_clear_cache-success' );
			}

			return $return_args;

		}

	}

endif; // End if class_exists check.