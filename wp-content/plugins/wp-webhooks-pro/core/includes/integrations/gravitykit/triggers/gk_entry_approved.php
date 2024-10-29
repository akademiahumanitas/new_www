<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_gravitykit_Triggers_gk_entry_approved' ) ) :

	/**
	 * Load the gk_entry_approved trigger
	*
	* @since 5.2.4
	* @author Ironikus <info@ironikus.com>
	*/
	class WP_Webhooks_Integrations_gravitykit_Triggers_gk_entry_approved {

		public function get_callbacks(){

			return array(
				array(
					'type' => 'action',
					'hook' => 'gravityview/approve_entries/approved',
					'callback' => array( $this, 'ironikus_trigger_gk_entry_approved' ),
					'priority' => 20,
					'arguments' => 1,
					'delayed' => true,
				),
			);

		}

		public function get_details(){

				$parameter = array(
				'form_id' => array( 'short_description' => __( 'The id of the form that was currently approved.', 'wp-webhooks' ) ),
				'entry_id' => array( 'short_description' => __( 'The id of the current form entry.', 'wp-webhooks' ) ),
				'entry' => array( 'short_description' => __( 'The full data of the form entry.', 'wp-webhooks' ) ),
			);

			$settings = array(
				'load_default_settings' => false,
				'data' => array(
					'wpwhpro_gk_entry_approved_trigger_on_forms' => array(
						'id'		  => 'wpwhpro_gk_entry_approved_trigger_on_forms',
						'type'		=> 'select',
						'multiple'	=> true,
						'choices'	  => array(),
						'query'			=> array(
							'filter'	=> 'helpers',
							'args'		=> array(
								'integration' => 'gravityforms',
								'helper' => 'gf_helpers',
								'function' => 'get_query_forms',
							)
						),
						'label'	   => __( 'Trigger on selected forms', 'wp-webhooks' ),
						'placeholder' => '',
						'required'	=> false,
						'description' => __( 'Select only the forms you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', 'wp-webhooks' )
					),
				)
			);

			return array(
				'trigger'	  => 'gk_entry_approved',
				'name'	   => __( 'Entry approved', 'wp-webhooks' ),
				'sentence'	   => __( 'an entry was approved', 'wp-webhooks' ),
				'parameter'	 => $parameter,
				'settings'	 => $settings,
				'returns_code'   => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires after an entry was approved within GravityKit.', 'wp-webhooks' ),
				'description'	=> array(),
				'callback'	 => 'test_gk_entry_approved',
				'integration'	=> 'gravitykit',
				'premium'	=> false,
			);

		}

		public function ironikus_trigger_gk_entry_approved( $entry_id ){

			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'gk_entry_approved' );
			$entry = gravityview_get_entry( $entry_id, true, false );
			$data_array = array(
				'form_id' => ( is_array( $entry ) && isset( $entry['form_id'] ) ) ? intval( $entry['form_id'] ) : 0,
				'entry_id' => $entry_id,
				'entry' => $entry,
			);
			$response_data = array();

			foreach( $webhooks as $webhook ){

				$is_valid = true;

				if( isset( $webhook['settings'] ) ){
					foreach( $webhook['settings'] as $settings_name => $settings_data ){

						if( $settings_name === 'wpwhpro_gk_entry_approved_trigger_on_forms' && ! empty( $settings_data ) ){
							if( ! in_array( $data_array['form_id'], $settings_data ) ){
								$is_valid = false;
							}
						}

					}
				}

				if( $is_valid ) {
					$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

					if( $webhook_url_name !== null ){
						$response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
					} else {
						$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
					}
				}
			}

			do_action( 'wpwhpro/webhooks/trigger_gk_entry_approved', $data_array, $response_data );
		}

		public function get_demo( $options = array() ) {

			$data = array (
				'form_id' => 2,
				'entry_id' => 30,
				'entry' => 
				array (
				2 => '',
				'id' => '30',
				'form_id' => '2',
				'post_id' => NULL,
				'date_created' => '2022-03-14 04:04:30',
				'date_updated' => '2022-03-14 04:04:30',
				'is_starred' => '0',
				'is_read' => '0',
				'ip' => '127.0.0.1',
				'source_url' => 'https://demodomain.test/gravity-forms/',
				'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Safari/537.36',
				'currency' => 'USD',
				'payment_status' => NULL,
				'payment_date' => NULL,
				'payment_amount' => NULL,
				'payment_method' => NULL,
				'transaction_id' => NULL,
				'is_fulfilled' => NULL,
				'created_by' => '1',
				'transaction_type' => NULL,
				'status' => 'active',
				'1.1' => 'Demo Product',
				'1.2' => '$10.00',
				'1.3' => '1',
				'is_approved' => '1',
				'workflow_current_status_timestamp' => false,
				),
			);

			return $data;
		}

	}

endif; // End if class_exists check.