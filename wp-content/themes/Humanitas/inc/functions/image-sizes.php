<?php

// add custom image sizes
add_action('after_setup_theme', function() {
  add_image_size('hero', 1094, 600, true);
});

add_filter( 'option_image_default_link_type', fn () => 'file' );