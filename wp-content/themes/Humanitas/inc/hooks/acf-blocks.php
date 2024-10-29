<?php
/**
 * @Author: Timi Wahalahti
 * @Date:   2021-05-11 14:34:14
 * @Last Modified by:   Timi Wahalahti
 * @Last Modified time: 2021-05-26 13:06:36
 * @package humanitas
 */

namespace Air_Light;

function acf_blocks_add_category_in_gutenberg( $categories, $post ) {
  return array_merge( $categories, array(
    array(
      'slug'  => 'humanitas',
      'title' => __( 'Theme blocks', 'humanitas' ),
    ),
  ) );
} // end acf_blocks_add_category_in_gutenberg

function acf_blocks_init() {
  if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
  }

  if ( ! isset( THEME_SETTINGS['acf_blocks'] ) ) {
		return;
  }


  foreach ( THEME_SETTINGS['acf_blocks'] as $block ) {
		// Check if we have added example data via hook
		$screenshot = get_theme_file_path( "template-parts/blocks/{$block['name']}/screenshot.jpg" );

		if ( file_exists( $screenshot ) ) {
			$block['example'] = array(
					'attributes' => array(
					'mode' => 'preview',
					'data' => array('is_example' => true, 'screenshot' => $screenshot),
				),
			);
		}

		// Check if icon is set, otherwise try to load svg icon
		if ( ! isset( $block['icon'] ) || empty( $block['icon'] ) ) {
		  $icon_path = get_theme_file_path( "svg/block-icons/{$block['name']}.svg" );
		  $icon_path = apply_filters( 'air_light_acf_block_icon', $icon_path, $block['name'], $block );

		  if ( file_exists( $icon_path ) ) {
				$block['icon'] = get_acf_block_icon_str( $icon_path );
		  }
			}

		acf_register_block_type( wp_parse_args( $block, THEME_SETTINGS['acf_block_defaults'] ) );
  }
} // end acf_blocks_init

/**
 * Thank you WordPress.org theme repository for not allowing
 * file_get_contents even for local files.
 */
function get_acf_block_icon_str( $icon_path ) {
  if ( ! file_exists( $icon_path ) ) {
		return;
  }

  ob_start();
  include $icon_path;
  return ob_get_clean();
} // end get_acf_block_icon_str
