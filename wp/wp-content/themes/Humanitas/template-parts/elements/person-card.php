<?php

$post_ID = $post_ID ?? get_the_ID();
$position = get_field('position', $post_ID);
$name = get_the_title($post_ID);
// post thumbnail id
$thumbnail = get_post_thumbnail_id($post_ID);
$phone_number = get_field('phone_number', $post_ID);
$email_address = get_field('email_address', $post_ID);
$class = $class ?? '';
?>
<div class="person-card <?= $class; ?>">
    <figure class="person-card__image">
        <?php if($thumbnail) : ?>
            <?= wp_get_attachment_image($thumbnail, 'full', false, array('class' => 'person-card__image-img')); ?>
        <?php else : ?>
            <?= get_image('user-line'); ?>
        <?php endif; ?>
    </figure>
    <div class="person-card__content">
        <p class="person-card__position"><?= $position; ?></p>
        <h3 class="person-card__name"><?= $name; ?></h3>
        <div class="person-card__contact">
            <?php if ($phone_number) : ?>
                <a href="tel:<?= $phone_number; ?>" class="person-card__contact-item">
                    <span class="person-card__contact-icon">
                        <?= get_image('phone'); ?>
                    </span>
                    <span class="person-card__contact-text"><?= $phone_number; ?></span>
                </a>
            <?php endif; ?>
            <?php if ($email_address) : ?>
                <a href="mailto:<?= $email_address; ?>" class="person-card__contact-item">
                    <span class="person-card__contact-icon">
                        <?= get_image('email'); ?>
                    </span>
                    <span class="person-card__contact-text"><?= $email_address; ?></span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>