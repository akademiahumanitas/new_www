<?php

    $contact_box = $contact_box ?? get_field('contact_box');

    // contact_box is an array of values, explode it
    $image = $contact_box['image'];
    $subtitle = $contact_box['subtitle'];
    $title = $contact_box['title'];
    $text = $contact_box['text'];
    $link = $contact_box['link'];
    $version = $version ?? 'primary';
?>

<div class="contact-box contact-box--<?= $version; ?>">
    <figure class="contact-box__image">
        <?= get_image($image, 'contact-box'); ?>
    </figure>
    <div class="contact-box__content">
        <?php if($subtitle) : ?>
            <h4 class="contact-box__subtitle"><?= $subtitle; ?></h4>
        <?php endif; ?>
        <?php if($title) : ?>
            <h3 class="contact-box__title"><?= $title; ?></h3>
        <?php endif; ?>
        <?php if($text) : ?>
            <div class="contact-box__text"><?= $text; ?></div>
        <?php endif; ?>
        <?php if($link) : ?>
            <?php get_theme_part('elements/button', [
                'button' => $link,
                'button_classes' => 'contact-box__button',
                'icon'  => $version ==='secondary' ? '' : 'arrow-up-right',
                'icon_position' => 'right',
                'color' => $version === 'primary' ? 'yellow' : 'white button-tertiary'
            ]); ?>
        <?php endif; ?>
    </div>
</div>