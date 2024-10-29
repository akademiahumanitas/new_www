<?php

function enqueue_slick_assets() {
    // Register and enqueue slick.css
    wp_enqueue_style('slick', get_template_directory_uri() . '/css/vendor/slick/slick.css');

    // Register and enqueue slick-theme.css
    wp_enqueue_style('slick-theme', get_template_directory_uri() . '/css/vendor/slick/slick-theme.css');

    // Register and enqueue slick.min.js
    wp_enqueue_script('slick', get_template_directory_uri() . '/js/vendor/slick.js', array('jquery'), false, true);
}
add_action('wp_enqueue_scripts', 'enqueue_slick_assets', 1);

// include slick also in the admin panel
add_action('admin_enqueue_scripts', 'enqueue_slick_assets', 1);