<?php
$triggers = WPWHPRO()->webhook->get_triggers();
$triggers_data = WPWHPRO()->webhook->get_hooks( 'trigger' );
$current_url = WPWHPRO()->helpers->get_current_url( false, true );
$current_url_full = WPWHPRO()->helpers->get_current_url( true, true );
$trigger_nonce_data = WPWHPRO()->settings->get_trigger_nonce();
$clear_form_url = WPWHPRO()->helpers->get_current_url( true, true );
$data_mapping_templates = WPWHPRO()->data_mapping->get_data_mapping();

if( ! empty( $triggers ) ){
    usort($triggers, function($a, $b) {
        $aname = isset( $a['name'] ) ? $a['name'] : '';
        $bname = isset( $b['name'] ) ? $b['name'] : '';
        return strcmp($aname, $bname);
    });
}

if( isset( $_POST['wpwh-add-webhook-url'] ) ){
    if ( check_admin_referer( $trigger_nonce_data['action'], $trigger_nonce_data['arg'] ) ) {

		if( WPWHPRO()->helpers->current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-send-data-add-url' ), 'wpwhpro-page-send-data-add-url' ) ){
			$percentage_escape		= '{irnksescprcntg}';
			$webhook_url            = $_POST['wpwh-add-webhook-url'];
			$webhook_url 			= str_replace( '%', $percentage_escape, $webhook_url );
			$webhook_url 			= sanitize_text_field( $webhook_url );
			$webhook_url 			= str_replace( $percentage_escape, '%', $webhook_url );

			if( $webhook_url !== 'wpwhflow' ){
				$webhook_slug           = isset( $_POST['wpwh-add-webhook-name'] ) ? sanitize_title( $_POST['wpwh-add-webhook-name'] ) : '';
				$webhook_group          = isset( $_POST['wpwh-add-webhook-group'] ) ? sanitize_text_field( $_POST['wpwh-add-webhook-group'] ) : '';
				$webhook_integration    = isset( $_POST['wpwh-add-webhook-integration'] ) ? sanitize_title( $_POST['wpwh-add-webhook-integration'] ) : '';
				$webhooks               = WPWHPRO()->webhook->get_hooks( 'trigger', $webhook_group );

				if( ! empty( $webhook_slug ) ){
					$new_webhook = $webhook_slug;
				} else {
					$new_webhook = strtotime( date( 'Y-n-d H:i:s' ) ) . 999 . rand( 10, 9999 );
				}

				if( ! isset( $webhooks[ $new_webhook ] ) ){
					$check = WPWHPRO()->webhook->create( $new_webhook, 'trigger', array( 'group' => $webhook_group, 'webhook_url' => $webhook_url, 'integration' => $webhook_integration ) );

					if( $check ){
						echo WPWHPRO()->helpers->create_admin_notice( 'The webhook URL has been added.', 'success', true );
					} else {
						echo WPWHPRO()->helpers->create_admin_notice( 'Error while adding the webhook URL.', 'warning', true );
					}

					//reload data
					$triggers = WPWHPRO()->webhook->get_triggers();
					$triggers_data = WPWHPRO()->webhook->get_hooks( 'trigger' );
				}
			} else {
				echo WPWHPRO()->helpers->create_admin_notice( 'This webhook URL is reserved for internal use only.', 'warning', true );
			}
		}

	}
}

//Sort webhooks
$grouped_triggers = array();
foreach( $triggers as $identkey => $webhook_trigger ){
    $group = 'ungrouped';

    if( isset( $webhook_trigger['integration'] ) ){
        $group = $webhook_trigger['integration'];
    }

    if( ! isset( $grouped_triggers[ $group ] ) ){
        $grouped_triggers[ $group ] = array(
            $identkey => $webhook_trigger
        );
    } else {
        $grouped_triggers[ $group ][ $identkey ] = $webhook_trigger;
    }
}

//add ungroped elements at the end
if( isset( $grouped_triggers['ungrouped'] ) ){
	$ungrouped_triggers = $grouped_triggers['ungrouped'];
	unset( $grouped_triggers['ungrouped'] );
	$grouped_triggers['ungrouped'] = $ungrouped_triggers;
}

$active_trigger = isset( $_GET['wpwh-trigger'] ) ? sanitize_title( $_GET['wpwh-trigger'] ) : 'create_user';

?>
<?php add_ThickBox(); ?>

