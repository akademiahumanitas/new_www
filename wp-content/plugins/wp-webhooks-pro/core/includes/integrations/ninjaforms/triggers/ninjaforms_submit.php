<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_ninjaforms_Triggers_ninjaforms_submit' ) ) :

 /**
  * Load the ninjaforms_submit trigger
  *
  * @since 4.2.1
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_ninjaforms_Triggers_ninjaforms_submit {

  /**
   * Register the actual functionality of the webhook
   *
   * @return array The registered callbacks
   */
	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'ninja_forms_after_submission',
				'callback' => array( $this, 'wpwh_trigger_ninjaforms_submit' ),
				'priority' => 20,
				'arguments' => 1,
				'delayed' => false,
			),
		);
	}

	public function get_details(){

		$parameter = array(
			'form_id' => array( 'short_description' => __( '(Integer) ID of the form that was submitted.', 'wp-webhooks' ) ),
			'actions' => array( 'short_description' => __( '(Array) Further information about what happened after the form submission.', 'wp-webhooks' ) ),
			'form_submit_data' => array( 'short_description' => __( '(Array) The data that was submitted via the form.', 'wp-webhooks' ) ),
		);

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_ninja_forms' => array(
					'id'		  => 'wpwhpro_ninja_forms',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'helpers',
						'args'		=> array(
							'integration' => 'ninjaforms',
							'helper' => 'ninjaforms_helpers',
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
			'trigger'		   => 'ninjaforms_submit',
			'name'			  => __( 'Form submitted', 'wp-webhooks' ),
			'sentence'			  => __( 'a form was submitted', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as an "Ninja Forms" form was submitted.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'ninjaforms',
		);

	}

	/**
	 * Triggers once a new Ninja Forms form was submitted
	 *
	 * @param  $form The Ninja Forms form
	 */
	public function wpwh_trigger_ninjaforms_submit( $form ){

		$form_id = 0;
		if( isset( $form['form_id'] ) ){
			$form_id = $form['form_id'];
		}

		$actions = null;
		if( isset( $form['actions'] ) ){
			$actions = $form['actions'];
		}

		$form_submit_data = array();
		if( isset( $form['fields'] ) ){
			foreach ( $form['fields'] as $field ) {
				$form_submit_data[ $field['id'] ] = array(
					'field_id' => $field['id'],
					'key' => $field['key'],
					'value' => $field['value'],
					'label' => $field['label'],
				);
			}
		}

		$payload = array(
			'form_id' => $form_id,
			'actions' => $actions,
			'form_submit_data' => $form_submit_data,
		);

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'ninjaforms_submit' );
		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				if( isset( $webhook['settings']['wpwhpro_ninja_forms'] ) && ! empty( $webhook['settings']['wpwhpro_ninja_forms'] ) ){
					if( ! in_array( $form_id, $webhook['settings']['wpwhpro_ninja_forms'] ) ){
						$is_valid = false;
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

		do_action( 'wpwhpro/integrations/ninjaforms/triggers/ninjaforms_submit', $payload, $form );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'form_id' => '1',
			'actions' => 
			array (
			  'save' => 
			  array (
				'hidden' => 
				array (
				  0 => 'submit',
				),
				'sub_id' => 1235,
			  ),
			  'email' => 
			  array (
				'to' => 'admin@domain.test',
				'headers' => 
				array (
				  0 => 'Content-Type: text/html',
				  1 => 'charset=UTF-8',
				  2 => 'X-Ninja-Forms:ninja-forms',
				  3 => 'From: Admin <admin@domain.test>',
				  4 => 'Reply-to: jondoe@domain.test <jondoe@domain.test>',
				),
				'attachments' => 
				array (
				),
				'sent' => true,
			  ),
			  'success_message' => '<p>Form submittedted successfully.</p>
		  <p>A confirmation email was sent to jondoe@domain.test.</p>
		  ',
			),
			'form_submit_data' => 
			array (
			  1 => 
			  array (
				'field_id' => 1,
				'key' => 'name',
				'value' => 'Jon Doe',
				'label' => 'Name',
			  ),
			  2 => 
			  array (
				'field_id' => 2,
				'key' => 'email',
				'value' => 'jondoe@domain.test',
				'label' => 'Email',
			  ),
			  3 => 
			  array (
				'field_id' => 3,
				'key' => 'message',
				'value' => 'This is a sample message.',
				'label' => 'Message',
			  ),
			  4 => 
			  array (
				'field_id' => 4,
				'key' => 'submit',
				'value' => '',
				'label' => 'Submit',
			  ),
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.