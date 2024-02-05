<?php 
    $taxonomies = get_object_taxonomies($post_type);
    $terms = [];

    foreach ($taxonomies as $taxonomy) {
        if($taxonomy !== 'translation_priority') {
            $terms[$taxonomy] = get_terms([
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
            ]);
        }
    }

    if($terms) :
    ?>
<div class="archive-page__filters">
    <h4 class="archive-page__filters-title">
        <?= __('Filtrowanie', 'humanitas'); ?>
        <button type="reset" class="archive-page__filters-reset" form="archive-filters">
            <?= __('Wyczyść filtry', 'humanitas'); ?> (<span class="archive-page__filters-number"></span>)
        </button>
    </h4>
    <form id="archive-filters" class="archive-page__form"
        action="<?= get_post_type_archive_link($post_type); ?>" method="get"
    >
        <input type="hidden" name="post_type" value="<?= $post_type; ?>" />
        <?php foreach ($terms as $taxonomy => $terms) : ?>
            <?php get_theme_part('elements/archive-filter',
            [
                'taxonomy' => $taxonomy,
                'terms' => $terms,
            ]); ?>
        <?php endforeach; ?>
        <button class="archive-page__form-submit button button-blue" type="submit">
            <?= __('Filtruj', 'humanitas'); ?>
        </button>
    </form>
</div>
<?php endif; ?>