<div class="wpwh-container">
  <div class="wpwh-title-area mb-5">
    <h1><?php echo __( 'Send Data (Triggers)', 'wp-webhooks' ); ?></h1>
    <p>
		<?php if( WPWHPRO()->whitelabel->is_active() && ! empty( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_send_data' ) ) ) : ?>
			<?php echo __( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_send_data' ), 'wp-webhooks' ); ?>
		<?php else : ?>
			<?php echo sprintf( __( 'Triggers allow you to send data to a specific URL once an event happens. To use one, you need to specify a URL for your chosen event that should be triggered to send the available data. For more information on each of the available triggers, you can select the integration and then your trigger on <a class="text-secondary" title="Go to our product documentation" target="_blank" href="%2$s">our website</a>.', 'wp-webhooks' ), '<strong>' . $this->page_title . '</strong>', 'https://wp-webhooks.com/integrations/'); ?>
		<?php endif; ?>
	</p>
  </div>

  <div class="wpwh-triggers" data-wpwh-trigger="">

    <div class="wpwh-triggers__sidebar">

      <div class="wpwh-trigger-search wpwh-box">
        <div class="wpwh-trigger-search__search flex-wrap">
          <input type="search" data-wpwh-trigger-search class="wpwh-form-input" name="search-trigger" id="search-trigger" placeholder="<?php echo __( 'Search triggers', 'wp-webhooks' ); ?>">
		  <div class="wpwh-text-small mt-2"><?php echo __( 'Type <strong>wpwhactive</strong> to see all of the triggers that have URLs.', 'wp-webhooks' ); ?></div>
        </div>
				<?php if( ! empty( $triggers ) ) : ?>
					<div class="wpwh-trigger-search__items">
						<?php foreach( $grouped_triggers as $group => $single_triggers ) :

						if( $group === 'ungrouped' ){
							echo '<a class="wpwh-trigger-search__item wpwh-trigger-search__item--group">' . __( 'Others', 'wp-webhooks' ) . '</a>';
						} else {
							$group_details = WPWHPRO()->integrations->get_details( $group );
							if( is_array( $group_details ) && isset( $group_details['name'] ) && ! empty( $group_details['name'] ) ){
								echo '<a class="wpwh-trigger-search__item wpwh-trigger-search__item--group wpwh-trigger-search__item--group-icon">';

								if( isset( $group_details['icon'] ) && ! empty( $group_details['icon'] ) ){
									echo '<img class="wpwh-trigger-search__item-image" src="' . $group_details['icon'] . '" />';
								}

								echo '<span class="wpwh-trigger-search__item-name">' . $group_details['name'] . '</span>';
								echo '</a>';
							}
						}

						?>
							<?php foreach( $single_triggers as $identkey => $trigger ) :
								$trigger_name = !empty( $trigger['name'] ) ? $trigger['name'] : $trigger['trigger'];
								$webhook_name = !empty( $trigger['trigger'] ) ? $trigger['trigger'] : '';
								$has_triggers_class = ( isset( $triggers_data[ $trigger['trigger'] ] ) && ! empty( $triggers_data[ $trigger['trigger'] ] ) ) ? ' hastriggers' : '';

								$is_active = $webhook_name === $active_trigger;

								?>
								<a href="#webhook-<?php echo $webhook_name; ?>" data-wpwh-trigger-id="<?php echo $webhook_name; ?>" class="wpwh-trigger-search__item<?php echo $is_active ? ' wpwh-trigger-search__item--active' : ''; ?><?php echo $has_triggers_class; ?>"><?php echo $trigger_name; ?></a>
							<?php endforeach; ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
      </div>

    </div>

    <div class="wpwh-triggers__content" data-wpwh-trigger-content="">

		<?php if( ! empty( $triggers ) ) : ?>
				<div class="wpwh-trigger-items">
					<?php foreach( $triggers as $identkey => $trigger ) :

						$trigger_name = !empty( $trigger['name'] ) ? $trigger['name'] : $trigger['trigger'];
						$webhook_name = !empty( $trigger['trigger'] ) ? $trigger['trigger'] : '';
						$trigger_integration = isset( $trigger['integration'] ) ? $trigger['integration'] : '';
						$trigger_details = WPWHPRO()->integrations->get_details( $trigger_integration );

						$trigger_integration_icon = '';
						if( isset( $trigger_details['icon'] ) && ! empty( $trigger_details['icon'] ) ){
							$trigger_integration_icon = esc_html( $trigger_details['icon'] );
						}

						$trigger_integration_name = '';
						if( isset( $trigger_details['name'] ) && ! empty( $trigger_details['name'] ) ){
							$trigger_integration_name = esc_html( $trigger_details['name'] );
						}

						$is_active = $webhook_name === $active_trigger;

						?>
						<div class="wpwh-trigger-item<?php echo $is_active ? ' wpwh-trigger-item--active' : ''; ?> wpwh-table-container" id="webhook-<?php echo $webhook_name; ?>" <?php echo ! $is_active ? 'style="display: none;"' : ''; ?>>
							<div class="wpwh-table-header">
								<div class="mb-2 d-flex align-items-center justify-content-between">
									<h2 class="d-flex align-items-end" data-wpwh-trigger-name>
										<?php if( ! empty( $trigger_integration_icon ) ) : ?>
											<a title="<?php echo sprintf( __( 'Visit the %s integration', 'wp-webhooks' ), $trigger_integration_name ); ?>" target="_blank" href="<?php echo WPWHPRO()->helpers->get_wp_webhooks_endpoint_url( $trigger_integration ); ?>">
												<img class="wpwh-trigger-search__item-image mb-1" style="height:100%;max-height:40px;width:40px;max-width:40px;" src="<?php echo $trigger_integration_icon; ?>" />
											</a>
										<?php endif; ?>
										<div class="d-flex flex-column">
											<a title="<?php echo sprintf( __( 'Visit the %s integration', 'wp-webhooks' ), $trigger_integration_name ); ?>" target="_blank" href="<?php echo WPWHPRO()->helpers->get_wp_webhooks_endpoint_url( $trigger_integration ); ?>">
												<span class="wpwh-trigger-integration-name wpwh-text-small"><?php echo $trigger_integration_name; ?></span>
											</a>

											<a class="d-flex" title="<?php echo sprintf( __( 'Visit the %s trigger', 'wp-webhooks' ), $webhook_name ); ?>" target="_blank" href="<?php echo WPWHPRO()->helpers->get_wp_webhooks_endpoint_url( $trigger_integration, $webhook_name ); ?>">
												<span class="mr-2"><?php echo $trigger_name; ?></span>
												<div style="width:17px;height:17px;">
													<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="info-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-info-circle fa-w-16"><path fill="currentColor" d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 110c23.196 0 42 18.804 42 42s-18.804 42-42 42-42-18.804-42-42 18.804-42 42-42zm56 254c0 6.627-5.373 12-12 12h-88c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h12v-64h-12c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h64c6.627 0 12 5.373 12 12v100h12c6.627 0 12 5.373 12 12v24z" class=""></path></svg>
												</div>
											</a>

										</div>
									</h2>
									<div class="wpwh-trigger-webhook-name wpwh-text-small"><?php echo $webhook_name; ?></div>
								</div>
								<div class="wpwh-content mb-4">
									<?php echo $trigger['short_description']; ?>
								</div>
								<div class="d-flex align-items-center justify-content-end">
									<button class="wpwh-btn wpwh-btn--sm wpwh-btn--secondary" title="<?php echo __( 'Add Webhook URL', 'wp-webhooks' ); ?>" data-toggle="modal" data-target="#wpwhAddWebhookModal-<?php echo $identkey; ?>">
										<?php echo __( 'Add Webhook URL', 'wp-webhooks' ); ?>
									</button>
								</div>
							</div>
							<table class="wpwh-table wpwh-table--sm wpwh-text-small">
								<thead>
									<tr>
										<th></th>
										<th><?php echo __( 'Webhook Name', 'wp-webhooks' ); ?></th>
										<th><?php echo __( 'Webhook URL', 'wp-webhooks' ); ?></th>
										<th class="text-center pr-3"><?php echo __( 'Action', 'wp-webhooks' ); ?></th>
									</tr>
								</thead>
								<tbody>

									<?php $all_triggers = WPWHPRO()->webhook->get_hooks( 'trigger', $trigger['trigger'] ); ?>
									<?php foreach( $all_triggers as $webhook => $webhook_data ) : ?>
										<?php if( ! is_array( $webhook_data ) || empty( $webhook_data ) ) { continue; } ?>
										<?php if( ! current_user_can( apply_filters( 'wpwhpro/admin/settings/webhook/page_capability', WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-triggers' ), $webhook, $trigger['trigger'] ) ) ) { continue; } ?>
										<?php
											$is_flow = ( $webhook_data['webhook_url'] === 'wpwhflow' ) ? true : false;
											$flow_id = null;
											if( $is_flow ){
												$flow_id = intval( str_replace( 'wpwh-flow-', '', $webhook ) );
											}
											$flow = null;
											if( $is_flow && is_numeric( $flow_id ) ){
												$flow = WPWHPRO()->flows->get_flows( array( 'template' => $flow_id ) );
											}

											$status = 'active';
											$status_name = 'Deactivate';
											if( isset( $webhook_data['status'] ) && $webhook_data['status'] == 'inactive' ){
												$status = 'inactive';
												$status_name = 'Activate';
											}
										?>
										<tr id="webhook-trigger-<?php echo $trigger['trigger']; ?>-<?php echo $webhook; ?>">
											<td class="align-middle wpwh-status-cell">
												<button
													data-wpwh-event="deactivate"
													data-wpwh-event-type="send"
													data-wpwh-event-element="#webhook-trigger-<?php echo $trigger['trigger']; ?>-<?php echo $webhook; ?>"

													data-wpwh-webhook-status="<?php echo $status; ?>"
													data-wpwh-webhook-group="<?php echo $trigger['trigger']; ?>"
													data-wpwh-webhook-slug="<?php echo $webhook; ?>"

													class="wpwh-status wpwh-status--<?php echo $status; ?>"
												>
													<span><?php echo __( $status, 'wp-webhooks' ); ?></span>
												</button>
											</td>
											<td>
												<div class="wpwh-copy-wrapper" data-wpwh-tippy-content="<?php echo __( 'copied!', 'wp-webhooks' ); ?>"><input class="wpwh-form-input w-100" type='text' name='ironikus_wp_webhooks_pro_webhook_name' value="<?php echo $webhook; ?>" readonly /></div>
											</td>
											<td class="wpwh-w-50">
												<?php if( $is_flow ) :

													$flow_name = $flow_id;
													if( is_object( $flow ) && isset( $flow->flow_title ) ){
														$flow_name = $flow->flow_title;
													}

													?>
													<div class="wpwh-copy-wrapper" data-wpwh-tippy-content="<?php echo __( 'copied!', 'wp-webhooks' ); ?>">
														<input class="wpwh-form-input w-100" type='text' name='ironikus_wp_webhooks_pro_webhook_url' value="<?php echo sprintf( __( 'Connected flow: %1$s', 'wp-webhooks' ), $flow_name ); ?>" readonly />
													</div>
												<?php else : ?>
													<div class="wpwh-copy-wrapper" data-wpwh-tippy-content="<?php echo __( 'copied!', 'wp-webhooks' ); ?>"><input class="wpwh-form-input w-100" type='text' name='ironikus_wp_webhooks_pro_webhook_url' value="<?php echo $webhook_data['webhook_url']; ?>" readonly /></div>
												<?php endif; ?>
											</td>
											<td class="align-middle text-center wpwh-table__action pr-3">
												<?php if( $is_flow ) : ?>
													<div class="d-flex align-items-center justify-content-center">
														<a
															class="wpwh-btn wpwh-btn--link py-1 px-2 wpwh-btn--icon"
															href="<?php echo WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'wpwhprovrs' => 'flows', 'flow_id' => $flow_id, ) ) ); ?>"
															data-tippy=""
															data-tippy-content="<?php echo __( 'Edit flow', 'wp-webhooks' ); ?>"
															data-tippy=""
															data-tippy-content="<?php echo __( 'Edit', 'wp-webhooks' ); ?>"
														>
															<img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/cog.svg'; ?>" alt="<?php echo __( 'Edit flow', 'wp-webhooks' ); ?>">
														</a>
														<a
															class="wpwh-btn wpwh-btn--link py-1 px-2 wpwh-btn--icon"
															href="#"

															data-wpwh-event="demo"
															data-wpwh-event-type="send"

															data-wpwh-demo-data-callback="<?php echo isset( $trigger['callback'] ) ? $trigger['callback'] : ''; ?>"
															data-wpwh-webhook="<?php echo $webhook; ?>"
															data-wpwh-group="<?php echo $trigger['trigger']; ?>"

															data-tippy=""
															data-tippy-content="<?php echo __( 'Triggers the flow with the given, static data.', 'wp-webhooks' ); ?>"
														>
															<img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/send.svg'; ?>" alt="Settings">
														</a>
													</div>
												<?php else : ?>
													<div class="d-flex align-items-center justify-content-center">
														<a
															class="wpwh-btn wpwh-btn--link py-1 px-2 wpwh-btn--icon"
															href="#"

															data-wpwh-event="demo"
															data-wpwh-event-type="send"

															data-wpwh-demo-data-callback="<?php echo isset( $trigger['callback'] ) ? $trigger['callback'] : ''; ?>"
															data-wpwh-webhook="<?php echo $webhook; ?>"
															data-wpwh-group="<?php echo $trigger['trigger']; ?>"
															data-tippy=""
															data-tippy-content="<?php echo __( 'Send Demo', 'wp-webhooks' ); ?>"
														>
															<img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/send.svg'; ?>" alt="Settings">
														</a>
														<a
															class="wpwh-btn wpwh-btn--link py-1 px-2 wpwh-btn--icon"
															href="#"
															data-toggle="modal"
															data-target="#wpwhTriggerSettingsModal-<?php echo $identkey; ?>-<?php echo $webhook; ?>"
															data-tippy=""
															data-tippy-content="<?php echo __( 'Settings', 'wp-webhooks' ); ?>"
														>
															<img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/cog.svg'; ?>" alt="Settings">
														</a>
														<a
															class="wpwh-btn wpwh-btn--link py-1 px-2 wpwh-btn--icon"
															href="#"

															data-wpwh-event="delete"
															data-wpwh-event-type="send"
															data-wpwh-event-element="#webhook-trigger-<?php echo $trigger['trigger']; ?>-<?php echo $webhook; ?>"

															data-wpwh-delete="<?php echo $webhook; ?>"
															data-wpwh-group="<?php echo $trigger['trigger']; ?>"
															data-tippy=""
															data-tippy-content="<?php echo __( 'Delete', 'wp-webhooks' ); ?>"
														>
															<img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/delete.svg'; ?>" alt="Delete">
														</a>
													</div>
												<?php endif; ?>
											</td>
										</tr>

									<?php endforeach; ?>

								</tbody>
							</table>

							<div class="wpwh-accordion" id="wpwh_accordion_<?php echo $identkey; ?>">

								<div class="wpwh-accordion__item">
									<button class="wpwh-accordion__heading wpwh-btn wpwh-btn--link wpwh-btn--block text-left collapsed" type="button" data-toggle="collapse" data-target="#wpwh_accordion_arguments_<?php echo $identkey; ?>" aria-expanded="true" aria-controls="wpwh_accordion_arguments_<?php echo $identkey; ?>">
										<span><?php echo __( 'Outgoing data', 'wpwhpro-page-triggers'); ?></span>
										<span class="text-secondary">
											<?php echo __( 'Expand', 'wpwhpro-page-triggers'); ?>
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="9" fill="none" class="ml-1">
												<defs />
												<path stroke="#F1592A" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l7 7 7-7" />
											</svg>
										</span>
									</button>
									<div id="wpwh_accordion_arguments_<?php echo $identkey; ?>" class="wpwh-accordion__content collapse" aria-labelledby="headingOne">
										<table class="wpwh-table wpwh-text-small mb-4">
											<thead>
												<tr>
													<th><?php echo __( 'Argument', 'wp-webhooks' ); ?></th>
													<th><?php echo __( 'Description', 'wp-webhooks' ); ?></th>
												</tr>
											</thead>
											<tbody>
												<?php if( ! empty( $trigger['parameter'] ) ) : ?>
													<?php foreach( $trigger['parameter'] as $param => $param_data ) : ?>
														<tr>
															<td><?php echo $param;; ?></td>
															<td class="wpwh-w-50"><?php echo $param_data['short_description']; ?></td>
														</tr>
													<?php endforeach; ?>
												<?php else : ?>
													<tr>
														<td>-</td>
														<td class="wpwh-w-50"><?php echo __( 'No default values given', 'wp-webhooks' ); ?></td>
													</tr>
												<?php endif; ?>
											</tbody>
										</table>

										<?php if( ! empty( $trigger['returns_code'] ) ) :

											$display_code = $trigger['returns_code'];
											if( is_array( $trigger['returns_code'] ) ){
												$display_code = htmlspecialchars( json_encode( $display_code, JSON_PRETTY_PRINT ) );
											}

											?>
											<p>
												<?php echo __( 'Here is an example of all the available default fields that are sent after the trigger is fired. The fields may vary based on custom extensions or third party plugins.', 'wpwhpro-page-triggers'); ?>
											</p>
											<pre><?php echo $display_code; ?></pre>
										<?php endif; ?>
									</div>
								</div>

								<div class="wpwh-accordion__item">
									<button class="wpwh-accordion__heading wpwh-btn wpwh-btn--link wpwh-btn--block text-left collapsed" type="button" data-toggle="collapse" data-target="#wpwh_accordion_description_<?php echo $identkey; ?>" aria-expanded="true" aria-controls="wpwh_accordion_description_<?php echo $identkey; ?>">
										<span><?php echo __( 'Description', 'wpwhpro-page-triggers'); ?></span>
										<span class="text-secondary">
											<?php echo __( 'Expand', 'wpwhpro-page-triggers'); ?>
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="9" fill="none" class="ml-1">
												<defs />
												<path stroke="#F1592A" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l7 7 7-7" />
											</svg>
										</span>
									</button>
									<div id="wpwh_accordion_description_<?php echo $identkey; ?>" class="wpwh-accordion__content collapse" aria-labelledby="headingOne">
										<div class="wpwh-content">
											<?php if( is_array( $trigger['description'] ) ) : ?>

												<h4><?php echo __( 'How to set up a trigger.', 'wp-webhooks' ); ?></h4>
												<p><?php echo __( 'Please take a look at the following manual to learn more about how to set up a trigger: ', 'wp-webhooks' ); ?>
													<a target="_blank" title="Visit WP Webhooks docs" href="https://wp-webhooks.com/docs/knowledge-base/what-are-wp-webhooks-triggers/">https://wp-webhooks.com/docs/knowledge-base/what-are-wp-webhooks-triggers/</a>
												</p>

												<?php if( isset( $trigger['description']['trigger_hooks'] ) && ! empty( $trigger['description']['trigger_hooks'] ) ) : ?>
												<h4><?php echo __( 'When does this trigger fire?', 'wp-webhooks' ); ?></h4>
												<?php echo __( 'This trigger is registered on the following event (WordPress hook):', 'wp-webhooks' ); ?>
												<ol>
													<?php foreach( $trigger['description']['trigger_hooks'] as $trigger_hook ) : ?>
														<li>
															<?php if( isset( $trigger_hook['url'] ) && ! empty( $trigger_hook['url'] ) ) : ?>
																<a href="<?php echo esc_url( $trigger_hook['url'] ); ?>" target="_blank" title="<?php echo __( 'Visit documentation', 'wp-webhooks' ); ?>"><strong><?php echo isset( $trigger_hook['hook'] ) ? $trigger_hook['hook'] : ''; ?></strong></a>
															<?php else : ?>
																<strong><?php echo isset( $trigger_hook['hook'] ) ? $trigger_hook['hook'] : ''; ?></strong>
															<?php endif; ?>
															<?php if( isset( $trigger_hook['description'] ) ) : ?>
																<?php echo $trigger_hook['description']; ?>
															<?php endif; ?>
														</li>
													<?php endforeach; ?>
												</ol>
												<?php endif; ?>

												<h4><?php echo __( 'How to use this trigger.', 'wp-webhooks' ); ?></h4>
												<ol>
													<li><?php echo __( 'To get started, please copy the webhook URL of the endpoint you want to send the data to. This can be an external URL from your third-party provider, or any other URL that accepts webhooks.', 'wp-webhooks' ); ?></li>
													<li><?php echo __( 'Once you have this URL, please click on the <strong>Add Webhook URL</strong> button and place the URL into the <strong>Webhook URL</strong> field.', 'wp-webhooks' ); ?></li>
													<li><?php echo __( 'For a better identification of the webhook URL, we recommend to also fill the <strong>Webhook Name</strong> field. This field will be used as the slug for your webhook URL. In case you leave it empty, we will automatically generate a random number as an identifier.', 'wp-webhooks' ); ?></li>
													<li><?php echo sprintf( __( 'After you added your <strong>Webhook URL</strong>, press the <strong>Add for %1$s</strong> button to finish the setup.', 'wp-webhooks' ), $webhook_name ); ?></li>
													<li><?php echo __( 'That\'s it! Now you are able to recieve data on the URL once the trigger fires.', 'wp-webhooks' ); ?></li>
													<li><?php echo __( 'Next to the <strong>Webhook URL</strong>, you will find three dots that contain further actions once you click on it. Within these actions you can test your webhook trigger, customize the data and request, as well as deactivate or delete it.', 'wp-webhooks' ); ?></li>
													
													<?php if( isset( $trigger['description']['steps'] ) && ! empty( $trigger['description']['steps'] ) ) : ?>
														<?php foreach( $trigger['description']['steps'] as $step ) : ?>
															<li><?php echo $step; ?></li>
														<?php endforeach; ?>
													<?php endif; ?>
												</ol>

												<h4><?php echo __( 'Tipps', 'wp-webhooks' ); ?></h4>
												<ol>
													<?php if( ! empty( $trigger['description']['tipps'] ) ) : ?>
														<?php foreach( $trigger['description']['tipps'] as $tipp ) : ?>
															<li><?php echo $tipp; ?></li>
														<?php endforeach; ?>
													<?php endif; ?>
													<li><?php echo __( 'In case you don\'t need a specified webhook URL at the moment, you can simply deactivate it by clicking the <strong>Deactivate</strong> link within the Actions menu right next to the <strong>Webhook URL</strong>. This results in the specified URL not being fired once the trigger fires.', 'wp-webhooks' ); ?></li>
													<li><?php echo __( 'You can use the <strong>Send demo</strong> feature within the <strong>Actions</strong> menu to send a static request to your specified <strong>Webhook URL</strong>. Please note that the data sent within the request might differ from your live data as it is predefined by us.', 'wp-webhooks' ); ?></li>
													<li><?php echo __( 'Via the <strong>Settings</strong> feature within the <strong>Action</strong> menu right next to your <strong>Webhook URL</strong>, you can use customize the functionality of the request. It contains certain default settings like changing the request type the data is sent in, or custom settings, depending on your trigger. An explanation for each setting is right next to it. (Please don\'t forget to save the settings once you changed them - the button is at the end of the popup.)', 'wp-webhooks' ); ?></li>
												</ol>

												<?php echo __( 'In case you would like to learn more about our plugin, please check out our documentation at:', 'wp-webhooks' ); ?>
												<br>
												<a title="<?php echo __( 'Go to wp-webhooks.com/docs', 'wp-webhooks' ); ?>" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>

											<?php else : ?>
												<?php echo wpautop( $trigger['description'] ); ?>
											<?php endif; ?>
											
										</div>
									</div>
								</div>

							</div>

						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

    </div>

  </div>

</div>

<?php if( ! empty( $triggers ) ) : ?>
	<?php foreach( $triggers as $identkey => $trigger ) :

		$trigger_name = !empty( $trigger['name'] ) ? $trigger['name'] : $trigger['trigger'];
		$webhook_name = !empty( $trigger['trigger'] ) ? $trigger['trigger'] : '';

		//Map default trigger_attributes if available
		$settings = array();
		if( ! empty( $trigger['settings'] ) ){

			if( isset( $trigger['settings']['data'] ) ){
				$settings = (array) $trigger['settings']['data'];
			}
		}

		//Map the default trigger settings
		if( isset( $trigger['settings'] ) && isset( $trigger['settings']['load_default_settings'] ) && $trigger['settings']['load_default_settings'] === true ){
			$settings = array_merge( $settings, WPWHPRO()->settings->get_default_trigger_settings() );
		}

		//Add receivable trigger settings
		if( isset( $trigger['receivable_url'] ) && $trigger['receivable_url'] === true ){
			$settings = array_merge( WPWHPRO()->settings->get_receivable_trigger_settings(), $settings );
		}

		//Map dynamic settings
		$required_settings = WPWHPRO()->settings->get_required_trigger_settings();
		foreach( $required_settings as $settings_ident => $settings_data ){

			if( $settings_ident == 'wpwhpro_trigger_data_mapping' ){
				if( ! empty( $data_mapping_templates ) ){
					$required_settings[ $settings_ident ]['choices'] = array_replace( $required_settings[ $settings_ident ]['choices'], WPWHPRO()->data_mapping->flatten_data_mapping_data( $data_mapping_templates ) );
				} else {
					unset( $required_settings[ $settings_ident ] ); //if empty
				}
			}

			if( $settings_ident == 'wpwhpro_trigger_data_mapping_header' ){
				if( ! empty( $data_mapping_templates ) ){
					$required_settings[ $settings_ident ]['choices'] = array_replace( $required_settings[ $settings_ident ]['choices'], WPWHPRO()->data_mapping->flatten_data_mapping_data( $data_mapping_templates ) );
				} else {
					unset( $required_settings[ $settings_ident ] ); //if empty
				}
			}

			if( $settings_ident == 'wpwhpro_trigger_data_mapping_response' ){
				if( ! empty( $data_mapping_templates ) ){
					$required_settings[ $settings_ident ]['choices'] = array_replace( $required_settings[ $settings_ident ]['choices'], WPWHPRO()->data_mapping->flatten_data_mapping_data( $data_mapping_templates ) );
				} else {
					unset( $required_settings[ $settings_ident ] ); //if empty
				}
			}

			if( $settings_ident == 'wpwhpro_trigger_data_mapping_cookies' ){
				if( ! empty( $data_mapping_templates ) ){
					$required_settings[ $settings_ident ]['choices'] = array_replace( $required_settings[ $settings_ident ]['choices'], WPWHPRO()->data_mapping->flatten_data_mapping_data( $data_mapping_templates ) );
				} else {
					unset( $required_settings[ $settings_ident ] ); //if empty
				}
			}

		}

		$settings = array_merge( $settings, $required_settings );

		?>
		<div class="modal fade" id="wpwhAddWebhookModal-<?php echo $identkey; ?>" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h3 class="modal-title"><?php echo __( 'Add Webhook URL', 'wp-webhooks' ); ?></h3>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
								<path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
						</button>
					</div>
					<?php
						$overwrite_query_params = array(
							'wpwh-trigger' => $trigger['trigger']
						);

						$add_trigger_query_params = array_merge( $_GET, $overwrite_query_params );
						$add_trigger_form_url = WPWHPRO()->helpers->built_url( $current_url, $add_trigger_query_params );
					?>
					<form action="<?php echo $add_trigger_form_url; ?>" method="post">
						<div class="modal-body">
							<div class="form-group pb-4">
								<label class="wpwh-form-label" for="wpwh-webhook-slug-<?php echo $trigger['trigger']; ?>"><?php echo __( 'Webhook Name', 'wp-webhooks' ); ?></label>
								<input class="wpwh-form-input w-100" id="wpwh-webhook-slug-<?php echo $trigger['trigger']; ?>" name="wpwh-add-webhook-name" type="text" aria-label="<?php echo __( 'Webhook Name (Optional)', 'wp-webhooks' ); ?>" aria-describedby="input-group-webbhook-name-<?php echo $identkey; ?>" placeholder="<?php echo __( 'my-new-webhook', 'wp-webhooks' ); ?>">
							</div>
							<div class="form-group mb-0">
								<label class="wpwh-form-label" for="wpwh-webhook-url-<?php echo $trigger['trigger']; ?>">
									<?php echo __( 'Webhook URL', 'wp-webhooks' ); ?>
								</label>
								<div class="wpwh-content wpwh-text-small mb-3">
										<?php echo __( 'You can also add dynamic parameters to the URL that are later on mapped using the data mapping feature. E.g. ', 'wp-webhooks' ); ?><strong>https://yourdomain.test/endpoint/{:user_id:}</strong>
								</div>
								<input class="wpwh-form-input w-100" id="wpwh-webhook-url-<?php echo $trigger['trigger']; ?>" name="wpwh-add-webhook-url" type="text" class="form-control ironikus-webhook-input-new h30" aria-label="<?php echo __( 'Include your webhook url here', 'wp-webhooks' ); ?>" aria-describedby="input-group-webbhook-name-<?php echo $identkey; ?>" placeholder="<?php echo __( 'https://example.com/webbhook/onwzinsze', 'wp-webhooks' ); ?>">
							</div>
						</div>
						<div class="modal-footer">
							<?php echo WPWHPRO()->helpers->get_nonce_field( $trigger_nonce_data ); ?>
							<input type="hidden" name="wpwh-add-webhook-group" value="<?php echo $trigger['trigger']; ?>">
							<input type="hidden" name="wpwh-add-webhook-integration" value="<?php echo $trigger['integration']; ?>">
							<input type="submit" name="submit" id="submit-<?php echo $trigger['trigger']; ?>" class="wpwh-btn wpwh-btn--secondary w-100" value="<?php echo sprintf( __( 'Add for %s', 'wp-webhooks' ), $webhook_name ); ?>">
						</div>
					</form>
				</div>
			</div>
		</div>

		<?php $all_triggers = WPWHPRO()->webhook->get_hooks( 'trigger', $trigger['trigger'] ); ?>
		<?php foreach( $all_triggers as $webhook => $webhook_data ) :
			if( ! is_array( $webhook_data ) || empty( $webhook_data ) ) { continue; }
			if( ! current_user_can( apply_filters( 'wpwhpro/admin/settings/webhook/page_capability', WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-triggers' ), $webhook, $trigger['trigger'] ) ) ) { continue; }
			?>
			<div class="modal modal--lg fade" id="wpwhTriggerSettingsModal-<?php echo $identkey; ?>-<?php echo $webhook; ?>" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h3 class="modal-title"><?php echo __( 'Action Settings for', 'wp-webhooks' ); ?> "<?php echo $webhook; ?>"</h3>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
									<path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
								</svg>
							</button>
						</div>
						<div class="modal-body">
							<div class="d-flex align-items-center mb-3">
								<strong class="mr-4 flex-shrink-0">Webhook url:</strong>
								<input type="text" class="wpwh-form-input wpwh-form-input--sm rounded-lg" value="<?php echo $webhook_data['webhook_url']; ?>" readonly>
							</div>
							<div class="d-flex align-items-center mb-3">
								<strong class="mr-4 flex-shrink-0">Webhook trigger name:</strong>
								<?php echo $trigger_name; ?>
							</div>
							<div class="d-flex align-items-center mb-3">
								<strong class="mr-4 flex-shrink-0">Webhook technical name:</strong>
								<?php echo $webhook_name; ?>
							</div>
							<div class="ironikus-tb-webhook-settings">
								<?php if( $settings ) : ?>
									<form id="ironikus-webhook-form-<?php echo $trigger['trigger'] . '-' . $webhook; ?>">
										<table class="wpwh-table wpwh-table--sm mb-4">
											<tbody>
												<?php

												$settings_data = array();
												if( isset( $triggers_data[ $trigger['trigger'] ] ) ){
													if( isset( $triggers_data[ $trigger['trigger'] ][ $webhook ] ) ){
														if( isset( $triggers_data[ $trigger['trigger'] ][ $webhook ]['settings'] ) ){
															$settings_data = $triggers_data[ $trigger['trigger'] ][ $webhook ]['settings'];
														}
													}
												}

												foreach( $settings as $setting_name => $setting ) :

													$is_checked = ( $setting['type'] == 'checkbox' && isset( $setting['default_value'] ) && $setting['default_value'] == 'yes' ) ? 'checked' : '';
													$copyable = ( isset( $setting['copyable'] ) && $setting['copyable'] === true ) ? true : false;
													$value = isset( $setting['default_value'] ) ? $setting['default_value'] : '';
													$placeholder = ( $setting['type'] != 'checkbox' && isset( $setting['placeholder'] ) ) ? $setting['placeholder'] : '';

													$validated_atributes = '';
													if( isset( $setting['attributes'] ) ){
														foreach( $setting['attributes'] as $attribute_name => $attribute_value ){
															$validated_atributes .=  $attribute_name . '="' . $attribute_value . '" ';
														}
													}

													if( $setting['type'] == 'checkbox' ){
														$value = '1';
													}

													if( isset( $settings_data[ $setting_name ] ) ){
														$value = $settings_data[ $setting_name ];
														$is_checked = ( $setting['type'] == 'checkbox' && $value == 1 ) ? 'checked' : '';
													}

													if( $setting_name === 'wpwhpro_trigger_single_receivable_url' ){
														$value = WPWHPRO()->webhook->built_trigger_receivable_url( $trigger['trigger'], $webhook );
													}

													?>
													<tr>
														<td style="width:250px;">
															<label class="wpwh-form-label" for="iroikus-input-id-<?php echo $setting_name; ?>-<?php echo $trigger['trigger'] . '-' . $webhook; ?>">
																<strong><?php echo $setting['label']; ?></strong>
															</label>
															<?php if( in_array( $setting['type'], array( 'text' ) ) ) : ?>

																<?php if( ! empty( $copyable ) ) : ?>
																<div class="wpwh-copy-wrapper" data-wpwh-tippy-content="copied!">
																<?php endif; ?>

																<input class="wpwh-form-input" id="iroikus-input-id-<?php echo $setting_name; ?>-<?php echo $trigger['trigger'] . '-' . $webhook; ?>" name="<?php echo $setting_name; ?>" type="<?php echo $setting['type']; ?>" placeholder="<?php echo $placeholder; ?>" value="<?php echo $value; ?>" <?php echo $is_checked; ?> <?php echo $validated_atributes; ?>/>

																<?php if( ! empty( $copyable ) ) : ?>
																</div>
																<?php endif; ?>

															<?php elseif( in_array( $setting['type'], array( 'checkbox' ) ) ) : ?>
																<div class="wpwh-toggle wpwh-toggle--on-off">
																	<input type="<?php echo $setting['type']; ?>" id="iroikus-input-id-<?php echo $setting_name; ?>-<?php echo $trigger['trigger'] . '-' . $webhook; ?>" name="<?php echo $setting_name; ?>" class="wpwh-toggle__input" <?php echo $is_checked; ?> placeholder="<?php echo $placeholder; ?>" value="<?php echo $value; ?>" <?php echo $is_checked; ?> <?php echo $validated_atributes; ?>>
																	<label class="wpwh-toggle__btn" for="iroikus-input-id-<?php echo $setting_name; ?>-<?php echo $trigger['trigger'] . '-' . $webhook; ?>"></label>
																</div>
															<?php elseif( $setting['type'] === 'select' ) : ?>
																<select
																	class="wpwh-form-input"
																	name="<?php echo $setting_name; ?><?php echo ( isset( $setting['multiple'] ) && $setting['multiple'] ) ? '[]' : ''; ?>" <?php echo $validated_atributes; ?> <?php echo ( isset( $setting['multiple'] ) && $setting['multiple'] ) ? 'multiple' : ''; ?>

																	<?php if( isset( $setting['query'] ) ) : ?>
																		data-select2-ajax='<?php echo json_encode(array(
																			'action' => 'wp_webhooks_validate_field_query',
																			'webhook_type' => 'trigger',
																			'webhook_group' => $trigger['trigger'],
																			'webhook_integration' => $trigger['integration'],
																			'webhook_field' => $setting_name,
																		)); ?>'
																		data-select2-ajax-nonce-label="ironikus_nonce"
																	<?php endif; ?>
																>
																	<?php
																		if( isset( $settings_data[ $setting_name ] ) ){
																			$settings_data[ $setting_name ] = ( is_array( $settings_data[ $setting_name ] ) ) ? array_flip( $settings_data[ $setting_name ] ) : $settings_data[ $setting_name ];
																		}
																	?>
																	<?php if( isset( $setting['choices'] ) ) : ?>
																		<?php foreach( $setting['choices'] as $choice_name => $choice_label ) :

																			//Compatibility with 4.3.0
																			if( is_array( $choice_label ) ){
																				if( isset( $choice_label['label'] ) ){
																					$choice_label = $choice_label['label'];
																				} else {
																					$choice_label = $choice_name;
																				}
																			}

																			$selected = '';
																			if( isset( $settings_data[ $setting_name ] ) ){

																				if( is_array( $settings_data[ $setting_name ] ) ){
																					if( isset( $settings_data[ $setting_name ][ $choice_name ] ) ){
																						$selected = 'selected="selected"';
																					}
																				} else {
																					if( (string) $settings_data[ $setting_name ] === (string) $choice_name ){
																						$selected = 'selected="selected"';
																					}
																				}

																			} else {
																				//Make sure we also cover webhooks that settings haven't been saved yet
																				if( $choice_name === $value ){
																					$selected = 'selected="selected"';
																				}
																			}
																		?>
																		<option value="<?php echo $choice_name; ?>" <?php echo $selected; ?>><?php echo __( $choice_label, 'wp-webhooks' ); ?></option>
																		<?php endforeach; ?>
																	<?php endif; ?>
																	<?php if( isset( $setting['query'] ) && ! empty( $value ) ) : 
																	
																	//Make sure we always enforce an array 
																	$selected = WPWHPRO()->helpers->force_array( $value );
																	$query_items = WPWHPRO()->fields->get_query_items( $setting, $args = array(
																		's' => '',
																		'paged' => 1,
																		'selected' => $selected,
																	) );

																	$select_options = array();

																	if( ! empty( $query_items ) && isset( $query_items['items'] ) ){
																		$select_options = $query_items['items'];
																	}

																	?>
																		<?php foreach( $select_options as $skey => $sval ) : 
																		
																			if( ! is_array( $sval ) || ! isset( $sval['value'] ) || ! isset( $sval['label'] ) ){
																				continue;
																			}
																		
																		?>
																			<option value="<?php echo $sval['value']; ?>" selected="selected"><?php echo $sval['label']; ?></option>
																		<?php endforeach; ?>
																	<?php endif; ?>
																</select>
															<?php endif; ?>
														</td>
														<td><?php echo ( isset( $setting['short_description'] ) ) ? $setting['short_description'] : $setting['description']; ?></td>
													</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
										<button
											type="button"
											class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm"

											data-wpwh-event="save"
											data-wpwh-event-type="send"
											data-wpwh-event-element="wpwhTriggerSettingsModal-<?php echo $webhook; ?>"

											data-webhook-group="<?php echo $trigger['trigger']; ?>"
											data-webhook-id="<?php echo $webhook; ?>"
										>
											<span><?php echo __( 'Save Settings', 'wp-webhooks' ); ?></span>
										</button>
									</form>
								<?php else : ?>
									<div class="wpwhpro-empty">
										<?php echo __( 'For your current webhook are no settings available.', 'wp-webhooks' ); ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	<?php endforeach; ?>
<?php endif; ?>