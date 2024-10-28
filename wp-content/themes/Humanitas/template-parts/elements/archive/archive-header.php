<?php
    $page_title = get_field($post_type.'_section_title', 'options') ?? get_the_archive_title();
    $menu = get_field($post_type.'_secondary_menu', 'option');
    $show_secondary_menu = get_field($post_type.'_show_secondary_menu', 'option');
    $background_image = get_field($post_type.'_background_image', 'option');

    get_theme_part('blocks/block-hero/index', [
    'page_title' => $page_title,
    'menu' => $menu,
    'show_secondary_menu' => $show_secondary_menu,
    'background_image' => $background_image,
    'decoration' => true,
    'secondary_menu_automatic' => false
]); ?>