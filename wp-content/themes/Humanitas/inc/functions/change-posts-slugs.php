<?php

function add_rewrite_rules( $wp_rewrite ) {

    $new_rules = array(
        'aktualnosci/page/([0-9]+)/?$' => 'index.php?post_type=post&page=' . $wp_rewrite->preg_index(1),
        'aktualnosci/(.+?)/?$'         => 'index.php?post_type=post&name=' . $wp_rewrite->preg_index(1),
    );

    $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
}
add_action('generate_rewrite_rules', 'add_rewrite_rules');

function change_aktualnosci_links($post_link, $id=0){

    $post = get_post($id);

    if( is_object($post) && $post->post_type == 'post'){
        return home_url('/aktualnosci/'. $post->post_name.'/');
    }

    return $post_link;
}
add_filter('post_link', 'change_aktualnosci_links', 1, 3);
