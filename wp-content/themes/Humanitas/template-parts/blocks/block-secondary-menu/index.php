<?php

$menu = $menu ?? get_field('secondary_menu');
$secondary_menu_automatic = $secondary_menu_automatic ?? get_field('secondary_menu_automatic');
$show_secondary_menu = $show_secondary_menu ?? get_field('show_secondary_menu');

if($secondary_menu_automatic) {
    // get all gutenberg page blocks
    $blocks = parse_blocks(get_the_content());
    $menu = [];
    foreach ($blocks as $block) {

        if(isset($block['attrs']['data']['show_in_secondary_nav']) && $block['attrs']['data']['show_in_secondary_nav']) {
            $title = $block['attrs']['data']['secondary_nav_title'];
            // make and id from title
            $block_id = sanitize_title($title);
            if($title) {
                $menu[] = [
                    'link' => [
                        'url' => '#' . $block_id,
                        'title' => $title,
                    ],
                ];
            }
        }
    }
}


get_theme_part('elements/secondary-menu', [
    'menu' => $menu,
    'show_secondary_menu' => $show_secondary_menu,
    'automatic'  => $secondary_menu_automatic,
]);
