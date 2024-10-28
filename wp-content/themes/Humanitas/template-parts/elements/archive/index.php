<?php
if(is_home()) {
    $post_type = 'post';
} else {
    $post_type = get_query_var('post_type') ?? get_post_type();
}

$faq = get_field($post_type.'_faq', 'options');
$contact_box = get_field($post_type.'_contact_box', 'options');
?>

<main class="site-main archive-page archive-page--<?= $post_type; ?>" data-post-type="<?= $post_type; ?>">
    <?php get_theme_part(
        'elements/archive/archive-header',
        [
            'post_type' => $post_type,

        ]
    ); ?>
    <div class="container">
        <div class="archive-page__wrapper">
            <div class="archive-page__sidebar"><?php 
                get_theme_part('elements/archive/archive-sidebar', ['post_type' => $post_type]); 
                 if($contact_box) :
                     get_theme_part('elements/contact-box', [
                        'contact_box' => $contact_box,
                    ]); endif; ?></div>
            <div class="archive-page__content">
                <?php if($post_type !== 'contact') : ?>
                    <?php get_theme_part('elements/archive/archive-search', ['post_type'=> $post_type]); ?>
                <?php endif; ?>
                <div class="archive-page__content-posts">
                    <?php get_theme_part('elements/archive/archive-content', [
                        'post_type' => $post_type,
                    ]); ?>
                </div>
            </div>
            <?php if($post_type === 'contact') : ?>
                <div class="archive-page__faq">
                    <?php get_theme_part('elements/archive/archive-faq',[
                        'faq' => $faq,
                    ]); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php if($post_type === 'oferta') : ?>
    <?php get_theme_part('elements/related-offers',[
        'more_link' => false,
    ]); ?>
<?php endif; ?>