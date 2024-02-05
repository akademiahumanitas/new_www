<?php
    $title = $title ?? '-not-set-';
    $terms = $terms ?? false;

    if ( $terms ) : ?>

    <div class="block-offer-search__filters fade-in js-delay">
        <p class="block-offer-search__filters-title js-delay-item"><?php echo $title; ?></p>
        <div class="block-offer-search__filters-buttons">
            <?php foreach ( $terms as $term ) : ?>
                <button class="block-offer-search__filters-button button button-small button-ghost js-delay-item" type="button" data-taxonomy="<?php echo $term->taxonomy; ?>" data-term="<?php echo $term->term_id; ?>" data-term-name="<?php echo $term->slug; ?>" aria-label="Filter by <?php echo $term->name; ?>"><?php echo $term->name; ?></button>
            <?php endforeach; ?>
        </div>
    </div>

<?php endif; ?>