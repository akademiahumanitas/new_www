<?php
/**
 * The template for displaying all single posts
 *
 * @Date:   2019-10-15 12:30:02
 * @Last Modified by:   Roni Laukkarinen
 * @Last Modified time: 2022-09-07 11:57:39
 *
 * @package humanitas
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 */

namespace Air_Light;

the_post();
get_header(); 
$exclude = array(get_the_ID());

?>

<main class="site-main">

  <section class="block block-single">
    <article class="article-content">
      <?php get_theme_part('elements/breadcrumbs'); ?>

      <h1><?php the_title(); ?></h1>

      <?php the_content();
        entry_footer();
      ?>
    </article>
  </section>
  <?php 
    // if post type === events 
    if(get_post_type() === 'event') {
      get_theme_part('elements/related-events', [
          'exclude' => $exclude,
      ]); 
    }
    // if post type === books
    if(get_post_type() === 'books') {
      get_theme_part('elements/related-books', [
          'exclude' => $exclude,
      ]); 
    }
  ?>

</main>

<?php get_footer();
