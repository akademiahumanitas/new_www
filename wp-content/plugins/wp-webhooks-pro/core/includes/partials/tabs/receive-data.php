<?php
$actions = WPWHPRO()->webhook->get_actions();
$actions_data = WPWHPRO()->webhook->get_hooks( 'action' );
$current_url = WPWHPRO()->helpers->get_current_url( false, true );
$current_url_full = WPWHPRO()->helpers->get_current_url( true, true );
$action_nonce_data = WPWHPRO()->settings->get_action_nonce();
$clear_form_url = WPWHPRO()->helpers->get_current_url( true, true );
$data_mapping_templates = WPWHPRO()->data_mapping->get_data_mapping();
$authentication_templates = WPWHPRO()->auth->get_auth_templates();

//Collect non-migreated webhook action URLs prior version 5.0
$need_migration = array();
if( ! empty( $actions_data ) ){
    foreach( $actions_data as $mas => $mad ){
        if( isset( $mad['api_key'] ) && is_string( $mad['api_key'] ) ){
            $need_migration[ $mas ] = $mad;
        }
    }
}

if( ! empty( $actions ) ){
    usort($actions, function($a, $b) {
        $aname = isset( $a['name'] ) ? $a['name'] : '';
        $bname = isset( $b['name'] ) ? $b['name'] : '';
        return strcmp($aname, $bname);
    });
}

//Manage the migration
if( isset( $_POST['wpwh_migrate_action'] ) ){
    if ( check_admin_referer( $action_nonce_data['action'], $action_nonce_data['arg'] ) ) {

        $errors = array();
		$migrate_action = sanitize_title( $_POST['wpwh_migrate_action'] );
        $migrate_actions = ( is_array( $_POST['wpwh_migrate_actions'] ) ) ? $_POST['wpwh_migrate_actions'] : array();

        if( ! empty( $migrate_action ) && ! empty( $migrate_actions ) && isset( $need_migration[ $migrate_action ] ) ){
            foreach( $migrate_actions as $migrate_action_slug ){
                $migrate_action_slug = sanitize_title( $migrate_action_slug );

                $prepared_migrate_data = $need_migration[ $migrate_action ];

                //add the webhook group
                $prepared_migrate_data['group'] = $migrate_action_slug;

                $migrated = WPWHPRO()->webhook->create( $migrate_action, 'action', $prepared_migrate_data, $prepared_migrate_data['permission'] );
                if( $migrated ){
                    WPWHPRO()->webhook->unset_hooks( $migrate_action, 'action' );
                } else {
                    $errors[] = sprintf( __( 'An error occured while creating the new action URL %$1s for the %$2s action.', 'wp-webhooks' ), $migrate_action, $migrate_action_slug );
                }
            }
        }

        if( empty( $errors ) ){
            echo WPWHPRO()->helpers->create_admin_notice( 'The migration for action URL ' . $migrate_action . ' was successful.', 'success', true );
            unset( $need_migration[ $migrate_action ] );
        } else {
            echo WPWHPRO()->helpers->create_admin_notice( $errors, 'warning', true );
        }
	}
}
if( isset( $_POST['wpwh_migrate_action_delete'] ) ){
    if ( check_admin_referer( $action_nonce_data['action'], $action_nonce_data['arg'] ) ) {

        $errors = array();
		$migrate_action = sanitize_title( $_POST['wpwh_migrate_action_delete'] );

        if( ! empty( $migrate_action ) && isset( $need_migration[ $migrate_action ] ) ){
            $migrated = WPWHPRO()->webhook->unset_hooks( $migrate_action, 'action' );

            if( $migrated ){
                echo WPWHPRO()->helpers->create_admin_notice( 'The action URL ' . $migrate_action . ' was successfully deleted.', 'success', true );
                unset( $need_migration[ $migrate_action ] );
            } else {
                echo WPWHPRO()->helpers->create_admin_notice( 'An error occured deleting the ' . $migrate_action . ' action URL.', 'warning', true );
            }

        }
	}
}

if( isset( $_POST['wpwh-add-webhook-name'] ) ){
    if ( check_admin_referer( $action_nonce_data['action'], $action_nonce_data['arg'] ) ) {

		if( WPWHPRO()->helpers->current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-receive-data-create-url' ), 'wpwhpro-page-receive-data-create-url' ) ){

            $webhook_slug            = isset( $_POST['wpwh-add-webhook-name'] ) ? sanitize_title( $_POST['wpwh-add-webhook-name'] ) : '';

            if( isset( $need_migration[ $webhook_slug ] ) ){
                echo WPWHPRO()->helpers->create_admin_notice( 'Your chosen webhook name is part of a old URL. Please migrate it first.', 'error', true );
            } else {
                if( strpos( $webhook_slug, 'wpwh-flow-' ) === FALSE || substr( $webhook_slug, 0, 10 ) !== 'wpwh-flow-' ){
                    $webhook_group          = isset( $_POST['wpwh-add-webhook-group'] ) ? sanitize_text_field( $_POST['wpwh-add-webhook-group'] ) : '';
                    $webhook_integration    = isset( $_POST['wpwh-add-webhook-integration'] ) ? sanitize_title( $_POST['wpwh-add-webhook-integration'] ) : '';

                    if( ! empty( $webhook_slug ) ){
                        $new_webhook = $webhook_slug;
                    } else {
                        $new_webhook = strtotime( date( 'Y-n-d H:i:s' ) ) . 999 . rand( 10, 9999 );
                    }

                    $webhook = WPWHPRO()->webhook->get_hooks( 'action', $webhook_group, $new_webhook );

                    if( empty( $webhook ) ){
                        $check = WPWHPRO()->webhook->create( $new_webhook, 'action', array( 'group' => $webhook_group, 'integration' => $webhook_integration ) );

                        if( $check ){
                            echo WPWHPRO()->helpers->create_admin_notice( 'The webhook URL has been added.', 'success', true );
                        } else {
                            echo WPWHPRO()->helpers->create_admin_notice( 'Error while adding the webhook URL.', 'warning', true );
                        }

                        //reload data
                        $actions = WPWHPRO()->webhook->get_actions();
                        $actions_data = WPWHPRO()->webhook->get_hooks( 'action' );
                    }
                } else {
                    echo WPWHPRO()->helpers->create_admin_notice( 'Your chosen webhook name is reserved for internal use only. Please use a different one.', 'warning', true );
                }
            }

		}

	}
}

