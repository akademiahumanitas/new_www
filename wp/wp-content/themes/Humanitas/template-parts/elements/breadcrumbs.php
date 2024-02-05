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
                $post_type_name = $post_type_object->labels->singular_name;
            ?>
            <a href="<?= get_post_type_archive_link($post_type); ?>" class="breadcrumbs__link">
                <?= $post_type_name; ?>
            </a>
            <?= get_image('breadcrumbs-arrow'); ?>
            <span class="breadcrumbs__link breadcrumbs__link--current">
                <?= get_the_title(); ?>
            </span>
        <?php elseif (is_archive()) : ?>
            <?php
                $post_type = get_post_type();
                $post_type_object = get_post_type_object($post_type);
                $post_type_name = $post_type_object->labels->singular_name;
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