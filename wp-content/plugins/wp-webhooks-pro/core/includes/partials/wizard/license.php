<?php

$license_key = WPWHPRO()->settings->get_license('key');
$step_count = intval( WPWHPRO()->wizard->get_current_step_number() );

$license_key_output = '';
if( ! empty( $license_key ) ){
	$license_key_output = $license_key;
}

?>
<header class="wpwh-wizard__header">
	<h2><?php echo sprintf( __( 'Step %d', 'wp-webhooks' ), $step_count ); ?></h2>
	<p><?php echo __( 'Get pro benefits', 'wp-webhooks' ); ?></p>
</header>
<div class="wpwh-separator"></div>
<div class="wpwh-wizard__main">
	<div class="wpwh-form-field">
		<label for="form_1" class="wpwh-form-label"><?php echo __( 'Activate your license', 'wp-webhooks' ); ?></label>
		<p class="wpwh-form-description"><?php echo __( 'To make sure everything works as expected, please activate your plugin license.', 'wp-webhooks' ); ?></p>
		<input type="text" name="wpwh_wizard_license" id="wpwh-wizard-license" class="wpwh-form-input" placeholder="<?php echo __( 'Your license key', 'wp-webhooks' ); ?>" value="<?php echo $license_key_output; ?>">
		<?php if( isset( $_POST['wpwh_wizard_license'] ) && ! empty( $_POST['wpwh_wizard_license'] ) ) : ?>
			<p class="mt-4">
				<?php echo WPWHPRO()->helpers->create_admin_notice( 'An error occured while activating your license. Please make sure your license is active and has enough slots available. If you decide to skip the activation for now, simply leave the license field empty and continue the wizard.', 'error', true ); ?>
			</p>
		<?php endif; ?>
	</div>
</div>