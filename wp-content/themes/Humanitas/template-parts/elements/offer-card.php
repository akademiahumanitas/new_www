<?php 
    $post_ID = $post_ID ?? null;

    $title = get_the_title($post_ID);
    $permalink = get_the_permalink($post_ID);
    $the_excerpt = get_the_excerpt($post_ID);
    $thumbnail = get_post_thumbnail_id($post_ID);

    $icon = get_field('icon', $post_ID);
    $duration = get_field('duration', $post_ID);

    // get taxonomy offer_language term selected for this offer
    $offer_language = get_the_terms($post_ID, 'offer_language');
    $offer_location = get_the_terms($post_ID, 'offer_location');
    $offer_type = get_the_terms($post_ID, 'offer_type');
    $offer_format = get_the_terms($post_ID, 'offer_format');
    $class = $class ?? '';
?>
<article class="offer-card <?= $class ?? ''; ?>">
    <a href="<?= $permalink; ?>" 
        class="offer-card__link"
        title="<?= $title; ?>"
        aria-label="<?= $title; ?>"
    >
    <div class="offer-card__header">
        <figure class="offer-card__icon">
            <?= get_image($icon); ?>
        </figure>
        <div class="offer-card__content">
            <h3 class="offer-card__title"><?= $title; ?></h3>
            <p class="offer-card__type">
                <?php if($offer_type) : ?>
                    <?= join(' / ', array_map(function($term) {
                        return $term->name;
                    }, $offer_type)); ?>
                <?php endif; ?>
            </p>
        </div>
    </div>
    <div class="offer-card__information">
        <?php if($duration)  : ?>
            <p class="offer-card__label"><?= get_image('clock'); ?><?= $duration; ?></p>
        <?php endif; ?>
        <?php if($offer_language) : ?>
            <p class="offer-card__label"><?= get_image('globe'); ?>
                <?= join(' / ', array_map(function($term) {
                    return $term->name;
                }, $offer_language)); ?>
            </p>
        <?php endif; ?>
        <?php if($offer_format) : ?>
            <p class="offer-card__label"><?= get_image('buildings'); ?>
                <?= join(' / ', array_map(function($term) {
                    return $term->name;
                }, $offer_format)); ?>
            </p>
        <?php endif; ?>
    </div>
    </a>
</article>