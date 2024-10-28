<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_jetpack_crm_Triggers_jpcrm_invoice_created' ) ) :

	/**
	 * Load the jpcrm_invoice_created trigger
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_jetpack_crm_Triggers_jpcrm_invoice_created {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'zbs_new_invoice',
					'callback'  => array( $this, 'jpcrm_invoice_created_callback' ),
					'priority'  => 10,
					'arguments' => 1,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {

			$parameter = array(
				'success'    => array( 'short_description' => __( '(Bool) True if the invoice was successfully created.', 'wp-webhooks' ) ),
				'msg'        => array( 'short_description' => __( '(String) Further details about the creation of an invoice.', 'wp-webhooks' ) ),
				'invoice_id' => array(
					'label'             => __( 'Invoice ID', 'wp-webhooks' ),
					'short_description' => __( '(Integer) The invoice id.', 'wp-webhooks' ),
				),
				'invoice'    => array(
					'label'             => __( 'Invoice data', 'wp-webhooks' ),
					'short_description' => __( '(Array) The further details about the created invoice.', 'wp-webhooks' ),
				),
			);

			$settings = array(
				'load_default_settings' => true,
			);

			return array(
				'trigger'           => 'jpcrm_invoice_created',
				'name'              => __( 'Invoice created', 'wp-webhooks' ),
				'sentence'          => __( 'an invoice was created', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires as soon as an invoice was created within Jetpack CRM.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'jetpack-crm',
				'premium'           => true,
			);

		}

		/**
		 * Triggers when Jetpack CRM creates an invoice
		 *
		 * @param $invoice_id Invoice's id
		 */
		public function jpcrm_invoice_created_callback( $invoice_id ) {

			global $zbs;

			$invoice = $zbs->DAL->invoices->getInvoice( $invoice_id );

			$webhooks            = WPWHPRO()->webhook->get_hooks( 'trigger', 'jpcrm_invoice_created' );
			$response_data_array = array();

			$payload = array(
				'success'    => true,
				'msg'        => __( 'The invoice has been created.', 'wp-webhooks' ),
				'invoice_id' => $invoice_id,
				'invoice'    => $invoice,
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

			do_action( 'wpwhpro/webhooks/trigger_jpcrm_invoice_created', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array(
				'success'    => true,
				'msg'        => 'The invoice has been created.',
				'invoice_id' => 2,
				'invoice'    => array (
					'id' => '2',
					'owner' => '1',
					'id_override' => '5',
					'parent' => 0,
					'status' => 'Draft',
					'hash' => 'mAvo40Xqr8LSO5Gw6V2',
					'pdf_template' => '',
					'portal_template' => '',
					'email_template' => '',
					'invoice_frequency' => 0,
					'currency' => '',
					'pay_via' => 0,
					'logo_url' => '',
					'address_to_objtype' => 0,
					'addressed_from' => '',
					'addressed_to' => '',
					'allow_partial' => true,
					'allow_tip' => true,
					'send_attachments' => true,
					'hours_or_quantity' => '0',
					'date' => 1665878400,
					'date_date' => 'October 16, 2022',
					'due_date' => 1665878400,
					'due_date_date' => 'October 16, 2022',
					'paid_date' => 0,
					'paid_date_date' => false,
					'hash_viewed' => 0,
					'hash_viewed_date' => false,
					'hash_viewed_count' => 0,
					'portal_viewed' => 0,
					'portal_viewed_date' => false,
					'portal_viewed_count' => 0,
					'net' => '2300.00',
					'discount' => '0.00',
					'discount_type' => '0',
					'shipping' => '0.00',
					'shipping_taxes' => '',
					'shipping_tax' => '0.00',
					'taxes' => '',
					'tax' => '0.00',
					'total' => '2300.00',
					'created' => 1665942148,
					'created_date' => '2022-10-16 17:42:28',
					'lastupdated' => 1665942148,
					'lastupdated_date' => '2022-10-16 17:42:28',
					'lineitems' => 
					array (
					  0 => 
					  array (
						'id' => '3',
						'owner' => '1',
						'order' => 0,
						'title' => 'Demo Title',
						'desc' => 'Item description',
						'quantity' => '23',
						'price' => '100.00',
						'currency' => '',
						'net' => '2300.00',
						'discount' => '0.00',
						'fee' => '0.00',
						'shipping' => '0.00',
						'shipping_taxes' => '',
						'shipping_tax' => '0.00',
						'taxes' => '',
						'tax' => '0.00',
						'total' => '2300.00',
						'created' => 1665942148,
						'created_date' => '2022-10-16 17:42:28',
						'lastupdated' => 1665942148,
						'lastupdated_date' => '2022-10-16 17:42:28',
					  ),
					),
				  )
			);

			return $data;
		}

	}

endif; // End if class_exists check.
