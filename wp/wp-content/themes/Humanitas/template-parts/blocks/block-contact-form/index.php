<?php

    $title = get_field('section_title');
    $content = get_field('content');
    $image = get_field('image');
    $form = get_field('form');
?>

<section class="block-contact-form">
    <div class="container">
        <div class="block-contact-form__wrapper">
            <div class="block-contact-form__left">
                <h2 class="block-contact-form__title heading-underline"><?php echo $title; ?></h2>
                <div class="block-contact-form__content">
                    <?php echo $content; ?>
                </div>
                <?php echo do_shortcode($form); ?>
            </div>
            <div class="block-contact-form__right">
                <figure class="block-contact-form__image">
                    <?php echo wp_get_attachment_image($image, 'full'); ?>
                </figure>
            </div>
        </div>
    </div>