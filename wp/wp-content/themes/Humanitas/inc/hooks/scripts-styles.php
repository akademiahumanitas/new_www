<?php
/**
 * Enqueue and localize theme scripts and styles
 *
 * @Author: Niku Hietanen
 * @Date: 2020-02-20 13:46:50
 * @Last Modified by:   Roni Laukkarinen
 * @Last Modified time: 2022-12-29 19:05:21
 *
 * @package humanitas
 */

namespace Air_Light;

/**
 * Move jQuery to footer
 */
function move_jquery_into_footer( $wp_scripts ) {
  if ( ! is_admin() ) {
		$wp_scripts->add_data( 'jquery',         'group', 1 );
		$wp_scripts->add_data( 'jquery-core',    'group', 1 );
		$wp_scripts->add_data( 'jquery-migrate', 'group', 1 );
  }
} // end air_light_move_jquery_into_footer

/**
 * Enqueue scripts and styles.
 */
function enqueue_theme_scripts() {

  // Enqueue global.css
  wp_enqueue_style( 'styles',
    get_theme_file_uri( get_asset_file( 'global.css' ) ),
    array(),
    filemtime( get_theme_file_path( get_asset_file( 'global.css' ) ) )
  );

  // Enqueue jquery and front-end.js
  wp_enqueue_script( 'jquery-core' );
  wp_enqueue_script( 'scripts',
    get_theme_file_uri( get_asset_file( 'front-end.js' ) ),
    array(),
    filemtime( get_theme_file_path( get_asset_file( 'front-end.js' ) ) ),
    true
  );

  wp_localize_script( 'scripts', 'wp', array(
    'ajaxurl' => admin_url( 'admin-ajax.php' ),
  ) );

  // Required comment-reply script
  if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
  }

  wp_localize_script( 'scripts', 'air_light_screenReaderText', array(
    'expand_for'      => get_default_localization( 'Open child menu for' ),
    'collapse_for'    => get_default_localization( 'Close child menu for' ),
    'expand_toggle'   => get_default_localization( 'Open main menu' ),
    'collapse_toggle' => get_default_localization( 'Close main menu' ),
    'external_link'   => get_default_localization( 'External site' ),
    'target_blank'    => get_default_localization( 'opens in a new window' ),
    'previous_slide'  => get_default_localization( 'Previous slide' ),
    'next_slide'      => get_default_localization( 'Next slide' ),
    'last_slide'      => get_default_localization( 'Last slide' ),
    'skip_slider'     => get_default_localization( 'Skip over the carousel element' ),
  ) );

  // Add domains/hosts to disable external link indicators
  wp_localize_script( 'scripts', 'air_light_externalLinkDomains', THEME_SETTINGS['external_link_domains_exclude'] );
} // end air_light_scripts

/**
 * Load polyfills for legacy browsers
 */
function enqueue_polyfills() {
  // Include polyfills
  $script = '
  var supportsES6 = (function () {
  try {
    new Function("(a = 0) => a");
    return true;
  } catch (err) {
    return false;
  }
  }());
  var legacyScript ="' . esc_url( get_theme_file_uri( get_asset_file( 'legacy.js' ) ) ) . '";
  if (!supportsES6) {
    var script = document.createElement("script");
    script.src = legacyScript;
    document.head.appendChild(script);
  }';

  if ( file_exists( get_theme_file_path( get_asset_file( 'legacy.js' ) ) ) ) {
		wp_register_script( 'air_light_legacy', '', array(), filemtime( get_theme_file_path( get_asset_file( 'legacy.js' ) ) ), false );
		wp_enqueue_script( 'air_light_legacy' );
		wp_add_inline_script( 'air_light_legacy', $script, true );
  }
} // end enqueue_polyfills

/**
 * Returns the built asset filename and path depending on
 * current environment.
 *
 * @param string $filename File name with the extension
 * @return string file and path of the asset file
 */
function get_asset_file( $filename ) {
  $env = 'development' === wp_get_environment_type() && ! isset( $_GET['load_production_builds'] ) ? 'dev' : 'prod'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

  $filetype = pathinfo( $filename )['extension'];

  return "${filetype}/${env}/${filename}";
} // end get_asset_file
