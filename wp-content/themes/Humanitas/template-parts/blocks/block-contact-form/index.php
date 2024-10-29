<?php

    $title = get_field('section_title');
    $content = get_field('content');
    $image = get_field('image');
    $form = get_field('form');
    $style = get_field('style');
    $faq = get_field('faq');
    $faq_title = get_field('faq_title');
    $block_id = $block['id'];
?>

<section class="block-contact-form block-contact-form--<?= $style; ?>" id="<?= $block_id; ?>">
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
                <?php if($style === 'with-image'): ?>
                    <figure class="block-contact-form__image">
                        <?php echo wp_get_attachment_image($image, 'full'); ?>
                    </figure>
                <?php endif; ?>
                <?php if($style === 'with-faq'): ?>
                    <?php get_theme_part('blocks/block-faq/index', [
                        'faq' => $faq,
                        'section_title' => $faq_title,
                    ]); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>