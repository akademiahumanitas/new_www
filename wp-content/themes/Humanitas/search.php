<?php
get_header(); 

$post_type = get_post_type();

get_theme_part('elements/archive/index', [
    'post_type' => $post_type,
]);

get_footer();
