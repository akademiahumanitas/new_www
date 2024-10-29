<?php

if( ! isset( $auth_id ) || empty( $auth_id ) ){
	wp_die( __( 'The given log is not a valid log.', 'wp-webhooks' ) );
}

$auth_id = intval( $auth_id );
$auth_data = WPWHPRO()->auth->get_template( $auth_id );

if( empty( $auth_data ) ){
	wp_die( __( 'We have problems fetching the log.', 'wp-webhooks' ) );
}

?>
<?php add_ThickBox(); ?>
<div class="wpwh-container">
  <div class="wpwh-title-area mb-4">
    <h1><?php echo __( 'Authentication:', 'wp-webhooks' ) . esc_html( ' #' . $auth_id . ' - ' . $auth_data->name ); ?></h1>
    <p>
      <?php if( WPWHPRO()->whitelabel->is_active() && ! empty( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_authentication' ) ) ) : ?>
        <?php echo __( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_authentication' ), 'wp-webhooks' ); ?>
      <?php else : ?>
        <?php echo sprintf(__( 'Create your own authentication template down below. This allows you to authenticate your outgoing "Send Data" webhook triggers to a given endpoint, as well as your incoming "Receive Data" actions. For more information, please check out the authentication documentation by clicking <a class="text-secondary" title="Visit our documentation" href="%s" target="_blank" >here</a>.', 'wp-webhooks' ), 'https://wp-webhooks.com/docs/knowledge-base/how-to-use-authentication/'); ?>
      <?php endif; ?>
    </p>
  </div>

  <div class="wpwh-box wpwh-box--big mb-3">
    <div class="wpwh-box__body">
      <?php echo WPWHPRO()->auth->get_html_fields_form( $auth_data ); ?>
    </div>
  </div>

</div>

