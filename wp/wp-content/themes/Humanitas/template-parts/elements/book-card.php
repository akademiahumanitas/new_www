<?php
$post_ID = $post_ID ?? get_the_ID();
$book_title = get_the_title($post_ID);
$book_link = get_the_permalink($post_ID);
$book_thumbnail = get_post_thumbnail_id($post_ID);
$author = get_field('author', $post_ID);

$books_category  = get_the_terms($post_ID, 'books_category');
$class = $class ?? '';

?>
<div class="book-card js-delay-item <?= $class;?>">
    <figure class="book-card__image">
        <a href="<?= $book_link; ?>" aria-label="<?= __('Link do ksiąki', 'humanitas') . ' ' . $book_title; ?>" title="<?= $book_title; ?>">
            <?= wp_get_attachment_image($book_thumbnail, 'full', false, array('class' => 'book-card__image-img')); ?>
        </a>
    </figure>
    <div class="book-card__content">
        <p class="book-card__category"><?= join(', ', array_map(function($term) { return $term->name; }, $books_category)); ?></p>
        <h4 class="book-card__title">
            <a href="<?= $book_link; ?>" aria-label="<?= __('Link do ksiąki', 'humanitas') . ' ' . $book_title; ?>" title="<?= $book_title; ?>">
                <?= $book_title; ?>
            </a>
        </h4>
        <p class="book-card__author"><?= $author; ?></p>
        <a href="<?= $book_link; ?>" aria-label="<?= __('Link do ksiąki', 'humanitas') . ' ' . $book_title; ?>" title="<?= $book_title; ?>" class="book-card__title-link"><?= __('Zobacz szczegóły', 'humanitas'); ?></a>
    </div>
</div>
