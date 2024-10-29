<?php

$page_title = $page_title ?? get_the_title();
$menu = $menu ?? get_field( 'secondary_menu' );
$background_image = $background_image ?? get_field( 'background_image' );
$show_secondary_menu = $show_secondary_menu ?? get_field( 'show_secondary_menu' );
$secondary_menu_automatic = $secondary_menu_automatic ?? get_field( 'secondary_menu_automatic' );
$hero_boxes = $hero_boxes ?? get_field( 'hero_boxes' );
$decoration = $decoration ?? true;

// check if page has a parent
$parent_id = wp_get_post_parent_id( get_the_ID() );
if ( $parent_id ) {
    $decoration = false;
}
?>
<section class="block-hero">
    <?php if ( $background_image ) : ?>
        <figure class="block-hero__background-image">
            <?php echo get_image( $background_image, 'hero' ); ?>
        </figure>
    <?php endif; ?>
    <div class="container">
        <div class="block-hero__wrapper">
            <?php get_theme_part( 'elements/breadcrumbs', array( 'version' => 'secondary' ) ); ?>
            <h1 class="block-hero__title fade-in <?php echo $decoration ? 'heading-underline' : '' ?>"><?php echo $page_title; ?></h1>
            <?php if ( $hero_boxes ) : ?>
                <div class="block-hero__boxes">
                    <?php foreach ( $hero_boxes as $box ) :
                        $link = $box['link'];
                        $icon = $box['icon'];
                    ?>
                        <?php get_theme_part('elements/button-card', array(
                            'button' => $link,
                            'icon' => $icon,
                            'button_classes' => 'block-hero__box',
                        )); ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php if ( ! $show_secondary_menu ) : ?>
        <?php get_theme_part('elements/triangle', array(
            'position' => 'bottom-right',
        )); ?>
    <?php endif; ?>
</section>
<?php get_theme_part('blocks/block-secondary-menu/index', array(
    'menu' => $menu,
    'show_secondary_menu' => $show_secondary_menu,
    'secondary_menu_automatic' => $secondary_menu_automatic,
)); ?>