//Sort webhooks
$grouped_actions = array();
foreach( $actions as $identkey => $webhook_action ){
    $group = 'ungrouped';

    if( isset( $webhook_action['integration'] ) ){
        $group = $webhook_action['integration'];
    }

    if( ! isset( $grouped_actions[ $group ] ) ){
        $grouped_actions[ $group ] = array(
            $identkey => $webhook_action
        );
    } else {
        $grouped_actions[ $group ][ $identkey ] = $webhook_action;
    }
}

//add ungroped elements at the end
if( isset( $grouped_actions['ungrouped'] ) ){
	$ungrouped_actions = $grouped_actions['ungrouped'];
	unset( $grouped_actions['ungrouped'] );
	$grouped_actions['ungrouped'] = $ungrouped_actions;
}

$active_action = isset( $_GET['wpwh-action'] ) ? sanitize_title( $_GET['wpwh-action'] ) : 'create_user';

?>
<?php add_ThickBox(); ?>

<div class="wpwh-container">
  <div class="wpwh-title-area mb-5">
    <h1><?php echo __( 'Receive Data (Actions)', 'wp-webhooks' ); ?></h1>
    <p>
		<?php if( WPWHPRO()->whitelabel->is_active() && ! empty( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_receive_data' ) ) ) : ?>
			<?php echo __( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_receive_data' ), 'wp-webhooks' ); ?>
		<?php else : ?>
			<?php echo sprintf( __( 'Actions are dynamically created URLs that receive data to do something specific on your WordPress website using the available arguments. To use one, simply create a URL and send a POST request along with your chosen arguments (visible once you select a specific action down below). For more information on each of the available actions, you can also select the specific integration and the action on our <a class="text-secondary" title="Go to our product documentation" target="_blank" href="%2$s">integrations page</a>.', 'wp-webhooks' ), '<strong>' . $this->page_title . '</strong>', 'https://wp-webhooks.com/integrations/'); ?>
		<?php endif; ?>
	</p>
  </div>

  <?php if( ! empty( $need_migration ) ) : ?>
        <div class="wpwh-title-area mb-4 mt-4">
			<h2 class="wpwh-text-danger"><?php echo __( 'Migration required!', 'wp-webhooks' ); ?></h2>
			<p><?php echo sprintf( __( 'Since version 5.0, action URLs got migrated to an action layer, allowing better handling and security. Since you have been using %s prior to that (Thanks a lot!), you need to migrate your URLs to the new structure. Please note: Your URLs continue to work if you havent migrated them yet, however, you cannot edit them anymore until after the migration.', 'wp-webhooks' ), WPWHPRO()->settings->get_page_title() ); ?></p>
		</div>

        <div class="wpwh-box wpwh-box--big mb-4">
			<div class="wpwh-box__body">
				<p class="wpwh-text-danger mb-4">
					<?php echo sprintf( __( 'Some of the webhook action URLs could not be migrated automatically. Please verify the URLs down below and select the respective action you would like to use along with it. We will then migrate the URL to each of the chosen actions.', 'wp-webhooks' ), $this->page_title ); ?>
				</p>
            </div>
            <table class="wpwh-table wpwh-table--sm wpwh-text-small">
                <thead>
                    <tr>
                        <th>Webhook Name</th>
                        <th>Webhook URL</th>
                        <th class="text-center pr-3">Migrate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach( $need_migration as $migration_slug => $migration_data ) : ?>
                        <tr>
                            <td>
                                <div class="wpwh-copy-wrapper" data-wpwh-tippy-content="<?php echo __( 'copied!', 'wp-webhooks' ); ?>"><input class="wpwh-form-input w-100" type='text' value="<?php echo sanitize_title( $migration_slug ); ?>" readonly /></div>
                            </td>
                            <td>
                                <div class="wpwh-copy-wrapper" data-wpwh-tippy-content="<?php echo __( 'copied!', 'wp-webhooks' ); ?>"><input class="wpwh-form-input w-100" type='text' value="<?php echo WPWHPRO()->webhook->built_url( $migration_slug, $migration_data['api_key'] ); ?>" readonly /></div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center justify-content-center">
                                    <button class="wpwh-btn wpwh-btn--sm wpwh-btn--secondary mr-3" title="Migrate Action URL" data-toggle="modal" data-target="#wpwhMigrateURL-<?php echo sanitize_title( $migration_slug ); ?>"><?php echo __( 'Migrate', 'wp-webhooks' ); ?></button>
                                    <span class="wpwh-text-danger" style="cursor:pointer;" title="Delete action URL" data-toggle="modal" data-target="#wpwhMigrateURLDelete-<?php echo sanitize_title( $migration_slug ); ?>"><img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/delete.svg'; ?>" alt="Delete"></span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
  <?php endif; ?>

  <div class="wpwh-triggers" data-wpwh-trigger="">

    <div class="wpwh-triggers__sidebar">

      <div class="wpwh-trigger-search wpwh-box">
        <div class="wpwh-trigger-search__search flex-wrap">
          <input type="search" data-wpwh-trigger-search class="wpwh-form-input" name="search-trigger" id="search-trigger" placeholder="<?php echo __( 'Search actions', 'wp-webhooks' ); ?>">
          <div class="wpwh-text-small mt-2"><?php echo __( 'Type <strong>wpwhactive</strong> to see all of the actions that have URLs.', 'wp-webhooks' ); ?></div>
        </div>
				<?php if( ! empty( $actions ) ) : ?>
					<div class="wpwh-trigger-search__items">
						<?php foreach( $grouped_actions as $group => $single_actions ) :

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
							<?php foreach( $single_actions as $identkey => $action ) :
								$action_name = !empty( $action['name'] ) ? $action['name'] : $action['action'];
								$webhook_name = !empty( $action['action'] ) ? $action['action'] : '';

                                $has_actions_class = '';
                                if( isset( $actions_data[ $action['action'] ] ) && ! empty( $actions_data[ $action['action'] ] ) ){
                                    foreach( $actions_data[ $action['action'] ] as $webhook_class_name => $webhook_class_data ){
                                        if( strpos( $webhook_class_name, 'wpwh-flow-' ) !== FALSE && substr( $webhook_class_name, 0, 10 ) === 'wpwh-flow-' ){
                                            continue;
                                        }

                                        $has_actions_class = ' hasactions';
                                        break;
                                    }
                                }

								$is_active = $webhook_name === $active_action;

								?>
								<a href="#webhook-<?php echo $webhook_name; ?>" data-wpwh-trigger-id="<?php echo $webhook_name; ?>" class="wpwh-trigger-search__item<?php echo $is_active ? ' wpwh-trigger-search__item--active' : ''; ?><?php echo $has_actions_class; ?>"><?php echo $action_name; ?></a>
							<?php endforeach; ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
      </div>

    </div>

    <div class="wpwh-triggers__content" data-wpwh-trigger-content="">

		<?php if( ! empty( $actions ) ) : ?>
				<div class="wpwh-trigger-items">
					<?php foreach( $actions as $identkey => $action ) :

						$action_name = !empty( $action['name'] ) ? $action['name'] : $action['action'];
						$webhook_name = !empty( $action['action'] ) ? $action['action'] : '';
						$action_integration = isset( $action['integration'] ) ? $action['integration'] : '';
						$action_details = WPWHPRO()->integrations->get_details( $action_integration );

						$action_integration_icon = '';
						if( isset( $action_details['icon'] ) && ! empty( $action_details['icon'] ) ){
							$action_integration_icon = esc_html( $action_details['icon'] );
						}

						$action_integration_name = '';
						if( isset( $action_details['name'] ) && ! empty( $action_details['name'] ) ){
							$action_integration_name = esc_html( $action_details['name'] );
						}

						$is_active = $webhook_name === $active_action;

						//Map default action_attributes if available
						$settings = array();
						if( ! empty( $action['settings'] ) ){

							if( isset( $action['settings']['data'] ) ){
								$settings = (array) $action['settings']['data'];
							}

						}

						//Map dynamic settings
						$required_settings = WPWHPRO()->settings->get_required_action_settings();
						foreach( $required_settings as $settings_ident => $settings_data ){

                            if( $settings_ident == 'wpwhpro_action_data_mapping' ){
                                if( ! empty( $data_mapping_templates ) ){
                                    $required_settings[ $settings_ident ]['choices'] = array_replace( $required_settings[ $settings_ident ]['choices'], WPWHPRO()->data_mapping->flatten_data_mapping_data( $data_mapping_templates ) );
                                } else {
                                    unset( $required_settings[ $settings_ident ] ); //if empty
                                }
                            }

                            if( $settings_ident == 'wpwhpro_action_data_mapping_response' ){
                                if( ! empty( $data_mapping_templates ) ){
                                    $required_settings[ $settings_ident ]['choices'] = array_replace( $required_settings[ $settings_ident ]['choices'], WPWHPRO()->data_mapping->flatten_data_mapping_data( $data_mapping_templates ) );
                                } else {
                                    unset( $required_settings[ $settings_ident ] ); //if empty
                                }
                            }

                            if( $settings_ident == 'wpwhpro_action_authentication' ){
                                if( ! empty( $authentication_templates ) ){
                                    $required_settings[ $settings_ident ]['choices'] = array_replace( $required_settings[ $settings_ident ]['choices'], WPWHPRO()->auth->flatten_authentication_data( $authentication_templates ) );
                                } else {
                                    unset( $required_settings[ $settings_ident ] ); //if empty
                                }
                            }

                            if( $settings_ident == 'wpwhpro_action_action_whitelist' ){
                                $flattened_webhook_data = array();
                                foreach( $actions as $fwd_identkey => $fwd_action ){
                                    $flattened_webhook_data[ $fwd_action['action'] ] = $fwd_action['action'];
                                }

                                if( ! empty( $flattened_webhook_data ) ){
                                    $required_settings[ $settings_ident ]['choices'] = $flattened_webhook_data;
                                } else {
                                    unset( $required_settings[ $settings_ident ] ); //if empty
                                }
                            }

                        }

						$settings = array_merge( $settings, $required_settings );

						?>
						<div class="wpwh-trigger-item<?php echo $is_active ? ' wpwh-trigger-item--active' : ''; ?> wpwh-table-container" id="webhook-<?php echo $webhook_name; ?>" <?php echo ! $is_active ? 'style="display: none;"' : ''; ?>>
							<div class="wpwh-table-header">
								<div class="mb-2 d-flex align-items-center justify-content-between">
									<h2 class="d-flex align-items-end" data-wpwh-trigger-name>
										<?php if( ! empty( $action_integration_icon ) ) : ?>
                                            <a title="<?php echo sprintf( __( 'Visit the %s integration', 'wp-webhooks' ), $action_integration_name ); ?>" target="_blank" href="<?php echo WPWHPRO()->helpers->get_wp_webhooks_endpoint_url( $action_integration ); ?>">
                                                <img class="wpwh-trigger-search__item-image mb-1" style="height:100%;max-height:40px;width:40px;max-width:40px;" src="<?php echo $action_integration_icon; ?>" />
											</a>
                                            <?php endif; ?>
										<div class="d-flex flex-column">

                                            <a title="<?php echo sprintf( __( 'Visit the %s integration', 'wp-webhooks' ), $action_integration_name ); ?>" target="_blank" href="<?php echo WPWHPRO()->helpers->get_wp_webhooks_endpoint_url( $action_integration ); ?>">
                                            <span class="wpwh-trigger-integration-name wpwh-text-small"><?php echo $action_integration_name; ?></span>
											</a>

                                            <a class="d-flex" title="<?php echo sprintf( __( 'Visit the %s trigger', 'wp-webhooks' ), $webhook_name ); ?>" target="_blank" href="<?php echo WPWHPRO()->helpers->get_wp_webhooks_endpoint_url( $action_integration, $webhook_name, 'action' ); ?>">
												<span class="mr-2"><?php echo $action_name; ?></span>
												<div style="width:17px;height:17px;">
													<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="info-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-info-circle fa-w-16"><path fill="currentColor" d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 110c23.196 0 42 18.804 42 42s-18.804 42-42 42-42-18.804-42-42 18.804-42 42-42zm56 254c0 6.627-5.373 12-12 12h-88c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h12v-64h-12c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h64c6.627 0 12 5.373 12 12v100h12c6.627 0 12 5.373 12 12v24z" class=""></path></svg>
												</div>
											</a>
										</div>
									</h2>
									<div class="wpwh-trigger-webhook-name wpwh-text-small"><?php echo $webhook_name; ?></div>
								</div>
								<div class="wpwh-content mb-4">
									<?php echo $action['short_description']; ?>
								</div>
								<div class="d-flex align-items-center justify-content-end">
									<button class="wpwh-btn wpwh-btn--sm wpwh-btn--secondary" title="<?php echo __( 'Create Action URL', 'wp-webhooks' ); ?>" data-toggle="modal" data-target="#wpwhAddWebhookModal-<?php echo $identkey; ?>">
										<?php echo __( 'Create Action URL', 'wp-webhooks' ); ?>
									</button>
								</div>
							</div>
							<table class="wpwh-table wpwh-table--sm wpwh-text-small">
								<thead>
									<tr>
										<th></th>
										<th><?php echo __( 'Webhook Name', 'wp-webhooks' ); ?></th>
										<th><?php echo __( 'Webhook URL', 'wp-webhooks' ); ?></th>
										<th><?php echo __( 'API Key', 'wp-webhooks' ); ?></th>
										<th class="text-center pr-3"><?php echo __( 'Action', 'wp-webhooks' ); ?></th>
									</tr>
								</thead>
								<tbody>

									<?php $all_actions = WPWHPRO()->webhook->get_hooks( 'action', $action['action'] ); ?>
									<?php foreach( $all_actions as $webhook => $webhook_data ) : ?>
										<?php if( ! is_array( $webhook_data ) || empty( $webhook_data ) ) { continue; } ?>
										<?php if( ! current_user_can( apply_filters( 'wpwhpro/admin/settings/webhook/page_capability', WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-actions' ), $webhook, $action['action'] ) ) ) { continue; } ?>
										<?php
											 if( strpos( $webhook, 'wpwh-flow-' ) !== FALSE && substr( $webhook, 0, 10 ) === 'wpwh-flow-' ){
                                                continue;
                                            }

											$status = 'active';
											$status_name = 'Active';
											if( isset( $webhook_data['status'] ) && $webhook_data['status'] == 'inactive' ){
												$status = 'inactive';
												$status_name = 'Inactive';
											}
										?>
										<tr id="webhook-action-<?php echo $action['action']; ?>-<?php echo $webhook; ?>">
											<td class="align-middle wpwh-status-cell">
												<button
													data-wpwh-event="deactivate"
													data-wpwh-event-type="receive"
                                                    data-wpwh-event-element="#webhook-action-<?php echo $action['action']; ?>-<?php echo $webhook; ?>"

													data-wpwh-webhook-status="<?php echo $status; ?>"
													data-wpwh-webhook-group="<?php echo $action['action']; ?>"
													data-wpwh-webhook-slug="<?php echo $webhook; ?>"

													class="wpwh-status wpwh-status--<?php echo $status; ?>"
												>
													<span><?php echo __( $status, 'wp-webhooks' ); ?></span>
												</button>
											</td>
											<td>
												<div class="wpwh-copy-wrapper" data-wpwh-tippy-content="<?php echo __( 'copied!', 'wp-webhooks' ); ?>"><input class="wpwh-form-input w-100" type='text' name='ironikus_wp_webhooks_pro_webhook_name' value="<?php echo $webhook; ?>" readonly /></div>
											</td>
											<td>
                                                <div class="wpwh-copy-wrapper" data-wpwh-tippy-content="<?php echo __( 'copied!', 'wp-webhooks' ); ?>"><input class="wpwh-form-input w-100" type="text" name='ironikus_wp_webhooks_pro_webhook_url' value="<?php echo WPWHPRO()->webhook->built_url( $webhook, $webhook_data['api_key'], array( 'action' => $action['action'] ) ); ?>" readonly /></div>
											</td>
											<td>
                                                <div class="wpwh-copy-wrapper" data-wpwh-tippy-content="<?php echo __( 'copied!', 'wp-webhooks' ); ?>"><input class="wpwh-form-input w-100" type="text" value="<?php echo $webhook_data['api_key']; ?>" readonly /></div>
											</td>
											<td class="align-middle text-center wpwh-table__action pr-3">
                                                <div class="d-flex align-items-center justify-content-center">
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
                                                        data-wpwh-event-type="receive"
                                                        data-wpwh-event-element="#webhook-action-<?php echo $action['action']; ?>-<?php echo $webhook; ?>"

                                                        data-wpwh-delete="<?php echo $webhook; ?>"
                                                        data-wpwh-group="<?php echo $action['action']; ?>"
                                                        data-tippy=""
                                                        data-tippy-content="<?php echo __( 'Delete', 'wp-webhooks' ); ?>"
                                                        data-wpwh-webhook-slug="<?php echo $webhook; ?>"
                                                    >
                                                        <img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/delete.svg'; ?>" alt="Delete">
                                                    </a>
                                                </div>
											</td>
										</tr>

									<?php endforeach; ?>

								</tbody>
							</table>

							<div class="wpwh-accordion" id="wpwh_accordion_<?php echo $identkey; ?>">

                            <div class="wpwh-accordion__item">
                                    <button class="wpwh-accordion__heading wpwh-btn wpwh-btn--link wpwh-btn--block text-left collapsed" type="button" data-toggle="collapse" data-target="#wpwh_accordion_arguments_<?php echo $identkey; ?>" aria-expanded="true" aria-controls="wpwh_accordion_arguments_<?php echo $identkey; ?>">
                                        <span><?php echo __( 'Accepted arguments', 'wpwhpro-page-actions'); ?></span>
                                        <span class="text-secondary">
                                            <span class="wpwh-text-expand"><?php echo __( 'Expand', 'wpwhpro-page-actions'); ?></span>
                                            <span class="wpwh-text-close"><?php echo __( 'Close', 'wpwhpro-page-actions'); ?></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="9" fill="none" class="ml-1">
                                                <defs />
                                                <path stroke="#F1592A" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l7 7 7-7" />
                                            </svg>
                                        </span>
                                    </button>
                                    <div id="wpwh_accordion_arguments_<?php echo $identkey; ?>" class="wpwh-accordion__content collapse" aria-labelledby="headingOne">
                                        <table class="wpwh-table wpwh-text-small">
                                            <thead>
                                                <tr>
                                                    <th><?php echo __( 'Argument', 'wp-webhooks' ); ?></th>
                                                    <th><?php echo __( 'Description', 'wp-webhooks' ); ?></th>
                                                    <th><?php echo __( 'More', 'wp-webhooks' ); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach( $action['parameter'] as $param => $param_data ) : ?>
                                                    <tr <?php if( ! empty( $param_data['required'] ) ) { echo 'class="wpwh-is-required"'; } ; ?>>
                                                        <td class="wpwh-w-25"><strong class="text-lg"><?php echo $param; ?></strong>
                                                            <?php if( ! empty( $param_data['required'] ) ) : ?>
                                                                <br><span class="text-primary"><?php echo __( 'Required', 'wp-webhooks' ); ?></span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo $param_data['short_description']; ?></td>
                                                        <td>
                                                            <?php if(
                                                                isset( $param_data['description'] ) && ! empty( $param_data['description'] )
                                                                ||
                                                                isset( $param_data['type'] ) && $param_data['type'] === 'select' && isset( $param_data['choices'] ) && ! empty( $param_data['choices'] )
                                                                ) : ?>
                                                                <a
                                                                    class="action-argument-details-<?php echo $action['action']; ?>"
                                                                    href="#"
                                                                    data-toggle="modal"
                                                                    data-target="#wpwhaction-argument-detail-modal-<?php echo $action['action']; ?>-<?php echo $param; ?>"
                                                                >
                                                                    <span><?php echo __( 'Details', 'wp-webhooks' ); ?></span>
                                                                </a>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="wpwh-accordion__item">
                                    <button class="wpwh-accordion__heading wpwh-btn wpwh-btn--link wpwh-btn--block text-left collapsed" type="button" data-toggle="collapse" data-target="#wpwh_accordion_return_values_<?php echo $identkey; ?>" aria-expanded="true" aria-controls="wpwh_accordion_return_values_<?php echo $identkey; ?>">
                                        <span><?php echo __( 'Return values', 'wpwhpro-page-actions'); ?></span>
                                        <span class="text-secondary">
                                            <span class="wpwh-text-expand"><?php echo __( 'Expand', 'wpwhpro-page-actions'); ?></span>
                                            <span class="wpwh-text-close"><?php echo __( 'Close', 'wpwhpro-page-actions'); ?></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="9" fill="none" class="ml-1">
                                                <defs />
                                                <path stroke="#F1592A" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l7 7 7-7" />
                                            </svg>
                                        </span>
                                    </button>
                                    <div id="wpwh_accordion_return_values_<?php echo $identkey; ?>" class="wpwh-accordion__content collapse" aria-labelledby="headingTwo">
                                        <?php if( ! empty( $action['returns'] ) ) : ?>
                                            <table class="wpwh-table wpwh-text-small mb-4">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo __( 'Argument', 'wp-webhooks' ); ?></th>
                                                        <th><?php echo __( 'Description', 'wp-webhooks' ); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach( $action['returns'] as $param => $param_data ) : ?>
                                                        <tr>
                                                            <th class="wpwh-text-left wpwh-w-25"><strong class="text-lg"><?php echo $param; ?></strong></th>
                                                            <td><?php echo $param_data['short_description']; ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>

                                            <?php if( ! empty( $action['returns_code'] ) ) :

                                                $display_code = $action['returns_code'];
                                                if( is_array( $action['returns_code'] ) ){
                                                    $display_code = '<pre>' . htmlspecialchars( json_encode( $display_code, JSON_PRETTY_PRINT ) ) . '</pre>';
                                                }

                                            ?>
                                                <div class="wpwh-content">
                                                    <p>
                                                        <?php echo __( 'Here is an example of all the available fields. The fields may vary based on custom extensions, third party plugins or different values.', 'wpwhpro-page-actions'); ?>
                                                    </p>
                                                    <?php echo $display_code; ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="wpwh-accordion__item">
                                    <button class="wpwh-accordion__heading wpwh-btn wpwh-btn--link wpwh-btn--block text-left collapsed" type="button" data-toggle="collapse" data-target="#wpwh_accordion_description_<?php echo $identkey; ?>" aria-expanded="true" aria-controls="wpwh_accordion_description_<?php echo $identkey; ?>">
                                        <span><?php echo __( 'Description', 'wpwhpro-page-actions'); ?></span>
                                        <span class="text-secondary">
                                            <span class="wpwh-text-expand"><?php echo __( 'Expand', 'wpwhpro-page-actions'); ?></span>
                                            <span class="wpwh-text-close"><?php echo __( 'Close', 'wpwhpro-page-actions'); ?></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="9" fill="none" class="ml-1">
                                                <defs />
                                                <path stroke="#F1592A" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l7 7 7-7" />
                                            </svg>
                                        </span>
                                    </button>
                                    <div id="wpwh_accordion_description_<?php echo $identkey; ?>" class="wpwh-accordion__content collapse" aria-labelledby="headingThree">
                                        <div class="wpwh-content">
                                            <?php if( is_array( $action['description'] ) ) : ?>

                                            <h4><?php echo __( 'How to set up a action.', 'wp-webhooks' ); ?></h4>
                                            <p><?php echo __( 'Please take a look at the following manual to learn more about how to set up a action: ', 'wp-webhooks' ); ?>
                                                <a target="_blank" title="Visit WP Webhooks docs" href="https://wp-webhooks.com/docs/knowledge-base/what-are-wp-webhooks-actions/">https://wp-webhooks.com/docs/knowledge-base/what-are-wp-webhooks-actions/</a>
                                            </p>

                                            <h4><?php echo __( 'How to use this action.', 'wp-webhooks' ); ?></h4>
                                            <ol>
                                                <?php if( isset( $action['description']['steps'] ) && ! empty( $action['description']['steps'] ) ) : ?>
                                                    <?php foreach( $action['description']['steps'] as $step ) : ?>
                                                        <li><?php echo $step; ?></li>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </ol>

                                            <h4><?php echo __( 'Tipps', 'wp-webhooks' ); ?></h4>
                                            <ol>
                                                <?php if( ! empty( $action['description']['tipps'] ) ) : ?>
                                                    <?php foreach( $action['description']['tipps'] as $tipp ) : ?>
                                                        <li><?php echo $tipp; ?></li>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                           </ol>

                                            <?php echo __( 'In case you would like to learn more about our plugin, please check out our documentation at:', 'wp-webhooks' ); ?>
                                            <br>
                                            <a title="<?php echo __( 'Go to wp-webhooks.com/docs', 'wp-webhooks' ); ?>" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>

                                            <?php else : ?>
                                            <?php echo wpautop( $action['description'] ); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="wpwh-accordion__item">
                                    <button class="wpwh-accordion__heading wpwh-btn wpwh-btn--link wpwh-btn--block text-left collapsed" type="button" data-toggle="collapse" data-target="#wpwh_accordion_test_action_<?php echo $identkey; ?>" aria-expanded="true" aria-controls="wpwh_accordion_test_action_<?php echo $identkey; ?>">
                                        <span><?php echo __( 'Test action', 'wpwhpro-page-actions'); ?></span>
                                        <span class="text-secondary">
                                            <span class="wpwh-text-expand"><?php echo __( 'Expand', 'wpwhpro-page-actions'); ?></span>
                                            <span class="wpwh-text-close"><?php echo __( 'Close', 'wpwhpro-page-actions'); ?></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="9" fill="none" class="ml-1">
                                                <defs />
                                                <path stroke="#F1592A" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l7 7 7-7" />
                                            </svg>
                                        </span>
                                    </button>
                                    <div id="wpwh_accordion_test_action_<?php echo $identkey; ?>" class="wpwh-accordion__content collapse" aria-labelledby="headingFour">
                                        <div class="wpwh-content">
                                            <p>
                                                <?php echo __( 'Here you can test the specified webhook. Please note, that this test can modify the data of your website (Depending on what action you test). Also, you will see the response as any web service receives it.', 'wpwhpro-page-actions'); ?>
                                            </p>
                                            <p>
                                                <?php echo __( 'Please choose the webhook you are going to run the test with. Simply select the one you want to use down below.', 'wpwhpro-page-actions'); ?>
                                            </p>
                                            <select
                                                class="wpwh-form-input wpwh-webhook-receive-test-action"
                                                data-wpwh-identkey="<?php echo $identkey; ?>"
                                                data-wpwh-target="#wpwh-action-testing-form-<?php echo $identkey; ?>"
                                            >
                                                <option value="empty"><?php echo __( 'Choose action...', 'wp-webhooks' ); ?></option>
                                                <?php if( is_array( $actions_data ) && isset( $actions_data[ $webhook_name ] ) && ! empty( $actions_data[ $webhook_name ] ) ) : ?>
                                                    <?php foreach( $actions_data[ $webhook_name ] as $subwebhook => $subwebhook_data ) :

                                                        if( ! is_array( $subwebhook_data ) || empty( $subwebhook_data ) ) { 
                                                            continue;
                                                        }

                                                        if( strpos( $subwebhook, 'wpwh-flow-' ) !== FALSE && substr( $subwebhook, 0, 10 ) === 'wpwh-flow-' ){
                                                            continue;
                                                        }

                                                    ?>
                                                        <option class="<?php echo $subwebhook; ?>" value="<?php echo WPWHPRO()->webhook->built_url( $subwebhook, $subwebhook_data['api_key'], array( 'action' => $action['action'], 'wpwhpro_direct_test' => 1 ) ); ?>"><?php echo $subwebhook; ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <form id="wpwh-action-testing-form-<?php echo $identkey; ?>" method="post" class="wpwh-actions-testing-form mt-4" action="" target="_blank" style="display:none;">
                                                <table class="wpwh-table wpwh-table--in-content">
                                                    <tbody>
                                                        <?php foreach( $action['parameter'] as $param => $param_data ) : ?>
                                                            <tr valign="top">
                                                                <td>
                                                                    <input id="wpwhprotest_<?php echo $action['action']; ?>_<?php echo $param; ?>" class="wpwh-form-input" type="text" name="<?php echo $param; ?>" placeholder="<?php echo ( ! empty( $param_data['required'] ) ) ? __( 'Required', 'wpwhpro-page-actions') : '' ?>">
                                                                </td>
                                                                <td scope="row" valign="top">
                                                                    <label for="wpwhprotest_<?php echo $action['action']; ?>_<?php echo $param; ?>">
                                                                        <strong><?php echo $param; ?></strong>
                                                                    </label>
                                                                </td>
                                                                <td>
                                                                    <?php echo $param_data['short_description']; ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                        <tr valign="top">
                                                            <td>
                                                                <input id="wpwhprotest_<?php echo $action['action']; ?>_access_token" class="wpwh-form-input" type="text" name="access_token">
                                                            </td>
                                                            <td scope="row" valign="top">
                                                                <label for="wpwhprotest_<?php echo $action['action']; ?>_access_token">
                                                                    <strong>access_token</strong>
                                                                </label>
                                                            </td>
                                                            <td>
                                                                <?php echo __( 'This is a static input field. You only need to set it in case you activated the access_token functionality within the webhook settings.', 'wp-webhooks' ); ?>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div class="wpwh-text-center my-3">
                                                    <input type="submit" name="submit" id="submit-<?php echo $action['action']; ?>" class="wpwh-btn wpwh-btn--secondary" value="<?php echo __( 'Test action', 'wp-webhooks' ) ?>">
                                                </div>
                                            </form>
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

<?php if( ! empty( $actions ) ) : ?>
	<?php foreach( $actions as $identkey => $action ) :

		$action_name = !empty( $action['name'] ) ? $action['name'] : $action['action'];
		$webhook_name = !empty( $action['action'] ) ? $action['action'] : '';

		//Map default action_attributes if available
		$settings = array();
		if( ! empty( $action['settings'] ) ){

			if( isset( $action['settings']['data'] ) ){
				$settings = (array) $action['settings']['data'];
			}

			if( isset( $action['settings']['load_default_settings'] ) && $action['settings']['load_default_settings'] === true ){
					$settings = array_merge( $settings, WPWHPRO()->settings->get_default_action_settings() );
			}
		}

		//Add receivable action settings
		if( isset( $action['receivable_url'] ) && $action['receivable_url'] === true ){
			$settings = array_merge( WPWHPRO()->settings->get_receivable_action_settings(), $settings );
		}

		//Map dynamic settings
		$required_settings = WPWHPRO()->settings->get_required_action_settings();
		foreach( $required_settings as $settings_ident => $settings_data ){

            if( $settings_ident == 'wpwhpro_action_data_mapping' ){
                if( ! empty( $data_mapping_templates ) ){
                    $required_settings[ $settings_ident ]['choices'] = array_replace( $required_settings[ $settings_ident ]['choices'], WPWHPRO()->data_mapping->flatten_data_mapping_data( $data_mapping_templates ) );
                } else {
                    unset( $required_settings[ $settings_ident ] ); //if empty
                }
            }

            if( $settings_ident == 'wpwhpro_action_data_mapping_response' ){
                if( ! empty( $data_mapping_templates ) ){
                    $required_settings[ $settings_ident ]['choices'] = array_replace( $required_settings[ $settings_ident ]['choices'], WPWHPRO()->data_mapping->flatten_data_mapping_data( $data_mapping_templates ) );
                } else {
                    unset( $required_settings[ $settings_ident ] ); //if empty
                }
            }

            if( $settings_ident == 'wpwhpro_action_authentication' ){
                if( ! empty( $authentication_templates ) ){
                    $required_settings[ $settings_ident ]['choices'] = array_replace( $required_settings[ $settings_ident ]['choices'], WPWHPRO()->auth->flatten_authentication_data( $authentication_templates ) );
                } else {
                    unset( $required_settings[ $settings_ident ] ); //if empty
                }
            }

            if( $settings_ident == 'wpwhpro_action_action_whitelist' ){
                $flattened_webhook_data = array();
                foreach( $actions as $fwd_identkey => $fwd_action ){
                    $flattened_webhook_data[ $fwd_action['action'] ] = $fwd_action['action'];
                }

                if( ! empty( $flattened_webhook_data ) ){
                    $required_settings[ $settings_ident ]['choices'] = $flattened_webhook_data;
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
						<h3 class="modal-title"><?php echo __( 'Create Action URL', 'wp-webhooks' ); ?></h3>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
								<path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
						</button>
					</div>

                    <?php if( empty( $need_migration ) ) : ?>
					<?php
						$overwrite_query_params = array(
							'wpwh-action' => $action['action']
						);

						$add_action_query_params = array_merge( $_GET, $overwrite_query_params );
						$add_action_form_url = WPWHPRO()->helpers->built_url( $current_url, $add_action_query_params );
					?>
					<form action="<?php echo $add_action_form_url; ?>" method="post">
						<div class="modal-body">
							<div class="form-group pb-4">
								<label class="wpwh-form-label" for="wpwh-webhook-slug-<?php echo $action['action']; ?>"><?php echo __( 'Webhook Name', 'wp-webhooks' ); ?></label>
								<input class="wpwh-form-input w-100" id="wpwh-webhook-slug-<?php echo $action['action']; ?>" name="wpwh-add-webhook-name" type="text" aria-label="<?php echo __( 'Webhook Name (Optional)', 'wp-webhooks' ); ?>" aria-describedby="input-group-webbhook-name-<?php echo $identkey; ?>" placeholder="<?php echo __( 'my-new-webhook', 'wp-webhooks' ); ?>">
							</div>
						</div>
						<div class="modal-footer">
							<?php echo WPWHPRO()->helpers->get_nonce_field( $action_nonce_data ); ?>
							<input type="hidden" name="wpwh-add-webhook-group" value="<?php echo $action['action']; ?>">
							<input type="hidden" name="wpwh-add-webhook-integration" value="<?php echo $action['integration']; ?>">
							<input type="submit" name="submit" id="submit-<?php echo $action['action']; ?>" class="wpwh-btn wpwh-btn--secondary w-100" value="<?php echo sprintf( __( 'Create for %s', 'wp-webhooks' ), $webhook_name ); ?>">
						</div>
					</form>
                    <?php else : ?>
                        <p class="wpwh-text-danger p-4"><?php echo sprintf( __( 'Before creating new action URLs, please migrate your existing ones first.', 'wp-webhooks' ), WPWHPRO()->settings->get_page_title() ); ?></p>
                    <?php endif; ?>
				</div>
			</div>
		</div>

		<?php $all_actions = WPWHPRO()->webhook->get_hooks( 'action', $action['action'] ); ?>
		<?php foreach( $all_actions as $webhook => $webhook_data ) :
			if( ! is_array( $webhook_data ) || empty( $webhook_data ) ) { continue; }
			if( ! current_user_can( apply_filters( 'wpwhpro/admin/settings/webhook/page_capability', WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-actions' ), $webhook, $action['action'] ) ) ) { continue; }
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
								<strong class="mr-4 flex-shrink-0">Webhook action:</strong>
								<?php echo $webhook_name; ?>
							</div>
							<div class="d-flex align-items-center mb-3">
								<strong class="mr-4 flex-shrink-0">Webhook URL name:</strong>
								<?php echo $webhook; ?>
							</div>
							<div class="ironikus-tb-webhook-settings">
								<?php if( $settings ) : ?>
									<form id="ironikus-webhook-form-<?php echo $action['action'] . '-' . $webhook; ?>">
										<table class="wpwh-table wpwh-table--sm mb-4">
											<tbody>
												<?php

												$settings_data = array();
												if( isset( $actions_data[ $action['action'] ] ) ){
													if( isset( $actions_data[ $action['action'] ][ $webhook ] ) ){
														if( isset( $actions_data[ $action['action'] ][ $webhook ]['settings'] ) ){
															$settings_data = $actions_data[ $action['action'] ][ $webhook ]['settings'];
														}
													}
												}

												foreach( $settings as $setting_name => $setting ) :

													$is_deprecated = ( isset( $setting['deprecated'] ) && $setting['deprecated'] ) ? true : false;
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

													?>
													<tr <?php echo ( $is_deprecated ) ? 'style="display:none;"' : ''; ?>>
														<td style="width:250px;">
															<label class="wpwh-form-label" for="iroikus-input-id-<?php echo $setting_name; ?>-<?php echo $action['action'] . '-' . $webhook; ?>">
																<strong><?php echo $setting['label']; ?></strong>
															</label>
															<?php if( in_array( $setting['type'], array( 'text' ) ) ) : ?>

																<?php if( ! empty( $copyable ) ) : ?>
																<div class="wpwh-copy-wrapper" data-wpwh-tippy-content="copied!">
																<?php endif; ?>

																<input class="wpwh-form-input" id="iroikus-input-id-<?php echo $setting_name; ?>-<?php echo $action['action'] . '-' . $webhook; ?>" name="<?php echo $setting_name; ?>" type="<?php echo $setting['type']; ?>" placeholder="<?php echo $placeholder; ?>" value="<?php echo $value; ?>" <?php echo $is_checked; ?> <?php echo $validated_atributes; ?>/>

																<?php if( ! empty( $copyable ) ) : ?>
																</div>
																<?php endif; ?>

															<?php elseif( in_array( $setting['type'], array( 'checkbox' ) ) ) : ?>
																<div class="wpwh-toggle wpwh-toggle--on-off">
																	<input type="<?php echo $setting['type']; ?>" id="iroikus-input-id-<?php echo $setting_name; ?>-<?php echo $action['action'] . '-' . $webhook; ?>" name="<?php echo $setting_name; ?>" class="wpwh-toggle__input" <?php echo $is_checked; ?> placeholder="<?php echo $placeholder; ?>" value="<?php echo $value; ?>" <?php echo $is_checked; ?> <?php echo $validated_atributes; ?>>
																	<label class="wpwh-toggle__btn" for="iroikus-input-id-<?php echo $setting_name; ?>-<?php echo $action['action'] . '-' . $webhook; ?>"></label>
																</div>
															<?php elseif( $setting['type'] === 'select' && isset( $setting['choices'] ) ) : ?>
																<select 
                                                                class="wpwh-form-input" 
                                                                name="<?php echo $setting_name; ?><?php echo ( isset( $setting['multiple'] ) && $setting['multiple'] ) ? '[]' : ''; ?>" <?php echo $validated_atributes; ?> <?php echo ( isset( $setting['multiple'] ) && $setting['multiple'] ) ? 'multiple' : ''; ?>
                                                                
                                                                <?php if( isset( $setting['query'] ) ) : ?>
                                                                    data-select2-ajax='<?php echo json_encode(array(
                                                                        'action' => 'wp_webhooks_validate_field_query',
                                                                        'webhook_type' => 'action',
                                                                        'webhook_group' => $action['action'],
                                                                        'webhook_integration' => $action['integration'],
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
														<td><?php echo $setting['description']; ?></td>
													</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
										<button
											type="button"
											class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm"

											data-wpwh-event="save"
											data-wpwh-event-type="receive"
											data-wpwh-event-element="wpwhTriggerSettingsModal-<?php echo $webhook; ?>"

											data-webhook-group="<?php echo $action['action']; ?>"
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

<!-- Action argument modals -->
<?php if( ! empty( $actions ) ) : ?>
    <?php foreach( $actions as $identkey => $action ) :
        $is_active = $action['action'] === $active_action;
    ?>

        <?php foreach( $action['parameter'] as $param => $param_data ) :

            if(
                ( ! isset( $param_data['description'] ) || empty( $param_data['description'] ) )
                && ( ! isset( $param_data['choices'] ) || empty( $param_data['choices'] ) )
            ){
                continue;
            }

        ?>
            <div class="modal modal--lg fade" id="wpwhaction-argument-detail-modal-<?php echo $action['action']; ?>-<?php echo $param; ?>" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title"><?php echo __( 'Details for:', 'wp-webhooks' ); ?> <?php echo $param; ?></h3>
                    </div>
                    <div class="modal-body">
                        <?php if( isset( $param_data['description'] ) && ! empty( $param_data['description'] ) ) : ?>
                            <?php echo wpautop( $param_data['description'] ); ?>
                        <?php endif; ?>
                        <?php if( isset( $param_data['type'] ) && $param_data['type'] === 'select' && isset( $param_data['choices'] ) && ! empty( $param_data['choices'] ) ) : ?>
                            <h4><?php echo __( 'Possible values:', 'wp-webhooks' ); ?></h4>
                            <p><?php echo __( 'In this section, you will find all default values defined by us. If you want to use one for this argument, simply copy the part written in <strong>bold</strong>.', 'wp-webhooks' ); ?></p>
                            <ul>
                                <?php foreach( $param_data['choices'] as $choice_slug => $choice_data ) :

                                    $choice_name = $choice_slug;

                                    if( is_array( $choice_data ) && isset( $choice_data['label'] ) ){
                                        $choice_name = $choice_data['label'];
                                    }

                                    if( isset( $param_data['default_value'] ) && $param_data['default_value'] === $choice_slug ){
                                        $choice_name .= ' ' . __( '(default)', 'wp-webhooks' );
                                    }

                                    ?>
                                    <li><strong><?php echo esc_html( $choice_slug ); ?></strong>: <?php echo esc_html( $choice_name ); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    <?php endforeach; ?>
<?php endif; ?>

<?php
//Add the migration popup for older versioned action URLs
foreach( $need_migration as $migration_slug => $migration_data ) :
?>
    <div class="modal fade" id="wpwhMigrateURL-<?php echo $migration_slug; ?>" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title mb-4"><?php echo sprintf( __( 'Migrate Action URL: %s', 'wp-webhooks' ), sanitize_title( $migration_slug ) ); ?></h3>

                    <p class="" style="text-align:center;">
                        <?php echo sprintf( __( 'To finalize the migration, please select the actions down below that should have access to this action URL. After clicking on migrate, we create the action URL for each of your selected actions.', 'wp-webhooks' ), $this->page_title ); ?>
                    </p>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="wpwh-form-label"><?php echo __( 'Migrate to:', 'wp-webhooks' ); ?></label>
                            <select class="wpwh-form-input" name="wpwh_migrate_actions[]" multiple>
                                <?php foreach( $actions as $action_slug => $action_data ) :
                                    $action_slug = sanitize_title( $action_data['action'] );
                                ?>
                                    <option value="<?php echo $action_slug; ?>"><?php echo __( $action_slug, 'wp-webhooks' ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <?php echo WPWHPRO()->helpers->get_nonce_field( $action_nonce_data ); ?>

                        <p class="wpwh-text-small wpwh-text-danger mb-4" style="text-align:center;">
                            <?php echo sprintf( __( 'After clicking on <strong>Migrate</strong>, the migration process is started and cannot be stopped. The migration is irreversible.<br><strong>Please note:</strong> If you have defined the action argument within your data mapping template, please add it to the action URL <strong>BEFORE</strong> migrating (at the end of the URL via <strong>&action=youraction</strong>) as otherwise your template stops working.', 'wp-webhooks' ), $this->page_title ); ?>
                        </p>
                        <input type="hidden" name="wpwh_migrate_action" value="<?php echo $migration_slug; ?>">
                        <input type="submit" name="submit" id="submit-<?php echo $action['action']; ?>" class="wpwh-btn wpwh-btn--secondary w-100" value="<?php echo sprintf( __( 'Migrate', 'wp-webhooks' ), $webhook_name ); ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="wpwhMigrateURLDelete-<?php echo $migration_slug; ?>" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title mb-4"><?php echo sprintf( __( 'Delete Action URL: %s', 'wp-webhooks' ), sanitize_title( $migration_slug ) ); ?></h3>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
                <form action="" method="post">
                    <div class="modal-footer">
                        <?php echo WPWHPRO()->helpers->get_nonce_field( $action_nonce_data ); ?>

                        <p class="wpwh-text-small wpwh-text-danger mb-4" style="text-align:center;">
                            <?php echo sprintf( __( 'Deleting an action URL results in all connected services (other services that send data to that URL) to not work anymore. Deleting the URL is final and irreversible.', 'wp-webhooks' ), $this->page_title ); ?>
                        </p>
                        <input type="hidden" name="wpwh_migrate_action_delete" value="<?php echo $migration_slug; ?>">
                        <input type="submit" name="submit" id="submit-<?php echo $action['action']; ?>" class="wpwh-btn wpwh-btn--secondary w-100" value="<?php echo sprintf( __( 'Delete action URL', 'wp-webhooks' ), $webhook_name ); ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>