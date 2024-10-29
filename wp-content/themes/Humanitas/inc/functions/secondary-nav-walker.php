<?php
// add icon to menu item link if its target has _blank value

add_filter('nav_menu_link_attributes', function($atts, $item, $args) {
    if ($item->target === '_blank') {
        $atts['aria-label'] = $item->title;
        $atts['title'] = $item->title;
        $atts['target'] = $item->target;
        $atts['rel'] = 'noopener noreferrer';
    }
    return $atts;
}, 10, 3);


// add icon to menu item link if its target has _blank value
add_filter('nav_menu_item_title', function($title, $item, $args, $depth) {
    if ($item->target === '_blank') {
        $title = $title . get_image('arrow-up-right');
    }
    return $title;
}, 10, 4);