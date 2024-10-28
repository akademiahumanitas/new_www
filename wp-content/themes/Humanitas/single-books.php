<?php
namespace Air_Light;

the_post();
get_header(); 

?>

<main class="site-main">
    <article>
        <?php get_theme_part('elements/single-book/book-hero'); ?>
        <?php get_theme_part('elements/single-book/book-contents'); ?>
    </article>
</main>

<?php get_footer();
