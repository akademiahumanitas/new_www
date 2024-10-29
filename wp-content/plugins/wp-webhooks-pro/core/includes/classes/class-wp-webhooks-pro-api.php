<?php

/**
 * WP_Webhooks_Pro_API Class
 *
 * This class contains all of the available api functions
 *
 * @since 1.0.0
 */

/**
 * The api class of the plugin.
 *
 * @since 1.0.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_API {

	/**
	 * This is the main page for handling api requests
	 * @var string
	 */
	protected $api_url = 'https://wp-webhooks.com';

	/**
	 * ################################
	 * ###
	 * ##### --- News feed ---
	 * ###
	 * ################################
	 */

	/**
	 * Get the news feed based on a specified post
	 *
	 * @param $news_id
	 * @return mixed bool if response is empty
	 */
	public function get_news_feed($news_id){

		if(!is_numeric($news_id))
			return false;

		$news_transient = get_transient( WPWHPRO()->settings->get_news_transient_key() );

		if( empty ( $news_transient ) || isset( $_GET['wpwhpro_renew_transient'] ) ){
			$news = WPWHPRO()->helpers->get_from_api( $this->api_url . '/wp-json/ironikus/v1/news/display/' . intval($news_id), 'body' );

			if(!empty($news)){
				$news             = ! empty( $news ) ? json_decode( $news, true ) : '' ;
				$news             = ( is_array( $news ) && $news['success'] == true ) ? $news['data'] : '' ;

				set_transient( WPWHPRO()->settings->get_news_transient_key(), $news, strtotime('1 day', 0) );

				return WPWHPRO()->helpers->validate_local_tags( $news );
			} else {
				return false;
			}

		} else {
			return WPWHPRO()->helpers->validate_local_tags( $news_transient );
		}

	}

	/**
	 * Get a list of all available extensions
	 *
	 * @param $news_id
	 * @return mixed bool if response is empty
	 */
	public function get_extension_list(){

		$extensions_transient = get_transient( WPWHPRO()->settings->get_extensions_transient_key() );

		if( empty ( $extensions_transient ) || isset( $_GET['wpwhpro_renew_transient'] ) ){
			$extensions = WPWHPRO()->helpers->get_from_api( $this->api_url . '/wp-json/ironikus/v1/extensions/list/', 'body' );

			if(!empty($extensions)){
				$extensions             = ! empty( $extensions ) ? json_decode( $extensions, true ) : '' ;
				$extensions             = ( is_array( $extensions ) && $extensions['success'] == true ) ? $extensions['data'] : '' ;

				set_transient( WPWHPRO()->settings->get_extensions_transient_key(), $extensions, strtotime('1 day', 0) );

				return $extensions;
			} else {
				return false;
			}

		} else {
			return $extensions_transient;
		}

	}

	/**
	 * Get a list of all available integrations
	 *
	 * @since 6.0
	 * @param $news_id
	 * @return mixed bool if response is empty
	 */
	public function get_integrations_list(){

		$integrations_transient = get_transient( WPWHPRO()->settings->get_integrations_transient_key() );

		if( empty ( $integrations_transient ) || isset( $_GET['wpwhpro_renew_transient'] ) ){
			$integrations = WPWHPRO()->helpers->get_from_api( $this->api_url . '/wp-json/ironikus/v1/integrations/list/?version=' . WPWHPRO_VERSION, 'body' );

			if(!empty($integrations)){
				$integrations             = ! empty( $integrations ) ? json_decode( $integrations, true ) : '' ;
				$integrations             = ( is_array( $integrations ) && $integrations['success'] == true ) ? $integrations['items'] : '' ;

				set_transient( WPWHPRO()->settings->get_integrations_transient_key(), $integrations, strtotime('1 day', 0) );

				return $integrations;
			} else {
				return false;
			}

		} else {
			return $integrations_transient;
		}

	}

	/**
     * Get a specific integration from our API
     *
     * @since 6.0
     * @param $integration_slug
     * @return array
     */
    public function get_integration_package( $integration_slug ){
        $integration_details = array(
            'success' => false,
            'msg' => __( "No specific response given.", 'wp-webhooks' ),
        );
        $integration_slug = sanitize_title( $integration_slug );

        if( ! empty( $integration_slug ) ){
            $license_data = WPWHPRO()->settings->get_license();
            $license_key = ( is_array( $license_data ) && isset( $license_data['key'] ) ) ? $license_data['key'] : false;
            $args = array(
				'method' => 'POST',
				'timeout' => 30,
				'headers' => array(
					'Cache-Control' => 'no-cache',
					'content-type' => 'application/x-www-form-urlencoded'
				),
				'body' => array(
					'wpwh_action' => 'get_integration',
					'wpwh_check_license_key' => $license_key,
					'wpwh_check_website_url' => home_url(),
					'wpwh_integration_slug' => $integration_slug,
					'wpwh_plugin_version' => WPWHPRO_VERSION,
				),
			);

            $integration_zip = wp_remote_post( $this->api_url . '/wp-json/ironikus/v1/integrations/install/?version=' . urlencode( WPWHPRO_VERSION ) . '&timestamp=' . time(), $args );
	
			if( ! is_wp_error( $integration_zip ) ){

				$response_body = wp_remote_retrieve_body( $integration_zip );
				$file_hash = wp_remote_retrieve_header( $integration_zip, 'x-wpwh-file-hash' );
				$response_code = wp_remote_retrieve_response_code( $integration_zip );

				if( $response_code > 299 ){
					$response_content = json_decode( $response_body, true );
					if( is_array( $response_content ) && isset( $response_content['msg'] ) ){
						$integration_details['msg'] = $response_content['msg'];
					}
				} else {
					if( ! empty( $response_body ) ){

						if( md5( $response_body ) === $file_hash ){
							$integration_details['success'] = true;
							$integration_details['msg'] = __( "The zip file stream has been returned.", 'wp-webhooks' );
							$integration_details['stream'] = $response_body;
						} else {
							$integration_details['msg'] = sprintf( __( "The downloaded integration '%s' was corrupted. Please try again.", 'wp-webhooks' ), $integration_slug );
							WPWHPRO()->helpers->log_issue( $integration_details['msg'] );
						}
						
					}
				}
				
			} else {
				$integration_details['msg'] = $integration_zip->get_error_message();
			}
			
        }

        return apply_filters( 'wpwhpro/api/get_integration_details', $integration_details, $integration_slug );
    }

}
