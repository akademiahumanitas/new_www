<?php
$auth_methods = WPWHPRO()->auth->get_auth_methods();
$authentication_nonce = WPWHPRO()->settings->get_authentication_nonce();
$clear_form_url = WPWHPRO()->helpers->get_current_url( true, true );

if( isset( $_POST['wpwh-authentication-name'] ) && isset( $_POST['wpwh-authentication-type'] ) ){
  if ( check_admin_referer( $authentication_nonce['action'], $authentication_nonce['arg'] ) ) {

    if( WPWHPRO()->helpers->current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-authentication-add-template' ), 'wpwhpro-page-authentication-add-template' ) ){
      $auth_template = isset( $_POST['wpwh-authentication-name'] ) ? sanitize_title( $_POST['wpwh-authentication-name'] ) : '';
      $auth_type = isset( $_POST['wpwh-authentication-type'] ) ? sanitize_title( $_POST['wpwh-authentication-type'] ) : '';

      if( ! empty( $auth_template ) && ! empty( $auth_type ) ){
        $check = WPWHPRO()->auth->add_template( $auth_template, $auth_type );

        if( $check ){
          echo WPWHPRO()->helpers->create_admin_notice( 'The auth template was successfully created.', 'success', true );
        } else {
          echo WPWHPRO()->helpers->create_admin_notice( 'An error occured while creating the template. Please try again.', 'warning', true );
        }
      }
    }

  }
}

?>
<?php add_ThickBox(); ?>
<div class="wpwh-container">
  <div class="wpwh-title-area mb-4">
    <div class="wpwh-title-area-wrapper d-flex mb-4">
      <h1 class="mb-0"><?php echo __( 'Authentication', 'wp-webhooks' ); ?></h1>
      <a href="#" class="wpwh-btn wpwh-btn--sm wpwh-btn--secondary ml-2 d-flex align-items-center justify-content-center" data-toggle="modal" data-target="#addAuthTemplateModal"><?php echo __( 'Create Template', 'wp-webhooks' ); ?></a>
    </div>

    <p>
      <?php if( WPWHPRO()->whitelabel->is_active() && ! empty( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_authentication' ) ) ) : ?>
        <?php echo __( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_authentication' ), 'wp-webhooks' ); ?>
      <?php else : ?>
        <?php echo sprintf(__( 'Create your own authentication template down below. This allows you to authenticate your outgoing "Send Data" webhook triggers to a given endpoint, as well as your incoming "Receive Data" actions. For more information, please check out the authentication documentation by clicking <a class="text-secondary" title="Visit our documentation" href="%s" target="_blank" >here</a>.', 'wp-webhooks' ), 'https://wp-webhooks.com/docs/knowledge-base/how-to-use-authentication/'); ?>
      <?php endif; ?>
    </p>
  </div>

  <?php 
    
    // Creating an instance
    $table = WPWHPRO()->auth->get_auth_lists_table_class();
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
        <h3 class="modal-title"><?php echo __( 'Create Auth Template', 'wp-webhooks' ); ?></h3>
		    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
        </button>
      </div>
      <form action="<?php echo $clear_form_url; ?>" method="post">
        <div class="modal-body">
          <label class="wpwh-form-label" for="wpwh-authentication-name"><?php echo __( 'Template Name', 'wp-webhooks' ); ?></label>
					<input class="wpwh-form-input w-100" type="text" id="wpwh-authentication-name" name="wpwh-authentication-name" placeholder="<?php echo __( 'demo-template', 'wp-webhooks' ); ?>" />

          <label class="wpwh-form-label mt-4" for="wpwh-authentication-type"><?php echo __( 'Auth Type', 'wp-webhooks' ); ?></label>
          <select class="wpwh-form-input w-100" id="wpwh-authentication-type" name="wpwh-authentication-type">
            <?php foreach( $auth_methods as $auth_type => $auth_data ) : ?>
              <option value="<?php echo $auth_type; ?>"><?php echo $auth_data['name']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="modal-footer">
          <?php echo WPWHPRO()->helpers->get_nonce_field( $authentication_nonce ); ?>
					<input type="submit" name="submit" id="submit" class="wpwh-btn wpwh-btn--secondary w-100" value="<?php echo __( 'Create', 'wp-webhooks' ); ?>">
        </div>
      </form>
    </div>
  </div>
</div>