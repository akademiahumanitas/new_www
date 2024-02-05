<?php

    $testimonial = get_field('testimonial');
    $author = get_field('author');
    $image = get_field('background_image');
    $block_id = get_field('block_id');
?>
<section class="block-testimonial" id="<?= $block_id; ?>">
    <?php get_theme_part('elements/triangle', [
        'position' => 'top-right',
    ]); ?>
    <figure class="block-testimonial__background-image fade-in">
        <?php echo wp_get_attachment_image($image, 'full'); ?>
    </figure>
    <div class="container">
        <div class="block-testimonial__wrapper">
            <blockquote class="block-testimonial__quote fade-in">
                <?= $testimonial; ?>
                <cite><?= $author; ?></cite>
            </blockquote>
        </div>
    </div>
    <?php get_theme_part('elements/triangle', [
        'position' => 'bottom-left',
    ]); ?>
</section>