<?php
$position = $position ?? 'bottom';
// get all wpml languages 
$languages = icl_get_languages('skip_missing=0&orderby=code');
// $languages = icl_get_languages( 'skip_missing=0&orderby=code' );
echo '<div class="custom-language-switcher">';
if ( ! empty( $languages ) ) {
    foreach ( $languages as $l ) {
        if ( $l['active'] ) {
            echo '<span class="custom-language-switcher__active" href="' . $l['url'] . '" tabIndex="0">';
            echo $l['code'];
            echo get_image( 'chevron-up' );
            echo '</span>';
        }
    }
    echo '<div class="custom-language-switcher__dropdown custom-language-switcher__dropdown--' . $position . '">';
    foreach ( $languages as $l ) {
        if ( ! $l['active'] ) {
            echo '<a class="custom-language-switcher__dropdown-item" href="' . $l['url'] . '">';
            echo $l['native_name'];
            echo '</a>';
        }
    }
    echo '</div>';
}
echo '</div>';
