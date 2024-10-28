<?php 
    $menu =  $menu ?? false;
    $show_secondary_menu = $show_secondary_menu ?? true;

    if (!$menu || !$show_secondary_menu) {
        return;
    }
    $class = $automatic ? ' secondary-menu--automatic' : '';
?>
<nav class="secondary-menu<?= $class; ?>">
    <div class="container fade-in">
        <ul class="secondary-menu__list js-delay">
            <?php if($automatic) : ?>
                    <?php foreach ($menu as $item) : 
                            if($item) :
                        ?>
                        <li class="menu-item js-delay-item">
                            <a 
                                href="<?= $item['link']['url']; ?>" 
                                class="secondary-menu__link" 
                                target="<?= $item['link']['target']; ?>"
                                aria-label="<?= $item['link']['title']; ?>"
                                title="<?= $item['link']['title']; ?>"><?= $item['link']['title']; ?></a>
                        </li>
                    <?php endif; endforeach; ?>
            <?php else : ?>
                <?php 
                // wp menu with id in $menu
                wp_nav_menu([
                    'menu' => $menu,
                    'container' => false,
                    'items_wrap' => '%3$s',
                    'menu_class' => 'secondary-menu__list',
                    // menu item additional class
                    'menu_item_class' => 'menu-item js-delay-item',
                ]);
                ?>
            <?php endif; ?>
        </ul>
    </div>
</nav>