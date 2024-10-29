<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_mailpoet_Triggers_mp_form_submitted' ) ) :

	/**
	 * Load the mp_form_submitted trigger
	 *
	 * @since 6.0.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_mailpoet_Triggers_mp_form_submitted {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'mailpoet_subscription_before_subscribe',
					'callback'  => array( $this, 'mp_form_submitted_callback' ),
					'priority'  => 10,
					'arguments' => 3,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {


			$parameter = array(
				'success'     => array( 'short_description' => __( '(Bool) True if the the webhook was fired successfully.', 'wp-webhooks' ) ),
				'msg'         => array( 'short_description' => __( '(String) Further details about the request.', 'wp-webhooks' ) ),
				'data'        => array(
					'label'             => __( 'Additional Data', 'wp-webhooks' ),
					'short_description' => __( '(Array) Further details about the submitted data.', 'wp-webhooks' ),
				),
			);

			$settings = array(
				'load_default_settings' => true,
				'data' => array(
					'wpwhpro_mp_submit_trigger_on_forms' => array(
						'id'		  => 'wpwhpro_mp_submit_trigger_on_forms',
						'type'		=> 'select',
						'multiple'	=> true,
						'choices'	  => array(),
						'query'			=> array(
							'filter'	=> 'helpers',
							'args'		=> array(
								'integration' => 'mailpoet',
								'helper' => 'mp_helpers',
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
				'trigger'           => 'mp_form_submitted',
				'name'              => __( 'Form submitted', 'wp-webhooks' ),
				'sentence'          => __( 'a form was submitted', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires as soon as a form was submitted within MailPoet.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'mailpoet',
				'premium'           => true,
			);

		}

		/**
		 * Triggers when MailPoet creates a company
		 *
		 * @param $company_id Company's id
		 */
		public function mp_form_submitted_callback( $data, $segmentIds, $form ) {

			$webhooks            = WPWHPRO()->webhook->get_hooks( 'trigger', 'mp_form_submitted' );
			$response_data_array = array();

			$form_id = $form->getID();

			$data_array = array(
				'form_name'  => $form->getName(),
				'form_id'    => $form_id,
				'data'      => $data,
			);

			$payload = array(
				'success' => true,
				'msg'     => __( 'The form has been submitted.', 'wp-webhooks' ),
				'data'    => $data_array,
			);

			foreach ( $webhooks as $webhook ) {

				$webhook_url_name = ( is_array( $webhook ) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
				$is_valid         = true;

				if( isset( $webhook['settings'] ) ){
					foreach( $webhook['settings'] as $settings_name => $settings_data ){
						if( $settings_name === 'wpwhpro_gf_submit_trigger_on_forms' && ! empty( $settings_data ) ){
							if( ! in_array( $form_id, $settings_data ) ){
								$is_valid = false;
							}
						}

					}
				}
				if ( $is_valid ) {
					if ( $webhook_url_name !== null ) {
						$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					} else {
						$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					}
				}
			}

			do_action( 'wpwhpro/webhooks/trigger_mp_form_submitted', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array(
				'success' => true,
				'msg' => 'The form has been submitted.',
				'data' => 
			   array(
				  'form_name' => 'Newsletter Signup',
				  'form_id' => 2,
				  'data' => 
				 array(
					'email' => 'demo@demo.test',
				 ),
			   ),
			);

			return $data;
		}

	}

endif; // End if class_exists check.
