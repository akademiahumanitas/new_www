<?php

function my_pre_get_posts( $query ) {
    if ( ! is_admin() && $query->is_main_query() ) {
        $params = ['category', 'offer_category', 'offer_format', 'offer_location', 'offer_language', 'book_genre', 'books_category', 'knowledge-base-categories'];

        foreach ($params as $param) {
            if (isset($_GET[$param])) {
                $t = get_tax_query_for_filters($param, $_GET[$param]);
                if($t) {
                    $query->set( 'tax_query', $t );
                }
            }
        }
    }
}
add_action( 'pre_get_posts', 'my_pre_get_posts' );