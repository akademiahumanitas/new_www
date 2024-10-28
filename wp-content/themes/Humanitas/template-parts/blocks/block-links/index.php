<?php 
/**
 * Block Links section
 */

$block_ID = $block['id'];
$title = get_field( 'section_title' );
$links = get_field( 'links' );
?>
<section class="block-links" id="<?= $block_ID; ?>">
    <div class="container">
        <div class="block-links__wrapper">
            <h3 class="block-links__title fade-in"><span><?= $title; ?></span></h3>
            <div class="block-links__links js-delay fade-in">
                <?php foreach ( $links as $single_link ) :
                        $link = $single_link['link'];
                    ?>
                        <?php get_theme_part('elements/button', [
                            'button' => $link,
                            'color' => 'secondary',
                            'size' => 'small',
                            'button_classes' => 'block-links__button js-delay-item',
                            'icon' => 'arrow-up-right',
                            'icon_position' => 'right'
                        ]); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
