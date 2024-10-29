<?php

$flow = WPWHPRO()->flows->get_flow_log( $flow_log_id );
$integrations = WPWHPRO()->integrations->get_integrations();
$trigger_settings = WPWHPRO()->settings->get_required_trigger_settings();
$action_settings = WPWHPRO()->settings->get_required_action_settings();
$default_trigger_settings = WPWHPRO()->settings->get_default_trigger_settings();
$conditional_labels = WPWHPRO()->settings->get_flow_condition_labels();

//initialize an action buffer
$this->single_flow_integration_buffer = array();
$this->single_flow_action_buffer = array();

$flow_date = date( 'F j, Y, g:i a', strtotime( $flow->flow_date ) );
$flow_status = ( ! empty( $flow->flow_completed ) ) ? __( 'Completed', 'wp-webhooks' ) : __( 'Incomplete', 'wp-webhooks' );
$trigger_integration_title = '';
$trigger_integration = '';
$trigger_integration_icon_url = '';
$trigger_conditionals = array();
$trigger_step_name = '';
$trigger_endpoint_slug = '';
$trigger_data = array();
$trigger_step_data_full = ( ! empty( $flow->flow_payload['trigger'] ) ) ? $flow->flow_payload['trigger'] : array();
//Compatibility with 6.1.0 
$trigger_step_data = ( isset( $trigger_step_data_full['wpwh_payload'] ) && is_array( $trigger_step_data_full['wpwh_payload'] ) ) ? $trigger_step_data_full['wpwh_payload'] : $trigger_step_data_full;
$trigger_step_settings = array();
$trigger_settings_display = array();

$is_trigger_error = ( isset( $trigger_step_data_full['wpwh_status'] ) && $trigger_step_data_full['wpwh_status'] === 'cancelled' ) ? true : false;
$trigger_error_label = ( $is_trigger_error && isset( $trigger_step_data_full['msg'] ) ) ? $trigger_step_data_full['msg'] : '';
$trigger_header_background_css = '';

if( $is_trigger_error ){
	$trigger_header_background_css = 'background-color: #ffc2c2 !important;';
}

if( 
	! empty( $flow ) 
	&& isset( $flow->flow_config )
	&& isset( $flow->flow_config['triggers'] )
	&& ! empty( $flow->flow_config['triggers'] )
){
	foreach( $flow->flow_config['triggers'] as $flow_trigger ){
		$trigger_integration = $flow_trigger['integration'];
		$trigger_step_name = $flow_trigger['name'];
		$trigger_endpoint_slug = $flow_trigger['trigger'];
		$trigger_step_settings = $flow_trigger['fields'];
		$trigger_conditionals = ( isset( $flow_trigger['conditionals'] ) ) ? $flow_trigger['conditionals'] : array();
		break;
	}
}

$trigger_integration_details = WPWHPRO()->integrations->get_details( $trigger_integration );
if( !empty( $trigger_integration_details ) ){
	$trigger_integration_title = $trigger_integration_details['name'];
	$trigger_integration_icon_url = $trigger_integration_details['icon'];
}

$trigger_data = WPWHPRO()->integrations->get_triggers( $trigger_integration, $trigger_endpoint_slug );

//Merge trigger default settings
if( isset( $trigger_data['settings'] ) && isset( $trigger_data['settings']['load_default_settings'] ) ){
	$trigger_settings = array_merge( $default_trigger_settings, $trigger_settings );
}

//Merge trigger specific settings
if( isset( $trigger_data['settings'] ) && isset( $trigger_data['settings']['data'] ) ){
	$trigger_settings = array_merge( $trigger_data['settings']['data'], $trigger_settings );
}

//Validate given trigger settings
foreach( $trigger_step_settings as $single_trigger_step_settings ){

	if( ! isset( $single_trigger_step_settings['value'] ) || $single_trigger_step_settings['value'] === '' ){
		continue;
	}

	if( is_array( $single_trigger_step_settings['value'] ) ){
		$single_settings_value = json_encode( $single_trigger_step_settings['value'] );
	} elseif ( is_bool( $single_trigger_step_settings['value'] ) ) {
		if( $single_trigger_step_settings['value'] ){
			$single_settings_value = 'true';
		} else {
			$single_settings_value = 'false';
		}
	} else {
		$single_settings_value = esc_html( $single_trigger_step_settings['value'] );
	}

	if( isset( $single_trigger_step_settings['field_type'] ) && $single_trigger_step_settings['field_type'] === 'checkbox' && ! empty( $single_trigger_step_settings['value'] ) ){
		$single_settings_value = '<svg width="20" height="20" aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="mr-0 ml-auto">
		<path fill="rgb(19, 208, 171)" d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z">
		</path>
		</svg>';
	}

	if( isset( $trigger_settings[ $single_trigger_step_settings['name'] ]['label'] ) ){
		$trigger_settings_display[ $single_trigger_step_settings['name'] ] = array(
			'label' => esc_html( $trigger_settings[ $single_trigger_step_settings['name'] ]['label'] ),
			'value' => $single_settings_value,
		);
	} else {
		$trigger_settings_display[ $single_trigger_step_settings['name'] ] = array(
			'label' => esc_html( $single_trigger_step_settings['name'] ),
			'value' => $single_settings_value,
		);
	}
}


//In case no specific name is given, use the integration name
if( 
	$trigger_step_name === '' 
	&& is_array( $trigger_integration_details ) 
	&& isset( $trigger_integration_details['name'] )
){
	$trigger_step_name = esc_html( $trigger_integration_details['name'] );
}

