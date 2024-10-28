<?php

$templates = WPWHPRO()->data_mapping->get_data_mapping();
$webhook_actions = WPWHPRO()->webhook->get_hooks( 'action' );
$webhook_triggers = WPWHPRO()->webhook->get_hooks( 'trigger' );
$settings = WPWHPRO()->settings->get_data_mapping_key_settings();
$data_mapping_nonce = WPWHPRO()->settings->get_data_mapping_nonce();
$clear_form_url = WPWHPRO()->helpers->get_current_url( true, true );

$data_mapping_triggers = array();
foreach( $webhook_triggers as $trigger_group => $wt ){
  foreach( $wt as $st => $sd ){
    if( isset( $sd['settings'] ) ){

      if( isset( $sd['settings']['wpwhpro_trigger_data_mapping'] ) ){
        if( ! isset( $data_mapping_triggers[ $trigger_group ] ) ){
          $data_mapping_triggers[ $trigger_group ] = array();
        }

        $data_mapping_triggers[ $trigger_group ][ $st ] = array(
          'name' => sanitize_title( $st ),
          'group' => sanitize_title( $trigger_group ),
          'template' => $sd['settings']['wpwhpro_trigger_data_mapping'],
        );
      }

      if( isset( $sd['settings']['wpwhpro_trigger_data_mapping_response'] ) ){
        if( ! isset( $data_mapping_triggers[ $trigger_group ] ) ){
          $data_mapping_triggers[ $trigger_group ] = array();
        }

        $data_mapping_triggers[ $trigger_group ][ $st ] = array(
          'name' => sanitize_title( $st ),
          'group' => sanitize_title( $trigger_group ),
          'template' => $sd['settings']['wpwhpro_trigger_data_mapping_response'],
        );
      }

      if( isset( $sd['settings']['wpwhpro_trigger_data_mapping_cookies'] ) ){
        if( ! isset( $data_mapping_triggers[ $trigger_group ] ) ){
          $data_mapping_triggers[ $trigger_group ] = array();
        }

        $data_mapping_triggers[ $trigger_group ][ $st ] = array(
          'name' => sanitize_title( $st ),
          'group' => sanitize_title( $trigger_group ),
          'template' => $sd['settings']['wpwhpro_trigger_data_mapping_cookies'],
        );
      }

      if( isset( $sd['settings']['wpwhpro_trigger_data_mapping_header'] ) ){
        if( ! isset( $data_mapping_triggers[ $trigger_group ] ) ){
          $data_mapping_triggers[ $trigger_group ] = array();
        }

        $data_mapping_triggers[ $trigger_group ][ $st ] = array(
          'name' => sanitize_title( $st ),
          'group' => sanitize_title( $trigger_group ),
          'template' => $sd['settings']['wpwhpro_trigger_data_mapping_header'],
        );
      }

    }
  }
}

