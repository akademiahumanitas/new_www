<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wpwh-container">

    <div class="wpwh-title-area text-center mb-4">
        <h2><?php echo __( 'Integrations', 'wp-webhooks' ); ?></h2>
        <?php if( WPWHPRO()->whitelabel->is_active() && ! empty( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_integrations' ) ) ) : ?>
            <p class="w-50 m-auto"><?php echo __( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_integrations' ), 'wp-webhooks' ); ?></p>
        <?php else : ?>
            <p class="w-50 m-auto"><?php echo sprintf( __( 'Browse through all of the available integrations for %s. Simlpy install them with a single click and start automating your website.', 'wp-webhooks' ), WPWHPRO()->settings->get_page_title() ); ?></p>
        <?php endif; ?>
    </div>

    <?php echo do_shortcode( '[WPWH_INTEGRATION_MANAGER]' ); ?>
</div>