<?php 
    $post_ID = $post_ID ?? null;

    $title = get_the_title($post_ID);
    $permalink = get_the_permalink($post_ID);
    $the_excerpt = get_the_excerpt($post_ID);
    $thumbnail = get_post_thumbnail_id($post_ID);

    // get taxonomy offer_language term selected for this offer
    $offer_language = get_the_terms($post_ID, 'offer_language');
    $offer_location = get_the_terms($post_ID, 'offer_location');
?>
<article class="offer-card <?= $class ?? ''; ?>">
    <a href="<?= $permalink; ?>" 
        class="offer-card__link"
        title="<?= $title; ?>"
        aria-label="<?= $title; ?>"
    >
        <figure class="offer-card__icon">
            <?= get_image(221); ?>
        </figure>
        <div class="offer-card__content">
            <h3 class="offer-card__title"><?= $title; ?></h3>
        </div>
    </a>
</article>