$data_mapping_actions = array();
foreach( $webhook_actions as $action_name => $wa ){

  if( ! isset( $wa['api_key'] ) || ! is_string( $wa['api_key'] ) ){
    foreach( $wa as $action_slug => $action_data ){
      if( isset( $action_data['settings'] ) ){

        if( ! isset( $data_mapping_actions[ $action_slug ] ) ){
          $data_mapping_actions[ $action_slug ] = array();
        }
    
        if( isset( $action_data['settings']['wpwhpro_action_data_mapping'] ) && ! empty( $action_data['settings']['wpwhpro_action_data_mapping'] ) ){
    
          //An error caused by the Flows feature to save errors as arrays
          if( is_array( $action_data['settings']['wpwhpro_action_data_mapping'] ) && isset( $action_data['settings']['wpwhpro_action_data_mapping'][0] ) ){
            $action_data['settings']['wpwhpro_action_data_mapping'] = $action_data['settings']['wpwhpro_action_data_mapping'][0];
          }
    
          $data_mapping_actions[ $action_slug ][ $action_data['settings']['wpwhpro_action_data_mapping'] ] = array(
            'name' => sanitize_title( $action_slug ),
            'template' => $action_data['settings']['wpwhpro_action_data_mapping'],
          );
        }
    
        if( isset( $action_data['settings']['wpwhpro_action_data_mapping_response'] ) && ! empty( $action_data['settings']['wpwhpro_action_data_mapping_response'] ) ){
    
          //An error caused by the Flows feature to save errors as arrays
          if( is_array( $action_data['settings']['wpwhpro_action_data_mapping_response'] ) && isset( $action_data['settings']['wpwhpro_action_data_mapping_response'][0] ) ){
            $action_data['settings']['wpwhpro_action_data_mapping_response'] = $action_data['settings']['wpwhpro_action_data_mapping_response'][0];
          }
    
          $data_mapping_actions[ $action_slug ][ $action_data['settings']['wpwhpro_action_data_mapping_response'] ] = array(
            'name' => sanitize_title( $action_slug ),
            'template' => $action_data['settings']['wpwhpro_action_data_mapping_response'],
          );
        }
    
        if( empty( $data_mapping_actions[ $action_slug ] ) ){
          unset( $data_mapping_actions[ $action_slug ] );
        }
    
      }
    }
  }

}

if( isset( $_POST['ironikus-template-name'] ) ){
    if ( check_admin_referer( $data_mapping_nonce['action'], $data_mapping_nonce['arg'] ) ) {

      if( WPWHPRO()->helpers->current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-data-mapping-add-template' ), 'wpwhpro-page-data-mapping-add-template' ) ){
        $data_mapping_name = isset( $_POST['ironikus-template-name'] ) ? sanitize_title( $_POST['ironikus-template-name'] ) : '';

        if( ! empty( $data_mapping_name ) ){
          $check = WPWHPRO()->data_mapping->add_template( $data_mapping_name );

          if( $check ){
            $templates = WPWHPRO()->data_mapping->get_data_mapping( 'all', true );
          }

        }
      }

    }
}

?>
<?php add_ThickBox(); ?>

