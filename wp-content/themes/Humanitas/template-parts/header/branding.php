<?php
/**
 * Site branding & logo
 *
 * @package humanitas
 */

namespace Air_Light;

$logo_id = get_field( 'logo', 'option' );
$logo_id_dark = get_field( 'logo_dark', 'option' );

?>

<p class="site-title">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
    <span class="screen-reader-text"><?php bloginfo( 'name' ); ?></span>
    <span class="site-title__logo-light"><?php echo get_image( $logo_id, 'full-size', true ); ?></span>
    <span class="site-title__logo-dark"><?php echo get_image( $logo_id_dark, 'full-size', true ); ?></span>
  </a>
</p>
  
