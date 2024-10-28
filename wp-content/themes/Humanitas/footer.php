<?php

/**
 * Template for displaying the footer
 *
 * Description for template.
 *
 * @Author: Roni Laukkarinen
 * @Date: 2020-05-11 13:33:49
 * @Last Modified by:   Roni Laukkarinen
 * @Last Modified time: 2022-09-07 11:57:45
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @package humanitas
 */

namespace Air_Light;

$footer_logo = get_field( 'footer_logo', 'option' );
$footer_text = get_field( 'footer_text', 'option' );

$footer_logos = get_field( 'footer_logos', 'option' );

$footer_column_1 = get_field( 'footer_column_1', 'option' );
$footer_column_2 = get_field( 'footer_column_2', 'option' );
$footer_column_3 = get_field( 'footer_column_3', 'option' );
$footer_column_4 = get_field( 'footer_column_4', 'option' );
$footer_column_5 = get_field( 'footer_column_5', 'option' );

$footer_copyright = get_field( 'footer_copyright', 'option' );
$footer_bottom_links = get_field( 'footer_bottom_links', 'option' );

?>

</div><!-- #content -->

<footer id="colophon" class="site-footer">
  <div class="container">
    <div class="site-footer__top">
      <div class="site-footer__left">
        <figure class="site-footer__logo">
          <?php echo get_image( $footer_logo ); ?>
        </figure>
        <div class="site-footer__text">
          <?php echo $footer_text; ?>
        </div>
        <div class="site-footer__logos">
          <?php foreach ( $footer_logos as $logo ) : ?>
            <a class="site-footer__logos-item" title="<?php echo $logo['link']['title']; ?>" href="<?php echo $logo['link']['url']; ?>">
              <?php echo get_image( $logo['logo'] ); ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="site-footer__right">
        <?php get_theme_part( 'footer/footer-column', array( 'column' => $footer_column_1 ) ); ?>
        <?php get_theme_part( 'footer/footer-column', array( 'column' => $footer_column_2 ) ); ?>
        <?php get_theme_part( 'footer/footer-column', array( 'column' => $footer_column_3 ) ); ?>
        <?php get_theme_part( 'footer/footer-column', array( 'column' => $footer_column_4 ) ); ?>
        <?php get_theme_part( 'footer/footer-column', array( 'column' => $footer_column_5 ) ); ?>
      </div>
    </div>
    <div class="site-footer__bottom">
      <div class="site-footer__bottom-left">
        <p class="site-footer__bottom-text">
          <?php echo $footer_copyright; ?>
        </p>
        <?php foreach ( $footer_bottom_links as $link ) : ?>
          <a class="site-footer__bottom-link" title="<?php echo $link['link']['title']; ?>" href="<?php echo $link['link']['url']; ?>"><?php echo $link['link']['title']; ?></a>
        <?php endforeach; ?>
      </div>
      <div class="site-footer__bottom-right">
        <?php get_theme_part( 'elements/language-switcher', array( 'position' => 'top' ) ); ?>
        <?php get_theme_part( 'footer/social-media' ); ?>
      </div>
    </div>


</footer><!-- #colophon -->

</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>