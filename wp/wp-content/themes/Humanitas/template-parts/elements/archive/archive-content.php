<?php
global $wp_query;

$new_query = $new_query ?? $wp_query;
$post_type = $post_type ?? $new_query->query['post_type'];
$content_title = array(
    'post' => __('Wszystkie aktualności', 'humanitas'),
    'events' => 'Aktualne Wydarzenia',
    'books' => __('Wszystkie Książki', 'humanitas'),
    'oferta' => __('Wszystkie kierunki', 'humanitas'),
);

if($post_type === 'events') {
    $old_events_query = clone $new_query;
    $new_query->set('meta_key', 'event_date');
    $new_query->set('orderby', 'meta_value');
    $new_query->set('order', 'ASC');
    $new_query->set('meta_query', array(
        array(
            'key' => 'event_date',
            'value' => date('Ymd'),
            'compare' => '>=',
            'type' => 'DATE'
        ),
    ));
    $new_query->get_posts();
}

?>
<?php if($content_title[$post_type]) : ?>
<h2 class="archive-page__title"><?= $content_title[$post_type]; ?> 
(<span class="archive-page__title-number"><?php echo $new_query->found_posts; ?></span>)
</h2>
<?php endif; ?>
<?php if ( $new_query->have_posts()) : ?>
    <div class="archive-page__grid archive-page__grid--<?= $post_type;?>">
        <?php while ( $new_query->have_posts() ) : $new_query->the_post(); ?>
            <?php if($post_type === 'events') : ?>
                <?php get_theme_part('elements/event-card', [
                    'post_ID' => $post->ID,
                ]); ?>
            <?php elseif($post_type === 'books') : ?>
                <?php get_theme_part('elements/book-card', [
                    'post_ID' => get_the_ID(),
                ]); 
                ?>
            <?php elseif($post_type === 'oferta') : ?>
                <?php get_theme_part('elements/offer-card', [
                    'post_ID' => get_the_ID(),
                ]); 
                ?>
            <?php elseif($post_type === 'post') : ?>
                <?php get_theme_part('elements/article-card', [
                    'post_ID' => get_the_ID(),
                ]); 
                ?>
            <?php elseif($post_type === 'contact') : ?>
                <?php get_theme_part('elements/contact-card', [
                    'post_ID' => get_the_ID(),
                ]); 
                ?>
            <?php endif; ?>
        <?php endwhile; ?>
    </div>
<?php the_posts_pagination(); ?>
<?php else : ?>
    <p>Brak wyników dla
        <strong><?= get_search_query(); ?></strong>

    </p>
<?php endif; ?>
<?php wp_reset_postdata(); ?>


<?php 
if($post_type === 'events') :

   $old_events_query->set('meta_key', 'event_date');
   $old_events_query->set('orderby', 'meta_value');
   $old_events_query->set('order', 'DESC');
   $old_events_query->set('meta_query', array(
       array(
           'key' => 'event_date',
           'value' => date('Ymd'),
           'compare' => '<',
           'type' => 'DATE'
       ),
   ));
   $old_events_query->get_posts();
   ?>
    <h2 class="archive-page__title"><?= __('Przeszłe wydarzenia', 'humanitas'); ?> 
    (<span class="archive-page__title-number"><?php echo $old_events_query->found_posts; ?></span>)
    </h2>
    <?php if ( $old_events_query->have_posts()) : ?>
        <div class="archive-page__grid archive-page__grid--<?= $post_type;?>">
            <?php while ( $old_events_query->have_posts() ) : $old_events_query->the_post(); ?>
                <?php if($post_type === 'events') : ?>
                    <?php get_theme_part('elements/event-card', [
                        'post_ID' => get_the_ID(),
                    ]); ?>
                <?php endif; ?>
            <?php endwhile; ?>
        </div>
    <?php the_posts_pagination(); ?>
    <?php else : ?>
        <p>Brak wyników </p>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>

<?php endif; ?>