<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_awesome_support_Triggers_wpas_ticket_reply_added' ) ) :

	/**
	 * Load the wpas_ticket_reply_added trigger
	 *
	 * @since 6.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_awesome_support_Triggers_wpas_ticket_reply_added {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'wpas_add_reply_complete',
					'callback'  => array( $this, 'wpas_ticket_reply_added_callback' ),
					'priority'  => 10,
					'arguments' => 2,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {

			$parameter = array(
				'reply_id' => array( 'short_description' => __( '(Integer) The ID of the reply.', 'wp-webhooks' ) ),
				'ticket_id' => array( 'short_description' => __( '(Integer) The ID of the ticket.', 'wp-webhooks' ) ),
				'reply' => array( 'short_description' => __( '(Array) The reply details.', 'wp-webhooks' ) ),
				'reply_meta' => array( 'short_description' => __( '(Array) Additional details about the reply.', 'wp-webhooks' ) ),
				'ticket' => array( 'short_description' => __( '(Array) The ticket details.', 'wp-webhooks' ) ),
				'ticket_meta' => array( 'short_description' => __( '(Array) Additional details about the ticket.', 'wp-webhooks' ) ),
			);

			$settings = array(
				'load_default_settings' => true,
			);

			return array(
				'trigger'           => 'wpas_ticket_reply_added',
				'name'              => __( 'Ticket reply added', 'wp-webhooks' ),
				'sentence'          => __( 'a reply was added to a ticket', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires as soon as a reply was added to a ticket within Awesome Support.', 'wp-webhooks' ),
				'description'       => array(),
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
		public function wpas_ticket_reply_added_callback( $reply_id, $data ) {

			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wpas_ticket_reply_added' );
			$reply = get_post( $reply_id );
			$ticket_id = ( isset( $reply->post_parent ) ) ? $reply->post_parent : 0;

			$payload = array(
				'reply_id'    => $reply_id,
				'ticket_id'   => $ticket_id,
				'reply'       => $reply,
				'reply_meta'  => get_post_meta( $reply_id ),
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

			do_action( 'wpwhpro/webhooks/trigger_wpas_ticket_reply_added', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array (
				'reply_id' => 9514,
				'ticket_id' => 9510,
				'reply' => 
				array (
				  'ID' => 9514,
				  'post_author' => '1',
				  'post_date' => '2022-11-16 16:38:49',
				  'post_date_gmt' => '2022-11-16 16:38:49',
				  'post_content' => 'This is a third reply. :)',
				  'post_title' => 'Reply to ticket #9510',
				  'post_excerpt' => '',
				  'post_status' => 'read',
				  'comment_status' => 'closed',
				  'ping_status' => 'closed',
				  'post_password' => '',
				  'post_name' => 'reply-to-ticket-9510-3',
				  'to_ping' => '',
				  'pinged' => '',
				  'post_modified' => '2022-11-16 16:38:49',
				  'post_modified_gmt' => '2022-11-16 16:38:49',
				  'post_content_filtered' => '',
				  'post_parent' => 9510,
				  'guid' => 'https://yourdomain.test/?post_type=ticket_reply&p=9514',
				  'menu_order' => 0,
				  'post_type' => 'ticket_reply',
				  'post_mime_type' => '',
				  'comment_count' => '0',
				  'filter' => 'raw',
				),
				'reply_meta' => 
				array (
				),
				'ticket' => 
				array (
				  'ID' => 9510,
				  'post_author' => '1',
				  'post_date' => '2022-11-16 14:14:22',
				  'post_date_gmt' => '2022-11-16 14:14:22',
				  'post_content' => 'This is a demo message.',
				  'post_title' => 'This is a demo title from wpwh',
				  'post_excerpt' => '',
				  'post_status' => 'processing',
				  'comment_status' => 'closed',
				  'ping_status' => 'closed',
				  'post_password' => '',
				  'post_name' => 'this-is-a-demo-title-from-wpwh-3',
				  'to_ping' => '',
				  'pinged' => '',
				  'post_modified' => '2022-11-16 16:38:49',
				  'post_modified_gmt' => '2022-11-16 16:38:49',
				  'post_content_filtered' => '',
				  'post_parent' => 0,
				  'guid' => 'https://yourdomain.test/ticket/this-is-a-demo-title-from-wpwh-3/',
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
					0 => '2022-11-16 16:38:49',
				  ),
				  '_wpas_last_reply_date_gmt' => 
				  array (
					0 => '2022-11-16 16:38:49',
				  ),
				  '_wpas_is_waiting_client_reply' => 
				  array (
					0 => '',
				  ),
				  '_wpas_assignee' => 
				  array (
					0 => '73',
				  ),
				  '_edit_lock' => 
				  array (
					0 => '1668616730:1',
				  ),
				  '_edit_last' => 
				  array (
					0 => '1',
				  ),
				  '_wpas_ttl_replies_by_customer' => 
				  array (
					0 => '3',
				  ),
				  '_wpas_ttl_replies_by_agent' => 
				  array (
					0 => '0',
				  ),
				  '_wpas_ttl_replies' => 
				  array (
					0 => '3',
				  ),
				  'auto_delete_attachments' => 
				  array (
					0 => '',
				  ),
				  'auto_delete_attachments_type' => 
				  array (
					0 => 'agent',
				  )
				),
			);

			return $data;
		}

	}

endif; // End if class_exists check.
