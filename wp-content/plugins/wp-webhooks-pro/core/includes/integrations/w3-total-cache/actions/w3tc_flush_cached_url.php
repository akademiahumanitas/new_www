<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_w3_total_cache_Actions_w3tc_flush_cached_url' ) ) :

	/**
	 * Load the w3tc_flush_cached_url action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_w3_total_cache_Actions_w3tc_flush_cached_url {

		public function get_details() {


			$parameter = array(
				'urls' => array( 
					'required'		=> true, 
					'label' => __( 'URLs', 'wp-webhooks' ),
					'short_description' => __( '(String) Clear specific URLs only. To add multiple ones, please comma-separate them.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'    => array( 'short_description' => __( '(String) An informative message about the action.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The cache for the given URLs has been flushed.',
			);

			return array(
				'action'            => 'w3tc_flush_cached_url',
				'name'              => __( 'Flush cached URL', 'wp-webhooks' ),
				'sentence'          => __( 'flush the cache for one or multiple URLs', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Flush the cache for one or multiple URLs within W3 Total Cache.', 'wp-webhooks' ),
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

			$urls = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'urls' );

			if( empty( $urls ) ){
				$return_args['msg']     = __( 'Please set the urls argument.', 'wp-webhooks' );
				return $return_args;
			}

			$validated_urls = array();
			
			if( WPWHPRO()->helpers->is_json( $urls ) ){
				$urls_data = json_decode( $urls, true );
			} else {
				$urls_data = explode( ',', $urls );
			}

			if( ! empty( $urls_data ) && is_array( $urls_data ) ){
				foreach( $urls_data as $url ){
					$validated_urls[] = $url;
				}
			}

			if( empty( $validated_urls ) ){
				$return_args['msg']     = __( 'We could not validate the given URLs.', 'wp-webhooks' );
				return $return_args;
			}

			if( ! empty( $validated_urls ) ){
				foreach( $validated_urls as $url ){
					w3tc_flush_url( $url );
				}
			}
			$return_args['msg']     = __( 'The cache for the given URLs has been flushed.', 'wp-webhooks' );
			
			$return_args['success'] = true;

			return $return_args;

		}

	}

endif; // End if class_exists check.
