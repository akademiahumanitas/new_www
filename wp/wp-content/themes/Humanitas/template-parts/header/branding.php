<?php
/**
 * Site branding & logo
 *
 * @package humanitas
 */

namespace Air_Light;

$logo_id = get_field( 'logo', 'option' );
$src = wp_get_attachment_image_src( $logo_id, 'full-size' );

?>

<div class="site-branding">

  <p class="site-title">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
      <span class="screen-reader-text"><?php bloginfo( 'name' ); ?></span>
      <?php echo get_image( $logo_id, 'full-size' ); ?>
      
    </a>
  </p>
  
</div>
