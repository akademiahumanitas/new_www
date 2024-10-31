<?php

    $title = get_field( 'section_title' );
    $content = get_field( 'content' );
    $buttons = get_field( 'buttons' );
    $image = get_field( 'image' );
    $block_ID = $block['id'];
    $background_color = get_field( 'background_color' );
    $button_style = get_field('button_style') ?? 'dark-blue';
    $button = get_field('button');
    $image_position = get_field( 'image_position' );

    if($background_color === 'dark-blue' && str_word_count($title, 0, 'ąćęłńóśźżĄĆĘŁŃÓŚŹŻ') > 1) {
        $title = preg_replace('/\b([\p{L}]+)$/u','<span class="text-highlight">$1</span>', $title);
    }
?>
<section class="block-content-with-buttons block-content-with-buttons--<?= $background_color;?> block-content-with-buttons--<?= $image_position;?>" id="<?= $block_ID; ?>">
    <?php if($background_color !=='white'): ?>
        <?php get_theme_part('elements/triangle', ['position' => 'top-left']); ?>
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
                <?php if($button_style === 'one-button') : ?>
                    <?php get_theme_part('elements/button', [
                        'button' => $button,
                        'button_classes' => 'button-tertiary button-blue'
                    ]); ?>
                <?php endif; ?>
                <div class="block-content-with-buttons__buttons block-content-with-buttons__buttons--<?= $button_style;?> js-delay fade-in">
                    <?php foreach ( $buttons as $button ) : ?>
                        <?php if($button_style === 'with-icons') : ?>
                            <?php get_theme_part('elements/button-card', [
                                'button' => $button['button'],
                                'button_classes' => 'block-content-with-buttons__button js-delay-item',
                                'icon' => $button['icon'],
                                'version' => $background_color === 'white' ? 'tertiary' : 'primary'
                            ]); ?>
                        <?php endif; ?>
                        <?php if($button_style === 'without-icons') : ?>
                            <?php $button_classes = $background_color !== 'dark-blue' ? 'button-tertiary button-blue' : 'button-tertiary'; ?> 
                            <?php get_theme_part('elements/button', [
                                'button' => $button['button'],
                                'button_classes' => 'block-content-with-buttons__button button-small '.$button_classes.' js-delay-item',
                            ]); ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php if($background_color !=='white'): ?>
        <?php get_theme_part('elements/triangle', ['position' => 'bottom-right']); ?>
    <?php endif; ?>
</section>
