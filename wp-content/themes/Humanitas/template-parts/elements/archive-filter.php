<?php 
    // file for filter dropdown

    $taxonomy = $taxonomy ?? null;
    $terms = $terms ?? null;
    $open = $open ?? false;

    $taxonomy_name = $taxonomy ? get_taxonomy($taxonomy)->labels->name : null;
    // get taxonomy from custom query args

    if(!$taxonomy || !$terms) {
        return;
    }
    $taxonomy_query = $_GET[$taxonomy] ?? null;
    $taxonomy_query_array = $taxonomy_query ? explode(',', $taxonomy_query) : [];

?>
<div class="filter-select">
    <details class="filter-select__wrapper"<?php echo $taxonomy_query || $open ? 'open' : '';?>>
        <summary class="filter-select__label">
            <span class="filter-select__label-text"><?= $taxonomy_name; ?></span>
            <span class="filter-select__label-icon">
                <?= get_image('chevron-up'); ?>
            </span>
        </summary>
        <div class="filter-select__dropdown">
            <?php foreach ($terms as $term) : 
                $is_checked = $taxonomy_query && in_array($term->slug, $taxonomy_query_array) ? 'checked' : null;
                ?>
                <label class="filter-select__checkbox-label" for="<?= $taxonomy.'-'.$term->term_id;?>">
                    <input type="checkbox" class="filter-select__checkbox" name="<?= $taxonomy; ?>" value="<?= $term->slug; ?>" id="<?= $taxonomy.'-'.$term->term_id;?>"
                        <?php echo $is_checked ? 'checked' : ''; ?> />
                    <?= $term->name; ?>
                </label>
            <?php endforeach; ?>
        </div>
    </details>
</div>
