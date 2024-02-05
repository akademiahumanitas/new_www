<?php

    // need to add wp fragment to load the page with the filters applied
// add wp_ajax to functions.php
// add wp_ajax_nopriv to functions.php

add_action('wp_ajax_archive_filters', 'archive_filters');
add_action( 'wp_ajax_nopriv_archive_filters', 'archive_filters' );

function archive_filters() {
    // get all $_POST data
    $post_data = stripslashes($_POST['data']);
    // data was stringify in the ajax call
    $data = json_decode($post_data);

    $taxonomies = [];
    $tax_query = [];

    
    foreach ($data as $key => $value) {
        if($key !== 'action' && $key !== 'post_type') {
            $taxonomies[$key] = $value;
        }
    }

    // for each taxonomy, check if taxonomy exists
    // if it does, add it to the query

    if($taxonomies) {
        foreach ($taxonomies as $taxonomy => $terms) {
            $tax_query[] = [
                'taxonomy' => $taxonomy,
                'field' => 'slug',
                'terms' => $terms,
            ];
        }
    }

    $wp_query = new WP_Query([
        'post_type' => $data->post_type[0],
        'tax_query' => $taxonomies ?  $tax_query : null,
    ]);

    get_theme_part('elements/archive/archive-content', [
        'new_query' => $wp_query,
    ]);


    wp_die();
}