<?php
/**
 * ACF Populate Select Field with Menus
 * @link https://www.advancedcustomfields.com/resources/acf-load_field/
 * @link https://www.advancedcustomfields.com/resources/dynamically-populate-a-select-fields-choices/
 *
 * Dynamically populates any ACF field with wd_nav_menus with list of navigation menus
 *
*/
add_filter( 'acf/load_field/name=secondary_menu', 'wd_nav_menus_load' );
function wd_nav_menus_load( $field ) {

     $menus = wp_get_nav_menus();

     if ( ! empty( $menus ) ) {
          $field['choices'] = [];
          
          foreach ( $menus as $menu ) {
               $field['choices'][ $menu->term_id ] = $menu->name;
          }

     }

     return $field;

}