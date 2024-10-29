<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_cache_enabler_Actions_ce_clear_cache' ) ) :

	/**
	 * Load the ce_clear_cache action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_cache_enabler_Actions_ce_clear_cache {

		public function get_details(){

				$parameter = array(
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(String) Further information about the response.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The cache has been cleared.',
			);

			return array(
				'action'			=> 'ce_clear_cache', //required
				'name'			   => __( 'Clear cache', 'wp-webhooks' ),
				'sentence'			   => __( 'clear the full cache', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Clear the full cache within Cache Enabler.', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'cache-enabler',
				'premium'	   	=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
			);

			Cache_Enabler::clear_complete_cache();

			$return_args['success'] = true;
			$return_args['msg'] = __( "The cache has been cleared.", 'action-ce_clear_cache-success' );

			return $return_args;
	
		}

	}

endif; // End if class_exists check.