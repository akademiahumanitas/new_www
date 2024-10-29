<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_hummingbird_Triggers_hb_page_cache_cleared' ) ) :

	/**
	 * Load the hb_page_cache_cleared trigger
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_hummingbird_Triggers_hb_page_cache_cleared {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'wphb_clear_page_cache',
					'callback'  => array( $this, 'hb_page_cache_cleared_callback' ),
					'priority'  => 20,
					'arguments' => 1,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {


			$parameter = array(
                'success' => array( 'short_description' => __( '(Bool) True if the cache was successfully cleared.', 'wp-webhooks' ) ),
                'msg' => array( 'short_description' => __( '(String) Further details about the clearing of the cache.', 'wp-webhooks' ) ),
                'post_id' => array( 'short_description' => __( '(Int) This field is set in case the cache has been cleared for a specific post ID.', 'wp-webhooks' ) ),
            );

			$settings = array(
				'load_default_settings' => true,
				'data' => array(
					'wpwhpro_hb_page_cache_cleared_trigger_on_single_post' => array(
						'id'		  => 'wpwhpro_hb_page_cache_cleared_trigger_on_single_post',
						'type'		=> 'select',
						'multiple'	=> false,
						'choices'	  => array(
							'yes' => array( 'label' => __( 'Yes', 'wp-webhooks' ) ),
							'no' => array( 'label' => __( 'No', 'wp-webhooks' ) ),
						),
						'label'	   => __( 'Trigger on single posts', 'wp-webhooks' ),
						'placeholder' => '',
						'required'	=> false,
						'description' => __( 'Select if you also want to fire this trigger if a single post cache is cleared. If nothing is selected, all are triggered.', 'wp-webhooks' )
					),
				)
			);

			return array(
				'trigger'           => 'hb_page_cache_cleared',
				'name'              => __( 'Page cache cleared', 'wp-webhooks' ),
				'sentence'          => __( 'the page cache was cleared', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires as soon as the page cache was cleared within Hummingbird.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'hummingbird',
				'premium'           => true,
			);

		}

		public function hb_page_cache_cleared_callback( $post_id = false ) {

			$webhooks            = WPWHPRO()->webhook->get_hooks( 'trigger', 'hb_page_cache_cleared' );
			$response_data_array = array();

			$payload = array(
				'success'  => true,
				'msg'      => __( 'The page cache has been cleared.', 'wp-webhooks' ),
				'post_id'  => $post_id
			);

			foreach ( $webhooks as $webhook ) {

				$webhook_url_name = ( is_array( $webhook ) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
				$is_valid         = true;

				if( isset( $webhook['settings'] ) ){
					if( isset( $webhook['settings']['wpwhpro_hb_page_cache_cleared_trigger_on_single_post'] ) && ! empty( $webhook['settings']['wpwhpro_hb_page_cache_cleared_trigger_on_single_post'] ) ){
						if( $webhook['settings']['wpwhpro_hb_page_cache_cleared_trigger_on_single_post'] === 'no' && ! empty( $post_id ) ){
							$is_valid = false;
						}
					}
				}

				if ( $is_valid ) {
					if ( $webhook_url_name !== null ) {
						$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					} else {
						$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					}
				}
			}

			do_action( 'wpwhpro/webhooks/trigger_hb_page_cache_cleared', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array(
				'success' => true,
				'msg'     => 'The page cache has been cleared.',
				'post_id' => false
			);

			return $data;
		}

	}

endif; // End if class_exists check.
