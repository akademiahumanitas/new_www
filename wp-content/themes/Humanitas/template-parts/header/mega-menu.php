<?php
namespace Air_Light;

    $main_menu = get_field('main_navigation', 'option');

    if (empty($main_menu) || !is_array($main_menu)) {
        $main_menu = [];
    }
?>

<nav class="mega-menu" aria-label="<?php echo esc_html( get_default_localization( 'Main navigation' ) ); ?>">
    <button aria-haspopup="true" aria-expanded="false" aria-controls="nav" id="megamenu-toggle" class="mega-menu__hamburger js-hamburger" type="button" aria-label="<?php echo esc_html( get_default_localization( 'Open main menu' ) ); ?>">
        <span class="mega-menu__hamburger-lines"></span>
        <span class="mega-menu__hamburger-lines"></span>
        <span class="mega-menu__hamburger-lines"></span>
    </button>
    <div class="mega-menu__wrapper">
        <ul class="mega-menu__list">
            <?php foreach ($main_menu as $first_tier_menu) :
                $type = $first_tier_menu['type']; // submenu, link
                $link_title = $first_tier_menu['link_title'];
                $link = $first_tier_menu['link'];
                $background_image = $first_tier_menu['background_image'];
            ?>
                <li class="mega-menu__item<?php echo $type === 'submenu' ? ' mega-menu__item--has-submenu' : '';?>">
                    <?php // if type === submenu use span tag for link_title; else use a tag for acf link
                    if($type === 'submenu') : ?>
                        <span class="mega-menu__link" tabindex="0"><?php echo $link_title; ?></span>
                        <?php get_theme_part('header/mega-menu-submenu', [
                            'submenu' => $first_tier_menu['submenu'],
                            'background_image' => $background_image,
                            'title' => $link_title,
                        ]); ?>
                    <?php else : ?>
                        <a href="<?php echo $link['url']; ?>" class="mega-menu__link"><?php echo $link['title']; ?></a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="mega-menu__footer">
          <?php get_theme_part( 'header/header-right', ['position' => 'top'] ); ?>
        </div>
    </div>
</nav>
