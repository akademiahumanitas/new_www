<?php
/**
 * Mega Menu Submenu
 * 
 * @package Humanitas
 */
$title = $title ?? get_sub_field('title');
$background_image = $background_image ?? get_sub_field('background_image');
$submenu = $submenu ?? get_sub_field('submenu');
?>
<div class="mega-menu__menu">
    <figure class="mega-menu__background-image">
        <?= get_image($background_image); ?>
    </figure>
    <div class="mega-menu__overflow">
        <div class="container">
            <div class="mega-menu__menu-container">
                <div class="mega-menu__menu-wrapper fade-in js-delay">
                    <button class="mega-menu__close-button js-close-megamenu js-delay-item" aria-label="<?= __('Wróć', 'humanitas');?>">
                        <?= get_image('arrow-left'); ?> <span class="mega-menu__close-button--desktop"><?= __('Wróć', 'humanitas');?></span><span class="mega-menu__close-button--mobile"><?= $title;?></span>
                    </button>
                    <ul class="mega-menu__menu-list">
                        <?php foreach ($submenu as $menu_item) : 
                            $type = $menu_item['type']; // submenu, link
                            $link_title = $menu_item['link_title'];
                            $link = $menu_item['link'];
                        ?>
                            <li class="mega-menu__submenu-item js-delay-item">
                                <?php if($type === 'submenu') : ?>
                                    <span class="mega-menu__submenu-item-link mega-menu__submenu-item-link--has-submenu" tabindex="0" data-for="<?php echo sanitize_title($link_title); ?>"><?php echo $link_title; ?><?= get_image('arrow-up-right'); ?></span>
                                <?php else : ?>
                                    <a href="<?php echo $link['url']; ?>" class="mega-menu__submenu-item-link"><?php echo $link['title']; ?><?= get_image('arrow-up-right'); ?></a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="mega-menu__submenus">
                    <?php foreach ($submenu as $menu_item) : 
                        $type = $menu_item['type']; // submenu, link
                        $link_title = $menu_item['link_title'];
                        $submenu = $menu_item['submenu'];
                        if($type !== 'submenu' || !$submenu) continue;
                    ?>
                        <div class="mega-menu__submenu fade-in js-delay" id="<?php echo sanitize_title($link_title); ?>">
                                <button class="mega-menu__close-button js-close-submenu js-delay-item" aria-label="<?= __('Wróć do', 'humanitas').' '.$link_title;?>">
                                    <?= get_image('arrow-left'); ?> <span><?= $link_title; ?></span>
                                </button>
                                <?php foreach ($submenu as $submenu_item) : 
                                    $acf_fc_layout = $submenu_item['acf_fc_layout']; // links/
                                    $title = $submenu_item['title'];
                                    $link = $submenu_item['link'];
                                ?>
                                <?php if($acf_fc_layout === 'links') : 
                                    $link = $submenu_item['links'];
                                    ?>
                                    <div class="mega-menu__submenu-links">
                                        <h3 class="mega-menu__submenu-title js-delay-item"><?php echo $title; ?></h3>
                                        <ul class="mega-menu__submenu-links-list">
                                            <?php foreach ($link as $link_item) : 
                                                $link_title = $link_item['link_title'];
                                                $link = $link_item['link'];
                                            ?>
                                                <li class="mega-menu__submenu-links-item js-delay-item">
                                                    <a href="<?php echo $link['url']; ?>" class="mega-menu__submenu-links-link"><?php echo $link['title']; ?><?= get_image('arrow-up-right'); ?></a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                                <?php if($acf_fc_layout === 'content') : 
                                    $title = $submenu_item['title'];
                                    $content = $submenu_item['content'];
                                    ?>
                                    <div class="mega-menu__submenu-links">
                                        <h3 class="mega-menu__submenu-title js-delay-item"><?php echo $title; ?></h3>
                                        <div class="mega-menu__submenu-content js-delay-item">
                                            <?php echo $content; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>