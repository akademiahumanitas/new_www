<?php

$page_title = $page_title ?? get_the_title();
$menu = $menu ?? get_field('secondary_menu');

?>
<section class="block-hero">
    <div class="container">
        <div class="block-hero__wrapper">
            <?php get_theme_part('elements/breadcrumbs', ['version' => 'secondary']); ?>
            <h1 class="block-hero__title fade-in heading-underline"><?= $page_title; ?></h1>
        </div>
    </div>
    <?php if(!$menu) : ?>
        <?php get_theme_part('elements/triangle', [
            'position' => 'bottom-right',
        ]); ?>
    <?php endif; ?>
</section>
