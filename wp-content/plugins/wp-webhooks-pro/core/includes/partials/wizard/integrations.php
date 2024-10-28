<?php

$wpwh_plugin_name   = WPWHPRO()->settings->get_page_title();
$next_step = sanitize_title( WPWHPRO()->wizard->get_next_step() );
$previous_step = sanitize_title( WPWHPRO()->wizard->get_previous_step() );

$previous_step_url = '';
if( ! empty( $previous_step ) ){
    $previous_step_url = WPWHPRO()->helpers->built_url( '', array_merge( $_GET, array( 'wpwhwizard' => $previous_step ) ) );
}

?>
<header class="wpwh-wizard__header">
	<h2><?php echo __( 'Install your integrations', 'wp-webhooks' ); ?></h2>
	<p><?php echo sprintf( __( 'Choose the integrations you would like to use with our plugin. Navigate using our predefined buttons or search it by its name.<br>You can adjust the integrations at any time within %s.', 'wp-webhooks' ), $wpwh_plugin_name ); ?></p>
</header>
<div class="wpwh-separator"></div>

<div class="d-flex justify-content-center wpwh-text-center">
	<?php if( ! empty( $previous_step_url ) ) : ?>
		<a class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm mr-1" href="<?php echo $previous_step_url; ?>">
			<span><?php echo __( 'Previous step', 'wp-webhooks' ); ?></span>
		</a>
	<?php endif; ?>

	<?php if( $next_step === 'complete' ) : ?>
		<button class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm ml-1" type="submit" name="wpwh_wizard_submit">
			<span><?php echo __( 'Complete', 'wp-webhooks' ); ?></span>
		</button>
	<?php else : ?>
		<button class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm ml-1" type="submit" name="wpwh_wizard_submit">
			<span><?php echo __( 'Next step', 'wp-webhooks' ); ?></span>
		</button>
	<?php endif; ?>
</div>

<div class="wpwh-separator"></div>
<div class="wpwh-wizard__main">
	<?php echo do_shortcode( '[WPWH_INTEGRATION_MANAGER columns_size="12"]' ); ?>
</div>