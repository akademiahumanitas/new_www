<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_comet_cache_Actions_cc_clear_cache' ) ) :

	/**
	 * Load the cc_clear_cache action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_comet_cache_Actions_cc_clear_cache {

		public function get_details(){

				$parameter = array(
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired actions.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The cache has been cleared.',
				'data' => array(
					'counter' => 2
				)
			);

			return array(
				'action'			=> 'cc_clear_cache', //required
				'name'			   => __( 'Clear cache', 'wp-webhooks' ),
				'sentence'			   => __( 'clear the full cache', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Clear the full cache within Comet Cache.', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'comet-cache',
				'premium'	   	=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'counter' => 0
				),
			);
			
			$counter = 0;

			if( class_exists( 'WebSharks\CometCache\Classes\Plugin' ) ){
				$comet_cache = new WebSharks\CometCache\Classes\Plugin();
				$comet_cache->setup();
				$counter = $comet_cache->clearCache(true);
			} else {
				$return_args['msg'] = __( "We could not locate the logic to clear Comet Cache.", 'action-cc_clear_cache-success' );
				return $return_args;
			}

			if( $counter > 0 ){
				$return_args['msg'] = __( "The cache has been cleared.", 'action-cc_clear_cache-success' );
			} else {
				$return_args['msg'] = __( "The cache was already empty beforehand.", 'action-cc_clear_cache-success' );
			}

			$return_args['success'] = true;
			$return_args['data']['counter'] = $counter;
			return $return_args;
	
		}

	}

endif; // End if class_exists check.