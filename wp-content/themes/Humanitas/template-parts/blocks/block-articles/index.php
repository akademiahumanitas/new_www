<?php
    $title = $title ?? get_field('section_title');
    $article_category = get_field('article_category');
    $knowledge_base = get_field('knowledge_base');
    $block_style = $block_style ?? get_field('block_style');
    $block_ID = $block['id'];
    $exclude = $exclude ?? array();
    $more_link = $more_link ?? true;
    $limit = $limit ?? 8;
    $custom_post_type = $custom_post_type ?? 'post';
    $post_type = get_field('post_type') ?? $custom_post_type;
    $background_color = get_field('background_color') ?? 'light-blue';
    $content = get_field('content');
    $cat = $post_type === 'post' ? $article_category : $knowledge_base;

    if($block_style === 'columns') {
        $limit = 4;
    }

    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => 8,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
    );

    if($limit) {
        $args['posts_per_page'] = $limit;
    }

    if($block_style === 'featured') {
        $args['posts_per_page'] = 5;
    }

    if($exclude) {
        $args['post__not_in'] = $exclude;
    }

    if($article_category && $post_type === 'post') {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'category',
                'field' => 'term_id',
                'terms' => $article_category,
            ),
        );
    }

    if($knowledge_base && $post_type === 'knowledge-base') {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'knowledge-base-categories',
                'field' => 'term_id',
                'terms' => $knowledge_base,
            ),
        );
    }

    $archive_link = get_post_type_archive_link($post_type);
    // if $article_category is set, it can be multiple categories, so we add taxonomy=term,term_2 to the link
    if($cat) {
        foreach($cat as $category) {
            // $category is an id of taxonomy term, need to get taxonomy name and term slug
            $term = get_term($category);
            $archive_link = add_query_arg($term->taxonomy, $term->slug, $archive_link);
        }
    }

    $articles = new WP_Query($args);

?>

<section class="block-articles block-articles--<?= $block_style; ?> block-articles--<?= $background_color; ?>" id="<?= $block_ID; ?>">
<?php if($block_style === 'featured' && $background_color === 'light-blue') : ?>
    <?php get_theme_part('elements/triangle', ['position' => 'top-right']); ?>
<?php endif; ?>
    <div class="container">
        <div class="block-articles__header">
            <h2 class="block-articles__title heading-underline fade-in"><?= $title; ?></h2>
            <?php if($more_link) : ?>
                <?php get_theme_part('elements/more-link', array('link' => array('url' => $archive_link, 'title' => __('Zobacz wszystkie', 'humanitas')))); ?>
            <?php endif; ?>
        </div>
        <?php if($content) : ?>
            <div class="block-articles__content fade-in">
                <?= $content; ?>
            </div>
        <?php endif; ?>
                    <?php if($block_style !== 'featured') : ?>
        <div class="block-articles__slider card-slider fade-in js-delay"
            <?php if($limit <= 4) : ?>
                data-slick='{"slidesToShow": <?= $limit; ?>}'
            <?php endif; ?>
        >
            <?php
            foreach ($articles->posts as $post) :
                setup_postdata($post);
            ?>
                <?php get_theme_part('elements/article-card',[
                    'post_ID' => $post->ID,
                    'class' => 'block-articles__slide',
                    'post_type' => $post_type,
                ]) ?>
            <?php
            endforeach;
            wp_reset_postdata();
            ?>
        </div>
        <?php else : ?>
            <div class="block-articles__grid">
                <?php
                $i = 0;
                foreach ($articles->posts as $post) :
                    setup_postdata($post);
                    $i++;
                ?>
                    <?php if($i === 1) : ?>
                        <div class="block-articles__featured">
                            <?php get_theme_part('elements/article-card',[
                                'post_ID' => $post->ID,
                                'class' => 'block-articles__featured-card',
                                'version' => 'featured',
                                'post_type' => $post_type,
                            ]) ?>
                        </div>
                        <div class="block-articles__posts">
                    <?php else : ?>
                        <?php get_theme_part('elements/article-card',[
                            'post_ID' => $post->ID,
                            'class' => 'block-articles__card',
                            'version' => 'horizontal'
                        ]) ?>
                    <?php endif; ?>
                <?php
                endforeach;
                wp_reset_postdata();
                ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php if($block_style === 'featured' && $background_color === 'light-blue') : ?>
        <?php get_theme_part('elements/triangle', ['position' => 'bottom-left']); ?>
    <?php endif; ?>
</section>