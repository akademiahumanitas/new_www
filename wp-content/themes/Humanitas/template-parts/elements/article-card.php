<?php 
    $post_ID = $post_ID ?? null;

    $title = get_the_title($post_ID);
    $permalink = get_the_permalink($post_ID);
    $the_excerpt = get_the_excerpt($post_ID);
    $thumbnail = get_post_thumbnail_id($post_ID);
    $categories = get_the_category($post_ID);
    $post_type = get_post_type($post_ID);

    if($post_type === 'knowledge-base') {
        $categories = get_the_terms($post_ID, 'knowledge-base-categories');
    }

    $class = $class ? $class.' article-card--'.$version : 'article-card--'.$version;

?>
<article class="article-card article-card--<?=$post_type;?> <?= $class; ?>">
    <a href="<?= $permalink; ?>" 
        class="article-card__link"
        title="<?= $title; ?>"
        aria-label="<?= $title; ?>"
    >
        <figure class="article-card__image">
            <?= get_image($thumbnail, 'medium'); ?>
        </figure>
        <div class="article-card__content">
            <div class="article-card__categories">
                <?php foreach($categories as $category) : ?>
                    <span class="article-card__category"><?= $category->name; ?></span>
                <?php endforeach; ?>
            </div>
            <h3 class="article-card__title"><?= $title; ?></h3>
        </div>
    </a>
</article>