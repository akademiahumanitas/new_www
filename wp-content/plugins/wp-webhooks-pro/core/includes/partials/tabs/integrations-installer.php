<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wpwh-container">

    <div class="wpwh-title-area text-center mb-4">
        <h2><?php echo __( 'Get your integrations', 'wp-webhooks' ); ?></h2>
        <?php if( WPWHPRO()->whitelabel->is_active() && ! empty( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_integrations' ) ) ) : ?>
            <p class="w-50 m-auto"><?php echo __( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_integrations' ), 'wp-webhooks' ); ?></p>
        <?php else : ?>
            <p class="w-50 m-auto"><?php echo sprintf( __( 'To get started, please select the integrations you want to use. To install and activate it, simply click on the "Install" button and you are ready to go. If you would like to add more at a later point, you can always do that using the "Integrations" tab.', 'wp-webhooks' ), WPWHPRO()->settings->get_page_title() ); ?></p>
        <?php endif; ?>
        <p><div class="wpwh-btn wpwh-btn--secondary" onclick="window.location.reload();">Done?</div></p>
    </div>

    <?php echo do_shortcode( '[WPWH_INTEGRATION_MANAGER]' ); ?>
</div>