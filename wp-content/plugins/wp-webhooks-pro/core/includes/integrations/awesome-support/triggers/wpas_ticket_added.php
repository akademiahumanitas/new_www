<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_awesome_support_Triggers_wpas_ticket_added' ) ) :

	/**
	 * Load the wpas_ticket_added trigger
	 *
	 * @since 6.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_awesome_support_Triggers_wpas_ticket_added {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'wpas_open_ticket_after',
					'callback'  => array( $this, 'wpas_ticket_added_callback' ),
					'priority'  => 10,
					'arguments' => 2,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {

			$parameter = array(
				'ticket_id' => array( 'short_description' => __( '(Integer) The ID of the ticket.', 'wp-webhooks' ) ),
				'ticket' => array( 'short_description' => __( '(Array) The ticket details.', 'wp-webhooks' ) ),
				'ticket_meta' => array( 'short_description' => __( '(Array) Additional details about the ticket.', 'wp-webhooks' ) ),
			);

			$settings = array(
				'load_default_settings' => true,
			);

			$description = array(
				'tipps' => array(
					__( 'Please note that creating a ticket from wihtin the backend does not fire this trigger.', 'wp-webhooks' ),
				),
			);

			return array(
				'trigger'           => 'wpas_ticket_added',
				'name'              => __( 'Ticket added', 'wp-webhooks' ),
				'sentence'          => __( 'a ticket was added', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires as soon as a ticket was added within Awesome Support.', 'wp-webhooks' ),
				'description'       => $description,
				'integration'       => 'awesome-support',
				'premium'           => true,
			);

		}

		/**
		 * Triggers once a ticket was opened within Awesome Support
		 *
		 * @param integer $ticket_id The ticket id
		 * @param array   $data The ticket data
		 */
		public function wpas_ticket_added_callback( $ticket_id, $data ) {

			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wpas_ticket_added' );

			$payload = array(
				'ticket_id'   => $ticket_id,
				'ticket'      => get_post( $ticket_id ),
				'ticket_meta' => get_post_meta( $ticket_id ),
			);

			$response_data_array = array();

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

			do_action( 'wpwhpro/webhooks/trigger_wpas_ticket_added', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array (
				'ticket_id' => 9505,
				'ticket' => 
				array (
				  'ID' => 9505,
				  'post_author' => '203',
				  'post_date' => '2022-11-16 12:39:42',
				  'post_date_gmt' => '2022-11-16 12:39:42',
				  'post_content' => 'This is a demo ticket.',
				  'post_title' => 'Demo Ticket',
				  'post_excerpt' => '',
				  'post_status' => 'queued',
				  'comment_status' => 'closed',
				  'ping_status' => 'closed',
				  'post_password' => '',
				  'post_name' => 'demo-ticket',
				  'to_ping' => '',
				  'pinged' => '',
				  'post_modified' => '2022-11-16 12:39:42',
				  'post_modified_gmt' => '2022-11-16 12:39:42',
				  'post_content_filtered' => '',
				  'post_parent' => 0,
				  'guid' => 'https://yourdomain.test/ticket/demo-ticket/',
				  'menu_order' => 0,
				  'post_type' => 'ticket',
				  'post_mime_type' => '',
				  'comment_count' => '0',
				  'filter' => 'raw',
				),
				'ticket_meta' => 
				array (
				  '_wpas_status' => 
				  array (
					0 => 'open',
				  ),
				  '_wpas_last_reply_date' => 
				  array (
					0 => NULL,
				  ),
				  '_wpas_last_reply_date_gmt' => 
				  array (
					0 => NULL,
				  ),
				  '_wpas_is_waiting_client_reply' => 
				  array (
					0 => '1',
				  ),
				  '_wpas_assignee' => 
				  array (
					0 => '1',
				  ),
				),
			);

			return $data;
		}

	}

endif; // End if class_exists check.
