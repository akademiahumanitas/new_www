<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_shipstation_Triggers_wcss_order_shipped' ) ) :

	/**
	 * Load the wcss_order_shipped trigger
	 *
	 * @since 6.1.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_woocommerce_shipstation_Triggers_wcss_order_shipped {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'woocommerce_shipstation_shipnotify',
					'callback'  => array( $this, 'wcss_order_shipped_callback' ),
					'priority'  => 99,
					'arguments' => 2,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {

			$parameter = array(
				'success' => array( 'short_description' => __( '(Bool) True if the order was successfully shipped.', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further information about the action.', 'wp-webhooks' ) ),
				'data'    => array( 'short_description' => __( '(Array) Further details about the action.', 'wp-webhooks' ) ),
			);

			$settings = array(
				'load_default_settings' => true,
			);

			return array(
				'trigger'           => 'wcss_order_shipped',
				'name'              => __( 'Order shipped', 'wp-webhooks' ),
				'sentence'          => __( 'an order was shipped', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires as soon as order was shipped within WooCommerce ShipStation.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'woocommerce-shipstation',
				'premium'           => true,
			);

		}

		/**
		 * Triggers when WooCommerce Shipstation order was shipped 
		 *
		 * @param $order Order data
		 * @param $args Shipping data
		 */
		public function wcss_order_shipped_callback( $order, $args ) {

			$webhooks            = WPWHPRO()->webhook->get_hooks( 'trigger', 'wcss_order_shipped' );
			$response_data_array = array();

			if ( empty( $args ) ) {
				return;
			}
			$xml              = new SimpleXMLElement( $args['xml'] );
			$shipstation_data = array(
				'carrier'   => $args['carrier'],
				'ship_date' => $args['ship_date'],
				'details'   => $xml,
			);

			$payload = array(
				'success' => true,
				'msg'     => __( 'The order was shipped succesfully.', 'wp-webhooks' ),
				'data'    => $shipstation_data,
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

			do_action( 'wpwhpro/webhooks/trigger_wcss_order_shipped', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array(
				'success' => true,
				'msg'     => 'The order was shipped succesfully.',
				'data'    =>
				array(
					'carrier'   => 'APC',
					'ship_date' => 1670976000,
					'details'       =>
					array(
						'OrderNumber'     => '1157',
						'OrderID'         => '1157',
						'CustomerCode'    => 'demo@example.com',
						'CustomerNotes'   =>
						array(),
						'InternalNotes'   =>
						array(),
						'NotesToCustomer' =>
						array(),
						'NotifyCustomer'  => 'true',
						'LabelCreateDate' => '12/14/2022 03:00',
						'ShipDate'        => '12/14/2022',
						'Carrier'         => 'APC',
						'Service'         =>
						array(),
						'TrackingNumber'  => '66',
						'ShippingCost'    => '0',
						'CustomField1'    =>
						array(),
						'CustomField2'    =>
						array(),
						'CustomField3'    =>
						array(),
						'Recipient'       =>
						array(
							'Name'       => 'John Doe',
							'Company'    => 'Demo Company',
							'Address1'   => 'White Chappel st 3/37',
							'Address2'   =>
							array(),
							'City'       => 'London',
							'State'      => 'Bakers',
							'PostalCode' => 'NW2',
							'Country'    => 'UK',
						),
						'Items'           =>
						array(
							'Item' =>
							array(
								'Name'       => 'Demo Product',
								'SKU'        =>
								array(),
								'LineItemID' => '15',
								'Quantity'   => '2',
							),
						),
					),
				),
			);

			return $data;
		}

	}

endif; // End if class_exists check.
