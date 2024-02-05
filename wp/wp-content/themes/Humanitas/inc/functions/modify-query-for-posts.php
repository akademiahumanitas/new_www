<?php

function my_pre_get_posts( $query ) {
    if ( ! is_admin() && $query->is_main_query() ) {
        if (isset($_GET['category'])) {
            $category_names = explode(',', $_GET['category']);
            $category_ids = array();
            foreach ($category_names as $name) {
                $term = get_term_by('name', $name, 'category');
                if ($term) {
                    $category_ids[] = $term->term_id;
                }
            }
            if (!empty($category_ids)) {
                $query->set( 'tax_query', array(
                    array(
                        'taxonomy' => 'category',
                        'field'    => 'term_id',
                        'terms'    => $category_ids,
                    ),
                ));
            }
        }
    }
}
add_action( 'pre_get_posts', 'my_pre_get_posts' );