<?php
    $section_title = get_field( 'section_title' );
    $steps = get_field( 'steps' );
    $content = get_field( 'content' );
    $background_color = get_field( 'background_color' );
    $block_ID = $block['id'];
    $show_additional_links = get_field( 'show_additional_links' );
    $additional_links = get_field( 'additional_links' );
    $additional_links_title = get_field( 'additional_links_title' );
    $i = 1;
?>
<section class="block-steps-section block-steps-section--<?= $background_color; ?>" id="<?= $block_ID; ?>">
    <?php if($background_color !=='white'): ?>
        <?php get_theme_part('elements/triangle', ['position' => 'top-right']); ?>
    <?php endif; ?>
    <div class="container">
        <div class="block-steps-section__wrapper">
            <h2 class="block-steps-section__title heading-underline heading-dot fade-in"><?= $section_title; ?></h2>
            <div class="block-steps-section__content fade-in">
                <?= $content; ?>
            </div>
            <div class="block-steps-section__steps">
                <?php foreach ( $steps as $step ) : ?>
                    <div class="block-steps-section__step fade-in">
                        <div class="block-steps-section__step-number"><?= $i; ?></div>
                        <h3 class="block-steps-section__step-title"><?= $step['title']; ?></h3>
                        <div class="block-steps-section__step-content"><?= $step['content']; ?></div>
                    </div>
                <?php $i++; endforeach; ?>
            </div>
            <?php if($show_additional_links) : ?>
                <div class="block-steps-section__additional-links fade-in">
                    <h5 class="block-steps-section__additional-links-title"><?= $additional_links_title; ?></h5>
                    <div class="block-steps-section__additional-links-list js-delay">
                        <?php foreach ( $additional_links as $link ) :
                            $single_link = $link['link'];
                            ?>
                            <?php get_theme_part('elements/button', [
                                'button' => $single_link,
                                'size' => 'small',
                                'button_classes' => 'block-steps-section__additional-link button-secondary js-delay'
                            ]); ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php if($background_color !=='white'): ?>
        <?php get_theme_part('elements/triangle', ['position' => 'bottom-left']); ?>
    <?php endif; ?>
</section>