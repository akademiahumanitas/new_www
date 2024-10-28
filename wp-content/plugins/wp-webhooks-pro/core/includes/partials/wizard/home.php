<?php

$wpwh_plugin_name   = WPWHPRO()->settings->get_page_title();

?>
<header class="wpwh-wizard__header">
	<h2><?php echo sprintf( __( 'Welcome to <strong>%s</strong>!', 'wp-webhooks' ), $wpwh_plugin_name ); ?></h2>
	<p><?php echo __( 'Let\'s get you set up.', 'wp-webhooks' ); ?> 🚀</p>
</header>
<div class="wpwh-separator"></div>
<div class="wpwh-wizard__main">
	<p><?php echo __( 'To make sure you get started in the best possible way, please follow the steps of this wizard carefully. This will help you to configure the plugin without spending time on digging through the settings yourself.', 'wp-webhooks' ); ?></p>
</div>