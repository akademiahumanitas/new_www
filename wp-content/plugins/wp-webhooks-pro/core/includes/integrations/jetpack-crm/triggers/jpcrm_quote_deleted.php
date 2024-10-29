<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_jetpack_crm_Triggers_jpcrm_quote_deleted' ) ) :

	/**
	 * Load the jpcrm_quote_deleted trigger
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_jetpack_crm_Triggers_jpcrm_quote_deleted {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'zbs_delete_quote',
					'callback'  => array( $this, 'jpcrm_quote_deleted_callback' ),
					'priority'  => 10,
					'arguments' => 1,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {


			$parameter = array(
				'success' => array( 'short_description' => __( '(Bool) True if the quote was successfully deleted.', 'wp-webhooks' ) ),
                'msg' => array( 'short_description' => __( '(String) Further details about the deletion of a quote.', 'wp-webhooks' ) ),
				'quote_id'   => array(
					'label'             => __( 'Quote ID', 'wp-webhooks' ),
					'short_description' => __( '(Integer) The quote id.', 'wp-webhooks' ),
				),
			);

			$settings = array(
				'load_default_settings' => true,
			);

			return array(
				'trigger'           => 'jpcrm_quote_deleted',
				'name'              => __( 'Quote deleted', 'wp-webhooks' ),
				'sentence'          => __( 'a quote was deleted', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires as soon as a quote was deleted within Jetpack CRM.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'jetpack-crm',
				'premium'           => true,
			);

		}

		/**
		 * Triggers when Jetpack CRM deletes a quote
		 *
		 * @param $quote_id Quote's id
		 */
		public function jpcrm_quote_deleted_callback( $quote_id ) {

			$webhooks            = WPWHPRO()->webhook->get_hooks( 'trigger', 'jpcrm_quote_deleted' );
			$response_data_array = array();
			
			$payload = array(
				'success' => true,
				'msg'     => __( 'The quote has been deleted.', 'wp-webhooks' ),
				'quote_id' => $quote_id
			);

			foreach ( $webhooks as $webhook ) {

				$webhook_url_name = ( is_array( $webhook ) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
				$is_valid         = true;

				if ( $is_valid ) {
					if ( $webhook_url_name !== null ) {
						$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					} else {
						$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					}
				}
			}

			do_action( 'wpwhpro/webhooks/trigger_jpcrm_quote_deleted', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array(
				'success' => true,
				'msg'     => 'The quote has been deleted.',
				'quote_id' => 3,
		
			);

			return $data;
		}

	}

endif; // End if class_exists check.