$flow_actions = array();
if( 
	! empty( $flow ) 
	&& isset( $flow->flow_config )
	&& isset( $flow->flow_config['actions'] )
	&& ! empty( $flow->flow_config['actions'] )
){
	foreach( $flow->flow_config['actions'] as $flow_action_key => $flow_action ){
		$action_integration = $flow_action['integration'];
		$action_step_name = $flow_action['name'];
		$action_endpoint_slug = $flow_action['action'];
		$action_step_settings = $flow_action['fields'];
		$action_step_arguments = array();
		$action_settings_display = array();
		$action_argument_display = array();
		$action_step_conditionals = array(
			'conditionals' => ( isset( $flow_action['conditionals'] ) ) ? $flow_action['conditionals'] : array(),
			'conditionals_after' => ( isset( $flow_action['conditionals_after'] ) ) ? $flow_action['conditionals_after'] : array(),
		);

		if( ! isset( $this->single_flow_integration_buffer[ $flow_action['integration'] ] ) ){
			$this->single_flow_integration_buffer[ $flow_action['integration'] ] = WPWHPRO()->integrations->get_details( $flow_action['integration'] );
		}

		if( ! isset( $this->single_flow_action_buffer[ $action_endpoint_slug ] ) ){
			$this->single_flow_action_buffer[ $action_endpoint_slug ] = WPWHPRO()->integrations->get_actions( $action_integration, $action_endpoint_slug );
		}

		//In case no specific name is given, use the integration name
		if( $action_step_name === '' ){
			$action_step_name = esc_html( $this->single_flow_integration_buffer[ $flow_action['integration'] ]['name'] );
		}

		$action_step_response = array();
		$action_step_response_status = 'skipped';
		$action_step_response_timestamp = 0;
		$action_step_response_msg = '';
		if( 
			isset( $flow->flow_payload ) 
			&& isset( $flow->flow_payload['actions'] )
			&& isset( $flow->flow_payload['actions'][ $flow_action_key ] )
		){
			$action_step_response_full = $flow->flow_payload['actions'][ $flow_action_key ];
			$action_step_response = $action_step_response_full;

			//Follow the new 6.1.0 notation
			if( is_array( $action_step_response ) && isset( $action_step_response['wpwh_payload'] ) ){
				$action_step_response = $action_step_response['wpwh_payload'];

				if( isset( $action_step_response_full['msg'] ) ){
					$action_step_response_msg = $action_step_response_full['msg'];
				}

				if( isset( $action_step_response_full['wpwh_status'] ) ){
					$action_step_response_status = $action_step_response_full['wpwh_status'];
				}

				if( isset( $action_step_response_full['timestamp'] ) ){
					$action_step_response_timestamp = $action_step_response_full['timestamp'];
				}
				
			} else {
				$action_step_response_status = 'ok';
			}

		}

		$action_step_is_scheduled = 0;

		if( 
			is_array( $action_step_response )
			&& isset( $action_step_response['success'] )
			&& $action_step_response['success'] === true
			&& isset( $action_step_response['content'] )
			&& isset( $action_step_response['content']['wpwh_schedule'] )
			&& $action_step_response['content']['wpwh_schedule'] === 'action'
			&& isset( $action_step_response['content']['scheduled_id'] )
			&& ! empty( $action_step_response['content']['scheduled_id'] )
			&& isset( $action_step_response['content']['timestamp'] )
			&& ! empty( $action_step_response['content']['timestamp'] )
		){
			$action_step_is_scheduled = (int) $action_step_response['content']['timestamp'];
		}

		//Validate given action settings
		foreach( $action_step_settings as $single_action_step_settings_key => $single_action_step_settings ){

			//Only show settings and no arguments
			if( $single_action_step_settings['type'] !== 'setting' || ! isset( $single_action_step_settings['value'] ) || $single_action_step_settings['value'] === '' ){

				if( $single_action_step_settings['type'] === 'argument' ){
					$action_step_arguments[ $single_action_step_settings_key ] = $single_action_step_settings;
				}

				continue;
			}

			if( is_array( $single_action_step_settings['value'] ) ){
				$single_settings_value = json_encode( $single_action_step_settings['value'] );
			} elseif( is_bool( $single_action_step_settings['value'] ) ) {
				if( $single_action_step_settings['value'] ){
					$single_settings_value = 'true';
				} else {
					$single_settings_value = 'false';
				}
			} else {
				$single_settings_value = esc_html( $single_action_step_settings['value'] );
			}

			if( isset( $single_action_step_settings['field_type'] ) && $single_action_step_settings['field_type'] === 'checkbox' && ! empty( $single_action_step_settings['value'] ) ){
				$single_settings_value = '<svg width="20" height="20" aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="mr-0 ml-auto">
				<path fill="rgb(19, 208, 171)" d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z">
				</path>
				</svg>';
			}

			if( isset( $action_settings[ $single_action_step_settings['name'] ]['label'] ) ){
				$action_settings_display[ $single_action_step_settings['name'] ] = array(
					'label' => esc_html( $action_settings[ $single_action_step_settings['name'] ]['label'] ),
					'value' => $single_settings_value,
				);
			} else {
				$action_settings_display[ $single_action_step_settings['name'] ] = array(
					'label' => esc_html( $single_action_step_settings['name'] ),
					'value' => $single_settings_value,
				);
			}
		}

		//validate given action arguments
		$validated_body = WPWHPRO()->flows->validate_action_fields( $action_step_arguments, $flow->flow_payload );

		foreach( $validated_body as $argument_slug => $argument_value ){

			$action_argument_label = $argument_slug;
			if( 
				isset( $this->single_flow_action_buffer[ $action_endpoint_slug ] )
				&& isset( $this->single_flow_action_buffer[ $action_endpoint_slug ]['parameter'] )
				&& isset( $this->single_flow_action_buffer[ $action_endpoint_slug ]['parameter'][ $argument_slug ] )
				&& isset( $this->single_flow_action_buffer[ $action_endpoint_slug ]['parameter'][ $argument_slug ]['label'] )
			){
				$action_argument_label = '<strong>' . esc_html( $this->single_flow_action_buffer[ $action_endpoint_slug ]['parameter'][ $argument_slug ]['label'] ) . '</strong>: ' . $argument_slug;
			}

			if( is_array( $argument_value ) ){
				$argument_value = json_encode( $argument_value );
			} elseif ( is_bool( $argument_value ) ) {
				//Maybe validate bool values
				if( $argument_value ){
					$argument_value = 'true';
				} else {
					$argument_value = 'false';
				}
			} else {
				$argument_value = esc_html( $argument_value );
			}

			if( 
				isset( $action_step_settings[ $argument_slug ] )
				&& isset( $action_step_settings[ $argument_slug ]['field_type'] )
				&& $action_step_settings[ $argument_slug ]['field_type'] === 'checkbox'
				&& ( $argument_value === 'yes' || $argument_value === 1 )
			){
				$argument_value = '<svg width="20" height="20" aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="mr-0 ml-auto">
				<path fill="rgb(19, 208, 171)" d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z">
				</path>
				</svg>';
			}

			$action_argument_display[ $argument_slug ] = array(
				'label' => $action_argument_label,
				'value' => $argument_value,
			);

		}
		
		$flow_actions[ $flow_action_key ] = array(
			'integration_slug' => $flow_action['integration'],
			'action_endpoint_slug' => $action_endpoint_slug,
			'action_step_name' => $action_step_name,
			'action_step_arguments' => array_reverse( $action_argument_display ),
			'action_step_request_data' => array_reverse( $validated_body ),
			'action_step_response' => $action_step_response,
			'action_step_response_status' => $action_step_response_status,
			'action_step_response_msg' => $action_step_response_msg,
			'action_step_response_timestamp' => $action_step_response_timestamp,
			'action_step_is_scheduled' => $action_step_is_scheduled,
			'action_step_settings' => $action_settings_display,
			'action_step_conditionals' => $action_step_conditionals,
			'integration_name' => $this->single_flow_integration_buffer[ $flow_action['integration'] ]['name'],
			'integration_icon' => $this->single_flow_integration_buffer[ $flow_action['integration'] ]['icon'],
		);
	}
}

