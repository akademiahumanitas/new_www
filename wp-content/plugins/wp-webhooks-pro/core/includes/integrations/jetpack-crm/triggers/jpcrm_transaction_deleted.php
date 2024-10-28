<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_jetpack_crm_Triggers_jpcrm_transaction_deleted' ) ) :

	/**
	 * Load the jpcrm_transaction_deleted trigger
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_jetpack_crm_Triggers_jpcrm_transaction_deleted {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'zbs_delete_transaction',
					'callback'  => array( $this, 'jpcrm_transaction_deleted_callback' ),
					'priority'  => 10,
					'arguments' => 1,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {

			$parameter = array(
				'success'        => array( 'short_description' => __( '(Bool) True if the transaction was successfully deleted.', 'wp-webhooks' ) ),
				'msg'            => array( 'short_description' => __( '(String) Further details about deleting a transaction.', 'wp-webhooks' ) ),
				'transaction_id' => array(
					'label'             => __( 'Transaction ID', 'wp-webhooks' ),
					'short_description' => __( '(Integer) The transaction id.', 'wp-webhooks' ),
				),
			);

			$settings = array(
				'load_default_settings' => true,
			);

			return array(
				'trigger'           => 'jpcrm_transaction_deleted',
				'name'              => __( 'Transaction deleted', 'wp-webhooks' ),
				'sentence'          => __( 'a transaction was deleted', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires as soon as a transaction was deleted within Jetpack CRM.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'jetpack-crm',
				'premium'           => true,
			);

		}

		/**
		 * Triggers when Jetpack CRM deletes a transaction
		 *
		 * @param $transaction_id Transaction's id
		 */
		public function jpcrm_transaction_deleted_callback( $transaction_id ) {

			$webhooks            = WPWHPRO()->webhook->get_hooks( 'trigger', 'jpcrm_transaction_deleted' );
			$response_data_array = array();

			$payload = array(
				'success'        => true,
				'msg'            => __( 'The transaction has been deleted.', 'wp-webhooks' ),
				'transaction_id' => $transaction_id,
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

			do_action( 'wpwhpro/webhooks/trigger_jpcrm_transaction_deleted', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array(
				'success'        => true,
				'msg'            => 'The transaction has been deleted.',
				'transaction_id' => 13,
			);

			return $data;
		}

	}

endif; // End if class_exists check.
