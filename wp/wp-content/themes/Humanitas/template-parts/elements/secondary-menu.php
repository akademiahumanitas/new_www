<?php 
    $menu =  $menu ?? false;

    if (!$menu) {
        return;
    }
$current_url = $automatic ? $_SERVER['REQUEST_URI'] : home_url( $_SERVER['REQUEST_URI'] );
$class = $automatic ? ' secondary-menu--automatic' : '';
?>
<nav class="secondary-menu<?= $class; ?>">
    <div class="container">
        <ul class="secondary-menu__list">
            <?php foreach ($menu as $item) : 
                    $striped_url = preg_replace('/\?.*/', '', $current_url);
                    $is_active = rtrim($item['link']['url'], '/') === $striped_url;
                ?>
                <li class="secondary-menu__item">
                    <a 
                        href="<?= $item['link']['url']; ?>" 
                        class="secondary-menu__link<?= $is_active ? ' secondary-menu__link--active' : ''; ?>" 
                        title="<?= $item['link']['title']; ?>"><?= $item['link']['title']; ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>