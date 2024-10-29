<?php
    $post_ID = $post_ID ?? get_the_ID();
    $title = $title ?? get_the_title($post_ID);
    $link = $link ?? get_the_permalink($post_ID);
    $icon = get_field('icon', $post_ID);
    $short_description = get_field('short_description', $post_ID);
?>
<a href="<?= $link; ?>" class="contact-card">
    <div class="contact-card__icon">
        <?php if($icon) : ?>
            <?= get_image($icon); ?>
        <?php endif; ?>
    </div>
    <div class="contact-card__content">
        <p class="contact-card__title"><?= $title; ?></p>
        <h3 class="contact-card__short-description"><?= $short_description; ?><?= get_image('arrow-up-right'); ?></h3>
    </div>
</a>
