<?php

function get_tax_query_for_filters($category, $category_names) {
    $category_names = explode(',', $category_names);
    $category_ids = array();
    foreach ($category_names as $name) {
        $term = get_term_by('slug', $name, $category);
        if ($term) {
            $category_ids[] = $term->term_id;
        }
    }
    
    if (!empty($category_ids)) {
        $tax_query = array(
            array(
                'taxonomy' => $category,
                'field'    => 'taxonomy_term_id',
                'terms'    => $category_ids,
                'operator' => 'IN',
            ),
        );
    }

    return $tax_query;
}