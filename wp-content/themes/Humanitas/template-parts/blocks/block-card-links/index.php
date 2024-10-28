<?php 
/**
 * Block Links as Cards section
 */

$block_ID = $block['id'];
$title = get_field( 'section_title' );
$links = get_field( 'links' );
?>
<section class="block-card-links" id="<?= $block_ID; ?>">
    <div class="container">
        <div class="block-card-links__wrapper">
            <h3 class="block-card-links__title fade-in"><span><?= $title; ?></span></h3>
            <div class="block-card-links__links js-delay fade-in">
                <?php foreach ( $links as $single_link ) :
                        $link = $single_link['link'];
                        $icon = $single_link['icon'];
                    ?>
                        <?php get_theme_part('elements/button-card', [
                            'button' => $link,
                            'icon' => $icon,
                            'button_classes' => 'button-card--secondary',
                        ]); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>