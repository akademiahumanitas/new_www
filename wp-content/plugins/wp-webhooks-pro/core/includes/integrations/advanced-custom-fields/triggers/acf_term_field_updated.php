<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_advanced_custom_fields_Triggers_acf_term_field_updated' ) ) :

 /**
  * Load the acf_term_field_updated trigger
  *
  * @since 4.3.7
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_advanced_custom_fields_Triggers_acf_term_field_updated {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'update_term_meta',
				'callback' => array( $this, 'acf_term_field_updated_callback' ),
				'priority' => 20,
				'arguments' => 4,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'meta_id' => array( 'short_description' => __( '(Integer) The unique ID of the meta value.', 'wp-webhooks' ) ),
			'term_id' => array( 'short_description' => __( '(Integer) The ID of the term this meta value was updated on.', 'wp-webhooks' ) ),
			'meta_key' => array( 'short_description' => __( '(String) The meta key of the updated field.', 'wp-webhooks' ) ),
			'meta_value' => array( 'short_description' => __( '(String) The meta value of the updated field.', 'wp-webhooks' ) ),
		);

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_advanced_custom_fields_trigger_on_selected_term_ids' => array(
					'id'		  => 'wpwhpro_advanced_custom_fields_trigger_on_selected_term_ids',
					'type'		=> 'text',
					'label'	   => __( 'Trigger on selected term IDs', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'Add only the term IDs you want to fire the trigger on. You can also choose multiple ones by comma-separating them. If none are added, all are triggered.', 'wp-webhooks' )
				),
				'wpwhpro_advanced_custom_fields_trigger_on_selected_keys' => array(
					'id'		  => 'wpwhpro_advanced_custom_fields_trigger_on_selected_keys',
					'type'		=> 'text',
					'label'	   => __( 'Trigger on selected meta keys', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'Add only the meta keys you want to fire the trigger on. You can also choose multiple ones by comma-separating them. If none are added, all are triggered.', 'wp-webhooks' )
				),
			)
		);

		return array(
			'trigger'		   => 'acf_term_field_updated',
			'name'			  => __( 'ACF term field updated', 'wp-webhooks' ),
			'sentence'			  => __( 'an ACF term field was updated', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a term field was updated within Advanced Custom Fields.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'advanced-custom-fields',
			'premium'		   => false,
		);

	}

	public function acf_term_field_updated_callback( $meta_id, $object_id, $meta_key, $meta_value ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'acf_term_field_updated' );
		$acf_helpers = WPWHPRO()->integrations->get_helper( 'advanced-custom-fields', 'acf_helpers' );
		$response_data_array = array();

		if( ! $acf_helpers->is_acf_meta_field( $object_id, $meta_key, 'term' ) ){
			return;
		}

		$payload = array(
			'meta_id' => $meta_id,
			'term_id' => $object_id,
			'meta_key' => $meta_key,
			'meta_value' => $meta_value,
		);

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){
	  
				  if( $is_valid && $settings_name === 'wpwhpro_advanced_custom_fields_trigger_on_selected_term_ids' && ! empty( $settings_data ) ){
					$is_valid = false;

					$validated_term_ids = array_map( 'trim', explode( ',', $settings_data ) );

					if( in_array( $object_id, $validated_term_ids ) ){
					  $is_valid = true;
					}

				  }
	  
				  if( $is_valid && $settings_name === 'wpwhpro_advanced_custom_fields_trigger_on_selected_keys' && ! empty( $settings_data ) ){
					$is_valid = false;

					$validated_meta_keys = array_map( 'trim', explode( ',', $settings_data ) );

					if( in_array( $meta_key, $validated_meta_keys ) ){
					  $is_valid = true;
					}

				  }
	  
				}
			}

			if( $is_valid ){
				if( $webhook_url_name !== null ){
					$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				} else {
					$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				}
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_acf_term_field_updated', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'meta_id' => '4928',
			'term_id' => 73,
			'meta_key' => 'demo_field',
			'meta_value' => 'This is a demo value.',
		  );

		return $data;
	}

  }

endif; // End if class_exists check.