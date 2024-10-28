<?php
/**
 * @Author: Timi Wahalahti
 * @Date:   2019-12-03 11:03:31
 * @Last Modified by:   Roni Laukkarinen
 * @Last Modified time: 2022-12-29 19:05:36
 *
 * @package humanitas
 */

namespace Air_Light;

add_filter( 'air_helper_pll_register_strings', function () {
  $strings = array(
    // 'Key: String' => 'String',
  );

  /**
   * Uncomment if you need to have default humanitas accessibility strings
   * translatable via Polylang string translations.
   */
  // foreach ( get_default_localization_strings() as $key => $value ) {
  // $strings[ "Accessibility: {$key}" ] = $value;
  // }

  return $strings;
} );

function get_default_localization_strings( $language = 'en' ) {
  $strings = array(
    'en'  => array(
      'Add a menu'                                   => __( 'Add a menu', 'humanitas' ),
      'Open main menu'                               => __( 'Open main menu', 'humanitas' ),
      'Close main menu'                              => __( 'Close main menu', 'humanitas' ),
      'Main navigation'                              => __( 'Main navigation', 'humanitas' ),
      'Back to top'                                  => __( 'Back to top', 'humanitas' ),
      'Open child menu for'                          => __( 'Open child menu for', 'humanitas' ),
      'Close child menu for'                         => __( 'Close child menu for', 'humanitas' ),
      'Skip to content'                              => __( 'Skip to content', 'humanitas' ),
      'Skip over the carousel element'               => __( 'Skip over the carousel element', 'humanitas' ),
      'External site'                                => __( 'External site', 'humanitas' ),
      'opens in a new window'                        => __( 'opens in a new window', 'humanitas' ),
      'Page not found.'                              => __( 'Page not found.', 'humanitas' ),
      'The reason might be mistyped or expired URL.' => __( 'The reason might be mistyped or expired URL.', 'humanitas' ),
      'Search'                                       => __( 'Search', 'humanitas' ),
      'Block missing required data'                  => __( 'Block missing required data', 'humanitas' ),
      'This error is shown only for logged in users' => __( 'This error is shown only for logged in users', 'humanitas' ),
      'No results found for your search'             => __( 'No results found for your search', 'humanitas' ),
      'Edit'                                         => __( 'Edit', 'humanitas' ),
      'Previous slide'                               => __( 'Previous slide', 'humanitas' ),
      'Next slide'                                   => __( 'Next slide', 'humanitas' ),
      'Last slide'                                   => __( 'Last slide', 'humanitas' ),
    ),
    'fi'  => array(
      'Add a menu'                                   => 'Luo uusi valikko',
      'Open main menu'                               => 'Avaa päävalikko',
      'Close main menu'                              => 'Sulje päävalikko',
      'Main navigation'                              => 'Päävalikko',
      'Back to top'                                  => 'Siirry takaisin sivun alkuun',
      'Open child menu for'                          => 'Avaa alavalikko kohteelle',
      'Close child menu for'                         => 'Sulje alavalikko kohteelle',
      'Skip to content'                              => 'Siirry suoraan sisältöön',
      'Skip over the carousel element'               => 'Hyppää karusellisisällön yli seuraavaan sisältöön',
      'External site'                                => 'Ulkoinen sivusto',
      'opens in a new window'                        => 'avautuu uuteen ikkunaan',
      'Page not found.'                              => 'Hups. Näyttää, ettei sivua löydy.',
      'The reason might be mistyped or expired URL.' => 'Syynä voi olla virheellisesti kirjoitettu tai vanhentunut linkki.',
      'Search'                                       => 'Haku',
      'Block missing required data'                  => 'Lohkon pakollisia tietoja puuttuu',
      'This error is shown only for logged in users' => 'Tämä virhe näytetään vain kirjautuneille käyttäjille',
      'No results for your search'                   => 'Haullasi ei löytynyt tuloksia',
      'Edit'                                         => 'Muokkaa',
      'Previous slide'                               => 'Edellinen dia',
      'Next slide'                                   => 'Seuraava dia',
      'Last slide'                                   => 'Viimeinen dia',
    ),
  );

  return ( array_key_exists( $language, $strings ) ) ? $strings[ $language ] : $strings['en'];
} // end get_default_localization_strings

function get_default_localization( $string ) {
  if ( function_exists( 'ask__' ) && array_key_exists( "Accessibility: {$string}", apply_filters( 'air_helper_pll_register_strings', array() ) ) ) {
		return ask__( "Accessibility: {$string}" );
  }

  return esc_html( get_default_localization_translation( $string ) );
} // end get_default_localization

function get_default_localization_translation( $string ) {
  $language = get_bloginfo( 'language' );
  if ( function_exists( 'pll_the_languages' ) ) {
		$language = pll_current_language();
  }

  $translations = get_default_localization_strings( $language );

  return ( array_key_exists( $string, $translations ) ) ? $translations[ $string ] : '';
} // end get_default_localization_translation
