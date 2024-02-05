<?php
get_header(); 

$post_type = $wp_query->query['post_type'];

get_theme_part('elements/archive/index', [
    'post_type' => $post_type,
]);

get_footer();
