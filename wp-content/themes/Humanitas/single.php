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

add_filter( 'render_block_data', 'my_render_block_data' );

?>

<main class="site-main">
  <?php get_theme_part('blocks/block-hero/index', [
    'title' => get_the_title(),
    'decoration' => false
  ]); ?>
  <section class="block block-single">
    <div class="container">
      <article class="single-article">
        <div class="single-article__content">
          <?php the_content();?>
        </div>
        <div class="single-article__sidebar">
          <?php if(get_post_type() === 'events') {
            get_theme_part('elements/event-details'); 
          } ?>
          <div class="single-article__sidebar-wrapper">
            <h4 class="single-article__sidebar-heading"><?= __('Treść artykułu', 'humanitas'); ?></h4>
            <div class="single-article__sidebar-menu">
            <?php 
                global $toc_headings;
                if ( is_array( $toc_headings ) ) {
            
                    foreach ( $toc_headings as $id => $text ) {
                        $toc .= sprintf(
                            '<a href="#%s">%s</a>',
                            esc_attr( $id ),
                            esc_html( $text )
                        );
                    }
            
                  }
                  echo $toc;
                ?>
              </div>
          </div>
        </div>
      </div>
    </article>
  </section>
  <?php 
    // if post type === events 
    if(get_post_type() === 'events') {
      get_theme_part('elements/related-events', [
          'exclude' => $exclude,
      ]); 
    }
    // if post type === books
    if(get_post_type() === 'post' || get_post_type() === 'knowledge-base') {
      get_theme_part('blocks/block-articles/index', [
          'exclude' => $exclude,
          'title' => __('Mogą cię również zainteresować', 'humanitas'),
          'block_style' => 'slider',
          'more_link' => true,
          'limit' => 3,
          'custom_post_type' => get_post_type()
      ]); 
    }
  ?>

</main>

<?php 
remove_filter( 'render_block_data', 'my_render_block_data' );
get_footer();
