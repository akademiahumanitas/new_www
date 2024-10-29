<?php

$flows = WPWHPRO()->flows->get_flows();
$flows_nonce = WPWHPRO()->settings->get_flows_nonce();
$clear_form_url = WPWHPRO()->helpers->get_current_url( true, true );
$clean_url = WPWHPRO()->helpers->get_current_url( false, true );

//Create flow
if( isset( $_POST['wpwh-flows-name'] ) ){
  if ( check_admin_referer( $flows_nonce['action'], $flows_nonce['arg'] ) ) {

    if( WPWHPRO()->helpers->current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-flows-add-flow' ), 'wpwhpro-page-flows-add-flow' ) ){
      $flows_template = isset( $_POST['wpwh-flows-name'] ) ? wp_strip_all_tags( sanitize_text_field( $_POST['wpwh-flows-name'] ) ) : '';

      if( ! empty( $flows_template ) ){
        $flow_name = sanitize_title( $flows_template );
  
        $check = WPWHPRO()->flows->add_flow( array(
          'flow_title' => $flows_template,
          'flow_name' => $flow_name,
        ) );
  
          if( ! empty( $check ) && is_numeric( $check ) ){
            
            if( ! headers_sent() ){
              $new_flow_url = WPWHPRO()->helpers->built_url( $clean_url, array_merge( $_GET, array( 'flow_id' => $check, ) ) );
              wp_redirect( $new_flow_url );
              die();
            } else {
              $flows = WPWHPRO()->flows->get_flows( array(), false );
            }
            
          } else {
            echo WPWHPRO()->helpers->create_admin_notice( 'An error occured while creating the flow. Please try again.', 'warning', true );
          }
  
      }
    }

  }
}

//Import flow
if( ! empty( $_FILES['wpwh_flow_import_file'] ) && isset( $_FILES['wpwh_flow_import_file'] ) ){

	if ( check_admin_referer( $flows_nonce['action'], $flows_nonce['arg'] ) ) {

		if( WPWHPRO()->helpers->current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-flows-import-flow-data' ), 'wpwhpro-page-flows-import-flow-data' ) ){

			if ( empty( $_FILES['wpwh_flow_import_file']['size'] ) ) {
				echo WPWHPRO()->helpers->create_admin_notice( 'No file selected.', 'warning', true );
			} else {
				$file = $_FILES['wpwh_flow_import_file'];

				if ( $file['error'] ) {
					echo WPWHPRO()->helpers->create_admin_notice( 'An error occured while uploading the flow template. Please try again.', 'warning', true );
				} else {

					if ( pathinfo( $file['name'], PATHINFO_EXTENSION ) !== 'txt' ) {
						echo WPWHPRO()->helpers->create_admin_notice( 'The uploaded flow template has an incorrect file type.', 'warning', true );
					} else {

						$file_content = file_get_contents( $file['tmp_name'] );

						if( ! WPWHPRO()->helpers->is_json( $file_content ) ){
							echo WPWHPRO()->helpers->create_admin_notice( 'The import file is corrupt and cannot be imported.', 'warning', true );
						} else {

							$json_array = json_decode( $file_content, true );
			
							if ( ! $json_array || ! is_array( $json_array ) ) {
								echo WPWHPRO()->helpers->create_admin_notice( 'The import data cannot be empty.', 'warning', true );
							} else {
                
                //Unset the ID in case given
                if( isset( $json_array['id'] ) ){
                  unset( $json_array['id'] );
                }
                
								$flow_id = WPWHPRO()->flows->add_flow( $json_array );
						
								if( ! empty( $flow_id ) && is_numeric( $flow_id ) ){
									echo WPWHPRO()->helpers->create_admin_notice( array( 'The flow template was successfully imported with the ID #%d', $flow_id ), 'success', true );
								} else {
									echo WPWHPRO()->helpers->create_admin_notice( 'An error occured while creating the flow template.', 'warning', true );
								}
							}

						}

					}

				}
				
			}

		} else {
			echo WPWHPRO()->helpers->create_admin_notice( 'You don\'t have permission to import data.', 'warning', true );
		}
  
	}
}

