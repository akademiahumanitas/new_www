<?php
/**
 * Gather all bits and pieces together.
 * If you end up having multiple post types, taxonomies,
 * hooks and functions - please split those to their
 * own files under /inc and just require here.
 *
 * @Date: 2019-10-15 12:30:02
 * @Last Modified by:   Roni Laukkarinen
 * @Last Modified time: 2023-09-12 12:20:49
 *
 * @package humanitas
 */

namespace Air_Light;

/**
 * The current version of the theme.
 */
define( 'AIR_LIGHT_VERSION', '9.3.5' );

// We need to have some defaults as comments or empties so let's allow this:
// phpcs:disable Squiz.Commenting.InlineComment.SpacingBefore, WordPress.Arrays.ArrayDeclarationSpacing.SpaceInEmptyArray


/**
 * Theme settings
 */
add_action( 'after_setup_theme', function () {
  $acf_block_names = [
    'acf/block-offer-search',
    'acf/block-hero-slider',
    'acf/block-events',
    'acf/block-articles',
    'acf/block-links',
    'acf/block-card-links',
    'acf/block-content-with-buttons',
    'acf/block-offer-links',
    'acf/block-logo-slider',
    'acf/block-contact-form',
    'acf/block-testimonial',
    'acf/block-hero',
    'acf/block-three-boxes',
    'acf/block-steps-section',
    'acf/block-map'
  ];
  
  $theme_settings = array(
    /**
     * Theme textdomain
     */
    'textdomain' => 'Humanitas',

    /**
     * Content width
     */
    'content_width' => 800,

    /**
     * Logo and featured image
     */
    'default_featured_image'  => null,
    'logo'                    => '/svg/logo.svg',

    /**
     * Custom setting group settings when using Air setting groups plugin.
     * On multilingual sites using Polylang, translations are handled automatically.
     */
    'custom_settings' => array(
      // 'your-custom-setting' => [
      //   'id' => Your custom setting post id,
      //   'title' => 'Your custom setting',
      //   'block-editor' => true,
      //  ],
    ),

    'social_media_accounts'  => array(
      // 'twitter' => [
      //   'title' => 'Twitter',
      //   'url'   => 'https://twitter.com/digitoimistodude',
      // ],
    ),

    /**
     * All links are cheked with JS, if those direct to external site and if,
     * indicator of that is included. Exclude domains from that check in this array.
     */
    'external_link_domains_exclude' => array(
      'localhost:3000',
      'airdev.test',
      'airwptheme.com',
      'localhost',
    ),

    /**
     * Menu locations
     */
    'menu_locations' => array(
      'primary' => __( 'Primary Menu', 'humanitas' ),
    ),

    /**
     * Taxonomies
     *
     * See the instructions:
     * https://github.com/digitoimistodude/humanitas#custom-taxonomies
     */
    'taxonomies' => array(
      // 'Your_Taxonomy' => [ 'post', 'page' ],
    ),

    /**
     * Post types
     *
     * See the instructions:
     * https://github.com/digitoimistodude/humanitas#custom-post-types
     */
    'post_types' => array(
      // 'Your_Post_Type',
    ),

    /**
     * Gutenberg -related settings
     */
    // Register custom ACF Blocks
    'acf_blocks' => array(
      // [
      //   'name'           => 'block-file-slug',
      //   'title'          => 'Block Visible Name',
      //   // You can safely remove lines below if you find no use for them
      //   'prevent_cache'  => false, // Defaults to false,
      //   // Icon defaults to svg file inside svg/block-icons named after the block name,
      //   // eg. svg/block-icons/block-file-slug.svg
      //   //
      //   // Icon setting defines the dashicon equivalent: https://developer.wordpress.org/resource/dashicons/#block-default
      //   // 'icon'  => 'block-default',
      // ],
      array(
        'name'          => 'block-offer-search',
        'title'         => 'Offer Search',
      ),
      array(
        'name'          => 'block-hero-slider',
        'title'         => 'Hero Slider',
      ),
      array(
        'name'          => 'block-events',
        'title'         => 'Events',
      ),
      array(
        'name'          => 'block-articles',
        'title'         => 'Artykuły',
      ),
      array(
        'name'          => 'block-links',
        'title'         => 'Block z linkami',
      ),
      array(
        'name'          => 'block-card-links',
        'title'         => 'Block z boxami',
      ),
      array(
        'name'          => 'block-content-with-buttons',
        'title'         => 'Block z obrazkiem, treścią i przyciskami',
      ),
      array(
        'name'          => 'block-content-with-list',
        'title'         => 'Block z obrazkiem, treścią i listą',
      ),
      array(
        'name'          => 'block-content-with-gallery',
        'title'         => 'Block z treścią i galerią',
      ),
      array(
        'name'          => 'block-content-with-people',
        'title'         => 'Block z treścią i osobami',
      ),
      array(
        'name'          => 'block-content-with-columns',
        'title'         => 'Block z treścią i kolumnami',
      ),
      array(
        'name'          => 'block-content-with-table',
        'title'         => 'Block z treścią i tabelą',
      ),
      array(
        'name'          => 'block-content-with-offers',
        'title'         => 'Block z treścią i ofertami',
      ),
      array(
        'name'          => 'block-offer-links',
        'title'         => 'Lista ofert z linkami',
      ),
      array(
        'name'          => 'block-logo-slider',
        'title'         => 'Logo Slider',
      ),
      array(
        'name'          => 'block-contact-form',
        'title'         => 'Formularz kontaktowy',
      ),
      array(
        'name'          => 'block-testimonial',
        'title'         => 'Pojedyncza Opinia',
      ),
      array(
        'name'          => 'block-hero',
        'title'         => 'Hero',
      ),
      array(
        'name'          => 'block-three-boxes',
        'title'         => 'Trzy boxy',
      ),
      //block-steps-section
      array(
        'name'          => 'block-steps-section',
        'title'         => 'Sekcja z krokami',
      ),
      array(
        'name'          => 'block-map',
        'title'         => 'Block z mapą',
      ),
    ),

    // Custom ACF block default settings
    'acf_block_defaults' => array(
      'category'          => 'humanitas',
      'mode'              => 'preview',
      'align'             => 'wide',
      'post_types'        => array(
        'page', 'oferta'
      ),
      'supports'  => array(
        'align'           => false,
        'anchor'          => true,
        'customClassName' => false,
      ),
      'render_callback'   => __NAMESPACE__ . '\render_acf_block',
    ),

    // Restrict to only selected blocks
    // Set the value to 'all' to allow all blocks everywhere
   'allowed_blocks' => array(
      'default' => array(),
      'post' => array(
        'core/archives',
        'core/audio',
        'core/buttons',
        'core/categories',
        'core/code',
        'core/column',
        'core/columns',
        'core/coverImage',
        'core/embed',
        'core/file',
        'core/freeform',
        'core/gallery',
        'core/heading',
        'core/html',
        'core/image',
        'core/latestComments',
        'core/latestPosts',
        'core/list',
        'core/list-item',
        'core/more',
        'core/nextpage',
        'core/paragraph',
        'core/preformatted',
        'core/pullquote',
        'core/quote',
        'core/block',
        'core/separator',
        'core/shortcode',
        'core/spacer',
        'core/subhead',
        'core/table',
        'core/textColumns',
        'core/verse',
        'core/video',
      ),
      'events' => array(
        'core/archives',
        'core/audio',
        'core/buttons',
        'core/button',
        'core/categories',
        'core/code',
        'core/column',
        'core/columns',
        'core/coverImage',
        'core/embed',
        'core/file',
        'core/freeform',
        'core/gallery',
        'core/heading',
        'core/html',
        'core/image',
        'core/latestComments',
        'core/latestPosts',
        'core/list',
        'core/list-item',
        'core/nextpage',
        'core/paragraph',
        'core/pullquote',
        'core/quote',
        'core/block',
        'core/separator',
        'core/shortcode',
        'core/spacer',
        'core/subhead',
        'core/table',
        'core/textColumns',
        'core/video',
      ),
      'lab' => array(
        'core/archives',
        'core/audio',
        'core/buttons',
        'core/button',
        'core/categories',
        'core/code',
        'core/column',
        'core/columns',
        'core/coverImage',
        'core/embed',
        'core/file',
        'core/freeform',
        'core/gallery',
        'core/heading',
        'core/html',
        'core/image',
        'core/latestComments',
        'core/latestPosts',
        'core/list',
        'core/list-item',
        'core/nextpage',
        'core/paragraph',
        'core/pullquote',
        'core/quote',
        'core/block',
        'core/separator',
        'core/shortcode',
        'core/spacer',
        'core/subhead',
        'core/table',
        'core/textColumns',
        'core/video',
      ),
      'knowledge-base' => array(
        'core/archives',
        'core/audio',
        'core/buttons',
        'core/button',
        'core/categories',
        'core/code',
        'core/column',
        'core/columns',
        'core/coverImage',
        'core/embed',
        'core/file',
        'core/freeform',
        'core/gallery',
        'core/heading',
        'core/html',
        'core/image',
        'core/latestComments',
        'core/latestPosts',
        'core/list',
        'core/list-item',
        'core/nextpage',
        'core/paragraph',
        'core/pullquote',
        'core/quote',
        'core/block',
        'core/separator',
        'core/shortcode',
        'core/spacer',
        'core/subhead',
        'core/table',
        'core/textColumns',
        'core/video',
      ),
    ),

    // If you want to use classic editor somewhere, define it here
    'use_classic_editor' => array(),

    // Add your own settings and use them wherever you need, for example THEME_SETTINGS['my_custom_setting']
    'my_custom_setting' => true,
  );

  $theme_settings = apply_filters( 'air_light_theme_settings', $theme_settings );

  define( 'THEME_SETTINGS', $theme_settings );
} ); // end action after_setup_theme

/**
 * Required files
 */
require get_theme_file_path( '/inc/hooks.php' );
require get_theme_file_path( '/inc/includes.php' );
require get_theme_file_path( '/inc/template-tags.php' );
require get_theme_file_path( '/inc/functions.php' );

// Run theme setup
add_action( 'after_setup_theme', __NAMESPACE__ . '\theme_setup' );
add_action( 'after_setup_theme', __NAMESPACE__ . '\build_theme_support' );
