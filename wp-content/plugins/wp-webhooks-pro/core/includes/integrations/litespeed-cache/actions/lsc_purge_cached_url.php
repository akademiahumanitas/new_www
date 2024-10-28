<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_litespeed_cache_Actions_lsc_purge_cached_url' ) ) :

	/**
	 * Load the lsc_purge_cached_url action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_litespeed_cache_Actions_lsc_purge_cached_url {

		public function get_details() {


			$parameter = array(
				'urls' => array(
					'label'			=> __( 'URLs', 'wp-webhooks' ),
					'required'		=> true,
					'short_description' => __( '(String) Clear specific URLs. To clear multiple ones, please comma-separate them.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success' => array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'    => array( 'short_description' => __( '(String) An informative message about the action.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(array) Further details about the fired actions.', 'wp-webhooks' ) ),
			);

			$returns_code = array(
				'success' => true,
				'msg'     => 'The URL(s) have been purged.',
				'data'	  => array(
					'urls' => array(
						'https://demodomain.test',
					)
				)
			);

			return array(
				'action'            => 'lsc_purge_cached_url', // required
				'name'              => __( 'Purge cached URL', 'wp-webhooks' ),
				'sentence'          => __( 'purge one or multiple cached URLs', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'returns'           => $returns,
				'returns_code'      => $returns_code,
				'short_description' => __( 'Purge one or multiple cached URLs within LiteSpeed Cache.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'litespeed-cache',
				'premium'           => true,
			);

		}

		public function execute( $return_data, $response_body ) {

			$return_args = array(
				'success' => false,
				'msg'     => '',
				'data'	  => array(
					'urls' => array()
				)
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
					do_action( 'litespeed_purge_url', $url );
				}
			}

			$return_args['success'] = true;
			$return_args['msg']     = __( 'The URL(s) have been purged.', 'wp-webhooks' );
			$return_args['data']['urls'] = $validated_urls;

			return $return_args;

		}

	}

endif; // End if class_exists check.
