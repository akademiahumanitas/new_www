<?php

    $title = get_field('section_title');
    $article_category = get_field('article_category');
    $block_style = get_field('block_style');
    $block_ID = $block['id'];

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 8,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
    );

    if($block_style === 'featured') {
        $args['posts_per_page'] = 5;
    }

    if($article_category) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'category',
                'field' => 'term_id',
                'terms' => $article_category,
            ),
        );
    }

    $archive_link = get_post_type_archive_link('post');
    // if $article_category is set, it can be multiple categories, so we add taxonomy=term,term_2 to the link
    if($article_category) {
        foreach($article_category as $category) {
            // $category is an id of taxonomy term, need to get taxonomy name and term slug
            $term = get_term($category);
            $archive_link = add_query_arg($term->taxonomy, $term->slug, $archive_link);
        }
    }

    $articles = new WP_Query($args);

?>

<section class="block-articles block-articles--<?= $block_style; ?>" id="<?= $block_ID; ?>">
<?php if($block_style === 'featured') : ?>
    <?php get_theme_part('elements/triangle', ['position' => 'top-right']); ?>
<?php endif; ?>
    <div class="container">
        <div class="block-articles__header">
            <h2 class="block-articles__title heading-underline fade-in"><?= $title; ?></h2>
            <?php get_theme_part('elements/more-link', array('link' => array('url' => $archive_link, 'title' => __('Zobacz wszystkie', 'humanitas')))); ?>
        </div>
        <?php if($block_style === 'slider') : ?>
        <div class="block-articles__slider card-slider fade-in js-delay">
            <?php
            foreach ($articles->posts as $post) :
                setup_postdata($post);
            ?>
                <?php get_theme_part('elements/article-card',[
                    'post_ID' => $post->ID,
                    'class' => 'block-articles__slide',
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
                                'version' => 'featured'
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
    <?php if($block_style === 'featured') : ?>
        <?php get_theme_part('elements/triangle', ['position' => 'bottom-left']); ?>
    <?php endif; ?>
</section>