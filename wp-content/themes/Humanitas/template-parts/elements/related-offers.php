<?php
/* 
* Element Related Events
*/

$block_ID = $block_ID ?? 'related-oferta';
$title = $title ?? __('Mogą cię również zainteresować', 'humanitas');
$exclude = $exclude ?? false;
$more_link = $more_link ?? true;
$limit = $limit ?? 10;

$books_args = array(
    'post_type' => 'oferta',
    'posts_per_page' => $limit,
);

if($exclude) {
    $books_args['post__not_in'] = $exclude;
}

$books = new WP_Query($books_args);
$books = $books->get_posts();

?>
<section class="block-related-offers fade-in" id="<?= $block_ID; ?>">
    <div class="container">
        <div class="block-related-offers__header">
            <h3 class="block-related-offers__title <?php echo $more_link ? 'heading-underline' : '';?> fade-in"><?= $title; ?></h3>
            <?php if($more_link) : ?>
                <?php get_theme_part('elements/more-link', array('link' => array('url' => get_post_type_archive_link('books'), 'title' => __('Zobacz wszystkie', 'humanitas')))); ?>
            <?php endif; ?>
        </div>
        <div class="block-related-offers__slider card-slider fade-in js-delay"
            data-slick='{"slidesToShow": 3, "slidesToScroll": 1}'
        >
            <?php
            foreach ($books as $post) :
                setup_postdata($post);
            ?>
                <?php get_theme_part('elements/offer-card',[
                    'post_ID' => $post->ID,
                    'class' => 'block-related-offers__slide',
                ]) ?>
            <?php
            endforeach;
            wp_reset_postdata();
            ?>
        </div>
    </div>
</section>