<?php 

    $book_contents = get_field('book_contents');
    $exclude = array(get_the_ID());

?>

<section class="book-contents">
    <div class="container">
        <h2><?php _e('Spis treści', 'humanitas'); ?></h2>
        <div class="book-contents__wrapper">
            <div class="book-contents__content">
                <?= $book_contents; ?>
            </div>
            <div class="book-contents__sidebar">
                <?php get_theme_part('elements/related-books', [
                        'exclude' => $exclude,
                        'version' => 'secondary',
                        'more_books' => false,
                        'limit' => 3,
                    ]); ?>
            </div>
        </div>
    </div>
</section>