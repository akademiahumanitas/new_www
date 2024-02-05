<?php
get_header(); 


get_theme_part('elements/archive/index', [
    'post_type' => 'post',
]);

get_footer();
