<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_litespeed_cache_Actions_lsc_purge_full_cache' ) ) :

	/**
	 * Load the lsc_purge_full_cache action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_litespeed_cache_Actions_lsc_purge_full_cache {

		public function get_details() {


			$parameter = array();

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) An informative message about the action.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The full cache has been purged.',
			);

			return array(
				'action'            => 'lsc_purge_full_cache', // required
				'name'              => __( 'Purge full cache', 'wp-webhooks' ),
				'sentence'          => __( 'purge the full cache', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Purge the full cache within LiteSpeed Cache.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'litespeed-cache',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$page_title = WPWHPRO()->settings->get_page_title();
			$return_args = array(
				'success' => false,
				'msg'     => '',
			);
			$reason = $page_title . ': lsc_purge_full_cache';

			do_action( 'litespeed_purge_all', $reason );

			$return_args['success'] = true;
			$return_args['msg']     = __( 'The full cache has been purged.', 'wp-webhooks' );

			return $return_args;

		}

	}

endif; // End if class_exists check.