?>
<div class="wpwh-container">
	<div class="wpwh-title-area mb-4">
		<h2><?php echo __( 'Flow log:', 'wp-webhooks' ); ?> #<?php echo $flow_log_id; ?></h2>
		<p><?php echo sprintf( __( 'Learn more about the data this specific flow exectution. Just click on the trigger or one of the actions to learn more.', 'wp-webhooks' ), WPWHPRO()->settings->get_page_title() ); ?></p>
	</div>

	<div class="wpwh-flows">
		<div class="wpwh-flows__content-sidebar">
			<div class="wpwh-flows__content">
				<div class="wpwh-flows-steps">

					<div id="flowStepTrigger" class="wpwh-accordion wpwh-flows-step wpwh-flows-step--trigger m-0 is-collapsed">
						<div class="wpwh-accordion__item wpwh-flows-step__inner p-0">
							<div class="wpwh-flows-step__header" style="cursor:pointer;<?php echo $trigger_header_background_css; ?>" data-toggle="collapse" data-target="#wpwh_accordion_trigger" aria-expanded="true" aria-controls="wpwh_accordion_trigger">
								<div class="wpwh-flows-step__header-icon">
									<img src="<?php echo $trigger_integration_icon_url; ?>" alt="The logo of the <?php echo $trigger_integration_title; ?> integration.">
								</div>
								<div class="wpwh-flows-step__header-text">
									<small class="wpwh-flows-step__header-text">
										<span>1. Trigger</span>
									</small>
									<h3 class="wpwh-flows-step__title">
										<span><?php echo $trigger_step_name; ?></span>
									</h3>
									<small class="wpwh-flows-step__header-trigger">Trigger: <?php echo $trigger_endpoint_slug; ?></small>
								</div>
								<div class="wpwh-flows-step__header-actions ml-auto mr-0 d-flex">
									<?php echo $trigger_error_label; ?>
									<button type="button" class="wpwh-accordion__heading wpwh-btn wpwh-btn--link px-2 py-1 wpwh-btn--icon has-tooltip collapse" data-original-title="null" data-toggle="collapse" data-target="#wpwh_accordion_trigger" aria-expanded="true" aria-controls="wpwh_accordion_trigger">
										<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-up" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-chevron-up fa-w-14">
										<path fill="currentColor" d="M240.971 130.524l194.343 194.343c9.373 9.373 9.373 24.569 0 33.941l-22.667 22.667c-9.357 9.357-24.522 9.375-33.901.04L224 227.495 69.255 381.516c-9.379 9.335-24.544 9.317-33.901-.04l-22.667-22.667c-9.373-9.373-9.373-24.569 0-33.941L207.03 130.525c9.372-9.373 24.568-9.373 33.941-.001z" class="">
										</path>
										</svg>
									</button>
								</div>
							</div>
							<div class="wpwh-flows-step__body">
								<div id="wpwh_accordion_trigger" class="wpwh-accordion__content collapse wpwh-flows-accordions wpwh-flows-step__sub-steps p-0 show">

									<div class="wpwh-accordion">

										<div class="wpwh-accordion__item">
											<button class="wpwh-accordion__heading wpwh-flows-accordion__header justify-content-start wpwh-btn wpwh-btn--link wpwh-btn--block text-left p-3 collapsed" type="button" data-toggle="collapse" data-target="#wpwh_accordion_trigger_config" aria-expanded="true" aria-controls="wpwh_accordion_trigger_config">
												<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M7.41 8.59009L12 13.1701L16.59 8.59009L18 10.0001L12 16.0001L6 10.0001L7.41 8.59009Z" fill="#666666">
													</path>
												</svg>
												<span>Configuration</span>
											</button>
											<div id="wpwh_accordion_trigger_config" class="wpwh-accordion__content collapse" aria-labelledby="headingOne">
												<div class="wpwh-content">
													<?php if( ! empty( $trigger_settings_display ) ) : ?>
														<p>
														<?php echo __( 'Below you will find a list with all the settings that have been set for this specific execution of the flow.', 'wp-webhooks' ); ?>
														</p>
														<table class="wpwh-table wpwh-text-small mb-4">
															<thead>
																<tr>
																	<th>Setting</th>
																	<th>Value</th>
																</tr>
															</thead>
															<tbody>
																<?php foreach( $trigger_settings_display as $display_setting ) : ?>
																	<tr>
																		<td><?php echo $display_setting['label']; ?></td>
																		<td class="wpwh-w-50"><?php echo $display_setting['value']; ?></td>
																	</tr>
																<?php endforeach; ?>
															</tbody>
														</table>
													<?php else : ?>
														<?php echo __( 'No specific settings have been set for this trigger.', 'wp-webhooks' ); ?>
													<?php endif; ?>
												</div>
											</div>
										</div>

										<div class="wpwh-accordion__item">
											<button class="wpwh-accordion__heading wpwh-flows-accordion__header justify-content-start wpwh-btn wpwh-btn--link wpwh-btn--block text-left p-3 collapsed" type="button" data-toggle="collapse" data-target="#wpwh_accordion_trigger_data" aria-expanded="true" aria-controls="wpwh_accordion_trigger_data">
												<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M7.41 8.59009L12 13.1701L16.59 8.59009L18 10.0001L12 16.0001L6 10.0001L7.41 8.59009Z" fill="#666666">
													</path>
												</svg>
												<span>Sent data</span>
											</button>
											<div id="wpwh_accordion_trigger_data" class="wpwh-accordion__content collapse" aria-labelledby="headingOne">
												<div class="wpwh-content">
													<p>The data that was sent along once the trigger was fired and the flow executed.</p>
													<pre><?php echo htmlspecialchars( json_encode( $trigger_step_data, JSON_PRETTY_PRINT ) ); ?></pre>
												</div>
											</div>
										</div>

										<div class="wpwh-accordion__item">
											<button class="wpwh-accordion__heading wpwh-flows-accordion__header justify-content-start wpwh-btn wpwh-btn--link wpwh-btn--block text-left p-3 collapsed" type="button" data-toggle="collapse" data-target="#wpwh_accordion_trigger_conditional_data" aria-expanded="true" aria-controls="wpwh_accordion_trigger_conditional_data">
												<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M7.41 8.59009L12 13.1701L16.59 8.59009L18 10.0001L12 16.0001L6 10.0001L7.41 8.59009Z" fill="#666666">
													</path>
												</svg>
												<span>Conditionals</span>
											</button>
											<div id="wpwh_accordion_trigger_conditional_data" class="wpwh-accordion__content collapse" aria-labelledby="headingOne">
												<div class="wpwh-content">

												<?php if( ! empty( $trigger_conditionals ) ) : 

													$relation_label = isset( $trigger_conditionals['relation'] ) ? strtoupper( $trigger_conditionals['relation'] ) : __( 'Undefined', 'wp-webhooks' );
													$con_action_label = isset( $trigger_conditionals['flowAction'] ) ? strtoupper( $trigger_conditionals['flowAction'] ) : __( 'Undefined', 'wp-webhooks' );

												?>
													<div class="conditions-before-wrapper">
														<div class="wpwh-text-small d-flex align-items-center justify-content-between">
															<div class="wpwh-text-small d-flex align-items-center">
																<span>Run if <strong><?php echo esc_html( $relation_label ) ?></strong> of the following conditions are met.</span> 
															</div> 
														</div>
													</div>

													<div class="wpwh-flows-conditions mb-4">

														<?php if( $trigger_conditionals['conditions'] ) : ?>
															<?php foreach( $trigger_conditionals['conditions'] as $single_condition ):
																
																$operator_label = isset( $single_condition['condition_operator'] ) ? $single_condition['condition_operator']['value'] : '';
																$operator_label = isset( $conditional_labels['conditions'][ $operator_label ] ) ? $conditional_labels['conditions'][ $operator_label ] : $operator_label;
																
																if( isset( $single_condition['condition_input']['mappings'] ) ){
																	$condition_input = WPWHPRO()->flows->validate_mappings( $single_condition['condition_input']['value'], $single_condition['condition_input']['mappings'], $flow->flow_payload );
																} else {
																	$condition_input = $single_condition['condition_input']['value'];
																}
																
																if( is_bool( $condition_input ) ){
																	if( $condition_input ){
																		$condition_input = 'true';
																	} else {
																		$condition_input = 'false';
																	}
																}
																
																if( isset( $single_condition['condition_value']['mappings'] ) ){
																	$condition_value = WPWHPRO()->flows->validate_mappings( $single_condition['condition_value']['value'], $single_condition['condition_value']['mappings'], $flow->flow_payload );
																} else {
																	$condition_value = $single_condition['condition_value']['value'];
																}

																if( is_bool( $condition_value ) ){
																	if( $condition_value ){
																		$condition_value = 'true';
																	} else {
																		$condition_value = 'false';
																	}
																}

																//Rebuild the condition check for each condition to get the matched ones
																$temp_conditional = array(
																	'relation' => isset( $trigger_conditionals['relation'] ) ? $trigger_conditionals['relation'] : '',
																	'conditions' => array( $single_condition ),
																);
																$condition_matched = WPWHPRO()->flows->validate_trigger_conditions( $temp_conditional, $flow->flow_payload );

															?>
																<div class="wpwh-flows-condition" style="<?php echo ( $condition_matched ) ? 'color: #16524a;background-color: #d4ebe9;border-color: #c3e4e0;' : ''; ?>">
																	<div class="wpwh-flows-condition__inner">
																		<div class="wpwh-flows-condition__content">
																			<div class="wpwh-flows-condition__input">
																				<?php echo esc_html( $condition_input ); ?>
																			</div> 
																			<div class="wpwh-flows-condition__operator"> <?php echo esc_html( $operator_label ) ?> </div> 
																			<div class="wpwh-flows-condition__value">
																				<?php echo esc_html( $condition_value ); ?>
																			</div>
																		</div> 
																	</div>
																</div>
															<?php endforeach; ?>
														<?php else : ?>
															<span class="wpwh-text-small"><?php echo __( 'No conditions set.', 'wp-webhooks' ); ?></span>
														<?php endif; ?>
													</div>

													<?php else : ?>
														<span class="wpwh-text-small">No conditionals have been set.</span>
													<?php endif; ?>
												</div>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>

						<div class="wpwh-flows-step__footer d-flex align-items-center justify-content-center flex-column">
							<svg width="5" height="24" viewBox="0 0 5 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="my-3">
								<circle opacity="0.3" cx="2.49981" cy="2.32256" r="2.32256" fill="#393939">
								</circle> <circle opacity="0.3" cx="2.49981" cy="11.9998" r="2.32256" fill="#393939">
								</circle> <circle opacity="0.3" cx="2.49981" cy="21.6775" r="2.32256" fill="#393939">
								</circle>
							</svg>
						</div>
					</div>
					<?php $action_counter = 1; ?>
					<?php foreach( $flow_actions as $single_action_key => $single_action ) : 
					
						$action_counter++;
					
					?>

						<?php 
						
						$is_error = ( in_array( $single_action['action_step_response_status'], array( 'cancelled', 'skipped' ) ) ) ? true : false;
						$error_header_css = 'background-color:rgb(229 229 229) !important;';
						$is_scheduled = ! empty( $single_action['action_step_is_scheduled'] ) ? true : false;
						$timestamp = ! empty( $single_action['action_step_response_timestamp'] ) ? __( 'Executed on: ', 'wp-webhooks' ) .  WPWHPRO()->helpers->get_formatted_date( $single_action['action_step_response_timestamp'], 'F j, Y, g:i a' ) : '';
						$show_body = true;
						$error_type_label = ( ! empty( $single_action['action_step_response_msg'] ) ) ? $single_action['action_step_response_msg'] : 'Skipped: This step was skipped';

						if( $single_action['action_step_response_status'] === 'cancelled' ){
							$error_header_css = 'background-color: #ffc2c2 !important;';
						} elseif( $single_action['action_step_response_status'] === 'skipped' ){
							$show_body = false;
						}
							
						?>

						<div id="flowStepAction-<?php echo $single_action_key; ?>" class="wpwh-accordion wpwh-flows-step wpwh-flows-step--action m-0 is-collapse">
							<div class="wpwh-accordion__item wpwh-flows-step__inner p-0">
								<div class="wpwh-flows-step__header" style="<?php echo ( $is_error ) ? $error_header_css : ''; ?><?php echo ( $show_body ) ? 'cursor:pointer;' : ''; ?>" data-original-title="null" data-toggle="collapse" data-target="#wpwh_accordion_action-<?php echo $single_action_key; ?>" aria-expanded="true" aria-controls="wpwh_accordion_action-<?php echo $single_action_key; ?>">
									<div class="wpwh-flows-step__header-icon">
										<img src="<?php echo $single_action['integration_icon']; ?>" alt="The logo of the <?php echo $single_action['integration_name']; ?> integration.">
									</div>
									<div class="wpwh-flows-step__header-text">
										<small class="wpwh-flows-step__header-text">
											<span><?php echo $action_counter; ?>. Action</span>
										</small>
										<h3 class="wpwh-flows-step__title">
											<span><?php echo $single_action['action_step_name']; ?></span>
										</h3>
										<div class="wpwh-flows-step__header-action"><small>Action: <?php echo $single_action['action_endpoint_slug']; ?></small></div>
										<div class="wpwh-flows-step__header-action"><small><?php echo ( $is_scheduled ) ? 'Scheduled for: ' . WPWHPRO()->helpers->get_formatted_date( $is_scheduled, 'F j, Y, g:i a' ) : $timestamp; ?></small></div>
									</div>
									<div class="wpwh-flows-step__header-actions ml-auto mr-0 d-flex">
										<?php echo ( $is_error ) ? $error_type_label : ''; ?>
										<?php if( $show_body ) : ?>
										<button type="button" class="wpwh-accordion__heading wpwh-btn wpwh-btn--link px-2 py-1 wpwh-btn--icon has-tooltip collapsed" data-original-title="null" data-toggle="collapse" data-target="#wpwh_accordion_action-<?php echo $single_action_key; ?>" aria-expanded="true" aria-controls="wpwh_accordion_action-<?php echo $single_action_key; ?>">
											<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-up" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-chevron-up fa-w-14">
											<path fill="currentColor" d="M240.971 130.524l194.343 194.343c9.373 9.373 9.373 24.569 0 33.941l-22.667 22.667c-9.357 9.357-24.522 9.375-33.901.04L224 227.495 69.255 381.516c-9.379 9.335-24.544 9.317-33.901-.04l-22.667-22.667c-9.373-9.373-9.373-24.569 0-33.941L207.03 130.525c9.372-9.373 24.568-9.373 33.941-.001z" class="">
											</path>
											</svg>
										</button>
										<?php endif; ?>
									</div>
								</div>

								<?php if( $show_body ) : ?>
								<div class="wpwh-flows-step__body">
									<div id="wpwh_accordion_action-<?php echo $single_action_key; ?>" class="wpwh-accordion__content collapse wpwh-flows-accordions wpwh-flows-step__sub-steps p-0">

										<div class="wpwh-accordion">

											<div class="wpwh-accordion__item">
												<button class="wpwh-accordion__heading wpwh-flows-accordion__header justify-content-start wpwh-btn wpwh-btn--link wpwh-btn--block text-left p-3 collapsed" type="button" data-toggle="collapse" data-target="#wpwh_accordion_action_config-<?php echo $single_action_key; ?>" aria-expanded="true" aria-controls="wpwh_accordion_action_config-<?php echo $single_action_key; ?>">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M7.41 8.59009L12 13.1701L16.59 8.59009L18 10.0001L12 16.0001L6 10.0001L7.41 8.59009Z" fill="#666666">
														</path>
													</svg>
													<span>Configuration</span>
												</button>
												<div id="wpwh_accordion_action_config-<?php echo $single_action_key; ?>" class="wpwh-accordion__content collapse" aria-labelledby="headingOne">
													<div class="wpwh-content">
														<?php if( ! empty( $single_action['action_step_settings'] ) ) : ?>
															<p>
															<?php echo __( 'Below you will find a list with all the settings that have been set for this specific action within the flow.', 'wp-webhooks' ); ?>
															</p>
															<table class="wpwh-table wpwh-text-small mb-4">
																<thead>
																	<tr>
																		<th>Setting</th>
																		<th>Value</th>
																	</tr>
																</thead>
																<tbody>
																	<?php foreach( $single_action['action_step_settings'] as $display_setting ) : ?>
																		<tr>
																			<td><?php echo $display_setting['label']; ?></td>
																			<td class="wpwh-w-50"><?php echo $display_setting['value']; ?></td>
																		</tr>
																	<?php endforeach; ?>
																</tbody>
															</table>
														<?php else : ?>
															<?php echo __( 'No specific settings have been set for this action.', 'wp-webhooks' ); ?>
														<?php endif; ?>
													</div>
												</div>
											</div>

											<div class="wpwh-accordion__item">
												<button class="wpwh-accordion__heading wpwh-flows-accordion__header justify-content-start wpwh-btn wpwh-btn--link wpwh-btn--block text-left p-3 collapsed" type="button" data-toggle="collapse" data-target="#wpwh_accordion_action_arguments-<?php echo $single_action_key; ?>" aria-expanded="true" aria-controls="wpwh_accordion_action_arguments-<?php echo $single_action_key; ?>">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M7.41 8.59009L12 13.1701L16.59 8.59009L18 10.0001L12 16.0001L6 10.0001L7.41 8.59009Z" fill="#666666">
														</path>
													</svg>
													<span>Arguments/Payload</span>
												</button>
												<div id="wpwh_accordion_action_arguments-<?php echo $single_action_key; ?>" class="wpwh-accordion__content collapse" aria-labelledby="headingOne">
													<div class="wpwh-content">
														<?php if( ! empty( $single_action['action_step_arguments'] ) ) : ?>
															<p>
															<?php echo __( 'This section contains the payload along with all set and validated arguments for this specific action.', 'wp-webhooks' ); ?>
															</p>
															<table class="wpwh-table wpwh-text-small mb-4">
																<thead>
																	<tr>
																		<th>Argument</th>
																		<th>Value</th>
																	</tr>
																</thead>
																<tbody>
																	<?php foreach( $single_action['action_step_arguments'] as $display_setting ) : ?>
																		<tr>
																			<td><?php echo $display_setting['label']; ?></td>
																			<td class="wpwh-w-50"><?php echo $display_setting['value']; ?></td>
																		</tr>
																	<?php endforeach; ?>
																</tbody>
															</table>
															<p>
															<?php echo __( 'Below is the same data in it\'s raw format.', 'wp-webhooks' ); ?>
															</p>
															<pre><?php echo htmlspecialchars( json_encode( $single_action['action_step_request_data'], JSON_PRETTY_PRINT ) ); ?></pre>
														<?php else : ?>
															<?php echo __( 'No specific arguments have been set for this trigger.', 'wp-webhooks' ); ?>
														<?php endif; ?>
													</div>
												</div>
											</div>

											<div class="wpwh-accordion__item">
												<button class="wpwh-accordion__heading wpwh-flows-accordion__header justify-content-start wpwh-btn wpwh-btn--link wpwh-btn--block text-left p-3 collapsed" type="button" data-toggle="collapse" data-target="#wpwh_accordion_action_data-<?php echo $single_action_key; ?>" aria-expanded="true" aria-controls="wpwh_accordion_action_data-<?php echo $single_action_key; ?>">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M7.41 8.59009L12 13.1701L16.59 8.59009L18 10.0001L12 16.0001L6 10.0001L7.41 8.59009Z" fill="#666666">
														</path>
													</svg>
													<span>Response data</span>
												</button>
												<div id="wpwh_accordion_action_data-<?php echo $single_action_key; ?>" class="wpwh-accordion__content collapse" aria-labelledby="headingOne">
													<div class="wpwh-content">
														<p>The response data that was received after this action was fired.</p>
														<pre><?php echo htmlspecialchars( json_encode( $single_action['action_step_response'], JSON_PRETTY_PRINT ) ); ?></pre>
													</div>
												</div>
											</div>

											<div class="wpwh-accordion__item">
												<button class="wpwh-accordion__heading wpwh-flows-accordion__header justify-content-start wpwh-btn wpwh-btn--link wpwh-btn--block text-left p-3 collapsed" type="button" data-toggle="collapse" data-target="#wpwh_accordion_action_conditional_data-<?php echo $single_action_key; ?>" aria-expanded="true" aria-controls="wpwh_accordion_action_conditional_data-<?php echo $single_action_key; ?>">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M7.41 8.59009L12 13.1701L16.59 8.59009L18 10.0001L12 16.0001L6 10.0001L7.41 8.59009Z" fill="#666666">
														</path>
													</svg>
													<span>Conditionals</span>
												</button>
												<div id="wpwh_accordion_action_conditional_data-<?php echo $single_action_key; ?>" class="wpwh-accordion__content collapse" aria-labelledby="headingOne">
													<div class="wpwh-content">

													<?php if( ! empty( $single_action['action_step_conditionals']['conditionals'] ) ) : 

														$relation_label = isset( $single_action['action_step_conditionals']['conditionals']['relation'] ) ? strtoupper( $single_action['action_step_conditionals']['conditionals']['relation'] ) : __( 'Undefined', 'wp-webhooks' );
														$con_action_label = isset( $single_action['action_step_conditionals']['conditionals']['flowAction'] ) ? strtoupper( $single_action['action_step_conditionals']['conditionals']['flowAction'] ) : __( 'Undefined', 'wp-webhooks' );

													?>
														<div class="conditions-before-wrapper">
															<div class="wpwh-text-small d-flex align-items-center justify-content-between">
																<div class="wpwh-text-small d-flex align-items-center">
																	<span>Execute this step if <strong><?php echo esc_html( $relation_label ) ?></strong> of the following conditions are met, otherwise <strong><?php echo esc_html( $con_action_label ) ?></strong></span> 
																</div> 
															</div>
														</div>

														<div class="wpwh-flows-conditions mb-4">

															<?php if( $single_action['action_step_conditionals']['conditionals']['conditions'] ) : ?>
																<?php foreach( $single_action['action_step_conditionals']['conditionals']['conditions'] as $single_condition ):
																	
																	$operator_label = isset( $single_condition['condition_operator'] ) ? $single_condition['condition_operator']['value'] : '';
																	$operator_label = isset( $conditional_labels['conditions'][ $operator_label ] ) ? $conditional_labels['conditions'][ $operator_label ] : $operator_label;
																	
																	if( isset( $single_condition['condition_input']['mappings'] ) ){
																		$condition_input = WPWHPRO()->flows->validate_mappings( $single_condition['condition_input']['value'], $single_condition['condition_input']['mappings'], $flow->flow_payload );
																	} else {
																		$condition_input = $single_condition['condition_input']['value'];
																	}
																	
																	if( is_bool( $condition_input ) ){
																		if( $condition_input ){
																			$condition_input = 'true';
																		} else {
																			$condition_input = 'false';
																		}
																	}
																	
																	if( isset( $single_condition['condition_value']['mappings'] ) ){
																		$condition_value = WPWHPRO()->flows->validate_mappings( $single_condition['condition_value']['value'], $single_condition['condition_value']['mappings'], $flow->flow_payload );
																	} else {
																		$condition_value = $single_condition['condition_value']['value'];
																	}

																	if( is_bool( $condition_value ) ){
																		if( $condition_value ){
																			$condition_value = 'true';
																		} else {
																			$condition_value = 'false';
																		}
																	}
																	
																?>
																	<div class="wpwh-flows-condition">
																		<div class="wpwh-flows-condition__inner" style="">
																			<div class="wpwh-flows-condition__content">
																				<div class="wpwh-flows-condition__input">
																					<?php echo esc_html( $condition_input ); ?>
																				</div> 
																				<div class="wpwh-flows-condition__operator"> <?php echo esc_html( $operator_label ) ?> </div> 
																				<div class="wpwh-flows-condition__value">
																					<?php echo esc_html( $condition_value ); ?>
																				</div>
																			</div> 
																		</div>
																	</div>
																<?php endforeach; ?>
															<?php else : ?>
																<span class="wpwh-text-small"><?php echo __( 'No pre-conditions set.', 'wp-webhooks' ); ?></span>
															<?php endif; ?>
														</div>

														<?php else : ?>
															<span class="wpwh-text-small">No pre-conditionals have been set.</span>
														<?php endif; ?>

														<hr>

														<?php if( ! empty( $single_action['action_step_conditionals']['conditionals_after'] ) ) : 
															
															$relation_label = isset( $single_action['action_step_conditionals']['conditionals_after']['relation'] ) ? strtoupper( $single_action['action_step_conditionals']['conditionals']['relation'] ) : __( 'Undefined', 'wp-webhooks' );
														
														?>

															<div class="conditions-before-wrapper mt-4">
																<div class="wpwh-text-small d-flex align-items-center justify-content-between">
																	<div class="wpwh-text-small d-flex align-items-center">
																		<span>Continue flow execution only if <strong><?php echo esc_html( $relation_label ); ?></strong> of the conditions are met.</span>
																	</div> 
																</div>
															</div>

															<div class="wpwh-flows-conditions mb-4">
																<?php if( $single_action['action_step_conditionals']['conditionals_after']['conditions'] ) : ?>
																	<?php foreach( $single_action['action_step_conditionals']['conditionals_after']['conditions'] as $single_condition ):
																		
																		$operator_label = isset( $single_condition['condition_operator'] ) ? $single_condition['condition_operator']['value'] : '';
																		$operator_label = isset( $conditional_labels['conditions'][ $operator_label ] ) ? $conditional_labels['conditions'][ $operator_label ] : $operator_label;
																		
																		if( isset( $single_condition['condition_input']['mappings'] ) ){
																			$condition_input = WPWHPRO()->flows->validate_mappings( $single_condition['condition_input']['value'], $single_condition['condition_input']['mappings'], $flow->flow_payload );
																		} else {
																			$condition_input = $single_condition['condition_input']['value'];
																		}
																		
																		if( is_bool( $condition_input ) ){
																			if( $condition_input ){
																				$condition_input = 'true';
																			} else {
																				$condition_input = 'false';
																			}
																		}
																		
																		if( isset( $single_condition['condition_value']['mappings'] ) ){
																			$condition_value = WPWHPRO()->flows->validate_mappings( $single_condition['condition_value']['value'], $single_condition['condition_value']['mappings'], $flow->flow_payload );
																		} else {
																			$condition_value = $single_condition['condition_value']['value'];
																		}

																		if( is_bool( $condition_value ) ){
																			if( $condition_value ){
																				$condition_value = 'true';
																			} else {
																				$condition_value = 'false';
																			}
																		}
																		
																	?>
																	<div class="wpwh-flows-condition">
																		<div class="wpwh-flows-condition__inner" style="">
																			<div class="wpwh-flows-condition__content">
																				<div class="wpwh-flows-condition__input">
																					<?php echo esc_html( $condition_input ); ?>
																				</div> 
																				<div class="wpwh-flows-condition__operator"> <?php echo esc_html( $operator_label ) ?> </div> 
																				<div class="wpwh-flows-condition__value">
																					<?php echo esc_html( $condition_value ); ?>
																				</div>
																			</div> 
																		</div>
																	</div>

																	<?php endforeach; ?>
																<?php else : ?>
																	<span class="wpwh-text-small"><?php echo __( 'No post-conditions set.', 'wp-webhooks' ); ?></span>
																<?php endif; ?>
															</div>

														<?php else : ?>
															<span class="wpwh-text-small">No post-conditionals have been set.</span>
														<?php endif; ?>
													</div>
												</div>
											</div>

										</div>
									</div>
								</div>
								<?php endif; ?>
							</div>

							<div class="wpwh-flows-step__footer d-flex align-items-center justify-content-center flex-column">
								<svg width="5" height="24" viewBox="0 0 5 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="my-3">
									<circle opacity="0.3" cx="2.49981" cy="2.32256" r="2.32256" fill="#393939">
									</circle> <circle opacity="0.3" cx="2.49981" cy="11.9998" r="2.32256" fill="#393939">
									</circle> <circle opacity="0.3" cx="2.49981" cy="21.6775" r="2.32256" fill="#393939">
									</circle>
								</svg>
							</div>
						</div>

					<?php endforeach; ?>
					
				</div>
			</div>
			
			<aside class="wpwh-flows__sidebar p-0">
				<div class="wpwh-flows-widget">
					<div class="wpwh-publish-box">
						<p class="d-flex align-items-center">
							<span class="wpwh-publish-box__icon">
								<svg width="7" height="13" viewBox="0 0 7 13" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path opacity="0.6" d="M2.625 7.92969C2.88281 7.97656 3.14062 8 3.375 8C3.60938 8 3.84375 7.97656 4.125 7.92969V11.6094L3.60938 12.3828C3.53906 12.4766 3.46875 12.5 3.375 12.5C3.28125 12.5 3.1875 12.4766 3.14062 12.3828L2.625 11.6094V7.92969ZM3.375 0.5C3.98438 0.5 4.54688 0.664062 5.0625 0.96875C5.57812 1.27344 5.97656 1.67188 6.28125 2.1875C6.58594 2.70312 6.75 3.26562 6.75 3.875C6.75 4.48438 6.58594 5.04688 6.28125 5.5625C5.97656 6.07812 5.57812 6.5 5.0625 6.80469C4.54688 7.10938 3.98438 7.25 3.375 7.25C2.76562 7.25 2.20312 7.10938 1.6875 6.80469C1.17188 6.5 0.75 6.07812 0.445312 5.5625C0.140625 5.04688 0 4.48438 0 3.875C0 3.26562 0.140625 2.70312 0.445312 2.1875C0.75 1.67188 1.17188 1.27344 1.6875 0.96875C2.20312 0.664062 2.76562 0.5 3.375 0.5ZM3.375 2.28125C3.44531 2.28125 3.51562 2.25781 3.5625 2.21094C3.60938 2.16406 3.65625 2.09375 3.65625 2C3.65625 1.92969 3.60938 1.85938 3.5625 1.8125C3.51562 1.76562 3.44531 1.71875 3.375 1.71875C2.76562 1.71875 2.27344 1.92969 1.85156 2.35156C1.42969 2.77344 1.21875 3.28906 1.21875 3.875C1.21875 3.96875 1.24219 4.03906 1.28906 4.08594C1.33594 4.13281 1.40625 4.15625 1.5 4.15625C1.57031 4.15625 1.64062 4.13281 1.6875 4.08594C1.73438 4.03906 1.78125 3.96875 1.78125 3.875C1.78125 3.45312 1.92188 3.07812 2.25 2.75C2.55469 2.44531 2.92969 2.28125 3.375 2.28125Z" fill="#393939">
									</path>
								</svg>
							</span>
							<label for="wpwhFlowStatus" class="wpwh-publish-box__title">Status:</label> <strong><?php echo $flow_status ?></strong>
						</p>
						
						<p class="d-flex align-items-center">
							<span class="wpwh-publish-box__icon">
								<svg width="11" height="13" viewBox="0 0 11 13" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path opacity="0.6" d="M0.28125 5C0.1875 5 0.117188 5.04688 0.0703125 5.09375C0.0234375 5.14062 0 5.21094 0 5.28125V11.375C0 11.7031 0.09375 11.9609 0.328125 12.1719C0.539062 12.4062 0.796875 12.5 1.125 12.5H9.375C9.67969 12.5 9.9375 12.4062 10.1719 12.1719C10.3828 11.9609 10.5 11.7031 10.5 11.375V5.28125C10.5 5.21094 10.4531 5.14062 10.4062 5.09375C10.3594 5.04688 10.2891 5 10.2188 5H0.28125ZM10.5 3.96875C10.5 4.0625 10.4531 4.13281 10.4062 4.17969C10.3594 4.22656 10.2891 4.25 10.2188 4.25H0.28125C0.1875 4.25 0.117188 4.22656 0.0703125 4.17969C0.0234375 4.13281 0 4.0625 0 3.96875V3.125C0 2.82031 0.09375 2.5625 0.328125 2.32812C0.539062 2.11719 0.796875 2 1.125 2H2.25V0.78125C2.25 0.710938 2.27344 0.640625 2.32031 0.59375C2.36719 0.546875 2.4375 0.5 2.53125 0.5H3.46875C3.53906 0.5 3.60938 0.546875 3.65625 0.59375C3.70312 0.640625 3.75 0.710938 3.75 0.78125V2H6.75V0.78125C6.75 0.710938 6.77344 0.640625 6.82031 0.59375C6.86719 0.546875 6.9375 0.5 7.03125 0.5H7.96875C8.03906 0.5 8.10938 0.546875 8.15625 0.59375C8.20312 0.640625 8.25 0.710938 8.25 0.78125V2H9.375C9.67969 2 9.9375 2.11719 10.1719 2.32812C10.3828 2.5625 10.5 2.82031 10.5 3.125V3.96875Z" fill="#393939">
								</path>
								</svg>
							</span>
							<span class="wpwh-publish-box__title">Executed on:</span> <strong><?php echo $flow_date; ?></strong>
						</p>
					</div>
				</div>
				
				<div class="wpwh-flows-widget">
					<h4 class="wpwh-flows-widget__title">Available steps:</h4>
					<div class="wpwh-selected-steps">
						<div class="wpwh-selected-steps__inner">
							<a href="#flowStepTrigger" class="wpwh-selected-step wpwh-selected-step">
								<span class="wpwh-selected-step__icon">
									<img src="<?php echo $trigger_integration_icon_url; ?>" alt="The logo of the <?php echo $trigger_integration_title; ?> integration.">
								</span>
								<span class="wpwh-selected-step__text">1. <?php echo $trigger_integration_title; ?></span>
							</a>
							<?php $action_counter = 1; ?>
							<?php foreach( $flow_actions as $single_action_key => $single_action ) : 
							
								$action_counter++;
							
							?>

								<a href="#flowStepAction-<?php echo $single_action_key; ?>" class="wpwh-selected-step wpwh-selected-step">
									<span class="wpwh-selected-step__icon">
										<img src="<?php echo $single_action['integration_icon']; ?>" alt="The logo of the <?php echo $single_action['integration_name']; ?> integration.">
									</span>
									<span class="wpwh-selected-step__text"><?php echo $action_counter; ?>. <?php echo $single_action['integration_name']; ?></span>
								</a>

							<?php endforeach; ?>

						</div>
					</div>
				</div>
			</aside>
		</div>
	</div>

</div>