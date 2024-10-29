<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_rocket_Actions_wprocket_clear_cached_url' ) ) :

	/**
	 * Load the wprocket_clear_cached_url action
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_rocket_Actions_wprocket_clear_cached_url {

		public function get_details(){

				$parameter = array(
				'urls' => array( 
					'required'		=> true, 
					'label' => __( 'URLs', 'wp-webhooks' ),
					'short_description' => __( '(String) Clear specific URLs only. To add multiple ones, please comma-separate them.', 'wp-webhooks' ),
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => __( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'wp-webhooks' ) ),
				'msg'		=> array( 'short_description' => __( '(String) Further information about the action response.', 'wp-webhooks' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The cache for the given URLs has been cleared.',
			);

			return array(
				'action'			=> 'wprocket_clear_cached_url', //required
				'name'			   => __( 'Clear URL cache', 'wp-webhooks' ),
				'sentence'			   => __( 'clear the cache for one or multiple URLs', 'wp-webhooks' ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => __( 'Clear the cache for one or multiple URLs within WP Rocket.', 'wp-webhooks' ),
				'description'	   => array(),
				'integration'	   => 'wp-rocket',
				'premium'	   	=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
			);

			$urls = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'urls' );

			if( empty( $urls ) ){
				$return_args['msg'] = __( 'Please set the urls argument.', 'wp-webhooks' );
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
				$return_args['msg'] = __( 'We could not validate the given URLs.', 'wp-webhooks' );
				return $return_args;
			}

			$clean_home_url = false;
			$home_url = home_url( '/' );

			foreach( $validated_urls as $k_url => $url ){
	
				if ( $home_url === $url ) {
					$clean_home_url = true;
					unset( $validated_urls[ $k_url ] ); //unset home url
					break;
				}
				
			}

			if( $clean_home_url ){
				rocket_clean_home();
			}

			rocket_clean_files( $validated_urls );
			
			$return_args['success'] = true;
			$return_args['msg']     = __( 'The cache for the given URLs has been cleared.', 'wp-webhooks' );

			return $return_args;
	
		}

	}

endif; // End if class_exists check.