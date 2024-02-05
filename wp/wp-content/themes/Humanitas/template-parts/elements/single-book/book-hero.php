<?php 

    $title = get_the_title();
    $thumbnail_id = get_post_thumbnail_id();
    $author = get_field('author');
    $publishing = get_field('publishing');
    $subject = get_field('subject');
    $book_url = get_field('book_url');
    $book_preview_file = get_field('book_preview_file');

    $book_genre = get_the_terms( get_the_ID(), 'book_genre' );
    $books_category = get_the_terms( get_the_ID(), 'books_category' );

?>
<section class="book-hero">
    <figure class="book-hero__background-image">
        <?= get_image($thumbnail_id, 'large'); ?>
    </figure>
    <div class="container">
        <?php get_theme_part('elements/breadcrumbs', ['version' => 'secondary']); ?>
        <div class="book-hero__wrapper">
            <figure class="book-hero__image">
                <?= get_image($thumbnail_id, 'large'); ?>
            </figure>
            <div class="book-hero__content">
                <h1 class="book-hero__book-title"><?= $title; ?></h1>
                <h5><?php _e('Autor', 'humanitas'); ?></h5>
                <p><?= $author; ?></p>
                <h5><?php _e('Wydawnictwo', 'humanitas'); ?></h5>
                <p><?= $publishing; ?></p>
                <h5><?php _e('Subject', 'humanitas'); ?></h5>
                <p><?= $subject; ?></p>
                <h5><?php _e('Genre', 'humanitas'); ?></h5>
                <p><?= join(', ', array_map(function($term) { return $term->name; }, $book_genre)); ?></p>
                <h5><?php _e('Category', 'humanitas'); ?></h5>
                <p><?= join(', ', array_map(function($term) { return $term->name; }, $books_category)); ?></p>

                <div class="book-hero__buttons">
                    <?php if ($book_url) : ?>
                        <a href="<?= $book_url['url']; ?>" class="button button-yellow" target="_blank"><?php _e('Zamów książkę', 'humanitas'); ?></a>
                    <?php endif; ?>
                    <?php if ($book_preview_file) : ?>
                        <a href="<?= $book_preview_file; ?>" 
                            class="button button-tertiary" target="_blank"
                            aria-label="<?= __('Pobierz fragment książki', 'humanitas'); ?>"
                            download
                            ><?php _e('Podgląd książki', 'humanitas'); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php get_theme_part('elements/triangle', ['position' => 'bottom-left']); ?>
</section>