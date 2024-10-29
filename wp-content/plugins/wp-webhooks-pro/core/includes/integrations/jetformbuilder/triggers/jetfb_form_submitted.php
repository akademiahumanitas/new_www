<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Webhooks_Integrations_jetformbuilder_Triggers_jetfb_form_submitted' ) ) :

	/**
	 * Load the jetfb_form_submitted trigger
	 *
	 * @since 6.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_jetformbuilder_Triggers_jetfb_form_submitted {

		public function get_callbacks() {

			return array(
				array(
					'type'      => 'action',
					'hook'      => 'jet-form-builder/form-handler/after-send',
					'callback'  => array( $this, 'jetfb_form_submitted_callback' ),
					'priority'  => 10,
					'arguments' => 2,
					'delayed'   => true,
				),
			);
		}

		public function get_details() {

			$parameter = array(
				'success' => array( 'short_description' => __( '(Bool) True if the form was successfully submitted.', 'wp-webhooks' ) ),
				'msg'     => array( 'short_description' => __( '(String) Further details about the submitting a form.', 'wp-webhooks' ) ),
				'data'     => array( 'short_description' => __( '(String) Further data about the submitted form.', 'wp-webhooks' ) ),
			);

			$settings = array(
				'load_default_settings' => true,
                'data' => array(
					'wpwhpro_jetfb_submit_trigger_on_forms' => array(
						'id'		  => 'wpwhpro_jetfb_submit_trigger_on_forms',
						'type'		=> 'select',
						'multiple'	=> true,
						'choices'	  => array(),
						'query'			=> array(
							'filter'	=> 'posts',
							'args'		=> array(
								'post_type' => 'jet-form-builder',
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
				'trigger'           => 'jetfb_form_submitted',
				'name'              => __( 'Form submitted', 'wp-webhooks' ),
				'sentence'          => __( 'a form was submitted', 'wp-webhooks' ),
				'parameter'         => $parameter,
				'settings'          => $settings,
				'returns_code'      => $this->get_demo( array() ),
				'short_description' => __( 'This webhook fires as soon as a form was submitted within JetFormBuilder.', 'wp-webhooks' ),
				'description'       => array(),
				'integration'       => 'jetformbuilder',
				'premium'           => true,
			);

		}

		/**
		 * Triggers when JetFormBuilder submits a form
		 *
		 * @param $data Object
		 * @param $is_success Bool
		 */
		public function jetfb_form_submitted_callback( $data, $is_success ) {

            if( $is_success == false ) {
                return;
            }

			$webhooks            = WPWHPRO()->webhook->get_hooks( 'trigger', 'jetfb_form_submitted' );
			$response_data_array = array();

			$form_data = $data;
			unset( $form_data->request_handler );

			$payload = array(
				'success'    => true,
				'msg'        => __( 'The form has been submitted successfully.', 'wp-webhooks' ),
				'dataid'       => $form_data->form_id,
				'data'       => $form_data,
			);

			foreach ( $webhooks as $webhook ) {

				$webhook_url_name = ( is_array( $webhook ) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
				$is_valid         = true;

                if( isset( $webhook['settings'] ) ){
					foreach( $webhook['settings'] as $settings_name => $settings_data ){
						if( $settings_name === 'wpwhpro_jetfb_submit_trigger_on_forms' && ! empty( $settings_data ) ){
							if( ! in_array( $data->form_id, $settings_data ) ){
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

			do_action( 'wpwhpro/webhooks/trigger_jetfb_form_submitted', $payload, $response_data_array );
		}

		public function get_demo( $options = array() ) {

			$data = array (
				'success' => true,
				'msg' => 'The form has been submitted successfully.',
				'data' => 
				array (
				  'hook_key' => 'jet_form_builder_submit',
				  'hook_val' => 'submit',
				  'form_data' => 
				  array (
				  ),
				  'response_data' => 
				  array (
				  ),
				  'is_ajax' => false,
				  'is_success' => true,
				  'response_args' => 
				  array (
					'status' => 'success',
				  ),
				  'form_id' => 9464,
				  'refer' => 'https://yourdomain.test/jetformbuilder/',
				  'manager' => NULL,
				  'action_handler' => 
				  array (
					'form_id' => 9464,
					'request_data' => 
					array (
					  '__form_id' => 9464,
					  '__refer' => 'https://yourdomain.test/jetformbuilder/',
					  '__is_ajax' => false,
					  'post_id' => '9466',
					  'text_field' => 'This is a demo text field',
					  'field_name' => '',
					),
					'form_actions' => 
					array (
					  1946 => 
					  array (
						'settings' => 
						array (
						  'subject' => 'New order on website',
						  'content' => 'Hi admin!
			  
			  There are new order on your website.
			  
			  Order details:
			  - Post ID: %post_id%',
						  'mail_to' => 'admin',
						  'reply_to' => 'form',
						),
						'_id' => 1946,
						'option_name' => NULL,
						'messages' => 
						array (
						),
						'heading' => '',
					  ),
					),
					'is_ajax' => false,
					'size_all' => NULL,
					'response_data' => 
					array (
					),
					'context' => 
					array (
					),
					'current_position' => 0,
					'current_flow_handler' => '',
				  ),
				  'form_key' => '_jet_engine_booking_form_id',
				  'refer_key' => '_jet_engine_refer',
				  'post_id_key' => '__queried_post_id',
				  'csrf' => 
				  array (
				  ),
				),
			);

			return $data;
		}

	}

endif; // End if class_exists check.
