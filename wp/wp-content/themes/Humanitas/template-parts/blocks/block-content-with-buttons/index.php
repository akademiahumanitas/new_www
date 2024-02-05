<?php

    $title = get_field( 'section_title' );
    $content = get_field( 'content' );
    $buttons = get_field( 'buttons' );
    $image = get_field( 'image' );
    $block_ID = $block['id'];
    $background_color = get_field( 'background_color' );
    $image_position = get_field( 'image_position' );

?>

<section class="block-content-with-buttons block-content-with-buttons--<?= $background_color;?> block-content-with-buttons--<?= $image_position;?>" id="<?= $block_ID; ?>">
    <?php if($background_color !=='white'): ?>
        <figure class="block-content-with-buttons__triangle">
            <?= get_image('triangle-top-left'); ?>
        </figure>
    <?php endif; ?>
    <div class="container">
        <div class="block-content-with-buttons__wrapper">
            <figure class="block-content-with-buttons__image fade-in">
                <?= get_image( $image, 'full' ); ?>
            </figure>
            <div class="block-content-with-buttons__content">
                <h2 class="block-content-with-buttons__title heading-underline heading-dot fade-in"><?= $title; ?></h3>
                <div class="block-content-with-buttons__text fade-in">
                    <?= $content; ?>
                </div>
                <div class="block-content-with-buttons__buttons js-delay fade-in">
                    <?php foreach ( $buttons as $button ) : ?>
                        <?php get_theme_part('elements/button-card', [
                            'button' => $button['button'],
                            'button_classes' => 'block-content-with-buttons__button js-delay-item',
                            'icon' => $button['icon'],
                            'version' => $background_color === 'white' ? 'tertiary' : 'primary'
                        ]); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php if($background_color !=='white'): ?>
        <figure class="block-content-with-buttons__triangle">
            <?= get_image('triangle-bottom-right'); ?>
        </figure>
    <?php endif; ?>
</section>
