<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_jetpack_crm_Triggers_jpcrm_company_deleted' ) ) :

	/**
	 * Load the jpcrm_company_deleted trigger
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_jetpack_crm_Triggers_jpcrm_company_deleted {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'zbs_delete_company',
					'callback'  => array( $this, 'jpcrm_company_deleted_callback' ),
					'priority'  => 10,
					'arguments' => 1,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {


			$parameter = array(
				'success' => array( 'short_description' => __( '(Bool) True if the company was successfully deleted.', 'wp-webhooks' ) ),
                'msg' => array( 'short_description' => __( '(String) Further details about the deletion of a company.', 'wp-webhooks' ) ),
				'company_id'   => array(
					'label'             => __( 'Company ID', 'wp-webhooks' ),
					'short_description' => __( '(Integer) The company id.', 'wp-webhooks' ),
				),
			);

			$settings = array(
				'load_default_settings' => true,
			);

			return array(
				'trigger'           => 'jpcrm_company_deleted',
				'name'              => __( 'Company deleted', 'wp-webhooks' ),
				'sentence'          => __( 'a company was deleted', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires as soon as a company was deleted within Jetpack CRM.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'jetpack-crm',
				'premium'           => true,
			);

		}

		/**
		 * Triggers when Jetpack CRM deletes a company
		 *
		 * @param $company_id Company's id
		 */
		public function jpcrm_company_deleted_callback( $company_id ) {
			$webhooks            = WPWHPRO()->webhook->get_hooks( 'trigger', 'jpcrm_company_deleted' );
			$response_data_array = array();
			
			$payload = array(
				'success' => true,
				'msg'     => __( 'The company has been deleted.', 'wp-webhooks' ),
				'company_id' => $company_id,
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

			do_action( 'wpwhpro/webhooks/trigger_jpcrm_company_deleted', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array(
				'success' => true,
				'msg'     => 'The company has been deleted.',
				'company_id' => 22,

			);

			return $data;
		}

	}

endif; // End if class_exists check.
