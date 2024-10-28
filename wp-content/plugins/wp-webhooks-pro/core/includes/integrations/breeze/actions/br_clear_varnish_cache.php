<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_breeze_Actions_br_clear_varnish_cache' ) ) :

	/**
	 * Load the br_clear_varnish_cache action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_breeze_Actions_br_clear_varnish_cache {

		public function get_details(){

				$parameter = array(
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'data'		=> array( 'short_description' => __( '(array) Further data about the fired actions.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The Varnish cache has been cleared.',
			);

			return array(
				'action'			=> 'br_clear_varnish_cache', //required
				'name'			   => __( 'Clear Varnish cache', 'wp-webhooks' ),
				'sentence'			   => __( 'clear the Varnish cache', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Clear the Varnish cache within Breeze.', 'wp-webhooks' ),
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

			//Clear the Varnish cache for Breeze
			do_action( 'breeze_clear_varnish' );

			$return_args['success'] = true;
			$return_args['msg'] = __( "The Varnish cache has been cleared.", 'action-br_clear_varnish_cache-success' );

			return $return_args;
	
		}

	}

endif; // End if class_exists check.