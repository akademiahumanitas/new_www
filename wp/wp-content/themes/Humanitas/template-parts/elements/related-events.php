<?php
/* 
* Element Related Events
*/

$block_ID = $block_ID ?? 'related-events';
$title = $title ?? __('Mogą cię zainteresować również', 'humanitas');
$exclude = $exclude ?? false;

$args_for_current = array(
    'post_type' => 'events',
    'posts_per_page' => 10,
    'meta_key' => 'event_date',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'meta_query' => array(
        array(
            'key' => 'event_date',
            'value' => date('Ymd'),
            'compare' => '>=',
            'type' => 'DATE'
        ),
    )
);

$args_for_past = array(
    'post_type' => 'events',
    'posts_per_page' => 10,
    'meta_key' => 'event_date',
    'orderby' => 'meta_value',
    'order' => 'DESC',
    'meta_query' => array(
        array(
            'key' => 'event_date',
            'value' => date('Ymd'),
            'compare' => '<',
            'type' => 'DATE'
        ),
    )
);

if($exclude) {
    $args_for_current['post__not_in'] = $exclude;
    $args_for_past['post__not_in'] = $exclude;
}

$events = new WP_Query($args_for_current);
$old_events = new WP_Query($args_for_past);

$all_events = array_merge($events->posts, $old_events->posts);
?>
<section class="block-related-events" id="<?= $block_ID; ?>">
    <div class="container">
        <div class="block-related-events__header">
            <h2 class="block-related-events__title heading-underline fade-in"><?= $title; ?></h2>
            <?php get_theme_part('elements/more-link', array('link' => array('url' => get_post_type_archive_link('events'), 'title' => __('Zobacz wszystkie', 'humanitas')))); ?>
        </div>
        <div class="block-related-events__slider card-slider fade-in js-delay">
            <?php
            foreach ($all_events as $post) :
                setup_postdata($post);
            ?>
                <?php get_theme_part('elements/event-card',[
                    'post_ID' => $post->ID,
                    'class' => 'block-related-events__slide',
                ]) ?>
            <?php
            endforeach;
            wp_reset_postdata();
            ?>
        </div>
    </div>
</section>