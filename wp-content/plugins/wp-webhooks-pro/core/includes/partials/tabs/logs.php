<?php


$current_url = WPWHPRO()->helpers->get_current_url( false, true );
$clear_form_url = WPWHPRO()->helpers->get_current_url( true, true );
$log_nonce_data = WPWHPRO()->settings->get_log_nonce();
$log_count = WPWHPRO()->logs->get_log_count();
$logs = null;

// Delete all logs
if( isset( $_POST['wpwhpro_delete_logs'] ) ) {
	if ( check_admin_referer( $log_nonce_data['action'], $log_nonce_data['arg'] ) ) {
		if ( current_user_can( apply_filters( 'wpwhpro/admin/settings/webhook/page_capability', WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-logs-delete-logs' ), array() ) ) ) { //$webhook argument is deprecated
			WPWHPRO()->logs->delete_log();

			echo WPWHPRO()->helpers->create_admin_notice( 'All logs have bee successfully deleted.', 'success', true );
		}
	}
}

// Delete a single log
if( isset( $_GET['wpwhpro_log_delete'] ) ) {
	if ( current_user_can( apply_filters( 'wpwhpro/admin/settings/webhook/page_capability', WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-logs-delete-log' ), array() ) ) ) { //$webhook argument is deprecated
		$log_id = intval( $_GET['wpwhpro_log_delete'] );

		unset( $_GET['wpwhpro_log_delete'] );
		$clear_form_url = str_replace( '&wpwhpro_log_delete=' . $log_id, '', $clear_form_url );

		WPWHPRO()->logs->delete_log( $log_id );

		echo WPWHPRO()->helpers->create_admin_notice( array(
			'Log has been successfully deleted: %s',
			$log_id,
		), 'success', true );
	}
}

$per_page = '';
$current_offset = '';
$per_page = ( isset( $_POST['item_count'] ) && ! empty( $_POST['item_count'] ) ) ? intval( $_POST['item_count'] ) : 10;
$per_page = ( isset( $_GET['item_count'] ) && ! empty( $_GET['item_count'] ) && ! isset( $_POST['item_count'] ) ) ? intval( $_GET['item_count'] ) : $per_page;
$log_page = ( isset( $_GET['log_page'] ) && ! empty( $_GET['log_page'] ) ) ? intval( $_GET['log_page'] ) : 1;
$log_last_page = ceil( $log_count / $per_page );

if( isset( $_POST['item_count'] ) && isset( $_POST['item_offset'] ) || isset( $_GET['log_page'] ) ){

	if( WPWHPRO()->helpers->current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-logs-filter-logs' ), 'wpwhpro-page-logs-filter-logs' ) ){
		if( isset( $_GET['log_page'] ) ){
			if ( current_user_can( apply_filters( 'wpwhpro/admin/settings/webhook/page_capability', WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-logs-paginate-logs' ), array() ) ) ) { //$webhook argument is deprecated
				
				$log_page = intval( $_GET['log_page'] );
				
				$per_page = $per_page;
				$current_offset = ( $log_page - 1 ) * $per_page;
	
				$logs = WPWHPRO()->logs->get_log( $current_offset, $per_page );
			}
		} else {
			if( check_admin_referer( $log_nonce_data['action'], $log_nonce_data['arg'] ) ){
				$current_offset = ( ! empty( $_POST['item_offset'] ) ) ? intval( $_POST['item_offset'] ) : 0;
		
				$logs = WPWHPRO()->logs->get_log( $current_offset, $per_page );
			}
		}
	}

}

if( $logs === null ){
	$logs = WPWHPRO()->logs->get_log();
}

?>
<div class="wpwh-container">
  <div class="wpwh-title-area mb-4">
		<div class="wpwh-title-wrapper d-flex justify-content-between">
			<h2 class="mb-0"><?php echo __( 'Request logs', 'wp-webhooks' ); ?></h2>
			<div class="d-flex align-items-center justify-content-start">
				<form method="post" action="<?php echo $clear_form_url; ?>">
					<?php echo WPWHPRO()->helpers->get_nonce_field( $log_nonce_data ); ?>
					<input type="hidden" name="wpwhpro_delete_logs" value="1">
					<button type="submit" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm"><?php echo __( 'Delete All Logs', 'wp-webhooks' ); ?></button>
				</form>
			</div>
		</div>
		<?php if( WPWHPRO()->whitelabel->is_active() && ! empty( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_logs' ) ) ) : ?>
			<p><?php echo __( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_logs' ), 'wp-webhooks' ); ?></p>
		<?php else : ?>
			<p><?php echo sprintf( __( 'The log feature will log every single request of your website that was triggered either by a trigger or by a valid action. An action is valid once the authentication of the webhook URL was successful. To find out more about a specific log, Check its details with the button on the right.', 'wp-webhooks' ), WPWHPRO()->settings->get_page_title() ); ?></p>
		<?php endif; ?>
  </div>

  <?php 
    
    // Creating an instance
    $table = WPWHPRO()->logs->get_log_lists_table_class();
    // Prepare table
    $table->prepare_items();
    // Display table
    $table->display();

  ?>

</div>