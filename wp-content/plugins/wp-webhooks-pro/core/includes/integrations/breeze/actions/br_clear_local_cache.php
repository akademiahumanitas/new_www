<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_breeze_Actions_br_clear_local_cache' ) ) :

	/**
	 * Load the br_clear_local_cache action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_breeze_Actions_br_clear_local_cache {

		public function get_details(){

				$parameter = array(
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired actions.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The local cache has been cleared.',
			);

			return array(
				'action'			=> 'br_clear_local_cache', //required
				'name'			   => __( 'Clear local cache', 'wp-webhooks' ),
				'sentence'			   => __( 'clear the local cache', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Clear the local cache within Breeze.', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'breeze',
				'premium'	   	=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
			);

			//delete minify
			if( class_exists( 'Breeze_MinificationCache' ) ){
				Breeze_MinificationCache::clear_minification();
			}
			
			//clear normal cache
			if( class_exists( 'Breeze_PurgeCache' ) ){
				Breeze_PurgeCache::breeze_cache_flush();
			}

			$return_args['success'] = true;
			$return_args['msg'] = __( "The local cache has been cleared.", 'action-br_clear_local_cache-success' );

			return $return_args;
	
		}

	}

endif; // End if class_exists check.