<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_advanced_custom_fields_Triggers_acf_post_field_updated' ) ) :

 /**
  * Load the acf_post_field_updated trigger
  *
  * @since 4.3.7
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_advanced_custom_fields_Triggers_acf_post_field_updated {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'updated_post_meta',
				'callback' => array( $this, 'acf_post_field_updated_callback' ),
				'priority' => 20,
				'arguments' => 4,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'meta_id' => array( 'short_description' => __( '(Integer) The unique ID of the meta value.', 'wp-webhooks' ) ),
			'post_id' => array( 'short_description' => __( '(Integer) The ID of the post this meta value was updated on.', 'wp-webhooks' ) ),
			'meta_key' => array( 'short_description' => __( '(String) The meta key of the updated field.', 'wp-webhooks' ) ),
			'meta_value' => array( 'short_description' => __( '(String) The meta value of the updated field.', 'wp-webhooks' ) ),
		);

		$description = array(
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'updated_post_meta',
				),
			),
			'tipps' => array(
				__( 'You can fire this trigger as well on specific post types. To do that, simply select the post types from the dropdown within the webhook URL settings.', 'wp-webhooks' ),
				__( 'It is also posible to fire trigger on specific post IDs. To do that, simply specify the post IDs within the webhook URL settings.', 'wp-webhooks' ),
				__( 'It is also posible to fire trigger on specific meta keys. To do that, simply specify the meta keys within the webhook URL settings.', 'wp-webhooks' ),
			)
		);

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_advanced_custom_fields_trigger_on_selected_post_types' => array(
					'id'		  => 'wpwhpro_advanced_custom_fields_trigger_on_selected_post_types',
					'type'		=> 'select',
					'multiple'		=> true,
					'choices'		=> array(),
					'query'			=> array(
						'filter'	=> 'post_types',
						'args'		=> array(
							'show_ui' => true
						)
					),
					'label'	   => __( 'Trigger on selected post types', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'Select only the post types you want to fire the trigger on. You can also choose multiple ones. If none are selected, all are triggered.', 'wp-webhooks' )
				),
				'wpwhpro_advanced_custom_fields_trigger_on_selected_post_ids' => array(
					'id'		  => 'wpwhpro_advanced_custom_fields_trigger_on_selected_post_ids',
					'type'		=> 'text',
					'label'	   => __( 'Trigger on selected post IDs', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'Add only the post IDs you want to fire the trigger on. You can also choose multiple ones by comma-separating them. If none are added, all are triggered.', 'wp-webhooks' )
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
			'trigger'		   => 'acf_post_field_updated',
			'name'			  => __( 'ACF post field updated', 'wp-webhooks' ),
			'sentence'			  => __( 'an ACF post field was updated', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a post field was updated within Advanced Custom Fields.', 'wp-webhooks' ),
			'description'	   => $description,
			'integration'	   => 'advanced-custom-fields',
			'premium'		   => false,
		);

	}

	public function acf_post_field_updated_callback( $meta_id, $object_id, $meta_key, $meta_value ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'acf_post_field_updated' );
		$acf_helpers = WPWHPRO()->integrations->get_helper( 'advanced-custom-fields', 'acf_helpers' );
		$response_data_array = array();
		$post_type = get_post_type( $object_id );

		if( ! $acf_helpers->is_acf_meta_field( $object_id, $meta_key ) ){
			return;
		}

		$payload = array(
			'meta_id' => $meta_id,
			'post_id' => $object_id,
			'meta_key' => $meta_key,
			'meta_value' => $meta_value,
		);

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){
	  
				  if( $settings_name === 'wpwhpro_advanced_custom_fields_trigger_on_selected_post_types' && ! empty( $settings_data ) ){
					if( ! in_array( $post_type, $settings_data ) ){
						$is_valid = false;
					  }
				  }
	  
				  if( $is_valid && $settings_name === 'wpwhpro_advanced_custom_fields_trigger_on_selected_post_ids' && ! empty( $settings_data ) ){
					$is_valid = false;

					$validated_post_ids = array_map( 'trim', explode( ',', $settings_data ) );

					if( in_array( $object_id, $validated_post_ids ) ){
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

		do_action( 'wpwhpro/webhooks/trigger_acf_post_field_updated', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'meta_id' => '73626',
			'post_id' => 7919,
			'meta_key' => 'billing_address_1',
			'meta_value' => 'Demo Street 1234',
		);

		return $data;
	}

  }

endif; // End if class_exists check.