<?php

function highlight_shortcode( $atts, $content = null ) {
    return '<span class="text-highlight">' . $content . '</span>';
}
add_shortcode( 'highlight', 'highlight_shortcode' );



function dot_shortcode( $atts, $content = null ) {
    return '<span class="text-dot">' . $content . '</span>';
}
add_shortcode( 'kropka', 'dot_shortcode' );