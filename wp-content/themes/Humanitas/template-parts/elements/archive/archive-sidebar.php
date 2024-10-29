<?php 
    $terms = get_taxonomy_terms_by_post_type($post_type);
    // how many filters are there in $terms with terms
    $filters_length = count($terms);
    if($terms) :
    ?>
<div class="archive-page__filters-wrapper">
<div class="archive-page__filters">
    <h4 class="archive-page__filters-title">
        <span class="archive-page__filters-heading"><?= __('Filtrowanie', 'humanitas'); ?></span>
        <button class="archive-page__filters-close" aria-label="Zamknij filtry">
            <?= get_image('close'); ?>
        </button>
        <button type="reset" class="archive-page__filters-reset" form="archive-filters">
            <?= __('Wyczyść filtry', 'humanitas'); ?> (<span class="archive-page__filters-number js-selected-filters"></span>)
        </button>
    </h4>
    <form id="archive-filters" class="archive-page__form"
        action="<?= get_post_type_archive_link($post_type); ?>" method="get"
    >
        <input type="hidden" name="post_type" value="<?= $post_type; ?>" />
        <?php foreach ($terms as $taxonomy => $t) : ?>
            <?php get_theme_part('elements/archive-filter',
            [
                'taxonomy' => $taxonomy,
                'terms' => $t,
                'open' => $filters_length === 1 ? true : false,
            ]); ?>
        <?php endforeach; ?>
        <button class="archive-page__form-submit button button-blue" type="submit">
            <?= __('Filtruj', 'humanitas'); ?>
        </button>
    </form>
</div>
</div>
<?php endif; ?>