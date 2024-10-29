<?php
// edit post labels
function change_post_labels() {
    global $wp_post_types;
    $labels = &$wp_post_types['post']->labels;
    $labels->name = __('Aktualności', 'humanitas');
    $labels->singular_name = __('Artykuł', 'humanitas');
    $labels->add_new = __('Dodaj artykuł', 'humanitas');
    $labels->add_new_item = __('Dodaj artykuł', 'humanitas');
    $labels->edit_item = __('Edytuj artykuł', 'humanitas');
    $labels->new_item = __('Nowy artykuł', 'humanitas');
    $labels->view_item = __('Zobacz artykuł', 'humanitas');
    $labels->search_items = __('Szukaj artykułów', 'humanitas');
    $labels->not_found = __('Nie znaleziono artykułów', 'humanitas');
    $labels->not_found_in_trash = __('Nie znaleziono artykułów w koszu', 'humanitas');
    $labels->all_items = __('Wszystkie Aktualności', 'humanitas');
    $labels->menu_name = __('Aktualności', 'humanitas');
    $labels->name_admin_bar = __('Aktualności', 'humanitas');
}
add_action( 'init', 'change_post_labels' );