<?php
/* 
* Block Events
*/

$block_ID = $block['id'];
$title = get_field('title');

get_theme_part('elements/related-events', [
    'block_ID' => $block_ID,
    'title' => $title,
]);
?>