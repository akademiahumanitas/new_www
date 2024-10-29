<?php
  $position = $position ?? 'bottom';
?>

<div class="header-right">
  <div class="header-right__wcag">
    <div class="header-right__wcag-font-size">
      <button class="header-right__wcag-button header-right__wcag-button--normal" data-fontSize="normal"  aria-label="<?php echo esc_html( ( 'Reset font size' ) ); ?>">
        <span class="header-right__wcag-button-icon" aria-hidden="false">A</span>
      </button>
      <button class="header-right__wcag-button header-right__wcag-button--big" data-fontSize="big" aria-label="<?php echo esc_html( ( 'Increase font size' ) ); ?>">
        <span class="header-right__wcag-button-icon" aria-hidden="false">A+</span>
      </button>
      <button class="header-right__wcag-button header-right__wcag-button--bigger" data-fontSize="bigger" aria-label="<?php echo esc_html( ( 'Increase font size' ) ); ?>">
        <span class="header-right__wcag-button-icon" aria-hidden="false">A++</span>
      </button>
    </div>
    <!-- <div class="header-right__wcag-contrast">
      <button class="header-right__wcag-button header-right__wcag-button--contrast" aria-label="<?php echo esc_html( ( 'Toggle contrast' ) ); ?>">
        <span class="header-right__wcag-button-icon" aria-hidden="false"><?php echo get_image( 'contrast-icon' ); ?></span>
      </button>
    </div> -->
    <div class="header-right__language-switcher">
      <?php get_theme_part( 'elements/language-switcher', ['position' => $position] ); ?>
    </div>
  </div>
</div>