<?php

function get_taxonomy_terms_by_post_type($post_type) {
    $taxonomies = get_object_taxonomies($post_type);
    $terms = [];

    foreach ($taxonomies as $taxonomy) {
        if($taxonomy !== 'translation_priority' && $taxonomy !== 'post_format') {
             $taxonomy_terms = get_terms([
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
            ]);
            if($taxonomy_terms) {
                $terms[$taxonomy] = $taxonomy_terms;
            }
        }
    }

    return $terms;
}