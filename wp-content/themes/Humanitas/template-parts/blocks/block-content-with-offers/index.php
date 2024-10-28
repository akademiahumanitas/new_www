<?php

$title = get_field( 'title' );
$content = get_field( 'content' );
$image = get_field( 'image' );
$offers = get_field( 'offers' ); // repeater with offer - offer post type id, title

$block_ID = $block['id'];
?>

<section class="block-content-with-offers" id="<?= $block_ID; ?>">
    <div class="container">
        <div class="block-content-with-offers__wrapper">
            <figure class="block-content-with-offers__image fade-in">
                <?= get_image($image); ?>
            </figure>
            <div class="block-content-with-offers__container">
                <h2 class="block-content-with-offers__title heading-underline heading-dot fade-in"><?= $title; ?></h2>
                <div class="block-content-with-offers__content fade-in">
                    <?= $content; ?>
                </div>
                <div class="block-content-with-offers__offers">
                    <?php foreach($offers as $offer) : ?>
                        <h4 class="block-content-with-offers__offer-title fade-in"><?= $offer['title']; ?></h4>
                        <div class="block-content-with-offers__offer-items fade-in js-delay">
                            <?php foreach($offer['offers'] as $offer_id) : ?>
                                <?php get_theme_part('elements/offer-card', ['post_ID' => $offer_id, 'class' => 'js-delay-item']); ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>