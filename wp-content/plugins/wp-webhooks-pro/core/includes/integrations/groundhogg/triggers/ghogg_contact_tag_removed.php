<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_groundhogg_Triggers_ghogg_contact_tag_removed' ) ) :

 /**
  * Load the ghogg_contact_tag_removed trigger
  *
  * @since 4.3.5
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_groundhogg_Triggers_ghogg_contact_tag_removed {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'groundhogg/contact/tag_removed',
				'callback' => array( $this, 'ghogg_contact_tag_removed_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'tag_id' => array( 'short_description' => __( '(Integer) The id of the tag that was removed.', 'wp-webhooks' ) ),
			'contact_id' => array( 'short_description' => __( '(Integer) The id of the contact.', 'wp-webhooks' ) ),
			'user_id' => array( 'short_description' => __( '(Integer) The id of user.', 'wp-webhooks' ) ),
			'contact_first_name' => array( 'short_description' => __( '(String) The first name of the contact.', 'wp-webhooks' ) ),
			'contact_last_name' => array( 'short_description' => __( '(String) The last name of the contact.', 'wp-webhooks' ) ),
			'contact_email' => array( 'short_description' => __( '(String) The email of the contact.', 'wp-webhooks' ) ),
			'contact_address' => array( 'short_description' => __( '(String) The address of the contact.', 'wp-webhooks' ) ),
			'contact_phone' => array( 'short_description' => __( '(String) The phone number of the contact.', 'wp-webhooks' ) ),
			'contact_mobile' => array( 'short_description' => __( '(String) The mobile number of the contact.', 'wp-webhooks' ) ),
			'contact_optin_status' => array( 'short_description' => __( '(String) The optin status of the contact.', 'wp-webhooks' ) ),
			'contact_marketable' => array( 'short_description' => __( '(Bool) True if the contact is marketable, false if not.', 'wp-webhooks' ) ),
			'contact_notes' => array( 'short_description' => __( '(Array) Notes about the contact.', 'wp-webhooks' ) ),
			'contact' => array( 'short_description' => __( '(Array) The full contact data.', 'wp-webhooks' ) ),
		);

		$settings = array(
			'load_default_settings' => true,
			'data' => array()
		);

		return array(
			'trigger'		   => 'ghogg_contact_tag_removed',
			'name'			  => __( 'Contact tag removed', 'wp-webhooks' ),
			'sentence'			  => __( 'a tag was removed from a contact', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a tag was removed from a contact within Groundhogg.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'groundhogg',
			'premium'		   => true,
		);

	}

	public function ghogg_contact_tag_removed_callback( $contact, $tag_id ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'ghogg_contact_tag_removed' );
		$response_data_array = array();

		$payload = array(
			'tag_id' => $tag_id,
			'contact_id' => $contact->get_id(),
			'user_id' => $contact->get_user_id(),
			'contact_first_name' => $contact->get_first_name(),
			'contact_last_name' => $contact->get_last_name(),
			'contact_email' => $contact->get_email(),
			'contact_address' => $contact->get_address(),
			'contact_phone' => $contact->get_phone_number(),
			'contact_mobile' => $contact->get_mobile_number(),
			'contact_optin_status' => $contact->get_optin_status(),
			'contact_marketable' => $contact->is_marketable(),
			'contact_notes' => $contact->get_notes(),
			'contact' => $contact,
		);

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( $is_valid ){
				if( $webhook_url_name !== null ){
					$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				} else {
					$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				}
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_ghogg_contact_tag_removed', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'tag_id' => 12,
			'contact_id' => 1,
			'user_id' => 1,
			'contact_first_name' => 'Jon',
			'contact_last_name' => 'Doe',
			'contact_email' => 'jondoe@demo.test',
			'contact_address' => 
			array (
			  'street_address_1' => 'Demo Street 3',
			  'postal_zip' => '12345',
			  'city' => 'Demo City',
			  'country' => 'US',
			),
			'contact_phone' => '',
			'contact_mobile' => '',
			'contact_optin_status' => 2,
			'contact_marketable' => true,
			'contact_notes' => 
			array (
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.