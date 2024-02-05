<?php
    $footer_socials = get_field( 'footer_socials', 'option' );
    $facebook = $footer_socials['facebook'];
    $instagram = $footer_socials['instagram'];
    $linkedin = $footer_socials['linkedin'];
    $youtube = $footer_socials['youtube'];

?>

<div class="social-media">
    <?php if ( $facebook ) : ?>
        <a href="<?php echo $facebook['url']; ?>" title="<?php echo $facebook['title']; ?>" target="<?php echo $facebook['target']; ?>" class="social-media__item no-external-link-indicator">
            <?php echo get_image( 'facebook' ); ?>
        </a>
    <?php endif; ?>
    <?php if ( $linkedin ) : ?>
        <a href="<?php echo $linkedin['url']; ?>" title="<?php echo $linkedin['title']; ?>" target="<?php echo $linkedin['target']; ?>" class="social-media__item no-external-link-indicator">
            <?php echo get_image( 'linkedin' ); ?>
        </a>
    <?php endif; ?>
    <?php if ( $instagram ) : ?>
        <a href="<?php echo $instagram['url']; ?>" title="<?php echo $instagram['title']; ?>" target="<?php echo $instagram['target']; ?>" class="social-media__item no-external-link-indicator">
            <?php echo get_image( 'instagram' ); ?>
        </a>
    <?php endif; ?>
    <?php if ( $youtube ) : ?>
        <a href="<?php echo $youtube['url']; ?>" title="<?php echo $youtube['title']; ?>" target="<?php echo $youtube['target']; ?>" class="social-media__item no-external-link-indicator">
            <?php echo get_image( 'youtube' ); ?>
        </a>
    <?php endif; ?>
</div>