<div class="wpwh-container">
  <div class="wpwh-title-area mb-4">
		<h2><?php echo __( 'Data Mapping', 'wp-webhooks' ); ?></h2>
    <?php if( WPWHPRO()->whitelabel->is_active() && ! empty( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_data_mapping' ) ) ) : ?>
			<p class="wpwh-text-small wpwh-content"><?php echo __( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_data_mapping' ), 'wp-webhooks' ); ?></p>
		<?php else : ?>
			<p class="wpwh-text-small wpwh-content"><?php echo sprintf(__( 'Create your own data mapping templates down below. Mapping the data allows you to redirect certain data keys to new ones to fit the standards of %1$s (For incoming webhook actions) or your external service (For outgoing webhook triggers). For more information, please check out the data mapping documentation by clicking <a href="%2$s" target="_blank" >here</a>.', 'wp-webhooks' ), WPWHPRO()->settings->get_page_title(), 'https://wp-webhooks.com/docs/knowledge-base/how-to-use-data-mapping/'); ?></p>
		<?php endif; ?>
  </div>

  <div class="wpwh-table-container mb-5">
	  <div class="wpwh-table-header d-flex align-items-center justify-content-between">
      <h4 class="mb-0"><?php echo __( 'Templates', 'wp-webhooks' ); ?></h4>
      <button class="wpwh-btn wpwh-btn--sm wpwh-btn--secondary" title="<?php echo __( 'Create Template', 'wp-webhooks' ); ?>" data-toggle="modal" data-target="#wpwhCreateDataMappingTemplateModal"><?php echo __( 'Create Template', 'wp-webhooks' ); ?></button>
	  </div>

    <table class="wpwh-table wpwh-table--sm wpwh-data-mapping-templates">
      <thead>
        <tr>
          <th class="w-10"><?php echo __( 'Id', 'wp-webhooks' ); ?></th>
          <th class="w-20"><?php echo __( 'Name', 'wp-webhooks' ); ?></th>
          <th class="w-20"><?php echo __( 'Date & Time', 'wp-webhooks' ); ?></th>
          <th class="w-20"><?php echo __( 'Connected Triggers', 'wp-webhooks' ); ?></th>
          <th class="w-20"><?php echo __( 'Connected Actions', 'wp-webhooks' ); ?></th>
          <th class="wpwh-text-center"><?php echo __( 'Actions', 'wp-webhooks' ); ?></th>
        </tr>
      </thead>
      <tbody>
				<?php if( ! empty( $templates ) ) : ?>
          <?php foreach( $templates as $template ) :

            $log_time = date( 'F j, Y, g:i a', strtotime( $template->log_time ) );

            ?>
            <tr id="data-mapping-<?php echo $template->id; ?>">
              <td class="align-middle wpwh-text-left"><?php echo $template->id; ?></td>
              <td class="align-middle wpwh-text-left"><?php echo $template->name; ?></td>
              <td class="align-middle wpwh-text-left"><?php echo $log_time; ?></td>
              <td class="align-middle wpwh-text-left">
                <?php
                  if( ! empty( $data_mapping_triggers ) ){
                    $trigger_output = '';
                    foreach( $data_mapping_triggers as $group => $trigger_data ){
                      foreach( $trigger_data as $single_trigger_data ){
                        if( intval( $template->id ) === intval( $single_trigger_data['template'] ) ){
                          $trigger_output .= $single_trigger_data['name'] . ' (' . $single_trigger_data['group'] . ')<br>';
                        }
                      }
                    }

                    echo trim( $trigger_output, '<br>' );
                  }
                ?>
              </td>
              <td class="align-middle wpwh-text-left">
               <?php
                  if( ! empty( $data_mapping_actions ) ){
                    $action_output = '';
                    foreach( $data_mapping_actions as $an => $single_action_data_array ){
                      foreach( $single_action_data_array as $single_action_data ){
                        if( intval( $template->id ) === intval( $single_action_data['template'] ) ){
                          $action_output .= $single_action_data['name'] . '<br>';
                        }
                      }
                    }

                    echo trim( $action_output, '<br>' );
                  }
                ?>
              </td>
              <td class="wpwh-text-center">
                <div class="d-flex align-items-center justify-content-center">
                  <button
                    type="button"
                    class="wpwh-btn wpwh-btn--link px-2 py-1 wpwh-btn--icon wpwh-dm-delete-template-btn"
                    title="<?php echo __( 'Delete', 'wp-webhooks' ); ?>"

                    data-wpwh-mapping-id="<?php echo $template->id; ?>"
                    data-tippy=""
                    data-tippy-content="<?php echo __( 'Delete', 'wp-webhooks' ); ?>"
                  >
                    <img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/delete.svg'; ?>" alt="Delete">
                  </button>
                  <button
                    type="button"
                    class="wpwh-btn wpwh-btn--link px-2 py-1 wpwh-btn--icon wpwh-dm-view-template-btn"

                    data-wpwh-mapping-id="<?php echo $template->id; ?>"
                    data-tippy=""
                    data-tippy-content="<?php echo __( 'Settings', 'wp-webhooks' ); ?>"
                  >
                    <img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/cog.svg'; ?>" alt="<?php echo __( 'Settings', 'wp-webhooks' ); ?>">
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="2" class="wpwh-text-center"><?php echo __( 'You currently don\'t have any data mapping templates available. Please create one first.', 'wp-webhooks' ); ?></td>
					</tr>
				<?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="wpwh-table-container mt-5">
    <div class="wpwh-table-header d-flex align-items-center justify-content-between">
      <h4 class="mb-0"><?php echo __( 'Helpers', 'wp-webhooks' ); ?></h4>
    </div>

    <table class="wpwh-table wpwh-table--sm">
      <thead>
        <tr>
          <th><?php echo __( 'Tag', 'wp-webhooks' ); ?></th>
          <th><?php echo __( 'Used by', 'wp-webhooks' ); ?></th>
          <th><?php echo __( 'Description', 'wp-webhooks' ); ?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td scope="row"><strong>{:</strong>key<strong>:}</strong></td>
          <td>
            <?php echo __( 'Actions', 'wp-webhooks' ); ?>, <?php echo __( 'Triggers', 'wp-webhooks' ); ?>
          </td>
          <td>
            <?php echo __( 'By defining {:some_key:} within a <strong>Data Value</strong> field, it will be replaced by the content of the given key of the response. You can also use multiple of these tags. Example: you get the key first_name and you want to add it to the following string: "This is my first name: MYNAME",  you can do the following: "This is my first name: {:first_name:}" ', 'wp-webhooks' ); ?>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

</div>

<div class="modal fade" id="wpwhCreateDataMappingTemplateModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title"><?php echo __( 'Create Template', 'wp-webhooks' ); ?></h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-tippy data-tippy-placement="left" data-tippy-content="Close without saving">
          <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
      </div>
      <form action="<?php echo $clear_form_url; ?>" method="post">
        <div class="modal-body">
          <label class="wpwh-form-label" for="wpwh_data_mapping_add_template_name"><?php echo __( 'Template Name', 'wp-webhooks' ); ?></label>
          <input class="wpwh-form-input w-100" type="text" id="wpwh_data_mapping_add_template_name" name="ironikus-template-name" placeholder="<?php echo __( 'Enter template name', 'wp-webhooks' ); ?>" />
        </div>
        <div class="modal-footer">
          <?php echo WPWHPRO()->helpers->get_nonce_field( $data_mapping_nonce ); ?>
          <input type="submit" name="submit" id="submit" class="wpwh-btn wpwh-btn--secondary w-100" value="<?php echo __( 'Create', 'wp-webhooks' ); ?>">
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade wpwh-mapping-modal" id="wpwhDataMappingModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title"><?php echo __( 'Create/Edit Template', 'wp-webhooks' ); ?></h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-tippy data-tippy-placement="left" data-tippy-content="Close without saving">
          <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
      </div>
      <div class="modal-body">
        <div class="wpwh-form-field">
          <label class="wpwh-form-label" for="wpwh_data_mapping_template_name"><?php echo __( 'Template Name', 'wp-webhooks' ); ?></label>
          <input class="wpwh-form-input w-100" type="text" id="wpwh_data_mapping_template_name" name="ironikus-template-name" placeholder="<?php echo __( 'Enter template name', 'wp-webhooks' ); ?>" value="" readonly />
        </div>
        <div class="wpwh-data-mapping-wrapper">
          <div class="wpwh-data-editor ui-sortable">
          </div>
          <div class="wpwh-data-mapping-actions">
            <button type="button" class="wpwh-btn wpwh-btn--outline-secondary wpwh-btn--sm wpwh-add-row-button-text">Add Row</button>
            <div class="wpwh-data-mapping-imexport">
              <button type="button" class="wpwh-btn wpwh-btn--link wpwh-btn--sm px-0 text-success wpwh-dm-import-data">
                <strong>Import</strong>
              </button>
              <button type="button" class="wpwh-btn wpwh-btn--link wpwh-btn--sm px-0 text-secondary wpwh-dm-export-data ml-3">
                <strong>Export</strong>
              </button>
              <p class="wpwh-dm-export-data-dialogue" style="display:none !important;"></p>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer text-center">
        <div class="d-flex align-items-center justify-content-center mt-4">
          <button class="wpwh-btn wpwh-btn--link wpwh-btn--sm px-0 wpwh-text-success" data-dismiss="modal" aria-label="close" data-toggle="modal" data-target="#wpwhDataMappingPreviewModal" data-backdrop="static" data-keyboard="false">
            <strong><?php echo __( 'PREVIEW TEMPLATE', 'wp-webhooks' ); ?></strong>
          </button>
          <div class="mx-3"><small><strong>OR</strong></small></div>
          <button type="submit" class="wpwh-dm-save-template-btn wpwh-btn wpwh-btn--secondary" data-wpwh-mapping-id="">
            <span><?php echo __( 'Save Template', 'wp-webhooks' ); ?></span>
          </button>
        </div>
        <div class="d-flex align-items-center justify-content-center mt-4">
          <button class="wpwh-btn wpwh-btn--link wpwh-btn--sm px-0 wpwh-text-success mr-3" data-dismiss="modal" aria-label="close" data-toggle="modal" data-target="#wpwhDataMappingModalSettings" data-backdrop="static" data-keyboard="false">
            <strong><?php echo __( 'TEMPLATE SETTINGS', 'wp-webhooks' ); ?></strong>
          </button>
          <button
            type="button"
            class="wpwh-btn wpwh-btn--link wpwh-btn--sm px-0 wpwh-dm-delete-template-btn wpwh-text-danger ml-3"
            title="<?php echo __( 'Delete', 'wp-webhooks' ); ?>"

            data-wpwh-mapping-id=""
          >
            <strong><?php echo __( 'DELETE TEMPLATE', 'wp-webhooks' ); ?></strong>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade wpwh-mapping-modal" id="wpwhDataMappingModalSettings" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title"><?php echo __( 'Template Settings', 'wp-webhooks' ); ?></h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="modal" data-target="#wpwhDataMappingModal">
          <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
      </div>
      <form id="wpwh-data-mapping-template-settings-form" action="<?php echo $clear_form_url; ?>" method="post">
        <div class="modal-body"></div>
        <div class="modal-footer text-center">
          <button type="button" class="wpwh-btn wpwh-btn--secondary" data-dismiss="modal" aria-label="close" data-toggle="modal" data-target="#wpwhDataMappingModal" data-backdrop="static" data-keyboard="false">
            <span><?php echo __( 'Apply Settings', 'wp-webhooks' ); ?></span>
          </button>
          <div class="d-flex align-items-center justify-content-center mt-4">
            <button class="wpwh-btn wpwh-btn--link wpwh-btn--sm px-0 wpwh-text-success" data-dismiss="modal" aria-label="close" data-toggle="modal" data-target="#wpwhDataMappingModal" data-backdrop="static" data-keyboard="false">
              <strong><?php echo __( 'BACK', 'wp-webhooks' ); ?></strong>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade wpwh-mapping-modal" id="wpwhDataMappingPreviewModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title"><?php echo __( 'Data Mapping Preview', 'wp-webhooks' ); ?></h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="modal" data-target="#wpwhDataMappingModal">
          <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
      </div>
      <div class="modal-body">
        <div class="wpwh-title-area mb-4">
          <?php if( WPWHPRO()->whitelabel->is_active() && ! empty( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_data_mapping_preview' ) ) ) : ?>
            <p class="wpwh-text-small wpwh-content">
            <?php echo __( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_data_mapping_preview' ), 'wp-webhooks' ); ?>
            </p>
          <?php else : ?>
            <p class="wpwh-text-small wpwh-content">
              <?php echo __( 'You can use the preview down below to apply your data mapping template to some given data. This allows you to see instant results for your defined data mapping template. <strong>Please note that the preview uses the currently given data mapping template with all of its unsaved changes.</strong> If you want to check it with the saved changes, simply refresh the page without making changes to the mapping template.', 'wp-webhooks' ); ?>
              <br>
              <?php echo sprintf( __( 'To get started, you can simply include your <strong>JSON-, Query-, or XML-string</strong> down below. <a href="%s" target="_blank">Click here to learn more</a>.', 'wp-webhooks' ), 'https://wp-webhooks.com/docs/knowledge-base/advanced-data-mapping/' ); ?>
            </p>
          <?php endif; ?>
        </div>

        <div class="wpwh-dm-preview">
          <div class="row wpwh-dm-preview__row">
            <div class="col-md-6 wpwh-dm-preview__input-container">
              <h4 class="mb-3"><?php echo __( 'Before Data Mapping', 'wp-webhooks' ); ?> <small class="text-gray">(Input)</small></h4>
              <textarea id="wpwh-data-mapping-preview-input" class="wpwh-dm-preview__input wpwh-form-input w-100 rounded-sm" placeholder="<?php echo __( 'Include your payload here.', 'wp-webhooks' ); ?>"></textarea>
            </div>
            <div class="col-md-6 wpwh-dm-preview__output-container">
              <h4 class="mb-3"><?php echo __( 'After Data Mapping', 'wp-webhooks' ); ?> <small class="text-gray">(Output)</small></h4>
              <pre id="wpwh-data-mapping-preview-output" class="wpwh-dm-preview__output"></pre>
            </div>
          </div>

          <div class="d-flex align-items-center">
            <a href="#" class="wpwh-btn wpwh-btn--sm wpwh-btn--outline-secondary wpwh-dm-preview__submit-btn" data-mapping-type="trigger"><span><?php echo __( 'Apply for outgoing data', 'wp-webhooks' ); ?></span></a>
            <a href="#" class="wpwh-btn wpwh-btn--sm wpwh-btn--outline-primary wpwh-dm-preview__submit-btn ml-3" data-mapping-type="action"><span><?php echo __( 'Apply for incoming data', 'wp-webhooks' ); ?></span></a>
          </div>
        </div>
      </div>
      <div class="modal-footer text-center">
        <div class="d-flex align-items-center justify-content-center mt-4">
          <button class="wpwh-btn wpwh-btn--link wpwh-btn--sm px-0 wpwh-text-success" data-dismiss="modal" aria-label="close" data-toggle="modal" data-target="#wpwhDataMappingModal" data-backdrop="static" data-keyboard="false">
            <strong><?php echo __( 'BACK', 'wp-webhooks' ); ?></strong>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
var wpwhDataMappingSettings = <?php echo json_encode( $settings, JSON_HEX_QUOT | JSON_HEX_TAG ); ?>;
</script>

<?php if ( ! empty( $templates ) && false ): ?>
  <?php foreach ( $templates as $template ): ?>
    <div class="modal fade wpwh-mapping-modal" id="wpwhDataMappingModal-<?php echo $template->id; ?>" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title"><?php echo __( 'Create/Edit Template', 'wp-webhooks' ); ?></h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-tippy data-tippy-placement="left" data-tippy-content="Close without saving">
              <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>
          </div>
          <div class="modal-body">
            <div class="wpwh-form-field">
              <label class="wpwh-form-label" for="wpwh_data_mapping_template_name-<?php echo $template->id; ?>"><?php echo __( 'Template Name', 'wp-webhooks' ); ?></label>
              <input class="wpwh-form-input w-100" type="text" id="wpwh_data_mapping_template_name-<?php echo $template->id; ?>" name="ironikus-template-name" placeholder="<?php echo __( 'Enter template name', 'wp-webhooks' ); ?>" value="<?php echo $template->name; ?>" readonly />
            </div>
            <div class="wpwh-data-mapping-wrapper">
              <div class="wpwh-data-editor ui-sortable">
                <div class="wpwh-empty ui-sortable-handle">Add a row to get started!</div>
              </div>
              <div class="wpwh-data-mapping-actions">
                <button type="button" class="wpwh-btn wpwh-btn--outline-secondary wpwh-btn--sm wpwh-add-row-button-text">Add Row</button>
                <div class="wpwh-data-mapping-imexport">
                  <button type="button" class="wpwh-btn wpwh-btn--link wpwh-btn--sm px-0 text-success wpwh-dm-import-data">
                    <strong>Import</strong>
                  </button>
                  <button type="button" class="wpwh-btn wpwh-btn--link wpwh-btn--sm px-0 text-secondary wpwh-dm-export-data ml-3">
                    <strong>Export</strong>
                  </button>
                  <p class="wpwh-dm-export-data-dialogue" style="display:none !important;"></p>
                </div>
              </div>
              <div class="wpwh-data-mapping-key-settings d-none">
                <?php
                  echo json_encode( $settings, JSON_HEX_QUOT | JSON_HEX_TAG );
                ?>
              </div>
            </div>
          </div>
          <div class="modal-footer text-center">
            <button type="submit" class="wpwh-dm-save-template-btn wpwh-btn wpwh-btn--secondary" data-wpwh-mapping-id="<?php echo $template->id; ?>">
              <span><?php echo __( 'Save Template', 'wp-webhooks' ); ?></span>
            </button>
            <div class="d-flex align-items-center justify-content-center mt-4">
              <button class="wpwh-btn wpwh-btn--link wpwh-btn--sm px-0 wpwh-text-success mr-3" data-dismiss="modal" aria-label="close" data-toggle="modal" data-target="#wpwhDataMappingModalSettings-<?php echo $template->id; ?>">
                <strong><?php echo __( 'TEMPLATE SETTINGS', 'wp-webhooks' ); ?></strong>
              </button>
              <button
                type="button"
                class="wpwh-btn wpwh-btn--link wpwh-btn--sm px-0 wpwh-dm-delete-template-btn wpwh-text-danger ml-3"
                title="<?php echo __( 'Delete', 'wp-webhooks' ); ?>"

                data-wpwh-mapping-id="<?php echo $template->id; ?>"
              >
                <strong><?php echo __( 'DELETE TEMPLATE', 'wp-webhooks' ); ?></strong>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade wpwh-mapping-modal" id="wpwhDataMappingModalSettings-<?php echo $template->id; ?>" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title"><?php echo __( 'Template Settings', 'wp-webhooks' ); ?></h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-tippy data-tippy-placement="left" data-tippy-content="Close without saving">
              <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>
          </div>
          <form id="wpwhDataMappingModalSettingsForm-<?php echo $template->id; ?>" action="<?php echo $clear_form_url; ?>" method="post">
            <div class="modal-body">
              <p><?php echo __( 'Check this settings item to only send over the keys defined within this template (Whitelist) or every key except of the ones in this template. This way, you can prevents unnecessary data to be sent over via the endpoint. To only map a key without modifications, simply define the same key as the new key and assign the same key again. E.g.: user_email -> user_email', 'wp-webhooks' ); ?></p>
              <label class="wpwh-form-label" for="wpwhpro_data_mapping_whitelist_payload"><?php echo __( 'Whitelist/Blacklist Payload', 'wp-webhooks' ); ?></label>
              <select class="wpwh-form-input w-100" id="wpwhpro_data_mapping_whitelist_payload" name="wpwhpro_data_mapping_whitelist_payload">
                <option value="none"><?php echo __( 'Choose..', 'wp-webhooks' ); ?></option>
                <option value="whitelist"><?php echo __( 'Whitelist', 'wp-webhooks' ); ?></option>
                <option value="blacklist"><?php echo __( 'Blacklist', 'wp-webhooks' ); ?></option>
              </select>
            </div>
            <div class="modal-footer">
              <button type="submit" class="wpwh-btn wpwh-btn--secondary w-100">
                <span><?php echo __( 'Apply Settings', 'wp-webhooks' ); ?></span>
              </button>
              <div class="d-flex align-items-center justify-content-center mt-5">
                <button class="wpwh-btn wpwh-btn--link wpwh-btn--sm px-0 wpwh-text-success ml-3" data-dismiss="modal" aria-label="close" data-toggle="modal" data-target="#wpwhDataMappingModal-<?php echo $template->id; ?>">
                  <strong><?php echo __( 'BACK', 'wp-webhooks' ); ?></strong>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>