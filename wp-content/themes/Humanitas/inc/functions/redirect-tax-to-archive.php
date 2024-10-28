<?php
// redirect page from taxonomy page to archive page with selected taxonomy and term
function redirect_tax_to_archive() {
    if ( (is_tax() && !is_post_type_archive()) || (is_archive() && is_category())) {
        $queried_object = get_queried_object();
        $taxonomy = $queried_object->taxonomy;
        $term_slug = $queried_object->slug;
        // get post type from taxonomy
        $post_type = get_taxonomy($taxonomy)->object_type[0];
        $post_type_archive_link = get_post_type_archive_link($post_type);
        $additional_url = '';

        $additional_url = '?'.$taxonomy.'='.$term_slug;
        $new_url = $post_type_archive_link.$additional_url;
        // echo $new_url;
        wp_redirect( $new_url );
        exit;
    }
}
add_action('template_redirect', 'redirect_tax_to_archive');