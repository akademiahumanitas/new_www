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
	<p><?php echo __( 'Useful information', 'wp-webhooks' ); ?></p>
</header>
<div class="wpwh-separator"></div>
<div class="wpwh-wizard__main">

	<div class="wpwh-form-field">
		<p><?php echo sprintf( __( 'Thank you for being a part of %s. Down below, you will find more useful links that help you getting started.', 'wp-webhooks' ), $wpwh_plugin_name ); ?></p>
	</div>

	<div class="wpwh-form-field">
		<label for="form_1" class="wpwh-form-label"><?php echo __( 'Join the community', 'wp-webhooks' ); ?></label>
		<div class="wpwh-form-description">
			<p>
				<?php echo __( 'A big part of WP Webhooks is our community, and we would love to see you there as well.', 'wp-webhooks' ); ?>
			</p>

			<p class="mb-4">
				<a href="https://www.facebook.com/groups/wordpress.automation/" target="_blank" rel="noopener noreferrer" class="text-facebook mr-2"><strong><?php echo __( 'Join our Facebook group', 'wp-webhooks' ); ?></strong></a>
				<a href="https://wp-webhooks.com/#newsletter" target="_blank" rel="noopener noreferrer" class="text-success mr-2"><strong><?php echo __( 'Newsletter', 'wp-webhooks' ); ?></strong></a>
			</p>
		</div>
	</div>

	<div class="wpwh-form-field">
		<label for="form_1" class="wpwh-form-label"><?php echo __( 'Support & help', 'wp-webhooks' ); ?></label>
		<div class="wpwh-form-description">
			<p>
				<?php echo __( 'Sometimes, things can be a bit challenging, but we are here to help. Simpy follow the links below for more information.', 'wp-webhooks' ); ?>
			</p>

			<p class="mb-4">
				<a href="https://wp-webhooks.com/get-help/" target="_blank" rel="noopener noreferrer" class="text-facebook mr-2"><strong><?php echo __( 'Get help', 'wp-webhooks' ); ?></strong></a>
				<a href="https://wp-webhooks.com/docs/" target="_blank" rel="noopener noreferrer" class="text-success mr-2"><strong><?php echo __( 'Documentation', 'wp-webhooks' ); ?></strong></a>
				<a href="https://wp-webhooks.com/visit/youtube" target="_blank" rel="noopener noreferrer" class=""><strong><?php echo __( 'YouTube', 'wp-webhooks' ); ?></strong></a>
			</p>
		</div>
	</div>

	<div class="wpwh-form-field pd-4">
		<label for="form_1" class="wpwh-form-label"><?php echo __( 'Suggestions and bugs', 'wp-webhooks' ); ?></label>
		<div class="wpwh-form-description">
			<p><?php echo __( 'Our plugin is made for you. That\'s why we value your feedback more than everything else. In case you ever find a bug or crave for a feature, we are more than happy to help.', 'wp-webhooks' ); ?></p>
			<p class="mb-4">
				<a href="https://wp-webhooks.com/contact/?custom-subject=I%20would%20like%20to%20suggest%20a%20feature" target="_blank" rel="noopener noreferrer" class="text-secondary mr-2"><strong><?php echo __( 'Suggest feature', 'wp-webhooks' ); ?></strong></a>
				<a href="https://wp-webhooks.com/contact/?custom-subject=I%20would%20like%20to%20report%20a%20bug" target="_blank" rel="noopener noreferrer" class="text-success mr-2"><strong><?php echo __( 'Report bug', 'wp-webhooks' ); ?></strong></a>
				<a href="https://wp-webhooks.com/contact/?custom-subject=Contact%20us" target="_blank" rel="noopener noreferrer" class="text-instagram"><strong><?php echo __( 'Contact us', 'wp-webhooks' ); ?></strong></a>
			</p>
		</div>
	</div>
</div>