<?php

$current_url = WPWHPRO()->helpers->get_current_url( false, true );
$data_mapping_url = WPWHPRO()->helpers->built_url( $current_url, array( 'page' => $this->page_name, 'wpwhprovrs' => 'data-mapping' ) );
$logs_url = WPWHPRO()->helpers->built_url( $current_url, array( 'page' => $this->page_name, 'wpwhprovrs' => 'logs' ) );
$flows_logs_url = WPWHPRO()->helpers->built_url( $current_url, array( 'page' => $this->page_name, 'wpwhprovrs' => 'flow-logs' ) );
$authentication_url = WPWHPRO()->helpers->built_url( $current_url, array( 'page' => $this->page_name, 'wpwhprovrs' => 'authentication' ) );
$whitelist_url = WPWHPRO()->helpers->built_url( $current_url, array( 'page' => $this->page_name, 'wpwhprovrs' => 'whitelist' ) );

$logs_is_active = ( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_logs' ) !== 'yes' ) ? true : false;
$flows_logs_is_active = ( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_flow_logs' ) !== 'yes' ) ? true : false;
$auth_is_active = ( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_authentication' ) !== 'yes' ) ? true : false;
$data_mapping_is_active = ( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_data_mapping' ) !== 'yes' ) ? true : false;
$whitelist_is_active = ( WPWHPRO()->whitelist->is_active() && ( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_ip_whitelist' ) !== 'yes' ) ) ? true : false;

?>
<div class="wpwh-container">
  <div class="wpwh-title-area mb-4">
    <h1><?php echo __( 'Features', 'wp-webhooks' ); ?></h1>
    <p>
      <?php if( WPWHPRO()->whitelabel->is_active() && ! empty( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_features' ) ) ) : ?>
        <?php echo __( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_features' ), 'wp-webhooks' ); ?>
      <?php else : ?>
        <?php echo sprintf(__( 'This plugin features a wide range of additional features as seen down below. To learn more about each of them in detail, feel free to check out our <a class="text-secondary" title="Go to the documentation" href="%s" target="_blank" >documentation</a>.', 'wp-webhooks' ), 'https://wp-webhooks.com/docs/article-categories/features/'); ?>
      <?php endif; ?>
    </p>
  </div>

  <?php if( $logs_is_active ) : ?>
    <div class="wpwh-box wpwh-box--big mb-3">
      <div class="wpwh-box__body">
          <h2><?php echo __( 'Request Logs', 'wp-webhooks' ); ?></h2>
          <p class="mb-4"><?php echo sprintf( __( 'Review every request that was sent or received by %s. This is perfect for debugging and to review traffic. Flows are supported as well.', 'wp-webhooks' ), $this->page_title ); ?></p>
          <p class="mb-1">
              <a href="<?php echo $logs_url; ?>" rel="noopener noreferrer" class="text-secondary mr-4"><strong><?php echo __( 'Go to logs', 'wp-webhooks' ); ?></strong></a>
              <a href="https://wp-webhooks.com/docs/article-categories/logs/" target="_blank" rel="noopener noreferrer" class="mr-2"><strong><?php echo __( 'Documentation', 'wp-webhooks' ); ?></strong></a>
          </p>
      </div>
    </div>
  <?php endif; ?>

  <?php if( $flows_logs_is_active ) : ?>
    <div class="wpwh-box wpwh-box--big mb-3">
      <div class="wpwh-box__body">
          <h2><?php echo __( 'Flows Logs', 'wp-webhooks' ); ?></h2>
          <p class="mb-4"><?php echo sprintf( __( 'Check the activity for each of the Flows. Get insights on how many times a Flow run, if everything went well, and much more.', 'wp-webhooks' ), $this->page_title ); ?></p>
          <p class="mb-1">
              <a href="<?php echo $flows_logs_url; ?>" rel="noopener noreferrer" class="text-secondary mr-4"><strong><?php echo __( 'Go to Flows logs', 'wp-webhooks' ); ?></strong></a>
              <a href="https://wp-webhooks.com/docs/article-categories/logs/" target="_blank" rel="noopener noreferrer" class="mr-2"><strong><?php echo __( 'Documentation', 'wp-webhooks' ); ?></strong></a>
          </p>
      </div>
    </div>
  <?php endif; ?>

  <?php if( $data_mapping_is_active ) : ?>
    <div class="wpwh-box wpwh-box--big mb-3">
      <div class="wpwh-box__body">
          <h2><?php echo __( 'Data Mapping', 'wp-webhooks' ); ?></h2>
          <p class="mb-4"><?php echo __( 'This feature allows you to directly manipulate the format and structure of the (payload) data for trigger and action requests.', 'wp-webhooks' ); ?></p>
          <p class="mb-1">
              <a href="<?php echo $data_mapping_url; ?>" rel="noopener noreferrer" class="text-secondary mr-4"><strong><?php echo __( 'Go to data mapping', 'wp-webhooks' ); ?></strong></a>
              <a href="https://wp-webhooks.com/docs/article-categories/data-mapping/" target="_blank" rel="noopener noreferrer" class="mr-2"><strong><?php echo __( 'Documentation', 'wp-webhooks' ); ?></strong></a>
          </p>
      </div>
    </div>
  <?php endif; ?>

  <?php if( $auth_is_active ) : ?>
    <div class="wpwh-box wpwh-box--big mb-3">
      <div class="wpwh-box__body">
          <h2><?php echo __( 'Authentication', 'wp-webhooks' ); ?></h2>
          <p class="mb-4"><?php echo __( 'Add authentication to webhook triggers to communicate with externa, protected endpoints, or use it to protect incoming connections by applying authentication to them.', 'wp-webhooks' ); ?></p>
          <p class="mb-1">
              <a href="<?php echo $authentication_url; ?>" rel="noopener noreferrer" class="text-secondary mr-4"><strong><?php echo __( 'Go to authentication', 'wp-webhooks' ); ?></strong></a>
              <a href="https://wp-webhooks.com/docs/article-categories/authentication/" target="_blank" rel="noopener noreferrer" class="mr-2"><strong><?php echo __( 'Documentation', 'wp-webhooks' ); ?></strong></a>
          </p>
      </div>
    </div>
  <?php endif; ?>

  <?php if( $whitelist_is_active ) : ?>
    <div class="wpwh-box wpwh-box--big mb-3">
      <div class="wpwh-box__body">
          <h2><?php echo __( 'IP Whitelist', 'wp-webhooks' ); ?></h2>
          <p class="mb-4"><?php echo __( 'Protect evey incoming connection by only allowing a set (or range) of whitelisted IP addresses.', 'wp-webhooks' ); ?></p>
          <p class="mb-1">
              <a href="<?php echo $whitelist_url; ?>" rel="noopener noreferrer" class="text-secondary mr-4"><strong><?php echo __( 'Go to IP whitelist', 'wp-webhooks' ); ?></strong></a>
              <a href="https://wp-webhooks.com/docs/article-categories/whitelist/" target="_blank" rel="noopener noreferrer" class="mr-2"><strong><?php echo __( 'Documentation', 'wp-webhooks' ); ?></strong></a>
          </p>
      </div>
    </div>
  <?php endif; ?>

</div>