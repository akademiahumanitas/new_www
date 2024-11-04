<?php

    $title = get_field( 'section_title' );
    $content = get_field( 'content' );
    $button = get_field( 'button' );
    $gallery = get_field( 'gallery' );
    $block_ID = $block['id'];
    $background_color = get_field( 'background_color' );
    $image_position = get_field( 'image_position' );
    $button_color = $background_color === 'dark-blue' ? 'button-white' : 'button-blue';

    if($background_color === 'dark-blue' && str_word_count($title, 0, 'ąćęłńóśźżĄĆĘŁŃÓŚŹŻ') > 1) {
        $title = preg_replace('/\b([\p{L}]+)$/u','<span class="text-highlight">$1</span>', $title);
    }

    $max_chars = 400; // Set the maximum number of characters
    $truncated_content = mb_substr($content, 0, $max_chars) . (strlen($content) > $max_chars ? '...' : '');

?>
<section class="block-content-with-gallery block-content-with-gallery--<?= $background_color;?> block-content-with-gallery--<?= $image_position;?>" id="<?= $block_ID; ?>">
    <?php if($background_color !=='white'): ?>
        <?php get_theme_part('elements/triangle', ['position' => 'top-left']); ?>
    <?php endif; ?>
    <div class="container">
        <div class="block-content-with-gallery__wrapper">
            <div class="block-content-with-gallery__gallery js-delay fade-in">
                <?php foreach ( $gallery as $item ) : ?>
                    <?php if($item['is_video']): ?>
                    <figure class="block-content-with-gallery__item block-content-with-gallery__item--video js-delay-item">
                            <?= $item['video']; ?>
                    </figure>
                    <?php else: ?>
                        <figure class="block-content-with-gallery__item js-delay-item">
                            <?= get_image($item['image']); ?>
                        </figure>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="block-content-with-gallery__content">
                <h2 class="block-content-with-gallery__title heading-underline heading-dot fade-in"><?= $title; ?></h3>
                <div class="block-content-with-gallery__text fade-in">
                    <div class="block-content-with-gallery__text-truncated">
                        <?= $truncated_content; ?>
                    </div>
                    <div class="block-content-with-gallery__text-full" style="display: none;">
                        <?= $content; ?>
                    </div>
                </div>
                <?php if (strlen($content) > $max_chars): ?>
                    <button class="block-content-with-gallery__read-more fade-in">Read More</button>
                <?php endif; ?>
                <?php if($button) : ?>
                    <?php get_theme_part('elements/button', [
                            'button' => $button,
                            'button_classes' => 'button-tertiary fade-in '.$button_color
                        ]); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php if($background_color !=='white'): ?>
        <?php get_theme_part('elements/triangle', ['position' => 'bottom-right']); ?>
    <?php endif; ?>
</section>
