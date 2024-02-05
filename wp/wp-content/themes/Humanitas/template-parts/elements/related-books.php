<?php
/* 
* Element Related Events
*/

$block_ID = $block_ID ?? 'related-books';
$title = $title ?? __('Mogą cię zainteresować również', 'humanitas');
$exclude = $exclude ?? false;
$version = $version ?? 'primary';
$more_books = $more_books ?? true;
$limit = $limit ?? 10;

$books_args = array(
    'post_type' => 'books',
    'posts_per_page' => $limit,
);

if($exclude) {
    $books_args['post__not_in'] = $exclude;
}

$books = new WP_Query($books_args);
$books = $books->get_posts();
?>
<section class="block-related-books block-related-books--<?= $version;?> fade-in" id="<?= $block_ID; ?>">
    <div class="container">
        <div class="block-related-books__header">
            <h2 class="block-related-books__title heading-underline fade-in"><?= $title; ?></h2>
            <?php if($more_books) : ?>
                <?php get_theme_part('elements/more-link', array('link' => array('url' => get_post_type_archive_link('books'), 'title' => __('Zobacz wszystkie', 'humanitas')))); ?>
            <?php endif; ?>
        </div>
        <div class="block-related-books__slider <?php echo $version === 'primary' ? 'card-slider' : ''?> fade-in js-delay">
            <?php
            foreach ($books as $post) :
                setup_postdata($post);
            ?>
                <?php get_theme_part('elements/book-card',[
                    'post_ID' => $post->ID,
                    'class' => 'block-related-books__slide',
                ]) ?>
            <?php
            endforeach;
            wp_reset_postdata();
            ?>
        </div>
    </div>
</section>