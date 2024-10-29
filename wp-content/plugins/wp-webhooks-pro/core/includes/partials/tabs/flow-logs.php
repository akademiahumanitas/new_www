<?php
/**
 * The Flow logs template
 */
?>
<div class="wpwh-container">
  <div class="wpwh-title-area mb-4">
		<h2><?php echo __( 'Flows logs', 'wp-webhooks' ); ?></h2>
		<?php if( WPWHPRO()->whitelabel->is_active() && ! empty( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_logs' ) ) ) : ?>
			<p><?php echo __( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_logs' ), 'wp-webhooks' ); ?></p>
		<?php else : ?>
			<p><?php echo sprintf( __( 'Once a specific flow was fired, it will create a log entry within this page. You can see the details by clicking on the name of the logs. Alternatively, you can resend a specific log based on the given trigger data.', 'wp-webhooks' ), WPWHPRO()->settings->get_page_title() ); ?></p>
		<?php endif; ?>
  </div>

<?php 
    
    // Creating an instance
    $table = WPWHPRO()->flows->get_flow_logs_lists_table_class();
    // Prepare table
    $table->prepare_items();
    // Display table
    $table->display();

?>

</div>