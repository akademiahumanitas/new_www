<?php
/**
 * @Author: Niku Hietanen
 * @Date: 2020-02-18 15:05:35
 * @Last Modified by:   Timi Wahalahti
 * @Last Modified time: 2023-03-31 14:29:17
 *
 * @package humanitas
 */

namespace Air_Light;

/**
 * Registers the Your Taxonomy taxonomy.
 *
 * @param Array $post_types Optional. Post types in
 * which the taxonomy should be registered.
 */
class Your_Taxonomy extends Taxonomy {


  public function register( array $post_types = array() ) {
		// Taxonomy labels.
		$labels = array(
		'name'                  => _x( 'Your Taxonomies', 'Taxonomy plural name', 'humanitas' ),
		'singular_name'         => _x( 'Your Taxonomy', 'Taxonomy singular name', 'humanitas' ),
		'search_items'          => __( 'Search Your Taxonomies', 'humanitas' ),
		'popular_items'         => __( 'Popular Your Taxonomies', 'humanitas' ),
		'all_items'             => __( 'All Your Taxonomies', 'humanitas' ),
		'parent_item'           => __( 'Parent Your Taxonomy', 'humanitas' ),
		'parent_item_colon'     => __( 'Parent Your Taxonomy', 'humanitas' ),
		'edit_item'             => __( 'Edit Your Taxonomy', 'humanitas' ),
		'update_item'           => __( 'Update Your Taxonomy', 'humanitas' ),
		'add_new_item'          => __( 'Add New Your Taxonomy', 'humanitas' ),
		'new_item_name'         => __( 'New Your Taxonomy', 'humanitas' ),
		'add_or_remove_items'   => __( 'Add or remove Your Taxonomies', 'humanitas' ),
		'choose_from_most_used' => __( 'Choose from most used Taxonomies', 'humanitas' ),
		'menu_name'             => __( 'Your Taxonomy', 'humanitas' ),
		);

		$args = array(
		'labels'            => $labels,
		'public'            => false,
		'show_in_nav_menus' => true,
		'show_admin_column' => true,
		'hierarchical'      => true,
		'show_tagcloud'     => false,
		'query_var'         => false,
		'pll_translatable'  => true,
		'rewrite'           => array(
        'slug' => 'your-taxonomy',
		),
		);

		$this->register_wp_taxonomy( $this->slug, $post_types, $args );
  }
}
