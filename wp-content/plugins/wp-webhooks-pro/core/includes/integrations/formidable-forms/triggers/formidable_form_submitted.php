<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_formidable_forms_Triggers_formidable_form_submitted' ) ) :

 /**
  * Load the formidable_form_submitted trigger
  *
  * @since 4.2.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_formidable_forms_Triggers_formidable_form_submitted {

  /**
   * Register the actual functionality of the webhook
   *
   * @param mixed $response
   * @param string $action
   * @param string $response_ident_value
   * @param string $response_api_key
   * @return mixed The response data for the webhook caller
   */
	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'frm_after_create_entry',
				'callback' => array( $this, 'frm_after_create_entry_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	/**
	 * Defines the details of the trigger
	 *
	 * @return array
	 */
	public function get_details(){

		$parameter = array(
			'submission' => array( 'short_description' => __( '(Array) The data that was submitted within the form.', 'wp-webhooks' ) ),
			'entry' => array( 'short_description' => __( '(Array) Further details about the current submission.', 'wp-webhooks' ) ),
			'form' => array( 'short_description' => __( '(Array) The data about the form definition (E.g. fields, standard text, etc.).', 'wp-webhooks' ) ),
		);

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_formidable_trigger_on_forms' => array(
					'id'		  => 'wpwhpro_formidable_trigger_on_forms',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'helpers',
						'args'		=> array(
							'integration' => 'formidable-forms',
							'helper' => 'formidable_helpers',
							'function' => 'get_query_forms',
						)
					),
					'label'	   => __( 'Trigger on selected forms', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'Select only the forms you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', 'wp-webhooks' )
				),
				'wpwhpro_formidable_submission_by_field_id' => array(
					'id'		  => 'wpwhpro_formidable_submission_by_field_id',
					'type'		=> 'checkbox',
					'label'	   => __( 'Submission by field IDs', 'wp-webhooks' ),
					'placeholder' => '',
					'required'	=> false,
					'description' => __( 'By default, Formidable returns the submission fields with generic keys. This doesn not matter when all fields are sent over, but in some cases, empty fields are not submitted, causing the order to shift. To avoid that, activate this setting and we turn the generic index into the form field ID, making sure you always address the same field.', 'wp-webhooks' )
				),
			)
		);

		return array(
			'trigger'		   => 'formidable_form_submitted',
			'name'			  => __( 'Form submitted', 'wp-webhooks' ),
			'sentence'			  => __( 'a form was submitted', 'wp-webhooks' ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => __( 'This webhook fires as soon as a form is submitted within Formidable Forms.', 'wp-webhooks' ),
			'description'	   => array(),
			'integration'	   => 'formidable-forms',
			'premium'		   => false,
		);

	}

	/**
	 * Triggers once a user submits a Formidable Forms form
	 *
	   * @param int $form_id - the form id
	 * @param array $response - the post response
	 */
	public function frm_after_create_entry_callback( $entry_id, $form_id ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'formidable_form_submitted' );

		$entry = $entry_id;
		$form = $form_id;
		$meta = FrmEntryMeta::get_entry_meta_info( $entry_id );

		FrmEntry::maybe_get_entry( $entry );
		FrmForm::maybe_get_form( $form );

		$payload = array(
			'submission' => $meta,
			'entry' => $entry,
			'form' => $form,
		);

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){

				if( isset( $webhook['settings']['wpwhpro_formidable_trigger_on_forms'] ) && ! empty( $webhook['settings']['wpwhpro_formidable_trigger_on_forms'] ) ){
					if( ! in_array( $form_id, $webhook['settings']['wpwhpro_formidable_trigger_on_forms'] ) ){
						$is_valid = false;
					}
				}

				if( 
					isset( $webhook['settings']['wpwhpro_formidable_submission_by_field_id'] ) 
					&& ! empty( $webhook['settings']['wpwhpro_formidable_submission_by_field_id'] )
				){
					
					$filtered_meta = array();

					if( ! empty( $meta ) ){
						foreach( $meta as $mk => $mv ){
							if( is_object( $mv ) && isset( $mv->field_id ) ){
								$field_id_key = 'field_id_' . $mv->field_id;
								$filtered_meta[ $field_id_key ] = $mv;
							} else {
								$filtered_meta[ $mk ] = $mv;
							}
						}
					}

					$payload['submission'] = $filtered_meta;

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

		do_action( 'wpwhpro/webhooks/trigger_formidable_form_submitted', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'submission' => 
			array (
			  0 => 
			  array (
				'id' => '7',
				'meta_value' => 'Value 1',
				'field_id' => '6',
				'item_id' => '4',
				'created_at' => '2021-07-24 20:31:26',
			  ),
			  1 => 
			  array (
				'id' => '8',
				'meta_value' => 'Value 2',
				'field_id' => '7',
				'item_id' => '4',
				'created_at' => '2021-07-24 20:31:26',
			  ),
			),
			'entry' => 
			array (
			  'id' => '4',
			  'item_key' => 'r6n2e',
			  'name' => 'Value 1',
			  'description' => 
			  array (
				'browser' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36',
				'referrer' => 'https://yourdomain.test/formidable/',
			  ),
			  'ip' => '127.0.0.1',
			  'form_id' => '3',
			  'post_id' => '0',
			  'user_id' => '1',
			  'parent_item_id' => '0',
			  'is_draft' => '0',
			  'updated_by' => '1',
			  'created_at' => '2021-07-24 20:31:26',
			  'updated_at' => '2021-07-24 20:31:26',
			  'form_name' => 'Demo Template 1',
			  'form_key' => 'demotemplate1',
			),
			'form' => 
			array (
			  'id' => '3',
			  'form_key' => 'demotemplate1',
			  'name' => 'Demo Template 1',
			  'description' => '',
			  'parent_form_id' => '0',
			  'logged_in' => '0',
			  'editable' => '0',
			  'is_template' => '0',
			  'default_template' => '0',
			  'status' => 'published',
			  'options' => 
			  array (
				'submit_value' => 'Submit',
				'success_action' => 'message',
				'success_msg' => 'Your responses were successfully submitted. Thank you!',
				'show_form' => 0,
				'akismet' => '',
				'honeypot' => 'basic',
				'antispam' => 0,
				'no_save' => 0,
				'ajax_load' => 0,
				'js_validate' => 0,
				'form_class' => '',
				'custom_style' => 1,
				'before_html' => htmlspecialchars( '<legend class="frm_screen_reader">[form_name]</legend>
				[if form_name]<h3 class="frm_form_title">[form_name]</h3>[/if form_name]
				[if form_description]<div class="frm_description">[form_description]</div>[/if form_description]' ),
				'after_html' => '',
				'submit_html' => htmlspecialchars( '<div class="frm_submit">
				[if back_button]<button type="submit" name="frm_prev_page" formnovalidate="formnovalidate" class="frm_prev_page" [back_hook]>[back_label]</button>[/if back_button]
				<button class="frm_button_submit" type="submit"  [button_action]>[button_label]</button>
				[if save_draft]<a href="#" tabindex="0" class="frm_save_draft" [draft_hook]>[draft_label]</a>[/if save_draft]
				</div>' ),
			  ),
			  'created_at' => '2021-07-24 20:17:45',
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.