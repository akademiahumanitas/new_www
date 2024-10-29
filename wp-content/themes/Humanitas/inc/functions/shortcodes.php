<?php

function highlight_shortcode( $atts, $content = null ) {
    return '<span class="text-highlight">' . $content . '</span>';
}

function dot_shortcode( $atts, $content = null ) {
    return '<span class="text-dot">' . $content . '</span>';
}

function wpdocs_add_custom_shortcode() {
    add_shortcode( 'highlight', 'highlight_shortcode' );
    add_shortcode( 'kropka', 'dot_shortcode' );
}
add_action( 'init', 'wpdocs_add_custom_shortcode' );