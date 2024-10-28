<?php

$license_key = WPWHPRO()->settings->get_license('key');
$step_count = intval( WPWHPRO()->wizard->get_current_step_number() );
$wpwh_plugin_name   = WPWHPRO()->settings->get_page_title();

$license_key_output = '';
if( ! empty( $license_key ) ){
	$license_key_output = $license_key;
}

?>
<header class="wpwh-wizard__header">
	<h2><?php echo sprintf( __( 'Step %d', 'wp-webhooks' ), $step_count ); ?></h2>
	<p class="wpwh-text-danger"><?php echo __( 'Migration required', 'wp-webhooks' ); ?></p>
</header>
<div class="wpwh-separator"></div>
<div class="wpwh-wizard__main">
	<p><?php echo sprintf( __( 'Since version 5.0, action URLs (Receive Data) got migrated to an action layer, allowing better handling and security. Since you have been using %s prior to that (Thanks a lot!), you need to migrate your URLs to the new structure. Please note: Your URLs continue to work if you havent migrated them yet, however, you cannot edit them anymore until after the migration.', 'wp-webhooks' ), $wpwh_plugin_name ); ?></p>
	<p class="wpwh-text-danger">
		<strong>
			<?php echo sprintf( __( 'You can upgrade your action URLs after the Wizard within the "Receive Data" tab.', 'wp-webhooks' ), $wpwh_plugin_name ); ?>
		</strong>
	</p>
</div>