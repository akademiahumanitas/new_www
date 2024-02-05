<?php
    $page_title = get_field($post_type.'_section_title', 'options') ?? get_the_archive_title();
    $menu = get_field($post_type.'_secondary_menu', 'option');
?>

<section class="archive-header">
    <div class="container">
        <div class="archive-header__wrapper">
            <?php get_theme_part('elements/breadcrumbs', ['version' => 'secondary']); ?>
            <h1 class="archive-header__title fade-in heading-underline"><?= $page_title; ?></h1>
        </div>
    </div>
    <?php if(!$menu) : ?>
        <?php get_theme_part('elements/triangle', [
            'position' => 'bottom-right',
        ]); ?>
    <?php endif; ?>
</section>
<?php get_theme_part('elements/secondary-menu', [
        'menu' => $menu,
    ]); ?>