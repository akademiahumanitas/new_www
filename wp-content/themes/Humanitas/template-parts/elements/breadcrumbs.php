<?php
    $class = $version ? $version : 'primary';
?>

<section class="breadcrumbs breadcrumbs--<?= $class; ?>">
    <div class="breadcrumbs__wrapper">
        <a href="<?= get_home_url(); ?>" class="breadcrumbs__link">
            <?= __('Strona główna', 'Humanitas'); ?>
        </a>
        <?= get_image('breadcrumbs-arrow'); ?>
        <?php if (is_page()) : ?>
            <?php
                $ancestors = get_post_ancestors($post);
                $ancestors = array_reverse($ancestors);
            ?>
            <?php foreach ($ancestors as $ancestor) : ?>
                <a href="<?= get_permalink($ancestor); ?>" class="breadcrumbs__link">
                    <?= get_the_title($ancestor); ?>
                </a>
            <?= get_image('breadcrumbs-arrow'); ?>
            <?php endforeach; ?>
            <span class="breadcrumbs__link breadcrumbs__link--current">
                <?= get_the_title(); ?>
            </span>
        <?php elseif (is_single()) : ?>
            <?php
                $post_type = get_post_type();
                $post_type_object = get_post_type_object($post_type);
                $post_type_name = $post_type_object->labels->name;
                $additional_url = '';

                if($post_type === 'oferta') {
                    $offer_type = get_the_terms(get_the_ID(), 'offer_type');
                    $offer_type = $offer_type[0];
                    $post_type_name = $offer_type->name;
                    $additional_url = '?offer_type='.$offer_type->slug;
                }
            ?>
            <a href="<?= get_post_type_archive_link($post_type); ?><?= $additional_url;?>" class="breadcrumbs__link">
                <?= $post_type_name; ?>
            </a>
            <?= get_image('breadcrumbs-arrow'); ?>
            <span class="breadcrumbs__link breadcrumbs__link--current">
                <?= get_the_title(); ?>
            </span>
        <?php elseif (is_archive() || is_home()) : ?>
            <?php
                $post_type = get_post_type();
                $post_type_object = get_post_type_object($post_type);

                $post_type_name = get_field($post_type.'_section_title', 'options') ?? $post_type_object->labels->name;

            ?>
            <span class="breadcrumbs__link breadcrumbs__link--current">
                <?= $post_type_name; ?>
            </span>
        <?php elseif (is_404()) : ?>
            <span class="breadcrumbs__link breadcrumbs__link--current">
                <?= __('Błąd 404', 'Humanitas'); ?>
            </span>
        <?php endif; ?>
    </div>
</section>