?>
<?php add_ThickBox(); ?>
<div class="wpwh-container">
  <div class="wpwh-title-area mb-4">
    <div class="wpwh-title-area-wrapper d-flex mb-4">
      <h1 class="mb-0"><?php echo __( 'Flows', 'wp-webhooks' ); ?></h1>
      <a href="#" class="wpwh-btn wpwh-btn--sm wpwh-btn--secondary ml-2 d-flex align-items-center justify-content-center" data-toggle="modal" data-target="#addAuthTemplateModal"><?php echo __( 'Create Flow', 'wp-webhooks' ); ?></a>
      <a href="#" data-tippy="" data-tippy-content="<?php echo __( 'Import a flow.', 'wp-webhooks' ); ?>" class="wpwh-btn wpwh-btn--sm wpwh-btn--primary ml-2 d-flex align-items-center justify-content-center" data-toggle="modal" data-target="#importFlowModal">
        <span class="dashicons dashicons-cloud-upload"></span>
      </a>
    </div>
    
    <p>
      <?php if( WPWHPRO()->whitelabel->is_active() && ! empty( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_flows' ) ) ) : ?>
        <?php echo __( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_flows' ), 'wp-webhooks' ); ?>
      <?php else : ?>
        <?php echo sprintf(__( 'Flows are automation workflows that allows you to do various actions after a specific event happened. To learn more about Flows, please take a look at <a class="text-secondary" title="Visit the Flows documentation" href="%s" target="_blank">our documentation</a>.', 'wp-webhooks' ), 'https://wp-webhooks.com/docs/knowledge-base/how-to-use-wp-webhooks-flows/'); ?>
      <?php endif; ?>
    </p>
  </div>

  <?php 
    
    // Creating an instance
    $table = WPWHPRO()->flows->get_flow_lists_table_class();
    // Prepare table
    $table->prepare_items();
    // Display table
    $table->display();

  ?>

</div>

<div class="modal fade" id="addAuthTemplateModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title"><?php echo __( 'Create Flow', 'wp-webhooks' ); ?></h3>
		    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
        </button>
      </div>
      <form action="<?php echo $clear_form_url; ?>" method="post">
        <div class="modal-body">
          <label class="wpwh-form-label" for="wpwh-flows-name"><?php echo __( 'Flow Name', 'wp-webhooks' ); ?></label>
					<input class="wpwh-form-input w-100" type="text" id="wpwh-flows-name" name="wpwh-flows-name" placeholder="<?php echo __( 'flow-name', 'wp-webhooks' ); ?>" />
        </div>
        <div class="modal-footer">
					<?php wp_nonce_field( $flows_nonce['action'], $flows_nonce['arg'] ); ?>
					<input type="submit" name="submit" id="submit" class="wpwh-btn wpwh-btn--secondary w-100" value="<?php echo __( 'Create', 'wp-webhooks' ); ?>">
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="importFlowModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title"><?php echo __( 'Import Flow', 'wp-webhooks' ); ?></h3>
		    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
        </button>
      </div>
      <form action="<?php echo $clear_form_url; ?>" enctype="multipart/form-data" method="post">
        <div class="modal-body">
          <div class="wpwh-content wpwh-text-small mb-3">
            <?php echo __( 'Import a single flow from a flow template file. Simply drag and drop the .txt file. or select the template after clicking the button.', 'wp-webhooks' ); ?>
          </div>
          <div class="wpwh-file-uploader mt-4 mb-4">
            <input type="file" id="wpwh_flow_import_file" name="wpwh_flow_import_file" accept=".txt">
          </div>
        </div>
        <div class="modal-footer">
					<?php wp_nonce_field( $flows_nonce['action'], $flows_nonce['arg'] ); ?>
					<input type="submit" name="submit" id="submit" class="wpwh-btn wpwh-btn--secondary w-100" value="<?php echo __( 'Import', 'wp-webhooks' ); ?>">
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  jQuery(document).ready(function($) {

    /**
     * Flow:
     *
     * Delete flow template
     */
    $(document).on( "click", ".wpwh-delete-flow-template", function() {

      var $this = $(this);
      var dataTemplateId = $this.data( 'wpwh-auth-id' );
      var wrapperHtml = '';

      if ( dataTemplateId && confirm( "Are you sure you want to delete this template?" ) ) {

        // Prevent from clicking again
        if ( $this.hasClass( 'is-loading' ) ) {
          return;
        }

        $this.addClass( 'is-loading' );
        $this.find('img').animate( { 'opacity': 0 }, 150 );

        $.ajax({
          url: ironikusflows.ajax_url,
          type: 'post',
          data: {
            action: 'ironikus_flows_handler',
            ironikusflows_nonce: ironikusflows.ajax_nonce,
            handler: 'delete_flow',
            language: ironikusflows.language,
            flow_id: dataTemplateId,
          },
          success: function( res ) {

            console.log(res);

            $this.removeClass( 'is-loading' );
            $this.find('img').animate( { 'opacity': 1 }, 150 );

            if ( res[ 'success' ] === 'true' || res[ 'success' ] === true ) {
              $this.closest('tr').remove();

              $('#wpwh-authentication-content-wrapper').html('');
            }
          },
          error: function( errorThrown ) {
            $this.removeClass( 'is-loading' );
            console.log( errorThrown );
          }
        });
      }

    });

    /**
     * Flow:
     *
     * Duplicate flow template
     */
    $(document).on( "click", ".wpwh-duplicate-flow-template", function() {

      var $this = $(this);
      var dataTemplateId = $this.data( 'wpwh-template-id' );
      var wrapperHtml = '';

      if ( dataTemplateId && confirm( "Are you sure you want to duplicate this template?" ) ) {

        // Prevent from clicking again
        if ( $this.hasClass( 'is-loading' ) ) {
          return;
        }

        $this.addClass( 'is-loading' );
        $this.find('img').animate( { 'opacity': 0 }, 150 );

        $.ajax({
          url: ironikusflows.ajax_url,
          type: 'post',
          data: {
            action: 'ironikus_flows_handler',
            ironikusflows_nonce: ironikusflows.ajax_nonce,
            handler: 'duplicate_flow',
            language: ironikusflows.language,
            flow_id: dataTemplateId,
          },
          success: function( res ) {

            console.log(res);

            $this.removeClass( 'is-loading' );
            $this.find('img').animate( { 'opacity': 1 }, 150 );

            if ( res[ 'success' ] === 'true' || res[ 'success' ] === true ) {
              window.location.reload();
            }
          },
          error: function( errorThrown ) {
            $this.removeClass( 'is-loading' );
            console.log( errorThrown );
          }
        });
      }

    });


  });
</script>